<?php
// Include the JSON endpoint
require( 'json-endpoint.php' );

if ( ! function_exists( 'collections_add_backbone_scripts' ) ) :
/**
 * Add scripts for the SPA.
 *
 * @since  1.0.
 *
 * @return void
 */
function collections_add_backbone_scripts() {
	if ( ! collections_spa_enabled() ) {
		return;
	}

	$debug = defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG;
	$wpcom = defined( 'IS_WPCOM' ) && IS_WPCOM;

	if ( true === $debug || true === $wpcom ) {
		wp_enqueue_script(
			'collections-spa/app.js',
			get_template_directory_uri() . '/includes/javascripts/spa/app.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/collections/post-archive.js',
			get_template_directory_uri() . '/includes/javascripts/spa/collections/post-archive.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/models/core.js',
			get_template_directory_uri() . '/includes/javascripts/spa/models/core.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/models/post.js',
			get_template_directory_uri() . '/includes/javascripts/spa/models/post.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/views/core.js',
			get_template_directory_uri() . '/includes/javascripts/spa/views/core.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/views/post-archive.js',
			get_template_directory_uri() . '/includes/javascripts/spa/views/post-archive.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/views/post-single.js',
			get_template_directory_uri() . '/includes/javascripts/spa/views/post-single.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa/routers/router.js',
			get_template_directory_uri() . '/includes/javascripts/spa/routers/router.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);

		wp_enqueue_script(
			'collections-spa',
			get_template_directory_uri() . '/includes/javascripts/spa/main.js',
			array(
				'backbone',
				'collections-spa/app.js',
				'collections-spa/models/core.js',
				'collections-spa/models/post.js',
				'collections-spa/collections/post-archive.js',
				'collections-spa/views/core.js',
				'collections-spa/views/post-archive.js',
				'collections-spa/views/post-single.js',
				'collections-spa/routers/router.js',
			),
			COLLECTIONS_VERSION,
			true
		);
	} else {
		wp_enqueue_script(
			'collections-spa',
			get_template_directory_uri() . '/includes/javascripts/spa/spa.min.js',
			array( 'backbone' ),
			COLLECTIONS_VERSION,
			true
		);
	}

	global $paged;

	// Create the path; should be in the form of "blah/blah/"
	$path = $_SERVER['REQUEST_URI'];

	// Trim "/" from in front of the path
	$path = ltrim( $path, '/' );

	// Add "/" to the end
	if ( get_option( 'permalink_structure' ) ) {
		$path = trailingslashit( $path );
	} elseif ( empty( $path ) ) {
		$path = '/';
	}

	// Prepare data array
	$data = array(
		'isSingle'    => ( is_single() ) ? 1 : 0,
		'isTax'       => ( is_tax() ) ? 1 : 0,
		'isFrontPage' => ( is_front_page() ) ? 1 : 0,
		'isHome'      => ( is_home() ) ? 1 : 0,
		'isArchive'   => ( is_archive() ) ? 1 : 0,
		'page'        => $paged,
		'pathname'    => $path,
		'permalink'   => ( get_option( 'permalink_structure' ) ) ? 1 : 0,
	);

	wp_localize_script(
		'collections-spa',
		'collectionsSPAData',
		$data
	);
}
endif;

add_action( 'wp_enqueue_scripts', 'collections_add_backbone_scripts' );

if ( ! function_exists( 'collections_denote_loaded_scripts_and_styles' ) ) :
/**
 * On page load, denote the existing scripts and styles that are loaded.
 *
 * This function is modified from Automattic's Infinite Scroll that is available via the JetPack plugin.
 *
 * @author Automattic's Infinite Scroll (http://jetpack.me/support/infinite-scroll/).
 * @since  1.0.
 *
 * @return void
 */
function collections_denote_loaded_scripts_and_styles() {
	if ( ! collections_spa_enabled() ) {
		return;
	}

	global $wp_scripts, $wp_styles;

	$scripts = ( is_a( $wp_scripts, 'WP_Scripts' ) ) ? $wp_scripts->done : array();
	$styles  = ( is_a( $wp_styles, 'WP_Styles' ) ) ? $wp_styles->done : array();
?>
	<script type="text/javascript">
		collectionsSPAData.scripts = <?php echo json_encode( $scripts ); ?>;
		collectionsSPAData.styles = <?php echo json_encode( $styles ); ?>;
	</script>
<?php
}
endif;

add_action( 'wp_footer', 'collections_denote_loaded_scripts_and_styles', 20 );

if ( ! function_exists( 'collections_get_rendered_scripts' ) ) :
/**
 * Get all scripts and styles needed for a page load.
 *
 * This function borrows from Jetpack's Infinite Scroll. It runs wp_head() and wp_footer(), but silences the output from
 * those functions via output buffering. By running those functions, the scripts that would be rendered are denoted in
 * $wp_scripts/styles->done. After those functions run, the needed scripts can be extracted from the done properties.
 * The function returns an array of script and style IDs.
 *
 * @since  1.0.
 *
 * @param  bool     $run_tags    Whether or not to run wp_head() and wp_footer().
 * @return array                 Array of script and style IDs.
 */
function collections_get_rendered_enqueues( $run_tags = true ) {
	global $wp_scripts, $wp_styles;

	// Run functions without outputting any content
	if ( true === $run_tags ) {
		ob_start();
		wp_head();
		wp_footer();
		ob_end_clean();
	}

	// Get the scripts/styles that were rendered
	$scripts = ( is_a( $wp_scripts, 'WP_Scripts' ) ) ? $wp_scripts->done : array();
	$styles  = ( is_a( $wp_styles, 'WP_Styles' ) ) ? $wp_styles->done : array();

	return array(
		'scripts' => $scripts,
		'styles'  => $styles,
	);
}
endif;

if ( ! function_exists( 'collections_get_script_data' ) ) :
/**
 * Get all data associated with an enqueued script.
 *
 * Given a handle, all data for a script is surfaced. This function is derived from Jetpack's Infinite Scroll.
 *
 * @since  1.0.
 *
 * @param  string    $handle    The string ID for an enqueued script.
 * @return array                All data associated with a script.
 */
function collections_get_script_data( $handle ) {
	global $wp_scripts;

	// Exit if the handle is not registered
	if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
		return array();
	}

	$data = array(
		'handle' => $handle,
		'footer' => ( is_array( $wp_scripts->in_footer ) && in_array( $handle, $wp_scripts->in_footer ) ),
		'extra_data' => $wp_scripts->print_extra_script( $handle, false ),
	);

	// Base source
	$src = $wp_scripts->registered[ $handle ]->src;

	// Take base_url into account
	if ( strpos( $src, 'http' ) !== 0 ) {
		$src = $wp_scripts->base_url . $src;
	}

	// Append the version number if necessary
	if ( null === $wp_scripts->registered[ $handle ]->ver ) {
		$ver = '';
	} else {
		$ver = ( $wp_scripts->registered[ $handle ]->ver ) ? $wp_scripts->registered[ $handle ]->ver : $wp_scripts->default_version;
	}

	// Append any arguments
	if ( isset( $wp_scripts->args[ $handle ] ) ) {
		$ver = ( $ver ) ? $ver . '&amp;' . $wp_scripts->args[ $handle ] : $wp_scripts->args[ $handle ];
	}

	// Full script source with version info
	$data['src'] = add_query_arg( 'ver', $ver, $src );

	return $data;
}
endif;

