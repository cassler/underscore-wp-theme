<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_Content' ) ) :
/**
 * Class Collections_Content
 *
 * A parent class that contains shared methods for other Collections_* content grabber classes
 *
 * @since 1.0.
 */
class Collections_Content {
	/**
	 * The ID used to specify the post.
	 *
	 * @since 1.0.
	 *
	 * @var   int    The ID used to specify the post.
	 */
	var $post_id = 0;

	/**
	 * The post format of the specified post.
	 *
	 * @since 1.0.
	 *
	 * @var   string    The post format of the specified post.
	 */
	var $post_format = '';

	/**
	 * The piece of data extracted from the post content for special treatment.
	 *
	 * @since 1.0.
	 *
	 * @var   int|string|array    The piece of data extracted from the post content for special treatment.
	 */
	var $special_content = '';

	/**
	 * The remaining data after the special content has been removed.
	 *
	 * @since 1.0.
	 *
	 * @var   string    The remaining data after the special content has been removed.
	 */
	var $remaining_content = '';

	/**
	 * Set if special content was successfully extracted from the post content.
	 *
	 * @since 1.0.
	 *
	 * @var   boolean    Set if special content was successfully extracted from the post content.
	 */
	var $has_special = false;

	/**
	 * Set if the post is paginated and the page number is greater than 1.
	 *
	 * @since 1.0.
	 *
	 * @var   boolean    Set if the post is paginated and the page number is greater than 1.
	 */
	var $is_paged = false;

