<?php
/** A secondary sale selection, distinct in hierarchy; all commerce remains native. */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WooCommerce' ) ) { return; }
$html = do_shortcode( '[products limit="2" columns="2" on_sale="true" orderby="date" order="DESC"]' );
if ( false === strpos( $html, '<li ' ) ) { return; }
?>
<section class="home-section signature-discovery">
	<div class="signature-discovery-intro"><p class="eyebrow"><?php esc_html_e( 'Outro ponto de vista', 'marquesano-store' ); ?></p><h2><?php echo esc_html( marquesano_store_setting( 'tech_discovery_title' ) ); ?></h2><p><?php echo esc_html( marquesano_store_setting( 'tech_discovery_text' ) ); ?></p><a class="signature-link" href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Continuar explorando', 'marquesano-store' ); ?> <span aria-hidden="true">↗</span></a></div>
	<div class="signature-discovery-shelf woocommerce"><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- native templates. ?></div>
</section>