if ( ! function_exists( 'collections_get_style_data' ) ) :
/**
 * Get all data associated with an enqueued stylesheet.
 *
 * Given a handle, all data for a stylesheet is surfaced. This function is derived from Jetpack's Infinite Scroll.
 *
 * @since  1.0.
 *
 * @param  string    $handle    The string ID for an enqueued stylesheet.
 * @return array                All data associated with a stylesheet.
 */
function collections_get_style_data( $handle ) {
	global $wp_styles;

	// Exit if the handle is not registered
	if ( ! isset( $wp_styles->registered[ $handle ] ) ) {
		return array();
	}

	$data = array(
		'handle' => $handle,
		'media'  => '',
	);

	// Get the base source
	$src = $wp_styles->registered[ $handle ]->src;

	// Take base_url into account
	if ( strpos( $src, 'http' ) !== 0 ) {
		$src = $wp_styles->base_url . $src;
	}

	// Get the style's version
	if ( null === $wp_styles->registered[ $handle ]->ver ) {
		$ver = '';
	} else {
		$ver = ( $wp_styles->registered[ $handle ]->ver ) ? $wp_styles->registered[ $handle ]->ver : $wp_styles->default_version;
	}

	// Add the additional arguments
	if ( isset($wp_styles->args[ $handle ] ) ) {
		$ver = ( $ver ) ? $ver . '&amp;' . $wp_styles->args[$handle] : $wp_styles->args[$handle];
	}

	// Complete the source for the stylesheet
	$data['src'] = add_query_arg( 'ver', $ver, $src );

	// Parse stylesheet's conditional comments if present, converting to logic executable in JS
	if ( isset( $wp_styles->registered[ $handle ]->extra['conditional'] ) && $wp_styles->registered[ $handle ]->extra['conditional'] ) {
		// First, convert conditional comment operators to standard logical operators. %ver is replaced in JS with the IE version
		$data['conditional'] = str_replace(
			array(
				'lte',
				'lt',
				'gte',
				'gt'
			),
			array(
				'%ver <=',
				'%ver <',
				'%ver >=',
				'%ver >',
			),
			$wp_styles->registered[ $handle ]->extra['conditional']
		);

		// Next, replace any !IE checks. These shouldn't be present since WP's conditional stylesheet implementation doesn't support them, but someone could be _doing_it_wrong().
		$data['conditional'] = preg_replace( '#!\s*IE(\s*\d+){0}#i', '1==2', $data['conditional'] );

		// Lastly, remove the IE strings
		$data['conditional'] = str_replace( 'IE', '', $data['conditional'] );
	}

	// Parse requested media context for stylesheet
	if ( isset( $wp_styles->registered[ $handle ]->args ) ) {
		$data['media'] = esc_attr( $wp_styles->registered[ $handle ]->args );
	}

	return $data;
}
endif;

