<?php
namespace WPLangSwitch;

class Wp_Language_Switch_Language_Provider {

	protected static $_instance;

	private $languages;

	public static function instance() {
		if (is_null( self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->languages = json_decode( file_get_contents( BASE_PATH . 'assets/data/languages.json'), true);
	}

	public function get_languages() {
		return $this->languages;
	}

	public function get_language_name( $iso, $nameProperty = 'name' ) {
		$key = strtolower( $iso );
		return isset( $this->languages[$key])
			? isset( $this->languages[$key][$nameProperty] )
				? $this->languages[$key][$nameProperty]
				: $this->languages[$key]['name']
			: '';
	}
}