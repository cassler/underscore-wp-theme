/**
 * The main Backbone Collection that holds post Views for the index.
 *
 * When instantiated, the Collection takes a model, which it refers to when it fetches new posts. On the 'reset' method
 * it regenerates the index view, populating it with all new posts. It is also responsible for updating the load more
 * button.
 */
( function (  window, Backbone, $, _, CollectionsApp  ) {
	CollectionsApp.Collections.PostArchive = Backbone.Collection.extend( {
		// The "constructor" method
		initialize : function ( model ) {
			// Set the model to an instance var
			this.set( 'model', model );

			// On reset, regenerate the posts
			this.listenTo( this, 'reset', this.addAll );

			// Setup the event for updating the load more button
			var that = this;
			$( window.document ).on( 'faded-in', function () {
				that.updateLoadMoreButton();
			} );
		},

		// Get the wrapper around the archive area
		$archiveWrapper : $( '#archive-wrapper' ),

		// Get the wrapper around the footer area
		$loadMoreWrapper : $( '.stream-footer' ),

		// Grab the body element
		$body : $( 'body' ),

		// Create the URL which differs depending on the permalink structure
		url : function() {
			if ( -1 === CollectionsApp.history.current.pathname.indexOf( '?' ) ) {
				return '?cspa-json=1';
			} else {
				return CollectionsApp.history.current.pathname + '&cspa-json=1';
			}
		},

		// Parse the response
		parse : function( response ) {
			// Set the global data that is not post specific
			CollectionsApp.queriedObject = response.global.queriedObject;
			CollectionsApp.enqueues      = response.global.enqueues;
			CollectionsApp.nextButton    = response.global.nextButton;
			CollectionsApp.bodyClasses   = response.global.bodyClasses;
			CollectionsApp.isHome        = response.global.isHome;
			CollectionsApp.isArchive     = response.global.isArchive;
			CollectionsApp.isTax         = response.global.isTax;
			CollectionsApp.titleAttr     = response.global.titleAttr;
			CollectionsApp.archiveTitle  = response.global.archiveTitle;

			// Return the array of posts for the Collection to iterate through
			return response.posts;
		},

		// Adds a single post
		addOne : function( post ) {
			// Create a new archive view for the post
			var view = new CollectionsApp.Views.PostArchive( {
				model      : post,
				collection : this
			} );

			// Render the view
			view.render();
			return view.html;
		},

		// Regenerate the whole index view
		addAll : function() {
			var that   = this,
				header = '',
				body   = '',
				footer = '',
				html;

			// Update the body classes
			this.$body.attr( 'class', CollectionsApp.bodyClasses );

			// Get rid of the old HTML
			this.$archiveWrapper.empty();

			// Potentially add wrapping HTML if necessary
			if ( -1 !== CollectionsApp.bodyClasses.indexOf( 'term-post-format-image' ) ) {
				header = '<div id="photo-content-wrapper">';
				footer = '<div class="column-sizer"></div><div class="gutter-sizer"></div></div>';
			} else if ( -1 !== CollectionsApp.bodyClasses.indexOf( 'term-post-format-video' ) ) {
				header = '<div id="audio-content-wrapper">';
				footer = '</div>';
			}

			// Add archive title if necessary
			if ( 1 === CollectionsApp.isArchive && 1 !== CollectionsApp.isTax ) {
				header += '<h3 class="stream-title">' + CollectionsApp.archiveTitle + '</h3>';
			}

			// Render the HTML for each post
			_.each( that.models, function( post, iterator ) {
				body += that.addOne( post );
			} );

			// Combine the header, the body and the footer
			html = header + body + footer;

			// Render the scripts
			CollectionsApp.renderEnqueues( 'scripts', CollectionsApp.enqueues.scripts, collectionsSPAData.scripts );

			// Render the styles
			CollectionsApp.renderEnqueues( 'styles', CollectionsApp.enqueues.styles, collectionsSPAData.styles );

			// Remove the spinner
			CollectionsApp.clearSpinner();

			// Add the new HTML and finish the fade out if it is still going before showing the div again
			this.$archiveWrapper.append( html ).finish().show();

			// Everything is loaded, so signal post loaded
			CollectionsApp.postLoadJS();
		},

		// Handle the load more button
		updateLoadMoreButton : function () {
			if ( 'undefined' === typeof( CollectionsApp.nextButton ) ) {
				this.$loadMoreWrapper
					.hide()
					.fadeIn( 'fast' );
			} else {
				this.$loadMoreWrapper
					.html( CollectionsApp.nextButton )
					.hide()
					.fadeIn( 'fast' );
			}
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );