<?php defined( 'ABSPATH' ) || exit; $identity = marquesano_store_identity(); ?>
<div class="hero-copy">
	<p class="eyebrow"><span class="eyebrow-line" aria-hidden="true"></span><?php echo esc_html( $identity['eyebrow'] ); ?></p>
	<h1><?php echo esc_html( marquesano_store_setting( 'hero_title' ) ); ?></h1>
	<p class="hero-description"><?php echo nl2br( esc_html( marquesano_store_setting( 'hero_text' ) ) ); ?></p>
	<?php if ( marquesano_store_setting( 'hero_button' ) ) : ?>
	<a class="button hero-cta" href="<?php echo esc_url( marquesano_store_setting( 'hero_url' ) ?: marquesano_store_shop_url() ); ?>"><?php echo esc_html( marquesano_store_setting( 'hero_button' ) ); ?><span aria-hidden="true">↗</span></a>
	<?php endif; ?>
</div>
