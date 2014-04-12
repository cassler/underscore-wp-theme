<?php
if ( ! function_exists( 'collections_add_json_query_var' ) ) :
/**
 * Register a new query variable that will be used to show a JSON representation of a post.
 *
 * @since  1.0.
 *
 * @param  array    $vars    The current query variables.
 * @return array             The modified query variables.
 */
function collections_add_json_query_var( $vars ) {
	$vars[] = 'cspa-json';
	return $vars;
}
endif;

add_filter( 'query_vars', 'collections_add_json_query_var' );

if ( ! function_exists( 'collections_json_template_redirect' ) ) :
/**
 * Redirect to a JSON representation of a post if the JSON query var is set.
 *
 * In order to power the SPA, a JSON representation of a post is needed. This function hooks into "template_redirect"
 * and will use a JSON template to render a post when needed. Appending "?cspa-json=1" to a post or a post format
 * archive will render the JSON template.
 *
 * @since  1.0.
 *
 * @return void
 */
function collections_json_template_redirect() {
	// Be extra cautious if SPA is disabled
	if ( 'false' === collections_spa_enabled() ) {
		return;
	}

	global $wp_query;

	// If this is not a request for json then bail
	if ( ! isset( $wp_query->query_vars['cspa-json'] ) || '1' !== $wp_query->query_vars['cspa-json'] ) {
		return;
	}

	// Set the appropriate header
	header( 'Content-Type: application/json; charset=utf-8' );

	// Help prevent MIME-type confusion attacks in IE8+
	send_nosniff_header();

	// Render the template and stop execution
	get_template_part( 'json', 'posts' );
	exit;
}
endif;

add_action( 'template_redirect', 'collections_json_template_redirect' );