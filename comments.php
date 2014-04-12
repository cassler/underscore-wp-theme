<?php
/**
 * @package Collections
 */
?>
<?php if ( post_password_required() ) : ?>
	<?php return; ?>
<?php endif; ?>

<?php if ( comments_open() || have_comments() ) : ?>
	<div class="comment-bubble">
		<span><?php comments_number( '0', '1', '%' ); ?></span>
	</div>
<?php endif; ?>

<?php if ( have_comments() ) : ?>
	<section id="comments" class="thecomments">

		<ol>
			<?php wp_list_comments( array( 'callback' => 'collections_comment' ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav id="comments-nav" class="pagination">
				<div class="comments-previous"><?php previous_comments_link( __( 'Older comments', 'collections' ) ); ?></div>
				<div class="comments-next"><?php next_comments_link( __( 'Newer comments', 'collections' ) ); ?></div>
			</nav>
		<?php endif; ?>

		<?php if ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
			<p class="comments-closed"><em><?php _e( 'Comments are closed.', 'collections' ); ?></em></p>
		<?php endif; ?>
	</section>
<?php endif; ?>

<?php comment_form();