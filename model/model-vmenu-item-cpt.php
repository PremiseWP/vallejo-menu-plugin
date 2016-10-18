<?php
/**
 * Menu item Custom Post Type Class
 *
 * Adds Menu item meta fields
 *
 * @package Vallejo Menu Plugin
 */

/**
 * Menu item Custom Post Type Class
 */
class Vallejo_Menu_Item_CPT {

	/**
	 * The defaults
	 *
	 * @see constructor
	 *
	 * @var array
	 */
	var $defaults = array();


	/**
	 * Constructor
	 *
	 * Add meta box (@see menu_item_metabox_add)
	 * Save meta box (@see menu_item_metabox_save)
	 */
	public function __construct() {

		$this->defaults = array(
			'short_description' => '',
			'price' => '1.00',
			'icons' => array(),
		);

		add_action( 'add_meta_boxes', array( $this, 'menu_item_metabox_add' ) );

		add_action( 'save_post', array( $this, 'menu_item_metabox_save' ) );

	}


	/**
	 * Add meta box
	 * and its default field values if new post
	 *
	 * @param string $post_type Post Type.
	 */
	public function menu_item_metabox_add( $post_type ) {

		$post_types = array( 'menu-item' );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'menu-item-options',
				__( 'Menu item info', 'vmenu' ),
				array( $this, 'menu_item_metabox_render' ),
				$post_type,
				'normal',
				'low'
			);
		}
	}


	/**
	 * Save meta box
	 *
	 * @param integer $post_id Post ID.
	 */
	public function menu_item_metabox_save( $post_id ) {

		if ( ! isset( $_POST['menu_item_nonce'] ) ) {

			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['menu_item_nonce'] ) ), 'menu_item_nonce' ) ) {

			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' )
			&& DOING_AUTOSAVE ) {

			return $post_id;
		}

		// Check the user's permissions.
		if ( ! isset( $_POST['post_type'] )
			&& 'menu-item' !== $_POST['post_type'] ) {

			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {

			return $post_id;
		}

		$mydata = wp_unslash( $_POST['menu_item_meta'] );

		// Sanitize price.
		$mydata['price'] = $this->sanitize_price( $mydata['price'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, 'menu_item_meta', $mydata );

	}


	/**
	 * Render the meta box and its fields
	 *
	 * @param  object $post Menu item post.
	 */
	public function menu_item_metabox_render( $post ) {

		wp_nonce_field( 'menu_item_nonce', 'menu_item_nonce' );

		$meta = get_post_meta( $post->ID, 'menu_item_meta', true );

		// Add the default meta field in the database if new post.
		if ( empty( $meta ) ) {

			add_post_meta( $post->ID, 'menu_item_meta', $this->defaults );

			$meta = $this->defaults;
		}
		?>

		<p><?php esc_attr_e( 'Enter the Menu item details in the following fields', 'vmenu' ); ?></p>

		<div class="premise-row">

		<?php

		// Build Icons + labels fields.
		$icons_fields = array();

		$icon_number = 1;

		// Icons options.
		$options = array( __( 'Select an icon' ) => '' );

		$options = $options + $this->vallejo_get_foodies_icons();

		// @link http://stackoverflow.com/questions/9508029/dropdown-select-with-images
		// Existing icons.
		foreach ( (array) $meta['icons'] as $icon ) {

			if ( ! empty( $icon['file'] ) ) {
				$icons_fields[] = array(
					'type'           => 'select',
					'name'           => 'menu_item_meta[icons][' . $icon_number . '][file]',
					'id'           	 => 'field-msdropdown' . $icon_number,
					'value'          => $icon['file'],
					'label'          => __( 'Icon', 'vmenu' ) . ' ' . $icon_number,
					'tooltip'	     => __( 'Add an icon to your menu item', 'vmenu' ),
					'wrapper_class'	 => 'span6 field-msdropdown premise-clear',
					'options'		 => $options,
					'add_filter'     => array(
						'premise_field_html',
						array( $this, 'msdropdown_input_field' ),
					),
				);

				$icons_fields[] = array(
					'type'           => 'text',
					'name'           => 'menu_item_meta[icons][' . $icon_number . '][label]',
					'value'          => $icon['label'],
					'label'          => __( 'Label', 'vmenu' ) . ' ' . $icon_number,
					'tooltip'	     => __( 'Icon label', 'vmenu' ),
					'wrapper_class'	 => 'span6',
					'maxlength'      => '256',
				);

				$icon_number++;
			}
		}

		// New icon.
		$icons_fields[] = array(
			'type'           => 'select',
			'name'           => 'menu_item_meta[icons][' . $icon_number . '][file]',
			'id'             => 'field-msdropdown' . $icon_number,
			'value'          => '',
			'label'          => __( 'Icon', 'vmenu' ) . ' ' . $icon_number,
			'tooltip'	     => __( 'Add an icon to your menu item', 'vmenu' ),
			'wrapper_class'	 => 'span6 field-msdropdown premise-clear',
			'options'		 => $options,
			'add_filter'     => array(
				'premise_field_html',
				array( $this, 'msdropdown_input_field' ),
			),
		);

		$icons_fields[] = array(
			'type'           => 'text',
			'name'           => 'menu_item_meta[icons][' . $icon_number . '][label]',
			'value'          => '',
			'label'          => __( 'Label', 'vmenu' ) . ' ' . $icon_number,
			'tooltip'	     => __( 'Icon label', 'vmenu' ),
			'wrapper_class'	 => 'span6',
			'maxlength'      => '256',
		);

		$field_builder = array(
			array(
				'type'           => 'text',
				'name'           => 'menu_item_meta[short_description]',
				'value'          => $meta['short_description'],
				'label'          => __( 'Short description', 'vmenu' ),
				'tooltip'	     => __( 'Short descriptions are displayed in the menu', 'vmenu' ),
				'wrapper_class'	 => 'span12',
				'maxlength'      => '256',
			),
			array(
				'type'           => 'text',
				'name'           => 'menu_item_meta[price]',
				'value'          => $meta['price'],
				'label'          => __( 'Price', 'vmenu' ) .
					' (' . premise_get_option( 'vmenu_currency' ) . ')',
				'placeholder'    => __( '19.90', 'vmenu' ),
				'tooltip'	     => __( 'Item price (without currency)', 'vmenu' ),
				'wrapper_class'	 => 'span12',
				'required'       => 'required',
				'maxlength'      => '10',
			),
		);

		// Add Icons fields.
		$field_builder = array_merge( $field_builder, $icons_fields );

		premise_field_section( $field_builder ); ?>

		</div>

		<?php

	}


	/**
	 * Build our msdropdown input element from the select field
	 *
	 * @uses premise_input_field filter
	 *
	 * @param  string $field_html the html for the field default.
	 * @return string             the new html for the field
	 */
	public function msdropdown_input_field( $field_html ) {

		static $icon_number = 1;

		$js  = '<script>jQuery(document).ready(function(e) { jQuery("#field-msdropdown' . $icon_number++ . '").msDropdown({}); });</script>';

		$icons = $this->vallejo_get_foodies_icons();

		foreach ( $icons as $key => $icon ) {
			$field_html = str_replace(
				array( 'value="' . $icon . '"', '>' . $key . '</option>' ),
				array( 'value="' . $icon . '" data-image="' . $icon . '"', '></option>' ),
				$field_html
			);
		}

		return $js . $field_html;
	}


	/**
	 * Sanitize price
	 * Remove currency and limit to 1 to 6 digits and eventual . followed by 1 to 2 decimal digits
	 *
	 * @param  string $price Price.
	 *
	 * @return string Sanitized price or default price
	 */
	public function sanitize_price( $price ) {

		// Remove not numeric or dots.
		$price = preg_replace( '([^0-9.])', '', $price );

		$float_price = floatval( $price );

		// Round float to 2 decimal digits.
		$float_price = round( $float_price, 2 );

		// Test if right format: 1 to 6 digits and eventual . followed by 1 to 2 decimal digits.
		if ( preg_match( '^[0-9]{1,6}([.][0-9]{1,2})?^', $float_price ) !== 1 ) {
			// If not, reset price.
			$price = $this->defaults['price'];

		} elseif ( $float_price != $price ) {

			$price = $float_price;
		}

		// var_dump( $mydata['price'] ); exit;

		return $price;
	}


	/**
	 * Get Foodies icons
	 *
	 * @return array Icons (full URL), array starts at index 1
	 */
	public function vallejo_get_foodies_icons() {

		$theme_path = get_template_directory();

		$icons = glob( $theme_path . '/img/foodies-icons/*.png' );

		$i = 1;

		$return = array();

		foreach ( $icons as $icon ) {

			$return[ $i ] = str_replace( $theme_path, THEME_URL, $icon );

			$i++;
		}

		return $return;
	}


	/**
	 * Register Menu item custom post type
	 * Register Thumbnail size
	 *
	 * @example Vallejo_Menu_Item_CPT::register_cpt();
	 *
	 * @see Premise WP Framework for more information
	 * @link https://github.com/Premise-WP/Premise-WP
	 *
	 * @return void
	 */
	public static function register_cpt() {

		if ( class_exists( 'PremiseCPT' ) ) {

			/**
			 * Register Menu item custom post type
			 *
			 * Holds instance of new CPT
			 *
			 * @var object
			 */
			$menu_item = new PremiseCPT(
				'menu-item',
				array(
					'supports' => array(
						'title',
						'thumbnail',
						'editor',
					),
					// @see https://developer.wordpress.org/resource/dashicons/#welcome-widgets-menus
					'menu_icon' => 'dashicons-welcome-widgets-menus',
				)
			);

			$menu_item->register_taxonomy(
				array(
					'taxonomy_name' => 'menu-category',
					'singular' => 'Menu Category',
					'plural' => 'Menu Categories',
					'slug' => 'menu-category',
				),
				array(
					'hierarchical' => false, // No sub-categories.
					'show_tagcloud' => false
				)
			);
		}

		if ( is_admin() ) {

			/**
			 * Calls the Vallejo_Menu_Item_CPT class on the post edit screen.
			 */
			function call_vallejo_menu_item_cpt() {

				new Vallejo_Menu_Item_CPT();

			}

			add_action( 'load-post.php', 'call_vallejo_menu_item_cpt' ); // Add Menu item post meta fields.
			add_action( 'load-post-new.php', 'call_vallejo_menu_item_cpt' ); // Add Menu item post meta fields.

		}

		if ( function_exists( 'add_theme_support' ) ) {
			/**
			 * Thumbnail size
			 *
			 * Menu item featured: 68*68
			 */
			add_image_size( 'menu-item-featured', 68, 68, true );
		}

	}



	/**
	 * Get the icons associated to a menu item
	 *
	 * @example Vallejo_Menu_Item_CPT::get_menu_item_icons( $icons );
	 *
	 * @see  Vallejo_Menu_Item_CPT::menu_item_metabox_render()
	 *
	 * @param  array $icons icons + labels array.
	 *
	 * @return outputs icons images HTML + label as title attribute
	 */
	public static function get_menu_item_icons( $icons ) {

		$icons_html = '';

		foreach ( (array) $icons as $icon ) {

			if ( ! empty( $icon['file'] ) ) {
				$icons_html .= '<span class="vmenu-icon-label">' . $icon['label'] . '</span>';
				$icons_html .= '<img src="' . $icon['file'] . '" title="' . $icon['label'] . '" height="32px" />';
			}
		}

		return $icons_html;
	}



	/**
	 * Get menu item Subtitle (short description)
	 *
	 * @example Vallejo_Menu_Item_CPT::get_menu_item_subtitle( $post_ID );
	 *
	 * @param  int $post_ID Post ID.
	 *
	 * @return string
	 */
	public static function get_menu_item_subtitle( $post_ID ) {

		$post_type = get_post_type( $post_ID );

		$subtitle = '';

		$subtitle = get_post_meta( $post_ID, 'menu_item_meta', true );
		$subtitle = $subtitle['short_description'];

		return $subtitle;

	}
}
