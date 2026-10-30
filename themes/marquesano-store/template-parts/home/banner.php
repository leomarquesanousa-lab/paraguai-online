<?php
/** Optional campaign banner. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;
if ( ! marquesano_store_setting( 'banner_title' ) ) { return; }
?>
<section class="home-section campaign-banner">
	<?php if ( marquesano_store_setting( 'banner_image' ) ) { echo wp_get_attachment_image( marquesano_store_setting( 'banner_image' ), 'large' ); } ?>
	<div><h2><?php echo esc_html( marquesano_store_setting( 'banner_title' ) ); ?></h2>
	<p><?php echo nl2br( esc_html( marquesano_store_setting( 'banner_text' ) ) ); ?></p>
	<?php if ( marquesano_store_setting( 'banner_url' ) ) : ?><a class="button" href="<?php echo esc_url( marquesano_store_setting( 'banner_url' ) ); ?>"><?php esc_html_e( 'Saiba mais', 'marquesano-store' ); ?></a><?php endif; ?></div>
</section>
