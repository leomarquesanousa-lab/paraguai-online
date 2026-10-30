<?php
/** Admin navigation and capability boundaries. @package MarquesanoAdmin */
namespace Marquesano\Admin;
defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_menu_page( __( 'Marquesano Store', 'marquesano-admin' ), 'Marquesano', 'manage_woocommerce', 'marquesano-admin', __NAMESPACE__ . '\\dashboard', 'dashicons-store', 56 );
	add_submenu_page( 'marquesano-admin', __( 'Visão geral', 'marquesano-admin' ), __( 'Visão geral', 'marquesano-admin' ), 'manage_woocommerce', 'marquesano-admin', __NAMESPACE__ . '\\dashboard' );
	add_submenu_page( 'marquesano-admin', __( 'Gestão da loja', 'marquesano-admin' ), __( 'Gestão da loja', 'marquesano-admin' ), 'manage_woocommerce', 'marquesano-management', __NAMESPACE__ . '\\management' );
	add_submenu_page( 'marquesano-admin', __( 'Personalização', 'marquesano-admin' ), __( 'Personalização', 'marquesano-admin' ), 'edit_theme_options', 'marquesano-appearance', __NAMESPACE__ . '\\appearance' );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( false === strpos( $hook, 'marquesano' ) ) { return; }
	wp_enqueue_style( 'marquesano-admin', plugins_url( 'assets/admin.css', dirname( __DIR__ ) . '/marquesano-admin.php' ), array(), (string) filemtime( dirname( __DIR__ ) . '/assets/admin.css' ) );
} );

function guard( $capability ) {
	if ( ! current_user_can( $capability ) ) {
		wp_die( esc_html__( 'Você não tem permissão para acessar esta área.', 'marquesano-admin' ), '', array( 'response' => 403 ) );
	}
}

function heading( $title ) {
	echo '<div class="wrap marquesano-admin"><p class="ms-eyebrow">MARQUESANO STORE 1.0</p><h1>' . esc_html( $title ) . '</h1>';
}

function native_link( $title, $description, $path, $capability ) {
	if ( ! current_user_can( $capability ) ) { return; }
	echo '<a class="ms-card ms-link" href="' . esc_url( admin_url( $path ) ) . '"><h2>' . esc_html( $title ) . ' <span aria-hidden="true">↗</span></h2><p>' . esc_html( $description ) . '</p></a>';
}

function woo_available() {
	if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_orders' ) ) { return true; }
	echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'WooCommerce indisponível. Ative uma versão compatível para gerenciar a loja. Nenhuma configuração foi alterada.', 'marquesano-admin' ) . '</p></div>';
	return false;
}
