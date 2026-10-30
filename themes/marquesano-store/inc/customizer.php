<?php
/** Native persistent customization; WordPress handles nonces and permissions. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function ( $customizer ) {
	$customizer->add_section( 'marquesano_store', array( 'title' => __( 'Marquesano • Loja', 'marquesano-store' ), 'priority' => 30, 'description' => __( 'O template altera a prévia completa. Campos vazios de cor, fonte e textos do hero usam a identidade selecionada. Use preset no campo de seções para herdar sua composição. Logo e favicon ficam em Identidade do site. Salve no Personalizador para aplicar à loja.', 'marquesano-store' ) ) );
	$customizer->add_section( 'marquesano_tech', array( 'title' => __( 'Marquesano • TECH Signature', 'marquesano-store' ), 'priority' => 31, 'description' => __( 'Produtos protagonistas, imagens, superfícies e textos exclusivos do TECH. Produtos e imagens vazios usam o catálogo existente. Nada é criado ou gravado automaticamente.', 'marquesano-store' ) ) );
	foreach ( marquesano_store_fields() as $key => $field ) {
		if ( 'images' === $field['type'] ) { continue; } // Ordered gallery is managed in /painel/aparencia.
		$id = 'marquesano_store_settings[' . $key . ']';
		$customizer->add_setting( $id, array(
			'type' => 'option', 'default' => $field['default'], 'capability' => 'edit_theme_options', 'transport' => 'refresh',
			'sanitize_callback' => function ( $value ) use ( $key ) { return marquesano_store_sanitize_setting( $value, $key ); },
			'validate_callback' => 'tech_hero_image' === $key ? static function ( $validity, $value ) {
				if ( '' === (string) $value || '0' === (string) $value ) { return $validity; }
				$image_id = marquesano_store_sanitize_setting( $value, 'tech_hero_image' );
				if ( ! $image_id || ! wp_attachment_is_image( $image_id ) ) {
					$validity->add( 'invalid_tech_hero_image', __( 'Selecione uma imagem válida da Biblioteca de Mídia.', 'marquesano-store' ) );
				}
				return $validity;
			} : null,
		) );
		$args = array( 'label' => $field['label'], 'section' => 'marquesano_store', 'settings' => $id, 'type' => $field['type'] );
		if ( 0 === strpos( $key, 'tech_' ) ) { $args['section'] = 'marquesano_tech'; }
		if ( 'number' === $field['type'] ) { $args['input_attrs'] = array( 'min' => 0, 'step' => 1 ); }
		if ( isset( $field['choices'] ) ) { $args['choices'] = $field['choices']; }
		if ( 'image' === $field['type'] ) {
			// Media_Control stores attachment IDs. The legacy "image" control stores
			// URLs, which our ID sanitizer would convert to zero on preview/save.
			unset( $args['type'] );
			$args['mime_type'] = 'image';
			$customizer->add_control( new WP_Customize_Media_Control( $customizer, $id, $args ) );
		} elseif ( 'color' === $field['type'] ) {
			$customizer->add_control( new WP_Customize_Color_Control( $customizer, $id, $args ) );
		} else {
			$customizer->add_control( $id, $args );
		}
	}
} );
