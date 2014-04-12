<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_Photo' ) ) :
/**
 * Class Collections_Photo
 *
 * A class for grabbing and handling special post format content in Image and Gallery posts.
 *
 * @since 1.0.
 */
class Collections_Photo extends Collections_Content {
	/**
	 * Additional vars from parent class:
	 * $post_id, $post_format, $special_content, $remaining_content, $has_special, $is_paged
	 */

	/**
	 * An array of supported gallery shortcode tags.
	 *
	 * @since 1.0.
	 *
	 * @var   array    An array of supported gallery shortcode tags.
	 */
	var $gallery_shortcodes = array();

	/**
	 * An image caption from the [caption] shortcode.
	 *
	 * @since 1.0.
	 *
	 * @var   string    An image caption from the [caption] shortcode.
	 */
	var $image_caption = '';

	/**
	 * Construct the object by setting up basic vars and then running the split.
	 *
	 * @since  1.0.
	 *
	 * @param  int                  $post_id    The ID used to specify the post.
	 * @return Collections_Photo
	 */
	public function __construct( $post_id = 0 ) {
		// Default to the global post ID if none is given.
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		// Clean the ID
		$this->post_id = $this->_clean_post_id( $post_id );

		// Get the post format
		$this->post_format = $this->_set_post_format( $this->post_id, array( 'image', 'gallery' ) );

		// Detect pagination
		$this->_set_paged();

		// Gallery shortcode tags
		$this->gallery_shortcodes = apply_filters( 'collections_gallery_shortcodes', array( 'gallery' ) );

		// Populate the object
		if ( 0 !== $this->post_id && '' !== $this->post_format ) {
			// Populate the class properties
			$this->_split_content( $this->post_id, $this->post_format );
		}
	}

	/**
	 * Compile Photo data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The array of data to use for rendering, etc.
	 */
	public function compile_photo_data() {
		$data = array(
			'content'       => $this->special_content,
			'image_caption' => $this->image_caption
		);

		return $data;
	}

	/**
	 * Content split wrapper function. Determines which split function to use based on post format.
	 *
	 * @since  1.0.
	 *
	 * @param  int        $post_id    The post ID.
	 * @param  string     $format     The post format.
	 * @return void
	 */
	private function _split_content( $post_id, $format ) {
		if ( 'image' === $format ) {
			$this->_split_image_content( $post_id );
		} else if ( 'gallery' === $format ) {
			$this->_split_gallery_content( $post_id );
		}
	}

