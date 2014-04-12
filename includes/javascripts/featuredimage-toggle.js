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
	var CollectionsAdmin = function () {

		/**
		 * Holds reusable elements.
		 *
		 * @since 1.0.
		 *
		 * @type  {{}}
		 */
		var cache = {};

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

			// Toggle featured image box on Post Edit screen
			if ( 'object' === typeof cache.$format ) {
				var format;

				$(cache.$format).on('change', 'input:radio:checked', function() {
					format = $(this).val();
					featuredImageToggle( format );
				});

				$(cache.$format).ready(function() {
					$(this).find('input:radio:checked').trigger('change');
				});
			}

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
			cache.$format   = $( '#post-formats-select' );
			cache.$imgtxt   = $( '#postimagediv .hide-if-no-js' );
		}

		/**
		 * Toggle featured image box on Post Edit screen
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function featuredImageToggle( format ) {
			// Remove a prior message.
			$('#collections-admin-featuredimage').remove();
			switch(format) {
				case 'audio':
				case 'video':
				case 'image':
				case 'gallery':
					cache.$imgtxt.show();
					break;
				
				case 'aside':
				case 'link':
				case 'quote':
				case '0':
				default:
					var message = $('<p id="collections-admin-featuredimage"></p>').text(CollectionsAdminLocalization.featuredImage);
					cache.$imgtxt.before(message).hide();
					break;
			}
		}

		// Initiate the actions.
		init();
	}

	// Instantiate the "class".
	window.CollectionsAdmin = new CollectionsAdmin();
})( window, jQuery );