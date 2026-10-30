<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;
foreach ( array( 'views-catalog', 'views-commerce', 'views-settings' ) as $view ) { require_once __DIR__ . '/' . $view . '.php'; }

final class Views {
	public static function menu() {
		return array( '' => array( 'Visão geral', 'access_panel' ), 'produtos' => array( 'Produtos', 'manage_products' ), 'categorias' => array( 'Categorias', 'manage_categories' ), 'estoque' => array( 'Estoque', 'manage_products' ), 'pedidos' => array( 'Pedidos', 'manage_orders' ), 'clientes' => array( 'Clientes', 'view_customers' ), 'navegacao' => array( 'Navegação', 'manage_appearance' ), 'aparencia' => array( 'Aparência', 'manage_appearance' ), 'configuracoes' => array( 'Configurações', 'manage_settings' ) );
	}
	public static function render() {
		$base = dirname( __DIR__, 2 ); $plugin = $base . '/marquesano-admin.php';
		wp_enqueue_style( 'marquesano-panel', plugins_url( 'assets/panel.css', $plugin ), array(), (string) filemtime( $base . '/assets/panel.css' ) );
		$deps = array();
		if ( 'product' === Router::$route ) { wp_enqueue_script( 'marquesano-category-picker', plugins_url( 'assets/category-picker.js', $plugin ), array(), (string) filemtime( $base . '/assets/category-picker.js' ), true ); $deps[] = 'marquesano-category-picker'; }
		wp_enqueue_script( 'marquesano-panel', plugins_url( 'assets/panel.js', $plugin ), $deps, (string) filemtime( $base . '/assets/panel.js' ), true );
		$allowed = is_user_logged_in() && can( 'access_panel' );
		?><!doctype html><html lang="pt-BR"><head><meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Painel Paraguai Online · <?php echo esc_html( get_bloginfo( 'name' ) ); ?></title><?php wp_print_styles( array( 'marquesano-panel' ) ); wp_site_icon(); ?></head><body class="mp-body" data-upload-url="<?php echo esc_url( url() ); ?>" data-upload-nonce="<?php echo esc_attr( wp_create_nonce( 'marquesano_panel_upload' ) ); ?>">
		<a class="mp-skip" href="#mp-main">Pular para o conteúdo</a>
		<?php if ( $allowed ) : ?><div class="mp-layout"><aside class="mp-sidebar"><a class="mp-brand" href="<?php echo esc_url( url() ); ?>"><?php echo panel_brand_image(); ?><span>OPERAÇÃO DA LOJA</span></a><nav aria-label="Módulos do painel"><?php foreach ( self::menu() as $path => $item ) { if ( can( $item[1] ) ) { $active = ( '' === $path && 'dashboard' === Router::$route ) || ( $path && false !== strpos( (string) ( $GLOBALS['wp']->request ?? '' ), 'painel/' . $path ) ); echo '<a ' . ( $active ? 'aria-current="page"' : '' ) . ' href="' . esc_url( url( $path ) ) . '">' . esc_html( $item[0] ) . '</a>'; } } ?></nav><div class="mp-sidebar-bottom"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ver loja ↗</a><?php if ( current_user_can( 'manage_options' ) ) : ?><a href="<?php echo esc_url( admin_url() ); ?>">Administração técnica ↗</a><?php endif; ?><?php form_start( 'logout' ); ?><button type="submit" class="mp-logout">Sair com segurança</button></form></div></aside><div class="mp-workspace"><header class="mp-topbar"><span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span><span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span></header><?php endif; ?>
		<main id="mp-main" class="<?php echo $allowed ? 'mp-main' : 'mp-auth'; ?>">
		<?php if ( Router::$error ) { echo '<div class="mp-notice mp-error" role="alert">' . esc_html( Router::$error ) . '</div>'; }
		$messages = array( 'salvo' => 'Alterações salvas com sucesso.', 'lixeira' => 'Produto enviado à lixeira nativa. A restauração pode ser feita pelo administrador técnico.', 'recuperacao' => 'Se a conta estiver cadastrada e habilitada, você receberá instruções por e-mail.', 'senha' => 'Senha redefinida. Entre com sua nova senha.' );
		$ok = text( $_GET, 'ok' ); if ( isset( $messages[ $ok ] ) ) { echo '<div class="mp-notice" role="status">' . esc_html( $messages[ $ok ] ) . '</div>'; }
		try { $screen = __NAMESPACE__ . '\\screen_' . Router::$route; if ( function_exists( $screen ) ) { $screen(); } else { echo '<h1>Página não encontrada</h1><a href="' . esc_url( url() ) . '">Voltar ao painel</a>'; } }
		catch ( \Throwable $error ) { echo '<div class="mp-notice mp-error" role="alert">' . esc_html( $error instanceof \RuntimeException ? $error->getMessage() : 'Não foi possível carregar esta área. Contate o responsável técnico.' ) . '</div>'; }
		?></main><?php if ( $allowed ) : ?></div></div><?php self::library(); endif; ?><?php wp_print_scripts( array( 'marquesano-panel' ) ); ?></body></html><?php
	}
	public static function library() {
		if ( ! can( 'manage_products' ) && ! can( 'manage_appearance' ) ) { return; }
		$images = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => array( 'image/jpeg', 'image/png', 'image/webp' ), 'posts_per_page' => 60, 'orderby' => 'date', 'order' => 'DESC' ) );
		echo '<dialog id="mp-library"><div class="mp-heading"><h2>Biblioteca de imagens</h2><button type="button" data-close-library>Fechar</button></div><p>Selecione uma das 60 imagens recentes ou envie uma nova no formulário.</p><div class="mp-library-grid">';
		foreach ( $images as $image ) { echo '<button type="button" data-select-image="' . esc_attr( $image->ID ) . '" data-image-url="' . esc_url( wp_get_attachment_image_url( $image->ID, 'thumbnail' ) ) . '" data-image-preview-url="' . esc_url( wp_get_attachment_image_url( $image->ID, 'medium_large' ) ) . '">' . wp_get_attachment_image( $image->ID, 'thumbnail' ) . '<span>' . esc_html( $image->post_title ) . '</span></button>'; }
		echo '</div></dialog>';
	}
}

