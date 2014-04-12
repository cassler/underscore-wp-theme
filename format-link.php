<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="entry stitching-wrapper">
		<div class="entry-content stitching-content">
			<?php get_template_part( '_entry-title' ); ?>
			<?php get_template_part( '_post-date' ); ?>
			<section class="content-wrapper">
				<?php if ( collections_is_js_template() ) : ?>
					{{{ remainingContent }}}
				<?php else : ?>
					<?php collections_the_remaining_content(); ?>
				<?php endif; ?>
			</section>
			<section class="link-icon">
				<a title="<?php esc_attr_e( 'Visit page', 'collections' ); ?>" href="<?php echo ( collections_is_js_template() ) ? '{{{ specialContent }}}' : collections_the_special_content( false ); ?>">
					<span><?php _e( 'Link', 'collections' ); ?></span>
				</a>
			</section>
			<?php get_template_part( '_wp-link-pages' ); ?>
		</div>
	</div>
</div>
<?php get_template_part( '_comments' ); ?>