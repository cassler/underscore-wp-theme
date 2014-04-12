<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="entry">
		<div class="entry-content">
			<?php get_template_part( '_the-content' ); ?>
			<?php get_template_part( '_wp-link-pages' ); ?>
		</div>
		<?php get_template_part( '_post-date' ); ?>
		<?php get_template_part( '_post-author' ); ?>
	</div>
</div>
<?php get_template_part( '_comments' ); ?>