/**
 * Core view for the index page
 */
( function ( window, Backbone, $, _, CollectionsApp, collectionsTimeouts ) {
	var document = window.document;

	CollectionsApp.Views.Core = Backbone.View.extend( {
		// The main element is the page container
		el : function() {
			return document.getElementById( 'container' );
		},

		// Cache the archive wrapper
		$archiveWrapper : $( '#archive-wrapper' ),

		// Cache the load more button
		$loadMoreWrapper : $( '.stream-footer' ),

		// Set an even to change the page when a controller link is clicked
		events : {
			'click .cspa-control' : 'initRouter'
		},

		// Set important variables and start the router
		initRouter : function ( evt ) {
			evt.preventDefault();

			// Get the path and the post format of the target
			var pathname = '';

			// Get the path name, which can be an href or a data attribute
			if ( evt.target.pathname ) {
				pathname = evt.target.pathname;
			} else if ( evt.target.getAttribute( 'data-pathname' ) ) {
				pathname = evt.target.getAttribute( 'data-pathname' );
			}

			// Get the "search" element of the URL in case default URLs are being used
			var nextSearch = evt.target.search;
			nextSearch = ( 'undefined' === nextSearch ) ? '' : nextSearch;

			var currentSearch = window.location.search;
			currentSearch = ( 'undefined' === currentSearch ) ? '' : currentSearch;

			// If the target destination and current destination are the same, do nothing
			if ( pathname + nextSearch === window.location.pathname + currentSearch ) {
				return;
			}

			// Fade out elements that will be replaced
			this.$archiveWrapper.fadeOut( 'slow' );
			this.$loadMoreWrapper.fadeOut( 'slow');

			// Clear any other running actions
			_.each( window.collectionsTimeouts, function( element ) {
				window.clearTimeout( element );
			} );
			window.collectionsTimeouts = [];

			// Initialize the spinner
			CollectionsApp.initSpinner();

			// If not using pretty permalinks, add the query var to the string
			if ( 'undefined' !== typeof( evt.target.search ) && '' !== evt.target.search ) {
				pathname += evt.target.search;
			}

			// Replace the "/" if it is the first character in the path
			if ( '/' === pathname.charAt( 0 ) ) {
				pathname = pathname.substr( 1 );
			}

			// The homepage comes through with a null pathname. Correct this.
			if ( null === pathname || '' === pathname ) {
				pathname = '/';
			}

			// Spoof the page name if permalinks are not being used
			if ( 0 == collectionsSPAData.permalink && '/' === pathname ) {
				pathname = '?view=stream';
			}

			// Store the values in the history
			CollectionsApp.history[ pathname ] = {
				pathname : pathname
			};

			// Trigger the router
			Backbone.history.navigate( pathname, { trigger: true } );
		},

		// Get data for the collection
		fetchData : function() {
			// Get the collection
			var postArchiveInstance = this.model.get( 'postArchiveInstance' );

			// Set up the fetch args
			var args = {};
			args.reset = true;

			// Fetch the posts
			postArchiveInstance.fetch( args );
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp, collectionsTimeouts );