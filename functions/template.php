<?php
/**
 * @package Collections
 */

if ( ! function_exists( 'collections_is_js_template' ) ) :
/**
 * Conditional: Determine if the current template should be a JS template.
 *
 * @since  1.0.
 *
 * @return bool    True if it is a JS template. False if it is not.
 */
function collections_is_js_template() {
	return defined( 'COLLECTIONS_IS_JS_TEMPLATE' ) && COLLECTIONS_IS_JS_TEMPLATE;
}
endif;

if ( ! function_exists( 'collections_get_background_image_style' ) ) :
/**
 * Template tag: Generate an inline-style string to add a background image to a particular frame.
 *
 * @since  1.0.
 *
 * @param  int       $attachment_id    The ID of the image.
 * @param  string    $size             The name of the image size to use.
 * @param  array     $frame_size       Frame dimensions width/height array.
 * @return string                      String of inline CSS.
 */
function collections_get_background_image_style( $attachment_id, $size, $frame_size = null ) {
	if ( empty( $attachment_id ) ) {
		return '';
	}

	$style = '';

	$img = wp_get_attachment_image_src( $attachment_id, $size );

	if ( ! empty( $img ) ) {
		$style .= "background-image: url('" . esc_url( $img[0] ) . "');";

		if ( is_array( $frame_size ) ) {
			// Get aspect ratios
			$frame_aspect = (int) $frame_size[0] / (int) $frame_size[1];
			$img_aspect = (int) $img[1] / (int) $img[2];
			$comp = $frame_aspect - $img_aspect;

			if ( $comp > 0 ) {
				$style .= ' background-size: 100% auto;';
			} else if ( $comp < 0 ) {
				$style .= ' background-size: auto 100%;';
			} else {
				$style .= ' background-size: 100% 100%;';
			}
		}
	}

	return $style;
}
endif;

if ( ! function_exists( 'collections_read_more' ) ) :
/**
 * Filter: Produce a reusable read more link.
 *
 * @since  1.0.
 *
 * @return string    The read more link.
 */
function collections_read_more() {
	$href = ( collections_is_js_template() ) ? "{{ permalink }}" : esc_url( get_permalink() );
	return sprintf(
		'&hellip; <a class="collections-more-link" href="%2$s">%1$s</a>',
		__( 'read more', 'collections' ),
		$href
	);
}
endif;

if ( ! function_exists( 'collections_faux_read_more' ) ) :
/**
 * Filter: Produce a read more element for jQuery to attach events to.
 *
 * @since  1.0.
 *
 * @return string    The read more link.
 */
function collections_faux_read_more() {
	return sprintf(
		'&hellip; <span class="collections-more-link">%1$s</span>',
		__( 'read more', 'collections' )
	);
}
endif;

if ( ! function_exists( 'collections_read_more_excerpt' ) ) :
/**
 * Filter: Add Read More link as a filter on the excerpt.
 *
 * @since  1.0.
 *
 * @param  string    $more_text    The current read more text.
 * @return string                  Modified read more text.
 */
function collections_read_more_excerpt( $more_text ) {
	if ( is_front_page() || is_page_template( 'homepage.php' ) ) {
		return collections_faux_read_more();
	} else {
		return collections_read_more();
	}
}
endif;

add_filter( 'excerpt_more', 'collections_read_more_excerpt' );

if ( ! function_exists( 'collections_truncate_excerpt' ) ) :
/**
 * Filter: Truncate excerpt length.
 *
 * @since  1.0.
 *
 * @param  int    $length    Default length.
 * @return int               Modified length.
 */
function collections_truncate_excerpt( $length ) {
	return 25;
}
endif;

if ( ! function_exists( 'collections_get_homepage_background_image' ) ) :
/**
 * Template tag: Based on the collection type, generate a background-image property for the box.
 *
 * @since  1.0.
 *
 * @param  string    $type    The collection type.
 * @return string             The background-image property.
 */
function collections_get_homepage_background_image( $type ) {
	$background = '';

	// If in the photos collection, get the special content
	if ( 'photos' === $type ) {
		// If gallery, get the gallery cover image
		if ( 'gallery' === get_post_format() ) {
			$url = collections_get_the_gallery_cover_url( 'collections-homepage-background' );
		} else {
			$url = collections_get_the_image_url( 'collections-homepage-background' );
		}

		// If in the videos collection, get the video's post thumbnail
	} else if ( 'videos' === $type ) {
		$thumb_id = get_post_thumbnail_id();

		if ( $thumb_id ) {
			$src = wp_get_attachment_image_src( $thumb_id, 'collections-homepage-background' );
			$url = $src[0];
		}
	}

	// Create the background property if there is a URL
	if ( isset( $url ) ) {
		$background = ' style="background-image: url(' . addcslashes( esc_url_raw( $url ), '"' ) . ');"';
	}

	return $background;
}
endif;

if ( ! function_exists( 'collections_page_title' ) ) :
/**
 * Filter: Adjust the page title based on context.
 *
 * @since  1.0.
 *
 * @param  string    $title    Current title value.
 * @return string              New value of the title.
 */
function collections_page_title( $title ) {
	// We don't want to affect RSS feeds
	if ( is_feed() )
		return $title;

	if ( is_front_page() ) {
		return get_bloginfo( 'name' );
	} elseif ( is_404() ) {
		return __( 'Page Not Found | ', 'collections' ) . get_bloginfo( 'name' );
	} elseif ( is_search() ) {
		return __( 'Search results | ', 'collections' ) . get_bloginfo( 'name' );
	} else {
		return trim( $title ) . ' | ' . get_bloginfo( 'name' );
	}
}
endif;

add_filter( 'wp_title', 'collections_page_title' );

if ( ! function_exists( 'collections_get_next_posts_button' ) ) :
/**
 * Template tag: A recreation of "get_next_posts_link" to display as a button.
 *
 * @since  1.0.
 *
 * @param  null      $label       The label to use for the button.
 * @param  int       $max_page    The maximum number of pages that can be displayed.
 * @return string                 The resulting HTML.
 */
function collections_get_next_posts_button( $label = null, $max_page = 0 ) {
	global $paged, $wp_query;

	if ( ! $max_page )
		$max_page = $wp_query->max_num_pages;

	if ( ! $paged )
		$paged = 1;

	$nextpage = intval( $paged ) + 1;

	if ( null === $label )
		$label = __( 'Load More', 'collections' );

	if ( ! is_single() && ( $nextpage <= $max_page ) ) {
		$url = next_posts( $max_page, false );

		// Clean bits and pieces of the URL
		$url = str_replace(
			array(
				'?cspa-json=1',
				'&cspa-json=1',
				'&#038;cspa-json=1',
				'&amp;cspa-json=1',
				'/&amp;',
				'/&#038;',
				'/&',
				'&#038;paged='
			),
			array(
				'',
				'',
				'',
				'',
				'/?',
				'/?',
				'/?',
				'&paged='
			),
			$url
		);

		$pieces = parse_url( $url );

		// Extract the domain piece from the next post URL.
		$path = '';
		if ( isset( $pieces['path'] ) ) {
			$path .= ltrim( $pieces['path'], '/' );
		}

		if ( isset( $pieces['query'] ) ) {
			$path .= '?' . $pieces['query'];
		}

		return '<button class="cspa-load-more cspa-control" data-pathname="' . esc_attr( $path ) . '" data-url="' . esc_url( $url ) . '">' . $label . '</button>';
	}
}
endif;