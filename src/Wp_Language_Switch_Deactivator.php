<?php

namespace WPLangSwitch;

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/winklr
 * @since      1.0.0
 *
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Language_Switch
 * @subpackage Wp_Language_Switch/includes
 * @author     Martin Winkler <martinwinkler.mw@gmail.com>
 */
class Wp_Language_Switch_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		// Delete menu language switchers
		$ids = get_posts(
			array(
				'post_type'   => 'nav_menu_item',
				'numberposts' => -1,
				'nopaging'    => true,
				'fields'      => 'ids',
				'meta_key'    => '_wpls_menu_item',
			)
		);

		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
	}

}
