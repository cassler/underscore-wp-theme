<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_AV' ) ) :
/**
 * Class Collections_AV
 *
 * A class for grabbing and handling special post format content in Audio and Video posts.
 * Forked from Photography 2.0.16
 *
 * @since 1.0.
 */
class Collections_AV extends Collections_Content {

	/**
	 * Additional vars from parent class:
	 * $post_id, $post_format, $special_content, $remaining_content, $has_special, $is_paged
	 */

	/**
	 * An array of supported audio shortcode tags.
	 *
	 * @since 1.0.
	 *
	 * @var   array    An array of supported gallery shortcode tags.
	 */
	var $audio_shortcodes = array();

	/**
	 * An array of supported video shortcode tags.
	 *
	 * @since 1.0.
	 *
	 * @var   array    An array of supported gallery shortcode tags.
	 */
	var $video_shortcodes = array();

	/**
	 * The title of the media.
	 *
	 * @since 1.0.
	 *
	 * @var   string    The title of the media.
	 */
	var $av_title = '';

	/**
	 * An array of metadata extracted from the media file.
	 *
	 * @since 1.0.
	 *
	 * @var   array    An array of metadata extracted from the media file.
	 */
	var $av_metadata = array();

	/**
	 * The id of the thumbnail, either of the media file or the post.
	 *
	 * @since 1.0.
	 *
	 * @var   int    The id of the thumbnail, either of the media file or the post.
	 */
	var $av_thumb = 0;

	/**
	 * Is the URL a renderable oEmbed link?
	 *
	 * @since 1.0.
	 *
	 * @var   bool    Is the URL a renderable oEmbed link?
	 */
	var $av_oembed = false;

	/**
	 * Construct the object by setting up basic vars and then running the split.
	 *
	 * @since 1.0.
	 *
	 * @param int    $post_id    The ID used to specify the post.
	 */
	public function __construct( $post_id = 0 ) {
		// Default to the global post ID if none is given.
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		// Clean the ID
		$this->post_id = $this->_clean_post_id( $post_id );

		// Get the post format
		$this->post_format = $this->_set_post_format( $this->post_id, array( 'audio', 'video' ) );

		// Detect pagination
		$this->_set_paged();

		// Audio shortcode tags
		$this->audio_shortcodes = apply_filters( 'collections_audio_shortcodes', array( 'audio', 'embed' ) );

		// Video shortcode tags
		$this->video_shortcodes = apply_filters( 'collections_video_shortcodes', array( 'video', 'embed', 'wpvideo' ) );

		// Populate the object
		if ( 0 !== $this->post_id && '' !== $this->post_format ) {
			// Populate the class properties
			$this->_split_content( $this->post_id, $this->post_format );
		}
	}

	/**
	 * Compile AV data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The array of data to use for rendering, etc.
	 */
	public function compile_av_data() {
		$data = array(
			'title'   => $this->av_title,
			'content' => $this->special_content,
			'meta'    => $this->av_metadata,
			'thumb'   => $this->av_thumb
		);

		return $data;
	}

