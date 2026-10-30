<?php
/** Product-led Signature hero; no generic or unlinked illustration fallback. */
defined( 'ABSPATH' ) || exit;
$products = marquesano_store_tech_products();
$fallback = $products[0] ?? null;
foreach ( $products as $item ) { if ( $item->is_on_sale() ) { $fallback = $item; break; } }
$product = marquesano_store_tech_selected_product( 'tech_hero_product', $fallback );
// Visual selection is independent from the automatic product used by links.
// Try configured media first; only use product media when no custom image renders.
$image = '';
foreach ( array( marquesano_store_setting( 'tech_hero_image' ), marquesano_store_setting( 'hero_image' ), $product ? $product->get_image_id() : 0 ) as $image_id ) {
	$image = marquesano_store_tech_image( $image_id, 'full', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 767px) 92vw, 55vw' ) );
	if ( $image ) { break; }
}
$url = marquesano_store_setting( 'hero_url' ) ?: marquesano_store_shop_url();
$image_url = $image ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
$slides = paraguai_tech_hero_slides( $image ? (int) $image_id : 0 );
$background_style = $image_url ? '--tech-hero-image:url(' . wp_json_encode( esc_url_raw( $image_url ), JSON_UNESCAPED_SLASHES ) . ');' : '';
?>
<section class="hero hero-tech home-section signature-hero <?php echo $image_url ? 'has-product-image' : 'no-product-image'; ?>" style="<?php echo esc_attr( $background_style ); ?>"<?php if ( count( $slides ) > 1 ) : ?> data-tech-hero-crossfade="<?php echo esc_attr( wp_json_encode( $slides ) ); ?>"<?php endif; ?>>
 <div class="signature-hero-copy">
  <p class="eyebrow"><?php echo esc_html( marquesano_store_identity()['eyebrow'] ); ?></p>
  <h1><?php
  $title = marquesano_store_setting( 'hero_title' );
  $highlight = marquesano_store_setting( 'tech_hero_highlight' );
  $position = $highlight ? strpos( $title, $highlight ) : false;
  if ( false === $position ) { echo esc_html( $title ); } else { echo esc_html( substr( $title, 0, $position ) ) . '<span>' . esc_html( $highlight ) . '</span>' . esc_html( substr( $title, $position + strlen( $highlight ) ) ); }
  ?></h1>
  <p class="hero-description"><?php echo nl2br( esc_html( marquesano_store_setting( 'hero_text' ) ) ); ?></p>
  <div class="signature-hero-actions"><a class="button hero-cta" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( marquesano_store_setting( 'hero_button' ) ); ?><span aria-hidden="true">↗</span></a><a class="signature-link" href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Ver a coleção completa', 'marquesano-store' ); ?></a></div>
  <?php get_template_part( 'template-parts/tech/service-links' ); ?>
 </div>
</section>
