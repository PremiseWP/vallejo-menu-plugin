<?php
/**
 * Menu page template
 *
 * Template name: Menu
 *
 * @package Vallejo Menu Plugin
 */

defined( 'ABSPATH' ) or exit;

?>
<div class="premise-row vmenu">

	<?php
	/**
	 * Menu categories template
	 *
	 * @package Vallejo Menu Plugin
	 */

	$menu_categories_ID = premise_get_option( 'vmenu_menus[' . $atts['number'] . '][menu-categories]' );

	$menu_categories = get_categories( array(
		'taxonomy' => 'menu-category',
		'hide_empty' => true,
		'include' => (array) $menu_categories_ID,
	) );

	// Reorder categories to respect $menu_categories_ID order.
	$menu_categories_ordered = array();

	$menu_categories_ID_flip = array_flip( $menu_categories_ID );

	foreach ( $menu_categories as $menu_category ) {

		$key = $menu_categories_ID_flip[ $menu_category->term_id ];

		$menu_categories_ordered[ $key ] = $menu_category;
	}

	ksort( $menu_categories_ordered );

	$first_cat = true; ?>

	<ul class="vmenu-categories">

	<?php foreach ( $menu_categories_ordered as $menu_category ) :

		$class = 'vmenu-category';

		// Set current class for link color styling.
		if ( $first_cat ) {
			$class .= ' current';

			$first_cat = false;

			define( 'MENU_CATEGORY_CURRENT', $menu_category->slug );
		}
	?>

		<li>
			<a href="#vmenu-category-<?php echo esc_url( $menu_category->slug ); ?>" class="<?php echo esc_attr( $class ); ?>" id="vmenu-category-<?php echo esc_attr( $menu_category->slug ); ?>" data-slug="<?php echo esc_attr( $menu_category->slug ); ?>">
				<?php echo esc_html( $menu_category->name ); ?>
			</a>
		</li>

	<?php endforeach; ?>

	</ul><!-- /vmenu-categories -->

	<?php
	/**
	 * Menu items posts loop template
	 *
	 * @package Vallejo Menu Plugin
	 */

	$menu_query = new WP_Query( 'post_type=menu-item&orderby=menu-category&posts_per_page=-1' );

	if ( $menu_query->have_posts() ) :
		while ( $menu_query->have_posts() ) : $menu_query->the_post();

			$menu_item_class = array( 'vmenu-item', 'vmenu-same-height' );

			// Display columns.
			$menu_item_class[] = 'col' . premise_get_option( 'vmenu_columns' );

			// Add .current CSS class.
			if ( ( $categories = get_the_terms( get_the_ID(), 'menu-category' ) ) ) {

				foreach ( $categories as $category ) {

					if ( defined( 'MENU_CATEGORY_CURRENT' )
						&& $category->slug === MENU_CATEGORY_CURRENT ) {

						$menu_item_class[] = 'current';
					}

					$menu_item_class[] = 'vmenu-category-' . $category->slug;
				}
			}

			$menu_item_meta = get_post_meta( get_the_ID(), 'menu_item_meta', true ); // Get Price, Short desc, icons. ?>

		<article id="vmenu-item-<?php the_ID(); ?>" <?php post_class( $menu_item_class ); ?>>

			<table>
				<tr><td class="vmenu-item-thumbnail-td">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" class="vmenu-item-thumbnail">
						<?php
							the_post_thumbnail( array( 68, 68 ) ); // Declare pixel size you need inside the array.
						?>
					</a><!-- /vmenu-item-thumbnail -->
				</td>
				<td class="vmenu-item-title-desc">

					<table class="vmenu-item-title-table vmenu-dotted-title">
						<tr>
							<td class="vmenu-item-title-td">
								<h4 class="vmenu-item-title">
									<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>">
										<?php the_title(); ?>
									</a>
								</h4>
							</td>
							<td class="vmenu-line">
								<div></div>
							</td>
							<td class="vmenu-item-price" title="<?php echo esc_attr( $price = premise_get_option( 'vmenu_currency' ) . $menu_item_meta['price'] ); ?>">
									<?php echo esc_html( $price ); ?>
							</td>
						</tr>
					</table><!-- /vmenu-item-title -->


					<div class="vmenu-item-descr-icons-wrapper">
						<div class="vmenu-item-short-description vmenu-item-short-description-has-<?php echo esc_attr( count( $menu_item_meta['icons'] ) -1 ); ?>-icons">
							<?php echo wp_kses_post( $menu_item_meta['short_description'] ); ?>
						</div><!-- /vmenu-item-short-description -->

						<div class="vmenu-item-icons">
							<?php echo wp_kses_post( Vallejo_Menu_Item_CPT::get_menu_item_icons( $menu_item_meta['icons'] ) ); ?>
						</div>
					</div>

					<?php edit_post_link(); ?>
				</td></tr>
			</table>

		</article>

	<?php endwhile;
		wp_reset_query(); ?>

	<?php endif; ?>

</div>
