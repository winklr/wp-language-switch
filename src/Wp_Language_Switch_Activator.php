<?php

namespace WPLangSwitch;

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/winklr
 * @since      1.0.0
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 * @author     Martin Winkler <martinwinkler.mw@gmail.com>
 */
class Wp_Language_Switch_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if (! get_option('wplangswitch_options')) {
			update_option( 'wplangswitch_options', array());
		}
	}
}
