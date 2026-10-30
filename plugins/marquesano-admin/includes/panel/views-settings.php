<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

function screen_appearance() {
	if ( ! function_exists( 'marquesano_store_fields' ) || 'marquesano-store' !== get_template() ) { throw new \RuntimeException( 'Tema Marquesano Store indisponível.' ); }
	echo '<div class="mp-heading"><div><p class="mp-eyebrow">IDENTIDADE E CONTEÚDO</p><h1>Aparência da loja</h1></div><a href="' . esc_url( home_url( '/' ) ) . '">Ver loja ↗</a></div><p>As opções são as mesmas utilizadas pelo tema. Salvar aplica a loja atual sem trocar o motor WooCommerce. Use preset na ordem das seções para herdar o layout escolhido.</p>';
	form_start( 'appearance_save' ); echo '<section class="mp-card"><h2>Identidade</h2>'; input( 'blogname', 'Nome da loja', get_bloginfo( 'name' ), 'text', 'required' ); echo '<div class="mp-form-grid">'; image_picker( 'custom_logo', 'Logotipo', get_theme_mod( 'custom_logo' ) ); image_picker( 'site_icon', 'Favicon (quadrado, mínimo 512px)', get_option( 'site_icon' ) ); echo '</div></section>';
	$settings = get_option( 'marquesano_store_settings', array() ); $settings = is_array( $settings ) ? $settings : array();
	screen_hero_banners( $settings );
	$groups = array( 'general' => array(), 'tech' => array() ); foreach ( marquesano_store_fields() as $key => $field ) { $groups[ 0 === strpos( $key, 'tech_' ) ? 'tech' : 'general' ][ $key ] = $field; }
	foreach ( $groups as $group => $fields ) {
		echo '<details class="mp-card" ' . ( 'general' === $group ? 'open' : '' ) . '><summary><h2>' . ( 'general' === $group ? 'Templates, conteúdo e contato' : 'Direção de arte TECH' ) . '</h2></summary><div class="mp-form-grid">';
		foreach ( $fields as $key => $field ) {
			if ( in_array( $key, array( 'tech_hero_image', 'tech_hero_images' ), true ) || ( 'hero_image' === $key && 'tech' === marquesano_store_setting( 'preset' ) ) ) { continue; }
			$name = 'appearance[' . $key . ']'; $value = $settings[ $key ] ?? $field['default'];
			if ( 'images' === $field['type'] ) { image_picker( $name, $field['label'], $value, true, true ); echo '<p class="mp-muted">' . esc_html( paraguai_tech_hero_image_guidance() ) . '</p>'; }
			elseif ( 'image' === $field['type'] ) { image_picker( $name, $field['label'], $value ); }
			elseif ( 'select' === $field['type'] ) { select( $name, $field['label'], $field['choices'], $value ); }
			elseif ( 'textarea' === $field['type'] ) { textarea( $name, $field['label'], $value ); }
			else { $type = in_array( $field['type'], array( 'url', 'email', 'number' ), true ) ? $field['type'] : 'text'; input( $name, $field['label'], $value, $type, 'number' === $type ? 'min="0" step="1"' : '' ); }
		}
		echo '</div></details>';
	}
	echo '<div class="mp-form-actions"><button>Salvar aparência</button></div></form>';
}
function screen_business() {
	echo '<h1>Configurações comerciais</h1><p>Estas opções são salvas diretamente nas configurações do WooCommerce. Alterar moeda não converte preços já cadastrados.</p>'; form_start( 'business_save', 'data-confirm="Aplicar as configurações comerciais à loja? Revise país, moeda e estoque antes de confirmar."' ); echo '<section class="mp-card"><div class="mp-form-grid">';
	foreach ( Settings::business_fields() as $key => $label ) {
		$value = get_option( $key, '' );
		if ( 'woocommerce_currency' === $key ) { select( $key, $label, get_woocommerce_currencies(), $value ); }
		else { input( $key, $label, $value, in_array( $key, array( 'woocommerce_price_num_decimals', 'woocommerce_notify_low_stock_amount', 'woocommerce_notify_no_stock_amount' ), true ) ? 'number' : 'text', 'woocommerce_store_address_2' === $key ? '' : 'required' ); }
	}
	echo '</div><button>Salvar configurações</button></section></form><section class="mp-card"><h2>Configurações com acompanhamento técnico</h2><p>Gateways, credenciais, zonas/tarifas de frete, tributos, e-mails transacionais, políticas, menus e páginas institucionais continuam nos mecanismos nativos e exigem o responsável técnico nesta versão. O painel não habilita meios de pagamento nem altera o modo Em breve.</p></section>';
}

function screen_hero_banners( $settings ) {
    if ( ! function_exists( 'paraguai_tech_hero_reference_id' ) ) { return; }
    $original = paraguai_tech_hero_reference_id( $settings );
    $saved = marquesano_store_sanitize_setting( $settings['tech_hero_image'] ?? 0, 'tech_hero_image' );
    $extra = implode( ',', array_values( array_diff( ids( $settings['tech_hero_images'] ?? '' ), array( $original ) ) ) );
    echo '<section id="po-hero-banners" class="mp-card po-hero-banners" data-adapt-nonce="' . esc_attr(wp_create_nonce('marquesano_panel_hero_adapt')) . '" data-original-id="' . (int) $original . '"><p class="mp-eyebrow">TECH DO TEMPLATE</p><h2>BANNERS DO HERO</h2><p>O Banner 1 é o original e permanece em primeiro lugar. Use <strong>Adicionar banner</strong> para incluir novas imagens sem substituí-lo.</p><p class="mp-muted">Exibição: <strong>5 segundos</strong> · Crossfade por opacidade: <strong>1,2 segundo</strong>. Até 20 banners adicionais.</p>';
    if ( $original ) {
        echo '<div class="po-hero-current po-hero-banner-original"><strong>Banner 1 — Original</strong><input type="hidden" name="appearance[tech_hero_image]" value="' . (int) $saved . '">' . wp_get_attachment_image( $original, 'medium_large', false, array( 'loading' => 'eager', 'alt' => get_the_title( $original ) ) ) . '<span class="mp-banner-name">' . esc_html( get_the_title( $original ) ?: 'Imagem #' . $original ) . '</span><small>Imagem selecionada: #' . (int) $original . ' · Original preservado</small></div>';
    } else {
        image_picker( 'appearance[tech_hero_image]', 'Banner 1 — selecionar imagem original', $saved, false, false, 'medium_large' );
    }
    image_picker( 'appearance[tech_hero_images]', 'Galeria de banners adicionais', $extra, true, true, 'medium_large', 'hero-gallery' );
    echo '<p class="mp-muted">' . esc_html( 'Imagens de outras proporções: prévia com recorte ajustável antes de criar uma cópia WebP de 1896 × 829 px. O arquivo original é preservado. Imagens pequenas podem perder nitidez ao ampliar.' ) . '</p><p>As setas mudam a ordem dos banners adicionais. Remover um banner retira apenas sua seleção, sem excluir a mídia.</p><button type="submit">Salvar alterações</button></section>';
}
