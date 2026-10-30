<?php
/** Real metrics via WooCommerce APIs, compatible with HPOS. @package MarquesanoAdmin */
namespace Marquesano\Admin;
defined( 'ABSPATH' ) || exit;

function dashboard() {
	guard( 'manage_woocommerce' );
	heading( __( 'Visão geral da loja', 'marquesano-admin' ) );
	echo '<p>' . esc_html__( 'Dados atuais desta instalação. A gestão utiliza os cadastros e fluxos oficiais do WooCommerce.', 'marquesano-admin' ) . '</p>';
	if ( ! woo_available() ) { echo '</div>'; return; }
	if ( 'yes' === get_option( 'woocommerce_coming_soon' ) ) {
		echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'O modo Em breve do WooCommerce está ativo. Visitantes anônimos podem não visualizar o catálogo. Faça a validação autenticado; esta configuração foi preservada e não será desativada automaticamente.', 'marquesano-admin' ) . '</p></div>';
	}
	if ( current_user_can( 'edit_shop_orders' ) ) {
		try {
			echo '<div class="ms-grid">';
			foreach ( array( 'pending', 'on-hold', 'processing', 'completed' ) as $status ) {
				echo '<div class="ms-card"><h2>' . esc_html( wc_get_order_status_name( $status ) ) . '</h2><strong class="ms-number">' . esc_html( number_format_i18n( wc_orders_count( $status ) ) ) . '</strong><p>' . esc_html__( 'Pedidos no status · todos os períodos', 'marquesano-admin' ) . '</p></div>';
			}
			echo '</div><section class="ms-card"><h2>' . esc_html__( 'Últimos pedidos', 'marquesano-admin' ) . '</h2>';
			$orders = wc_get_orders( array( 'limit' => 5, 'orderby' => 'date', 'order' => 'DESC', 'type' => 'shop_order' ) );
			if ( ! $orders ) {
				echo '<p>' . esc_html__( 'Nenhum pedido encontrado.', 'marquesano-admin' ) . '</p>';
			} else {
				echo '<div class="ms-table"><table class="widefat striped"><thead><tr><th scope="col">' . esc_html__( 'Pedido', 'marquesano-admin' ) . '</th><th scope="col">' . esc_html__( 'Data', 'marquesano-admin' ) . '</th><th scope="col">' . esc_html__( 'Status', 'marquesano-admin' ) . '</th><th scope="col">' . esc_html__( 'Total do pedido', 'marquesano-admin' ) . '</th></tr></thead><tbody>';
				foreach ( $orders as $order ) {
					$date = $order->get_date_created();
					echo '<tr><td><a href="' . esc_url( $order->get_edit_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a></td><td>' . esc_html( $date ? wc_format_datetime( $date ) : '—' ) . '</td><td>' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</td><td>' . wp_kses_post( $order->get_formatted_order_total() ) . '</td></tr>';
				}
				echo '</tbody></table></div>';
			}
			echo '</section>';
		} catch ( \Throwable $error ) {
			echo '<div class="notice notice-error inline"><p>' . esc_html__( 'Não foi possível consultar os pedidos. Confira o status do WooCommerce na gestão da loja.', 'marquesano-admin' ) . '</p></div>';
		}
	}
	echo '<section class="ms-card"><h2>' . esc_html__( 'Páginas essenciais', 'marquesano-admin' ) . '</h2><ul class="ms-checklist">';
	foreach ( array( 'shop' => __( 'Catálogo', 'marquesano-admin' ), 'cart' => __( 'Carrinho', 'marquesano-admin' ), 'checkout' => __( 'Checkout', 'marquesano-admin' ), 'myaccount' => __( 'Minha conta', 'marquesano-admin' ) ) as $key => $label ) {
		$id = wc_get_page_id( $key );
		$page = $id > 0 ? get_post( $id ) : null;
		$ok = $page && 'publish' === $page->post_status;
		$message = $ok ? __( 'Página publicada', 'marquesano-admin' ) : __( 'Configuração manual necessária em WooCommerce → Configurações', 'marquesano-admin' );
		if ( $ok && 'shop' !== $key ) {
			$block = array( 'cart' => 'woocommerce/cart', 'checkout' => 'woocommerce/checkout', 'myaccount' => '' );
			if ( ! has_shortcode( $page->post_content, 'woocommerce_' . $key ) && ( ! $block[ $key ] || ! has_block( $block[ $key ], $page ) ) ) {
				$message = __( 'Revisar conteúdo: bloco ou shortcode nativo não detectado', 'marquesano-admin' );
			}
		}
		echo '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $message );
		if ( $ok ) { echo ' <a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html__( 'Abrir', 'marquesano-admin' ) . '</a>'; }
		echo '</li>';
	}
	echo '</ul><p>' . esc_html__( 'Este diagnóstico não valida pagamentos, frete ou conclusão de compra.', 'marquesano-admin' ) . '</p></section>';
	echo '<div class="ms-grid">';
	native_link( __( 'Gestão da loja', 'marquesano-admin' ), __( 'Produtos, estoque, pedidos, clientes e configurações.', 'marquesano-admin' ), 'admin.php?page=marquesano-management', 'manage_woocommerce' );
	native_link( __( 'Personalização', 'marquesano-admin' ), __( 'Template, identidade, banners, seções e contato.', 'marquesano-admin' ), 'admin.php?page=marquesano-appearance', 'edit_theme_options' );
	echo '</div></div>';
}
