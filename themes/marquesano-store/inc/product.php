<?php
/** Product presentation only. WooCommerce owns gallery, variations, notices and cart. */
defined( 'ABSPATH' ) || exit;

add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_product' ) && is_product() ) { $classes[] = 'po-product'; }
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) { return; }
	$file = '/assets/css/product.css';
	wp_enqueue_style( 'paraguai-product', get_template_directory_uri() . $file, array( 'marquesano-store' ), (string) filemtime( get_template_directory() . $file ) );
}, 30 );
