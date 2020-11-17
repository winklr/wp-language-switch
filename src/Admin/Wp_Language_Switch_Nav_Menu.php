<?php

namespace WPLangSwitch\Admin;

class Wp_Language_Switch_Nav_Menu {

    public function __construct() {
	    add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 10, 2 );
    }

    // Render content of menu meta box on menu edit screen
	public function nav_menu_link() {?>
		<div id="posttype-wpls" class="posttypediv">
			<div id="tabs-panel-wpls" class="tabs-panel tabs-panel-active">
				<ul id ="wpls-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php esc_html_e('Languages', 'wp-language-switch')?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php esc_html_e( 'Languages', 'wp-language-switch' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#wpls_switcher">
						<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="wl-login-pop">
					</li>
				</ul>
			</div>
			<p class="button-controls">
        			<span class="list-controls">
        				<a href="/wordpress/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-page" class="select-all">Select All</a>
        			</span>
				<span class="add-to-menu">
        				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-wpls">
        				<span class="spinner"></span>
        			</span>
			</p>
		</div>
	<?php }

	/**
	 * Save our menu item options
	 *
	 * @since 1.1
	 *
	 * @param int $menu_id not used
	 * @param int $menu_item_db_id
	 */
	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0 ) {
		if ( empty( $_POST['menu-item-url'][ $menu_item_db_id ] ) || '#wpls_switcher' !== $_POST['menu-item-url'][ $menu_item_db_id ] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Security check as 'wp_update_nav_menu_item' can be called from outside WP admin
		if ( current_user_can( 'edit_theme_options' ) ) {
			check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

			// default values
			$options = array(
				'menu-item-' . $menu_item_db_id . '-language_0' => '',
				'menu-item-' . $menu_item_db_id . '-language_0_label' => '',
				'menu-item-' . $menu_item_db_id . '-language_1' => '',
				'menu-item-' . $menu_item_db_id . '-language_1_label' => ''
			);
			// Our jQuery form has not been displayed
			if ( empty( $_POST['menu-item-wpls-detect'][ $menu_item_db_id ] ) ) {
				if ( ! get_post_meta( $menu_item_db_id, '_wpls_menu_item', true ) ) { // Our options were never saved
					update_post_meta( $menu_item_db_id, '_wpls_menu_item', $options );
				}
			}
			else {
				foreach ( array_keys( $options ) as $opt ) {
					$options[ $opt ] = !empty( $_POST[ $opt ] ) ? sanitize_text_field( $_POST[ $opt ] ) : '';
				}
				update_post_meta( $menu_item_db_id, '_wpls_menu_item', $options ); // Allow us to easily identify our nav menu item
			}
		}
	}
}