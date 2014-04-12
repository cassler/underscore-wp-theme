<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_Link' ) ) :
/**
 * Class Collections_Link
 *
 * A class for grabbing and handling special post format content in Link posts.
 *
 * @since 1.0.
 */
class Collections_Link extends Collections_Content {

	/**
	 * Additional vars from parent class:
	 * $post_id, $post_format, $special_content, $remaining_content, $has_special, $is_paged
	 */

	/**
	 * The domain of the special content URL.
	 *
	 * @since 1.0.
	 *
	 * @var   array    The domain of the special content URL.
	 */
	var $link_domain = '';

	/**
	 * Construct the object by setting up basic vars and then running the split.
	 *
	 * @since  1.0.
	 *
	 * @param  int    $post_id    The ID used to specify the post.
	 * @return Collections_Link
	 */
	public function __construct( $post_id = 0 ) {
		// Default to the global post ID if none is given.
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		// Clean the ID
		$this->post_id = $this->_clean_post_id( $post_id );

		// Get the post format
		$this->post_format = $this->_set_post_format( $this->post_id, array( 'link' ) );

		// Detect pagination
		$this->_set_paged();

		// Populate the object
		if ( 0 !== $this->post_id && '' !== $this->post_format ) {
			// Populate the class properties
			$this->_split_content( $this->post_id, $this->post_format );
		}
	}

	/**
	 * Compile Link data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The array of data to use for rendering, etc.
	 */
	public function compile_link_data() {
		$data = array(
			'content' => $this->special_content,
			'domain'  => $this->link_domain
		);

		return $data;
	}

	/**
	 * Content split function for Link posts.
	 *
	 * Split content is stored in instance variables for later use.
	 *
	 * @since 1.0.
	 *
	 * @param  int       $post_id    The post ID.
	 * @param  string    $format     The post format.
	 * @return void
	 */
	private function _split_content( $post_id, $format ) {
		// If is_paged, use the whole content block to derive the special content. (This is different than other content grabbers).
		global $pages;
		$current_page = get_the_content( null, false, $post_id );
		$content = ( true === $this->is_paged ) ? implode( "\n\n", $pages ) : $current_page;

		// 1. Plain URL on first line (url)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_line_url( $content );
			if ( ! empty( $split ) ) {
				$this->special_content = $split['url'];
				$this->remaining_content = ( true === $this->is_paged ) ? $current_page : $split['content'];
			}
		}

		// 2. First href within the HTML (url)
		if ( '' === $this->special_content ) {
			$split = $this->_get_first_href( $content );
			if ( ! empty( $split ) ) {
				$this->special_content = $split['url'];
				$this->remaining_content = $current_page;
			}
		}

		// Content was successfully split
		if ( '' !== $this->special_content ) {
			$this->has_special = true;
			$this->link_domain = $this->_extract_url_domain( $this->special_content );
		}

		// No split, so all content is remaining
		if ( false === $this->has_special ) {
			$this->remaining_content = $content;
		}
	}

	/**
	 * Render the special content for Link posts.
	 *
	 * @since  1.0.
	 *
	 * @param  array     $data     An array of data to feed to the render functions.
	 * @param  string    $format   The post format.
	 * @return string              The rendered content or an empty string.
	 */
	public function render_special_content( $data = array(), $format = '' ) {
		if ( empty( $data ) )
			$data = $this->compile_link_data();

		// Unpack
		$defaults = array(
			'content' => '',
			'domain'  => ''
		);
		wp_parse_args( $data, $defaults );

		// URL
		if ( 0 === stripos( $data['content'], 'http' ) ) {
			return esc_url( $data['content'] );
		}

		return '';
	}

	/**
	 * Extract the domain/hostname part of a URL.
	 *
	 * @since  1.0.
	 *
	 * @param  string          $url      The URL to parse.
	 * @return mixed|string              The host part of the URL.
	 */
	private function _extract_url_domain( $url = '' ) {
		$domain = '';

		if ( '' === $url )
			return $domain;

		$domain = parse_url( $url, PHP_URL_HOST );

		return $domain;
	}

} // end class

/**
 * Template tag: Render the Link content to display on the Link archive.
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The content.
 */
function collections_the_link_archive_content( $echo = true ) {
	global $collections_post;

	if ( empty( $collections_post ) )
		return '';

	if ( 'link' !== $collections_post->post_format )
		return '';

	// The title
	$title = ( get_the_title() ) ? '<h3 class="collections-link-title"><a href="' . esc_url( get_permalink() ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . "</a></h3>\n\n" : '';

	// The link domain
	$link_domain = '';
	$orig_link_domain = $collections_post->link_domain;
	if ( '' !== $orig_link_domain ) {
		$trunc_link_domain = $orig_link_domain;
		// Truncate long domain strings
		$char_limit = 17;
		if ( strlen( $trunc_link_domain ) > $char_limit ) {
			$trunc_link_domain = substr( $trunc_link_domain, 0, $char_limit ) . '&hellip;';
		}
		$link_domain = '<a class="collections-link-domain" title="' . esc_attr( $orig_link_domain ) . '" href="' . esc_url_raw( $collections_post->special_content ) . '">' . $trunc_link_domain . '</a>';
	}

	$render = wpautop( $title . $link_domain );

	// Return or echo.
	if ( false === $echo )
		return $render;
	else
		echo $render;
}
endif;