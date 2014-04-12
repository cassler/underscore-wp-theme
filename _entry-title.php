<?php
/**
 * @package Collections
 */
?>

<h1 class="entry-title">
	<?php if ( ! is_single() && ! is_page() && ! is_404() ) : ?>
		<?php if ( collections_is_js_template() ) : ?>
			<a href="{{ permalink }}" title="{{ title }}">
		<?php else : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( collections_is_js_template() ) : ?>
		{{{ title }}}
	<?php else : ?>
		<?php if ( is_404() ) : ?>
			<?php _e( '404: Page Not Found', 'collections' ); ?>
		<?php else : ?>
			<?php the_title(); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! is_single() && ! is_page() && ! is_404() ) : ?>
		</a>
	<?php endif; ?>
</h1>