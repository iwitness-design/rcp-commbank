<?php

namespace Commbank;

use RCP_Payment_Gateway;
use RCP_Member;
use RCP_Payments;

class RCP_Payment_Gateway_Commbank extends RCP_Payment_Gateway {



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
		$rcp_options = get_option( 'rcp_settings' );
//error_log( var_export( $rcp_options, true ) );

		$this->checkout_url = 'https://paymentgateway.commbank.com.au/api/rest/version/34/merchant/';
		if ( $this->test_mode ) {
			//$configArray["gatewayUrl"] = "https://paymentgateway.commbank.com.au/api/rest";
			//gatewayUrl' => 'https://paymentgateway.commbank.com.au/api/rest/version/34/merchant/TESTROYCHIEVAL02/
			$this->merchant_id = 'TEST' . $rcp_options['rcpcommbank_merchant_id'] . '/';

		} else {

			$this->merchant_id =  $rcp_options['rcpcommbank_merchant_id'] . '/';
			//rcpcommbank_password
		}
		$this->checkout_url = 'https://paymentgateway.commbank.com.au/api/rest/version/34/merchant/' . $this->merchant_id;

//error_log( print_r( var_dump( $this->checkout_url ), true ) );
//******************
		$this->configArray = array();

		// If using a proxy server, uncomment the following proxy settings

		// If no authentication is required, only uncomment proxyServer
		// Server name or IP address and port number of your proxy server
		//$configArray["proxyServer"] = "proxy:port";

		// Username and password for proxy server authentication
		//$configArray["proxyAuth"] = "username:password";

		// The below value should not be changed
		//$configArray["proxyCurlOption"] = CURLOPT_PROXYAUTH;

		// The CURL Proxy type. Currently supported values: CURLAUTH_NTLM and CURLAUTH_BASIC
		//$configArray["proxyCurlValue"] = CURLAUTH_NTLM;


		// If using certificate validation, modify the following configuration settings

		// alternate trusted certificate file
		// leave as "" if you do not have a certificate path
		//$configArray["certificatePath"] = "C:/ca-cert-bundle.crt";

		// possible values:
		// FALSE = disable verification
		// TRUE = enable verification
		$this->configArray['certificateVerifyPeer'] = TRUE;

		// possible values:
		// 0 = do not check/verify hostname
		// 1 = check for existence of hostname in certificate
		// 2 = verify request hostname matches certificate hostname
		$this->configArray['certificateVerifyHost'] = 2;


		// Base URL of the Payment Gateway. Do not include the version.
		//$this->configArray["gatewayUrl"] = "https://paymentgateway.commbank.com.au/api/rest";

		//get merchantid and password from db


		// Merchant ID supplied by your payments provider
//		$this->configArray["merchantId"] = "TESTROYCHIEVAL02"; //"TESTROYCHIEVAL02";//[INSERT-MERCHANT-ID]";

		// API username in the format below where Merchant ID is the same as above
		$this->configArray['apiUsername'] = 'merchant.' . $this->merchant_id ; //"merchant.[INSERT-MERCHANT-ID]";

		// API password which can be configured in Merchant Administration
//		$this->configArray["password"] =  "76b57f4e647378ba1c94c235a8fceb02"; //"b828f05c54db5ea807f4630c7c20df86"; //"vQ790wH3EC"; //"76b57f4e647378ba1c94c235a8fceb02";


		// The debug setting controls displaying the raw content of the request and
		// response for a transaction.
		// In production you should ensure this is set to FALSE as to not display/use
		// this debugging information
		$this->configArray['debug'] = FALSE;

		// Version number of the API being used for your integration
		// this is the default value if it isn't being specified in process.php
		$this->configArray['version'] = "13";

/*		if (array_key_exists("proxyServer", $configArray))
			$this->proxyServer = $configArray["proxyServer"];

		if (array_key_exists("proxyAuth", $configArray))
			$this->proxyAuth = $configArray["proxyAuth"];

		if (array_key_exists("proxyCurlOption", $configArray))
			$this->proxyCurlOption = $configArray["proxyCurlOption"];

		if (array_key_exists("proxyCurlValue", $configArray))
			$this->proxyCurlValue = $configArray["proxyCurlValue"];

		if (array_key_exists("certificatePath", $configArray))
			$this->certificatePath = $configArray["certificatePath"];

		if (array_key_exists("certificateVerifyPeer", $configArray))
			$this->certificateVerifyPeer = $configArray["certificateVerifyPeer"];

		if (array_key_exists("certificateVerifyHost", $configArray))
			$this->certificateVerifyHost = $configArray["certificateVerifyHost"];

		if (array_key_exists("gatewayUrl", $configArray))
			$this->gatewayUrl = $configArray["gatewayUrl"];

		if (array_key_exists("debug", $configArray))
			$this->debug = $configArray["debug"];

		if (array_key_exists("version", $configArray))
			$this->version = $configArray["version"];

		if (array_key_exists("merchantId", $configArray))
			$this->merchantId = $configArray["merchantId"];

		if (array_key_exists("password", $configArray))
			$this->password = $configArray["password"];

		if (array_key_exists("apiUsername", $configArray))
			$this->apiUsername = $configArray["apiUsername"]; */
//******************

