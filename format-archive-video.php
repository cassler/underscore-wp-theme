<?php
/**
 * @package Collections
 */
?>

<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="video-container">
		<?php if ( collections_is_js_template() ) : ?>
			<a href="{{ permalink }}" title="{{ title }}">
		<?php else : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		<?php endif; ?>

		<div class="video-cover">
			<?php if ( collections_is_js_template() ) : ?>
				<# if ( videoPostThumbnail ) { #>
					{{{ videoPostThumbnail }}}
				<# } else { #>
					<img width="378" height="235" src="<?php echo get_template_directory_uri(); ?>/images/video-placeholder.svg">
				<# } #>
			<?php else : ?>
				<?php $thumb = get_the_post_thumbnail( get_the_ID(), 'collections-video-cover' ); ?>
				<?php if ( '' !== $thumb ) : ?>
					<?php echo $thumb; ?>
				<?php else : ?>
					<img width="378" height="235" src="<?php echo get_template_directory_uri(); ?>/images/video-placeholder.svg">
				<?php endif; ?>
			<?php endif; ?>
		</div>
		</a>
		<?php get_template_part( '_entry-title' ); ?>
	</div>
</div>
