/**
 * The post archive view.
 *
 * Used for the index page for the site.
 */
( function (  window, Backbone, $, _, CollectionsApp  ) {
	CollectionsApp.Views.PostArchive = Backbone.View.extend( {
		// The main element tag
		el : 'div',

		// The element's class name
		className : 'post',

		// Default HTML
		html : '',

		// Helper for rendering the template
		renderTemplate : function ( data ) {
			// Handle the stream and articles mappings
			if ( _.isNull( CollectionsApp.queriedObject ) || 1 === CollectionsApp.isHome || ( 1 === CollectionsApp.isArchive  && 0 === CollectionsApp.isTax ) ) {
				var format = 'stream'
			} else {
				var format = ( _.contains( [ '', 'chat', 'status' ], data.postFormat ) ) ? 'standard' : data.postFormat;
			}

			// Grab the template HTML
			var template = $( '#tmpl-collections-archive-' + format ).html();

			// Render the template with the passed data and return the resulting HTML
			return _.template( template, data, CollectionsApp.templateOptions );
		},

		// Render the template
		render : function () {
			// Change the document title
			CollectionsApp.eventAggregator.trigger( 'domchange:title', CollectionsApp.titleAttr );

			// Grab the model's data
			var data = this.model.toJSON();

			// Render the template with the data from the model
			this.html = this.renderTemplate( data );

			return this;
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );