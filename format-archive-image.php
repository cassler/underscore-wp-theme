<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="image-container">
		<figure class="image-frame">
			<span class="theme-shadow">
				<?php if ( collections_is_js_template() ) : ?>
					<a href="{{ permalink }}" title="{{ title }}">
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php endif; ?>
				<div class="collections-photo-icon"></div>
				<?php if ( collections_is_js_template() ) : ?>
					<# if ( '' !== smallSquareImage ) { #>
						{{{ smallSquareImage }}}
					<# } else { #>
						<img width="252" height="252" src="<?php echo get_template_directory_uri(); ?>/images/image-format-placeholder.svg" class="attachment-collections-archive-small-square" alt="<?php esc_attr_e( 'Gallery format placeholder', 'collections' ); ?>" />
					<# } #>
				<?php else : ?>
					<?php $image = collections_the_image_sized( 'collections-archive-small-square', false ); ?>
					<?php if ( '' !== $image ) : ?>
						<?php echo $image; ?>
					<?php else : ?>
						<img width="252" height="252" src="<?php echo get_template_directory_uri(); ?>/images/image-format-placeholder.svg" class="attachment-collections-archive-small-square" alt="<?php esc_attr_e( 'Gallery format placeholder', 'collections' ); ?>" />
					<?php endif; ?>
				<?php endif; ?>
				</a>
			</span>
		</figure>
	</div>
</div>
