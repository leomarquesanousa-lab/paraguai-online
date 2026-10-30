<?php
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;

function catalog_query( $inventory = false ) {
	$status = text( $_GET, 'status', 'publish' ); if ( ! in_array( $status, array( 'publish', 'draft', 'pending', 'private', 'trash' ), true ) ) { $status = 'publish'; }
	$args = array( 'post_type' => $inventory ? array( 'product', 'product_variation' ) : 'product', 'post_status' => $inventory ? array( 'publish', 'private' ) : $status, 'posts_per_page' => 15, 'paged' => max( 1, number( $_GET, 'pg', 1 ) ), 's' => text( $_GET, 'q' ), 'orderby' => 'modified', 'order' => 'DESC', 'fields' => 'ids' );
	$type = text( $_GET, 'type' ); if ( ! $inventory && in_array( $type, array( 'simple', 'variable' ), true ) ) { $args['tax_query'][] = array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => $type ); }
	$category = number( $_GET, 'category' ); if ( ! $inventory && $category ) { $args['tax_query'][] = array( 'taxonomy' => 'product_cat', 'terms' => $category ); }
	$stock = text( $_GET, 'stock' ); if ( in_array( $stock, array( 'instock', 'outofstock' ), true ) ) { $args['meta_query'][] = array( 'key' => '_stock_status', 'value' => $stock ); }
	return new \WP_Query( $args );
}
function screen_products() {
	echo '<div class="mp-heading"><div><p class="mp-eyebrow">CATÁLOGO</p><h1>Produtos</h1></div><a class="mp-button" href="' . esc_url( url( 'produto/novo' ) ) . '">Novo produto</a></div><form class="mp-filters" method="get">';
	input( 'q', 'Buscar por nome', text( $_GET, 'q' ), 'search' ); select( 'status', 'Situação', array( 'publish' => 'Publicados', 'draft' => 'Rascunhos', 'pending' => 'Pendentes', 'private' => 'Privados', 'trash' => 'Lixeira' ), text( $_GET, 'status', 'publish' ) ); select( 'type', 'Tipo', array( '' => 'Todos', 'simple' => 'Simples', 'variable' => 'Variável' ), text( $_GET, 'type' ) ); select( 'stock', 'Estoque', array( '' => 'Todos', 'instock' => 'Disponível', 'outofstock' => 'Esgotado' ), text( $_GET, 'stock' ) );
	$choices = array( '' => 'Todas' ); $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ); if ( ! is_wp_error( $terms ) ) { foreach ( $terms as $term ) { $choices[ $term->term_id ] = $term->name; } } select( 'category', 'Categoria', $choices, number( $_GET, 'category' ) );
	echo '<button type="submit">Filtrar</button></form>'; $query = catalog_query();
	echo '<div class="mp-card mp-table"><table><thead><tr><th>Produto</th><th>Tipo</th><th>Preço</th><th>Estoque</th><th>Ação</th></tr></thead><tbody>';
	foreach ( $query->posts as $id ) { $p = wc_get_product( $id ); if ( ! $p ) { continue; } echo '<tr><td><div class="mp-product-cell">' . $p->get_image( 'thumbnail' ) . '<span>' . esc_html( $p->get_name() ) . '<small>' . esc_html( $p->get_sku() ) . '</small></span></div></td><td>' . esc_html( $p->is_type( 'variable' ) ? 'Variável' : ( $p->is_type( 'simple' ) ? 'Simples' : $p->get_type() ) ) . '</td><td>' . wp_kses_post( $p->get_price_html() ) . '</td><td>' . esc_html( $p->get_manage_stock() ? $p->get_stock_quantity() : ( $p->is_in_stock() ? 'Disponível' : 'Esgotado' ) ) . '</td><td>'; if ( 'trash' !== $p->get_status() && in_array( $p->get_type(), array( 'simple', 'variable' ), true ) ) { echo '<a href="' . esc_url( url( 'produto/' . $id ) ) . '">Editar</a>'; } else { echo 'Gestão técnica'; } echo '</td></tr>'; }
	if ( ! $query->posts ) { echo '<tr><td colspan="5">Nenhum produto encontrado nesta seleção.</td></tr>'; } echo '</tbody></table></div>'; pages( max( 1, number( $_GET, 'pg', 1 ) ), $query->max_num_pages );
}
function variation_form( $index, $v = null ) {
	$prefix = 'variations[' . $index . ']'; $id = $v ? $v->get_id() : 0; $date = $v ? $v->get_date_modified() : null;
	$map = array(); if ( $v ) { foreach ( $v->get_attributes() as $key => $value ) { $map[] = $key . '=' . $value; } }
	echo '<details class="mp-variation" open><summary>Variação ' . ( $id ? '#' . esc_html( $id ) : 'nova' ) . '</summary><input type="hidden" name="' . esc_attr( $prefix . '[id]' ) . '" value="' . esc_attr( $id ) . '"><input type="hidden" name="' . esc_attr( $prefix . '[version]' ) . '" value="' . esc_attr( $date ? $date->getTimestamp() : 0 ) . '"><input type="hidden" name="' . esc_attr( $prefix . '[old_stock]' ) . '" value="' . esc_attr( $v ? $v->get_stock_quantity( 'edit' ) : '' ) . '"><div class="mp-form-grid">';
	input( $prefix . '[attributes]', 'Combinação: Nome=Valor;Outro=Valor', implode( ';', $map ), 'text', 'required' ); input( $prefix . '[sku]', 'SKU próprio', $v ? $v->get_sku( 'edit' ) : '' ); input( $prefix . '[regular_price]', 'Preço normal', $v ? $v->get_regular_price( 'edit' ) : '', 'number', 'min="0" step="any"' ); input( $prefix . '[sale_price]', 'Preço promocional', $v ? $v->get_sale_price( 'edit' ) : '', 'number', 'min="0" step="any"' );
	select( $prefix . '[status]', 'Situação', array( 'publish' => 'Ativa', 'private' => 'Desativada' ), $v ? $v->get_status() : 'publish' ); select( $prefix . '[stock_status]', 'Disponibilidade', array( 'instock' => 'Disponível', 'outofstock' => 'Esgotada' ), $v ? $v->get_stock_status() : 'instock' );
	echo '<label class="mp-check"><input type="checkbox" name="' . esc_attr( $prefix . '[manage_stock]' ) . '" value="1" ' . checked( product_checked( $prefix . '[manage_stock]', $v && true === $v->get_manage_stock( 'edit' ) ), true, false ) . '>Controlar estoque desta variação</label>'; input( $prefix . '[stock_quantity]', 'Quantidade', $v ? $v->get_stock_quantity( 'edit' ) : 0, 'number', 'min="0" step="1"' ); image_picker( $prefix . '[image_id]', 'Imagem da variação', $v ? $v->get_image_id() : 0 ); echo '</div>'; if ( ! $id ) { echo '<button type="button" class="mp-secondary" data-remove-variation>Remover linha não salva</button>'; } echo '</details>';
}
function product_category_picker( $selected ) {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name' ) );
	if ( is_wp_error( $terms ) ) { throw new \RuntimeException( 'Categorias indisponíveis. Recarregue antes de salvar o produto.' ); }
	$children = array(); foreach ( $terms as $term ) { $children[ $term->parent ][] = $term; }
	echo '<div class="mp-category-picker"><label id="mp-category-label" for="mp-product-categories">Categorias</label><select id="mp-product-categories" name="categories[]" multiple size="5" aria-describedby="mp-category-help">';
	$seen = array();
	$render = static function ( $term, $path = '' ) use ( &$render, &$seen, $children, $selected ) {
		if ( isset( $seen[ $term->term_id ] ) ) { return; } $seen[ $term->term_id ] = true;
		$path = $path ? $path . ' › ' . $term->name : $term->name;
		echo '<option value="' . esc_attr( $term->term_id ) . '" data-name="' . esc_attr( $term->name ) . '" ' . selected( in_array( $term->term_id, $selected, true ), true, false ) . '>' . esc_html( $path ) . '</option>';
		foreach ( $children[ $term->term_id ] ?? array() as $child ) { $render( $child, $path ); }
	};
	foreach ( $children[0] ?? array() as $term ) { $render( $term ); }
	foreach ( $terms as $term ) { if ( ! isset( $seen[ $term->term_id ] ) ) { $render( $term ); } }
	echo '</select><small id="mp-category-help">Selecione uma ou mais categorias. Com a pesquisa aberta, use as setas e Enter para selecionar; Escape para fechar.</small></div>';
}
function screen_product() {
	$p = Router::$id ? Products::get( Router::$id ) : null; $type = $p ? $p->get_type() : ( 'variable' === text( $_GET, 'type' ) ? 'variable' : 'simple' );
	echo '<div class="mp-heading"><div><p class="mp-eyebrow">CATÁLOGO / ' . esc_html( 'variable' === $type ? 'VARIÁVEL' : 'SIMPLES' ) . '</p><h1>' . ( $p ? 'Editar produto' : 'Novo produto' ) . '</h1></div><a href="' . esc_url( url( 'produtos' ) ) . '">Voltar aos produtos</a></div>';
	if ( ! $p ) { echo '<p class="mp-tabs"><a href="' . esc_url( url( 'produto/novo' ) ) . '">Produto simples</a><a href="' . esc_url( url( 'produto/novo', array( 'type' => 'variable' ) ) ) . '">Produto variável</a></p>'; }
	form_start( 'product_save', 'class="mp-product-form"' ); $date = $p ? $p->get_date_modified() : null;
	echo '<input type="hidden" name="id" value="' . esc_attr( $p ? $p->get_id() : 0 ) . '"><input type="hidden" name="type" value="' . esc_attr( $type ) . '"><input type="hidden" name="version" value="' . esc_attr( $date ? $date->getTimestamp() : 0 ) . '"><input type="hidden" name="old_stock" value="' . esc_attr( $p ? $p->get_stock_quantity( 'edit' ) : '' ) . '"><section class="mp-card"><h2>Informações do produto</h2><div class="mp-form-grid">';
	input( 'name', 'Nome do produto', $p ? $p->get_name( 'edit' ) : '', 'text', 'required maxlength="200"' ); select( 'status', 'Situação', array( 'draft' => 'Rascunho', 'publish' => 'Publicado' ), $p ? $p->get_status() : 'draft' ); input( 'sku', 'SKU', $p ? $p->get_sku( 'edit' ) : '' );
	if ( 'simple' === $type ) { input( 'regular_price', 'Preço normal', $p ? $p->get_regular_price( 'edit' ) : '', 'number', 'step="any" min="0"' ); input( 'sale_price', 'Preço promocional', $p ? $p->get_sale_price( 'edit' ) : '', 'number', 'step="any" min="0"' ); }
	echo '</div>'; textarea( 'short_description', 'Resumo comercial', $p ? $p->get_short_description( 'edit' ) : '' ); textarea( 'description', 'Descrição completa (HTML seguro permitido)', $p ? $p->get_description( 'edit' ) : '' );
	$cats = product_posted() ? ids( wp_unslash( $_POST['categories'] ?? array() ) ) : ( $p ? $p->get_category_ids() : array() );
	product_category_picker( $cats ); echo '</section><section class="mp-card"><h2>Imagens</h2><div class="mp-form-grid">'; image_picker( 'image_id', 'Imagem principal', $p ? $p->get_image_id() : 0 ); image_picker( 'gallery_ids', 'Galeria (até 20 imagens)', $p ? implode( ',', $p->get_gallery_image_ids() ) : '', true );
	echo '</div></section><section class="mp-card"><h2>Estoque e medidas</h2><div class="mp-form-grid"><label class="mp-check"><input type="checkbox" name="manage_stock" value="1" ' . checked( product_checked( 'manage_stock', $p && $p->get_manage_stock() ), true, false ) . '>Controlar estoque do produto</label>'; input( 'stock_quantity', 'Quantidade', $p ? $p->get_stock_quantity( 'edit' ) : 0, 'number', 'min="0" step="1"' ); select( 'stock_status', 'Disponibilidade', array( 'instock' => 'Disponível', 'outofstock' => 'Esgotado' ), $p ? $p->get_stock_status() : 'instock' );
	foreach ( array( 'weight' => 'Peso (' . get_option( 'woocommerce_weight_unit' ) . ')', 'length' => 'Comprimento', 'width' => 'Largura', 'height' => 'Altura' ) as $key => $label ) { $getter = 'get_' . $key; input( $key, $label, $p ? $p->$getter( 'edit' ) : '', 'number', 'min="0" step="any"' ); }
	echo '</div><small>Dimensões na unidade configurada: ' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . '.</small></section><section class="mp-card"><h2>Atributos e especificações</h2><p>Um atributo por linha: <code>Material: Algodão | Linho</code>. Para variações: <code>*Tamanho: P | M | G</code>. Atributos globais existentes usam o nome técnico <code>pa_nome</code>.</p>'; textarea( 'attributes', 'Atributos', $p ? Products::attribute_text( $p ) : '' );
	if ( 'variable' === $type ) {
		echo '<h3>Variações</h3><p>Cadastre cada combinação explicitamente. Não há geração automática de combinações. Para atributo global, use o slug do valor. Limite de 50 variações por edição.</p><div id="mp-variations">';
		$index = 0; $rendered = array();
		$children = $p ? $p->get_children() : array();
		$rows = product_posted() && is_array( $_POST['variations'] ?? null ) ? array_slice( wp_unslash( $_POST['variations'] ), 0, 50, true ) : array();
		foreach ( $rows as $key => $row ) {
			if ( ! ctype_digit( (string) $key ) || ! is_array( $row ) ) { continue; }
			$id = number( $row, 'id' );
			if ( $id && ( ! in_array( $id, $children, true ) || isset( $rendered[ $id ] ) ) ) { continue; }
			variation_form( (int) $key, $id ? wc_get_product( $id ) : null );
			$rendered[ $id ] = true; $index = max( $index, (int) $key + 1 );
		}
		foreach ( $children as $child ) { if ( ! isset( $rendered[ $child ] ) ) { $v = wc_get_product( $child ); if ( $v ) { variation_form( $index++, $v ); } } }
		echo '</div><button type="button" class="mp-secondary" id="mp-add-variation" data-index="' . esc_attr( $index ) . '">Adicionar variação</button><template id="mp-variation-template">'; variation_form( '__INDEX__' ); echo '</template>';
	}
	echo '</section><div class="mp-form-actions"><button type="submit">Salvar produto</button></div></form>';
	if ( $p ) { form_start( 'product_trash', 'class="mp-card mp-danger" data-confirm="Enviar este produto à lixeira? Ele deixará de aparecer na loja."' ); echo '<input type="hidden" name="id" value="' . esc_attr( $p->get_id() ) . '"><input type="hidden" name="version" value="' . esc_attr( $date ? $date->getTimestamp() : 0 ) . '"><label class="mp-check"><input type="checkbox" name="confirm" value="1" required>Confirmo o envio à lixeira</label><button type="submit" class="mp-secondary">Enviar à lixeira</button></form>'; }
}
function screen_categories() {
	echo '<div class="mp-heading"><h1>Categorias</h1></div>'; $id = number( $_GET, 'edit' ); $term = $id ? get_term( $id, 'product_cat' ) : null; if ( $id && ( ! $term || is_wp_error( $term ) ) ) { throw new \RuntimeException( 'Categoria não encontrada.' ); }
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ); if ( is_wp_error( $terms ) ) { throw new \RuntimeException( 'Categorias indisponíveis.' ); }
	echo '<section class="mp-card"><h2>' . ( $term ? 'Editar categoria' : 'Nova categoria' ) . '</h2>'; form_start( 'category_save' ); echo '<input type="hidden" name="id" value="' . esc_attr( $id ) . '"><div class="mp-form-grid">'; input( 'name', 'Nome', $term ? $term->name : '', 'text', 'required' ); $choices = array( 0 => 'Sem categoria superior' ); foreach ( $terms as $item ) { if ( $item->term_id !== $id ) { $choices[ $item->term_id ] = $item->name; } } select( 'parent', 'Categoria superior', $choices, $term ? $term->parent : 0 ); echo '</div>'; textarea( 'description', 'Descrição da categoria (resumo nos cards)', $term ? $term->description : '' ); image_picker( 'thumbnail_id', 'Imagem da categoria', $term ? get_term_meta( $term->term_id, 'thumbnail_id', true ) : 0 ); echo '<button type="submit">Salvar categoria</button></form></section><div class="mp-card mp-table"><table><thead><tr><th>Nome</th><th>Produtos</th><th>Ação</th></tr></thead><tbody>';
	foreach ( $terms as $item ) { echo '<tr><td>' . esc_html( $item->name ) . '</td><td>' . esc_html( $item->count ) . '</td><td><a href="' . esc_url( url( 'categorias', array( 'edit' => $item->term_id ) ) ) . '">Editar</a></td></tr>'; } echo '</tbody></table></div>';
}
function screen_stock() {
	echo '<h1>Estoque</h1><p>Atualizações são aplicadas diretamente ao WooCommerce. Para produtos variáveis, ajuste cada variação.</p><form class="mp-filters" method="get">'; input( 'q', 'Buscar produto', text( $_GET, 'q' ), 'search' ); select( 'stock', 'Disponibilidade', array( '' => 'Todas', 'instock' => 'Disponível', 'outofstock' => 'Esgotado' ), text( $_GET, 'stock' ) ); echo '<button>Consultar</button></form>'; $query = catalog_query( true );
	echo '<div class="mp-card mp-table"><table><thead><tr><th>Produto / SKU</th><th>Controle</th><th>Quantidade</th></tr></thead><tbody>'; foreach ( $query->posts as $id ) { $p = wc_get_product( $id ); if ( ! $p ) { continue; } echo '<tr><td>' . esc_html( $p->get_name() ) . '<small>' . esc_html( $p->get_sku() ) . '</small></td><td>' . ( $p->get_manage_stock() ? 'Individual' : 'Sem controle individual' ) . '</td><td>'; if ( true === $p->get_manage_stock( 'edit' ) ) { form_start( 'stock_save', 'class="mp-inline"' ); echo '<input type="hidden" name="id" value="' . esc_attr( $id ) . '"><input type="hidden" name="old_stock" value="' . esc_attr( $p->get_stock_quantity( 'edit' ) ) . '">'; input( 'stock_quantity', 'Quantidade', $p->get_stock_quantity( 'edit' ), 'number', 'min="0" step="1" required' ); echo '<button>Atualizar</button></form>'; } else { echo '<a href="' . esc_url( url( 'produto/' . ( $p->is_type( 'variation' ) ? $p->get_parent_id() : $id ) ) ) . '">Editar produto</a>'; } echo '</td></tr>'; } if ( ! $query->posts ) { echo '<tr><td colspan="3">Nenhum produto encontrado.</td></tr>'; } echo '</tbody></table></div>'; pages( max( 1, number( $_GET, 'pg', 1 ) ), $query->max_num_pages );
}
