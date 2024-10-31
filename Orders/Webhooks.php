<?php
/**
 * Class Webhooks
 *
 * @package  Ecomerciar\MODO\Helper\Webhooks
 */

namespace Ecomerciar\MODO\Orders;

use Ecomerciar\MODO\Helper\Helper;
use Ecomerciar\MODO\Sdk\MODOSdk;
use Ecomerciar\MODO\Gateway\WC_MODO;

defined( 'ABSPATH' ) || exit();

/**
 * WebHook's base Class
 */
class Webhooks {

	const OK    = 'HTTP/1.1 200 OK';
	const ERROR = 'HTTP/1.1 500 ERROR';

	/**
	 * Receives the webhook and check if it's valid to proceed
	 *
	 * @param string $data Webhook json Data for testing purpouses.
	 *
	 * @return bool
	 */
	public static function listener( string $data = null ) {

		// Takes raw data from the request.
		if ( is_null( $data ) || empty( $data ) ) {
			$json = file_get_contents( 'php://input' );
		} else {
			$json = $data;
		}

		Helper::log_info( 'Webhook recibido' );
		Helper::log(
			__FUNCTION__ .
				__( '- Webhook recibido de MODO:', \MODO::TEXT_DOMAIN ) .
				$json
		);

		$process = self::process_webhook( $json );

		if ( is_null( $data ) ) {
			// Real Webhook.
			if ( $process ) {
				header( self::OK );
			} else {
				header( self::ERROR );
				wp_die(
					__( 'WooCommerce MODO Webhook no válido.', \MODO::TEXT_DOMAIN ),
					'MODO Webhook',
					array( 'response' => 500 )
				);
			}
		} else {
			// For testing purpouse.
			return $process;			
		}
	}


	/**
	 * Process Webhook
	 *
	 * @param json $json Webhook data for.
	 *
	 * @return bool
	 */
	public static function process_webhook( $json ) {

		// Converts it into a PHP object.
		$data = json_decode( $json, true );

		if ( empty( $json ) || ! self::validate_input( $data ) ) {
			return false;
		}

		return self::handle_webhook( $data );		
	}

	/**
	 * Get Order Id from Data Json
	 *
	 * @param array $data Webhook data.
	 *
	 * @return int
	 */
	private static function get_order_id( array $data ) {
		$modo_id  = filter_var( $data['id'], FILTER_SANITIZE_STRING );
		return Helper::find_order_by_itemmeta_value(
			\MODO::META_ORDER_PAYMENT_ID,
			$modo_id
		);
	}

	/**
	 * Validates the incoming webhook
	 *
	 * @param array $data Webhook data to be validated.
	 *
	 * @return bool
	 */
	private static function validate_input( array $data ) {
		$return = true;
		$data   = wp_unslash( $data );
		if ( ! isset( $data['id'] ) || empty( $data['id'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook recibido sin id.', \MODO::TEXT_DOMAIN )
			);
			$return = false;
		}
		if ( ! isset( $data['external_intention_id'] ) || empty( $data['external_intention_id'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__(
						'- Webhook recibido sin external_intention_id.',
						\MODO::TEXT_DOMAIN
					)
			);
			$return = false;
		}
		if ( ! isset( $data['status'] ) || empty( $data['status'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook recibido sin status.', \MODO::TEXT_DOMAIN )
			);
			$return = false;
		} else {

			if ( 'ACCEPTED' !== $data['status']  &&  'REJECTED' !== $data['status'] &&  'CANCELLED' !== $data['status']) {
				Helper::log(
					__FUNCTION__ .
						__( '- Webhook recibido status: ' . $data['status']  , \MODO::TEXT_DOMAIN )
				);
				$return = false;
			}
		}

		if ( $return ) {
			/*Tiene MODO como medio de pago?*/
			$order_id = self::get_order_id( $data );
			if ( empty( $order_id ) || is_null( $order_id ) || ! is_int( $order_id ) ) {
				Helper::log(
					__FUNCTION__ .
						__(
							'- Webhook recibido sin orden relacionada.',
							\MODO::TEXT_DOMAIN
						)
				);
				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Handles and processes the webhook
	 *
	 * @param array $data webhook data to be processed.
	 *
	 * @return bool
	 */
	private static function handle_webhook( array $data ) {

		$order_id = self::get_order_id( $data );
		$order    = wc_get_order( $order_id );

		$sdk = new MODOSdk(
			Helper::get_option( 'clientid' ),
			Helper::get_option( 'clientsecret' ),
			Helper::get_option( 'storeid' )
		);
		$sdk->create_access_token();
		$response = $sdk->get_payment_intention( $data['id'] );

		if ( 'ACCEPTED' === $response['status'] ) {
			$order->payment_complete();
			$order->add_order_note(
				sprintf(
					__(
						'MODO - Pago Aceptado. ID %s',
						\MODO::TEXT_DOMAIN
					),
					$data['id']
				)
			);			
		} 
		if ( 'REJECTED' === $response['status'] ) {
			$order->add_order_note(
				sprintf(
					__(
						'MODO - Pago Rechazado. ID %s',
						\MODO::TEXT_DOMAIN
					),
					$data['id']
				)
			);
		} 		
		if ( 'CANCELLED' === $response['status'] ) {
			$order->add_order_note(
				sprintf(
					__(
						'MODO - Pago Cancelado. ID %s',
						\MODO::TEXT_DOMAIN
					),
					$data['id']
				)
			);
		} 
		return true;
	}
}
