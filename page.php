<?php
/**
 * @package Collections
 */

get_header(); ?>
	<div id="page-<?php the_ID(); ?>">
		<?php get_template_part( '_return-link' ); ?>
		<div id="cspa-page-wrapper">
			<?php while( have_posts() ) : the_post(); ?>
				<div class="default-wrapper entry stitching-wrapper">
					<div class="entry-content stitching-content">
						<?php get_template_part( '_entry-title' ); ?>
						<div class="standard-content">
							<?php $thumb = get_the_post_thumbnail(); ?>
							<?php if ( '' !== $thumb ) : ?>
								<div class="page-thumb">
									<?php echo $thumb; ?>
								</div>
							<?php endif; ?>
							<?php the_content(); ?>
						</div>
						<?php wp_link_pages(); ?>
					</div>
				</div>
			<?php endwhile; ?>
			<?php comments_template( '', true ); ?>
		</div>
	</div>
<?php get_footer(); ?>