	/**
	 * Content split function for Audio and Video posts.
	 *
	 * Audio and video embedding are nearly identical in WordPress so we can use the same
	 * split function for both formats.
	 *
	 * @since  1.0.
	 *
	 * @param  int       $post_id    The post ID.
	 * @param  string    $format     The post format.
	 *
	 * @return void
	 */
	private function _split_content( $post_id, $format ) {
		// If is_paged, use the first page of the content to derive the special content.
		global $pages;
		$current_page = get_the_content( null, false, $post_id );
		$content = ( true === $this->is_paged ) ? $pages[0] : $current_page;

		// 1. Plain URL on first line (id or url)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_line_url( $content );
			if ( ! empty( $split ) ) {
				// The URL matches a file in the media library
				if ( 0 !== $attachment_id = $this->_get_attachment_id_from_url( $split['url'] ) ) {
					$this->special_content = $attachment_id;
					$this->av_title = get_the_title( $this->special_content );
					$this->av_metadata = get_post_meta( $this->special_content, '_wp_attachment_metadata', true );
				}
				// The URL is external
				else {
					$this->special_content = $split['url'];
					$this->av_oembed = $this->_check_embed_handlers( $this->special_content );
				}
				$this->av_thumb = $this->_get_av_thumbnail_id( $this->special_content );
				// If is_paged, use the normal get_the_content method for the remaining content.
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 2. Audio or Video shortcode (shortcode string)
		if ( '' === $this->special_content ) {
			$shortcodes = ( 'audio' === $format ) ? $this->audio_shortcodes : $this->video_shortcodes;
			$split = $this->_get_first_line_shortcode( $shortcodes, $content );
			if ( ! empty( $split ) ) {
				$this->special_content = $split['tag'];
				// The media referred to in the shortcode is in the Media Library
				if ( 0 !== $attachment_id = $this->_get_av_shortcode_id( $split['tag'] ) ) {
					$this->av_title = get_the_title( $attachment_id );
					$this->av_metadata = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
				}
				$this->av_thumb = $this->_get_av_thumbnail_id( $attachment_id );
				// If is_paged, use the normal get_the_content method for the remaining content.
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 3. First compatible attachment (id)
		if ( '' === $this->special_content ) {
			$attachment = $this->_get_matching_attachments( $post_id, $format, 1 );
			$attachment = array_shift( $attachment );
			if ( $attachment ) {
				$this->special_content = absint( $attachment->ID );
				$this->av_title = get_the_title( $this->special_content );
				$this->av_metadata = get_post_meta( $this->special_content, '_wp_attachment_metadata', true );
				$this->av_thumb = $this->_get_av_thumbnail_id( $this->special_content );
				$this->remaining_content = $current_page;
			}
		}

		// Content was successfully split
		if ( '' !== $this->special_content ) {
			$this->has_special = true;
		}

		// No split, so all content is remaining
		if ( false === $this->has_special ) {
			$this->remaining_content = $content;
		}
	}

	/**
	 * Render the special content for Audio and Video posts.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data     An array of data to feed to the render functions.
	 * @param  string    $format   The post format.
	 * @return string              The rendered content or an empty string.
	 */
	public function render_special_content( $data = array(), $format = '' ) {
		if ( empty( $data ) ) {
			$data = $this->compile_av_data();
		}

		if ( '' === $format ) {
			$format = $this->post_format;
		}

		// Unpack
		$defaults = array(
			'title'   => '',
			'content' => '',
			'meta'    => array(),
			'thumb'   => 0
		);
		wp_parse_args( $data, $defaults );

		$shortcodes = ( 'audio' === $format ) ? $this->audio_shortcodes : $this->video_shortcodes;

		// ID
		if ( is_int( $data['content'] ) ) {
			$url = wp_get_attachment_url( $data['content'] );
			if ( $url ) {
				return call_user_func_array( apply_filters( 'wp_' . $format . '_shortcode_handler', 'wp_' . $format . '_shortcode' ), array( array( 'src' => $url ) ) );
			}
		}

		// URL
		if ( 0 === stripos( $data['content'], 'http' ) ) {
			// Try to generate the embed markup from the URL. Includes oEmbeds.
			$output = $GLOBALS['wp_embed']->shortcode( array(), $data['content'] );
			if ( $this->_shortcode_in_array( $output, $shortcodes ) ) {
				return do_shortcode( $output );
			}

			// If that doesn't work, see if it has given us a link.
			if ( 0 === stripos( $output, 'http' ) ) {
				return sprintf( '<a class="wp-post-format-link-%1$s" href="%2$s">%3$s</a>', $format, esc_url( $output ), esc_html( $output ) );
			}

			// If we made it to here, it's already the full embed code, so return it.
			return $output;
		}

		// Shortcode
		if ( $this->_shortcode_in_array( $data['content'], $shortcodes ) ) {
			return do_shortcode( $data['content'] );
		}

		return '';
	}

	/**
	 * Get an attachment id from the src attribute of an audio or video shortcode
	 *
	 * @since  1.0.
	 *
	 * @param  string      $content    The content to extract the ID from.
	 * @return bool|int                ID for the content.
	 */
	private function _get_av_shortcode_id( $content ) {
		$id = 0;

		// Get shortcode atts
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", $content, $matches );
		$atts = shortcode_parse_atts( $matches[3] );

		// Compatible attributes
		$source_list = array( 'src', 'mp3', 'm4a', 'ogg', 'wav' );

		// Check for existing compatible shortcode att
		$existing_sources = array_intersect( $source_list, array_keys( $atts ) );
		if ( empty( $existing_sources ) ) {
			return $id;
		}

		// Get the first existing source
		$src = $atts[array_shift( $existing_sources )];

		// Get the id
		$id = $this->_get_attachment_id_from_url( $src );

		return $id;
	}

	/**
	 * Get the thumbnail ID for an audio or video post.
	 *
	 * Determine which, if any, image should be used as the thumbnail. Media files can now have
	 * featured images associated with them, so if one exists, that will be used first. If not,
	 * we fall back on the featured image of the post.
	 *
	 * @since  1.0.
	 *
	 * @param  int    $content_id    The special content.
	 * @param  int    $post_id       The post ID.
	 * @return int                   The thumbnail ID.
	 */
	private function _get_av_thumbnail_id( $content_id = 0, $post_id = 0 ) {
		if ( 0 === $post_id )
			$post_id = $this->post_id;

		// If special content isn't an ID, look for featured image on post.
		if ( 0 === $content_id || ! is_int( $content_id ) ) {
			return get_post_thumbnail_id( $post_id );
		}

		// See if the special content attachment has a featured image. If not, fall back on post.
		if ( '' !== $attachment_id = get_post_thumbnail_id( $content_id ) ) {
			return $attachment_id;
		} else {
			return get_post_thumbnail_id( $post_id );
		}
	}

	/**
	 * Filters the title of Audio posts
	 *
	 * Looks for the stored av_title value, and replaces the post title with it if it exists.
	 * Then it runs through the other procedural code that the normal get_the_title function uses.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $title    The post title.
	 * @param  int       $id       The post ID.
	 * @return mixed               The modified title.
	 */
	public function audio_title_filter( $title, $id ) {
		// If there is a special AV title use it, and then go through normal get_the_title procedures.
		global $post;
		if ( '' !== $this->av_title && ! is_admin() ) {
			$title = $this->av_title;
			if ( ! empty( $post->post_password ) ) {
				$protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s', 'collections' ) );
				$title = sprintf( $protected_title_format, $title );
			} else if ( isset( $post->post_status ) && 'private' == $post->post_status ) {
				$private_title_format = apply_filters( 'private_title_format', __( 'Private: %s', 'collections' ) );
				$title = sprintf( $private_title_format, $title );
			}
		}

		return $title;
	}

	/**
	 * Compare a URL to WP's embed patterns and return true for a match.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $url    The URL to test for embed patterns.
	 * @return bool              True if there is a match.
	 */
	private function _check_embed_handlers( $url ) {
		// Get embed patterns
		require_once( ABSPATH . WPINC . '/class-oembed.php' );
		$oembed = _wp_oembed_get_object();
		$patterns = (array) $oembed->providers;

		// Test URL against patterns.
		foreach ( $patterns as $pattern => $attr ) {
			// Turn the asterisk-type provider URLs into regex. Straight out of get_html() in wp-includes/class-oembed.php
			if ( ! $attr[1] ) {
				$pattern = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $pattern ), '#' ) ) . '#i';
				$pattern = preg_replace( '|^#http\\\://|', '#https?\://', $pattern );
			}

			// Test the pattern.
			if ( preg_match( $pattern, $url ) )
				return true;
		}

		return false;
	}

}
endif;

