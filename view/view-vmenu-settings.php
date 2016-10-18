<?php
/**
 * Settings View
 *
 * @package Vallejo Menu Plugin
 */

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Vallejo Menu Settings', 'vmenu' ); ?></h2>

	<?php // Auto Share notifications.
	if ( isset( $_POST['fb'] ) ) :

		$this->notification( __( 'Facebook Settings updated!' ) );

	elseif ( isset( $_POST['twit'] ) ) :

		$this->notification( __( 'Twitter Settings updated!' ) );

	elseif ( isset( $_POST['bsettngs'] ) ) :

		$this->notification( __( 'Basic Settings updated!' ) );

	endif; ?>

	<form method="post" action="options.php" enctype="multipart/form-data" id="vmenu-option-form" class="premise-admin">

	<?php
		submit_button( __( 'Save Settings', 'vmenu' ), 'button button-primary right' );

		// This prints out all hidden setting fields.
		settings_fields( $this->options_group );
		do_settings_sections( $this->options_group );

		ob_start();

		// Currency.
		premise_field(
			'text',
			array(
				'name'          => 'vmenu_currency',
				'label'         => __( 'Currency', 'vmenu' ),
				'tooltip'       => __( 'Currency symbol. Displayed before prices.', 'vmenu' ),
				'wrapper_class' => 'span6',
				'required'      => 'required',
				'maxlength'     => '6',
			)
		);

		// Columns.
		premise_field(
			'select',
			array(
				'name'          => 'vmenu_columns',
				'label'         => __( 'Display columns', 'vmenu' ),
				'tooltip'       => __( 'Display menu items in 1, 2 or 3 columns.', 'vmenu' ),
				'wrapper_class' => 'span6',
				'options'       => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		// Categories options.
		$options = array( __( 'Select a Category', 'vmenu' ) => '' );

		$menu_categories_objects = get_categories( array( 'taxonomy' => 'menu-category' ) );

		foreach ( $menu_categories_objects as $menu_categories_object ) {

			$options[ $menu_categories_object->name ] = $menu_categories_object->term_id;
		}

		$menu_number = 1;

		// Existing menus.
		foreach ( (array) $this->options['vmenu_menus'] as $menu ) {

			$category_number = 1;

			// Build Category fields.
			$category_fields = array();

			// Existing categories.
			foreach ( (array) $menu['menu-categories'] as $menu_category ) {

				if ( ! empty( $menu_category ) ) {

					$category_fields[] = array(
						'type'           => 'select',
						'name'           => 'vmenu_menus[' . $menu_number . '][menu-categories][' . $category_number . ']',
						'options'        => $options,
						'label'	   		 => __( 'Menu category', 'vmenu' ) . ' ' . $category_number,
						'tooltip'	     => __( 'Note: Menu Items are ordered by Date.', 'vmenu' ),
						'taxonomy'		 => 'menu-category',
						'wrapper_class'  => 'span6',
					);

					$category_number++;
				}
			}

			if ( $category_fields ) {

				// New category.
				$category_fields[] = array(
					'type'           => 'select',
					'name'           => 'vmenu_menus[' . $menu_number . '][menu-categories][' . $category_number . ']',
					'options'        => $options,
					'label'	   		 => __( 'Menu category', 'vmenu' ) . ' ' . $category_number,
					'tooltip'	     => __( 'Note: Menu Items are ordered by Date.', 'vmenu' ),
					'taxonomy'		 => 'menu-category',
					'wrapper_class'  => 'span6',
				);
				?>

					<p class="span12">
						<strong>
							<?php echo esc_html( __( 'Menu', 'vmenu' ) . ' ' . $menu_number ); ?>
						</strong>
					<i>
						<?php echo wp_kses_data( sprintf(
							__( 'Display it using the %s shortcode.' ),
							'<b>[vmenu number=' . $menu_number . ']</b>'
						) ); ?>
					</i></p>

				<?php

				premise_field_section( $category_fields );

				$menu_number++;
			}
		}

		$category_number = 1;

		// Build Category fields.
		$category_fields = array();

		// New category.
		$category_fields[] = array(
			'type'           => 'select',
			'name'           => 'vmenu_menus[' . $menu_number . '][menu-categories][' . $category_number . ']',
			'options'        => $options,
			'label'	   		 => __( 'Menu category', 'vmenu' ) . ' ' . $category_number,
			'taxonomy'		 => 'menu-category',
			'wrapper_class'  => 'span6',
		);

		?>

			<p class="span12">
				<strong>
					<?php echo esc_html( __( 'Menu', 'vmenu' ) . ' ' . $menu_number ); ?>
				</strong>
			</p>

		<?php

		premise_field_section( $category_fields );

		// Menus tab content.
		$menus_content = ob_get_clean();

		ob_start();

		// Auto Share.
		// Activated.
		premise_field(
			'checkbox',
			array(
				'name'      => 'vmenu_share[activated]',
				'label'     => premise_get_option( 'vmenu_share[activated]' ) ?
					__( 'Activated', 'vmenu' ) :
					__( 'Activate', 'vmenu' ),
				'tooltip'   => __( 'Activate Auto Share feature.', 'vmenu' ),
				'wrapper_class'     => 'span12',
			)
		);


		?>
	</form>
		<?php

		if ( premise_get_option( 'vmenu_share[activated]' ) ) {

			// Social Media Auto Publish plugin settings tab content.
			xyz_smap_settings();

			$auto_share_content = ob_get_clean();

			ob_start();

			// Auto Share Logs.
			// Social Media Auto Publish plugin logs tab content.
			xyz_smap_logs();

			$auto_share_logs_content = ob_get_clean();

		} else {

			$auto_share_logs_content = '';

			$auto_share_content = ob_get_clean();
		}

		$tabs = array(
			// Load Menus settings.
			array(
				'title' => __( 'Menus', 'vmenu' ),
				'icon' => 'fa-cutlery',
				'content' => $menus_content,
			),
			// Load Social Media Auto Publish plugin settings.
			array(
				'title' => __( 'Auto Share', 'psmp' ),
				'icon' => 'fa-share',
				'content' => $auto_share_content,
			),
		);

		if ( $auto_share_logs_content ) {

			// Auto Share Logs.
			// Load Social Media Auto Publish plugin logs.
			$tabs[] = array(
				'title' => __( 'Auto Share Logs', 'psmp' ),
				'icon' => 'fa-exchange',
				'content' => $auto_share_logs_content,
			);
		}

		new Premise_Tabs( $tabs );
	?>
</div>
