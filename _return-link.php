<?php
/**
 * @package Collections
 */
?>
<div id="cspa-return-to">
	<?php if ( is_page() || is_404() ) : ?>
		<a class="return-to" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span><?php _e( 'Home', 'collections' ); ?></span>
		</a>
	<?php else : ?>
		<a class="return-to" href="<?php echo esc_url( collections_pf_archive_url() ); ?>">
			<span>
				<?php
				printf(
					__( 'View all %s', 'collections' ),
					esc_html( strtolower( collections_pf_archive_title() ) )
				);
				?>
			</span>
		</a>
	<?php endif; ?>
</div>