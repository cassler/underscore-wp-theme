/**
 * The Router for the app.
 *
 * Handles routing for all of the views.
 */
( function ( window, Backbone, $, _, CollectionsApp ) {
	CollectionsApp.Routers.Router = Backbone.Router.extend( {
		// The "constructor"
		initialize : function ( options ) {
			// Store the reference view
			this.view = options.view;
		},

		// Cache the body selector. Awkward formatting is necessary for proper scrolling
		$htmlBody : $( 'body,html' ),

		// Defines the pseudo routes
		routes : {
			// A catch all route which allows us to avoid representing all of WordPress' rewrites here
			'*notFound' : 'default',
			''          : 'default'
		},

		// Handle app routing
		default : function ( pathname ) {
			// Scroll the page back to the top
			this.$htmlBody.animate( {
				scrollTop : 0
			}, 500 );

			// The homepage comes through with a null pathname. Correct this.
			if ( null === pathname ) {
				pathname = '/';
			}

			// Get the "search" element of the URL in case default URLs are being used
			var search = window.location.search;

			// Doublecheck that we do not have a real URL
			if ( ( '/' === pathname || null === pathname ) && '' !== search && 'undefined' !== search ) {
				// See if we can find the search value in the history
				if ( 'undefined' !== CollectionsApp.history[ search ] ) {
					pathname = search;
				}
			}

			// Set the current value to the clicked value
			CollectionsApp.history.current = CollectionsApp.history[ pathname ];

			// Handle fetching data differently depending on the type of view
			if ( '1' === collectionsSPAData.isSingle ) {
				// Using the pathname, get the link to the post and grab the ID from it
				var link = $( 'a.collections-post-nav[href*="' + pathname + '"]' );
				this.view.fetchData(
					link.attr( 'data-id' )
				);
			} else if ( '1' === collectionsSPAData.isTax || '1' === collectionsSPAData.isFrontPage || '1' === collectionsSPAData.isArchive  || '1' === collectionsSPAData.isHome ) {
				this.view.fetchData();
			}
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );