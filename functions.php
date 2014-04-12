<?php
/**
 * @package Collections
 */

/**
 * The current version of the theme.
 *
 * @since 1.0.
 */
define( 'COLLECTIONS_VERSION', '1.1.5' );

/**
 * Set the theme's content width.
 *
 * @since 1.0.
 *
 * @var   Sets the width for the site
 */
if ( ! isset( $content_width ) ) {
	$content_width = 522;
}

/**
 * A global var to store an instance of a Collections_* object.
 *
 * @since 1.0.
 *
 * @var   array    $collections_post    An array for storing objects.
 */
if ( ! isset( $collections_post ) ) {
	$collections_post = array();
}

if ( ! function_exists( 'collections_theme_support' ) ) :
/**
 * Setup theme basics on "after_theme_setup".
 *
 * @since  1.0.
 *
 * @return void
 */
function collections_after_setup_theme() {
	// Initiate the SPA if necessary
	if ( true === collections_spa_enabled() ) {
		require( get_template_directory() . '/functions/single-page-application/single-page-application.php' );
	}

	// Load the theme's text domain
	load_theme_textdomain( 'collections', get_template_directory() . '/includes' );

	// Includes
	require( get_template_directory() . '/functions/formats.php' );
	require( get_template_directory() . '/functions/content.php' );
	require( get_template_directory() . '/functions/photo.php' );
	require( get_template_directory() . '/functions/audio-video.php' );
	require( get_template_directory() . '/functions/link.php' );
	require( get_template_directory() . '/functions/quote.php' );
	require( get_template_directory() . '/functions/logo.php' );
	require( get_template_directory() . '/functions/homepage-query.php' );
	require( get_template_directory() . '/functions/tinymce.php' );
	require( get_template_directory() . '/functions/template.php' );
	require( get_template_directory() . '/functions/options-shared.php' );
	require( get_template_directory() . '/functions/options.php' );
	require( get_template_directory() . '/functions/avatar.php' );

	// Custom image sizes
	add_image_size( 'collections-full-width', 780, 9999 );
	add_image_size( 'collections-archive-medium', 504, 9999 );
	add_image_size( 'collections-archive-small-square', 252, 252, true );
	add_image_size( 'collections-album-cover', 450, 450, true );
	add_image_size( 'collections-video-cover', 378, 235, true );
	add_image_size( 'collections-homepage-background', 756, 470, true );

	// Pingback link
	add_action( 'wp_head', 'collections_pingback', 3 );

	// Add the theme's editor style
	add_editor_style( 'includes/stylesheets/editor-style.css' );

	// Post formats
	$formats = array(
		'aside',
		'link',
		'quote',
		'image',
		'gallery',
		'audio',
		'video'
	);
	add_theme_support( 'post-formats', $formats );

	// Audio and video thumbnails
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	// Post thumbnails
	add_theme_support( 'post-thumbnails', array( 'post', 'page', 'attachment:audio', 'attachment:video' ) );

	// Feed links
	add_theme_support( 'automatic-feed-links' );

	// Register the nav menu
	register_nav_menu( 'collections-homepage', __( 'Homepage Menu', 'collections' ) );
}
endif;

add_action( 'after_setup_theme', 'collections_after_setup_theme' );

if ( ! function_exists( 'collections_enqueue_scripts' ) ) :
/**
 * Enqueue frontend scripts and stylesheets
 *
 * @since  1.0.
 *
 * @return void
 */
function collections_enqueue_scripts() {
	// Set arrays to hold fonts and dependencies
	$fonts = array();

	/**
	 * Translators: If there are characters in your language that are not supported by one of the Google Fonts,
	 * translate the corresponding translation to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Source Sans Pro font: on or off', 'collections' ) ) {
		$fonts['sans'] = 'Source+Sans+Pro:400,600,700,400italic,600italic,700italic';
	}

	if ( 'off' !== _x( 'on', 'PT Serif font: on or off', 'collections' ) ) {
		$fonts['serif'] = 'PT+Serif:400,700,400italic,700italic';
	}

	if ( 'off' !== _x( 'on', 'Satisfy font: on or off', 'collections' ) ) {
		$fonts['script'] = 'Satisfy';
	}

	// Use Google Fonts url style to append fonts
	$fonts = implode( '|', $fonts );

	// Set the protocol
	$protocol = ( is_ssl() ) ? 'https' : 'http';

	// Enqueue the fonts
	wp_enqueue_style(
		'collections-google-fonts',
		$protocol . '://fonts.googleapis.com/css?family=' . $fonts,
		array(),
		COLLECTIONS_VERSION
	);

	// Main stylesheet
	wp_enqueue_style(
		'collections-style',
		get_stylesheet_directory_uri() . '/style.css',
		'collections-google-fonts',
		COLLECTIONS_VERSION,
		'screen'
	);

	// Add the print styles
	wp_enqueue_style(
		'collections-print-style',
		get_template_directory_uri() . '/includes/stylesheets/print-styles.css',
		array( 'collections-style' ),
		COLLECTIONS_VERSION,
		'print'
	);

	// Set the JS deps array
	$dependencies = array(
		'jquery',
	);

	// Enqueue FitVids only if needed
	if ( is_single() || is_page() ) {
		collections_fitvids_script_setup( 'collections-fitvids' );
		$dependencies[] = 'collections-fitvids';
	}

	// Enqueue dotdotdot only if needed
	if ( in_array( get_post_format(), array( 'link', 'quote' ) ) && is_tax( 'post_format' ) ) {
		wp_register_script(
			'collections-dotdotdot',
			get_template_directory_uri() . '/includes/javascripts/lib/jquery.dotdotdot.js',
			array( 'jquery' ),
			COLLECTIONS_VERSION,
			true
		);

		$dependencies[] = 'collections-dotdotdot';
	}

	// Enqueue ResponsiveSlides only if needed
	if ( collections_is_photo() && is_single() ) {
		collections_responsiveslides_script_setup( 'collections-responsiveslides' );
		$dependencies[] = 'collections-responsiveslides';
	}

	// Enqueue Masonry only if needed
	if ( collections_is_photo() && is_tax( 'post_format' ) ) {
		$dependencies[] = 'jquery-masonry';
	}

	// Primary theme JavaScript
	wp_enqueue_script(
		'collections-javascript',
		get_template_directory_uri() . '/javascripts/theme.js',
		$dependencies,
		COLLECTIONS_VERSION,
		true
	);

	// Make variables available to JS
	wp_localize_script(
		'collections-javascript',
		'collectionsVars',
		array(
			'spaEnabled' => collections_spa_enabled()
		)
	);

	// Support threaded comment replies
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;

add_action( 'wp_enqueue_scripts', 'collections_enqueue_scripts' );

if ( ! function_exists( 'collections_pingback' ) ) :
/**
 * Print the pingback link.
 *
 * @since  1.0.
 *
 * @return void
 */
function collections_pingback() {
?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
}
endif;

if ( ! function_exists( 'collections_comment_form_defaults' ) ) :
/**
 * Change the comment form defaults.
 *
 * @since  1.0.
 *
 * @param  array    $defaults    The default values.
 * @return array                 The modified defaults.
 */
function collections_comment_form_defaults( $defaults ) {
	// Set basic fields
	$defaults['comment_field']        = '<fieldset><label for="comment" class="comment-field">' . _x( 'Comment', 'noun', 'collections' ) . '</label><textarea id="comment" class="blog-textarea respond-type" name="comment" rows="10" aria-required="true" tabindex="4"></textarea></fieldset>';
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes_after']  = sprintf(
		'<p class="guidelines"><span class="respond-note">%1$s</span>' . "\n" . '</p>',
		__( 'Basic <abbr title="Hypertext Markup Language">HTML</abbr> is allowed. Your email address will not be published.', 'collections' )
	);

	// Get the generic field and commenter information for use with the form fields
	$field                = '<p><label for="%1$s" class="comment-field">%2$s</label><input class="text-input respond-type" type="text" name="%1$s" id="%1$s" value="%3$s" size="36" tabindex="%4$d" /></p>';
	$comment_author       = ( isset( $_POST['author'] ) ) ? trim( strip_tags( $_POST['author'] ) ) : null;
	$comment_author_email = ( isset( $_POST['email'] ) )  ? trim( $_POST['email'] ) : null;
	$comment_author_url   = ( isset( $_POST['url'] ) )    ? trim( $_POST['url'] ) : null;

	// Using information above, set the individual form fields
	$author_field = sprintf(
		$field,
		'author',
		__( 'Name <span class="required">(Required)</span>', 'collections' ),
		esc_attr( $comment_author ),
		1
	);

	$email_field = sprintf(
		$field,
		'email',
		__( 'Email <span class="required">(Required)</span>', 'collections' ),
		esc_attr( $comment_author_email ),
		2
	);

	$url_field = sprintf(
		$field,
		'url',
		__( 'Website', 'collections' ),
		esc_attr( $comment_author_url ),
		3
	);

	// Set the fields in the $defaults array
	$defaults['fields'] = array(
		'author' => $author_field,
		'email'  => $email_field,
		'url'    => $url_field,
	);

	return $defaults;
}
endif;

add_filter( 'comment_form_defaults', 'collections_comment_form_defaults' );

if ( ! function_exists( 'collections_comment' ) ) :
/**
 * Callback function for writing the comments.
 *
 * @since  1.0.
 *
 * @param  string    $comment    The comment text.
 * @param  array     $args       Arguments to adjust the output.
 * @param  int       $depth      Comment depth.
 * @return void
 */
function collections_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?>>
		<div id="comment-<?php comment_ID(); ?>">
			<div class="collections-avatar-wrapper">
				<?php echo get_avatar( $comment, $size = '90' ); ?>
			</div>
			<article class="respond-body">
				<header class="comment-author vcard">
					<span class="fn comment-name"><?php comment_author_link(); ?></span>
				</header>

				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p><?php _e( 'Your comment is awaiting moderation.', 'collections' ); ?></p>
				<?php endif ?>

				<section class="comment-text entry-content">
					<?php comment_text(); ?>
				</section>

				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

				<?php
					printf(
						'<a title="%1$s" href="%2$s"><time class="comment-date post-detail" datetime="%3$s">%4$s</time></a>',
						esc_attr( __( 'Permalink', 'collections' ) ),
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						get_comment_date()
					);
				?>

			</article>
		</div>
	<?php
}
endif;

if ( ! function_exists( 'collections_get_stream_url' ) ) :
/**
 * Return the URL of the "stream" (posts page)
 *
 * @since  1.0.
 *
 * @return string    The stream URL.
 */
function collections_get_stream_url() {
	$page_for_posts = get_option( 'page_for_posts' );

	if ( 'page' === get_option( 'show_on_front' ) && $page_for_posts ) {
		$url = esc_url( get_permalink( $page_for_posts ) );
	} else {
		$url = esc_url( home_url( '/' ) );
	}

	return $url;
}
endif;

if ( ! function_exists( 'collections_archives_title' ) ) :
/**
 * Print archive title depending on the archive context.
 *
 * @since  1.0.
 *
 * @param  bool           $echo    True to echo results. False to return results
 * @return void|string             String if echo is false. Void if echo is false.
 */
function collections_archives_title( $echo = true ) {
	global $wp_query;

	$result = '';

	if ( is_category() ) {
		$result = sprintf( __( 'Posts from the <strong>%s</strong> category', 'collections' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$result = sprintf( __( 'Posts tagged &#8216;<strong>%s</strong>&#8217;', 'collections' ), single_tag_title( '', false ) );
	} elseif ( is_day() ) {
		$result = sprintf( __( 'Archive for <strong>%s</strong>', 'collections' ), get_the_time( 'F jS, Y' ) );
	} elseif ( is_month() ) {
		$result = sprintf( __( 'Archive for <strong>%s</strong>', 'collections' ), get_the_time( 'F, Y' ) );
	} elseif ( is_year() ) {
		$result = sprintf( __( 'Archive for <strong>%s</strong>', 'collections' ), get_the_time( 'Y' ) );
	} elseif ( is_author() ) {

		// In order for "get_the_author" to work, we need to call "the_post"
		the_post();

		$result = sprintf( __( 'Posts by <strong>%s</strong>', 'collections' ), get_the_author() );

		// Rewind the posts to reset the loop
		rewind_posts();

	} elseif ( is_search() ) {
		$search_count = $wp_query->found_posts;
		$result = sprintf( _n( '%1$d result for: &#8216;<strong>%2$s</strong>&#8217;', '%1$d results for &#8216;<strong>%2$s</strong>&#8217;', $search_count, 'collections' ), $search_count, get_search_query() );
	} elseif ( is_paged() ) {
		$result = __( 'Blog Archives', 'collections' );
	}

	if ( true === $echo ) {
		echo $result;
	} else {
		return $result;
	}
}
endif;

if ( ! function_exists( 'collections_setup_data' ) ) :
/**
 * Setup the content grabber class for post formats.
 *
 * This function hooks into the_post and uses its data to create a new instance
 * of a content grabber class, after first clearing out a previous instance.
 *
 * @since  1.0.
 *
 * @param  object    $post    The current post object.
 * @return void
 */
function collections_setup_data( $post ) {
	global $collections_post;
	$collections_post = array();

	$id = absint( $post->ID );

	if ( collections_is_article( $id ) ) {
		return;
	} else if ( collections_is_photo( $id ) ) {
		$collections_post = new Collections_Photo( $id );
	} else if ( collections_is_av( $id ) ) {
		$collections_post = new Collections_AV( $id );
	} else {
		$obj = 'Collections_' . ucwords( get_post_format( $id ) );
		$collections_post = new $obj;
	}
}
endif;

add_action( 'the_post', 'collections_setup_data' );

if ( ! function_exists( 'collections_spa_enabled' ) ) :
/**
 * Determines if the single page app is turned on or not.
 *
 * @since  1.0.
 *
 * @return bool    True if enabled. False if not enabled.
 */
function collections_spa_enabled() {
	// 1 = SPA disabled; 0 = SPA enabled
	$is_disabled = (bool) get_theme_mod( 'disable-spa', 0 );
	return ! $is_disabled;
}
endif;

if ( ! function_exists( 'collections_content_width' ) ) :
/**
 * Adjusts content_width value for homepage and archive page.
 *
 * @since  1.0
 *
 * @return void
 */
function collections_content_width() {
	global $content_width;

	$template = get_page_template_slug();

	if ( 'homepage.php' === $template ) {
		$content_width = 321;
	}

	if ( is_tax() ) {
		$content_width = 500;
	}

	if ( is_single() ) {
		$content_width = 780;
	}

	if ( is_page() ) {
		$content_width = 524;
	}
}
endif;

add_action( 'template_redirect', 'collections_content_width' );

if ( ! function_exists( 'collections_fitvids_script_setup' ) ) :
/**
 * Enqueue scripts needed for Fitvids and localize.
 *
 * @since 1.1
 *
 * @param string $name    The handle for registering the script.
 */
function collections_fitvids_script_setup( $name ) {
	wp_enqueue_script(
		$name,
		get_template_directory_uri() . '/includes/javascripts/lib/jquery.fitvids.js',
		array( 'jquery' ),
		'1.1',
		true
	);

	// Set the options for the slider
	$selector_array = array(
		"iframe[src*='www.viddler.com']",
		"iframe[src*='money.cnn.com']",
		"iframe[src*='www.educreations.com']",
		"iframe[src*='//blip.tv']",
		"iframe[src*='//embed.ted.com']",
		"iframe[src*='//www.hulu.com']",
	);
	$fitvids_custom_selectors = array(
		'customSelector' => implode( ',', $selector_array )
	);

	// Allow dev to customize the options
	$fitvids_custom_selectors = apply_filters( 'collections_fitvids_custom_selectors', $fitvids_custom_selectors, get_the_ID() );

	// Send to the script
	wp_localize_script(
		$name,
		'CollectionsFitvidsCustomSelectors',
		$fitvids_custom_selectors
	);
}
endif;

if ( ! function_exists( 'collections_responsiveslides_script_setup' ) ) :
/**
 * Enqueue scripts needed for Responsive Slides slider.
 *
 * @since 1.0.
 *
 * @param string $name    The handle for registering the script.
 */
function collections_responsiveslides_script_setup( $name ) {
	wp_enqueue_script(
		$name,
		get_template_directory_uri() . '/includes/javascripts/lib/jquery.responsiveslides.js',
		array( 'jquery' ),
		'1.54',
		true
	);

	// Set the options for the slider
	$responsive_slides_options = array(
		'nav' => true,
		'pause' => true,
		'prevText' => '',
		'nextText' => ''
	);

	// Allow dev to customize the options
	$responsive_slides_options = apply_filters( 'collections_responsive_slides_options', $responsive_slides_options, get_the_ID() );

	// Send to the script
	wp_localize_script(
		$name,
		'CollectionsResponsiveSlidesOptions',
		$responsive_slides_options
	);
}
endif;

if ( ! function_exists( 'collections_post_parity' ) ) :
/**
 * Filter: Add parity class to list of post classes.
 *
 * Determines if a post is odd or even, based on its position in the query results.
 *
 * @since  1.0
 *
 * @param  array     $classes    The list of post classes that will be applied before filtering.
 * @param  string    $class      Class to add.
 * @param  int       $post_id    The post's ID.
 * @return array                 The list of post classes that will be applied after filtering.
 */
function collections_post_parity( $classes, $class, $post_id ) {
	global $wp_query;
	$post_num = $wp_query->current_post + 1;

	if ( 0 === $post_num % 2 )
		$classes[] = 'collections-even';
	else
		$classes[] = 'collections-odd';

	return $classes;
}
endif;

add_filter( 'post_class', 'collections_post_parity', 10, 3 );

if ( ! function_exists( 'collections_allowed_tags' ) ) :
/**
 * Allow only the allowedtags array in a string.
 *
 * @since  1.1
 *
 * @param  string    $string    The unsanitized string.
 * @return string               The sanitized string.
 */
function collections_allowed_tags( $string ) {
	global $allowedtags;
	return wp_kses( $string , $allowedtags );
}
endif;