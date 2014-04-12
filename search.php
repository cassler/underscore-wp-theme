<?php
/**
 * @package Collections
 */
get_header(); ?>

<?php get_template_part( '_sidebar-header' ); ?>

<?php if ( have_posts() ) : ?>
	<div class="main-content stream-wrapper">
		<h3 class="stream-title"><?php collections_archives_title(); ?></h3>
		<?php get_search_form(); ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( '_stream-item' ); ?>
		<?php endwhile; ?>
	</div>
<?php else : ?>
	<div class="main-content stream-wrapper">
		<h3 class="stream-title"><?php _e( 'No results found', 'collections' ); ?></h3>
		<p><?php printf( __( 'Sorry, your search for &#8216;<strong>%s</strong>&#8217; did not turn up any results. Please try again using a different search term.', 'collections' ), get_search_query() );?></p>
		<?php get_search_form(); ?>
	</div>
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>