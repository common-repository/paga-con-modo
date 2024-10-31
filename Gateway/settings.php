<?php
/**
 * Settings.php
 *
 * @package  Ecomerciar\MODO\Gateway\
 */

namespace Ecomerciar\MODO\Gateway;

use Ecomerciar\MODO\Helper\Helper;

return apply_filters(
	'wc_modo_form_fields',
	array(
		'wc_modo_checkout_section'     => array(
			'title'       => __( 'MODO CheckOut', \MODO::TEXT_DOMAIN ),
			'type'        => 'title',
			'description' => '',
		),
		
		'enabled'                      => array(
			'title'   => __( 'Activar/Desactivar', \MODO::TEXT_DOMAIN ),
			'type'    => 'checkbox',
			'label'   => __(
				'Habilitar <i><b>MODO</b></i> en tu E-commerce',
				\MODO::TEXT_DOMAIN
			),
			'default' => 'yes',
		),		

		'wc_modo_credentials_section'  => array(
			'title'       => __( 'Credenciales', \MODO::TEXT_DOMAIN ),
			'type'        => 'title',
			'description' => '',
		),

		'wc_modo_credentials_subtitle' => array(
			'title'       => '',
			'type'        => 'title',
			'description' => __('Si todavía no tenés tus credenciales para operar con MODO, registrate <a href="https://modoencuestas.typeform.com/to/gbq8AMHH" target="_bank">aquí</a> y nos comunicaremos a la brevedad para ayudarte.', \MODO::TEXT_DOMAIN ),
		),

		'wc_modo_clientid'             => array(
			'title' => __( 'Client Id', \MODO::TEXT_DOMAIN ),
			'type'  => 'text',
		),

		'wc_modo_clientsecret'         => array(
			'title' => __( 'Client Secret', \MODO::TEXT_DOMAIN ),
			'type'  => 'password',
		),
		'wc_modo_storeid'              => array(
			'title' => __( 'Store Id', \MODO::TEXT_DOMAIN ),
			'type'  => 'text',
		),

		'wc_modo_validate_credentials' => array(
			'title'       => '',
			'type'        => 'title',
			'description' =>
				'<p class="submit"><button name="save" class="button-primary woocommerce-save-button" type="submit" value="' .
				__( 'Validar Credenciales', \MODO::TEXT_DOMAIN ) .
				'">' .
				__( 'Validar Credenciales', \MODO::TEXT_DOMAIN ) .
				'</button></p>',
		),

		'wc_modo_validations_section'  => array(
			'title'       => __( 'Validación', \MODO::TEXT_DOMAIN ),
			'type'        => 'title',
			'description' => Helper::validate_all_html(),
		),

		'wc_modo_testanddebug_section' => array(
			'title'       => __( 'Debug', \MODO::TEXT_DOMAIN ),
			'type'        => 'title',
			'description' => '',
		),

		'wc_modo_log_enabled'          => array(
			'title'       => __( 'Activar/Desactivar', \MODO::TEXT_DOMAIN ),
			'type'        => 'checkbox',
			'label'       => __( 'Activar Logs', \MODO::TEXT_DOMAIN ),
			'description' => sprintf(
				__(
					'Puede habilitar el debug del plugin para realizar un seguimiento de la comunicación efectuada entre el plugin y la API de MODO. Podrá ver el registro desde el menú <a href="%s">WooCommerce > Estado > Registros</a>.',
					\MODO::TEXT_DOMAIN
				),
				esc_url( get_admin_url( null, 'admin.php?page=wc-status&tab=logs' ) )
			),
			'default'     => 'no',
		),
	)
);
