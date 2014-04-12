<?php
/**
 * @package Collections
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>
	<div id="cspa-post-wrapper">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( '_return-link' ); ?>
			<div id="cspa-post">
				<?php if ( post_password_required() ) : ?>
					<div class="protect-post-wrapper">
						<h3><?php the_title(); ?></h3>
						<?php the_content(); ?>
					</div>
				<?php else : ?>
					<?php get_template_part( 'format', get_post_format() ); ?>
				<?php endif; ?>
			</div>

			<div id="cspa-post-navigation">
				<?php collections_previous_post_link( '%link', '<span class="post-nav-right"></span>' ); ?>
				<?php collections_next_post_link( '%link', '<span class="post-nav-left"></span>' ); ?>
			</div>
		<?php endwhile; ?>
		<?php if ( collections_spa_enabled() && function_exists( 'collections_add_spinner' ) ) : ?>
			<?php collections_add_spinner(); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php get_footer(); ?>