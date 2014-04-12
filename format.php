<?php
/**
 * @package Collections
 */
?>
<?php if ( collections_is_js_template() ) : ?>
<# if ( passwordProtected ) { #>
	<div class="protect-post-wrapper">
		<h3>{{{ title }}}</h3>
		{{{ content }}}
	</div>
<# } else { #>
<?php endif; ?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="default-wrapper entry stitching-wrapper">
		<div class="entry-content stitching-content">
			<?php get_template_part( '_entry-title' ); ?>
			<?php get_template_part( '_post-date' ); ?>
			<div class="standard-content">
				<?php get_template_part( '_the-content' ); ?>
			</div>
			<?php get_template_part( '_post-author' ); ?>
			<?php get_template_part( '_wp-link-pages' ); ?>
		</div>
	</div>
</div>
<?php if ( collections_is_js_template() ) : ?>
<# } #>
<?php endif; ?>
<?php get_template_part( '_comments' ); ?>