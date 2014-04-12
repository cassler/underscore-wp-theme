<?php
/**
 * @package Collections
 */

if ( ! function_exists( 'collections_get_types' ) ) :
/**
 * Returns the Collections collection types that are mapped to post formats.
 *
 * @since  1.0.
 *
 * @param  array    The order of array keys.
 * @return array    Array of collections mapped to post formats.
 */
function collections_get_types( $order = array() ) {
	// The formats are added in the default order which is the sidebar order on the archive page
	$formats = array(
		'articles' => array(
			'label'   => __( 'Articles', 'collections' ),
			'slug'    => 'aside',
			'formats' => array(
				'aside',
				'standard',
				'chat',
				'status'
			),
		),
		'links'    => array(
			'label'   => __( 'Links', 'collections' ),
			'slug'    => 'link',
			'formats' => array(
				'link',
			),
		),
		'photos'   => array(
			'label'   => __( 'Photos', 'collections' ),
			'slug'    => 'image',
			'formats' => array(
				'image',
				'gallery',
			),
		),
		'audios'   => array(
			'label'   => __( 'Audio', 'collections' ),
			'slug'    => 'audio',
			'formats' => array(
				'audio',
			),
		),
		'videos'   => array(
			'label'   => __( 'Videos', 'collections' ),
			'slug'    => 'video',
			'formats' => array(
				'video',
			),
		),
		'quotes'   => array(
			'label'   => __( 'Quotes', 'collections' ),
			'slug'    => 'quote',
			'formats' => array(
				'quote',
			),
		),
	);

	// Set up the dummy array for the sorting
	$final_formats = array();

	// If there is a sort order defined, use it
	if ( ! empty( $order ) ) {
		foreach ( $order as $key ) {
			$final_formats[ $key ] = $formats[ $key ];
		}
	} else {
		$final_formats = $formats;
	}

	return $final_formats;
}
endif;

if ( ! function_exists( 'collections_is_article' ) ) :
/**
 * Conditional: is the post an "Article"?
 *
 * Is the post an 'article'? We are defining 'article' as
 * a 'standard' or 'aside' post, or any other post format
 * that Collections doesn't specifically support.
 *
 * @since  1.0.
 *
 * @param  int     $post_id    The id of the post.
 * @return bool                True if the post is an Article.
 */
function collections_is_article( $post_id = null ) {
	$format = get_post_format( $post_id );

	/**
	 * We'll use article as a catch-all for unsupported formats,in addition to 'standard' and 'aside'. So, we specify
	 * which formats _aren't_ in article.
	 */
	$not_these_formats = array(
		'image',
		'gallery',
		'link',
		'quote',
		'video',
		'audio'
	);

	if ( ! in_array( $format, $not_these_formats ) )
		return true;

	return false;
}
endif;

if ( ! function_exists( 'collections_is_photo' ) ) :
/**
 * Conditional: is the post a "Photo"?
 *
 * Is the post a 'photo'? We are defining 'photo' as either an 'image' or 'gallery' post.
 *
 * @since  1.0.
 *
 * @param  int     $post_id    The id of the post.
 * @return bool                True if the post is a Photo.
 */
function collections_is_photo( $post_id = null ) {
	$format = get_post_format( $post_id );

	$these_formats = array(
		'image',
		'gallery'
	);

	if ( in_array( $format, $these_formats ) )
		return true;

	return false;
}
endif;

if ( ! function_exists( 'collections_is_av' ) ) :
/**
 * Conditional: is the post an audio clip or video?
 *
 * Is the post 'av'? We are defining 'av' as either an 'audio' or 'video' post.
 *
 * @since  1.0.
 *
 * @param  int     $post_id    The id of the post.
 * @return bool                True if the post is audio or video.
 */
function collections_is_av( $post_id = null ) {
	$format = get_post_format( $post_id );

	$these_formats = array(
		'audio',
		'video'
	);

	if ( in_array( $format, $these_formats ) )
		return true;

	return false;
}
endif;

if ( ! function_exists( 'collections_query_mod' ) ) :
/**
 * Add post format parameters to a loop query
 *
 * @since  1.0.
 *
 * @param  object    $query     The query object.
 * @param  string    $format    The post format.
 * @return void
 */
