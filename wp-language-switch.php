<?php

define( 'BASE_PATH', plugin_dir_path( __FILE__));
define( 'BASE_URL', plugin_dir_url( __FILE__));
define( 'ASSETS_BASE_PATH', plugins_url('assets', __FILE__));

require BASE_PATH . 'vendor/autoload.php';

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/winklr
 * @since             1.0.0
 * @package           Wp_Language_Switch
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Language Switch
 * Plugin URI:        https://github.com/winklr/wp-language-switch.git
 * Description:       Provides settings and language switcher menu to switch between two localized wordpress instances
 * Version:           1.0.1
 * Author:            Martin Winkler
 * Author URI:        https://github.com/winklr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-language-switch
 * Domain Path:       /languages
 */

use WPLangSwitch\Wp_Language_Switch;
use WPLangSwitch\Wp_Language_Switch_Activator;
use WPLangSwitch\Wp_Language_Switch_Deactivator;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_LANGUAGE_SWITCH_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-language-switch-activator.php
 */
function activate_wp_language_switch() {
	Wp_Language_Switch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-language-switch-deactivator.php
 */
function deactivate_wp_language_switch() {
	Wp_Language_Switch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_language_switch' );
register_deactivation_hook( __FILE__, 'deactivate_wp_language_switch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_language_switch() {

	$plugin = new Wp_Language_Switch();
	$plugin->run();

}
run_wp_language_switch();
