<?php
/** Composable storefront. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;

// Preserve a configured home page that also serves a transactional WooCommerce flow.
if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
	require get_template_directory() . '/page.php';
	return;
}
get_header();
?>
<main id="main-content" class="store-home">
	<?php
	$sections = explode( ',', marquesano_store_setting( 'sections' ) );
	if ( ! in_array( 'hero', $sections, true ) ) { echo '<h1 class="screen-reader-text">' . esc_html( get_bloginfo( 'name' ) ) . '</h1>'; }
	foreach ( $sections as $section ) {
		if ( in_array( $section, array( 'spotlight', 'discovery' ), true ) ) {
			if ( 'tech' === marquesano_store_setting( 'preset' ) ) { get_template_part( 'template-parts/tech/' . $section ); }
			continue;
		}
		if ( in_array( $section, array( 'hero', 'categories', 'offers', 'products', 'banner', 'about' ), true ) ) {
			get_template_part( 'template-parts/home/' . $section );
		}
	}
	// Keep existing static-front-page content editable in the WordPress page editor.
	if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
		while ( have_posts() ) {
			the_post();
			if ( trim( get_the_content() ) ) {
				echo '<section class="home-section page-content">';
				the_content();
				wp_link_pages();
				echo '</section>';
			}
		}
	}
	?>
</main>
<?php get_footer(); ?>
