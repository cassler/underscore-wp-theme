<?php
/**
 * @package Collections
 */

$style = collections_get_background_image_style( collections_get_audio_thumbnail_id(), 'collections-album-cover', array( 450, 450 ) );
$style = ( collections_is_js_template() ) ? '{{{ albumCoverStyle }}}' : $style;
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<?php if ( collections_audio_has_embed() ) : ?>
		<?php if ( collections_has_special() || collections_is_js_template()  ) : ?>
			<div class="collections-embedded-content">
			<?php if ( collections_is_js_template() ) : ?>
				{{{ specialContent }}}
			<?php else : ?>
				<?php collections_the_special_content(); ?>
			<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
	<div class="image-container" style="<?php echo $style; ?>">
		<div class="audio-content">
			<?php get_template_part( '_entry-title' ); ?>
			<span class="post-author">
				<?php if ( collections_is_js_template() ) : ?>
					{{{ audioArtist }}}
				<?php else : ?>
					<?php collections_the_audio_artist(); ?>
				<?php endif; ?>
			</span>
			<?php get_template_part( '_post-date' ); ?>
		</div>
		<?php if ( collections_has_special() || collections_is_js_template()  ) : ?>
			<?php if ( collections_is_js_template() ) : ?>
				{{{ specialContent }}}
			<?php else : ?>
				<?php collections_the_special_content(); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="sleeve"></div>
	<div class="record"></div>
	<?php endif; ?>
	<div class="entry-content">
		<?php if ( collections_is_js_template() ) : ?>
			{{{ remainingContent }}}
		<?php else : ?>
			<?php collections_the_remaining_content(); ?>
		<?php endif; ?>
		<?php wp_link_pages(); ?>
	</div>
</div>
<?php get_template_part( '_comments' ); ?>