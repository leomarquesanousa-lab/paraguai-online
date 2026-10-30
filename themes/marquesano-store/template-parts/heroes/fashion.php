<?php defined( 'ABSPATH' ) || exit; ?>
<section class="hero hero-fashion home-section">
	<div class="editorial-spine" aria-hidden="true"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
	<?php get_template_part( 'template-parts/heroes/media' ); ?>
	<div class="editorial-panel"><?php get_template_part( 'template-parts/heroes/copy' ); ?></div>
	<p class="hero-footnote"><?php echo esc_html( marquesano_store_identity()['caption'] ); ?></p>
</section>
