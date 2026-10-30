<?php
/** WooCommerce integration without template copies or order storage assumptions. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	if ( ! class_exists( 'WooCommerce' ) ) { return; }
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	add_action( 'woocommerce_before_main_content', function () { echo '<main id="main-content" class="site-main shop-main">'; }, 10 );
	add_action( 'woocommerce_after_main_content', function () { echo '</main>'; }, 10 );
} );

add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );

// Enhance native cards without replacing their links, purchase actions or prices.
add_action( 'woocommerce_after_shop_loop_item_title', function () {
	global $product;
	if ( ! $product instanceof WC_Product ) { return; }
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) { echo '<span class="product-category-label">' . esc_html( $terms[0]->name ) . '</span>'; }
	if ( 'tech' !== marquesano_store_setting( 'preset' ) ) { return; }
	$specs = array();
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) { continue; }
		$value = $product->get_attribute( $attribute->get_name() );
		if ( $value ) { $specs[] = wc_attribute_label( $attribute->get_name(), $product ) . ': ' . $value; }
		if ( count( $specs ) >= 2 ) { break; }
	}
	if ( $specs ) { echo '<p class="product-specs">' . esc_html( implode( ' · ', $specs ) ) . '</p>'; }
}, 7 );

add_action( 'woocommerce_after_shop_loop_item_title', function () {
	global $product;
	if ( $product instanceof WC_Product ) {
		echo wp_kses_post( wc_get_stock_html( $product ) );
	}
}, 15 );

// Native price filtering treats an empty max_price as zero. Remove empty
// filters with a read-only redirect so the form also works without JavaScript.
add_action( 'template_redirect', function () {
	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_taxonomy() && ! is_search() ) ) { return; }
	$remove = array();
	foreach ( array( 'min_price', 'max_price' ) as $key ) {
		if ( isset( $_GET[ $key ] ) && '' === $_GET[ $key ] ) { $remove[] = $key; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( isset( $_GET['product_cat'] ) && '0' === $_GET['product_cat'] ) { $remove[] = 'product_cat'; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $remove ) { wp_safe_redirect( remove_query_arg( $remove ) ); exit; }
}, 5 );

add_action( 'woocommerce_before_shop_loop', 'marquesano_store_catalog_filters', 15 );
add_action( 'woocommerce_no_products_found', 'marquesano_store_catalog_filters', 5 );
function marquesano_store_catalog_filters() {
	if ( ! is_shop() && ! is_product_taxonomy() && ! is_search() ) { return; }
	get_template_part( 'template-parts/catalog-filters' );
}
