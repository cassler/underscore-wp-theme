<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_Quote' ) ) :
/**
 * Class Collections_Quote
 *
 * A class for grabbing and handling special post format content in Link posts.
 *
 * @since 1.0.
 */
class Collections_Quote extends Collections_Content {
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
	var $quote_attribution = '';

	/**
	 * Construct the object by setting up basic vars and then running the split.
	 *
	 * @since  1.0.
	 *
	 * @param  int    $post_id    The ID used to specify the post.
	 * @return Collections_Quote
	 */
	public function __construct( $post_id = 0 ) {
		// Default to the global post ID if none is given.
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		// Clean the ID
		$this->post_id = $this->_clean_post_id( $post_id );

		// Get the post format
		$this->post_format = $this->_set_post_format( $this->post_id, array( 'quote' ) );

		// Detect pagination
		$this->_set_paged();

		// Populate the object
		if ( 0 !== $this->post_id && '' !== $this->post_format ) {
			// Populate the class properties
			$this->_split_content( $this->post_id, $this->post_format );
		}
	}

	/**
	 * Compile Quote data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The array of data to use for rendering, etc.
	 */
	public function compile_quote_data() {
		$data = array(
			'content'           => $this->special_content,
			'quote_attribution' => $this->quote_attribution
		);

		return $data;
	}

	/**
	 * Content split function for Quote posts.
	 *
	 * @since  1.0.
	 *
	 * @param  int        $post_id    The post ID.
	 * @param  string     $format     The post format.
	 * @return void
	 */
	private function _split_content( $post_id, $format ) {
		// If is_paged, use the first page of the content to derive the special content.
		global $pages;
		$current_page = get_the_content( null, false, $post_id );
		$content = ( true === $this->is_paged ) ? $pages[0] : $current_page;

		// Get the attribution
		if ( '' === $this->quote_attribution ) {
			$this->quote_attribution = $this->_get_quote_attribution( $content );
		}

		// Get the blockquote
		if ( '' === $this->special_content ) {
			$split = $this->_get_quote_content( $content );
			if ( ! empty( $split ) ) {
				$this->special_content = $split['quote'];
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
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
	 * Find and return a quote attribution from the content.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The content to search.
	 * @return string                The quote attribution.
	 */
	private function _get_quote_attribution( $content ) {
		$output = '';

		if ( empty( $content ) )
			return $output;

		// Look for an attribution as inserted via the Styles dropdown.
		$regex = '/<cite[^>]*collections-quote-attribute[^>]*>(.+?)<\/cite>/is';

		if ( preg_match( $regex, $content, $matches ) )
			$output = $matches[1];

		return $output;
	}

	/**
	 * Get a blockquote from the beginning of the content.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The content to split.
	 * @return array                 The separated pieces of content.
	 */
	private function _get_quote_content( $content ) {
		$output = array();

		if ( empty( $content ) )
			return $output;

		// Blockquote regex
		$regex = '/^<blockquote[^>]*>(.+?)<\/blockquote>/is';

		// Split the beginning blockquote off from the rest of the content.
		if ( preg_match( $regex, $content, $matches ) ) {
			$output['quote'] = $matches[1];
			$output['content'] = preg_replace( $regex, '', $content );
		}

		return $output;
	}

	/**
	 * Render the special content for Quote posts.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data      An array of data to feed to the render functions.
	 * @param  string    $format    The post format.
	 * @return string               The rendered content or an empty string.
	 */
	public function render_special_content( $data = array(), $format = '' ) {
		if ( empty( $data ) )
			$data = $this->compile_quote_data();

		if ( '' === $format )
			$format = $this->post_format;

		// Unpack
		$defaults = array(
			'content'           => '',
			'quote_attribution' => ''
		);
		wp_parse_args( $data, $defaults );

		$output = $data['content'];

		return apply_filters( 'collections_quote_content', $output );
	}
}
endif;

if ( ! function_exists( 'collections_quote_content_filter' ) ) :
/**
 * Filter: Apply selected the_content filters to the Quote special content.
 *
 * @since  1.0.
 *
 * @param  string    $output    The content to filter.
 * @return string               The filtered content.
 */
function collections_quote_content_filter( $output ) {
	return wpautop( $output );
}
endif;

add_filter( 'collections_quote_content', 'collections_quote_content_filter' );

if ( ! function_exists( 'collections_the_quote_archive_content' ) ) :
/**
 * Template tag: Render the Quote content to display on the Quote archive.
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The content.
 */
function collections_the_quote_archive_content( $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'quote' !== $collections_post->post_format )
		return '';

	// The title
	$title = ( get_the_title() ) ? '<h3 class="collections-quote-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . "</a></h3>\n\n" : '';

	// The quote attribution
	$quote_attribute = ( '' !== $collections_post->quote_attribution ) ? '<cite class="collections-quote-attribute"><a href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . $collections_post->quote_attribution . '</a></cite>' : '';

	$render = wpautop( $title . $quote_attribute );

	// Return or echo.
	if ( false === $echo )
		return $render;
	else
		echo $render;
}
endif;