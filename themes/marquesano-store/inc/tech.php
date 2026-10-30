<?php
/** TECH presentation only. No settings writes or commerce mutations. */
defined( 'ABSPATH' ) || exit;

function marquesano_store_tech_fields() {
	$fields = array(
		'tech_surface_ink' => array( 'label' => __( 'TECH: superfície grafite', 'marquesano-store' ), 'type' => 'color', 'default' => '#25292a' ),
		'tech_surface_mineral' => array( 'label' => __( 'TECH: superfície mineral', 'marquesano-store' ), 'type' => 'color', 'default' => '#e8e9e7' ),
		'tech_hero_product' => array( 'label' => __( 'TECH: ID do produto do hero (0 = automático)', 'marquesano-store' ), 'type' => 'number', 'default' => 0 ),
		'tech_hero_image' => array( 'label' => __( 'TECH: imagem do hero (recomendado 1600 × 700)', 'marquesano-store' ), 'type' => 'image', 'default' => 0 ),
		'tech_hero_images' => array( 'label' => __( 'TECH: imagens adicionais do hero (até 20)', 'marquesano-store' ), 'type' => 'images', 'default' => '' ),
		'tech_hero_highlight' => array( 'label' => __( 'TECH: trecho do título em azul', 'marquesano-store' ), 'type' => 'text', 'default' => 'um só lugar.' ),
		'tech_hero_aside' => array( 'label' => __( 'TECH: assinatura do hero', 'marquesano-store' ), 'type' => 'textarea', 'default' => 'O essencial, visto de perto.' ),
	);
	$fields['tech_spotlight_product'] = array( 'label' => __( 'TECH: ID do produto protagonista (0 = automático)', 'marquesano-store' ), 'type' => 'number', 'default' => 0 );
	$fields['tech_spotlight_image'] = array( 'label' => __( 'TECH: imagem editorial do protagonista', 'marquesano-store' ), 'type' => 'image', 'default' => 0 );
	foreach ( array( 'tech_editorial_title' => 'Objetos. Contextos. Possibilidades.', 'tech_spotlight_title' => '', 'tech_spotlight_text' => '', 'tech_discovery_title' => 'Uma boa oportunidade.', 'tech_discovery_text' => 'Uma seleção de produtos em promoção. Conheça os detalhes, confira os valores e escolha no seu tempo.', 'tech_footer_text' => 'O seu próximo movimento começa com uma escolha.' ) as $key => $default ) {
		$labels = array( 'tech_editorial_title' => 'Título da dupla editorial', 'tech_spotlight_title' => 'Título do protagonista (vazio usa o produto)', 'tech_spotlight_text' => 'Texto do protagonista (vazio usa a descrição)', 'tech_discovery_title' => 'Título da segunda vitrine', 'tech_discovery_text' => 'Texto da segunda vitrine', 'tech_footer_text' => 'Texto editorial do rodapé' );
		$fields[ $key ] = array( 'label' => 'TECH: ' . $labels[ $key ], 'type' => 'textarea', 'default' => $default );
	}
	foreach ( array( 1, 2 ) as $slot ) {
		foreach ( array( 'product' => array( 'number', 0, 'ID do produto (0 = automático)' ), 'image' => array( 'image', 0, 'Imagem (vazio = imagem do produto)' ), 'title' => array( 'text', '', 'Título (vazio = nome do produto)' ), 'text' => array( 'textarea', '', 'Descrição (vazio = resumo do produto)' ), 'url' => array( 'url', '', 'Link (vazio = página do produto)' ) ) as $key => $data ) {
			$fields[ 'tech_promo_' . $slot . '_' . $key ] = array( 'label' => sprintf( __( 'TECH banner %1$d: %2$s', 'marquesano-store' ), $slot, $data[2] ), 'type' => $data[0], 'default' => $data[1] );
		}
	}
	foreach ( array( 1 => 'Catálogo completo|Explore nossos produtos', 2 => 'Minha conta|Acompanhe seus pedidos', 3 => 'Seu carrinho|Revise sua seleção', 4 => 'Categorias|Encontre o que procura' ) as $slot => $text ) {
		$fields[ 'tech_service_' . $slot ] = array( 'label' => sprintf( __( 'TECH atalho %d: título|descrição (vazio oculta)', 'marquesano-store' ), $slot ), 'type' => 'text', 'default' => $text );
	}
	return $fields;
}

function marquesano_store_tech_products() {
	static $products;
	if ( isset( $products ) ) { return $products; }
	$products = array();
	if ( ! function_exists( 'wc_get_products' ) ) { return $products; }
	$registry = get_option( 'marquesano_demo_manifest_v1', array() );
	$exclude = array();
	if ( is_array( $registry ) ) { foreach ( $registry as $key => $id ) { if ( 0 !== strpos( $key, 'tech-' ) ) { $exclude[] = absint( $id ); } } }
	$items = wc_get_products( array( 'status' => 'publish', 'limit' => 12, 'orderby' => 'date', 'order' => 'DESC', 'exclude' => $exclude ) );
	foreach ( $items as $product ) {
		if ( ! $product->is_visible() ) { continue; }
		if ( 'marquesano-visual-v1' === $product->get_meta( '_marquesano_demo_batch' ) && 'tech' !== $product->get_meta( '_marquesano_demo_segment' ) ) { continue; }
		$products[] = $product;
		if ( count( $products ) >= 6 ) { break; }
	}
	return $products;
}

function marquesano_store_tech_selected_product( $setting, $fallback = null ) {
	$id = marquesano_store_setting( $setting );
	$product = $id && function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : $fallback;
	if ( $product && 'publish' === $product->get_status() && $product->is_visible() && ( 'marquesano-visual-v1' !== $product->get_meta( '_marquesano_demo_batch' ) || 'tech' === $product->get_meta( '_marquesano_demo_segment' ) ) ) { return $product; }
	return $fallback;
}

function marquesano_store_tech_image( $id, $size = 'large', $attributes = array() ) {
	if ( ! $id || ! wp_attachment_is_image( $id ) ) { return ''; }
	$file = get_attached_file( $id );
	if ( ! $file || ! is_file( $file ) ) { return ''; }
	return wp_get_attachment_image( $id, $size, false, $attributes );
}

function marquesano_store_tech_icon( $name ) {
	$paths = array(
		'user' => '<circle cx="12" cy="7" r="4"/><path d="M4 22v-3a8 8 0 0 1 16 0v3Z"/>',
		'cart' => '<path d="M2 3h3l3 13h12l2-10H6M9 20h.01M19 20h.01"/>',
		'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
		'box' => '<path d="m3 7 9-5 9 5v11l-9 5-9-5ZM3 7l9 5 9-5M12 12v11M7 4l10 6"/>',
	);
	return '<svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $paths[ $name ] ?? $paths['grid'] ) . '</svg>';
}

function marquesano_store_tech_cart_count() {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="ms-tech-cart-count">' . esc_html( $count ) . '</span>';
}
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	if ( 'tech' === marquesano_store_setting( 'preset' ) ) { $fragments['span.ms-tech-cart-count'] = marquesano_store_tech_cart_count(); }
	return $fragments;
} );
add_filter( 'woocommerce_sale_flash', function ( $html ) {
	return 'tech' === marquesano_store_setting( 'preset' ) ? '<span class="onsale">' . esc_html__( 'Oferta', 'marquesano-store' ) . '</span>' : $html;
} );
