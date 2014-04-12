<?php
/**
 * @package Collections
 */

if ( ! function_exists( 'collections_customize_display' ) ) :
/**
 * Add theme options for the home page via the WordPress Customizer.
 *
 * @since  1.0.
 *
 * @param  object    $wp_customize    The main customizer object.
 * @return void
 */
function collections_customize_display( $wp_customize ) {
	// Add the Social Links section
	$wp_customize->add_section(
		'collections_display',
		array(
			'title'    => __( 'Display', 'collections' ),
			'priority' => 160,
		)
	);

	// Hide the About section
	$wp_customize->add_setting(
		'hide-about-section',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'collections_hide-about-section',
		array(
			'settings' => 'hide-about-section',
			'section'  => 'collections_display',
			'label'    => __( 'Hide the Homepage About section', 'collections' ),
			'type'     => 'checkbox',
			'priority' => 10,
		)
	);

	// Hide the About section
	$wp_customize->add_setting(
		'disable-spa',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'collections_disable-spa',
		array(
			'settings' => 'disable-spa',
			'section'  => 'collections_display',
			'label'    => __( 'Disable Fast page loading', 'collections' ),
			'type'     => 'checkbox',
			'priority' => 20,
		)
	);
}
endif;

add_action( 'customize_register', 'collections_customize_display' );

if ( ! function_exists( 'collections_customize_footer' ) ) :
/**
 * Add theme options for the footer via the WordPress Customizer.
 *
 * @since  1.0.
 *
 * @param  object    $wp_customize    The main customizer object.
 * @return void
 */
function collections_customize_footer( $wp_customize ) {
	// Add the Social Links section
	$wp_customize->add_section(
		'collections_footer',
		array(
			'title'    => __( 'Footer', 'collections' ),
			'priority' => 161,
		)
	);

	// Footer text
	$wp_customize->add_setting(
		'footer-text',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'collections_allowed_tags',
		)
	);

	$wp_customize->add_control(
		'collections_footer-text',
		array(
			'settings' => 'footer-text',
			'section'  => 'collections_footer',
			'label'    => __( 'Footer Text', 'collections' ),
			'type'     => 'text',
			'priority' => 10,
		)
	);

	// Twitter
	$wp_customize->add_setting(
		'twitter',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_twitter',
		array(
			'settings' => 'twitter',
			'section'  => 'collections_footer',
			'label'    => __( 'Twitter URL', 'collections' ),
			'type'     => 'text',
			'priority' => 20,
		)
	);

	// Facebook
	$wp_customize->add_setting(
		'facebook',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_facebook',
		array(
			'settings' => 'facebook',
			'section'  => 'collections_footer',
			'label'    => __( 'Facebook URL', 'collections' ),
			'type'     => 'text',
			'priority' => 30,
		)
	);

	// Google
	$wp_customize->add_setting(
		'google',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_google',
		array(
			'settings' => 'google',
			'section'  => 'collections_footer',
			'label'    => __( 'Google Plus URL', 'collections' ),
			'type'     => 'text',
			'priority' => 40,
		)
	);

	// Flickr
	$wp_customize->add_setting(
		'flickr',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_flickr',
		array(
			'settings' => 'flickr',
			'section'  => 'collections_footer',
			'label'    => __( 'Flickr URL', 'collections' ),
			'type'     => 'text',
			'priority' => 50,
		)
	);

	// Pinterest
	$wp_customize->add_setting(
		'pinterest',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_pinterest',
		array(
			'settings' => 'pinterest',
			'section'  => 'collections_footer',
			'label'    => __( 'Pinterest URL', 'collections' ),
			'type'     => 'text',
			'priority' => 60,
		)
	);

	// Linked In
	$wp_customize->add_setting(
		'linkedin',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_linked-in',
		array(
			'settings' => 'linkedin',
			'section'  => 'collections_footer',
			'label'    => __( 'LinkedIn URL', 'collections' ),
			'type'     => 'text',
			'priority' => 70,
		)
	);

	// RSS
	$wp_customize->add_setting(
		'rss',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'collections_rss',
		array(
			'settings' => 'rss',
			'section'  => 'collections_footer',
			'label'    => __( 'RSS URL', 'collections' ),
			'type'     => 'text',
			'priority' => 70,
		)
	);

}
endif;

add_action( 'customize_register', 'collections_customize_footer' );
