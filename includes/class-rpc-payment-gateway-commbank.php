<?php

namespace RCPCommbank;

use RCP_Payment_Gateway;
use RCP_Member;
use RCP_Payments;

class Gateway extends RCP_Payment_Gateway {

	/**
	 * Initialize the Payment gateway
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function init() {
		$this->supports[] = 'one-time';
		$this->supports[] = 'fees';
		$this->supports[] = 'gateway-submits-form';

		if ( $this->test_mode ) {
			$this->checkout_url = 'https://testsecureacceptance.cybersource.com/pay';
		} else {
			$this->checkout_url = 'https://secureacceptance.cybersource.com/pay';
		}

		if ( isset( $_GET['commbank-error'], $_GET['message'] ) ) {
			rcp_errors()->add( 'commbank-error', urldecode( $_GET['message'] ), 'register' );
		}
	}

	/**
	 * Process registration
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function process_signup() {
		global $rcp_options;

		$member = new RCP_Member( $this->user_id );

		if( ! $customer_id = $member->get_payment_profile_id() ) {
			$customer_id = uniqid();
			$member->set_payment_profile_id( $customer_id );
		}

		update_user_meta( $member->ID, 'registration_page', sanitize_text_field( $_SERVER['HTTP_REFERER'] ) );

		$signed_fields = array(
			'access_key'           => $rcp_options['rcpcommbank_access_key'],
			'profile_id'           => $rcp_options['rcpcommbank_profile_id'],
			'reference_number'     => $customer_id,
			'transaction_uuid'     => uniqid(),
			'transaction_type'     => 'sale',
			'unsigned_field_names' => '',
			'signed_field_names'   => '',
			'signed_date_time'     => gmdate( "Y-m-d\TH:i:s\Z" ),
			'locale'               => 'en',
			'currency'             => 'USD',
			'amount'               => rcp_get_registration()->get_total(),
		);

		$signed_fields['signed_field_names'] = implode( ',', array_keys( $signed_fields ) );

		$action = $this->test_mode ? 'https://testsecureacceptance.cybersource.com/pay' : 'https://secureacceptance.cybersource.com/pay';

		ob_start();

		// custom form may go here

		wp_send_json_success( ob_get_clean() );
	}

	/**
	 * Get request signature. Made up of all of the signed keys.
	 *
	 * @param $params
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function get_signature( $params ) {
		global $rcp_options;

		$signed_fields = explode( ",", $params["signed_field_names"] );
		$params_to_sign = array();
		$secret_key     = $rcp_options['rcpcommbank_secret_key'];

		foreach( $signed_fields as $field ) {
			$params_to_sign[] = $field . "=" . $params[ $field ];
		}

		$params_to_sign = implode( ',', $params_to_sign );

		$signature = hash_hmac( 'sha256', $params_to_sign, $secret_key, true );

		return base64_encode( $signature );
	}

	/**
	 * Cybersource will send the results back to us. Here we process those results and redirect accordingly
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function process_webhooks() {
		global $rcp_options;

		if( ! isset( $_GET['listener'] ) || strtolower( $_GET['listener'] ) != 'commbank' ) {
			return;
		}

		// Ensure listener URL is not cached by W3TC
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		$defaults = array(
			'req_reference_number' => '',
			'req_transaction_type' => '',
			'decision'             => '',
			'message'              => '',
			'auth_time'            => '',
			'auth_amount'          => '',
			'transaction_id'       => '',
		);

		$_post = wp_parse_args( $_POST, $defaults );

		if ( ! $_post['req_reference_number'] || 'sale' != $_post['req_transaction_type'] ) {
			return;
		}

		$user   = rcp_get_member_id_from_profile_id( $_post['req_reference_number'] );
		$member = new RCP_Member( $user );
		$redirect = rcp_get_return_url( $member->ID );

		switch( $_post['decision'] ) {
			case 'ACCEPT' :

				if ( ! $name = $member->get_pending_subscription_name() ) {
					$name = $member->get_subscription_name();
				}

				if ( ! $key = $member->get_pending_subscription_key() ) {
					$key = $member->get_subscription_key();
				}

				$payment_data = array(
					'date'             => date_i18n( 'Y-m-d g:i:s', strtotime( $_post['auth_time'] ) ),
					'subscription'     => $name,
					'payment_type'     => 'Credit Card One Time',
					'subscription_key' => $key,
					'amount'           => $_post['auth_amount'],
					'user_id'          => $member->ID,
					'transaction_id'   => $_post['transaction_id'],
				);

				$rcp_payments = new RCP_Payments();
				$rcp_payments->insert( $payment_data );

				// If the customer has an existing subscription, we need to cancel it
				if( $member->just_upgraded() && $member->can_cancel() ) {
					$member->cancel_payment_profile( false );
				}

				$member->set_recurring( false );

				if ( ! is_user_logged_in() ) {
					rcp_login_user_in( $this->user_id, $this->user_name );
				}

				$member->set_expiration_date( $member->calculate_expiration() );

				// set this user to active
				$member->set_status( 'active' );

				wp_redirect( $redirect ); exit;

			case 'DECLINE' :
			case 'ERROR' :
			case 'CANCEL' :
				if ( ! $redirect = get_user_meta( $member->ID, 'registration_page', true ) ) {
					if ( isset( $rcp_options['registration_page'] ) ) {
						$redirect = get_permalink( $rcp_options['registration_page'] );
					} else {
						$redirect = get_home_url();
					}
				}

				$redirect = add_query_arg( array( 'commbank-error' => strtolower( $_post['decision'] ), 'message' => urlencode( $_post['message'] ) ), $redirect );
				wp_redirect( $redirect );
		}

	}

	/**
	 * Triggers the default RCP functionality then creates and submits CyberSource compatible form
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function fields() {

		ob_start();
		?>
		<script type="text/javascript">

			var rcp_script_options;
			var rcp_commbank_processing = false;

			jQuery(document).ready(function ($) {

				$('body').on('rcp_register_form_submission', function() {

					if (rcp_commbank_processing) {
						return false;
					}

					var form = $('#rcp_registration_form');
					rcp_commbank_processing = true;

					$.post(rcp_script_options.ajaxurl, form.serialize() + '&action=rcp_process_register_form', function (response) {
						$('.rcp-submit-ajax', form).remove();
						$('.rcp_message.error', form).remove();
					}).success(function (response) {
						$('body').append(response.data);
						document.getElementById('commbank_payment').submit();
					}).done(function (response) {
					}).fail(function (response) {
						console.log(response);
					}).always(function (response) {
					});
				});

			});
		</script>
		<?php

		return ob_get_clean();
	}

	/**
	 * Validate additional fields during registration submission
	 *
	 * @since  1.0.0
	 */
	public function validate_fields() {
		global $rcp_options;

		if ( empty( $rcp_options['commbank_profile_id'] ) || empty( $rcp_options['commbank_access_key'] ) || empty( $rcp_options['commbank_secret_key'] ) ) {
			rcp_errors()->add( 'missing_commbank_settings', __( 'Missing Commbank tokens.', rcpcommbank()->get_id() ), 'register' );
		}
	}

}