<?php
/** Archive presentation and stock query. Native WooCommerce price/order/pagination remain in charge. */
defined( 'ABSPATH' ) || exit;

function paraguai_online_is_catalog() {
	return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) );
}

add_filter( 'body_class', function ( $classes ) {
	if ( paraguai_online_is_catalog() ) { $classes[] = 'po-catalog'; }
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! paraguai_online_is_catalog() ) { return; }
	$file = '/assets/css/catalog.css';
	wp_enqueue_style( 'paraguai-catalog', get_template_directory_uri() . $file, array( 'marquesano-store' ), (string) filemtime( get_template_directory() . $file ) );
}, 30 );

add_filter( 'woocommerce_product_query_meta_query', function ( $meta ) {
	if ( is_admin() || ! paraguai_online_is_catalog() ) { return $meta; }
	$stock = isset( $_GET['stock_status'] ) && is_string( $_GET['stock_status'] ) ? sanitize_key( wp_unslash( $_GET['stock_status'] ) ) : '';
	if ( in_array( $stock, array( 'instock', 'outofstock', 'onbackorder' ), true ) ) { $meta[] = array( 'key' => '_stock_status', 'value' => $stock ); }
	return $meta;
} );

add_action( 'woocommerce_shop_loop_header', function () {
	if ( paraguai_online_is_catalog() ) { echo '<p class="po-catalog-eyebrow">PARAGUAI ONLINE / CATÁLOGO</p>'; }
}, 5 );

add_action( 'woocommerce_archive_description', function () {
	if ( ! paraguai_online_is_catalog() ) { return; }
	if ( is_search() ) { echo '<p class="po-catalog-description">Refine os resultados com os filtros abaixo.</p>'; }
	elseif ( ! is_product_taxonomy() || ! term_description() ) { echo '<p class="po-catalog-description">Explore os produtos por categoria, preço e disponibilidade.</p>'; }
}, 20 );

add_filter( 'woocommerce_get_stock_html', function ( $html, $product ) {
	if ( paraguai_online_is_catalog() && '' === $html && $product->is_in_stock() && ! $product->is_on_backorder() ) { return '<p class="stock in-stock">Disponível</p>'; }
	return $html;
}, 10, 2 );
