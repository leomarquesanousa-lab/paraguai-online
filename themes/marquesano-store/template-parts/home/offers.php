<?php
/** Real promotions only. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
if ( 'tech' === marquesano_store_setting( 'preset' ) ) { get_template_part( 'template-parts/tech/promotions' ); return; }
if ( ! class_exists( 'WooCommerce' ) ) { return; }
$html = do_shortcode( '[products limit="4" columns="4" on_sale="true" orderby="date" order="DESC"]' );
if ( false === strpos( $html, '<li ' ) ) { return; }
?>
<section class="home-section offers-section woocommerce">
	<div class="section-heading"><div><p class="eyebrow"><?php esc_html_e( 'Boas oportunidades', 'marquesano-store' ); ?></p><h2><?php esc_html_e( 'Vale a descoberta', 'marquesano-store' ); ?></h2></div><span class="sale-label"><?php esc_html_e( 'Em promoção', 'marquesano-store' ); ?></span></div>
	<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce templates. ?>
</section>
