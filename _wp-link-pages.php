<?php
/**
 * @package Collections
 */

if ( collections_is_js_template() ) : ?>
	{{{ pageLinks }}}
<?php else : ?>
	<?php wp_link_pages(); ?>
<?php endif;