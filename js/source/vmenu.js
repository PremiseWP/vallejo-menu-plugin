/**
 * Plugin JS
 *
 * @package Vallejo Menu Plugin
 */
(function ($) {

	// when DOM ready
	$( document ).ready( function() {

		// Menu categories display
		$( '.vmenu-categories .vmenu-category' ).click(function(event) {
			event.preventDefault();

			if ( !$( this ).hasClass( 'current' ) ) {
				$( '.vmenu-category' ).removeClass( 'current' );
				$( this ).addClass( 'current' );

				// get category last CSS class (slug)
				var categorySlug = $( this ).attr( 'data-slug' );

				$( 'article.vmenu-item' ).hide();
				$( '.vmenu-category-' + categorySlug ).show();
			}
		});



		// When our page loads, check to see if it contains an #vmenu-category-* anchor.
		if ( window.location.hash.indexOf('vmenu-category') != -1 ) {

			if ( $( window.location.hash ).length ) {

				$( window.location.hash ).click();
			}
		}

	});
})(jQuery);
