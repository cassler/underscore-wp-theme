<?php
/**
 * @package Collections
 */

get_header(); ?>

<?php get_template_part( '_sidebar-header' ); ?>

<?php if ( have_posts() ) : ?>
	<div class="main-content">
		<div id="archive-wrapper">
			<?php if ( collections_is_photo() ) : ?>
				<div id="photo-content-wrapper">
			<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'format-archive', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php if ( collections_is_photo() ) : ?>
				<div class="column-sizer"></div>
				<div class="gutter-sizer"></div></div>
			<?php endif; ?>
		</div>
	
		<?php get_template_part( '_load-more' ); ?>
		<?php if ( collections_spa_enabled() && function_exists( 'collections_add_spinner' ) ) : ?>
			<?php collections_add_spinner(); ?>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="main-content">
		<div id="archive-wrapper">
			<?php
			if ( current_user_can( 'install_themes' ) ) :
				printf(
					__(
						'<strong>Admin:</strong> It looks like you haven\'t added any posts for this format yet. <a href="%s" title="Add post">Add your first one</a> now!',
						'collections'
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
			else :
				_e( 'There are no posts to display.', 'collections' );
			endif;
			?>
		</div>
	</div>
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>