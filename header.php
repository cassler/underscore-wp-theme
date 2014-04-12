<?php
/**
 * @package Collections
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>    <html class="no-js IE7 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js IE8 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>    <html class="no-js IE9 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<title><?php wp_title( '' ); ?></title>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="container" id="container">