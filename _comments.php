<?php
/**
 * @package Collections
 */
?>
<div id="cspa-comments">
	<?php if ( collections_is_js_template() ) : ?>
		{{{ comments }}}
	<?php else : ?>
		<?php comments_template( '', true ); ?>
	<?php endif; ?>
</div>
