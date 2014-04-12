<?php
/**
 * @package Collections
 */
?>
<time class="post-date" datetime="<?php if ( collections_is_js_template() ) : ?>{{ y_m_d }}<?php else : the_time( 'Y-m-d' ); endif; ?>"><span>
	<?php if ( collections_is_js_template() ) : ?>
		{{ time }}
	<?php else : ?>
		<?php the_time( get_option( 'date_format' ) ); ?>
	<?php endif; ?>
</span></time>