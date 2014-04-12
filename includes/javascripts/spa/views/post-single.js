/**
 * Define a view for single post pages.
 *
 * The view listens to click events on the navigation links. When the navigation is clicked, a request to the server
 * is made for the post data. Upon returning, the post is faded out and the new post is faded in. Navigation links are
 * updated and new scripts/styles are loaded in.
 */
( function (  window, Backbone, $, _, CollectionsApp  ) {
	CollectionsApp.Views.PostSingle = Backbone.View.extend( {
		// The main element for the view
		el : $( '#cspa-post-wrapper' ),

		// Cache the body element
		$body : $( 'body' ),

		// Cache the post
		$cspaPost : $( '#cspa-post' ),

		// Cache the footer
		$footer : $( '#footer' ),

		// The "constructor
		initialize : function () {
			// When the model changes, render the content
			this.listenTo( this.model, 'change', this.render );
		},

		// Initialize the page change when the nav buttons are clicked
		events : {
			// Listen to the click event for navigation links
			'click .collections-post-nav' : 'initRouter'
		},

		// Start the page change
		initRouter : function( evt ) {
			evt.preventDefault();

			// Fade out the post first thing
			this.$cspaPost.fadeOut( 'slow' );
			this.$footer.fadeOut( 'slow' );

			// Initialize the spinner
			CollectionsApp.initSpinner();

			// Since the click event is on a `span` wrapped in an `a`, get the parent's pathname
			var $link    = $( evt.target ).parent(),
				pathname = $link.get( 0 ).pathname,
				search   = $link.get( 0 ).search;

			// If not using pretty permalinks, add the query var to the string
			if ( 'undefined' !== typeof( search ) && '' !== search ) {
				pathname += search;
			}

			// Initiate the router by navigating to the new link
			Backbone.history.navigate( pathname, { trigger : true } );
		},

		// Get the model's data from the server
		fetchData : function ( id ) {
			if ( !_.isNaN( id ) ) {
				// Set the new ID on the model, but do not trigger the change event
				this.model.set( { id : id }, { silent: true } );
				this.model.fetch();
			}
		},

		// Template rendering helper
		renderTemplate : function ( data ) {
			// Map the "articles" formats to standard if necessary
			var format = ( _.contains( [ '', 'chat', 'status' ], data.postFormat ) ) ? 'standard' : data.postFormat;

			// Grab the template HTML
			var template = $( '#tmpl-collections-' + format ).html();

			// Render the template with the passed data and return the resulting HTML
			return _.template( template, data, CollectionsApp.templateOptions );
		},

		// Render the template
		render : function () {
			// Grab the model's data
			var data = this.model.toJSON();

			// Render the template with the data from the model
			var html = this.renderTemplate( data );

			// Change the document title
			CollectionsApp.eventAggregator.trigger( 'domchange:title', data.titleAttr );

			// Update body classes
			this.$body.attr( 'class', _.escape( data.bodyClasses ) );

			// Replace the content with new content and fade it in
			var that = this;
			this.$cspaPost.html( html ).hide().fadeIn( 'fast', function() {
				// Update the post navigation but only when the nav is present
				if ( $( '.collections-post-nav' ).length > 0 ) {
					that.updateNavigation( 'prev', data );
					that.updateNavigation( 'next', data );
				}

				// Render the scripts
				CollectionsApp.renderEnqueues( 'scripts', data.enqueues.scripts, collectionsSPAData.scripts );

				// Everything is loaded, so signal post loaded
				CollectionsApp.postLoadJS();
			} );

			// Bring the footer back
			this.$footer.fadeIn( 'fast' );

			// Remove the spinner
			CollectionsApp.clearSpinner();

			// Add needed styles
			CollectionsApp.renderEnqueues( 'styles', data.enqueues.styles, collectionsSPAData.styles );

			return this;
		},

		// Update the next/prev post navigation
		updateNavigation : function ( which, data ) {
			// We must affect the "next" or "prev" link
			if ( _.contains( [ 'prev', 'next' ], which ) ) {
				// Generate the keys
				var IDKey  = which + 'PostID',
					URLKey = which + 'PostURL',
					$link  = $( '.collections-post-nav[rel=' + which + ']' );

				// Make sure that the necessary variables are set and with acceptable values
				if ( IDKey in data && URLKey in data && '' !== data[ IDKey ] && '' !== data[ URLKey ] ) {
					// Rewrite the attributes
					$link
						.attr( 'data-id', parseInt( data[ IDKey ] ) )
						.attr( 'href', encodeURI( data[ URLKey ] ) )
						.fadeIn( 'slow' ); // Just in case it was previously hidden
				} else {
					// There is no next/prev post, so hide the element
					$link
						.fadeOut( 'slow' )
						.attr( 'data-id', '' )
						.attr( 'href', '#' );
				}
			}
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );