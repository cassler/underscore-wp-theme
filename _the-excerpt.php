<?php
/**
 * @package Collections
 */
if ( collections_is_js_template() ) : ?>
	{{{ excerpt }}}
<?php else : ?>
	<?php the_excerpt(); ?>
<?php endif;