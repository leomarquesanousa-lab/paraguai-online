<?php
/**
 * Module bootstrap.
 *
 * @package MarquesanoAdmin
 */

namespace Marquesano\Admin;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	/**
	 * Load implemented modules after other plugins have initialized.
	 */
	public static function boot() {
		require_once __DIR__ . '/modules.php';
	}
}
