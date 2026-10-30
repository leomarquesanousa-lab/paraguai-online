<?php
/** Cart presentation only. Native Blocks/Store API own all mutations and totals. */
defined( 'ABSPATH' ) || exit;

function paraguai_online_is_cart() { return function_exists( 'is_cart' ) && is_cart(); }

add_filter( 'body_class', function ( $classes ) {
	if ( paraguai_online_is_cart() ) { $classes[] = 'po-cart'; }
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! paraguai_online_is_cart() ) { return; }
	$file = '/assets/css/cart.css';
	wp_enqueue_style( 'paraguai-cart', get_template_directory_uri() . $file, array( 'marquesano-store' ), (string) filemtime( get_template_directory() . $file ) );
}, 30 );

// The cloned page contains this literal English heading, outside WooCommerce translations.
add_filter( 'render_block_core/heading', function ( $html ) {
	if ( ! paraguai_online_is_cart() ) { return $html; }
	if ( 'You may be interested in…' === trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) ) {
		return preg_replace( '/You may be interested in(?:&hellip;|&#8230;|…)/u', 'Você também pode gostar', $html );
	}
	return $html;
} );
