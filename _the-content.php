<?php
/**
 * @package Collections
 */
if ( collections_is_js_template() ) : ?>
	{{{ content }}}
<?php else : ?>
	<?php the_content(); ?>
<?php endif;