function screen_denied() { echo '<h1>Acesso indisponível</h1><p>Confira sua permissão ou entre novamente.</p><a href="' . esc_url( url( 'login' ) ) . '">Ir para o login</a>'; }
function screen_login() {
	echo '<section class="mp-auth-card mp-auth-tech">' . panel_brand_link() . '<p class="mp-eyebrow">GESTÃO DA LOJA</p><h1>Seu negócio.<br>Em suas mãos.</h1><p>Acesse o painel com sua conta de lojista ou administrador.</p>';
	form_start( 'login' ); input( 'identity', 'E-mail ou usuário', '', 'text', 'required autocomplete="username"' ); input( 'password', 'Senha', '', 'password', 'required autocomplete="current-password"' );
	echo '<label class="mp-check"><input type="checkbox" name="remember" value="1">Manter conectado neste dispositivo</label><button type="submit">Entrar no painel</button></form><a href="' . esc_url( url( 'recuperar' ) ) . '">Esqueci minha senha</a><a href="' . esc_url( home_url( '/' ) ) . '">Voltar à loja ↗</a></section>';
}
function screen_recover() { echo '<section class="mp-auth-card">' . panel_brand_link() . '<p class="mp-eyebrow">ACESSO À LOJA</p><h1>Recuperar acesso</h1>'; form_start( 'recover' ); input( 'identity', 'E-mail ou usuário', '', 'text', 'required autocomplete="username"' ); echo '<button type="submit">Enviar instruções</button></form><a href="' . esc_url( url( 'login' ) ) . '">Voltar ao login</a></section>'; }
function screen_reset() { echo '<section class="mp-auth-card">' . panel_brand_link() . '<h1>Definir nova senha</h1>'; form_start( 'reset' ); echo '<input type="hidden" name="key" value="' . esc_attr( text( $_GET, 'key' ) ) . '"><input type="hidden" name="login" value="' . esc_attr( text( $_GET, 'login' ) ) . '">'; input( 'password', 'Nova senha', '', 'password', 'required minlength="12" autocomplete="new-password"' ); input( 'password_confirm', 'Confirmar nova senha', '', 'password', 'required minlength="12" autocomplete="new-password"' ); echo '<button type="submit">Salvar nova senha</button></form></section>'; }
function screen_dashboard() {
	echo '<div class="mp-heading"><div><p class="mp-eyebrow">SUA OPERAÇÃO, EM PERSPECTIVA</p><h1>Visão geral</h1></div><a class="mp-secondary" href="' . esc_url( home_url( '/' ) ) . '">Abrir loja ↗</a></div>';
	if ( ! can( 'manage_orders' ) ) { echo '<p>Use o menu para acessar os módulos habilitados para sua conta.</p>'; return; }
	$data = Commerce::metrics();
	echo '<section class="mp-card"><h2>Pedidos pagos nos últimos 30 dias</h2><p class="mp-muted">Valores líquidos dos reembolsos registrados, pela data de pagamento do WooCommerce. Não representa conciliação bancária.</p>';
	if ( ! $data['totals'] ) { echo '<strong class="mp-metric">' . wp_kses_post( wc_price( 0 ) ) . '</strong>'; }
	foreach ( $data['totals'] as $currency => $total ) { echo '<strong class="mp-metric">' . wp_kses_post( wc_price( $total, array( 'currency' => $currency ) ) ) . '</strong>'; }
	echo '</section><div class="mp-metrics">';
	$labels = array( 'pending' => 'Aguardando pagamento', 'on-hold' => 'Pagamento em análise', 'processing' => 'Pagos · em processamento', 'completed' => 'Concluídos', 'cancelled' => 'Cancelados', 'refunded' => 'Reembolsados', 'failed' => 'Falha de pagamento' );
	foreach ( $data['counts'] as $status => $count ) { echo '<div class="mp-card"><span>' . esc_html( $labels[ $status ] ) . '</span><strong class="mp-metric">' . esc_html( number_format_i18n( $count ) ) . '</strong><small>Pedidos em todos os períodos</small></div>'; }
	echo '</div>'; if ( 'yes' === get_option( 'woocommerce_coming_soon' ) ) { echo '<div class="mp-notice">A loja está em modo Em breve. Essa configuração foi preservada.</div>'; }
}

function panel_brand_image() { return function_exists( 'paraguai_online_brand_image' ) ? (paraguai_online_brand_image( 'eager' ) ?: esc_html( 'Paraguai Online' )) : esc_html( 'Paraguai Online' ); }
function panel_brand_link() { return '<a class="mp-auth-brand" href="' . esc_url( home_url( '/' ) ) . '">' . panel_brand_image() . '</a>'; }
