<?php
/** Shared presentation settings. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;

function marquesano_store_presets() {
	return array(
		'tech'    => array( 'label' => 'TECH', 'primary' => '#315ded', 'accent' => '#dfe2e2', 'background' => '#f7f6f2', 'text' => '#202425', 'font' => 'system', 'radius' => '8px', 'space' => '4rem' ),
		'fashion' => array( 'label' => 'FASHION', 'primary' => '#282723', 'accent' => '#e5dfd4', 'background' => '#faf9f6', 'text' => '#282723', 'font' => 'serif', 'radius' => '0px', 'space' => '7rem' ),
		'beauty'  => array( 'label' => 'BEAUTY', 'primary' => '#673c48', 'accent' => '#ecddd5', 'background' => '#fcf8f4', 'text' => '#422f36', 'font' => 'serif', 'radius' => '18px', 'space' => '6rem' ),
		'home'    => array( 'label' => 'HOME', 'primary' => '#3c5145', 'accent' => '#e5dac5', 'background' => '#f7f5ee', 'text' => '#343e35', 'font' => 'serif', 'radius' => '4px', 'space' => '6rem' ),
	);
}

function marquesano_store_fields() {
	// The schema is deliberately independent of get_option(). Customizer calls
	// sanitizers while filtering that same option; reading it here recurses.
	$fields = array(
		'preset' => array( 'label' => __( 'Template visual', 'marquesano-store' ), 'type' => 'select', 'default' => 'tech', 'choices' => array( 'tech' => 'TECH', 'fashion' => 'FASHION', 'beauty' => 'BEAUTY', 'home' => 'HOME' ) ),
		'primary' => array( 'label' => __( 'Cor principal (vazio usa o preset)', 'marquesano-store' ), 'type' => 'color', 'default' => '' ),
		'accent' => array( 'label' => __( 'Cor de destaque', 'marquesano-store' ), 'type' => 'color', 'default' => '' ),
		'background' => array( 'label' => __( 'Cor de fundo', 'marquesano-store' ), 'type' => 'color', 'default' => '' ),
		'text' => array( 'label' => __( 'Cor do texto', 'marquesano-store' ), 'type' => 'color', 'default' => '' ),
		'font' => array( 'label' => __( 'Tipografia', 'marquesano-store' ), 'type' => 'select', 'default' => '', 'choices' => array( '' => __( 'Do template', 'marquesano-store' ), 'system' => 'System sans', 'serif' => 'Georgia / serif', 'humanist' => 'Trebuchet / sans', 'mono' => 'Consolas / mono' ) ),
		'announcement' => array( 'label' => __( 'Aviso comercial (opcional)', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'hero_title' => array( 'label' => __( 'Título do hero', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'hero_text' => array( 'label' => __( 'Texto do hero', 'marquesano-store' ), 'type' => 'textarea', 'default' => '' ),
		'hero_image' => array( 'label' => __( 'Imagem do hero', 'marquesano-store' ), 'type' => 'image', 'default' => 0 ),
		'hero_button' => array( 'label' => __( 'Texto do botão principal', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'hero_url' => array( 'label' => __( 'Destino do botão (vazio usa catálogo)', 'marquesano-store' ), 'type' => 'url', 'default' => '' ),
		'sections' => array( 'label' => __( 'Ordem das seções', 'marquesano-store' ), 'type' => 'text', 'default' => 'preset' ),
		'products_title' => array( 'label' => __( 'Título da coleção na home', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'products_mode' => array( 'label' => __( 'Coleção da home', 'marquesano-store' ), 'type' => 'select', 'default' => 'recent', 'choices' => array( 'recent' => __( 'Recentes', 'marquesano-store' ), 'featured' => __( 'Destaques', 'marquesano-store' ), 'sale' => __( 'Em promoção', 'marquesano-store' ) ) ),
		'banner_image' => array( 'label' => __( 'Imagem do banner secundário', 'marquesano-store' ), 'type' => 'image', 'default' => 0 ),
		'banner_title' => array( 'label' => __( 'Título do banner secundário', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'banner_text' => array( 'label' => __( 'Texto do banner secundário', 'marquesano-store' ), 'type' => 'textarea', 'default' => '' ),
		'banner_url' => array( 'label' => __( 'Link do banner secundário', 'marquesano-store' ), 'type' => 'url', 'default' => '' ),
		'about_title' => array( 'label' => __( 'Título institucional da home', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'about_text' => array( 'label' => __( 'Texto institucional da home', 'marquesano-store' ), 'type' => 'textarea', 'default' => '' ),
		'email' => array( 'label' => __( 'E-mail de contato', 'marquesano-store' ), 'type' => 'email', 'default' => '' ),
		'phone' => array( 'label' => __( 'Telefone de contato', 'marquesano-store' ), 'type' => 'text', 'default' => '' ),
		'address' => array( 'label' => __( 'Endereço comercial', 'marquesano-store' ), 'type' => 'textarea', 'default' => '' ),
		'instagram' => array( 'label' => 'Instagram URL', 'type' => 'url', 'default' => '' ),
		'facebook' => array( 'label' => 'Facebook URL', 'type' => 'url', 'default' => '' ),
		'whatsapp' => array( 'label' => 'WhatsApp URL', 'type' => 'url', 'default' => '' ),
	);
	foreach ( array( 'hero_title', 'hero_text', 'hero_button', 'products_title' ) as $key ) {
		$fields[ $key ]['label'] .= ' ' . __( '(vazio usa o template)', 'marquesano-store' );
	}
	$fields['sections']['label'] = __( 'Use preset para a ordem do template; ou organize hero,categories,offers,products,spotlight,discovery,banner,about. spotlight e discovery são exclusivos do TECH.', 'marquesano-store' );
	return array_merge( $fields, marquesano_store_tech_fields() );
}

function marquesano_store_sanitize_setting( $value, $key ) {
	$fields = marquesano_store_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return '';
	}
	$field = $fields[ $key ];
	if ( ! is_scalar( $value ) ) { return $field['default']; }
	if ( 'select' === $field['type'] ) {
		return array_key_exists( (string) $value, $field['choices'] ) ? (string) $value : $field['default'];
	}
	if ( 'sections' === $key ) {
		if ( 'preset' === trim( (string) $value ) ) { return 'preset'; }
		return implode( ',', array_unique( array_intersect( array_map( 'trim', explode( ',', (string) $value ) ), array( 'hero', 'categories', 'offers', 'products', 'spotlight', 'discovery', 'banner', 'about' ) ) ) );
	}
	switch ( $field['type'] ) {
		case 'images': return implode( ',', array_unique( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) ) );
		case 'color': return sanitize_hex_color( $value ) ?: '';
		case 'image':
			// Accept legacy URL-based TECH controls, but persist an attachment ID.
			if ( 'tech_hero_image' === $key && '' !== trim( (string) $value ) && ! ctype_digit( (string) $value ) ) {
				return attachment_url_to_postid( esc_url_raw( $value ) );
			}
			return absint( $value );
		case 'number': return absint( $value );
		case 'url': return esc_url_raw( $value );
		case 'email': return sanitize_email( $value );
		case 'textarea': return sanitize_textarea_field( $value );
		default: return sanitize_text_field( $value );
	}
}

function marquesano_store_setting( $key ) {
	$fields = marquesano_store_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return '';
	}
	$stored = get_option( 'marquesano_store_settings', array() );
	$stored = is_array( $stored ) ? $stored : array();
	$preset = marquesano_store_sanitize_setting( $stored['preset'] ?? 'tech', 'preset' );
	$value = isset( $stored[ $key ] ) ? marquesano_store_sanitize_setting( $stored[ $key ], $key ) : $fields[ $key ]['default'];
	if ( 'sections' === $key && ( ! isset( $stored[ $key ] ) || 'preset' === $value ) ) {
		$layouts = array( 'tech' => 'hero,categories,offers,products,banner,about', 'fashion' => 'hero,categories,products,banner,about', 'beauty' => 'hero,products,banner,categories,about', 'home' => 'hero,categories,banner,products,about' );
		return $layouts[ $preset ];
	}
	if ( '' === $value && in_array( $key, array( 'hero_title', 'hero_text', 'hero_button', 'products_title' ), true ) ) {
		return marquesano_store_identity( $preset )[ $key ];
	}
	return $value;
}

function marquesano_store_css_variables() {
	$presets = marquesano_store_presets();
	$preset = $presets[ marquesano_store_setting( 'preset' ) ];
	$fonts = array( 'system' => 'system-ui, sans-serif', 'serif' => 'Georgia, serif', 'humanist' => '"Trebuchet MS", sans-serif', 'mono' => 'Consolas, monospace' );
	$font = marquesano_store_setting( 'font' ) ?: $preset['font'];
	$css = ':root{';
	foreach ( array( 'primary', 'accent', 'background', 'text' ) as $key ) {
		$css .= '--ms-' . $key . ':' . ( marquesano_store_setting( $key ) ?: $preset[ $key ] ) . ';';
	}
	if ( 'tech' === marquesano_store_setting( 'preset' ) ) {
		$ink = marquesano_store_setting( 'tech_surface_ink' ) ?: '#25292a';
		$css .= '--ms-tech-ink:' . $ink . ';--ms-tech-on-ink:' . marquesano_store_on_primary( $ink ) . ';--ms-tech-mineral:' . ( marquesano_store_setting( 'tech_surface_mineral' ) ?: '#e8e9e7' ) . ';';
	}
	return $css . '--ms-on-primary:' . marquesano_store_on_primary( marquesano_store_setting( 'primary' ) ?: $preset['primary'] ) . ';--ms-font:' . $fonts[ $font ] . ';--ms-radius:' . $preset['radius'] . ';--ms-space:' . $preset['space'] . ';}';
}

function marquesano_store_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}
