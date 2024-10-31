<?php
/**
 * Plugin Name: Paga con MODO
 * Description: Integración entre MODO y WooCommerce
 * Version: 1.1.0
 * Requires PHP: 8.3.10
 * Author: MODO
 * Author URI: https://modo.com.ar
 * Text Domain: wc-modo
 * WC requires at least: 5.4
 * WC tested up to: 6.6.1
 *
 * @package Ecomerciar\MODO\MODO
 */

use Ecomerciar\MODO\Helper\Helper;
use Ecomerciar\MODO\Gateway\WC_MODO;

defined( 'ABSPATH' ) || exit();

add_action( 'plugins_loaded', array( 'MODO', 'init' ) );
add_action( 'activated_plugin', array( 'MODO', 'activation' ) );

/**
 * Plugin's base Class
 */
class MODO {

	const VERSION     = '1.1.0';
	const PLUGIN_NAME = 'MODO';
	const MAIN_FILE   = __FILE__;
	const MAIN_DIR    = __DIR__;
	const TEXT_DOMAIN = 'modo';

	const GATEWAY_ID            = 'wc_modo';
	const META_ORDER_PAYMENT_ID = '_MODO_PAYMENT_ID';
	const WC_WEBHOOK_OPTION     = 'wc_modo_webhook_url';

	/**
	 * Checks system requirements
	 *
	 * @return bool
	 */
	public static function check_system() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$system = self::check_components();

		if ( $system['flag'] ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">'
			. '<p>' .
				sprintf(
					__(
						'<strong>%1$s</strong> Requiere al menos %2$s versión %3$s o superior.',
						self::TEXT_DOMAIN
					),
					self::PLUGIN_NAME,
					$system['flag'],
					$system['version']
				) .
				'</p>'
			. '</div>';
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">'
			. '<p>' .
				sprintf(
					__(
						'WooCommerce debe estar activo antes de usar <strong>%s</strong>',
						self::TEXT_DOMAIN
					),
					self::PLUGIN_NAME
				) .
				'</p>'
			. '</div>';
			return false;
		}
		return true;
	}

	/**
	 * Check the components required for the plugin to work (PHP, WordPress and WooCommerce)
	 *
	 * @return array
	 */
	private static function check_components() {
		global $wp_version;
		$flag = $version = false;

		if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
			$flag    = 'PHP';
			$version = '8.0';
		} elseif ( version_compare( $wp_version, '5.4', '<' ) ) {
			$flag    = 'WordPress';
			$version = '5.4';
		} elseif (
			! defined( 'WC_VERSION' ) ||
			version_compare( WC_VERSION, '3.8.0', '<' )
		) {
			$flag    = 'WooCommerce';
			$version = '3.8.0';
		}

		return array(
			'flag'    => $flag,
			'version' => $version,
		);
	}

	/**
	 * Print Notices
	 *
	 * @return void
	 */
	public static function print_notices() {
		// Validations
		add_action(
			'admin_notices',
			function () {
				global $current_section;
				if ( $current_section === \MODO::GATEWAY_ID ) {
					echo '<div class="notice notice-info is-dismissible">'
					. '<h2>'
					. '<img style="float:left; max-height:16px;padding-right:5px;" src="' .
					Helper::get_assets_folder_url() .
					'/img/MODO-logo-xs.png' .
					'">' .
					__( 'Validaciones', \MODO::TEXT_DOMAIN ) .
					'</h2>'
					. Helper::validate_all_html()
					. '</div>';
				}
			}
		);
	}

	/**
	 * Inits our plugin
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! self::check_system() ) {
			return false;
		}

		spl_autoload_register(
			function ( $class ) {
				if ( strpos( $class, 'MODO' ) === false ) {
					return;
				}

				$name = str_replace( '\\', '/', $class );
				$name = str_replace( 'Ecomerciar/MODO/', '', $name );
				if ( $name === 'MODO' ) {
					return;
				}
				$path = plugin_dir_path( __FILE__ ) . $name . '.php';
				if (realpath($path)){
					require_once $path;
				}
			}
		);
		include_once __DIR__ . '/Hooks.php';
		Helper::init();
		self::load_textdomain();
		self::print_notices();
		return true;
	}

	/**
	 * Create a link to the settings page, in the plugins page
	 *
	 * @param array $links
	 * @return array
	 */
	public static function create_settings_link( array $links ) {
		$link =
			'<a href="' .
			esc_url(
				get_admin_url(
					null,
					'admin.php?page=wc-settings&tab=checkout&section=wc_modo'
				)
			) .
			'">' .
			__( 'Ajustes', self::TEXT_DOMAIN ) .
			'</a>';
		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Adds our shipping method to WooCommerce
	 *
	 * @param array $shipping_methods
	 * @return array
	 */
	public static function add_payment_method( $gateways ) {
		$gateways[] = '\Ecomerciar\MODO\Gateway\WC_MODO';
		return $gateways;
	}

	/**
	 * Loads the plugin text domain
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		load_plugin_textdomain(
			self::TEXT_DOMAIN,
			false,
			basename( dirname( __FILE__ ) ) . '/i18n/languages'
		);
	}

	/**
	 * Activation Plugin Actions
	 *
	 * @return void
	 */
	public static function activation( $plugin ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		self::redirect_to_onboarding_on_activation( $plugin );
	}

	/**
	 * Redirects to onboarding page on register_activation_hook
	 */
	public static function redirect_to_onboarding_on_activation( $plugin ) {
		if ( $plugin == plugin_basename( self::MAIN_FILE ) ) {
			exit(
				wp_redirect(
					admin_url(
						'admin.php?page=wc-settings&tab=checkout&section=' .
						self::GATEWAY_ID
					)
				)
			);
		}
		return true;
	}

	/**
	 * Registers all scripts to be loaded laters
	 *
	 * @return void
	 */
	public static function register_gateway_scripts() {
		wp_register_script(
			'modo-modal',
            'https://ecommerce-modal.modo.com.ar/bundle.js',
		);
		wp_register_script(
			'modo-gateway',
			Helper::get_assets_folder_url() . '/js/gateway.js',
			array( 'jquery', 'modo-modal' )
		);
		wp_register_style(
			'modo-redhat-font',
			'https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@700&display=swap'
		);
		$page_id = wc_get_page_id( 'checkout' );
		if (is_page($page_id)) {
			wp_enqueue_style('modo-redhat-font');
		}
	}
}
