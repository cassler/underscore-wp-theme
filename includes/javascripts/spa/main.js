/**
 * Initiates the app.
 */
( function ( CollectionsApp, $, _, Backbone, window ) {
	var pushState = !!( window.history && window.history.pushState );

	// Do not run the SPA if browser does not support pushState. While Backbone supports it, non pushState browsers were a mess with WP
	if ( pushState ) {
		// Cache document
		var view;

		if ( '1' === collectionsSPAData.isTax || '1' === collectionsSPAData.isFrontPage || '1' === collectionsSPAData.isArchive || '1' === collectionsSPAData.isHome ) {
			// Instantiate the core view for the archive page
			view = new CollectionsApp.Views.Core( {
				model : new CollectionsApp.Models.Core()
			} );

			var archiveView = new CollectionsApp.Views.PostArchive();
		}

		if ( '1' === collectionsSPAData.isSingle ) {
			// Instantiate the core view for the single post page
			view = new CollectionsApp.Views.PostSingle( {
				model : new CollectionsApp.Models.Post
			} );
		}

		if ( 'object' === typeof( view ) ) {
			// Setup an event aggregator
			CollectionsApp.eventAggregator = _.extend( {}, Backbone.Events );

			// Setup an event for changing the document title
			CollectionsApp.eventAggregator.on( 'domchange:title', function( title ) {
				$( 'title' ).html( title );
			} );

			// Cache the individual page view
			CollectionsApp.history[ collectionsSPAData.pathname ] = {
				pathname : collectionsSPAData.pathname
			};

			// Instantiate the router
			var router = new CollectionsApp.Routers.Router( {
				view : view
			} );

			// Initiate history and use pushState
			Backbone.history.start( {
				pushState : true,
				silent    : true
			} );
		}
	} else {
		// For non-SPA browsers, enable the load more button
		$( '.stream-footer' ).on( 'click', '.cspa-load-more', function () {
			window.location = $( this ).attr( 'data-url' );
		} );
	}
} ) ( CollectionsApp, jQuery, _, Backbone, window );