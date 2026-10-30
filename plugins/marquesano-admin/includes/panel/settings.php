<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

final class Settings {
	public static function appearance( $data ) {
		need( 'manage_appearance' );
		if ( ! function_exists( 'marquesano_store_fields' ) || 'marquesano-store' !== get_template() ) { throw new \RuntimeException( 'O tema Marquesano Store precisa estar ativo.' ); }
		$schema = marquesano_store_fields(); $submitted = $data['appearance'] ?? array(); if ( ! is_array( $submitted ) ) { throw new \RuntimeException( 'Configuração inválida.' ); }
		$settings = get_option( 'marquesano_store_settings', array() ); $settings = is_array( $settings ) ? $settings : array();
		foreach ( $submitted as $key => $value ) {
			if ( ! isset( $schema[ $key ] ) || ! is_scalar( $value ) ) { throw new \RuntimeException( 'Campo de aparência inválido.' ); }
			$settings[ $key ] = marquesano_store_sanitize_setting( $value, $key );
			if ( 'image' === $schema[ $key ]['type'] ) { image_id( $settings[ $key ] ); }
			if ( 'email' === $schema[ $key ]['type'] && $value && ! is_email( $settings[ $key ] ) ) { throw new \RuntimeException( 'E-mail inválido.' ); }
			if ( in_array( $schema[ $key ]['type'], array( 'url', 'color' ), true ) && $value && ! $settings[ $key ] ) { throw new \RuntimeException( 'Cor ou URL inválida.' ); }
		}
		if ( function_exists( 'paraguai_tech_hero_validate_images' ) && ( isset( $submitted['tech_hero_images'] ) || isset( $submitted['tech_hero_image'] ) || isset( $submitted['hero_image'] ) || isset( $submitted['tech_hero_product'] ) ) ) {
			$settings['tech_hero_images'] = paraguai_tech_hero_validate_images( $submitted['tech_hero_images'] ?? ( $settings['tech_hero_images'] ?? '' ), paraguai_tech_hero_reference_id( $settings ) );
		}
		$logo = image_id( number( $data, 'custom_logo' ) ); $icon = image_id( number( $data, 'site_icon', (int) get_option( 'site_icon' ) ) );
		if ( $icon && $icon !== (int) get_option( 'site_icon' ) ) { $meta = wp_get_attachment_metadata( $icon ); if ( empty( $meta['width'] ) || $meta['width'] < 512 || $meta['height'] < 512 || $meta['width'] !== $meta['height'] ) { throw new \RuntimeException( 'O favicon deve ser quadrado, com pelo menos 512 × 512 pixels.' ); } }
		$name = text( $data, 'blogname' ); if ( ! $name ) { throw new \RuntimeException( 'Preencha o nome da loja.' ); }
		update_option( 'marquesano_store_settings', $settings ); set_theme_mod( 'custom_logo', $logo ); update_option( 'site_icon', $icon ); update_option( 'blogname', $name );
	}
	public static function business_fields() {
		return array( 'woocommerce_store_address' => 'Endereço da loja', 'woocommerce_store_address_2' => 'Complemento', 'woocommerce_store_city' => 'Cidade', 'woocommerce_store_postcode' => 'CEP', 'woocommerce_default_country' => 'País/estado (ex.: BR:SP)', 'woocommerce_currency' => 'Moeda', 'woocommerce_price_num_decimals' => 'Casas decimais', 'woocommerce_notify_low_stock_amount' => 'Limite de estoque baixo', 'woocommerce_notify_no_stock_amount' => 'Limite de estoque esgotado' );
	}
	public static function business( $data ) {
		need( 'manage_settings' ); $settings = array();
		foreach ( self::business_fields() as $key => $label ) {
			$value = text( $data, $key );
			if ( 'woocommerce_currency' === $key && ! isset( get_woocommerce_currencies()[ $value ] ) ) { throw new \RuntimeException( 'Moeda inválida.' ); }
			if ( 'woocommerce_default_country' === $key ) {
				$parts = explode( ':', $value, 2 ); $countries = WC()->countries->get_countries(); $states = WC()->countries->get_states( $parts[0] );
				if ( ! isset( $countries[ $parts[0] ] ) || ( is_array( $states ) && $states && ( empty( $parts[1] ) || ! isset( $states[ $parts[1] ] ) ) ) ) { throw new \RuntimeException( 'País/estado inválido.' ); }
			}
			if ( in_array( $key, array( 'woocommerce_price_num_decimals', 'woocommerce_notify_low_stock_amount', 'woocommerce_notify_no_stock_amount' ), true ) ) {
				if ( ! preg_match( '/^\d{1,4}$/D', $value ) || ( 'woocommerce_price_num_decimals' === $key && (int) $value > 6 ) ) { throw new \RuntimeException( 'Limite numérico inválido.' ); }
			}
			$settings[ $key ] = $value;
		}
		foreach ( $settings as $key => $value ) { update_option( $key, $value ); }
	}
}
