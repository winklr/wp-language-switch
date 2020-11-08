<?php


namespace WPLangSwitch;


abstract class Wp_Language_Switch_Base {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Language provider
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var Wp_Language_Switch_Language_Provider $languageProvider provides language collection
	 */
	protected $languageProvider;


	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->languageProvider = Wp_Language_Switch_Language_Provider::instance();
	}

	/**
	 * Returns the path to the js file to enqueue
	 *
	 * @since 1.0.0
	 * @return string
	 */

	abstract function get_script_path();

	/**
	 * Returns the path to the css file to enqueue
	 *
	 * @since 1.0.0
	 * @return string
	 */

	abstract function get_style_path();


	/**
	 * Register the stylesheets.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, $this->get_style_path(), array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, $this->get_script_path(), array( 'jquery' ), $this->version );
	}

	/**
	 * Gets languages from options and returns corresponding data from language provider
	 *
	 * @return array
	 */

	public function get_languages_from_options(  ) {
		$options = get_option( 'wplangswitch_options' );

		$languages = [];
		foreach($options as $key => $value) {
			$languages[$key] = $this->languageProvider->get_language_name($key, 'nativeName');
		}
		return $languages;
	}

	/**
	 * Gets url target for given language from options
	 *
	 * @return string
	 */
	public function get_language_url( $iso ) {
		$options = get_option( 'wplangswitch_options' );

		return isset( $options[$iso]) ? $options[$iso] : '';
	}
}