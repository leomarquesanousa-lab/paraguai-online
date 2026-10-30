<?php
/**
 * Theme setup and assets.
 *
 * @package MarquesanoStore
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/presentation.php';
require_once get_template_directory() . '/inc/branding.php';
require_once get_template_directory() . '/inc/tech.php';
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/hero-crossfade.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/checkout.php';
require_once get_template_directory() . '/inc/cart.php';
require_once get_template_directory() . '/inc/product.php';
require_once get_template_directory() . '/inc/catalog.php';
require_once get_template_directory() . '/inc/account.php';
require_once get_template_directory() . '/inc/demo-catalog.php';

function marquesano_store_setup() {
	load_theme_textdomain( 'marquesano-store', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 360, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array( 'primary' => __( 'Navegação principal', 'marquesano-store' ), 'footer' => __( 'Links institucionais', 'marquesano-store' ) ) );
}
add_action( 'after_setup_theme', 'marquesano_store_setup' );

function marquesano_store_enqueue_styles() {
	$version = (string) filemtime( get_template_directory() . '/style.css' );
	wp_enqueue_style( 'marquesano-store', get_stylesheet_uri(), array(), $version );
	wp_add_inline_style( 'marquesano-store', marquesano_store_css_variables() );
	if ( 'tech' === marquesano_store_setting( 'preset' ) ) {
		wp_enqueue_style( 'marquesano-tech-premium', get_template_directory_uri() . '/assets/css/tech-premium.css', array( 'marquesano-store' ), (string) filemtime( get_template_directory() . '/assets/css/tech-premium.css' ) );
		wp_enqueue_style( 'marquesano-tech-signature', get_template_directory_uri() . '/assets/css/tech-signature.css', array( 'marquesano-tech-premium' ), (string) filemtime( get_template_directory() . '/assets/css/tech-signature.css' ) );
		if ( function_exists( 'WC' ) ) { wp_enqueue_script( 'wc-cart-fragments' ); }
	}
}
add_action( 'wp_enqueue_scripts', 'marquesano_store_enqueue_styles' );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'preset-' . marquesano_store_setting( 'preset' );
	return $classes;
} );

require_once get_template_directory() . '/inc/navigation.php';
