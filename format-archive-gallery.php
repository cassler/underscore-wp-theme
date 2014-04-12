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
					<# if ( '' !== theGalleryCover ) { #>
						{{{ theGalleryCover }}}
					<# } else { #>
						<img width="504" height="336" src="<?php echo get_template_directory_uri(); ?>/images/gallery-format-placeholder.svg" class="attachment-collections-archive-medium" alt="<?php esc_attr_e( 'Gallery format placeholder', 'collections' ); ?>" />
					<# } #>
				<?php else : ?>
					<?php $gallery_cover = collections_the_gallery_cover( 'collections-archive-medium', false ); ?>
					<?php if ( '' !== $gallery_cover ) : ?>
						<?php echo $gallery_cover; ?>
					<?php else : ?>
						<img width="504" height="336" src="<?php echo get_template_directory_uri(); ?>/images/gallery-format-placeholder.svg" class="attachment-collections-archive-medium" alt="<?php esc_attr_e( 'Gallery format placeholder', 'collections' ); ?>" />
					<?php endif; ?>
				<?php endif; ?>
				</a>
			</span>
		</figure>
	</div>
</div>