<?php
/**
 * @package Collections
 */
?>
<div id="post-<?php if ( collections_is_js_template() ) : ?>{{ id }}<?php else : the_ID(); endif; ?>" <?php if ( collections_is_js_template() ) : ?>class="{{ postClasses }}"<?php else : post_class(); endif; ?>>
	<div class="default-wrapper entry stitching-wrapper">
		<div class="entry-content stitching-content">
			<?php get_template_part( '_entry-title' ); ?>
			<?php if ( collections_is_js_template() ) : ?>
				<a href="{{ permalink }}" title="{{ title }}">
			<?php else : ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php endif; ?>
			<?php get_template_part( '_post-date' ); ?>
				</a>
			<?php get_template_part( '_the-excerpt' ); ?>

			<?php if ( collections_is_js_template() ) : ?>
				<# if ( showComments ) { #>
					<a class="index-comment" href="{{ commentsLink }}">
						<div class="comment-bubble">
							<span>{{ commentsNumber }}</span>
						</div>
						<span class="reply-title"><?php _e( 'Reply', 'collections' ); ?></span>
					</a>
				<# } #>
			<?php elseif ( comments_open() || have_comments() ) : ?>
				<a class="index-comment" href="<?php comments_link(); ?>">
					<div class="comment-bubble">
						<span><?php comments_number( '0', '1', '%' ); ?></span>
					</div>
					<span class="reply-title"><?php _e( 'Reply', 'collections' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>