	/**
	 * Content split function for Image posts.
	 *
	 * @since  1.0.
	 *
	 * @param  int     $post_id    The post ID.
	 * @return void
	 */
	private function _split_image_content( $post_id ) {
		// If is_paged, use the first page of the content to derive the special content.
		global $pages;
		$current_page = get_the_content( null, false, $post_id );
		$content = ( true === $this->is_paged ) ? $pages[0] : $current_page;

		// 1. Featured Image (id)
		if ( '' === $this->special_content ) {
			if ( '' !== get_the_post_thumbnail( $post_id ) ) {
				$this->special_content = absint( get_post_thumbnail_id( $post_id ) );
				$this->remaining_content = $current_page;
				$attachment = get_post( $this->special_content );
				$this->image_caption = $attachment->post_excerpt;
			}
		}

		// 2. <img> on first line (id or url)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_line_img( $content );
			if ( isset( $split['img'] ) ) {
				// See if an id was returned
				if ( is_int( $split['img'] ) ) {
					$this->special_content = $split['img'];
				} else {
					// Try to derive an id from the URL
					if ( 0 !== $attachment_id = $this->_get_attachment_id_from_url( $split['url'] ) ) {
						$this->special_content = $attachment_id;
					} else {
						$this->special_content = $split['img'];
					}
				}
				// Caption and remaining content
				if ( isset( $split['caption'] ) )
					$this->image_caption = $split['caption'];

				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 3. Plain URL or <a> on first line (id or url)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_line_url( $content );
			if ( ! empty( $split ) ) {
				// Try to derive an id from the URL
				$attachment_id = $this->_get_attachment_id_from_url( $split['url'] );
				if ( 0 !== $attachment_id ) {
					$this->special_content = $attachment_id;
					$attachment = get_post( $this->special_content );
					$this->image_caption = $attachment->post_excerpt;
				} else {
					$this->special_content = $split['url'];
				}
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 4. First image attachment (id)
		if ( '' === $this->special_content ) {
			$attachment = $this->_get_matching_attachments( $post_id, 'image', 1 );
			$attachment = array_shift( $attachment );
			if ( $attachment ) {
				$this->special_content = $attachment->ID;
				$this->remaining_content = $current_page;
				$attachment = get_post( $this->special_content );
				$this->image_caption = $attachment->post_excerpt;
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
	 * Content split function for Gallery posts.
	 *
	 * @since  1.0.
	 *
	 * @param  int     $post_id    The post ID.
	 * @return void
	 */
	private function _split_gallery_content( $post_id ) {
		// If is_paged, use the first page of the content to derive the special content.
		global $pages;
		$current_page = get_the_content( null, false, $post_id );
		$content = ( true === $this->is_paged ) ? $pages[0] : $current_page;

		// 1. Gallery shortcode (shortcode string)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_line_shortcode( $this->gallery_shortcodes, $content );
			if ( ! empty( $split ) ) {
				$this->special_content = $split['tag'];
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 2. Image attachments (array of ids)
		if ( '' === $this->special_content ) {
			$attachments = $this->_get_matching_attachments( $post_id, 'image' );
			// Collect the ids of each matching attachment into an array
			if ( ! empty( $attachments ) ) {
				$ids = array_keys( wp_list_pluck( (array) $attachments, 'id' ) );
				$this->special_content = $ids;
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
	 * Get the src of an <img> tag in the first line of content.
	 *
	 * Split off the first line of content and determine if it has an <img> tag.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The content to split.
	 * @return array                 The separated pieces of content.
	 */
	private function _get_first_line_img( $content ) {
		$output = array();

		if ( empty( $content ) )
			return $output;

		// Isolate the first line
		$lines = explode( "\n", trim( $content ) );
		$line = trim( array_shift( $lines ) );

		// Check for an img tag
		if ( false !== stripos( $line, '<img ' ) ) {

			// Check for an attachment id
			if ( 1 === preg_match( '#wp\-image\-(?<id>\d+)#', $line, $matches ) ) {
				$output['img'] = absint( $matches['id'] );
			} else if ( 1 === preg_match( '#src=[\'"](?<url>[^\'"]+)[\'"]#', $line, $matches ) ) {
				$output['img'] = esc_url_raw( $matches['url'] );
			}

			// Check for a caption
			if ( has_shortcode( $line, 'caption' ) ) {
				$pattern = get_shortcode_regex();
				preg_match( "/$pattern/", $line, $matches );
				$output['caption'] = trim( preg_replace( '#<img [^>]+>#', '', $matches[5] ) );
			}

			// Set the remaining content
			$output['content'] = trim( join( "\n", $lines ) );

		}

		return $output;
	}

	/**
	 * Wrapper function for rendering special content. Determines with render function to use
	 * based on the post format.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data      An array of data to feed to the render functions.
	 * @param  string    $format    The post format.
	 * @param  string    $size      The image size, if applicable.
	 * @return string               The rendered content or an empty string.
	 */
	public function render_special_content( $data = array(), $format = '', $size = 'collections-full-width' ) {
		if ( empty( $data ) )
			$data = $this->compile_photo_data();

		if ( '' === $format )
			$format = $this->post_format;

		// Image
		if ( 'image' === $format ) {
			return $this->_render_image_special_content( $data, $size );
		}

		// Gallery
		if ( 'gallery' === $format ) {
			return $this->_render_gallery_special_content( $data );
		}

		return '';
	}

	/**
	 * Render the special content for an Image post.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data    Array of data needed to render special content.
	 * @param  string    $size    The image size, if applicable.
	 * @return string             The rendered content or an empty string.
	 */
	private function _render_image_special_content( $data = array(), $size = 'collections-full-width' ) {
		// Unpack
		$defaults = array(
			'content'       => '',
			'image_caption' => ''
		);
		wp_parse_args( $data, $defaults );

		$output = '';

		// ID
		if ( is_int( $data['content'] ) ) {
			$output .= wp_get_attachment_image( $data['content'], $size );
		}

		// URL
		if ( 0 === stripos( $data['content'], 'http' ) ) {
			$alt = ' alt="' . the_title_attribute( array( 'echo' => false ) ) . '"';
			$url = $data['content'];
			$output .= '<img class="external-image" src="' . esc_url( $url ) . '"' . $alt . ' />';
		}

		return $output;
	}

	/**
	 * Render the special content for a Gallery post.
	 *
	 * Introduced in 1.0.6, a user can set a gallery shortcode attribute of showgrid="true" to have
	 * the special content render as the WordPress default grid of thumbnails, rather than the slideshow.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data    Array of data needed to render special content.
	 * @return string             The rendered content or an empty string.
	 */
	private function _render_gallery_special_content( $data = array() ) {
		// Unpack
		$defaults = array(
			'content' => ''
		);
		wp_parse_args( $data, $defaults );

		$image_ids = array();
		$use_collections_slider = true;

		// Array of ids
		if ( is_array( $data['content'] ) ) {
			$image_ids = $data['content'];
		}
		// Shortcode
		else if ( $this->_shortcode_in_array( $data['content'], $this->gallery_shortcodes ) ) {
			// Check for optional attributes
			$pattern = get_shortcode_regex();
			preg_match( "/$pattern/s", $data['content'], $matches );
			$atts = shortcode_parse_atts( $matches[3] );

			// showgrid
			if ( isset( $atts['showgrid'] ) && true == $atts['showgrid'] ) {
				$use_collections_slider = false;
			}

			// Jetpack / wpcom
			if ( ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'is_module_active' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) || ( defined( 'IS_WPCOM' ) && IS_WPCOM ) ) {
				if ( isset( $atts['type'] ) && in_array( $atts['type'], array( 'rectangular', 'square', 'circle' ) ) ) {
					$use_collections_slider = false;
				}
			}

			// Render using default shortcode function instead
			if ( false === $use_collections_slider ) {
				return do_shortcode( $data['content'] );
			}

			// Move ahead with Collections slider
			$image_ids = $this->_get_gallery_shortcode_ids( $data['content'] );
		}

		// Build slideshow markup
		if ( ! empty( $image_ids ) ) {
			$output = '<ul class="collections-gallery-slideshow deactivated">';

			// List items
			foreach ( $image_ids as $id ) {
				$attachment = get_post( $id );
				$output .= '<li><figure class="image-frame"><span class="theme-shadow">';
				$output .= wp_get_attachment_image( $id, 'collections-full-width' );
				$output .= '</span></figure></li>';
			}

			$output .= '</ul>';

			return $output;
		}

		return '';
	}

	/**
	 * Get the image URL for the post.
	 *
	 * @since  1.0.
	 *
	 * @param     array     $data    Array of data needed to render special content.
	 * @param     string    $size    The size of the image to display.
	 * @return    string             The URL or an empty string.
	 */
	public function get_the_image_url( $data = array(), $size = 'collections-full-width' ) {
		if ( empty( $data ) )
			$data = $this->compile_photo_data();

		// Unpack
		$defaults = array(
			'content' => '',
		);
		wp_parse_args( $data, $defaults );

		// Set the default
		$url = '';

		// ID
		if ( is_int( $data['content'] ) ) {
			$src = wp_get_attachment_image_src( $data['content'], $size );
			$url = $src[0];
		}

		// URL
		if ( 0 === stripos( $data['content'], 'http' ) ) {
			$url = $data['content'];
		}

		return $url;
	}

	/**
	 * Determine a "cover" image for a gallery, and return the attachment id.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The special content.
	 * @return int               The attachment id.
	 */
	public function get_gallery_cover_id( $data = array() ) {
		if ( empty( $data ) )
			$data = $this->compile_photo_data();

		if ( 'gallery' !== $this->post_format )
			return 0;

		$id = $this->post_id;

		// Featured image
		if ( '' !== get_the_post_thumbnail( $id ) ) {
			return absint( get_post_thumbnail_id( $id ) );
		}

		// Array of ids
		if ( is_array( $data['content'] ) ) {
			return absint( array_shift( $data['content'] ) );
		}

		// Shortcode
		if ( $this->_shortcode_in_array( $data['content'], $this->gallery_shortcodes ) ) {
			// Get array of ids
			$ids = $this->_get_gallery_shortcode_ids( $data['content'] );

			// Grab the first id
			if ( ! empty( $ids ) ) {
				return absint( array_shift( $ids ) );
			}
		}

		return 0;
	}

	/**
	 * Determine a "cover" image URL for a gallery.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data    The special content.
	 * @param  string    $size    The size of the image to display.
	 * @return string             The cover image URL.
	 */
	public function get_the_gallery_cover_url( $data = array(), $size = 'collections-full-width' ) {
		if ( empty( $data ) )
			$data = $this->compile_photo_data();

		// Unpack
		$defaults = array(
			'content' => '',
		);
		wp_parse_args( $data, $defaults );

		$id  = $this->get_gallery_cover_id( $data );
		$src = wp_get_attachment_image_src( $id, $size );
		$url = $src[ 0 ];

		return $url;
	}

	/**
	 * Get an array of image attachment ids from a gallery shortcode
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The shortcode to parse for ids.
	 * @return array                 An array of ids.
	 */
	private function _get_gallery_shortcode_ids( $content ) {
		$ids = array();

		// Get shortcode atts
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", $content, $matches );
		$atts = shortcode_parse_atts( $matches[3] );

		// Normalize relevant shortcode atts
		if ( isset( $atts['ids'] ) ) {
			$atts['include'] = $atts['ids'];
		}

		// Look for the ids attribute
		if ( isset( $atts['include'] ) ) {
			$ids = explode( ',', $atts['include'] );
			$ids = array_map( 'trim', $ids );
		}
		// If no ids attribute, grab all the images attached to the post
		else {
			$attachments = $this->_get_matching_attachments( $this->post_id, 'image' );
			// Collect the ids of each matching attachment into an array
			if ( ! empty( $attachments ) ) {
				$ids = array_keys( wp_list_pluck( (array) $attachments, 'id' ) );
			}
		}

		return $ids;
	}
}
endif;

if ( ! function_exists( 'collections_the_image_sized' ) ) :
/**
 * Template tag: Render a specific image size from the Image special content.
 *
 * @since  1.0.
 *
 * @param  string    $size    The image size to load.
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The rendered content.
 */
function collections_the_image_sized( $size = 'medium', $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'image' !== $collections_post->post_format )
		return '';

	// Get the image.
	$render = $collections_post->render_special_content( array(), '', $size );

	// Display the image.
	if ( true === $echo )
		echo $render;
	else
		return $render;
}
endif;

if ( ! function_exists( 'collections_the_gallery_cover' ) ) :
/**
 * Template tag: Render the cover image of a gallery.
 *
 * @since  1.0.
 *
 * @param  string    $size    The image size to load.
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The rendered content.
 */
function collections_the_gallery_cover( $size = 'medium', $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'gallery' !== $collections_post->post_format )
		return '';

	// Get the ID of the image that represents th gallery.
	$cover_id = $collections_post->get_gallery_cover_id();

	// Display the image.
	if ( true === $echo )
		echo wp_get_attachment_image( $cover_id, $size );
	else
		return wp_get_attachment_image( $cover_id, $size );
}
endif;

if ( ! function_exists( 'collections_get_the_image_url' ) ) :
/**
 * Render the image URL for the post.
 *
 * @since  1.0.
 *
 * @param  string    $size    The size of the image to display.
 * @return string             The URL or an empty string.
 */
function collections_get_the_image_url( $size = 'collections-full-width' ) {
	global $collections_post;
	return $collections_post->get_the_image_url( array(), $size );
}
endif;

if ( ! function_exists( 'collections_get_the_gallery_cover_url' ) ) :
/**
 * Render the image URL for the gallery cover image.
 *
 * @since  1.0.
 *
 * @param  string    $size    The size of the image to display.
 * @return string             The URL or an empty string.
 */
function collections_get_the_gallery_cover_url( $size = 'collections-full-width' ) {
	global $collections_post;
	return $collections_post->get_the_gallery_cover_url( array(), $size );
}
endif;