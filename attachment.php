<?php
/**
 * @package Collections
 */
?>
<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); global $post; ?>
	<div class="frame">
		<h3 class="stream-title"><?php the_title(); ?></h3>

		<?php if ( wp_attachment_is_image ( get_the_ID() ) ) : ?>
			<div class="image-container">
				<figure class="image-frame">
					<span class="theme-shadow">
						<?php echo wp_get_attachment_image( get_the_ID(), 'collections-full-width', false, array( 'class' => 'attachment-image attachment-collections-full-width' ) ); ?>
					</span>
				</figure>
			</div>
		<?php else : ?>
			<?php echo esc_html( basename( wp_get_attachment_url( get_the_ID() ) ) ); ?>
		<?php endif; ?>

		<?php if ( ! empty( $post->post_excerpt ) ) : ?>
			<div class="attachment-content attachment-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $content = $post->post_content ) : ?>
			<div class="attachment-content">
				<?php get_template_part( '_the-content' ); ?>
			</div>
		<?php endif; ?>

		<nav class="pagination post-footer">
			<div class="attachment-prev"><?php previous_image_link( 0, __( 'Previous image', 'collections' ) ); ?></div>
			<div class="attachment-next"><?php next_image_link( 0, __( 'Next image', 'collections' ) ); ?></div>
		</nav>

		<?php comments_template( '', true ); ?>
	</div>
<?php endwhile; ?>
<?php get_footer(); ?>