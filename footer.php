<?php
/**
 * @package Collections
 */
?>
	<footer role="contentinfo" id="footer">
		<?php $footer_text = get_theme_mod( 'footer-text' ); ?>
		<?php if ( ! empty( $footer_text ) ) : ?>
		<p id="footer-text">
			<?php echo collections_allowed_tags( $footer_text ); ?>
		</p>
		<?php endif; ?>
	
		<p id="theme-byline">
			<a title="<?php esc_attr_e( 'Theme info', 'collections' ); ?>" href="https://thethemefoundry.com/wordpress-themes/collections/">Collections theme</a> <span><?php _ex( 'by', 'attributed to', 'collections' ); ?></span> <a title="<?php esc_attr_e( 'The Theme Foundry homepage', 'collections' ); ?>" href="https://thethemefoundry.com/">The Theme Foundry</a>
		</p>

		<?php $social_links = collections_get_social_links(); ?>
		<?php if ( ! empty( $social_links ) ) : ?>
			<ul id="social" class="icons">
				<?php foreach ( $social_links as $service_name => $details ) : ?>
					<?php if ( ! empty( $details['title'] ) && ! empty( $details['url'] ) ) : ?>
						<li>
							<a class="<?php echo esc_attr( $service_name ); ?>" href="<?php echo esc_url( $details['url'] ); ?>" title="<?php echo esc_attr( $details['title'] ); ?>"></a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>