<?php
/**
 * @package Collections
 */

global $collections_type_query, $collections_type; $collections_types = collections_get_types(); ?>
<?php if ( $collections_type_query->have_posts() ) : ?>
	<?php while ( $collections_type_query->have_posts() ) : $collections_type_query->the_post(); ?>
		<?php $background  = collections_get_homepage_background_image( $collections_type ); ?>
		<?php $extra_class = ( '' !== $background ) ? ' has-image' : ''; ?>

		<a class="view-index" href="<?php echo esc_url( get_post_format_link( $collections_types[ $collections_type ]['slug'] ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'View all %s', 'collections' ), $collections_types[ $collections_type ]['label'] ) ); ?>">
			<div class="homepage-post <?php echo esc_attr( $collections_type ); ?>">
				<?php if ( in_array( $collections_type, array( 'links', 'audios' ) ) ) : ?>
					<p class="homepage-custom-link faux-link" title="<?php esc_attr_e( 'View post', 'collections' ); ?>" data-url="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</p>
				<?php endif; ?>
			
				<div class="homepage-post-wrapper stitching-wrapper <?php echo $extra_class; ?>"<?php echo $background; ?>>
					<div class="stitching-content">

						<p class="faux-link" data-url="<?php echo esc_url( get_post_format_link( $collections_types[ $collections_type ]['slug'] ) ); ?>">
							<div class="post-format-icon"></div>
						</p>

						<p class="faux-link" data-url="<?php echo esc_url( get_post_format_link( $collections_types[ $collections_type ]['slug'] ) ); ?>">
							<h2 class="post-format-title">
								<?php echo esc_html( $collections_types[ $collections_type ]['label'] ); ?>
							</h2>
						</p>

						<?php if ( 'articles' === $collections_type ) : ?>
							<p class="post-excerpt faux-link" title="<?php esc_attr_e( 'View post', 'collections' ); ?>" data-url="<?php the_permalink(); ?>">
								<?php
								add_filter( 'excerpt_length', 'collections_truncate_excerpt' );

								echo wp_trim_excerpt();

								remove_filter( 'excerpt_length', 'collections_truncate_excerpt' );
								?>
							</p>
						<?php endif; ?>

						<?php if ( 'quotes' === $collections_type ) : ?>
							<p class="post-excerpt faux-link" title="<?php esc_attr_e( 'View post', 'collections' ); ?>" data-url="<?php the_permalink(); ?>">
								<?php echo wp_trim_words( collections_the_special_content( false ), 25, collections_faux_read_more() ); ?>
							</p>
						<?php endif; ?>

						<p class="view-all faux-link" data-url="<?php echo esc_url( get_post_format_link( $collections_types[ $collections_type ]['slug'] ) ); ?>">
							<?php
							printf(
								__( 'View all %s', 'collections' ),
								esc_html( $collections_types[ $collections_type ]['label'] )
							);
							?>
						</p>
					</div>
				</div>
			</div>
		</a>
	<?php endwhile; ?>
<?php endif; wp_reset_postdata(); ?>