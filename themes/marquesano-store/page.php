<?php
/** Native pages, including cart, checkout and account blocks/shortcodes. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main-content" class="site-main page-main">
	<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( paraguai_online_checkout_shell() && ! is_wc_endpoint_url() ) : ?>
		<div class="po-checkout-heading"><p class="po-checkout-eyebrow">PARAGUAI ONLINE / CHECKOUT</p><h1>Seu próximo movimento.</h1><p>Confira sua seleção e finalize seu pedido.</p></div>
		<?php elseif ( paraguai_online_is_cart() ) : ?>
		<div class="po-cart-heading"><div><p class="po-cart-eyebrow">PARAGUAI ONLINE / SUA SELEÇÃO</p><h1>Seu carrinho.</h1><p>Confira os itens antes de continuar.</p></div><a class="po-cart-continue" href="<?php echo esc_url( marquesano_store_shop_url() ); ?>">Continuar comprando <span aria-hidden="true">↗</span></a></div>
		<?php elseif ( function_exists( 'is_account_page' ) && is_account_page() ) : ?>
		<div class="po-account-heading"><p class="po-account-eyebrow">PARAGUAI ONLINE / MINHA CONTA</p><h1><?php the_title(); ?></h1><p><?php echo is_user_logged_in() ? 'Seus pedidos e dados, em um só lugar.' : 'Acesse sua conta para acompanhar seus pedidos.'; ?></p></div>
		<?php else : ?><h1><?php the_title(); ?></h1><?php endif; ?>
		<?php the_content(); wp_link_pages(); ?>
	</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
