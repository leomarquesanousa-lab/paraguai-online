<?php
/**
 * Site header.
 *
 * @package MarquesanoStore
 */
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main-content"><?php esc_html_e( 'Pular para o conteúdo', 'marquesano-store' ); ?></a>
<?php if ( paraguai_online_checkout_shell() ) : ?>
<header class="po-checkout-header"><div class="po-checkout-header-inner">
	<a class="po-checkout-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo paraguai_online_brand_image( 'eager' ) ?: esc_html( 'Paraguai Online' ); ?></a>
	<a class="po-checkout-back" href="<?php echo esc_url( wc_get_cart_url() ); ?>">Voltar ao carrinho <span aria-hidden="true">↗</span></a>
</div></header>
<?php return; endif; ?>
<?php if ( marquesano_store_setting( 'announcement' ) ) : ?>
<div class="announcement"><?php echo esc_html( marquesano_store_setting( 'announcement' ) ); ?></div>
<?php endif; ?>
<?php if ( 'tech' === marquesano_store_setting( 'preset' ) ) : get_template_part( 'template-parts/tech/header' ); else : ?>
<header class="site-header">
	<div class="site-branding">
		<?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?>
		<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
		<?php } ?>
	</div>
	<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Navegação principal', 'marquesano-store' ); ?>">
		<?php if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 2 ) );
		} else { ?>
		<ul><li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Início', 'marquesano-store' ); ?></a></li>
		<?php if ( function_exists( 'WC' ) ) : ?><li><a href="<?php echo esc_url( marquesano_store_shop_url() ); ?>"><?php esc_html_e( 'Todos os produtos', 'marquesano-store' ); ?></a></li>
		<?php $nav_categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 4, 'exclude' => marquesano_store_demo_category_exclusions() ) );
		if ( ! is_wp_error( $nav_categories ) ) { foreach ( $nav_categories as $nav_category ) { $nav_url = get_term_link( $nav_category ); if ( ! is_wp_error( $nav_url ) ) { echo '<li><a href="' . esc_url( $nav_url ) . '">' . esc_html( $nav_category->name ) . '</a></li>'; } } } ?>
		<?php endif; ?></ul>
		<?php } ?>
	</nav>
	<div class="header-actions">
		<?php if ( function_exists( 'WC' ) ) : ?>
		<?php if ( wc_get_page_id( 'myaccount' ) > 0 ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Minha conta', 'marquesano-store' ); ?></a><?php endif; ?>
		<?php if ( wc_get_page_id( 'cart' ) > 0 ) : ?><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Carrinho', 'marquesano-store' ); ?></a><?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if ( function_exists( 'get_product_search_form' ) ) : ?><div class="header-search"><?php get_template_part( 'template-parts/search' ); ?></div><?php endif; ?>
</header>
<?php endif; ?>