function collections_query_mod( $query, $format = null ) {
	if ( ! $format ) {
		$format = get_post_format();

		if ( false === $format || collections_is_article() )
			$format = 'article';

		if ( collections_is_photo() )
			$format = 'photo';
	}

	switch ( $format ) {
		default:
			// Set post_format query var
			$query->set( 'post_format', 'post-format-' . $format );
			break;

		case 'article':
			// Standard is not an actual format, so we must instead
			// exclude the other formats we don't want.
			$not_these_formats = array(
				'post-format-image',
				'post-format-gallery',
				'post-format-link',
				'post-format-quote',
				'post-format-video',
				'post-format-audio'
			);

			// Build tax query
			$tax_query = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => $not_these_formats,
					'operator' => 'NOT IN'
				)
			);

			// Zero out the post format query var so it doesn't interfere.
			$query->set( 'post_format', '' );

			$query->set( 'tax_query', $tax_query );
			if ( isset( $query->queried_object->name ) )
				$query->queried_object->name = __( 'Article', 'collections' );
			break;

		case 'photo':
			// Combine image and gallery formats into "photos"
			$photo_formats = array(
				'post-format-image',
				'post-format-gallery'
			);
			$photo_formats = implode( ',', $photo_formats );

			$query->set( 'post_format', $photo_formats );
			if ( isset( $query->queried_object->name ) )
				$query->queried_object->name = __( 'Photo', 'collections' );
			break;
	}
}
endif;

if ( ! function_exists( 'collections_archive_postcount_query' ) ) :
/**
 * Modify the main query for certain post format archives to have a different posts_per_page.
 *
 * @since  1.0.
 *
 * @param  object    $query    The query object to modify.
 * @return void
 */
function collections_archive_postcount_query( $query ) {
	if ( ! $query->is_main_query() || ! $query->is_tax( 'post_format' ) )
		return;

	// Get the posts_per_page from the query first, then settings if not in query.
	$ppp = ( $query->get( 'posts_per_page' ) ) ? absint( $query->get( 'posts_per_page' ) ) : absint( get_option( 'posts_per_page' ) );
	if ( 0 === $ppp ) {
		$ppp = 10;
	}

	$format = $query->get( 'post_format' );

	if ( 'post-format-video' === $format ) {
		$ppp = $ppp * 2;
		if ( 0 !== $ppp % 3 ) {
			$ppp = $ppp + 3 - ( $ppp % 3 );
		}
	}

	// Set the query var
	$query->set( 'posts_per_page', $ppp );
}
endif;

add_action( 'pre_get_posts', 'collections_archive_postcount_query', 10 );

if ( ! function_exists( 'collections_archive_format_query' ) ) :
/**
 * Modify the main query for certain post format archives to include posts from other formats.
 *
 * @since  1.0.
 *
 * @param  object    $query    The query object to modify.
 * @return void
 */
function collections_archive_format_query( $query ) {
	// Never affect admin queries
	if ( is_admin() ) {
		return;
	}

	// Which post format archives do we want to hijack?
	$terms = array(
		'post-format-aside',
		'post-format-image'
	);

	// Only modify if it's the main query and also a post_format archive.
	if ( $query->is_main_query() && $query->is_tax( 'post_format', $terms ) ) {
		$format = $query->get( 'post_format' );

		if ( 'post-format-aside' === $format ) {
			collections_query_mod( $query, 'article' );
		}

		if ( 'post-format-image' === $format ) {
			collections_query_mod( $query, 'photo' );
		}
	}
}
endif;

add_action( 'pre_get_posts', 'collections_archive_format_query', 11 );

if ( ! function_exists( 'collections_pf_archive_url' ) ) :
/**
 * Return the URL of a post format archive
 *
 * @since  1.0.
 *
 * @param  string    $format    The post format.
 * @return string               The URL of the post format archive.
 */
function collections_pf_archive_url( $format = null ) {
	if ( ! $format )
		$format = get_post_format();

	if ( ! $format )
		$format = 'aside';

	return get_post_format_link( $format );
}
endif;

if ( ! function_exists( 'collections_pf_archive_title' ) ) :
/**
 * Return the "pretty" name of a post format
 *
 * @since  1.0.
 *
 * @param  string    $format    The post format.
 * @return string               The URL of the post format archive.
 */
