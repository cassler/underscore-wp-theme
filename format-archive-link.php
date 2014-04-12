<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" data-url="<?php if ( collections_is_js_template() ) : ?>{{ permalink }}<?php else: the_permalink(); endif; ?>"
	<?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="entry">
		<div class="post-wrapper" data-url="<?php if ( collections_is_js_template() ) : ?>{{ permalink }}<?php else : the_permalink(); endif; ?>">
			<?php if ( collections_is_js_template() ) : ?>
				{{{ linkContent }}}
			<?php else : ?>
				<?php collections_the_link_archive_content(); ?>
			<?php endif; ?>
		</div>
	</div>
</div>