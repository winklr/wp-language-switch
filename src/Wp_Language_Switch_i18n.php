<?php

namespace WPLangSwitch;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/winklr
 * @since      1.0.0
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 * @author     Martin Winkler <martinwinkler.mw@gmail.com>
 */
class Wp_Language_Switch_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wp-language-switch',
			false,
			 dirname( plugin_basename( BASE_PATH ) ) . '/languages/'
		);

	}
}
