<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

final class Access {
	public static function capabilities() { return array_fill_keys( array( 'read', 'marquesano_access_panel', 'marquesano_manage_products', 'marquesano_manage_categories', 'marquesano_manage_orders', 'marquesano_view_customers', 'marquesano_manage_appearance', 'marquesano_manage_settings', 'marquesano_upload_media' ), true ); }
	public static function register_role() { if ( ! get_role( 'marquesano_lojista' ) ) { add_role( 'marquesano_lojista', 'Lojista Marquesano', self::capabilities() ); } }
	public static function user_allowed( $user ) { return $user->has_cap( 'manage_options' ) || $user->has_cap( 'marquesano_access_panel' ); }
	public static function merchant() { return is_user_logged_in() && current_user_can( 'marquesano_access_panel' ) && ! current_user_can( 'manage_options' ); }
	public static function restrict_admin() {
		$file = basename( $_SERVER['PHP_SELF'] ?? '' );
		if ( ! self::should_block_admin( $file, wp_doing_ajax(), wp_doing_cron() ) ) { return; }
		wp_safe_redirect( url() ); exit;
	}
	public static function should_block_admin( $file, $ajax = false, $cron = false ) { return self::merchant() && ! $ajax && ! $cron && ! in_array( $file, array( 'admin-ajax.php', 'admin-post.php' ), true ); }
	public static function login( $data ) {
		$identity = text( $data, 'identity' );
		$key = 'mp_login_' . hash_hmac( 'sha256', ( $_SERVER['REMOTE_ADDR'] ?? '' ) . '|' . strtolower( $identity ), wp_salt( 'nonce' ) );
		$attempts = (int) get_transient( $key );
		$ip_key = 'mp_login_ip_' . hash_hmac( 'sha256', $_SERVER['REMOTE_ADDR'] ?? '', wp_salt( 'nonce' ) );
		$ip_attempts = (int) get_transient( $ip_key );
		if ( $attempts >= 5 || $ip_attempts >= 20 ) { throw new \RuntimeException( 'Aguarde 15 minutos antes de tentar novamente.' ); }
		$filter = static function ( $user ) { return $user instanceof \WP_User && ! Access::user_allowed( $user ) ? new \WP_Error( 'panel_forbidden', 'Acesso não autorizado.' ) : $user; };
		add_filter( 'authenticate', $filter, 99 );
		try { $user = wp_signon( array( 'user_login' => $identity, 'user_password' => value( $data, 'password' ), 'remember' => ! empty( $data['remember'] ) ), is_ssl() ); }
		finally { remove_filter( 'authenticate', $filter, 99 ); }
		if ( is_wp_error( $user ) ) { set_transient( $key, $attempts + 1, 15 * MINUTE_IN_SECONDS ); set_transient( $ip_key, $ip_attempts + 1, 15 * MINUTE_IN_SECONDS ); throw new \RuntimeException( 'Não foi possível entrar. Confira seus dados e sua permissão de acesso.' ); }
		delete_transient( $key );
		redirect();
	}
	public static function recover( $data ) {
		$rate_key = 'mp_recover_' . hash_hmac( 'sha256', $_SERVER['REMOTE_ADDR'] ?? '', wp_salt( 'nonce' ) ); $attempts = (int) get_transient( $rate_key );
		if ( $attempts >= 3 ) { throw new \RuntimeException( 'Aguarde uma hora antes de solicitar outra recuperação.' ); }
		set_transient( $rate_key, $attempts + 1, HOUR_IN_SECONDS );
		$identity = text( $data, 'identity' ); $user = is_email( $identity ) ? get_user_by( 'email', $identity ) : get_user_by( 'login', $identity );
		if ( ! $user || ! self::user_allowed( $user ) ) { redirect( 'login', array( 'ok' => 'recuperacao' ) ); }
		$filter = static function ( $message, $key, $login ) { return "Uma redefinição de senha foi solicitada.\n\n" . url( 'redefinir', array( 'key' => $key, 'login' => $login ) ) . "\n\nSe não foi você, ignore esta mensagem."; };
		add_filter( 'retrieve_password_message', $filter, 10, 3 );
		try { retrieve_password( text( $data, 'identity' ) ); } finally { remove_filter( 'retrieve_password_message', $filter, 10 ); }
		redirect( 'login', array( 'ok' => 'recuperacao' ) );
	}
	public static function reset( $data ) {
		$user = check_password_reset_key( value( $data, 'key' ), value( $data, 'login' ) );
		if ( is_wp_error( $user ) || ! self::user_allowed( $user ) ) { throw new \RuntimeException( 'Link inválido ou expirado. Solicite uma nova recuperação.' ); }
		$password = value( $data, 'password' );
		if ( strlen( $password ) < 12 || $password !== value( $data, 'password_confirm' ) ) { throw new \RuntimeException( 'Use pelo menos 12 caracteres e confirme a mesma senha.' ); }
		reset_password( $user, $password ); redirect( 'login', array( 'ok' => 'senha' ) );
	}
}
