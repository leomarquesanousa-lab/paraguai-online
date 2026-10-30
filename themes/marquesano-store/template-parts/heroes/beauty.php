<?php defined( 'ABSPATH' ) || exit; ?>
<section class="hero hero-beauty home-section">
	<p class="beauty-overline"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
	<?php get_template_part( 'template-parts/heroes/copy' ); ?>
	<div class="beauty-stage"><?php get_template_part( 'template-parts/heroes/media' ); ?><span class="beauty-seal" aria-hidden="true">Essência<br>& cuidado</span></div>
	<p class="hero-footnote"><?php echo esc_html( marquesano_store_identity()['caption'] ); ?></p>
</section>
