<?php
namespace Marquesano\Admin\Panel;
require_once __DIR__ . '/hero-images.php';
defined( 'ABSPATH' ) || exit;

final class Router {
	public static $route = ''; public static $id = 0; public static $error = '';
	public static function resolve( $request ) {
		$path = trim( $request, '/' );
		if ( ! preg_match( '~^painel(?:/(.*))?$~D', $path, $match ) ) { return false; }
		$tail = trim( $match[1] ?? '', '/' );
		$map = array( '' => 'dashboard', 'login' => 'login', 'recuperar' => 'recover', 'redefinir' => 'reset', 'produtos' => 'products', 'produto/novo' => 'product', 'categorias' => 'categories', 'estoque' => 'stock', 'pedidos' => 'orders', 'clientes' => 'customers', 'navegacao' => 'navigation', 'aparencia' => 'appearance', 'configuracoes' => 'business' );
		if ( isset( $map[ $tail ] ) ) { return array( $map[ $tail ], 0 ); }
		if ( preg_match( '~^(produto|pedido|cliente)/(\d+)$~D', $tail, $parts ) ) { return array( array( 'produto' => 'product', 'pedido' => 'order', 'cliente' => 'customer' )[ $parts[1] ], absint( $parts[2] ) ); }
		return array( 'missing', 0 );
	}
	public static function capability( $route ) {
		$map = array( 'dashboard' => 'access_panel', 'products' => 'manage_products', 'product' => 'manage_products', 'categories' => 'manage_categories', 'stock' => 'manage_products', 'orders' => 'manage_orders', 'order' => 'manage_orders', 'customers' => 'view_customers', 'customer' => 'view_customers', 'navigation' => 'manage_appearance', 'appearance' => 'manage_appearance', 'business' => 'manage_settings' );
		return $map[ $route ] ?? 'access_panel';
	}
	public static function dispatch() {
		global $wp, $wp_query;
		$resolved = self::resolve( $wp->request ?? '' ); if ( false === $resolved ) { return; }
		list( self::$route, self::$id ) = $resolved;
		$wp_query->is_404 = false; status_header( 200 ); nocache_headers();
		header( 'Cache-Control: private, no-store, max-age=0' ); header( 'X-Robots-Tag: noindex, nofollow, noarchive' ); header( 'Referrer-Policy: no-referrer' ); header( 'X-Content-Type-Options: nosniff' ); header( 'X-Frame-Options: SAMEORIGIN' );
		$public = in_array( self::$route, array( 'login', 'recover', 'reset' ), true );
		if ( ! $public && ! is_user_logged_in() ) { wp_safe_redirect( url( 'login' ) ); exit; }
		try {
			if ( ! $public ) { need( 'access_panel' ); need( self::capability( self::$route ) ); }
			if ( ! $public && ! class_exists( 'WooCommerce' ) ) { throw new \RuntimeException( 'WooCommerce indisponível. Contate o responsável técnico.', 503 ); }
			if ( $public && is_user_logged_in() && can( 'access_panel' ) && 'reset' !== self::$route ) { redirect(); }
			if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { self::post( wp_unslash( $_POST ) ); }
		} catch ( \Throwable $error ) {
			self::$error = $error instanceof \RuntimeException || $error instanceof \WC_Data_Exception ? $error->getMessage() : 'Não foi possível concluir a operação. Confira os campos ou contate o responsável técnico.';
			$status = in_array( $error->getCode(), array( 403, 404, 409, 503 ), true ) ? $error->getCode() : 400; status_header( $status );
			if ( in_array( text( $_POST, 'panel_action' ), array( 'upload', 'hero_adapt' ), true ) ) { wp_send_json_error( array( 'message' => self::$error ), $status ); }
			if ( 403 === $status || 503 === $status ) { self::$route = 'denied'; }
		}
		if ( 'missing' === self::$route ) { status_header( 404 ); }
		Views::render(); exit;
	}
	public static function post( $data ) {
		$action = text( $data, 'panel_action' );
		if ( ! wp_verify_nonce( value( $data, '_wpnonce' ), 'marquesano_panel_' . $action ) ) { throw new \RuntimeException( 'Sua sessão de formulário expirou. Recarregue a página.', 403 ); }
		$auth = array( 'login' => 'login', 'recover' => 'recover', 'reset' => 'reset' );
		if ( isset( $auth[ $action ] ) ) { if ( self::$route !== $auth[ $action ] ) { throw new \RuntimeException( 'Rota inválida.', 403 ); } Access::$action( $data ); return; }
		need( 'access_panel' );
		switch ( $action ) {
			case 'logout': wp_logout(); redirect( 'login' ); break;
			case 'product_save': $id = Products::save( $data ); redirect( 'produto/' . $id, array( 'ok' => 'salvo' ) ); break;
			case 'product_trash': Products::trash( $data ); redirect( 'produtos', array( 'ok' => 'lixeira' ) ); break;
			case 'category_save': Products::category( $data ); redirect( 'categorias', array( 'ok' => 'salvo' ) ); break;
			case 'stock_save': Products::stock( $data ); redirect( 'estoque', array( 'ok' => 'salvo' ) ); break;
			case 'order_save': $id = Commerce::save_order( $data ); redirect( 'pedido/' . $id, array( 'ok' => 'salvo' ) ); break;
			case 'navigation_save': Navigation::save( $data ); redirect( 'navegacao', array( 'ok' => 'salvo' ) ); break;
			case 'appearance_save': Settings::appearance( $data ); redirect( 'aparencia', array( 'ok' => 'salvo' ) ); break;
			case 'business_save': Settings::business( $data ); redirect( 'configuracoes', array( 'ok' => 'salvo' ) ); break;
			case 'hero_adapt': wp_send_json_success( Hero_Images::handle( $data ) ); break;
			case 'upload': wp_send_json_success( Products::upload() ); break;
			default: throw new \RuntimeException( 'Operação desconhecida.', 400 );
		}
	}
}
