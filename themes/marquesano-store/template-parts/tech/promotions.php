<?php
/** Two configurable campaigns backed by actual published products. */
defined( 'ABSPATH' ) || exit;
$products = marquesano_store_tech_products();
if ( ! $products ) { return; }
$selected = array();
foreach ( $products as $product ) { if ( $product->is_on_sale() ) { $selected[] = $product; break; } }
foreach ( $products as $product ) { if ( ! $selected || $product->get_id() !== $selected[0]->get_id() ) { $selected[] = $product; } if ( count( $selected ) >= 2 ) { break; } }
?>
<section class="home-section tech-promotions" aria-label="<?php esc_attr_e( 'Destaques da coleção', 'marquesano-store' ); ?>">
	<h2 class="screen-reader-text"><?php echo esc_html( marquesano_store_setting( 'tech_editorial_title' ) ); ?></h2>
	<?php foreach ( array( 1, 2 ) as $slot ) :
		$product = $selected[ $slot - 1 ] ?? null;
		$product = marquesano_store_tech_selected_product( 'tech_promo_' . $slot . '_product', $product );
		if ( ! $product ) { continue; }
		$title = marquesano_store_setting( 'tech_promo_' . $slot . '_title' ) ?: $product->get_name();
		$text = marquesano_store_setting( 'tech_promo_' . $slot . '_text' ) ?: wp_trim_words( wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ), 16 );
		$image_id = marquesano_store_setting( 'tech_promo_' . $slot . '_image' ) ?: $product->get_image_id();
		$url = marquesano_store_setting( 'tech_promo_' . $slot . '_url' ) ?: $product->get_permalink();
		?>
		<article class="tech-promo tech-promo-<?php echo esc_attr( $slot ); ?>">
			<?php $image = marquesano_store_tech_image( $image_id, 'large', array( 'class' => 'tech-promo-image', 'alt' => '', 'sizes' => '(max-width: 767px) 90vw, 50vw' ) ); if ( $image ) : ?><a class="signature-editorial-media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $image; ?></a><?php endif; ?>
			<div class="tech-promo-copy"><p class="eyebrow"><?php echo $product->is_on_sale() ? esc_html__( 'Em oferta', 'marquesano-store' ) : esc_html__( 'Em destaque', 'marquesano-store' ); ?></p>
			<h2><?php echo esc_html( $title ); ?></h2><p><?php echo esc_html( $text ); ?></p>
			<a class="button" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Ver detalhes', 'marquesano-store' ); ?><span aria-hidden="true">→</span></a></div>
		</article>
	<?php endforeach; ?>
</section>
