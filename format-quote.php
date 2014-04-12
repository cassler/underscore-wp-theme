<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="entry stitching-wrapper">
		<div class="entry-content stitching-content">
			<?php get_template_part( '_post-date' ); ?>
			<blockquote class="collections-quote">
				<?php if ( collections_has_special() || collections_is_js_template() ) : ?>
					<?php if ( collections_is_js_template() ) : ?>
						{{{ specialContent }}}
					<?php else : ?>
						<?php collections_the_special_content(); ?>
					<?php endif; ?>
				<?php endif; ?>
			</blockquote>
			<section class="remaining-content">
				<?php if ( collections_is_js_template() ) : ?>
					{{{ remainingContent }}}
				<?php else : ?>
					<?php collections_the_remaining_content(); ?>
				<?php endif; ?>
			</section>
			<?php get_template_part( '_wp-link-pages' ); ?>
		</div>
	</div>
</div>
<?php get_template_part( '_comments' ); ?>