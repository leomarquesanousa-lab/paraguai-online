<?php
/** Missing resource. WordPress sets the HTTP status. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main-content" class="site-main">
	<h1><?php esc_html_e( 'Página não encontrada', 'marquesano-store' ); ?></h1>
	<p><?php esc_html_e( 'Confira o endereço ou encontre um produto na busca.', 'marquesano-store' ); ?></p>
	<?php if ( function_exists( 'get_product_search_form' ) ) { get_product_search_form(); } else { get_search_form(); } ?>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Voltar ao início', 'marquesano-store' ); ?></a></p>
</main>
<?php get_footer(); ?>
