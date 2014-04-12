<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<?php if ( collections_has_special() && ! collections_is_js_template() ) : ?>
		<div class="image-container">
			<?php collections_the_special_content(); ?>
		</div>
	<?php elseif ( collections_is_js_template() ) : ?>
		<# if ( '' !== specialContent ) { #>
			<div class="image-container">
				{{{ specialContent }}}
			</div>
		<# } #>
	<?php endif; ?>
	<?php get_template_part( '_entry-title' ); ?>
	<?php get_template_part( '_post-date' ); ?>
	<div class="entry-content">
		<?php if (collections_is_js_template() ) : ?>
			{{{ remainingContent }}}
		<?php else : ?>
			<?php collections_the_remaining_content(); ?>
		<?php endif; ?>
		<?php get_template_part( '_wp-link-pages' ); ?>
	</div>
</div>
<?php get_template_part( '_comments' ); ?>