<?php
/** Up to six real top-level WooCommerce categories, including empty categories. */
defined( 'ABSPATH' ) || exit;
if ( ! taxonomy_exists( 'product_cat' ) ) { return; }
$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'number' => 6, 'orderby' => 'name', 'order' => 'ASC' ) );
if ( is_wp_error( $terms ) || ! $terms ) { return; }
?>
<section class="home-section tech-category-section" aria-label="<?php esc_attr_e( 'Categorias de produtos', 'marquesano-store' ); ?>">
	<div class="tech-category-rail">
	<?php foreach ( $terms as $term ) : $url = get_term_link( $term ); if ( is_wp_error( $url ) ) { continue; } ?>
		<a class="tech-category-item" href="<?php echo esc_url( $url ); ?>">
			<?php $thumbnail = wp_get_attachment_image( get_term_meta( $term->term_id, 'thumbnail_id', true ), 'thumbnail', false, array( 'alt' => '' ) ); echo $thumbnail ?: marquesano_store_tech_icon( 'grid' ); ?>
			<span><strong><?php echo esc_html( $term->name ); ?></strong><small><?php $summary = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $term->description ) ), 8, '…' ); echo esc_html( $summary ?: __( 'Explore a categoria', 'marquesano-store' ) ); ?></small></span><span aria-hidden="true">›</span>
		</a>
	<?php endforeach; ?>
	</div>
</section>