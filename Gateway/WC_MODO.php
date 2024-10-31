<?php
/**
 * Class WC_MODO
 *
 * @package  Ecomerciar\MODO\Gateway\WC_MODO
 */

namespace Ecomerciar\MODO\Gateway;

use Ecomerciar\MODO\Helper\Helper;
use Ecomerciar\MODO\Sdk\MODOSdk;

defined( 'ABSPATH' ) || class_exists( '\WC_Payment_Gateway' ) || exit();

/**
 * Main Class MODO Payment.
 */
class WC_MODO extends \WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = \MODO::GATEWAY_ID;
		$this->has_fields         = false;
		$this->method_title       = __( 'MODO', \MODO::TEXT_DOMAIN );
		$this->method_description = __(
			'Pagá en un click con la app de MODO.',
			\MODO::TEXT_DOMAIN
		);

		// Define user set variables
		$this->title = __( 'MODO', \MODO::TEXT_DOMAIN );
		$this->instructions = $this->get_option(
			$this->description,
			$this->method_description
		);
		$this->icon         =
			Helper::get_assets_folder_url() . '/img/MODO-logo-icon80x18.png';
		$this->enabled      = $this->get_option( 'enabled' );
		$this->clientId     = $this->get_client_id();
		$this->clientSecret = $this->get_client_secret();
		$this->storeId      = $this->get_store_id();
		$this->debug        = $this->get_debug();

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Actions.
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'register_webhook',
			)
		);
		add_action(
			'woocommerce_thankyou_' . $this->id,
			array(
				$this,
				'thankyou_page',
			)
		);
	}	

	/**
	 * Get Client Id property
	 *
	 * @return string
	 */
	public function get_client_id() {
		return $this->get_option( 'wc_modo_clientid' );
	}

	/**
	 * Get Client Secret property
	 *
	 * @return string
	 */
	public function get_client_secret() {
		return $this->get_option( 'wc_modo_clientsecret' );
	}

	/**
	 * Get Store Id property
	 *
	 * @return string
	 */
	public function get_store_id() {
		return $this->get_option( 'wc_modo_storeid' );
	}

	/**
	 * Get Debug property
	 *
	 * @return string
	 */
	public function get_debug() {
		return $this->get_option( 'wc_modo_log_enabled' );
	}

	/**
	 * Register Webhook
	 *
	 * @return bool
	 */
	public function register_webhook() {
		$sdk = new MODOSdk(
			$this->get_option( 'wc_modo_clientid' ),
			$this->get_option( 'wc_modo_clientsecret' )
		);
		$sdk->create_access_token();

		$response = $sdk->register_webhook(
			get_site_url( null, '/wc-api/wc-modo' )
		);

		$webhook_url = isset( $response['callbackUrl'] )
			? $response['callbackUrl']
			: '';

		update_option( \MODO::WC_WEBHOOK_OPTION, $webhook_url );

		return true;
	}

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = include 'settings.php';
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id ID of Woo Order.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ) . "&modo_cta=true",
		);
	}

	/**
	 * Output for the order received page.
	 */
	public function thankyou_page() {
		// Nothing to add, but required to avoid Warnings.
	}

	/**
	 * Set if MODO must be available or not
	 *
	 * @param Array $available_gateways Array of Available Gateways.
	 *
	 * @return Array
	 */
	public static function available_payment_method( $available_gateways ) {
		if ( isset( $available_gateways[ \MODO::GATEWAY_ID ] ) && (
			! Helper::validate_credentials() ||
			! Helper::validate_currency() ) ) {			
			unset( $available_gateways[ \MODO::GATEWAY_ID ] );
		}

		return $available_gateways;
	}
}
