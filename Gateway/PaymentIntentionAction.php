<?php
/**
 * Class PaymentIntentionAction
 *
 * @package  Ecomerciar\MODO\Gateway\PaymentIntentionAction
 */

namespace Ecomerciar\MODO\Gateway;

use Ecomerciar\MODO\Helper\Helper;
use Ecomerciar\MODO\Sdk\MODOSdk;
use Ecomerciar\MODO\Gateway\WC_MODO;
/**
 * Orders Base Action Class
 */
abstract class PaymentIntentionAction {

	/**
	 * Run Action
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return array
	 */
	public static function run( $order_id ) {
		$sdk = new MODOSdk(
			Helper::get_option( 'clientid' ),
			Helper::get_option( 'clientsecret' ),
			Helper::get_option( 'storeid' )
		);
		$sdk->create_access_token();
		$response = $sdk->create_payment_intention( $order_id );
		$order    = wc_get_order( $order_id );
		if ( isset( $response['status'] ) && 'CREATED' === $response['status'] ) {
			$order->add_order_note(
				sprintf(
					__(
						'Se ha creado la intención de pago en MODO. ID %s',
						\MODO::TEXT_DOMAIN
					),
					$response['id']
				)
			);
			$order->update_meta_data(
				\MODO::META_ORDER_PAYMENT_ID,
				$response['id']
			);
			$order->save();
		} else {
			$order->add_order_note(
				sprintf(
					__(
						'No es posible crear la intención de pagos MODO.',
						\MODO::TEXT_DOMAIN
					)
				)
			);
		}

		return $response;
	}

	/**
	 * Validates Post parameters for Ajax Request
	 *
	 * @return bool/string
	 */
	public static function validate_ajax_request() {
		$errorCd = '';
		if ( ! isset( $_POST['nonce'] ) ) {
			$errorCd = 'missing nonce';
		} else {
			if ( ! wp_verify_nonce( wp_unslash( sanitize_text_field( $_POST['nonce'] ) ), \MODO::GATEWAY_ID ) ) {
				$errorCd = 'nonce';
			}
		}

		if ( ! isset( $_POST['order_id'] ) ) {
			$errorCd = 'missing order_id';
		} else {
			if ( empty( $_POST['order_id'] ) ) {
				$errorCd = 'order_id';
			}

			$order_id = filter_var( wp_unslash( $_POST['order_id'] ), FILTER_SANITIZE_NUMBER_INT );
			$order    = wc_get_order( $order_id );
			if ( ! $order ) {
				$errorCd = 'not order';
			}

			$payment_method = $order->get_payment_method();
			if ( empty( $payment_method ) ) {
				$errorCd = 'not payment method';
			}

			if ( \MODO::GATEWAY_ID !== $payment_method ) {
				$errorCd = 'not modo';
			}
		}		
		
		if( ! empty( $errorCd ) ){
			return $errorCd;
		}			

		return true;
	}

	/**
	 * Ajax Callback
	 */
	public static function ajax_callback_wp() {
		$ret_validate = static::validate_ajax_request();
		if ( $ret_validate !== true ) {
			wp_send_json_error( $ret_validate );
		}

		$order_id = filter_var( wp_unslash( $_POST['order_id'] ), FILTER_SANITIZE_NUMBER_INT );

		$ret = static::run( $order_id );
		if ( $ret ) {
			wp_send_json_success( $ret );
		} else {
			wp_send_json_error();
		}
	}
}
