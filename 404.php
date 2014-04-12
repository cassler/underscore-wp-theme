<?php
/**
 * @package Collections
 */

get_header(); ?>
<div id="page-<?php the_ID(); ?>">
	<?php get_template_part( '_return-link' ); ?>
	<div class="default-wrapper entry stitching-wrapper">
		<div class="entry-content stitching-content">
			<?php get_template_part( '_entry-title' ); ?>
			<div class="standard-content">
				<p>
					<?php
					printf(
						__(
							'Sorry, this %1$s no longer exists. Try searching the site:',
							'collections'
						)
						,
						sprintf(
							'<abbr title="%1$s">%2$s</abbr>',
							esc_attr__( 'Uniform resource locator', 'collection' ),
							__( 'URL', 'collection' )
						)
					);
					?>
				</p>
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>