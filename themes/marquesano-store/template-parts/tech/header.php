<?php defined( 'ABSPATH' ) || exit; ?>
<header class="tech-header">
	<div class="tech-header-inner">
		<div class="site-branding"><?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a><?php } ?></div>
		<button type="button" class="po-menu-toggle" hidden aria-controls="po-main-menu" aria-expanded="false"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6h18M3 12h18M3 18h18"/></svg><span>Menu</span></button>
		<nav id="po-main-menu" class="tech-navigation po-tech-navigation" aria-label="Navegação principal"><?php paraguai_tech_navigation(); ?></nav>
		<div class="header-search"><?php get_template_part( 'template-parts/search' ); ?></div>
		<div class="tech-account-actions">
		<?php if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ) : ?>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo marquesano_store_tech_icon( 'user' ); ?><span><?php esc_html_e( 'Minha conta', 'marquesano-store' ); ?></span></a>
		<?php endif; ?>
		<?php if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'cart' ) > 0 ) : ?>
		<a class="tech-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo marquesano_store_tech_icon( 'cart' ); ?><?php echo marquesano_store_tech_cart_count(); ?><span><?php esc_html_e( 'Carrinho', 'marquesano-store' ); ?></span></a>
		<?php endif; ?>
		</div>
	</div>
</header>
