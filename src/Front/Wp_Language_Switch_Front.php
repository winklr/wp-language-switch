<?php

namespace WPLangSwitch\Front;

use WPLangSwitch\Wp_Language_Switch_Base;
use WPLangSwitch\Wp_Language_Switch_LanguageProvider;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/winklr
 * @since      1.0.0
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/public
 * @author     Martin Winkler <martinwinkler.mw@gmail.com>
 */
class Wp_Language_Switch_Front extends Wp_Language_Switch_Base {

	/**
	 * Returns the path to the js file to enqueue
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function get_script_path() {
		return ASSETS_BASE_PATH . '/js/wp-language-switch-public.js';
	}

	/**
	 * Returns the path to the css file to enqueue
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function get_style_path() {
		return ASSETS_BASE_PATH . '/css/wp-language-switch-public.css';
	}

	public function filter_nav_menu_items( $menu_item ) {
		$post_type = ($menu_item->object); //gets post type

		return $menu_item;
	}

	public function get_nav_menu_items( $items ) {
		if ( doing_action( 'customize_register' ) ) { // needed since WP 4.3, doing_action available since WP 3.9
			return $items;
		}

		// The customizer menus does not sort the items and we need them to be sorted before splitting the language switcher
		usort( $items, array( $this, 'usort_menu_items' ) );

		$new_items = array();
		$offset = 0;

		foreach ( $items as $item ) {
			if ( $languages = get_post_meta( $item->ID, '_wpls_menu_item', true ) ) {
				$i = 0;

				foreach ($languages as $key => $langIso) {
					$lang_item = clone $item;
					$lang_item->ID = $this->get_item_id( $key ); // A unique ID
					$lang_item->title = $this->get_item_title( $langIso );
					$lang_item->attr_title = '';
					$lang_item->url = $this->get_language_url( $langIso);
					$lang_item->classes = $this->get_item_classes( $item , $langIso);
					$lang_item->menu_order += $offset + $i++;

					$new_items[] = $lang_item;
				}
				$offset += $i - 1;
			} else {
				$item->menu_order += $offset;
				$new_items[] = $item;
			}
		}
		return $new_items;
	}

	/**
	 * Make unique item id
	 *
	 * @example get_item_id('menu-item-1234-language_0') => '1234-language_0'
	 *
	 * @param $id string item id e.g. 'menu-item-1234-language_0'
	 *
	 * @return string
	 */

	public function get_item_id( $id ) {
		$matches = [];
		preg_match( '/(\d+-language_\d+)$/', $id, $matches);

		return !empty( $matches[1]) ? $matches[1] : $id;
	}

	/**
	 * Get the language title from given iso
	 *
	 * @example get_item_title('en') => 'English'
	 *
	 * @param $iso string language iso e.g. 'de', 'en'
	 *
	 * @return string
	 */

	public function get_item_title( $iso ) {

		return $this->languageProvider->get_language_name( $iso, 'nativeName' );
	}

	/**
	 * Get item classes. If current site's url matches items url, 'active' class is added
	 *
	 * @param object $menu_item
	 *
	 * @return array
	 */

	public function get_item_classes( $menu_item, $iso ) {
		$wp_url = parse_url( home_url(), PHP_URL_HOST );
		$target = parse_url( $this->get_language_url( $iso ), PHP_URL_HOST );

		// add 'active' if item target matches current site url
		$classes = $wp_url === $target ? array('wpls-nav-item', 'active') : array('wpls-nav-item');

		return array_merge( $menu_item->classes, $classes);
	}

	/**
	 * Sort menu items by menu order
	 *
	 * @since 1.7.9
	 *
	 * @param object $a The first object to compare
	 * @param object $b The second object to compare
	 * @return int -1 or 1 if $a is considered to be respectively less than or greater than $b.
	 */
	protected function usort_menu_items( $a, $b ) {
		return ( $a->menu_order < $b->menu_order ) ? -1 : 1;
	}
}
