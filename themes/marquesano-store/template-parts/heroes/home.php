<?php defined( 'ABSPATH' ) || exit; ?>
<section class="hero hero-home home-section">
	<?php get_template_part( 'template-parts/heroes/media' ); ?>
	<div class="living-panel"><?php get_template_part( 'template-parts/heroes/copy' ); ?></div>
	<p class="hero-footnote"><?php echo esc_html( marquesano_store_identity()['caption'] ); ?></p>
</section>
