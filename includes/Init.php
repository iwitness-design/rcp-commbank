<?php

namespace RCPCommbank;


class Init {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of Init
	 *
	 * @return Init
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Init ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_filter( 'rcp_payment_gateways', array( $this, 'add_gateway' ) );
		add_action( 'rcp_payments_settings', array( $this, 'settings' ) );
	}

	/**
	 *
	 * @since  1.0.0
	 *
	 * @return mixed
	 */
	public function add_gateway( $gateways ) {
		$gateways['commbank'] = array(
			'label'       => 'Commbank',
			'admin_label' => 'Commbank',
			'class'       => 'RCPCommbank\\Gateway'
		);

		return $gateways;
	}

	/**
	 * Add Commbank settings
	 *
	 * @param $rcp_options
	 *
	 * @since  1.0.0
	 */
	public function settings( $rcp_options ) {
		?>
		<table class="form-table">
			<tr valign="top">
				<th colspan=2>
					<h3><?php _e( 'Commbank Settings', rcpcommbank()->get_id() ); ?></h3>
				</th>
			</tr>
			<tr>
				<th>
					<label for="rcp_settings[rcpcommbank_merchant_id]"><?php _e( 'MerchantID', rcpcommbank()->get_id() ); ?></label>
				</th>
				<td>
					<input class="regular-text" id="rcp_settings[rcpcommbank_merchant_id]" style="width: 300px;" name="rcp_settings[rcpcommbank_merchant_id]" value="<?php echo isset( $rcp_options['rcpcommbank_merchant_id'] ) ? $rcp_options['rcpcommbank_merchant_id'] : '' ; ?>" />
					<p class="description"><?php _e( 'Enter your merchant id.', rcpcommbank()->get_id() ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="rcp_settings[rcpcommbank_password]"><?php _e( 'Password', rcpcommbank()->get_id() ); ?></label>
				</th>
				<td>
					<input class="regular-text" id="rcp_settings[rcpcommbank_password]" style="width: 300px;" name="rcp_settings[rcpcommbank_password]" value="<?php echo isset( $rcp_options['rcpcommbank_password'] ) ? $rcp_options['rcpcommbank_password'] : '' ; ?>" />
					<p class="description"><?php _e( 'Enter your password.', rcpcommbank()->get_id() ); ?></p>
				</td>
			</tr>
		</table>

		<?php
	}
}