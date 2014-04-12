<?php
/**
 * @package Collections
 */
get_header(); ?>

<?php get_template_part( '_sidebar-header' ); ?>

<?php if ( have_posts() ) : ?>
	<div class="main-content">
		<div id="archive-wrapper">
			<h3 class="stream-title"><?php collections_archives_title(); ?></h3>
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="stream-wrapper">
					<?php get_template_part( '_stream-item' ); ?>
				</div>
			<?php endwhile; ?>
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
						'<strong>Admin:</strong> It looks like you haven\'t added any posts yet. <a href="%s" title="Add post">Add your first post</a> now!',
						'collections'
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
			else :
				_e( 'There are no posts to display.', 'collections' );
			endif;
			?>
		</div>
		<?php if ( collections_spa_enabled() && function_exists( 'collections_add_spinner' ) ) : ?>
			<?php collections_add_spinner(); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>