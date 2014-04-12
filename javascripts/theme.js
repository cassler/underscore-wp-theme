window.collectionsTimeouts = [];

/**
 * Add Immediately-Invoked Function Expression that initiates all of the general purpose theme JS.
 *
 * @since  1.0.
 *
 * @param  object    window    The window object.
 * @param  object    $         The jQuery object.
 * @return void
 */
(function ( window, $ ) {
	// Cache document for fast access.
	var document = window.document;

	/**
	 * The faux "class" constructor.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	var Collections = function () {

		/**
		 * Time in ms taken to fade an item in.
		 *
		 * @since 1.0.
		 *
		 * @type {number}
		 */
		var animationSpeed = 800;

		/**
		 * Time between fading in items.
		 *
		 * @since 1.0.
		 *
		 * @type {number}
		 */
		var delay = 200;

		/**
		 * Holds reusable elements.
		 *
		 * @since 1.0.
		 *
		 * @type {{$document: object, $window: object}}
		 */
		var cache = {
			$document : {},
			$window   : {}
		};

		/**
		 * Initiate all actions for this class.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function init() {
			// Cache the reusable elements
			cacheElements();

			// Make the faux links active
			activateFauxLinks();

			// Activate the Load More button
			activeLoadMoreButton();

			// Bind events
			bindEvents();

			// IE 10 detection
			ie10Detection();
		}

		/**
		 * Caches elements that are used in this scope.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function cacheElements() {
			cache.$window   = $( window );
			cache.$document = $( document );
		}

		/**
		 * Setup event binding.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function bindEvents() {
			// Run Dotdotdot on post load
			cache.$document.on( 'faded-in', setupDotdotdot );

			// Run responsive slides on post load
			cache.$document.on( 'ready', setupResponsiveSlides );
			cache.$document.on( 'post-load', setupResponsiveSlides );

			// Run FitVids on post load
			cache.$document.on( 'ready', setupFitVids );
			cache.$document.on( 'post-load', setupFitVids );

			// Run Masonry on post load
			cache.$document.on( 'ready', setupMasonry );
			cache.$document.on( 'post-load', setupMasonry );

			// Animate the homepage on post load
			cache.$document.on( 'ready', fadeInCards );
			cache.$document.on( 'post-load', fadeInCards );

			// Animate the posts on post load
			cache.$document.on( 'ready', animatePosts );
			cache.$document.on( 'post-load', animatePosts );
			cache.$document.on( 'masonry-done', animatePosts );

			// Show the load more button
			cache.$document.on( 'faded-in', showLoadMoreButton );

			// Fade in posts and pages on load
			cache.$document.on( 'ready', fadeInContent );
		}

		/**
		 * Run FitVids.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function setupFitVids() {
			// FitVids is only loaded on the pages and single post pages. Check for it before doing anything.
			if ( ! $.fn.fitVids )
				return;

			// Get the selectors
			var selectors;
			if ( 'object' === typeof CollectionsFitvidsCustomSelectors ) {
				selectors = CollectionsFitvidsCustomSelectors.customSelector;
			}

			$( '.type-post, .type-page' ).fitVids( { customSelector : selectors } );

			// Fix padding issue with Blip.tv issues; note that this *must* happen after Fitvids runs
			// The selextor finds the Blip.tv iFrame then grabs the .fluid-width-video-wrapper div sibling
			$( '.fluid-width-video-wrapper:nth-child(2)', '.video-container' )
				.css( { 'paddingTop' : 0 } );
		}

		/**
		 * Turns some p tags into clickable links.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function activateFauxLinks() {
			$( '.homepage-post, #archive-wrapper' ).on( 'click', '.faux-link', function ( evt ) {
				if ( 'A' !== evt.target.tagName )
					evt.preventDefault();

				// Set the default URL
				var $evt = $(evt.target);

				// Attempt to get the URL from the element
				var url = $evt.attr('data-url');

				// If no URL was retrieved try to get it from the parent element
				if ( '' === url || undefined === url ) {
					url = $evt.parent().attr('data-url');
				}

				if ( '' !== url ) {
					window.location = url;
				}
			} );
		}

		/**
		 * Animates the cards being shown on the homepage.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function fadeInCards() {
			var $col1links = $( '.view-index', '.column-one' ),
				$col2links = $( '.view-index', '.column-two' ),
				links = [];

			// Do not run the JS if the page is not the homepage and remove the events
			if ( 0 === $col1links.length ) {
				cache.$document.off( 'post-load', fadeInCards );
				return;
			}

			/**
			 * $col1links is the left side cards and $col2links is the right side cards. In order to fade them in in
			 * order, we must combine them into a single array.
			 */
			$col1links.each( function( index ) {
				// Add the card from the left side first
				links.push( $col1links[ index ] );

				// If a corresponding card on the right side exists, add it
				if ( index in $col2links ) {
					links.push( $col2links[ index ] );
				}
			} );

			// Loop through the links, fading each one in
			var numPosts = links.length;
			$.each( links, function( index ) {
				var that = this;
				setTimeout(
					function() {
						$( that )
							.children( '.homepage-post' )
								.css( 'visibility', 'visible' )
								.animate( {
									opacity : 1
								}, animationSpeed );

						// If this is the last item, trigger the faded-in event
						if ( numPosts === index ) {
							cache.$document.trigger( 'faded-in' );
						}
					}, ( delay * index )
				);
			} );
		}

		/**
		 * Fade in posts in the index view.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function animatePosts() {
			var $post = $( '.post, .page', '.main-content' ),
				numPosts = $post.length;

			if ( 0 === numPosts ) {
				return;
			}

			$post.each( function( index ) {
				var that = this;
				window.collectionsTimeouts.push( window.setTimeout(
					function() {
						$( that ).fadeIn( animationSpeed );
						if ( numPosts === index + 1 ) {
							cache.$document.trigger( 'faded-in' );
						}
					}, ( delay * index )
				) );
			} );
		}

		/**
		 * If needed, display the load more button when content has appeared.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		function showLoadMoreButton() {
			$( '.stream-footer' ).fadeIn( 'fast' );
		}

		/**
		 * Run Dotdotdot.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function setupDotdotdot() {
			if ( ! $.fn.dotdotdot )
				return;

			// Variables
			var $container1 = $('.collections-circle-1').find('.entry-content, .quote-content-container'),
				$container2 = $('.collections-circle-2').find('.entry-content, .quote-content-container'),
				$container3 = $('.collections-circle-3').find('.entry-content, .quote-content-container');

			// Run Dotdotdot
			if ( $container1.length )
				$container1.dotdotdot({ height : $container1.outerWidth() });
			if ( $container2.length )
				$container2.dotdotdot({ height : $container2.outerWidth() });
			if ( $container3.length )
				$container3.dotdotdot({ height : $container3.outerWidth() * 0.85 });
		}

		/**
		 * Run ResponsiveSlides.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function setupResponsiveSlides() {
			if ( ! $.fn.responsiveSlides )
				return;

			if ( 'object' === typeof CollectionsResponsiveSlidesOptions ) {
				$('.collections-gallery-slideshow.deactivated').responsiveSlides( CollectionsResponsiveSlidesOptions ).removeClass('deactivated');
			} else {
				$('.collections-gallery-slideshow.deactivated').responsiveSlides().removeClass('deactivated');
			}
		}

		/**
		 * Run Masonry.
		 *
		 * @since 1.0.
		 *
		 * @return void
		 */
		function setupMasonry() {
			// Masonry is only loaded on the Photo archive view. Check for it before doing anything.
			if ( ! $.fn.masonry )
				return;

			// Get variables
			var $container  = $( '#photo-content-wrapper' ),
				$gutter     = $( '.gutter-sizer' ),
				$column     = $( '.column-sizer' ),
				columnSizer = function() { return $column.outerWidth() / $container.outerWidth() };

			// Run Masonry
			var $masonry = $container.masonry( {
				columnWidth  : function( containerWidth ) { return containerWidth * columnSizer().toFixed(2); },
				gutterWidth  : $gutter.outerWidth(),
				itemSelector : '.post'
			} );

			/**
			 * Trigger Masonry a second time after all images are loaded. In some cases, particularly with Safari,
			 * Masonry will fire before images are loaded, and the sizes will not be properly calculated. Recalculating
			 * again once the images are loaded should fix the layout.
			 */
			$masonry.imagesLoaded( function () {
				$masonry.masonry().trigger('masonry-done');
			} );
		}

		/**
		 * Add ie10 class to HTML if browser is ie10.
		 *
		 * IE 10 does not read conditionals in the HTML like previous versions of IE do. Workaround found here:
		 * http://www.impressivewebs.com/ie10-css-hacks/#comment-27814
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function ie10Detection() {
			if (/*@cc_on!@*/false && document.documentMode === 10) {
				document.documentElement.className+=' ie10';
			}
		}

		/**
		 * Fades in single posts and pages.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function fadeInContent() {
			$( '#cspa-post, #cspa-post-navigation', '#cspa-post-wrapper' ).animate( {
				opacity : 1
			}, animationSpeed );

			$( '#cspa-page-wrapper', '.container' ).animate( {
				opacity : 1
			}, animationSpeed );
		}

		/**
		 * When the SPA is disabled, enable the load more button.
		 *
		 * @since  1.1.
		 *
		 * @return void
		 */
		function activeLoadMoreButton() {
			if ( '1' === collectionsVars.spaEnabled ) {
				return;
			}

			// For non-SPA browsers, enable the load more button
			$( '.stream-footer' ).on( 'click', '.cspa-load-more', function () {
				window.location = $( this ).attr( 'data-url' );
			} );
		}

		// Initiate the actions.
		init();
	}

	// Instantiate the "class".
	window.Collections = new Collections();
})( window, jQuery );
