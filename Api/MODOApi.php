<?php
/**
 * Class MODOApi
 *
 * @package  Ecomerciar\MODO\Api\MODOApi
 */

namespace Ecomerciar\MODO\Api;

defined( 'ABSPATH' ) || exit();
/**
 * MODO API Class
 */
class MODOApi extends ApiConnector implements ApiInterface {

	const API_BASE_URL = 'https://merchants.playdigital.com.ar/merchants';

	/**
	 * Class Constructor
	 *
	 * @param array $settings Modo Settings Object.
	 */
	public function __construct( array $settings = array() ) {
		$this->client_id     = $settings['clientId'];
		$this->client_secret = $settings['clientSecret'];
		$this->debug         = $settings['debug'];
	}

	/**
	 *  Get Base Url
	 *
	 * @return String
	 */
	public function get_base_url() {
		return $this::API_BASE_URL;
	}
}
