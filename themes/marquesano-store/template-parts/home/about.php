<?php
/** Optional institutional section. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
if ( ! marquesano_store_setting( 'about_title' ) && ! marquesano_store_setting( 'about_text' ) ) { return; }
?>
<section class="home-section about-section">
	<h2><?php echo esc_html( marquesano_store_setting( 'about_title' ) ); ?></h2>
	<p><?php echo nl2br( esc_html( marquesano_store_setting( 'about_text' ) ) ); ?></p>
</section>
