<?php
/**
 * Class Helper
 *
 * @package  Ecomerciar\MODO\Helper\Helper
 */

namespace Ecomerciar\MODO\Helper;

/**
 * Helper Class
 */
class Helper {
	use LoggerTrait;
	use DebugTrait;
	use SettingsTrait;
	use DatabaseTrait;
	use ValidationsTrait;

	const MODO_CURRENCY         = 'ARS';
	const VALIDATION_OK_ICON    = '<span class="dashicons dashicons-saved" style="color:green;"></span>';
	const VALIDATION_ERROR_ICON = '<span class="dashicons dashicons-no-alt" style="color:red;"></span>';

	/**
	 * Returns an url pointing to the main filder of the plugin assets
	 *
	 * @return string
	 */
	public static function get_assets_folder_url() {
		return plugin_dir_url( \MODO::MAIN_FILE ) . 'assets';
	}
}
