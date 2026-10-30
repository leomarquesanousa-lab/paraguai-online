<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

final class Commerce {
	public static function metrics() {
		need( 'manage_orders' );
		$cached = get_transient( 'marquesano_panel_metrics' ); if ( is_array( $cached ) ) { return $cached; }
		$totals = array(); $page = 1; $after = time() - 30 * DAY_IN_SECONDS;
		do {
			$result = wc_get_orders( array( 'type' => 'shop_order', 'status' => array_unique( array_merge( wc_get_is_paid_statuses(), array( 'refunded' ) ) ), 'date_paid' => '>=' . $after, 'limit' => 100, 'paged' => $page, 'paginate' => true ) );
			foreach ( $result->orders as $order ) {
				$currency = $order->get_currency(); $totals[ $currency ] = ( $totals[ $currency ] ?? 0 ) + max( 0, (float) $order->get_total() - (float) $order->get_total_refunded() );
			}
			$page++;
		} while ( $page <= $result->max_num_pages );
		$counts = array(); foreach ( array( 'pending', 'on-hold', 'processing', 'completed', 'cancelled', 'refunded', 'failed' ) as $status ) { $counts[ $status ] = wc_orders_count( $status ); }
		$data = array( 'totals' => $totals, 'counts' => $counts ); set_transient( 'marquesano_panel_metrics', $data, MINUTE_IN_SECONDS ); return $data;
	}
	public static function order( $id ) {
		$order = wc_get_order( $id ); if ( ! $order || 'shop_order' !== $order->get_type() ) { throw new \RuntimeException( 'Pedido não encontrado.', 404 ); } return $order;
	}
	public static function transitions( $order ) {
		$map = array( 'pending' => array( 'on-hold', 'processing', 'completed', 'cancelled' ), 'on-hold' => array( 'processing', 'completed', 'cancelled' ), 'processing' => array( 'completed' ), 'failed' => array( 'on-hold', 'cancelled' ) );
		return $map[ $order->get_status() ] ?? array();
	}
	public static function save_order( $data ) {
		need( 'manage_orders' ); $order = self::order( number( $data, 'id' ) ); Products::check_version( $order, $data );
		$status = text( $data, 'status' ); $current = $order->get_status();
		if ( $status !== $current ) {
			if ( ! in_array( $status, self::transitions( $order ), true ) || empty( $data['confirm'] ) ) { throw new \RuntimeException( 'Transição não permitida ou não confirmada.' ); }
			if ( 'cancelled' === $status && $order->is_paid() ) { throw new \RuntimeException( 'Pedidos pagos exigem análise e fluxo de reembolso pelo responsável técnico.' ); }
			if ( in_array( $status, array( 'processing', 'completed' ), true ) && ! $order->is_paid() ) {
				if ( empty( $data['confirm_payment'] ) ) { throw new \RuntimeException( 'Confirme que o pagamento foi efetivamente verificado antes de marcar como pago.' ); }
				if ( ! $order->payment_complete() || ! $order->is_paid() ) { throw new \RuntimeException( 'Não foi possível confirmar o pagamento pelo WooCommerce. Recarregue o pedido.' ); }
			}
			if ( $status !== $order->get_status() && ! $order->update_status( $status, 'Status atualizado pelo painel Marquesano.', true ) ) { throw new \RuntimeException( 'A atualização do status falhou. Recarregue o pedido.' ); }
		}
		$note = sanitize_textarea_field( value( $data, 'note' ) ); if ( $note && ! $order->add_order_note( $note, false, true ) ) { throw new \RuntimeException( 'A nota interna não pôde ser salva. Confira o status atual antes de tentar novamente.' ); }
		delete_transient( 'marquesano_panel_metrics' ); return $order->get_id();
	}
}
add_action( 'woocommerce_after_order_object_save', static function () { delete_transient( 'marquesano_panel_metrics' ); } );