function collections_pf_archive_title( $format = null ) {
	if ( ! $format ) {
		$format = get_post_format();
	}

	// Get the translated strings for the plural forms
	$types = collections_get_types();

	if ( collections_is_article() ) {
		return $types['articles']['label'];
	}

	if ( collections_is_photo() ) {
		return $types['photos']['label'];
	}

	return $types[$format . 's']['label'];
}
endif;

if ( ! function_exists( 'collections_adjacent_post' ) ) :
/**
 * Return the adjacent post to the current that is the same post format.
 *
 * @since  1.0.
 *
 * @param  bool     $previous    (bool) true for previous post. (bool) false for next post.
 * @return array                 Empty array if no post found. Array of post data if post found.
 */
function collections_adjacent_post( $previous = true ) {
	if ( ! $post = get_post() )
		return array();

	if ( ! is_object_in_taxonomy( $post->post_type, 'post_format' ) )
		return array();

	// Set needed variables
	$adjacent = ( true === $previous ) ? 'previous' : 'next';
	$order    = ( true === $previous ) ? 'DESC' : 'ASC';

	// Set the query args
	$args = array(
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => $order,
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	);

	/**
	 * Temporarily add filters to the post query:
	 * 1. turns off filter suppression and runs the query mod that groups post formats into collections
	 * 2. Adds a date range to the WHERE clause (this is what requires disabling filter suppression)
	 */
	add_filter( 'pre_get_posts', 'collections_adjacent_post_query' );
	add_filter( 'posts_where', 'collections_where_' . $adjacent );

	// Run the query
	$adjacent_post = get_posts( $args );

	// Remove the previously needed filters
	remove_filter( 'pre_get_posts', 'collections_adjacent_post_query' );
	remove_filter( 'posts_where', 'collections_where_' . $adjacent );

	// Return only the first post in the array
	return array_shift( $adjacent_post );
}
endif;

if ( ! function_exists( 'collections_adjacent_post_query' ) ) :
/**
 * Wrapper for using collections_query_mod to grab adjacent posts.
 *
 * @since  1.0.
 *
 * @param  object    $query    The query object to filter.
 * @return void
 */
function collections_adjacent_post_query( $query ) {
	// Turn off filter suppression so that the posts_where filter will work.
	$query->set( 'suppress_filters', false );

	// Mod the query.
	collections_query_mod( $query );
}
endif;

if ( ! function_exists( 'collections_where_previous' ) ) :
/**
 * Filter for get_posts to add a date range prior to the current post's date.
 *
 * @since  1.0.
 *
 * @param  string    $where    The WHERE statement to filter.
 * @return string              The modified WHERE statement.
 */
function collections_where_previous( $where ) {
	global $wpdb, $post;
	$current_post_date = $post->post_date;
	$where .= " AND $wpdb->posts.post_date < '$current_post_date'";
	return $where;
}
endif;

if ( ! function_exists( 'collections_where_next' ) ) :
/**
 * Filter for get_posts to add a date range after the current post's date.
 *
 * @since  1.0.
 *
 * @param  string    $where    The WHERE statement to filter.
 * @return string              The modified WHERE statement.
 */
function collections_where_next( $where ) {
	global $wpdb, $post;
	$current_post_date = $post->post_date;
	$where .= " AND $wpdb->posts.post_date > '$current_post_date'";
	return $where;
}
endif;

if ( ! function_exists( 'collections_adjacent_post_link' ) ) :
/**
 * Print a link for an adjacent post with the same post format.
 *
 * @since  1.0.
 *
 * @param  string    $format      The post format being inspected.
 * @param  string    $link        Additional text for the link.
 * @param  bool      $previous    Whether or not the link is for prev/next.
 * @return void
 */
function collections_adjacent_post_link( $format, $link, $previous = true ) {
	// Get the next/prev post
	$post = collections_adjacent_post( $previous );

	// Set vars for the link
	$rel         = ( true === $previous ) ? 'prev' : 'next';
	$title       = ( true === $previous ) ? __( 'Older', 'collections' ) : __( 'Newer', 'collections' );
	$id          = ( ! empty( $post ) ) ? $post->ID : '';
	$href        = ( ! empty( $post ) ) ? get_permalink( $post ) : '#';
	$extra_class = ( ! empty( $post ) ) ? '' : ' collections-post-nav-no-post';

	// Build the link
	$string  = '<a class="collections-post-nav' . esc_attr( $extra_class ) . '" data-id="' . absint( $id ) . '" href="' . esc_url( $href ) . '" rel="' . esc_attr( $rel ) . '" title="' . esc_attr( $title ) . '">';
	$in_link = $string . $link . '</a>';

	$output = str_replace( '%link', $in_link, $format );

	echo $output;
}
endif;

