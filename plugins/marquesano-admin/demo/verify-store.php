<?php
/** Manual local verification. Run with wp eval-file --user=ADMIN. No order submission. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! current_user_can( 'manage_woocommerce' ) ) { return; }
$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
if ( ! preg_match( '/(^localhost$|^127\.0\.0\.1$|\.(local|test)$)/', $host ) ) { WP_CLI::error( 'Verificação restrita ao ambiente local.' ); }
$check = static function ( $ok, $message ) { if ( ! $ok ) { throw new RuntimeException( $message ); } WP_CLI::log( 'OK: ' . $message ); };
$registry = get_option( 'marquesano_demo_manifest_v1', array() );
$catalog = require __DIR__ . '/catalog.php';
$orders_before = wc_get_orders( array( 'limit' => 1, 'paginate' => true, 'return' => 'ids', 'status' => array_keys( wc_get_order_statuses() ) ) )->total;
$draft_before = wc_orders_count( 'checkout-draft' );
$soon_before = get_option( 'woocommerce_coming_soon' );
$stock_before = array();
$token = '';
$auth_token = '';
$guest_id = '';
$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
$original_settings = get_option( 'marquesano_store_settings', array() );
$original_settings = is_array( $original_settings ) ? $original_settings : array();
$override = null;
$request = static function ( $url, $args = array() ) use ( $check ) {
	$result = wp_remote_request( $url, array_merge( array( 'timeout' => 20, 'redirection' => 3 ), $args ) );
	if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
	$status = wp_remote_retrieve_response_code( $result );
	if ( $status < 200 || $status >= 300 ) { throw new RuntimeException( 'HTTP ' . $status . ' em ' . wp_parse_url( $url, PHP_URL_PATH ) . ': ' . substr( wp_strip_all_tags( wp_remote_retrieve_body( $result ) ), 0, 180 ) ); }
	if ( preg_match( '/Fatal error|Parse error/', wp_remote_retrieve_body( $result ) ) ) { throw new RuntimeException( 'Erro PHP visível.' ); }
	return $result;
};
try {
	$check( 12 === count( $registry ), 'Registro contém exatamente 12 produtos demonstrativos.' );
	foreach ( $catalog as $segment => $rows ) {
		foreach ( $rows as $row ) {
			$product = wc_get_product( $registry[ $row['key'] ] ?? 0 );
			$check( $product && 'publish' === $product->get_status() && 'marquesano-visual-v1' === $product->get_meta( '_marquesano_demo_batch' ) && $segment === $product->get_meta( '_marquesano_demo_segment' ) && (float) $product->get_regular_price() === (float) $row['price'] && $product->get_stock_quantity() === $row['stock'] && count( $product->get_attributes() ) === 2 && $product->get_description(), $row['key'] . ': dados, especificações, estoque e publicação local.' );
			$image = get_attached_file( $product->get_image_id() );
			$check( $image && file_exists( $image ) && getimagesize( $image )[0] === 1000, $row['key'] . ': imagem própria associada e válida.' );
			$stock_before[ $product->get_id() ] = $product->get_stock_quantity();
		}
		$override = static function () use ( $original_settings, $segment ) { return array_merge( $original_settings, array( 'preset' => $segment ) ); };
		add_filter( 'pre_option_marquesano_store_settings', $override );
		$html = do_shortcode( '[products limit="12" columns="4"]' );
		foreach ( $catalog as $other_segment => $other_rows ) {
			foreach ( $other_rows as $row ) {
				$found = false !== strpos( $html, 'post-' . $registry[ $row['key'] ] . ' ' );
				$check( $found === ( $segment === $other_segment ), $segment . ': segmentação de ' . $row['key'] );
			}
		}
		foreach ( $rows as $row ) {
			$search = new WP_Query( array( 'post_type' => 'product', 's' => $row['name'], 'fields' => 'ids', 'meta_query' => marquesano_store_demo_query( array() ) ) );
			$check( in_array( (int) $registry[ $row['key'] ], $search->posts, true ), $row['key'] . ': busca nativa encontra o produto no preset.' );
		}
		remove_filter( 'pre_option_marquesano_store_settings', $override ); $override = null;
	}
	$attachments = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_marquesano_demo_batch', 'meta_value' => 'marquesano-visual-v1' ) );
	$check( 12 === count( $attachments ), 'Exatamente 12 imagens demonstrativas, sem duplicação na reexecução.' );
	// Temporary manager session for read-only HTML checks; never output the cookie.
	$expires = time() + 600;
	$auth_token = $manager->create( $expires );
	$cookie = new WP_Http_Cookie( array( 'name' => LOGGED_IN_COOKIE, 'value' => wp_generate_auth_cookie( get_current_user_id(), $expires, 'logged_in', $auth_token ) ) );
	foreach ( $registry as $key => $id ) {
		$page = $request( get_permalink( $id ), array( 'cookies' => array( $cookie ) ) );
		$body = wp_remote_retrieve_body( $page );
		$has_title = false !== strpos( $body, 'product_title' ) || false !== strpos( $body, 'wp-block-post-title' );
		$has_image = false !== strpos( $body, wp_basename( get_attached_file( wc_get_product( $id )->get_image_id() ) ) );
		$check( $has_title && $has_image, $key . ': página individual HTTP 200 com título e imagem (título=' . (int) $has_title . ', imagem=' . (int) $has_image . ').' );
	}
	$shop = $request( wc_get_page_permalink( 'shop' ), array( 'cookies' => array( $cookie ) ) );
	$check( false !== strpos( wp_remote_retrieve_body( $shop ), 'catalog-filters' ), 'Catálogo autenticado renderiza filtros nativos.' );
	// Guest-only Store API cart: no customer account and no manager cart touched.
	$cart = $request( rest_url( 'wc/store/v1/cart' ) );
	$token = wp_remote_retrieve_header( $cart, 'cart-token' );
	$check( ! empty( $token ), 'Carrinho de teste anônimo recebe Cart-Token.' );
	$payload = \Automattic\WooCommerce\StoreApi\Utilities\CartTokenUtils::get_cart_token_payload( $token );
	$guest_id = $payload['user_id'] ?? '';
	$check( is_string( $guest_id ) && ! is_numeric( $guest_id ), 'Sessão de carrinho isolada, sem conta de cliente.' );
	$headers = array( 'Cart-Token' => $token, 'Content-Type' => 'application/json' );
	$added = $request( rest_url( 'wc/store/v1/cart/add-item' ), array( 'method' => 'POST', 'headers' => $headers, 'body' => wp_json_encode( array( 'id' => $registry['tech-01'], 'quantity' => 2 ) ) ) );
	$data = json_decode( wp_remote_retrieve_body( $added ), true );
	$check( 2 === $data['items_count'] && 1 === count( $data['items'] ), 'Carrinho: adição de duas unidades confirmada.' );
	$minor = $data['totals']['currency_minor_unit'];
	$expected = (int) round( (float) wc_get_product( $registry['tech-01'] )->get_price() * 2 * pow( 10, $minor ) );
	$check( (int) $data['totals']['total_items'] === $expected, 'Carrinho: subtotal corresponde ao preço promocional real × quantidade.' );
	$key = $data['items'][0]['key'];
	$updated = $request( rest_url( 'wc/store/v1/cart/update-item' ), array( 'method' => 'POST', 'headers' => $headers, 'body' => wp_json_encode( array( 'key' => $key, 'quantity' => 1 ) ) ) );
	$check( 1 === json_decode( wp_remote_retrieve_body( $updated ), true )['items_count'], 'Carrinho: atualização de quantidade confirmada.' );
	// In WooCommerce 11.2, GET with no existing draft builds a response without creating an order.
	$checkout = $request( rest_url( 'wc/store/v1/checkout' ), array( 'headers' => $headers ) );
	$checkout_data = json_decode( wp_remote_retrieve_body( $checkout ), true );
	$check( isset( $checkout_data['billing_address'], $checkout_data['shipping_address'] ) && empty( $checkout_data['order_id'] ), 'Checkout: GET 200 com campos de endereço e sem criação de pedido.' );
	$removed = $request( rest_url( 'wc/store/v1/cart/remove-item' ), array( 'method' => 'POST', 'headers' => $headers, 'body' => wp_json_encode( array( 'key' => $key ) ) ) );
	$check( 0 === json_decode( wp_remote_retrieve_body( $removed ), true )['items_count'], 'Carrinho: remoção confirmada, sessão vazia.' );
} finally {
	if ( $override ) { remove_filter( 'pre_option_marquesano_store_settings', $override ); }
	if ( $auth_token ) { $manager->destroy( $auth_token ); }
	if ( $guest_id && ! is_numeric( $guest_id ) ) { ( new WC_Session_Handler() )->delete_session( $guest_id ); }
}
$check( $orders_before === wc_get_orders( array( 'limit' => 1, 'paginate' => true, 'return' => 'ids', 'status' => array_keys( wc_get_order_statuses() ) ) )->total && $draft_before === wc_orders_count( 'checkout-draft' ), 'Nenhum pedido ou rascunho de pedido criado.' );
foreach ( $stock_before as $id => $stock ) { if ( wc_get_product( $id )->get_stock_quantity() !== $stock ) { throw new RuntimeException( 'Estoque alterado no teste.' ); } }
$check( $soon_before === get_option( 'woocommerce_coming_soon' ) && $original_settings === get_option( 'marquesano_store_settings', array() ), 'Estoque, preset salvo e modo Em breve preservados.' );
WP_CLI::success( 'Verificação concluída. Pagamento, frete e conclusão de compra não foram executados.' );
