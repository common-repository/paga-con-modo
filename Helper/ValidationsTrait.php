<?php
/**
 * Class ValidationsTrait
 *
 * @package  Ecomerciar\MODO\Helper\ValidationsTrait
 */

namespace Ecomerciar\MODO\Helper;

use Ecomerciar\MODO\Sdk\MODOSdk;
use Ecomerciar\MODO\Gateway\WC_MODO;
/**
 * Validations Trait
 */
trait ValidationsTrait {

	/**
	 * Validate HTML
	 *
	 * @param bool   $bool Value to test.
	 * @param string $message_ok Message to show if $bool is true.
	 * @param string $message_error Message to show if $bool is false.
	 *
	 * @return Array
	 */
	public static function validate_html( $bool, $message_ok, $message_error ) {
		return $bool
			? self::VALIDATION_OK_ICON . $message_ok
			: self::VALIDATION_ERROR_ICON . $message_error;
	}

	/**
	 * Validate Credentials
	 *
	 * @return bool
	 */
	public static function validate_credentials() {
		$sdk = new MODOSdk(
			self::get_option( 'clientid' ),
			self::get_option( 'clientsecret' )
		);
		$sdk->create_access_token();

		return $sdk->has_access_token();
	}

	/**
	 * Validate Currency
	 *
	 * @return bool
	 */
	public static function validate_currency() {
		return get_woocommerce_currency() === self::MODO_CURRENCY;
	}

	/**
	 * Validate Webhook
	 *
	 * @return bool
	 */
	public static function validate_webhook() {
		 return ! empty( get_option( \MODO::WC_WEBHOOK_OPTION ) );
	}

	/**
	 * Validate Credentials HTML
	 *
	 * @return bool
	 */
	public static function validate_credentials_html() {
		return self::validate_html(
			self::validate_credentials(),
			__( 'Credenciales válidas.', \MODO::TEXT_DOMAIN ),
			__( 'Credenciales inválidas.', \MODO::TEXT_DOMAIN )
		);
	}

	/**
	 * Validate Currency HTML
	 *
	 * @return string
	 */
	public static function validate_currency_html() {
		return self::validate_html(
			self::validate_currency(),
			__(
				'La moneda configurada en WooCommerce es ARS - Pesos Argentinos.',
				\MODO::TEXT_DOMAIN
			),
			__(
				'La moneda configurada en WooCommerce debe ser ARS - Pesos Argentinos.',
				\MODO::TEXT_DOMAIN
			)
		);
	}

	/**
	 * Validate Webhook HTML
	 *
	 * @return string
	 */
	public static function validate_webhook_html() {
		return self::validate_html(
			self::validate_webhook(),
			sprintf(
				__(
					'Para recibir notificaciones acerca de tus envíos con MODO, se ha configurado un WebHook automáticamente en MODO con esta URL: <strong>%s</strong> con el método POST.',
					\MODO::TEXT_DOMAIN
				),
				get_option( \MODO::WC_WEBHOOK_OPTION )
			),
			__(
				'Un WebHook será configurado en MODO cuando las credenciales sean guardadas.',
				\MODO::TEXT_DOMAIN
			)
		);
	}

	/**
	 * Validations HTML
	 *
	 * @return string
	 */
	public static function validate_all_html() {
		return '<p>' .
			self::validate_currency_html() .
			'</p><p>' .
			self::validate_credentials_html() .
			'</p><p>' .
			self::validate_webhook_html() .
			'</p>';
	}
}
