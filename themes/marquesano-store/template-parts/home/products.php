<?php
/** Native product loop: prices, visibility, stock and variable-product links. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WooCommerce' ) ) { return; }
$mode = marquesano_store_setting( 'products_mode' );
$extra = 'featured' === $mode ? ' visibility="featured"' : ( 'sale' === $mode ? ' on_sale="true"' : '' );
?>
<section class="home-section products-section woocommerce">
	<div class="section-heading"><div><p class="eyebrow"><?php esc_html_e( 'Explore a seleção', 'marquesano-store' ); ?></p><h2><?php echo esc_html( marquesano_store_setting( 'products_title' ) ); ?></h2></div><a class="text-link" href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Ver todos os produtos', 'marquesano-store' ); ?> <span aria-hidden="true">↗</span></a></div>
	<?php
	add_action( 'woocommerce_shortcode_products_loop_no_results', 'marquesano_store_empty_collection' );
	$layout = 'tech' === marquesano_store_setting( 'preset' ) ? 'limit="12" columns="5"' : 'limit="8" columns="4"';
	$html = do_shortcode( '[products ' . $layout . ' orderby="date" order="DESC"' . $extra . ']' );
	remove_action( 'woocommerce_shortcode_products_loop_no_results', 'marquesano_store_empty_collection' );
	// The shortcode output is generated and escaped by WooCommerce templates.
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</section>
