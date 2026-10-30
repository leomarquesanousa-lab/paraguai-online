<?php
/**
 * Plugin Name: Marquesano Admin
 * Description: Painel operacional frontend e administração modular da Marquesano Store sobre WooCommerce.
 * Version: 1.0.0
 * Author: Marquesano
 * Text Domain: marquesano-admin
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package MarquesanoAdmin
 */

defined( 'ABSPATH' ) || exit;

// Declare storage compatibility before WooCommerce initializes its features.
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

require_once __DIR__ . '/includes/class-plugin.php';

add_action( 'plugins_loaded', array( '\Marquesano\Admin\Plugin', 'boot' ), 20 );
