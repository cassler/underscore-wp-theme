<?php
/**
 * @package Collections
 */
?>
<form method="get" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Type and press Enter to search', 'collections' ); ?>">
	<input type="text" id="s" name="s" autofocus="autofocus" placeholder="<?php esc_attr_e( 'Search site&hellip;', 'collections' ); ?>" />
</form>