		/*if ( isset( $_GET['commbank-error'], $_GET['message'] ) ) {
			rcp_errors()->add( 'commbank-error', urldecode( $_GET['message'] ), 'register' );
		}*/
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

/*'rcp_level' => '1',
  'rcp_gateway' => 'commbank',
  'rcp_card_number' => '1212',
  'rcp_card_cvc' => '123',
  'rcp_card_zip' => '12312',
  'rcp_card_name' => 'sdA ASDF',
  'rcp_card_exp_month' => '1',
  'rcp_card_exp_year' => '2018', */
//order.notificationUrl
//order.amount
//order.currency
//

/*	$creds = array (
		'debug' => true, //false,
		'version' => '34',
		'merchantId' => 'TESTROYCHIEVAL02',
		'password' => '76b57f4e647378ba1c94c235a8fceb02',
		'apiUsername' => 'merchant.TESTROYCHIEVAL02',
	); */

	$formData = array (
		'apiOperation' => 'PAY',
		'sourceOfFunds' => array (
			'type' => 'CARD',
			'provided' => array (
				'card' => array (
					'number' => $_POST['rcp_card_number'],
					'expiry' => array (
						'month' => $_POST['rcp_card_exp_month'],
						'year' => $_POST['rcp_card_exp_year'],
					),
					'securityCode' => $_POST['rcp_card_cvc'],
				),
			),
		),
		'order' => array(
			'amount' => $this->initial_amount, //rcp_get_registration()->get_total(),
			'currency' => $rcp_options['currency'],
			//'notificationUrl' => add_query_arg( 'listener', '2checkout', home_url( 'index.php' ) ),
		),
	);


	$request_data = json_encode($formData);
error_log( var_export( $request_data, true ) );

//
		/* $signed_fields = array(
			'password'           => $rcp_options['rcpcommbank_password'],
			'merchant_id'           => $rcp_options['rcpcommbank_merchant_id'],
			'reference_number'     => $customer_id,
			'transaction_type'     => 'sale',
			'unsigned_field_names' => '',
			'signed_field_names'   => '',
			'signed_date_time'     => gmdate( "Y-m-d\TH:i:s\Z" ),
			'locale'               => 'en',
			'currency'             => $rcp_options['currency'],
			'amount'               => rcp_get_registration()->get_total(),
		); */

		$order_id = $this->subscription_id;
		$transaction_id = $this->subscription_id;

		$request_URL = $this->checkout_url . 'order/'. $order_id . '/transaction/' . $transaction_id;
error_log( var_export( $request_URL, true ) );		//$request = new WP_REST_Request( 'PUT', $request_URL );
		//$request->set_param( 'key', $value );
		$response = wp_remote_request( $request_URL, array(
			'method' =>'PUT',
			'headers' => array (
				'Authorization' => 'Basic ' . base64_encode( 'merchant.TESTROYCHIEVAL02'.':'.'76b57f4e647378ba1c94c235a8fceb02'),
				 ),
			'body' => $request_data,
			) );
		//$response = rest_do_request( $request );

error_log( var_export( $response, true ) );

//		$signed_fields['signed_field_names'] = implode( ',', array_keys( $signed_fields ) );

//		$action = $this->test_mode ? 'https://testsecureacceptance.cybersource.com/pay' : 'https://paymentgateway.commbank.com.au/api/rest';

		//ob_start();

		// custom form may go here

		//wp_send_json_success( ob_get_clean() );
		//redirect to $this->return_url;
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
	/* protected function get_signature( $params ) {
		global $rcp_options;

		$signed_fields = explode( ",", $params["signed_field_names"] );
		$params_to_sign = array();
		$secret_key     = $rcp_options['rcpcommbank_password'];

		foreach( $signed_fields as $field ) {
			$params_to_sign[] = $field . "=" . $params[ $field ];
		}

		$params_to_sign = implode( ',', $params_to_sign );

		$signature = hash_hmac( 'sha256', $params_to_sign, $secret_key, true );

		return base64_encode( $signature );
	} */

	/**
	 * Cybersource will send the results back to us. Here we process those results and redirect accordingly
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function process_webhooks() {
		global $rcp_options;
error_log( '************* HELLO from webhook ***************');
//die();
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
		//rcp_get_template_part
		include rcpcommbank()->get_plugin_dir().'templates/card-form-full.php';
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

		//if ( empty( $rcp_options['commbank_profile_id'] ) || empty( $rcp_options['commbank_access_key'] ) || empty( $rcp_options['commbank_secret_key'] ) ) {
		//	rcp_errors()->add( 'missing_commbank_settings', __( 'Missing Commbank tokens.', rcpcommbank()->get_id() ), 'register' );
		//}
	}

}