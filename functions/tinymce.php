<?php
/**
 * @package Collections
 */

if ( ! function_exists( 'collections_mce_editor_buttons_2' ) ) :
/**
 * Activate the Styles dropdown for the Visual editor.
 *
 * @since  1.0.
 *
 * @param  array    $buttons    Array of activated buttons.
 * @return array                The modified array.
 */
function collections_mce_editor_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
endif;

add_filter( 'mce_buttons_2', 'collections_mce_editor_buttons_2' );

if ( ! function_exists( 'collections_mce_before_init' ) ) :
/**
 * Add styles to the Styles dropdown.
 *
 * @since  1.0.
 *
 * @param  array    $settings    TinyMCE settings array.
 * @return mixed                 Modified array.
 */
function collections_mce_before_init( $settings ) {
	$style_formats = array(
		array(
			'title' => __( 'Note', 'collections' ),
			'block' => 'div',
			'classes' => 'collections-note'
		),
		array(
			'title' => __( 'Quote Attribution', 'collections' ),
			'inline' => 'cite',
			'classes' => 'collections-quote-attribute'
		),
		array(
			'title' => __( 'Raised Cap', 'collections' ),
			'inline' => 'span',
			'classes' => 'collections-raised-cap'
		),
	);

	$settings['style_formats'] = json_encode( $style_formats );

	return $settings;
}
endif;

add_filter( 'tiny_mce_before_init', 'collections_mce_before_init' );