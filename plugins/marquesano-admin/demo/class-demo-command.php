<?php
/** Manual WP-CLI routine. Never called by activation, admin requests or cron. */
namespace Marquesano\Admin;
defined( 'ABSPATH' ) || exit;
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { return; }

final class Demo_Command {
	const BATCH = 'marquesano-visual-v1';
	const REGISTRY = 'marquesano_demo_manifest_v1';

	/**
	 * Show the manifest without writing anything.
	 *
	 * ## OPTIONS
	 *
	 * --segment=<segment>
	 * : tech, fashion, beauty ou home.
	 */
	public function plan( $args, $options ) {
		foreach ( $this->rows( $options ) as $row ) {
			\WP_CLI::log( $row['key'] . ' | ' . $row['name'] . ' | ' . $row['price'] . ' | estoque: ' . $row['stock'] );
		}
		\WP_CLI::success( 'Plano exibido. Nenhum produto gravado.' );
	}

	/**
	 * Create a marked segment. Without --yes, show the plan only.
	 *
	 * ## OPTIONS
	 *
	 * --segment=<segment>
	 * : tech, fashion, beauty ou home.
	 *
	 * [--yes]
	 * : Executar a gravação local explicitamente.
	 *
	 * [--image-map=<json>]
	 * : Objeto JSON de chaves de produto para IDs de mídia autorizada.
	 *
	 * [--with-images]
	 * : Gerar e importar ilustrações originais locais, sem downloads.
	 */
	public function create( $args, $options ) {
		if ( ! isset( $options['yes'] ) ) { $this->plan( $args, $options ); return; }
		$this->guard();
		$rows = $this->rows( $options );
		$images = isset( $options['image-map'] ) ? json_decode( $options['image-map'], true ) : array();
		if ( ! is_array( $images ) ) { \WP_CLI::error( 'image-map deve ser um objeto JSON de chave do produto para ID de imagem autorizada.' ); }
		foreach ( $images as $image_id ) {
			if ( ! is_scalar( $image_id ) || ! wp_attachment_is_image( absint( $image_id ) ) || 'image/svg+xml' === get_post_mime_type( absint( $image_id ) ) ) {
				\WP_CLI::error( 'Imagem inválida. Use IDs existentes de fotografias ou ilustrações raster autorizadas.' );
			}
		}
		$registry = get_option( self::REGISTRY, array() );
		$registry = is_array( $registry ) ? $registry : array();
		// Preflight all collisions before the first write; never update real records.
		foreach ( $rows as $row ) { $this->existing( $row, $registry ); }
		try {
			foreach ( $rows as $row ) {
				$existing = $this->existing( $row, $registry );
				if ( isset( $options['with-images'] ) && ( ! $existing || ( 'trash' !== $existing->get_status() && ! $existing->get_image_id() ) ) ) {
					require_once __DIR__ . '/class-demo-artwork.php';
					$images[ $row['key'] ] = Demo_Artwork::media( $row );
				}
				if ( $existing ) {
					if ( ! $existing->get_image_id() && isset( $images[ $row['key'] ] ) && 'trash' !== $existing->get_status() ) { $existing->set_image_id( $images[ $row['key'] ] ); $existing->save(); }
					\WP_CLI::log( $row['key'] . ': já identificado; preservado, inclusive se estiver na lixeira.' ); continue;
				}
				$segment = explode( '-', $row['key'] )[0];
				$slug = 'marquesano-demo-' . $segment;
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				if ( $term && self::BATCH !== get_term_meta( $term->term_id, '_marquesano_demo_batch', true ) ) { throw new \RuntimeException( 'Categoria já existe sem identificação demonstrativa: ' . $slug ); }
				if ( ! $term ) {
					$result = wp_insert_term( $row['category'] . ' · Demonstração', 'product_cat', array( 'slug' => $slug, 'description' => 'Categoria exclusiva de avaliação local Marquesano.' ) );
					if ( is_wp_error( $result ) ) { throw new \RuntimeException( $result->get_error_message() ); }
					$term = get_term( $result['term_id'], 'product_cat' );
					if ( ! $term || is_wp_error( $term ) ) { throw new \RuntimeException( 'Não foi possível consultar a categoria criada.' ); }
					add_term_meta( $term->term_id, '_marquesano_demo_batch', self::BATCH, true );
				}
				$product = new \WC_Product_Simple();
				$product->set_name( '[DEMONSTRAÇÃO] ' . $row['name'] );
				$product->set_sku( 'MS-DEMO-V1-' . strtoupper( $row['key'] ) );
				$product->set_status( 'publish' );
				$product->set_catalog_visibility( 'visible' );
				$product->set_description( $row['description'] );
				$product->set_short_description( 'Produto de demonstração local. Estoque e preço ilustrativos; não destinado à venda real.' );
				$product->set_regular_price( $row['price'] );
				if ( isset( $row['sale'] ) ) { $product->set_sale_price( $row['sale'] ); }
				$product->set_manage_stock( true );
				$product->set_stock_quantity( $row['stock'] );
				$product->set_stock_status( 'instock' );
				$product->set_backorders( 'no' );
				$product->set_reviews_allowed( false );
				$product->set_category_ids( array( $term->term_id ) );
				if ( isset( $images[ $row['key'] ] ) && ! get_term_meta( $term->term_id, 'thumbnail_id', true ) ) { update_term_meta( $term->term_id, 'thumbnail_id', absint( $images[ $row['key'] ] ) ); }
				if ( isset( $images[ $row['key'] ] ) ) { $product->set_image_id( absint( $images[ $row['key'] ] ) ); }
				$attributes = array();
				foreach ( $row['attributes'] as $name => $value ) {
					$attribute = new \WC_Product_Attribute();
					$attribute->set_name( $name );
					$attribute->set_options( array( $value ) );
					$attribute->set_visible( true );
					$attributes[] = $attribute;
				}
				$product->set_attributes( $attributes );
				$product->update_meta_data( '_marquesano_demo_batch', self::BATCH );
				$product->update_meta_data( '_marquesano_demo_key', $row['key'] );
				$product->update_meta_data( '_marquesano_demo_segment', $segment );
				$id = $product->save();
				if ( ! $id ) { throw new \RuntimeException( 'Não foi possível gravar ' . $row['key'] ); }
				$registry[ $row['key'] ] = $id;
				update_option( self::REGISTRY, $registry, false );
				\WP_CLI::log( $row['key'] . ': criado #' . $id );
			}
		} catch ( \Throwable $error ) {
			\WP_CLI::error( 'Importação interrompida: ' . $error->getMessage() . ' Use remove para arquivar produtos já criados. Nada existente foi sobrescrito.' );
		}
		\WP_CLI::success( 'Catálogo demonstrativo criado. Configurações, pedidos, mídia e modo Em breve foram preservados.' );
	}

