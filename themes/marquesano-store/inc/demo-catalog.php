<?php
/** Segment only marked demo merchandise; ordinary products remain eligible. */
defined( 'ABSPATH' ) || exit;

function marquesano_store_demo_category_exclusions() {
	$exclude = array();
	foreach ( array( 'tech', 'fashion', 'beauty', 'home' ) as $segment ) {
		if ( $segment === marquesano_store_setting( 'preset' ) ) { continue; }
		$term = get_term_by( 'slug', 'marquesano-demo-' . $segment, 'product_cat' );
		if ( $term && 'marquesano-visual-v1' === get_term_meta( $term->term_id, '_marquesano_demo_batch', true ) ) { $exclude[] = $term->term_id; }
	}
	return $exclude;
}

function marquesano_store_demo_query( $query ) {
	$query[] = array(
		'relation' => 'OR',
		array( 'key' => '_marquesano_demo_batch', 'compare' => 'NOT EXISTS' ),
		array( 'key' => '_marquesano_demo_batch', 'value' => 'marquesano-visual-v1', 'compare' => '!=' ),
		array( 'key' => '_marquesano_demo_segment', 'value' => marquesano_store_setting( 'preset' ) ),
	);
	return $query;
}

add_filter( 'woocommerce_product_query_meta_query', function ( $query ) {
	return is_admin() ? $query : marquesano_store_demo_query( $query );
} );
add_filter( 'woocommerce_shortcode_products_query', function ( $args ) {
	$args['meta_query'] = marquesano_store_demo_query( $args['meta_query'] ?? array() );
	return $args;
} );
