<?php
/** Frontend operations, separate from storefront and technical wp-admin. */
namespace Marquesano\Admin\Panel;
defined( 'ABSPATH' ) || exit;
foreach ( array( 'support', 'access', 'products', 'commerce', 'settings', 'navigation', 'router', 'views' ) as $module ) { require_once __DIR__ . '/' . $module . '.php'; }
add_action( 'init', array( Access::class, 'register_role' ) );
add_action( 'admin_init', array( Access::class, 'restrict_admin' ), 1 );
add_filter( 'show_admin_bar', static function ( $show ) { return Access::merchant() ? false : $show; } );
add_filter( 'login_redirect', static function ( $redirect, $requested, $user ) { return $user instanceof \WP_User && Access::user_allowed( $user ) && ! $user->has_cap( 'manage_options' ) ? url() : $redirect; }, 10, 3 );
add_filter( 'woocommerce_login_redirect', static function ( $redirect, $user ) { return $user instanceof \WP_User && Access::user_allowed( $user ) && ! $user->has_cap( 'manage_options' ) ? url() : $redirect; }, 10, 2 );
add_action( 'template_redirect', array( Router::class, 'dispatch' ), 0 );
