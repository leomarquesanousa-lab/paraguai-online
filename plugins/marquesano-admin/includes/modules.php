<?php
/**
 * Registration point for future modules.
 *
 * Admin modules check WooCommerce availability before querying commerce data.
 *
 * @package MarquesanoAdmin
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/dashboard.php';
require_once __DIR__ . '/management.php';
require_once __DIR__ . '/panel/bootstrap.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once dirname( __DIR__ ) . '/demo/class-demo-command.php';
}

// Local audit context only: signed requests, short-lived run and no auth bypass.
if ( ! empty( $_SERVER['HTTP_X_MARQUESANO_AUDIT'] ) ) { require_once dirname( __DIR__ ) . '/tests/audit/context.php'; }
