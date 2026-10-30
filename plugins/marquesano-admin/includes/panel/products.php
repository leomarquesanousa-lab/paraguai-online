<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

final class Products {
	public static function get( $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || $product instanceof \WC_Product_Variation || ! in_array( $product->get_type(), array( 'simple', 'variable' ), true ) || 'trash' === $product->get_status() ) { throw new \RuntimeException( 'Produto indisponível ou tipo não editável neste painel.', 404 ); }
		return $product;
	}
	public static function check_version( $object, $data, $key = 'version' ) {
		$date = $object->get_date_modified();
		if ( $object->get_id() && (string) ( $date ? $date->getTimestamp() : 0 ) !== value( $data, $key ) ) { throw new \RuntimeException( 'Este registro mudou desde a abertura do formulário. Recarregue antes de salvar.', 409 ); }
	}
	public static function prices( $data, $required ) {
		$regular = decimal( $data, 'regular_price', $required ); $sale = decimal( $data, 'sale_price' );
		if ( '' !== $sale && ( '' === $regular || (float) $sale >= (float) $regular ) ) { throw new \RuntimeException( 'A promoção deve ser menor que o preço normal.' ); }
		return array( $regular, $sale );
	}
	public static function attributes( $raw, $variable ) {
		$attributes = array();
		foreach ( preg_split( '/\R/', $raw ) as $line ) {
			if ( ! trim( $line ) ) { continue; }
			$parts = explode( ':', $line, 2 );
			if ( count( $parts ) !== 2 ) { throw new \RuntimeException( 'Atributos: use Nome: valor | valor. Use * antes do nome para variações.' ); }
			$name = sanitize_text_field( trim( $parts[0] ) ); $variation = 0 === strpos( $name, '*' ); $name = trim( ltrim( $name, '*' ) );
			$options = array_values( array_unique( array_filter( array_map( 'sanitize_text_field', array_map( 'trim', explode( '|', $parts[1] ) ) ), 'strlen' ) ) );
			if ( ! $name || ! $options || count( $options ) > 30 || count( $attributes ) >= 8 || isset( $attributes[ sanitize_title( $name ) ] ) ) { throw new \RuntimeException( 'Atributos inválidos, duplicados ou acima do limite (8 atributos, 30 valores cada).' ); }
			$attribute = new \WC_Product_Attribute();
			if ( 0 === strpos( $name, 'pa_' ) ) {
				if ( ! taxonomy_exists( $name ) ) { throw new \RuntimeException( 'Taxonomia de atributo inexistente.' ); }
				$ids = array(); foreach ( $options as $option ) { $term = get_term_by( 'name', $option, $name ); if ( ! $term ) { $term = get_term_by( 'slug', $option, $name ); } if ( ! $term ) { throw new \RuntimeException( 'Valor global de atributo inexistente: ' . $option . '. Peça o cadastro ao administrador técnico ou use atributo próprio do produto.' ); } $ids[] = $term->term_id; }
				$attribute->set_id( wc_attribute_taxonomy_id_by_name( $name ) ); $attribute->set_options( $ids );
			} else { $attribute->set_options( $options ); }
			$attribute->set_name( $name ); $attribute->set_visible( true ); $attribute->set_variation( $variable && $variation );
			$attributes[ sanitize_title( $name ) ] = $attribute;
		}
		return $attributes;
	}
	public static function attribute_text( $product ) {
		$lines = array(); foreach ( $product->get_attributes() as $attribute ) {
			$options = $attribute->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) ) : $attribute->get_options();
			$lines[] = ( $attribute->get_variation() ? '*' : '' ) . $attribute->get_name() . ': ' . implode( ' | ', $options );
		} return implode( "\n", $lines );
	}
	private static function variation_plan( $product, $rows, $attributes, $status, $parent_sku ) {
		if ( ! is_array( $rows ) || count( $rows ) > 50 ) { throw new \RuntimeException( 'Limite de 50 variações por edição neste painel.' ); }
		$children = $product->get_id() ? $product->get_children() : array();
		if ( count( $children ) > 50 ) { throw new \RuntimeException( 'Produto com mais de 50 variações exige gestão técnica nesta versão.' ); }
		$plan = array(); $combinations = array(); $seen_ids = array(); $skus = $parent_sku ? array( $parent_sku ) : array();
		$allowed = array(); foreach ( $attributes as $key => $attribute ) {
			if ( ! $attribute->get_variation() ) { continue; }
			$allowed[ $key ] = $attribute->is_taxonomy() ? get_terms( array( 'taxonomy' => $attribute->get_name(), 'include' => $attribute->get_options(), 'hide_empty' => false, 'fields' => 'slugs' ) ) : $attribute->get_options();
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) { throw new \RuntimeException( 'Variação inválida.' ); }
			$id = number( $row, 'id' ); $variation = $id ? wc_get_product( $id ) : new \WC_Product_Variation();
			if ( ! $variation instanceof \WC_Product_Variation || ( $id && ( $variation->get_parent_id() !== $product->get_id() || isset( $seen_ids[ $id ] ) ) ) ) { throw new \RuntimeException( 'Variação não pertence a este produto ou foi duplicada.', 403 ); }
			if ( $id ) { self::check_version( $variation, $row ); $seen_ids[ $id ] = true; }
			$map = array(); foreach ( explode( ';', value( $row, 'attributes' ) ) as $pair ) { $pair = explode( '=', $pair, 2 ); if ( 2 !== count( $pair ) ) { throw new \RuntimeException( 'Use Nome=Valor;Outro=Valor nas variações.' ); } $key = sanitize_title( trim( $pair[0] ) ); $value = sanitize_text_field( trim( $pair[1] ) ); if ( ! isset( $allowed[ $key ] ) || ! in_array( $value, $allowed[ $key ], true ) || isset( $map[ $key ] ) ) { throw new \RuntimeException( 'Combinação não corresponde aos atributos do produto.' ); } $map[ $key ] = $value; }
			if ( count( $map ) !== count( $allowed ) || ! $map ) { throw new \RuntimeException( 'Preencha todos os atributos da combinação, sem curingas.' ); }
			ksort( $map ); $hash = wp_json_encode( $map ); if ( isset( $combinations[ $hash ] ) ) { throw new \RuntimeException( 'Combinação de atributos repetida.' ); } $combinations[ $hash ] = true;
			$enabled = 'publish' === text( $row, 'status' );
			list( $regular, $sale ) = self::prices( $row, $enabled && 'publish' === $status );
			$sku = text( $row, 'sku' ); if ( $sku && in_array( $sku, $skus, true ) ) { throw new \RuntimeException( 'SKU repetido no formulário.' ); } if ( $sku ) { $skus[] = $sku; }
			$variation->set_sku( $sku ); $variation->set_regular_price( $regular ); $variation->set_sale_price( $sale ); $variation->set_attributes( $map ); $variation->set_status( $enabled ? 'publish' : 'private' );
			$variation->set_manage_stock( ! empty( $row['manage_stock'] ) );
			if ( ! empty( $row['manage_stock'] ) ) { if ( $id && value( $row, 'old_stock' ) !== (string) $variation->get_stock_quantity( 'edit' ) ) { throw new \RuntimeException( 'O estoque de uma variação mudou. Recarregue.' ); } $variation->set_stock_quantity( quantity( $row, 'stock_quantity' ) ); }
			$stock_status = text( $row, 'stock_status' ); if ( ! in_array( $stock_status, array( 'instock', 'outofstock' ), true ) ) { throw new \RuntimeException( 'Disponibilidade inválida.' ); }
			$variation->set_stock_status( $stock_status ); $variation->set_image_id( image_id( number( $row, 'image_id' ) ) );
			$plan[] = $variation;
		}
		if ( array_diff( $children, array_keys( $seen_ids ) ) ) { throw new \RuntimeException( 'Faltam variações existentes no formulário. Recarregue; nenhuma será removida implicitamente.' ); }
		if ( 'publish' === $status && ! $plan ) { throw new \RuntimeException( 'Cadastre pelo menos uma variação antes de publicar.' ); }
		return $plan;
	}
	public static function save( $data ) {
		need( 'manage_products' ); $id = number( $data, 'id' );
		$type = text( $data, 'type', 'simple' ); if ( ! in_array( $type, array( 'simple', 'variable' ), true ) ) { throw new \RuntimeException( 'Tipo de produto inválido.' ); }
		$product = $id ? self::get( $id ) : ( 'variable' === $type ? new \WC_Product_Variable() : new \WC_Product_Simple() );
		if ( $id ) { self::check_version( $product, $data ); if ( $product->get_type() !== $type ) { throw new \RuntimeException( 'A conversão de tipo de produto exige administrador técnico.' ); } }
		$name = text( $data, 'name' ); $status = text( $data, 'status' );
		if ( ! $name || strlen( $name ) > 400 || ! in_array( $status, array( 'draft', 'publish' ), true ) ) { throw new \RuntimeException( 'Preencha o nome e selecione rascunho ou publicação.' ); }
		$attributes = self::attributes( value( $data, 'attributes' ), 'variable' === $type );
		$categories = ids( $data['categories'] ?? array() ); foreach ( $categories as $category ) { $term = get_term( $category, 'product_cat' ); if ( ! $term || is_wp_error( $term ) ) { throw new \RuntimeException( 'Categoria inválida.' ); } }
		$gallery = ids( value( $data, 'gallery_ids' ) ); if ( count( $gallery ) > 20 ) { throw new \RuntimeException( 'Use até 20 imagens na galeria.' ); } foreach ( $gallery as $image ) { image_id( $image ); }
		$main_image = image_id( number( $data, 'image_id' ) );
		$dimensions = array(); foreach ( array( 'weight', 'length', 'width', 'height' ) as $key ) { $dimensions[ $key ] = decimal( $data, $key ); }
		$sku = text( $data, 'sku' ); $product->set_sku( $sku );
		$variations = 'variable' === $type ? self::variation_plan( $product, $data['variations'] ?? array(), $attributes, $status, $sku ) : array();
		if ( 'simple' === $type ) { list( $regular, $sale ) = self::prices( $data, 'publish' === $status ); $product->set_regular_price( $regular ); $product->set_sale_price( $sale ); }
		$managed = ! empty( $data['manage_stock'] );
		if ( $managed && $id && value( $data, 'old_stock' ) !== (string) $product->get_stock_quantity( 'edit' ) ) { throw new \RuntimeException( 'O estoque mudou. Recarregue antes de salvar.' ); }
		$product->set_manage_stock( $managed ); if ( $managed ) { $product->set_stock_quantity( quantity( $data, 'stock_quantity' ) ); }
		$stock_status = text( $data, 'stock_status' ); if ( ! in_array( $stock_status, array( 'instock', 'outofstock' ), true ) ) { throw new \RuntimeException( 'Disponibilidade inválida.' ); }
		$product->set_stock_status( $stock_status ); $product->set_name( $name ); $product->set_status( $status );
		$product->set_description( wp_kses_post( value( $data, 'description' ) ) ); $product->set_short_description( wp_kses_post( value( $data, 'short_description' ) ) );
		$product->set_category_ids( $categories ); $product->set_attributes( $attributes ); $product->set_image_id( $main_image ); $product->set_gallery_image_ids( array_values( array_diff( $gallery, array( $main_image ) ) ) );
		foreach ( $dimensions as $key => $value ) { $setter = 'set_' . $key; $product->$setter( $value ); }
		$id = $product->save(); if ( ! $id ) { throw new \RuntimeException( 'Não foi possível salvar o produto.' ); }
		foreach ( $variations as $variation ) {
			$variation->set_parent_id( $id );
			try { $saved = $variation->save(); } catch ( \Throwable $error ) { throw new \RuntimeException( 'O produto #' . $id . ' foi salvo, mas uma variação falhou. Reabra esse produto antes de tentar novamente; confira os SKUs e os campos.' ); }
			if ( ! $saved ) { throw new \RuntimeException( 'O produto #' . $id . ' foi salvo, mas uma variação não pôde ser gravada. Reabra esse produto.' ); }
		}
		if ( 'variable' === $type ) { \WC_Product_Variable::sync( $id ); }
		wc_delete_product_transients( $id ); return $id;
	}
	public static function trash( $data ) {
		need( 'manage_products' ); if ( empty( $data['confirm'] ) || ( defined( 'EMPTY_TRASH_DAYS' ) && EMPTY_TRASH_DAYS <= 0 ) ) { throw new \RuntimeException( 'Confirme a ação. A lixeira deve estar habilitada.' ); }
		$product = self::get( number( $data, 'id' ) ); self::check_version( $product, $data ); $id = $product->get_id(); $product->delete( false );
		if ( 'trash' !== get_post_status( $id ) ) { throw new \RuntimeException( 'Não foi possível confirmar o envio à lixeira.' ); }
	}
	public static function category( $data ) {
		need( 'manage_categories' ); $id = number( $data, 'id' ); $parent = number( $data, 'parent' ); $name = text( $data, 'name' );
		if ( ! $name ) { throw new \RuntimeException( 'Preencha o nome da categoria.' ); }
		$thumbnail = array_key_exists( 'thumbnail_id', $data ) ? image_id( number( $data, 'thumbnail_id' ) ) : null;
		if ( $id ) { $term = get_term( $id, 'product_cat' ); if ( ! $term || is_wp_error( $term ) ) { throw new \RuntimeException( 'Categoria inválida.' ); } }
		if ( $parent ) { $term = get_term( $parent, 'product_cat' ); if ( ! $term || is_wp_error( $term ) || $id === $parent || ( $id && term_is_ancestor_of( $id, $parent, 'product_cat' ) ) ) { throw new \RuntimeException( 'Categoria superior inválida.' ); } }
		$args = array( 'name' => $name, 'parent' => $parent, 'description' => sanitize_textarea_field( value( $data, 'description' ) ) );
		$result = $id ? wp_update_term( $id, 'product_cat', $args ) : wp_insert_term( $name, 'product_cat', $args );
		if ( is_wp_error( $result ) ) { throw new \RuntimeException( $result->get_error_message() ); }
		$term_id = (int) $result['term_id'];
		// Omitted field preserves legacy callers; an explicit zero removes only the association.
		if ( null !== $thumbnail ) {
			$saved = $thumbnail ? update_term_meta( $term_id, 'thumbnail_id', $thumbnail ) : delete_term_meta( $term_id, 'thumbnail_id' );
			if ( is_wp_error( $saved ) || (int) get_term_meta( $term_id, 'thumbnail_id', true ) !== $thumbnail ) { throw new \RuntimeException( 'Não foi possível salvar a imagem da categoria. Recarregue e tente novamente.' ); }
		}
		return $term_id;
	}
	public static function stock( $data ) {
		need( 'manage_products' ); $product = wc_get_product( number( $data, 'id' ) );
		if ( ! $product || 'trash' === $product->get_status() || true !== $product->get_manage_stock( 'edit' ) ) { throw new \RuntimeException( 'Produto sem controle individual de estoque. Ative-o na edição ou ajuste o produto de origem.' ); }
		if ( value( $data, 'old_stock' ) !== (string) $product->get_stock_quantity( 'edit' ) ) { throw new \RuntimeException( 'O estoque mudou. Recarregue a consulta.' ); }
		$result = wc_update_product_stock( $product, quantity( $data, 'stock_quantity' ), 'set' );
		if ( false === $result || null === $result ) { throw new \RuntimeException( 'Não foi possível atualizar o estoque. Recarregue a consulta.' ); }
	}
	public static function upload() {
		need( 'upload_media' );
		$file = $_FILES['image'] ?? null;
		if ( ! is_array( $file ) || ! isset( $file['error'], $file['tmp_name'], $file['name'] ) || ! is_scalar( $file['error'] ) || (int) $file['error'] !== UPLOAD_ERR_OK || ! is_string( $file['tmp_name'] ) || ! is_string( $file['name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) { throw new \RuntimeException( 'Envio inválido ou acima do limite do servidor.' ); }
		if ( filesize( $file['tmp_name'] ) > min( 5 * MB_IN_BYTES, wp_max_upload_size() ) ) { throw new \RuntimeException( 'A imagem excede o limite de envio (máximo 5 MB).' ); }
		$mimes = array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
		$info = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes ); $size = @getimagesize( $file['tmp_name'] );
		if ( ! $info['ext'] || ! $info['type'] || ! $size || ! in_array( $size['mime'] ?? '', $mimes, true ) || $size[0] > 8000 || $size[1] > 8000 || $size[0] * $size[1] > 20000000 ) { throw new \RuntimeException( 'Use JPEG, PNG ou WebP válido, até 8000 px por lado e 20 milhões de pixels.' ); }
		require_once ABSPATH . 'wp-admin/includes/file.php'; require_once ABSPATH . 'wp-admin/includes/media.php'; require_once ABSPATH . 'wp-admin/includes/image.php';
		$id = media_handle_upload( 'image', 0, array(), array( 'test_form' => false, 'mimes' => $mimes ) );
		if ( is_wp_error( $id ) ) { throw new \RuntimeException( $id->get_error_message() ); }
		return array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'thumbnail' ), 'preview_url' => wp_get_attachment_image_url( $id, 'medium_large' ) );
	}
}
