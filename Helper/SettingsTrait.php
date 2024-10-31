<?php
/**
 * Class SettingsTrait
 *
 * @package  Ecomerciar\MODO\Helper\SettingsTrait
 */

namespace Ecomerciar\MODO\Helper;

/**
 * Settings Trait
 */
trait SettingsTrait {

	/**
	 * Gets a plugin option
	 *
	 * @param string  $key Key value searching for.
	 * @param boolean $default A dafault value in case Key is not founded.
	 * 
	 * @return mixed
	 */
	public static function get_option( string $key, $default = false ) {
		return isset( self::get_options()[ $key ] ) &&
			! empty( self::get_options()[ $key ] )
			? self::get_options()[ $key ]
			: $default;
	}

	/**
	 * Get options
	 *
	 * @param string  $gateway Gateway Name.
	 * 
	 * @return Array
	 */
	public static function get_options( $gateway = 'wc_modo' ) {
		$option = get_option( 'woocommerce_' . $gateway . '_settings' );
		return array(
			'enabled'      => isset( $option['enabled'] ) ? $option['enabled'] : 'no',
			'clientid'     => isset( $option['wc_modo_clientid'] )
				? $option['wc_modo_clientid']
				: '',
			'clientsecret' => isset( $option['wc_modo_clientsecret'] )
				? $option['wc_modo_clientsecret']
				: '',
			'storeid'      => isset( $option['wc_modo_storeid'] )
				? $option['wc_modo_storeid']
				: '',
			'debug'        => isset( $option['wc_modo_log_enabled'] )
				? $option['wc_modo_log_enabled']
				: '',
		);
	}
}
