<?php

namespace WPLangSwitch\Admin;

use WPLangSwitch\Compatibility\TimberCompat;
use WPLangSwitch\Wp_Language_Switch_Base;

define('ADMIN_BASE_PATH', plugin_dir_path(__FILE__));

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/admin
 * @author     Martin Winkler <martinwinkler.mw@gmail.com>
 */
class Wp_Language_Switch_Admin extends Wp_Language_Switch_Base {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @param   string    $plugin_name The name of this plugin.
	 * @param   string    $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		parent::__construct($plugin_name, $version);
		// Set Timber locations using compatibility layer
		TimberCompat::set_locations(__DIR__ . '/Views');
	}

	/**
	 * Returns the path to the css file to enqueue
	 *
	 * @since 1.0.0
	 * @return string
	 */

	public function get_style_path() {
		return ASSETS_BASE_PATH . '/css/wp-language-switch-admin.css';
	}
	/**
	 * Returns the path to the js file to enqueue
	 *
	 * @since 1.0.0
	 * @return string
	 */

	function get_script_path() {
		return ASSETS_BASE_PATH . '/js/wp-language-switch-admin.js';
	}

	public function enqueue_nav_scripts() {
		$screen = get_current_screen();
		if ('nav-menus' != $screen->base) {
			return;
		}

		wp_enqueue_script('wpls_nav_menu', ASSETS_BASE_PATH . '/js/nav-menu.js', array('jquery'), $this->version);

		$data = array(
			'strings' => array(
				'select_default' => __('Select Language', 'wp-language-switch'),
				'label_input_placeholder' => __('Specify (optional) label', 'wp-language-switch')
			), // The strings for the options
			'title'   => __('Languages', 'wp-language-switch'), // The title
			'val'     => array(),
			'languages' => $this->get_languages_from_options()
		);

		// Get all language switcher menu items
		$items = get_posts(
			array(
				'numberposts' => -1,
				'nopaging'    => true,
				'post_type'   => 'nav_menu_item',
				'fields'      => 'ids',
				'meta_key'    => '_wpls_menu_item',
			)
		);

		// The options values for the language switcher
		foreach ($items as $item) {
			$data['val'][$item] = get_post_meta($item, '_wpls_menu_item', true);
		}

		// Send all these data to javascript
		wp_localize_script('wpls_nav_menu', 'wpls_data', $data);
	}

	public function add_menu_page() {
		add_options_page(
			__('WP Language Switch', 'wp-language-switch'),
			__('WP Language Switch Options', 'wp-language-switch'),
			'manage_options',
			'wp_language_switch_admin_page',
			array($this, 'render_menu_page')
		);
	}

	public function render_menu_page() {
		// Add debugging to see user capabilities
		if (!current_user_can('manage_options')) {
			$current_user = wp_get_current_user();
			$roles = (array) $current_user->roles;
			wp_die('You do not have sufficient permissions to access this page. Your roles: ' .
				implode(', ', $roles) . '. Required capability: manage_options');
		}

		$data = [];

		try {
			// Check if Timber is available
			if (!class_exists('\\Timber\\Timber')) {
				echo '<div class="error"><p>Timber library is not available. Please install and activate the Timber plugin.</p></div>';
				return;
			}

			// Get languages from provider
			if ($this->languageProvider) {
				$data['languages'] = $this->languageProvider->get_languages();
			} else {
				$data['languages'] = []; // Fallback if provider is null
				echo '<div class="notice notice-warning"><p>Language provider not initialized.</p></div>';
			}

			$data['title'] = get_admin_page_title();
			$data['options'] = get_option('wplangswitch_options', []);
			$data['message'] = !empty($_POST['wplangswitch_options']) ? __('Settings saved', 'wp-language-switch') : '';

			// Debug output
			echo '<!-- DEBUG: Timber version: ' . (class_exists('\\Timber\\Timber') ? \Timber\Timber::$version : 'Not loaded') . ' -->';
			echo '<!-- DEBUG: Template location: ' . __DIR__ . '/Views -->';

			// Use compatibility layer for rendering
			if (class_exists('\\Timber\\Timber')) {
				if (
					method_exists('\\WPLangSwitch\\Compatibility\\TimberCompat', 'is_timber_2') &&
					\WPLangSwitch\Compatibility\TimberCompat::is_timber_2()
				) {
					// Make sure the template exists
					if (!file_exists(__DIR__ . '/Views/admin.twig')) {
						echo '<div class="error"><p>Template file not found: ' . __DIR__ . '/Views/admin.twig</p></div>';
						return;
					}
					echo \Timber\Timber::compile('@wpls/admin.twig', $data);
				} else {
					// Make sure the template exists
					if (!file_exists(__DIR__ . '/Views/admin.twig')) {
						echo '<div class="error"><p>Template file not found: ' . __DIR__ . '/Views/admin.twig</p></div>';
						return;
					}
					echo \Timber\Timber::compile('admin.twig', $data);
				}
			} else {
				echo '<div class="error"><p>Timber is not available but is required for rendering the admin panel.</p></div>';
			}
		} catch (\Exception $e) {
			// Show error with more details
			echo '<div class="error"><p>Error: ' . $e->getMessage() . '</p>';
			echo '<p>File: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>';
			echo '<p>Trace:</p><pre>' . $e->getTraceAsString() . '</pre></div>';
		}
	}

	public function save_languages($data) {
		if (!current_user_can('manage_options')) {
			wp_die('You do not have sufficient permissions to access this page.');
		}

		if (isset($_POST['wplangswitch_options'])) {

			check_admin_referer('save_languages', '_wpls_nonce');

			$data = $_POST['wplangswitch_options'];

			$languages = array();

			for ($i = 0; $i < sizeof($data['language']); ++$i) {
				$language = trim(sanitize_text_field($data['language'][$i]));
				$target = trim(sanitize_text_field($data['target'][$i]));

				if ($language == '' && $target == '') {
					continue;
				} else {
					$languages[$language] = $target;
				}
			}

			update_option('wplangswitch_options', $languages);
		}
	}

	// adds a metabox to the menu edit screen (left side, accordion menu)
	public function add_nav_menu() {
		$custom_nav = new Wp_Language_Switch_Nav_Menu();
		add_meta_box(
			'wl_login_nav_link',
			__('WP Language Switcher'),
			array($custom_nav, 'nav_menu_link'),
			'nav-menus',
			'side',
			'high'
		);
	}
}
