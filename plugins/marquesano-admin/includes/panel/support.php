<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

function can( $cap ) { return current_user_can( 'manage_options' ) || current_user_can( 'marquesano_' . $cap ); }
function need( $cap ) { if ( ! can( $cap ) ) { throw new \RuntimeException( 'Você não tem permissão para esta operação.', 403 ); } }
function value( $data, $key, $default = '' ) { return isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? (string) $data[ $key ] : $default; }
function text( $data, $key, $default = '' ) { return sanitize_text_field( value( $data, $key, $default ) ); }
function number( $data, $key, $default = 0 ) { return absint( value( $data, $key, (string) $default ) ); }
function decimal( $data, $key, $required = false ) {
	$raw = trim( value( $data, $key ) );
	if ( '' === $raw && ! $required ) { return ''; }
	$labels = array( 'regular_price' => 'preço normal', 'sale_price' => 'preço promocional', 'weight' => 'peso', 'length' => 'comprimento', 'width' => 'largura', 'height' => 'altura' );
	if ( ! preg_match( '/^\d+(?:[.,]\d+)?$/D', $raw ) ) { throw new \RuntimeException( 'Informe um valor numérico válido e não negativo em ' . ( $labels[ $key ] ?? $key ) . '.' ); }
	return wc_format_decimal( str_replace( ',', '.', $raw ) );
}
function quantity( $data, $key ) {
	$raw = value( $data, $key );
	if ( ! preg_match( '/^\d{1,7}$/D', $raw ) ) { throw new \RuntimeException( 'Informe um estoque inteiro entre 0 e 9999999.' ); }
	return (int) $raw;
}
function ids( $raw ) {
	if ( is_int( $raw ) ) { $raw = array( $raw ); }
	elseif ( is_string( $raw ) ) { $raw = explode( ',', $raw ); }
	if ( ! is_array( $raw ) || count( $raw ) > 100 ) { return array(); }
	return array_values( array_unique( array_filter( array_map( static function ( $v ) { return is_scalar( $v ) ? absint( $v ) : 0; }, $raw ) ) ) );
}
function image_id( $id ) {
	$id = absint( $id );
	if ( $id && ( 'attachment' !== get_post_type( $id ) || ! in_array( get_post_mime_type( $id ), array( 'image/jpeg', 'image/png', 'image/webp' ), true ) || ! wp_attachment_is_image( $id ) || 'trash' === get_post_status( $id ) ) ) { throw new \RuntimeException( 'Imagem inválida. Selecione JPEG, PNG ou WebP da biblioteca.' ); }
	return $id;
}
function url( $path = '', $query = array() ) { return add_query_arg( $query, home_url( '/painel/' . ltrim( $path, '/' ) ) ); }
function redirect( $path = '', $query = array() ) { wp_safe_redirect( url( $path, $query ), 303 ); exit; }
function form_start( $action, $attributes = '' ) { echo '<form method="post" ' . $attributes . '>'; wp_nonce_field( 'marquesano_panel_' . $action ); echo '<input type="hidden" name="panel_action" value="' . esc_attr( $action ) . '">'; }
function posted( $name, $fallback ) {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { return $fallback; }
	preg_match_all( '/[^\[\]]+/', $name, $keys ); $data = wp_unslash( $_POST );
	foreach ( $keys[0] as $key ) { if ( ! is_array( $data ) || ! array_key_exists( $key, $data ) ) { return $fallback; } $data = $data[ $key ]; }
	return is_scalar( $data ) ? $data : $fallback;
}
function product_posted() { return 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && 'product_save' === text( $_POST, 'panel_action' ) && Router::$error && can( 'manage_products' ); }
function product_checked( $name, $fallback ) { return product_posted() ? (bool) posted( $name, false ) : $fallback; }
function input( $name, $label, $current = '', $type = 'text', $extra = '' ) {
	echo '<label class="mp-field"><span>' . esc_html( $label ) . '</span><input type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( 'password' === $type ? '' : posted( $name, $current ) ) . '" ' . $extra . '></label>';
}
function textarea( $name, $label, $current = '' ) { echo '<label class="mp-field"><span>' . esc_html( $label ) . '</span><textarea name="' . esc_attr( $name ) . '" rows="4">' . esc_textarea( posted( $name, $current ) ) . '</textarea></label>'; }
function select( $name, $label, $choices, $current = '' ) {
	echo '<label class="mp-field"><span>' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '">';
	foreach ( $choices as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '" ' . selected( posted( $name, $current ), $key, false ) . '>' . esc_html( $label ) . '</option>'; }
	echo '</select></label>';
}
function image_picker( $name, $label, $current = '', $multiple = false, $reorderable = false, $preview_size = 'thumbnail', $context = '' ) {
	$selected = ids( posted( $name, $current ) );
	echo '<div class="mp-field mp-media" data-multiple="' . ( $multiple ? '1' : '0' ) . '" data-reorderable="' . ( $reorderable ? '1' : '0' ) . '" data-preview-size="' . esc_attr( $preview_size ) . '" data-gallery-context="' . esc_attr( $context ) . '"><span>' . esc_html( $label ) . '</span><input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $selected ) ) . '"><div class="mp-media-preview">';
	foreach ( $selected as $index => $id ) {
        $title = get_the_title( $id ) ?: 'Imagem #' . $id;
        if ( 'hero-gallery' === $context ) { echo '<div class="mp-media-entry"><strong class="mp-banner-position">Banner ' . ( $index + 2 ) . '</strong>'; }
        echo '<button type="button" class="mp-image" data-id="' . esc_attr( $id ) . '" data-title="' . esc_attr( $title ) . '" aria-label="Remover imagem selecionada">' . wp_get_attachment_image( $id, $preview_size ) . '<span>Remover</span></button>';
        if ( 'hero-gallery' === $context ) { echo '<span class="mp-banner-name">' . esc_html( $title ) . ' (#' . (int) $id . ')</span><div class="mp-media-order"><button type="button" class="mp-secondary" data-move-media="-1" aria-label="Mover banner para antes" ' . disabled( $index, 0, false ) . '>←</button><button type="button" class="mp-secondary" data-move-media="1" aria-label="Mover banner para depois" ' . disabled( $index, count( $selected ) - 1, false ) . '>→</button></div></div>'; }
    }
	echo '</div>'; if ( 'thumbnail' !== $preview_size ) { echo '<small class="mp-media-current">' . esc_html( $selected ? 'Imagem selecionada: #' . implode( ', #', $selected ) : 'Nenhuma imagem selecionada' ) . '</small>'; } echo '<div class="mp-inline"><button type="button" class="mp-secondary" data-library>' . ( 'hero-gallery' === $context ? 'Adicionar banner' : ( ! $multiple && $selected ? 'Substituir imagem' : 'Abrir biblioteca' ) ) . '</button>';
	if ( can( 'upload_media' ) ) { echo '<label class="mp-upload">Enviar imagem<input type="file" accept="image/jpeg,image/png,image/webp" ' . ( $multiple ? 'multiple' : '' ) . '></label>'; }
	echo '</div><small>JPEG, PNG ou WebP. Até 5 MB e 20 milhões de pixels por imagem.</small><span class="mp-media-message" role="status"></span></div>';
}
function pages( $page, $total ) { if ( $total < 2 ) { return; } echo '<nav class="mp-pagination" aria-label="Paginação">'; if ( $page > 1 ) { echo '<a href="' . esc_url( add_query_arg( 'pg', $page - 1 ) ) . '">← Anterior</a>'; } echo '<span>Página ' . esc_html( $page ) . ' de ' . esc_html( $total ) . '</span>'; if ( $page < $total ) { echo '<a href="' . esc_url( add_query_arg( 'pg', $page + 1 ) ) . '">Próxima →</a>'; } echo '</nav>'; }
