<?php
/**
 * @package Collections
 */

if ( ! class_exists( 'Collections_Homepage_Query' ) ) :
/**
 * Class Collections_Homepage_Query
 *
 * Gets the post data needed for the homepage template.
 *
 * @since 1.0.
 */
class Collections_Homepage_Query {
	/**
	 * Holds the queries that are made to generate the data for the homepage.
	 *
	 * @since 1.0.
	 *
	 * @var   array    Array of WP_Query objects.
	 */
	public $queries = array();

	/**
	 * Holds the queries, but split into "left" and "right" areas for determine which column each goes in.
	 *
	 * @since 1.0.
	 *
	 * @var   array    Array of queries broken into "left" and "right" arrays.
	 */
	public $split_queries = array();

	/**
	 * Construct the object by getting the posts.
	 *
	 * @since  1.0.
	 *
	 * @return Collections_Homepage_Query
	 */
	public function __construct() {
		// Query for all of the posts
		$this->queries = $this->get_posts();

		$this->split_queries = $this->split_queries( $this->queries );
	}

	/**
	 * Cycle through the post formats and query for the first post for each format.
	 *
	 * @since  1.0.
	 *
	 * @return array    Array of WP_Query objects.
	 */
	public function get_posts() {
		$formats = collections_get_types( array(
			'articles', 'photos', 'links', 'audios', 'videos', 'quotes',
		) );

		$queries = array();

		// Foreach post format, query for the latest post. For articles, use a special function.
		foreach ( $formats as $key => $value ) {
			if ( 'articles' === $key ) {
				$queries[ $key ] = $this->article_format_query();
			} else {
				$queries[ $key ] = $this->post_format_query( $value['formats'] );
			}
		}

		return $queries;
	}

	/**
	 * Get the latest "article" post.
	 *
	 * Articles, in this context, refer to a post of standard, chat, aside, or status post format.
	 *
	 * @since  1.0.
	 *
	 * @return WP_Query    The query object.
	 */
	public function article_format_query() {
		// All formats that are not an "article" type
		$not_these_formats = array(
			'post-format-image',
			'post-format-gallery',
			'post-format-link',
			'post-format-quote',
			'post-format-video',
			'post-format-audio'
		);

		// Prep the query args
		$args = array(
			'posts_per_page'      => 1,
			'status'              => 'publish',
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'desc',
			'ignore_sticky_posts' => 1,
			'tax_query'           => array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => $not_these_formats,
					'operator' => 'NOT IN'
				),
			),
		);

		return new WP_Query( $args );
	}

	/**
	 * Get the latest post in the format.
	 *
	 * @since  1.0.
	 *
	 * @param  array       $formats    The formats to query for.
	 * @return WP_Query                WP_Query object for the query.
	 */
	public function post_format_query( $formats ) {
		if ( empty( $formats ) ) {
			return new WP_Query();
		}

		// Prepend each value with the 'post-format-' string
		foreach ( $formats as &$format ) {
			$format = 'post-format-' . $format;
		}

		// Prepare the query args
		$args = array(
			'posts_per_page'      => 1,
			'status'              => 'publish',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => 1,
			'tax_query'           => array(
				array(
					'field'    => 'slug',
					'terms'    => $formats,
					'taxonomy' => 'post_format',
				),
			),
		);

		return new WP_Query( $args );
	}

	/**
	 * Split the array of queries into two sub arrays, "left" and "right".
	 *
	 * This function first prunes out any queries that are empty. It then places the first query into the left array,
	 * the second in the right array and so on.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $queries    The post format queries.
	 * @return array                The post format queries broken into "left" and "right" arrays.
	 */
	public function split_queries( $queries ) {
		$split_queries = array(
			'left'  => array(),
			'right' => array(),
		);

		$current_side = 'left';

		foreach ( $queries as $key => $query ) {
			if ( $query->have_posts() ) {
				$split_queries[ $current_side ][ $key ] = $query;
				$current_side = ( 'left' === $current_side ) ? 'right' : 'left';
			}
		}

		return $split_queries;
	}

	/**
	 * Get a specific query.
	 *
	 * @since  1.0.
	 *
	 * @param  string      $type    The format type to get
	 * @return WP_Query             WP_Query for the format.
	 */
	public function get_query( $type ) {
		return $this->queries[ $type ];
	}
}
endif;