<?php
/**
 * @package Collections
 */
?>
<div class="sidebar header-sidebar">
	<?php if ( collections_get_logo()->has_logo() ) : ?>
		<a class="custom-logo" title="<?php esc_attr_e( 'Home', 'collections' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
	<?php else : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Home', 'collections' ); ?>">
			<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
		</a>
	<?php endif; ?>
</div>