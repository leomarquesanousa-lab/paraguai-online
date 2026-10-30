<?php
/** Read-only catalog filters using native WooCommerce query parameters. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
$read = static function ( $key ) { return isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : ''; }; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<form class="catalog-filters" action="<?php echo esc_url( marquesano_store_shop_url() ); ?>" method="get" role="search">
	<input type="hidden" name="post_type" value="product">
	<?php if ( '' === get_option( 'permalink_structure' ) && wc_get_page_id( 'shop' ) > 0 ) : ?><input type="hidden" name="page_id" value="<?php echo esc_attr( wc_get_page_id( 'shop' ) ); ?>"><?php endif; ?>
	<label><?php esc_html_e( 'Buscar produto', 'marquesano-store' ); ?><input type="search" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>"></label>
	<label for="ms-category"><?php esc_html_e( 'Categoria', 'marquesano-store' ); ?>
	<?php wp_dropdown_categories( array( 'taxonomy' => 'product_cat', 'name' => 'product_cat', 'id' => 'ms-category', 'value_field' => 'slug', 'show_option_all' => __( 'Todas', 'marquesano-store' ), 'option_none_value' => '', 'selected' => is_product_category() ? get_queried_object()->slug : sanitize_title( $read( 'product_cat' ) ), 'hierarchical' => true, 'hide_empty' => true ) ); ?>
	</label>
	<label><?php esc_html_e( 'Preço mínimo', 'marquesano-store' ); ?><input type="number" name="min_price" min="0" step="any" value="<?php echo esc_attr( $read( 'min_price' ) ); ?>"></label>
	<label><?php esc_html_e( 'Preço máximo', 'marquesano-store' ); ?><input type="number" name="max_price" min="0" step="any" value="<?php echo esc_attr( $read( 'max_price' ) ); ?>"></label>
	<label>Disponibilidade<select name="stock_status"><?php foreach ( array( '' => 'Todas', 'instock' => 'Disponível', 'outofstock' => 'Esgotado', 'onbackorder' => 'Sob encomenda' ) as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $read( 'stock_status' ), $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
	<?php if ( $read( 'orderby' ) ) : ?><input type="hidden" name="orderby" value="<?php echo esc_attr( $read( 'orderby' ) ); ?>"><?php endif; ?>
	<?php wc_query_string_form_fields( null, array( 's', 'post_type', 'product_cat', 'min_price', 'max_price', 'stock_status', 'orderby', 'paged', 'product-page', 'page_id', 'submit', 'add-to-cart' ) ); ?>
	<?php if ( is_product_tag() && ! $read( 'product_tag' ) ) : ?><input type="hidden" name="product_tag" value="<?php echo esc_attr( get_queried_object()->slug ); ?>"><?php endif; ?>
	<button type="submit"><?php esc_html_e( 'Filtrar', 'marquesano-store' ); ?></button>
	<a href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Limpar', 'marquesano-store' ); ?></a>
</form>
