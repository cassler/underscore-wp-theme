<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="entry">
		<div class="entry-content">
			<?php get_template_part( '_the-excerpt' ); ?>
			<?php get_template_part( '_wp-link-pages' ); ?>
		</div>
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php get_template_part( '_post-date' ); ?>
		</a>
	</div>
</div>