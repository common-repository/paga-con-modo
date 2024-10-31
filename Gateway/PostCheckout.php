<?php
/**
 * Class PostCheckout
 *
 * @package  Ecomerciar\MODO\Gateway\PostCheckout
 */

namespace Ecomerciar\MODO\Gateway;

use Ecomerciar\MODO\Helper\Helper;
use Ecomerciar\MODO\Sdk\MODOSdk;
use \WC_Payment_Gateway;
use Ecomerciar\MODO\Gateway\WC_MODO;

defined( 'ABSPATH' ) || exit();
/**
 * Post Checkout Page Controller
 */
class PostCheckout {

	/**
	 * Run Action
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return bool
	 */
	public static function render( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( \MODO::GATEWAY_ID !== $order->get_payment_method() ) {
			return false;
		}
		?>
		<span class="cta-MODO-post-checkout" style="font-size:110%">
		<?php echo __( 'Haga clic en el botón para completar la compra', \MODO::TEXT_DOMAIN ); ?>
		<span>
		<div class="checkout-top-div-container post-checkout">
		<a id="modo-modal-cta" class="button btn btn-primary">
			<span>
				<?php echo __( 'Pagar con ', \MODO::TEXT_DOMAIN ); ?> 
			</span>
			<img src="<?php echo Helper::get_assets_folder_url(); ?>/img/MODO-icon-white.png" alt="<?php echo __( 'MODO', \MODO::TEXT_DOMAIN ); ?>">
		</a>
		<a id="select-gateway" class="button btn btn-secondary" href="<?php echo $order->get_checkout_payment_url( false );?>">
			<span>
				<?php echo __( 'Seleccionar otro método de pago ', \MODO::TEXT_DOMAIN ); ?> 
			</span>			
		</a>
		</div>		
		<style>
			#modo-modal-cta{
				background: #00D0A6;
				border-radius: 6px;
				color: white;	
				font-family: "Red Hat Display", sans-serif;			
			}
			#modo-modal-cta *{
				display: inline-block;
			}
			#modo-modal-cta img{
				max-height: 24px;
				margin-left: 10px;
				padding-bottom: 5px;
				vertical-align:middle;
			}
		</style>
	<script type="text/javascript">
		var wc_modo_settings = {
			action: "modo_payment_intention_action",
			ajax_url : "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
			ajax_nonce : "<?php echo wp_create_nonce( \MODO::GATEWAY_ID ); ?>", 
			order_id : "<?php echo $order_id; ?>",
			spinner_id : 'modo-container-spinner',
			spinner_url : '<?php echo Helper::get_assets_folder_url(); ?>/img/Spin-1s-16px.gif',
			modalCallbackURL: "<?php echo $order->get_checkout_payment_url( true ); ?>",
			modalCallbackURLSuccess: "<?php echo $order->get_checkout_order_received_url(); ?>",
			modo_cta_flag: <?php echo isset($_GET["modo_cta"])? "true" : "false";?>
		}
	</script>
		<?php
		wp_enqueue_script( 'modo-modal' );
		wp_enqueue_script( 'modo-gateway' );

		return true;
	}
}
