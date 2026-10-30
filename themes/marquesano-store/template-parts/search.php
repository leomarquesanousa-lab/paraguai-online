<?php defined( 'ABSPATH' ) || exit; $search_id = wp_unique_id( 'ms-search-' ); ?>
<form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Buscar produtos', 'marquesano-store' ); ?></label>
	<input id="<?php echo esc_attr( $search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>" placeholder="<?php esc_attr_e( 'O que você procura?', 'marquesano-store' ); ?>">
	<input type="hidden" name="post_type" value="product">
	<button type="submit" aria-label="<?php esc_attr_e( 'Buscar produtos', 'marquesano-store' ); ?>"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg><span class="screen-reader-text"><?php esc_html_e( 'Buscar', 'marquesano-store' ); ?></span></button>
</form>
