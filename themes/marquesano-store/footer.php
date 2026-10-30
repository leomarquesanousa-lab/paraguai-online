<?php
/**
 * Site footer.
 *
 * @package MarquesanoStore
 */
defined( 'ABSPATH' ) || exit;
?>
<?php if ( paraguai_online_checkout_shell() ) : ?>
<footer class="po-checkout-footer"><span>Paraguai Online</span><nav aria-label="Informações do checkout"><?php the_privacy_policy_link(); ?><a href="<?php echo esc_url( marquesano_store_shop_url() ); ?>">Voltar à loja</a></nav></footer>
<?php wp_footer(); ?></body></html><?php return; endif; ?>
<?php if ( 'tech' === marquesano_store_setting( 'preset' ) ) : get_template_part( 'template-parts/tech/footer' ); else : ?>
<footer class="site-footer">
	<div><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo paraguai_online_brand_image() ?: esc_html( 'Paraguai Online' ); ?></a>
	<?php if ( marquesano_store_setting( 'address' ) ) : ?><p><?php echo nl2br( esc_html( marquesano_store_setting( 'address' ) ) ); ?></p><?php endif; ?>
	<?php if ( marquesano_store_setting( 'phone' ) ) : ?><p><?php echo esc_html( marquesano_store_setting( 'phone' ) ); ?></p><?php endif; ?>
	<?php if ( marquesano_store_setting( 'email' ) ) : ?><p><a href="<?php echo esc_url( 'mailto:' . marquesano_store_setting( 'email' ) ); ?>"><?php echo esc_html( marquesano_store_setting( 'email' ) ); ?></a></p><?php endif; ?>
	</div>
	<nav aria-label="<?php esc_attr_e( 'Links institucionais', 'marquesano-store' ); ?>">
	<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?>
	<?php the_privacy_policy_link(); ?>
	</nav>
	<div class="social-links">
	<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'whatsapp' => 'WhatsApp' ) as $key => $label ) : ?>
		<?php if ( marquesano_store_setting( $key ) ) : ?><a href="<?php echo esc_url( marquesano_store_setting( $key ) ); ?>" rel="external"><?php echo esc_html( $label ); ?></a><?php endif; ?>
	<?php endforeach; ?>
	</div>
</footer>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
