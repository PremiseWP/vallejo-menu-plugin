<?php
/**
 * Menu Page Controller
 *
 * @package Vallejo Menu Plugin
 */

/**
 * Model
 */

// Do logic.
/**
 * Add [vmenu] shortcode
 *
 * @see http://codex.wordpress.org/Function_Reference/add_shortcode
 */
add_shortcode( 'vmenu', 'vmenu_menu_page' );


/**
 * View
 *
 * @param array $atts Attributes.
 */
function vmenu_menu_page( $atts ) {

	$atts = shortcode_atts( array(
		'number' => '1',
	), $atts, 'vmenu' );

	// Check Menu number exists.
	if ( ! ( $cats = premise_get_option( 'vmenu_menus[' . $atts['number'] . '][menu-categories]' ) )
		|| empty( $cats['1'] ) ) {

		return '';
	}

	ob_start();

	require_once VMENU_PATH . 'view/view-vmenu-page-menu.php';

	return ob_get_clean();
}