if ( ! function_exists( 'collections_previous_post_link' ) ) :
/**
 * Output a link to the previous post with the same post format
 *
 * @since  1.0.
 *
 * @param  string    $format    The post format.
 * @param  string    $link      Additional text for the link.
 * @return void
 */
function collections_previous_post_link( $format, $link ) {
	collections_adjacent_post_link( $format, $link );
}
endif;

if ( ! function_exists( 'collections_next_post_link' ) ) :
/**
 * Output a link to the next post with the same post format
 *
 * @since  1.0.
 *
 * @param  string    $format    The post format.
 * @param  string    $link      Additional text for the link.
 * @return void
 */
function collections_next_post_link( $format, $link ) {
	collections_adjacent_post_link( $format, $link, false );
}
endif;

if ( ! function_exists( 'collections_post_format_redirects' ) ) :
/**
 * Redirect certain post format archives.
 *
 * Analyze the request and redirect to a different URL if necessary. This has to be run earlier
 * than template_redirect in order to avoid an endless redirect loop.
 *
 * @since  1.0.
 *
 * @param  object    $obj    The parsed request.
 * @return void
 */
function collections_post_format_redirects( $obj ) {
	// Don't redirect requests in admin
	if ( is_admin() ) {
		return;
	}

	// Check to see if the request is for a post format archive
	if ( ! isset( $obj->query_vars['post_format'] ) ) {
		return;
	}

	// Get the post format
	$f = str_replace( 'post-format-', '', $obj->query_vars['post_format'] );

	// Default ruleset.
	$rules = array(
		'chat' => collections_pf_archive_url( 'aside' ),
		'status' => collections_pf_archive_url( 'aside' ),
		'gallery' => collections_pf_archive_url( 'image' )
	);

	// Allow filtering of ruleset.
	$rules = apply_filters( 'collections_post_format_redirects', $rules );

	// Do the redirection.
	if ( in_array( $f, array_keys( $rules ) ) ) {
		wp_redirect( $rules[$f], 301 );
		exit();
	} else {
		return;
	}
}
endif;

add_action( 'parse_request', 'collections_post_format_redirects' );

if ( ! function_exists( 'collections_archive_navigation_query' ) ) :
/**
 * Query posts for active formats.
 *
 * Query and cache an array of formats that have at least one published post.
 *
 * @since  1.0.
 *
 * @return array    Array of post formats that are active.
 */
function collections_archive_navigation_query() {
	// Generate a key
	$cache_key = 'collections-' . md5( 'navigation-links-' . COLLECTIONS_VERSION );

	// Check for previously cached array.
	$active_collections = get_transient( $cache_key );

	// If not cached, run the query.
	if ( false === $active_collections ) {
		// Create container
		$active_collections = array();

		// Default args for queries
		$args = array(
			'posts_per_page'      => 1,
			'status'              => 'publish',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => 1
		);
		$types = collections_get_types();

		// Add tax Query args and run each query
		foreach ( $types as $key => $data ) {
			unset( $query );
			if ( 'articles' === $key ) {
				$not_these_formats = array(
					'post-format-image',
					'post-format-gallery',
					'post-format-link',
					'post-format-quote',
					'post-format-video',
					'post-format-audio'
				);
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => $not_these_formats,
						'operator' => 'NOT IN'
					),
				);
			} else {
				foreach ( $data['formats'] as &$format ) {
					$format = 'post-format-' . $format;
				}
				$args['tax_query'] = array(
					array(
						'field'    => 'slug',
						'terms'    => $data['formats'],
						'taxonomy' => 'post_format'
					),
				);
			}
			// Run query
			$query = new WP_Query( $args );
			if ( $query->have_posts() )
				$active_collections[$key] = $data;
		}

		// Store cache item
		set_transient( $cache_key, $active_collections, 86400 );
	}

	return $active_collections;
}
endif;

