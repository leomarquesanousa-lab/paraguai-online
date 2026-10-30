<?php
/** Account presentation only; native authentication, ownership checks and forms stay intact. */
defined( 'ABSPATH' ) || exit;

// Wording on native customer forms only; do not alter third-party translation files.
add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	if ( ! is_admin() && did_action( 'wp' ) && function_exists( 'is_account_page' ) && is_account_page() ) {
		$labels = array( 'Login' => 'Entrar', 'Log in' => 'Entrar', 'Register' => 'Criar Conta' );
		return $labels[ $text ] ?? $translation;
	}
	return $translation;
}, 10, 2 );
add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	$labels = array( 'dashboard' => 'Visão geral', 'orders' => 'Meus Pedidos', 'downloads' => 'Downloads', 'edit-address' => 'Endereços', 'edit-account' => 'Dados pessoais', 'customer-logout' => 'Sair' );
	foreach ( $labels as $key => $label ) { if ( isset( $items[ $key ] ) ) { $items[ $key ] = $label; } }
	return $items;
} );

add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_account_page' ) && is_account_page() ) { $classes[] = 'po-account'; }
	return $classes;
} );
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) { return; }
	$file = '/assets/css/account.css';
	wp_enqueue_style( 'paraguai-account', get_template_directory_uri() . $file, array( 'marquesano-store' ), (string) filemtime( get_template_directory() . $file ) );
}, 30 );

// Short-lived local fixtures may preview registration without enabling it for visitors.
if ( ! empty( $_SERVER['HTTP_X_MARQUESANO_AUDIT'] ) ) {
	$po_account_audit = get_option( 'marquesano_audit_context', array() );
	if ( ! empty( $po_account_audit['account'] ) && ! empty( $po_account_audit['secret'] ) && time() - (int) ( $po_account_audit['started'] ?? 0 ) < HOUR_IN_SECONDS && 'paraguai-online.local' === wp_parse_url( home_url(), PHP_URL_HOST ) && hash_equals( $po_account_audit['secret'], (string) $_SERVER['HTTP_X_MARQUESANO_AUDIT'] ) ) { require_once dirname( __DIR__ ) . '/tools/account-test-context.php'; }
	unset( $po_account_audit );
}
