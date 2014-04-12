/**
 * The Post model.
 *
 * Handles defining data and controlling fetching of the post objects.
 */
( function ( window, Backbone, $, _, CollectionsApp ) {
	CollectionsApp.Models.Post = Backbone.Model.extend( {
		// Alter the returned JSON
		parse : function( response ) {
			// Reformat the returned JSON
			var data           = response.posts[0];
			data.enqueues      = response.global.enqueues;
			data.queriedObject = response.global.queriedObject;
			data.titleAttr     = response.global.titleAttr;

			return data;
		},

		// Modify the URL to fetch the post
		url : function() {
			// Reformat the URL to match the JSON URL format
			return '?p=' + parseInt( this.id ) + '&cspa-json=1';
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );