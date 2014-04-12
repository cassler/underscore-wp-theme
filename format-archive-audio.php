<?php
/**
 * @package Collections
 */

$style = collections_get_background_image_style( collections_get_audio_thumbnail_id(), 'collections-album-cover', array( 450, 450 ) );
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<?php if ( collections_is_js_template() ) : ?>
		<a class="audio-link-wrapper" href="{{ permalink }}" title="{{ title }}">
			<div class="image-container" style="{{{ albumCoverStyle }}}">
				<div class="audio-content">
					<h1 class="entry-title">
						{{{ title }}}
					</h1>
					<?php get_template_part( '_post-date' ); ?>
				</div>
			</div>
		</a>
		<div class="audio-detail-wrapper">
			<div class="sleeve"></div>
			<div class="record"></div>
		</div>
	<?php else : ?>
		<a class="audio-link-wrapper" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<div class="image-container" style="<?php echo $style; ?>">
				<div class="audio-content">
					<h1 class="entry-title">
						<?php the_title(); ?>
					</h1>
					<?php get_template_part( '_post-date' ); ?>
				</div>
			</div>
		</a>
		<div class="audio-detail-wrapper">
			<div class="sleeve"></div>
			<div class="record"></div>
		</div>
	<?php endif; ?>
</div>