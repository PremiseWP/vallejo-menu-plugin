<?php
/**
 * Menu Item after content View
 *
 * @package Vallejo Menu Plugin
 */

defined( 'ABSPATH' ) or exit;

$menu_item_meta = get_post_meta( get_the_ID(), 'menu_item_meta', true ); // Get Price, Tags, icons, Categories.

$price = premise_get_option( 'vmenu_currency' ) . $menu_item_meta['price'] ; ?>

<div class="vmenu-item-meta premise-clear-float">

	<p class="vmenu-item-price" title="<?php echo esc_attr( $price ); ?>">
		<?php echo esc_html( $price ); ?>
	</p>

	<div class="premise-clear"></div>

	<?php if ( ( $menu_item_icons = Vallejo_Menu_Item_CPT::get_menu_item_icons( $menu_item_meta['icons'] ) )
		&& ! empty( $menu_item_icons ) ) : ?>
		<p class="vmenu-item-icons">
			<?php echo wp_kses_post( $menu_item_icons ); ?>
		</p>
	<?php endif; ?>

	<?php if ( ( $menu_item_categories = get_the_terms( get_the_ID(), 'menu-category' ) )
		&& ! empty( $menu_item_categories ) ) :

		$menus = premise_get_option( 'vmenu_menus' );

		$vmenu_number = $vmenu_number_temp = 0;
		?>

		<p class="vmenu-item-categories">
			<?php esc_html_e( 'Categorised in: ', 'vallejo' ); ?>

			<?php $first = true; foreach ( $menu_item_categories as $menu_item_category ) :

				// Search for vmenu number that displays menu item category.
				foreach ( (array) $menus as $current_vmenu_number => $menu ) {

					foreach ( (array) $menu['menu-categories'] as $menu_category ) {

						if ( $menu_category == $menu_item_category->term_id ) {

							$vmenu_number = $current_vmenu_number;

							break 2;
						}
					}
				}

				/**
				 * Find 1st post which have our vmenu shortcode.
				 *
				 * @link http://www.wpcustoms.net/snippets/find-posts-containing-shortcode/
				 */
				if ( $vmenu_number !== $vmenu_number_temp ) {

					$shortcode_args = array(
						's' => 'vmenu number=' . $vmenu_number,
					);

					$shortcode_query = new WP_Query( $shortcode_args );

					if ( $shortcode_query->have_posts() ) {
						while ( $shortcode_query->have_posts() ) {

							$shortcode_query->the_post();

							// Save post URL.
							$menu_page_url = get_the_permalink();

							break;
						}
					}
				}

				if ( ! $first ) {
						echo ', ';
				} else {
					$first = false; // Separated by commas.
				} ?>

				<?php if ( $vmenu_number ) : // If Menu page with right shortcode found. ?>
					<a href="<?php echo esc_url( $menu_page_url ); ?>#vmenu-category-<?php echo esc_attr( $menu_item_category->slug ); ?>">
						<?php echo esc_html( $menu_item_category->name ); ?>
					</a>
				<?php else : ?>
					<?php echo esc_html( $menu_item_category->name ); ?>
				<?php endif; ?>


				<?php $vmenu_number_temp = $vmenu_number;

				$vmenu_number = 0;

			endforeach; ?>
		</p>

	<?php endif; ?>

</div>