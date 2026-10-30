<?php
/** Large product study with authoritative WooCommerce attributes. */
defined( 'ABSPATH' ) || exit;
$products = marquesano_store_tech_products();
$product = marquesano_store_tech_selected_product( 'tech_spotlight_product', $products ? $products[ count( $products ) - 1 ] : null );
if ( ! $product ) { return; }
$title = marquesano_store_setting( 'tech_spotlight_title' ) ?: $product->get_name();
$text = marquesano_store_setting( 'tech_spotlight_text' ) ?: wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );
$image = marquesano_store_tech_image( marquesano_store_setting( 'tech_spotlight_image' ) ?: $product->get_image_id(), 'full', array( 'sizes' => '(max-width: 767px) 92vw, 58vw' ) );
?>
<section class="home-section signature-spotlight <?php echo $image ? 'has-product-image' : 'no-product-image'; ?>">
	<?php if ( $image ) : ?><figure class="signature-spotlight-media"><?php echo $image; ?><figcaption><?php echo esc_html( $product->get_name() ); ?></figcaption></figure><?php endif; ?>
	<div class="signature-spotlight-copy"><p class="eyebrow"><?php esc_html_e( 'Em detalhe', 'marquesano-store' ); ?></p>
	<h2><?php echo esc_html( $title ); ?></h2><p><?php echo esc_html( $text ); ?></p>
	<dl class="signature-specifications">
	<?php $count = 0; foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) { continue; }
		$value = $product->get_attribute( $attribute->get_name() ); if ( ! $value ) { continue; }
		echo '<div><dt>' . esc_html( wc_attribute_label( $attribute->get_name(), $product ) ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
		if ( ++$count >= 4 ) { break; }
	} ?>
	</dl>
	<p class="signature-spotlight-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
	<a class="button" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'Conhecer o produto', 'marquesano-store' ); ?><span aria-hidden="true">↗</span></a></div>
</section>
