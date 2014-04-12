<?php
/**
 * Template Name: Homepage Template
 * @package Collections
 */
get_header();
$collections_homepage_query = new Collections_Homepage_Query(); ?>

<div id="main-header" role="banner">
	<div class="frame header-wrapper">
		<nav id="nav">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'collections-homepage',
				'container_id'   => 'collections-nav',
				'menu_class'     => 'nav',
			) );
			?>
		</nav>
		<div class="logo-wrapper">
			<?php if ( collections_get_logo()->has_logo() ) : ?>
				<a class="custom-logo" title="<?php esc_attr_e( 'Home', 'collections' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
			<?php else : ?>
				<a title="<?php esc_attr_e( 'Home', 'collections' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
				</a>
			<?php endif; ?>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<span class="collections-tagline">
					<?php bloginfo( 'description' ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
</div>

<section class="column-one">
	<?php foreach ( $collections_homepage_query->split_queries['left'] as $collections_type => $collections_type_query ) : ?>
		<?php get_template_part( '_homepage', 'column' ); ?>
	<?php endforeach; ?>
</section>

<section class="column-two">
	<?php foreach ( $collections_homepage_query->split_queries['right'] as $collections_type => $collections_type_query ) : ?>
		<?php get_template_part( '_homepage', 'column' ); ?>
	<?php endforeach; ?>
</section>

<?php if ( 0 === get_theme_mod( 'hide-about-section', 0 )  ) : ?>
<div class="info">
	<section class="search<?php echo ( get_option( 'page_for_posts' ) ) ? '' : ' full-width'; ?>">
		<?php get_template_part( '_secondary-searchform' ); ?>
	</section>
	<?php if ( get_option( 'page_for_posts' ) ) : ?>
	<section class="archive">
		<a href="<?php echo collections_get_stream_url(); ?>">
			<h4><?php _e( 'Archive', 'collections' ); ?></h4>
		</a>
	</section>
	<?php endif; ?>

	<?php
	$thumb = get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
	$content = get_the_content();
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	?>

	<?php if ( '' !== $thumb || '' !== $content ) : ?>
		<section class="about">
			<?php if ( '' !== $thumb ) : ?>
				<div class="collections-avatar-wrapper">
					<?php echo $thumb; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $content ) : ?>
				<?php the_content(); ?>
			<?php endif; ?>
		</section>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php get_footer(); ?>