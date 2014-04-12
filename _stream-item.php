<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<?php if ( collections_is_js_template() ) : ?>
		<a href="{{ permalink }}" title="{{ title }}">
	<?php else : ?>
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
	<?php endif; ?>
		<div class="stream-item">
			<span class="stream-view-link" title="<?php if ( collections_is_js_template() ) : ?>{{ title }}<?php else : the_title_attribute(); endif; ?>">
				<?php _e( 'View', 'collections' ); ?>
			</span>
			<section class="stream-details">
				<?php get_template_part( '_entry-title' ); ?>
				<?php get_template_part( '_post-date' ); ?>
			</section>
		</div>
	</a>
</div>