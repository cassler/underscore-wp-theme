/**
 * Model for the "Core" view.
 *
 * This model primarily acts as a bridge between the Post Archive collection and the Core view.
 */
( function ( window, Backbone, $, _, CollectionsApp ) {
	CollectionsApp.Models.Core = Backbone.Model.extend( {
		// The "constructor"
		initialize : function() {
			// Attach the post archive instance to the model
			this.createNewPostArchiveInstance();
		},

		// Define the defaults
		defaults : {
			postArchiveInstance : null
		},

		// Generates a new post archive instance and attaches it to this model
		createNewPostArchiveInstance : function() {
			var postArchiveInstance = new CollectionsApp.Collections.PostArchive( {
				model : new CollectionsApp.Models.Post()
			} );
			this.set( 'postArchiveInstance', postArchiveInstance );
		}
	} );
} ) ( window, Backbone, jQuery, _, CollectionsApp );