<?php

namespace WPLangSwitch;

class Wp_Language_Switch_Language_Provider {

	protected static $_instance;

	private $languages;

	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$language_file = BASE_PATH . 'assets/data/languages.json';

		if (file_exists($language_file) && is_readable($language_file)) {
			$json_content = file_get_contents($language_file);
			if ($json_content) {
				$this->languages = json_decode($json_content, true);
			}
		}

		// Fallback if file can't be loaded
		if (empty($this->languages)) {
			$this->languages = $this->get_fallback_languages();
		}
	}

	/**
	 * Fallback language data if JSON file can't be loaded
	 */
	private function get_fallback_languages() {
		return [
			'en' => ['name' => 'English', 'nativeName' => 'English'],
			'de' => ['name' => 'German', 'nativeName' => 'Deutsch'],
			'fr' => ['name' => 'French', 'nativeName' => 'Français'],
			'es' => ['name' => 'Spanish', 'nativeName' => 'Español'],
		];
	}

	public function get_languages() {
		return $this->languages;
	}

	public function get_language_name($iso, $nameProperty = 'name') {
		$key = strtolower($iso);

		if (!isset($this->languages[$key])) {
			return '';
		}

		if (isset($this->languages[$key][$nameProperty])) {
			return $this->languages[$key][$nameProperty];
		}

		return $this->languages[$key]['name'];
	}
}
