<?php defined( 'ABSPATH' ) || exit; ?>
<div class="empty-collection">
	<div class="empty-art" aria-hidden="true"><span></span><span></span><span></span></div>
	<div><p class="eyebrow"><?php esc_html_e( 'Novas descobertas', 'marquesano-store' ); ?></p>
	<h3><?php echo esc_html( $args['title'] ?? __( 'Ainda não há produtos nesta seleção.', 'marquesano-store' ) ); ?></h3>
	<p><?php esc_html_e( 'Esta seleção ainda não tem produtos disponíveis. Explore o catálogo ou volte em outro momento.', 'marquesano-store' ); ?></p>
	<a class="text-link" href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Explorar o catálogo', 'marquesano-store' ); ?> <span aria-hidden="true">↗</span></a></div>
</div>
