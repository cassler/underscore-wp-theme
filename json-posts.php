<?php
/**
 * @package Collections
 */
$data = array(
	'posts'  => array(),
	'global' => array(),
);

ob_start();
wp_head();
ob_end_clean();

if ( have_posts() ) {
	while( have_posts() ) {
		the_post();

		// Make sure that the content is properly processed before printing
		$content = apply_filters( 'the_content', get_the_content(), get_the_ID() );
		$content = str_replace( ']]>', ']]&gt;', $content );

		// Same with the excerpt
		$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );

		// Get next and previous posts
		$next_post     = collections_adjacent_post( false );
		$previous_post = collections_adjacent_post();

		// Render the comments
		ob_start();
		comments_template( '', true );
		$comments = ob_get_contents();
		ob_end_clean();

		// Get the comments number
		ob_start();
		comments_number( '0', '1', '%' );
		$comments_number = ob_get_contents();
		ob_end_clean();

		// Get the thumb
		$video_thumb = get_the_post_thumbnail( get_the_ID(), 'collections-video-cover' );

		$data['posts'][] = array(
			'id'                 => get_the_ID(),
			'bodyClasses'        => join( ' ', get_body_class() ),
			'postClasses'        => join( ' ', get_post_class() ),
			'title'              => get_the_title(),
			'content'            => $content,
			'excerpt'            => $excerpt,
			'specialContent'     => collections_the_special_content( false ),
			'remainingContent'   => collections_the_remaining_content( false ),
			'time'               => get_the_time( get_option( 'date_format' ) ),
			'y_m_d'              => get_the_time( 'Y-m-d' ),
			'author'             => get_the_author(),
			'postFormat'         => ( false === get_post_format() ) ? 'standard' : get_post_format(),
			'nextPostID'         => ( !empty( $next_post ) && isset( $next_post->ID ) ) ? $next_post->ID : '',
			'prevPostID'         => ( !empty( $previous_post ) && isset( $previous_post->ID ) ) ? $previous_post->ID : '',
			'nextPostURL'        => ( !empty( $next_post ) && isset( $next_post->ID ) ) ? get_permalink( $next_post->ID ) : '',
			'prevPostURL'        => ( !empty( $previous_post ) && isset( $previous_post->ID ) ) ? get_permalink( $previous_post->ID ) : '',
			'comments'           => $comments,
			'pageLinks'          => wp_link_pages( array( 'echo' => 0 ) ),
			'permalink'          => esc_url( get_permalink() ),
			'albumCoverStyle'    => collections_get_background_image_style( collections_get_audio_thumbnail_id(), 'collections-album-cover', array( 450, 450 ) ),
			'audioArtist'        => collections_the_audio_artist( false ),
			'hasAudioEmbed'      => collections_audio_has_embed(),
			'linkContent'        => collections_the_link_archive_content( false ),
			'quoteContent'       => collections_the_quote_archive_content( false ),
			'videoPostThumbnail' => $video_thumb,
			'smallSquareImage'   => collections_the_image_sized( 'collections-archive-small-square', false ),
			'theGalleryCover'    => collections_the_gallery_cover( 'collections-archive-medium', false ),
			'showComments'       => ( comments_open() || have_comments() ) ? 1 : 0,
			'commentsLink'       => esc_url( get_comments_link() ),
			'commentsNumber'     => $comments_number,
			'passwordProtected'  => ( post_password_required() ) ? 1 : 0,
		);
	}
}

ob_start();
wp_footer();
ob_end_clean();

// Get the enqueues information
$enqueues = collections_get_rendered_enqueues();

// Get all of the necessary script data for each script
$scripts = array();
foreach ( $enqueues['scripts'] as $handle ) {
	$scripts[ $handle ] = collections_get_script_data( $handle );
}

// Get necessary styles data for each stylesheet
$styles = array();
foreach ( $enqueues['styles'] as $handle ) {
	$styles[ $handle ] = collections_get_style_data( $handle );
}

// Next post link
$next = collections_get_next_posts_button();

// Set the global request data
$data['global'] = array(
	'queriedObject' => get_queried_object(),
	'enqueues'      => array(
		'scripts' => $scripts,
		'styles'  => $styles,
	),
	'bodyClasses'   => join( ' ', get_body_class() ),
	'nextButton'    => $next,
	'isHome'        => ( is_home() ) ? 1 : 0,
	'isArchive'     => ( is_archive() ) ? 1 : 0,
	'isTax'         => ( is_tax() ) ? 1 : 0,
	'titleAttr'     => esc_attr( wp_title( '', false ) ),
	'archiveTitle'  => collections_archives_title( false ),
);

echo json_encode( $data );