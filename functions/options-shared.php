<?php
/**
 * @package Collections
 */

if ( ! function_exists( 'collections_admin_scripts' ) ) :
/**
 * Enqueue admin scripts and stylesheets.
 *
 * @since 1.0.
 *
 */
function collections_admin_scripts( $hook = null ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) )
		return;

	// Primary admin JavaScript
	wp_enqueue_script(
		'collections-featuredimage-toggle',
		get_template_directory_uri() . '/includes/javascripts/featuredimage-toggle.js',
		array(),
		COLLECTIONS_VERSION,
		true
	);

	// Localization strings
	$localize = array(
		'featuredImage' => __( 'Featured images are not supported for this format.', 'collections' )
	);

	// Send to the script
	wp_localize_script(
		'collections-featuredimage-toggle',
		'CollectionsAdminLocalization',
		$localize
	);
}
endif;

add_action( 'admin_enqueue_scripts', 'collections_admin_scripts' );

if ( ! function_exists( 'collections_get_social_links' ) ) :
/**
 * Get the social links from options.
 *
 * @since  1.0.
 *
 * @return array    Keys are service names and the values are links.
 */
function collections_get_social_links() {
	// Define default services
	$default_services = array(
		'twitter' => array(
			'title' => __( 'Twitter', 'collections' ),
		),
		'facebook' => array(
			'title' => __( 'Facebook', 'collections' ),
		),
		'google' => array(
			'title' => __( 'Google+', 'collections' ),
		),
		'flickr' => array(
			'title' => __( 'Flickr', 'collections' ),
		),
		'pinterest' => array(
			'title' => __( 'Pinterest', 'collections' ),
		),
		'linkedin' => array(
			'title' => __( 'LinkedIn', 'collections' ),
		),
		'rss' => array(
			'title' => __( 'RSS', 'collections' ),
		),
	);

	// Set up the collector array
	$services_with_links = array();

	// Get the links for these services
	foreach ( $default_services as $service => $details ) {
		$url = get_theme_mod( $service );
		if ( '' !== $url ) {
			$services_with_links[ $service ] = array(
				'title' => $details['title'],
				'url'   => $url,
			);
		}
	}

	return apply_filters( 'collections_get_social_links', $services_with_links );
}
endif;