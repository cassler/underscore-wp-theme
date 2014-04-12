<?php
/**
 * @package Collections
 */
?>
<span class="post-signature">
	<?php if ( collections_is_js_template() ) : ?>
		{{ author }}
	<?php else : ?>
		<?php the_author(); ?>
	<?php endif; ?>
</span>