if ( ! function_exists( 'collections_print_single_js_templates' ) ) :
/**
 * Print all of the single post JS templates in the footer.
 *
 * @since  1.0.
 *
 * @return void.
 */
function collections_print_single_js_templates() {
	if ( ! collections_spa_enabled() || ! is_single() ) {
		return;
	}

	define( 'COLLECTIONS_IS_JS_TEMPLATE', true );

	$post_format_types = array(
		'standard',
		'aside',
		'audio',
		'gallery',
		'image',
		'link',
		'quote',
		'video'
	);

	foreach ( $post_format_types as $type ) : ?>
		<script type="text/template" id="tmpl-collections-<?php echo esc_attr( $type ); ?>">
			<?php $type = ( 'standard' === $type ) ? '' : $type; ?>
			<?php get_template_part( 'format', $type ); ?>
		</script>
	<?php endforeach;
}
endif;

add_action( 'wp_footer', 'collections_print_single_js_templates', 100 );

if ( ! function_exists( 'collections_print_archive_js_templates' ) ) :
/**
 * Print all of the single post JS templates in the footer.
 *
 * @since  1.0.
 *
 * @return void.
 */
function collections_print_archive_js_templates() {
	if ( ! collections_spa_enabled() || ! ( is_tax() || is_front_page() || is_archive() || is_home() ) ) {
		return;
	}

	define( 'COLLECTIONS_IS_JS_TEMPLATE', true );

	$post_format_types = array(
		'standard',
		'aside',
		'audio',
		'gallery',
		'image',
		'link',
		'quote',
		'video'
	);

	foreach ( $post_format_types as $type ) : ?>
		<script type="text/template" id="tmpl-collections-archive-<?php echo esc_attr( $type ); ?>">
			<?php $type = ( 'standard' === $type ) ? '' : $type; ?>
			<?php get_template_part( 'format-archive', $type ); ?>
		</script>
	<?php endforeach;

	// Print the stream template ?>
		<script type="text/template" id="tmpl-collections-archive-stream">
			<?php get_template_part( 'format-archive', 'stream' ); ?>
		</script>
	<?php
}
endif;

add_action( 'wp_footer', 'collections_print_archive_js_templates', 100 );

if ( ! function_exists( 'collections_get_archives_link_filter' ) ) :
/**
 * Add the 'cspa-control' class to the archive links.
 *
 * @since  1.0.
 *
 * @param  string    $html    The original archives link.
 * @return string             The modified link.
 */
function collections_get_archives_link_filter( $html ) {
	return str_replace( '<a ', '<a class="cspa-control" ', $html );
}

add_filter( 'get_archives_link', 'collections_get_archives_link_filter' );

endif;

if ( ! function_exists( 'collections_add_spinner' ) ) :
/**
 * Print a hidden spinner to the page that will be revealed when pages load.
 *
 * @since  1.1.
 *
 * @return void
 */
function collections_add_spinner() {
?>
	<div class="cspa-spinner">
		<img src="<?php echo get_template_directory_uri(); ?>/images/stream-icon.svg" />
	</div>
<?php
}
endif;
