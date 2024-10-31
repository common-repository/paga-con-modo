<?php
/**
 * Hooks
 *
 * @package  Ecomerciar\MODO\
 */

defined( 'ABSPATH' ) || exit();

// --- Settings
add_filter(
	'plugin_action_links_' . plugin_basename( MODO::MAIN_FILE ),
	array(
		'MODO',
		'create_settings_link',
	)
);

// --- Payment Method
add_filter( 'woocommerce_payment_gateways', array( 'MODO', 'add_payment_method' ) );
add_filter(
	'woocommerce_available_payment_gateways',
	array(
		'\Ecomerciar\MODO\Gateway\WC_MODO',
		'available_payment_method',
	)
);

// --- Frontend buttons
add_action(
	'woocommerce_receipt_wc_modo',
	array( '\Ecomerciar\MODO\Gateway\PostCheckout', 'render' ),
	90
);
add_action( 'wp_enqueue_scripts', array( 'MODO', 'register_gateway_scripts' ) );

// --- Order Ajax Actions
add_action(
	'wp_ajax_modo_payment_intention_action',
	array(
		'\Ecomerciar\MODO\Gateway\PaymentIntentionAction',
		'ajax_callback_wp',
	)
);
add_action(
	'wp_ajax_nopriv_modo_payment_intention_action',
	array(
		'\Ecomerciar\MODO\Gateway\PaymentIntentionAction',
		'ajax_callback_wp',
	)
);

// --- Webhook
add_action(
	'woocommerce_api_wc-modo',
	array(
		'\Ecomerciar\MODO\Orders\Webhooks',
		'listener',
	)
);
