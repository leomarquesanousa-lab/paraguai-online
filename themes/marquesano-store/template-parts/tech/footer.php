<?php defined( 'ABSPATH' ) || exit;
$has_contact = false;
foreach ( array( 'email', 'phone', 'address', 'instagram', 'facebook', 'whatsapp' ) as $field ) { if ( marquesano_store_setting( $field ) ) { $has_contact = true; break; } }
?>
<footer class="signature-footer">
	<div class="signature-footer-top <?php echo $has_contact ? 'has-contact' : ''; ?>"><div class="signature-footer-brand"><a class="po-footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo paraguai_online_brand_image() ?: esc_html( 'Paraguai Online' ); ?></a><p><?php echo esc_html( marquesano_store_setting( 'tech_footer_text' ) ); ?></p></div>
	<nav aria-label="<?php esc_attr_e( 'Coleção', 'marquesano-store' ); ?>"><h2><?php esc_html_e( 'Explore', 'marquesano-store' ); ?></h2><a href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Todos os produtos', 'marquesano-store' ); ?></a>
	<?php if ( taxonomy_exists( 'product_cat' ) ) { $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 4, 'exclude' => marquesano_store_demo_category_exclusions() ) ); if ( ! is_wp_error( $terms ) ) { foreach ( $terms as $term ) { $url = get_term_link( $term ); if ( ! is_wp_error( $url ) ) { echo '<a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>'; } } } } ?>
	</nav><nav aria-label="<?php esc_attr_e( 'Sua conta', 'marquesano-store' ); ?>"><h2><?php esc_html_e( 'Sua experiência', 'marquesano-store' ); ?></h2>
	<?php if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Minha conta', 'marquesano-store' ); ?></a><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Meus pedidos', 'marquesano-store' ); ?></a><?php endif; ?>
	<?php if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'cart' ) > 0 ) : ?><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Carrinho', 'marquesano-store' ); ?></a><?php endif; ?>
	<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); the_privacy_policy_link(); ?></nav>
	<?php if ( $has_contact ) : ?><div class="signature-footer-contact">
	<?php if ( marquesano_store_setting( 'email' ) || marquesano_store_setting( 'phone' ) || marquesano_store_setting( 'address' ) ) : ?><h2><?php esc_html_e( 'Vamos conversar', 'marquesano-store' ); ?></h2><?php endif; ?>
	<?php if ( marquesano_store_setting( 'email' ) ) : ?><a href="<?php echo esc_url( 'mailto:' . marquesano_store_setting( 'email' ) ); ?>"><?php echo esc_html( marquesano_store_setting( 'email' ) ); ?></a><?php endif; ?>
	<?php foreach ( array( 'phone', 'address' ) as $field ) { if ( marquesano_store_setting( $field ) ) { echo '<p>' . nl2br( esc_html( marquesano_store_setting( $field ) ) ) . '</p>'; } } ?>
	<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'whatsapp' => 'WhatsApp' ) as $key => $label ) { if ( marquesano_store_setting( $key ) ) { echo '<a href="' . esc_url( marquesano_store_setting( $key ) ) . '">' . esc_html( $label ) . '</a>'; } } ?>
	</div><?php endif; ?></div><div class="signature-footer-bottom"><?php get_template_part( 'template-parts/tech/service-links' ); ?></div>
</footer>
