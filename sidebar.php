<?php
/**
 * @package Collections
 */
?>
<div id="sidebar" class="sidebar main-sidebar">
	<ul class="post-icons">
		<?php collections_archive_navigation_links(); ?>
	</ul>

	<a class="cspa-control stream-archive" data-post-format="stream" href="<?php echo collections_get_stream_url(); ?>"></a>

	<?php
		$instance = array(
			'title' => 'Archive',
		);
		$args = array(
			'before_title' => '<h3 class="widgettitle">',
			'after_title'  => '</h3>'
		);
		the_widget( 'WP_Widget_Archives', $instance, $args );
	?>

	<?php get_template_part( '_secondary-searchform' ); ?>
	
</div>