	/**
	 * Render the remaining content.
	 *
	 * @since  1.0.
	 *
	 * @return string    The rendered content.
	 */
	public function render_remaining_content() {
		$content = apply_filters( 'the_content', $this->remaining_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		return $content;
	}

	/**
	 * Get a URL in the first line of content.
	 *
	 * Split off the first line of content and determine if it is only a URL or anchor tag
	 * with an href. Adapted from get_content_url() in WP 3.6-beta4.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The content to split.
	 * @return array                 The separated pieces of content.
	 */
	protected function _get_first_line_url( $content ) {
		$output = array();

		if ( empty( $content ) )
			return $output;

		// Isolate the first line
		$lines = explode( "\n", trim( $content ) );
		$line  = trim( array_shift( $lines ) );

		// Test to see if the first line is a URL
		if ( 0 === stripos( $line, 'http' ) ) {
			$output['url']     = esc_url_raw( $line );
			$output['content'] = trim( join( "\n", $lines ) );
		} else if ( preg_match( '#^<a\s[^>]*href=([\'"])(.+?)\1[^>]*>.*</a>$#is', $line, $matches ) ) {
			$output['url']     = esc_url_raw( $matches[2] );
			$output['content'] = trim( join( "\n", $lines ) );
		}

		return $output;
	}

	/**
	 * Get a shortcode in the first line of content.
	 *
	 * Split off the first line of content and determine if it is only a shortcode from an array of
	 * allowed shortcode tags.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $tags       An array of allowed shortcode tags.
	 * @param  string    $content    The content to split.
	 * @return array                 The separated pieces of content.
	 */
	protected function _get_first_line_shortcode( $tags, $content ) {
		$output = array();

		if ( empty( $content ) )
			return $output;

		// Isolate the first line
		$lines = explode( "\n", trim( $content ) );
		$line  = trim( array_shift( $lines ) );

		// Cycle through each tag in the array until there's a positive match.
		foreach ( (array) $tags as $tag ) {
			// Test to see if the first line is the given shortcode tag, by itself
			if ( has_shortcode( $line, $tag ) && ! trim( strip_shortcodes( $line ) ) ) {
				$output['tag']     = $line;
				$output['content'] = trim( join( "\n", $lines ) );
				break;
			}
		}

		return $output;
	}

	/**
	 * Get the Media Library ID of an attachment from its URL.
	 *
	 * This function is taken from WordPress 3.6-beta3, though it looks like it may get removed from the final 3.6 release.
	 *
	 * @since  1.0.
	 *
	 * @param  string      $url    The path to an image.
	 * @return int|bool            ID of the attachment or 0 on failure.
	 */
	protected function _get_attachment_id_from_url( $url ) {
		global $wpdb;

		if ( preg_match( '#\.[a-zA-Z0-9]+$#', $url ) ) {
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' " . "AND guid = %s", $url ) );

			if ( ! empty( $id ) )
				return (int) $id;
		}

		return 0;
	}

	/**
	 * Get a particular type of attachment on a post.
	 *
	 * Grab a specified max number of attachments of a particular mime type from the current post.
	 *
	 * @since  1.0.
	 *
	 * @param  int       $post_id        The ID of the parent post.
	 * @param  string    $mime_type      The mime type to filter attachments by.
	 * @param  int       $numberposts    The number of attachments to grab.
	 * @return array                     An array of post objects.
	 */
	protected function _get_matching_attachments( $post_id, $mime_type, $numberposts = -1 ) {
		// Clean the ID
		$clean_id = absint( $post_id );

		// Attachment args
		$args = array(
			'post_parent'    => $clean_id,
			'post_type'      => 'attachment',
			'post_mime_type' => $mime_type,
			'numberposts'    => $numberposts,
			'order'          => 'ASC',
			'orderby'        => 'menu_order date' // Menu order only exists if the attachments have been manually arranged.
		);

		// Get the attachment
		$attachments = get_children( $args );

		return $attachments;
	}

	/**
	 * Get the value of the first href in the content.
	 *
	 * Search the content for URLs within href attributes and return the first one.
	 * Adapted from get_content_url() in WP 3.6-beta4
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The content to search.
	 * @return array                 The parsed content.
	 */
	protected function _get_first_href( $content ) {
		$output = array();

		if ( empty( $content ) )
			return $output;

		// Grab the first href value
		if ( preg_match( '/<a\s[^>]*?href=([\'"])(.+?)\1/is', $content, $matches ) ) {
			$output['url']     = esc_url_raw( $matches[2] );
			$output['content'] = $content;
		}

		return $output;
	}

	/**
	 * Check if a string contains a shortcode from an array of shortcode tags.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $string    String to check for shortcodes.
	 * @param  array     $tags      Array of tags to search for.
	 * @return bool                 Set if a matching tag is found.
	 */
	protected function _shortcode_in_array( $string, $tags ) {
		foreach ( (array) $tags as $tag ) {
			if ( has_shortcode( $string, $tag ) )
				return true;
		}

		return false;
	}

	/**
	 * Sanitize and validate a post ID.
	 *
	 * @since  1.0.
	 *
	 * @param  string|int    $post_id    The post ID.
	 * @return int                       0 if invalid. Clean ID if successful.
	 */
	protected function _clean_post_id( $post_id ) {
		$clean_id = absint( $post_id );

		// If the ID is 0, do not perform any more checks.
		if ( 0 !== $clean_id ) {

			// Validate that get_post( n ) returns post n
			$post = get_post( $clean_id );
			if ( $post->ID !== $post_id ) {
				$clean_id = 0;
			}
		}

		return $clean_id;
	}

	/**
	 * Determine and set the post_format property.
	 *
	 * Compare the format of the current post to a list of allowed formats. Return the format
	 * if it is allowed, otherwise return an empty string.
	 *
	 * @since  1.0.
	 *
	 * @param  int       $post_id            The post ID.
	 * @param  array     $allowed_formats    An array of allowed post format names.
	 * @return string                        The current post format, or an empty string.
	 */
	protected function _set_post_format( $post_id, $allowed_formats = array() ) {
		$clean_id = absint( $post_id );

		if ( empty( $allowed_formats ) )
			$allowed_formats = array_keys( get_post_format_strings() );

		$format = get_post_format( $clean_id );
		if ( in_array( $format, $allowed_formats ) )
			return $format;

		return '';
	}

	/**
	 * Determine and set the is_paged property.
	 *
	 * Detect whether the current post is paginated and is set to display a page other than the first.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	protected function _set_paged() {
		// Bail if the global postdata doesn't match the post in the content object.
		if ( get_the_ID() !== $this->post_id )
			return;

		global $page, $pages, $multipage;

		if ( $page > count( $pages ) )
			$page = count( $pages );

		if ( $multipage && $page > 1 )
			$this->is_paged = true;
	}
}
endif;

if ( ! function_exists( 'collections_has_special' ) ) :
/**
 * Template tag: Test for special content in the current post.
 *
 * @since  1.0.
 *
 * @return bool    True if the post has special content.
 */
function collections_has_special() {
	global $collections_post;

	if ( empty( $collections_post ) )
		return false;

	return $collections_post->has_special;
}
endif;

if ( ! function_exists( 'collections_the_special_content' ) ) :
/**
 * Template tag: Display a post's special content.
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The special content.
 */
function collections_the_special_content( $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	$render = $collections_post->render_special_content();

	// Return or echo.
	if ( false === $echo )
		return $render;
	else
		echo $render;
}
endif;

if ( ! function_exists( 'collections_the_remaining_content' ) ) :
/**
 * Template tag: Display a post's remaining content.
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The remaining content.
 */
function collections_the_remaining_content( $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	$render = $collections_post->render_remaining_content();

	// Return or echo.
	if ( false === $echo )
		return $render;
	else
		echo $render;
}
endif;

if ( ! function_exists( 'collections_get_circle_size_class' ) ) :
/**
 * Filter: Add circle class to certain posts.
 *
 * Generate a class that can be used to size the circles in the Link and Quote
 * archive layouts. The class suffix will be either 1 or 2, based on the character
 * count of the content.
 *
 * @since  1.0.
 *
 * @param  array     $classes    The array of post classes.
 * @param  string    $class      Unused.
 * @param  int       $post_id    Unused.
 * @return array                 The filtered array of post classes.
 */
function collections_get_circle_size_class( $classes, $class, $post_id ) {
	// Make sure it's a relevant format.
	$formats = array( 'link', 'quote' );
	$f = get_post_format();
	if ( ! in_array( $f, $formats ) || ! is_tax( 'post_format' ) )
		return $classes;

	// Prep content.
	$content = ( 'link' === $f ) ? collections_the_link_archive_content( false ) : collections_the_quote_archive_content( false );
	$clean_content = trim( strip_tags( $content ) );

	// Count characters.
	$char_count = strlen( $clean_content );
	if ( $char_count > 60 )
		$char_count = 60;
	if ( $char_count < 1 )
		$char_count = 1;

	// Generate class.
	$class_suffix = ceil( $char_count / 30 );
	$classes[] = 'collections-circle-' . absint( $class_suffix );
	$classes[] = 'collections-circle';
	$classes[] = 'faux-link';

	return $classes;
}
endif;

add_filter( 'post_class', 'collections_get_circle_size_class', 10, 3 );