	/**
	 * Move only identified demo products to trash; never delete permanently.
	 *
	 * ## OPTIONS
	 *
	 * --segment=<segment>
	 * : tech, fashion, beauty ou home.
	 *
	 * [--yes]
	 * : Executar o envio dos produtos identificados para a lixeira.
	 */
	public function remove( $args, $options ) {
		$this->guard();
		if ( isset( $options['yes'] ) && defined( 'EMPTY_TRASH_DAYS' ) && EMPTY_TRASH_DAYS <= 0 ) { \WP_CLI::error( 'A lixeira está desabilitada. Remoção bloqueada para impedir exclusão definitiva.' ); }
		$registry = get_option( self::REGISTRY, array() );
		$registry = is_array( $registry ) ? $registry : array();
		$products = array();
		foreach ( $this->rows( $options ) as $row ) {
			$product = $this->existing( $row, $registry );
			if ( $product ) {
				if ( ! current_user_can( 'delete_post', $product->get_id() ) ) { \WP_CLI::error( 'Sem permissão para remover #' . $product->get_id() ); }
				$products[] = $product;
			}
		}
		foreach ( $products as $product ) {
			$id = $product->get_id();
			if ( isset( $options['yes'] ) && 'trash' !== $product->get_status() ) { $product->delete( false ); }
			if ( isset( $options['yes'] ) && 'trash' !== get_post_status( $id ) ) { \WP_CLI::error( 'Não foi possível confirmar o envio de #' . $id . ' à lixeira. Operação interrompida.' ); }
			\WP_CLI::log( ( isset( $options['yes'] ) ? 'Na lixeira: #' : 'Seria movido para a lixeira: #' ) . $id );
		}
		\WP_CLI::success( isset( $options['yes'] ) ? 'Remoção concluída. Categorias marcadas e registro foram mantidos para permitir restauração pela lixeira nativa.' : 'Plano de remoção exibido; nada alterado. Adicione --yes para executar.' );
	}

	private function rows( $options ) {
		$catalog = require __DIR__ . '/catalog.php';
		$segment = $options['segment'] ?? '';
		if ( ! is_string( $segment ) || ! isset( $catalog[ $segment ] ) ) { \WP_CLI::error( 'Informe --segment=tech, fashion, beauty ou home. Um segmento por execução.' ); }
		return $catalog[ $segment ];
	}

	private function existing( $row, $registry ) {
		$ids = array_filter( array( $registry[ $row['key'] ] ?? 0, wc_get_product_id_by_sku( 'MS-DEMO-V1-' . strtoupper( $row['key'] ) ) ) );
		$found = null;
		foreach ( array_unique( $ids ) as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product ) { continue; }
			if ( self::BATCH !== $product->get_meta( '_marquesano_demo_batch' ) || $row['key'] !== $product->get_meta( '_marquesano_demo_key' ) ) { \WP_CLI::error( 'Colisão com registro sem a identificação esperada: #' . $id . '. Nenhuma alteração autorizada nesse registro.' ); }
			$found = $product;
		}
		return $found;
	}

	private function guard() {
		$host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		$is_local = in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) || preg_match( '/\.(local|test)$/', $host );
		if ( ! $is_local ) { \WP_CLI::error( 'Rotina restrita a localhost e domínios .local ou .test.' ); }
		if ( ! class_exists( '\WC_Product_Simple' ) ) { \WP_CLI::error( 'WooCommerce precisa estar ativo.' ); }
		if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'publish_products' ) || ! current_user_can( 'delete_products' ) ) { \WP_CLI::error( 'Informe --user=USUARIO_ADMIN com permissões de gestão e produtos.' ); }
	}
}

\WP_CLI::add_command( 'marquesano demo', __NAMESPACE__ . '\\Demo_Command' );