if ( ! function_exists( 'collections_the_audio_title_filter' ) ) :
/**
 * Filter: For audio posts, replace the post title with the audio file metadata title,
 * if it's available.
 *
 * @since  1.0.
 *
 * @param  string    $title    The post title.
 * @param  int       $id       The post ID.
 * @return mixed               The modified title.
 */
function collections_the_audio_title_filter( $title, $id ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return $title;

	if ( 'audio' !== $collections_post->post_format )
		return $title;

	$title = $collections_post->audio_title_filter( $title, $id );

	return $title;
}
endif;

add_filter( 'the_title', 'collections_the_audio_title_filter', 1, 2 );

if ( ! function_exists( 'collections_the_audio_artist' ) ) :
/**
 * Template tag: Render the Artist of the audio file, if it exists.
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The artist.
 */
function collections_the_audio_artist( $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'audio' !== $collections_post->post_format )
		return '';

	$render = '';

	// Pull the artist from the array
	if ( isset( $collections_post->av_metadata['artist'] ) ) {
		$render = esc_html( $collections_post->av_metadata['artist'] );
	}

	// Truncate long artist strings
	$char_limit = 40;
	if ( strlen( $render ) > $char_limit ) {
		$render = substr( $render, 0, $char_limit ) . '&hellip;';
	}

	// Return or echo.
	if ( false === $echo ) {
		return $render;
	} else {
		echo $render;
	}
}
endif;

if ( ! function_exists( 'collections_get_audio_thumbnail_id' ) ) :
/**
 * Template tag: Return the ID of the album art for the audio file, if it exists.
 *
 * @since  1.0.
 *
 * @return int    The thumbnail ID.
 */
function collections_get_audio_thumbnail_id() {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'audio' !== $collections_post->post_format )
		return '';

	if ( true === $collections_post->has_special )
		return $collections_post->av_thumb;

	return get_post_thumbnail_id();
}
endif;

if ( ! function_exists( 'collections_audio_has_embed' ) ) :
/**
 * Template tag: Is the special content an embeddable URL?
 *
 * @since  1.0.
 *
 * @return string    True if the special content is an embeddable URL.
 */
function collections_audio_has_embed() {
	global $collections_post;

	if ( empty( $collections_post ) )
		return false;

	if ( 'audio' !== $collections_post->post_format )
		return false;

	return $collections_post->av_oembed;
}
endif;

if ( ! function_exists( 'collections_video_post_class' ) ) :
/**
 * Filter: Add post classes for every second and third item in the Video post archive.
 *
 * @since  1.0.
 *
 * @param  array     $classes    The array of post classes.
 * @param  string    $class      Unused.
 * @param  int       $post_id    Unused.
 * @return array                 The filtered array of post classes.
 */
function collections_video_post_class( $classes, $class, $post_id ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return $classes;

	if ( 'video' !== $collections_post->post_format )
		return $classes;

	global $wp_query;
	$post_num = $wp_query->current_post + 1;

	if ( 0 === $post_num % 2 ) {
		$classes[] = 'collections-video-2';
	}

	if ( 0 === $post_num % 3 ) {
		$classes[] = 'collections-video-3';
	}

	return $classes;
}
endif;

add_filter( 'post_class', 'collections_video_post_class', 10, 3 );