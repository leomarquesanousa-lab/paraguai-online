<?php
defined( 'ABSPATH' ) || exit;
$links = array(
	1 => array( 'icon' => 'box', 'url' => marquesano_store_shop_url() ),
	2 => array( 'icon' => 'user', 'url' => function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ? wc_get_page_permalink( 'myaccount' ) : '' ),
	3 => array( 'icon' => 'cart', 'url' => function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'cart' ) > 0 ? wc_get_cart_url() : '' ),
	4 => array( 'icon' => 'grid', 'url' => marquesano_store_shop_url() ),
);
?>
<div class="tech-service-links">
	<?php foreach ( $links as $slot => $link ) :
		$text = marquesano_store_setting( 'tech_service_' . $slot );
		if ( ! $text || ! $link['url'] ) { continue; }
		$parts = explode( '|', $text, 2 ); ?>
		<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo marquesano_store_tech_icon( $link['icon'] ); ?><span><?php echo esc_html( $parts[0] ); ?><?php if ( ! empty( $parts[1] ) ) : ?><small><?php echo esc_html( $parts[1] ); ?></small><?php endif; ?></span></a>
	<?php endforeach; ?>
</div>
