<?php
/** Checkout presentation only; native blocks, endpoints and gateway hooks remain intact. */
defined( 'ABSPATH' ) || exit;

function paraguai_online_checkout_shell() {
	return function_exists( 'is_checkout' ) && is_checkout();
}

add_filter( 'body_class', function ( $classes ) {
	if ( paraguai_online_checkout_shell() ) { $classes[] = 'po-checkout'; }
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! paraguai_online_checkout_shell() ) { return; }
	$file = '/assets/css/checkout.css';
	wp_enqueue_style( 'paraguai-checkout', get_template_directory_uri() . $file, array( 'marquesano-store' ), (string) filemtime( get_template_directory() . $file ) );
}, 30 );