if ( ! function_exists( 'collections_archive_navigation_links' ) ) :
/**
 * Template tag: Collection archive links as list items
 *
 * @since  1.0.
 *
 * @param  bool      $echo    True echos the content, False returns it.
 * @return string             The list items.
 */
function collections_archive_navigation_links( $echo = true ) {
	$active_collections = collections_archive_navigation_query();

	$render = '';

	// Now render the archive navigation.
	foreach ( $active_collections as $collection => $atts ) {
		$render .= '<li>';
		$render .= '<a class="cspa-control ' . esc_attr( $collection ) . '" data-post-format="' . esc_attr( $atts['slug'] ) . '" href="' . get_post_format_link( $atts['slug'] ) . '" title="' . esc_attr( $atts['label'] ) . '"></a>';
		$render .= '</li>';
	}

	// Return or echo.
	if ( false === $echo )
		return $render;
	else
		echo $render;
}
endif;

if ( ! function_exists( 'collections_purge_navigation_cache' ) ) :
/**
 * Invalidate the active formats cache.
 *
 * @since  1.0.1.
 *
 * @return void
 */
function collections_purge_navigation_cache() {
	$cache_key = 'collections-' . md5( 'navigation-links-' . COLLECTIONS_VERSION );
	delete_transient( $cache_key );
}
endif;

if ( ! function_exists( 'collections_archive_navigation_update_on_save_post' ) ) :
/**
 * Invalidate the cache.
 *
 * @since  1.0.1.
 *
 * @param  string    $new_status    The new status.
 * @param  string    $old_status    The current status.
 * @param  string    $post          The post object.
 * @return void
 */
function collections_archive_navigation_update_on_save_post( $new_status, $old_status, $post ) {
	collections_purge_navigation_cache();
}
endif;

add_action( 'transition_post_status', 'collections_archive_navigation_update_on_save_post', 10, 3 );

if ( ! function_exists( 'collections_archive_navigation_update_on_post_delete' ) ) :
/**
 * Invalidate the cache
 *
 * @since  1.0.1.
 *
 * @param  int     $id    The ID of the deleted post.
 * @return void
 */
function collections_archive_navigation_update_on_post_delete( $id ) {
	collections_purge_navigation_cache();
}
endif;

add_action( 'after_delete_post', 'collections_archive_navigation_update_on_post_delete', 10, 1 );

if ( ! function_exists( 'collections_create_post_format_terms' ) ) :
/**
 * Generate the aside and image post format terms.
 *
 * Collections maps "textual" (e.g., standard, chat, status) post formats onto the aside post format and "photo" (e.g.,
 * gallery) formats onto the image post format. If the image and aside terms do not exist, fairly significant problems
 * will result (e.g., get_post_format_link('aside|image') will return an empty result breaking home page links, aside
 * and image post format archive will 404). The primary issue is that WordPress only creates these terms for post
 * formats once a post is given one of the post formats. If a user creates a standard post, but no aside post (a fairly
 * common occurrence), this problem will be present. As the least hacky work-around, this function creates the terms,
 * which will guarantee that everything functions properly. They are generated if a flag is not set.
 *
 * @since  1.0.4.
 *
 * @return void
 */
function collections_create_post_format_terms() {
	// Only add the terms if they have not been generated or if it is explicitly called for
	if ( 0 === get_theme_mod( 'terms-added', 0 ) || 1 === apply_filters( 'collections_regenerate_terms', 0 ) ) {
		// The post format terms to be created
		$post_formats = array(
			'post-format-aside',
			'post-format-image',
		);

		// Add each of the terms.
		$success_count = 0;
		foreach ( $post_formats as $post_format ) {
			$result = wp_insert_term( $post_format, 'post_format' );
			if ( ! is_wp_error( $result ) && isset( $result['term_id'] ) && isset( $result['term_taxonomy_id'] ) ) {
				$success_count++;
			}
		}

		// If the number of successful terms is the same as the number that we want added, note this in the theme mods
		if ( count( $post_formats ) === $success_count ) {
			set_theme_mod( 'terms-added', 1 );
		}
	}
}
endif;

add_action( 'admin_init', 'collections_create_post_format_terms' );