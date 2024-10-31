<?php
/**
 * Class MODOSdk
 *
 * @package  Ecomerciar\MODO\Helper\MODOSdk
 */

namespace Ecomerciar\MODO\Sdk;

use Ecomerciar\MODO\Api\MODOApi;
use Ecomerciar\MODO\Helper\Helper;

/**
 * Main Class MODO Sdk.
 */
class MODOSdk {

	private $api;
	private $client_id;
	private $client_secret;
	private $store_id;
	private $access_token;
	private $debug;

	const JSON = 'application/json';
	/**
	 * Constructor.
	 *
	 * @param string $client_id MODO Client ID.
	 * @param string $client_secret MODO Client Secret.
	 * @param string $store_id MODO Store ID.
	 * @param bool   $debug Check if debug is enabled or not.
	 * @param string $access_token MODO Access Token.
	 */
	public function __construct(
		string $client_id,
		string $client_secret,
		string $store_id = '',
		bool $debug = false,
		string $access_token = ''
	) {
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->store_id      = $store_id;
		$this->api           = new ModoApi(
			array(
				'clientId'     => $client_id,
				'clientSecret' => $client_secret,
				'debug'        => $debug,
			)
		);
		$this->set_access_token( $access_token );
		$this->debug = $debug;
	}

	/**
	 * Set access token
	 *
	 * @param string $access_token Access Token.
	 */
	private function set_access_token( string $access_token ) {
		$this->access_token = $access_token;
	}

	/**
	 * Check if system has credentials
	 *
	 * @return bool
	 */
	public function has_credentials(): bool {
		return ! empty( $this->client_id ) && ! empty( $this->client_secret );
	}

	/**
	 * Check if system has access token
	 *
	 * @return bool
	 */
	public function has_access_token(): bool {
		return ! empty( $this->access_token );
	}

	/**
	 * Create Access Token
	 *
	 * @return bool
	 */
	public function create_access_token() {
		$modo_access_token = get_option('modo_access_token', array());
		$current = time();

		if (!empty($modo_access_token) && isset($modo_access_token['token']) && isset($modo_access_token['expires_at'])) {
			if ( $current < $modo_access_token['expires_at'] ) {
				$this->set_access_token($modo_access_token['token']);
				Helper::log_info('Token founded');
				return true;
			}
		}

		$data_to_send = array(
			'username' => $this->client_id,
			'password' => $this->client_secret,
		);
		try {
			Helper::log_info('Try to get token');
			$res = $this->api->post(
				'/middleman/token',
				$data_to_send,
				array(
					'Content-Type' => self::JSON,
					'accept'       => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		$response = $this->handle_response( $res, __FUNCTION__ );
		if (!empty($response['accessToken'])) {
			$access_token_data = $this->get_access_token_data($response['accessToken']);

			if (empty($access_token_data)) {
				return false;
			}

			update_option('modo_access_token', $access_token_data);

			$this->set_access_token($access_token_data['token']);
			return true;
		}

		return false;
	}

	protected function get_access_token_data(string $access_token): array {
		$token_array = explode('.', $access_token);
		if (count($token_array) !== 3) {
			return array();
		}
		$payload = json_decode(base64_decode($token_array[1]), true);

		if ( isset($payload['exp']) ) {
			$expires_at = $payload['exp'];
		} else {
			$expires_at = time() + (3600 * 24);
		}

		return array(
			'token'      => 'Bearer ' . $access_token,
			'expires_at' => $expires_at,
		);
	}

	/**
	 * Create payment intention
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return array
	 */
	public function create_payment_intention( $order_id ) {
		$order        = wc_get_order( $order_id );
		$random_sufix_order = rand(10000, 99999);
		$data_to_send = array(
			'productName'         => __( 'Compra en ', \MODO::TEXT_DOMAIN ) . get_bloginfo( 'name' ),
			'price'               => $order->get_total(),
			'quantity'            => 1,
			'terminalId'          => '123',
			'storeId'             => $this->store_id,
			'externalIntentionId' => 'WC' . $order_id . '-' . $random_sufix_order,
			'currency'            => 'ARS',
		);
		try {
			$res = $this->api->post(
				'/ecommerce/payment-intention',
				$data_to_send,
				array(
					'Authorization' => $this->access_token,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get payment intention data
	 *
	 * @param string $payment_intention MODO Payment Intention.
	 *
	 * @return array
	 */
	public function get_payment_intention( $payment_intention ) {
		try {
			$res = $this->api->get(
				'/ecommerce/payment-intention/' . $payment_intention,
				array(),
				array(
					'Authorization' => $this->access_token,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Register Webhook into MODO
	 *
	 * @param string $url URL for linstening webhook messages.
	 *
	 * @return array
	 */
	public function register_webhook( $url ) {
		$data_to_send = array(
			'callbackUrl' => $url,
		);
		try {
			$res = $this->api->patch(
				'/middleman/',
				$data_to_send,
				array(
					'Authorization' => $this->access_token,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Handle Response
	 *
	 * @param array  $response Response data.
	 * @param string $function_name Function function is calling from.
	 *
	 * @return array
	 */
	protected function handle_response(
		$response = array(),
		string $function_name = ''
	): array {
		if ( 'create_access_token' === $function_name ) {
			if ( isset( $response['accessToken'] ) ) {
				return $response;
			} else {
				return array( 'accessToken' => '' );
			}
		}
		return $response;
	}
}
