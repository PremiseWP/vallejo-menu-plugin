<?php
/**
 * Menu Item Controller
 *
 * @package Vallejo Menu Plugin
 */

/**
 * Model
 */
// Load Vallejo_Menu_Item_CPT class.
require_once VMENU_PATH . 'model/model-vmenu-item-cpt.php';


/**
 * Do logic
 *
 * @see  Vallejo_Menu_Item_CPT class
 */
// Register Menu item custom post type & thumbnail size.
Vallejo_Menu_Item_CPT::register_cpt();



/**
 * View
 */

if ( ! is_admin() ) :

	/**
	 * Add menu item template after content
	 *
	 * Do not override single-menu-item.php if exists in theme
	 *
	 * @param  string $content Menu Item content.
	 *
	 * @return string          Menu Item content formatted
	 */
	function vmenu_menu_item_after_content( $content ) {

		if ( is_single()
			&& get_post_type() === 'menu-item' ) {

			// Include menu item template after content
			// Add Price, Icons, Categories.
			ob_start();

			require_once VMENU_PATH . 'view/view-vmenu-item-after-content.php';

			$content = $content . ob_get_clean();
		}

		return $content;
	}

	add_filter( 'the_content', 'vmenu_menu_item_after_content' );

endif;
