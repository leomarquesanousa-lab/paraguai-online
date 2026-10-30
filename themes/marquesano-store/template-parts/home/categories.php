<?php
/** Real, nonempty product categories. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
if ( 'tech' === marquesano_store_setting( 'preset' ) ) { get_template_part( 'template-parts/tech/categories' ); return; }
if ( ! taxonomy_exists( 'product_cat' ) ) { return; }
$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 8, 'exclude' => marquesano_store_demo_category_exclusions() ) );
if ( is_wp_error( $categories ) || ! $categories ) { return; }
?>
<section class="home-section categories-section">
	<div class="section-heading"><div><p class="eyebrow"><?php esc_html_e( 'Descubra por categoria', 'marquesano-store' ); ?></p><h2><?php echo esc_html( marquesano_store_identity()['categories_title'] ); ?></h2></div></div>
	<div class="category-grid">
	<?php foreach ( $categories as $category ) : $url = get_term_link( $category ); if ( is_wp_error( $url ) ) { continue; } ?>
		<a class="category-card" href="<?php echo esc_url( $url ); ?>">
			<?php $image = get_term_meta( $category->term_id, 'thumbnail_id', true ); if ( $image ) { echo wp_get_attachment_image( $image, 'large', false, array( 'alt' => '' ) ); } else { ?><span class="category-art" aria-hidden="true"><span><?php echo esc_html( sprintf( '%02d', array_search( $category, $categories, true ) + 1 ) ); ?></span></span><?php } ?>
			<span class="category-caption"><span><?php echo esc_html( $category->name ); ?></span><span aria-hidden="true">↗</span></span>
		</a>
	<?php endforeach; ?>
	</div>
</section>
