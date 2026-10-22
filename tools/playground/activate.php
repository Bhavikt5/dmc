<?php
/**
 * Playground only: activate theme + plugins in PHP (the blueprint activatePlugin step fails on Windows).
 */
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );
ini_set( 'display_errors', '1' );
error_reporting( E_ALL );
require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

switch_theme( 'hello-elementor' );
foreach ( array( 'woocommerce/woocommerce.php', 'elementor/elementor.php', 'dmc-revamp/dmc-revamp.php' ) as $plugin ) {
	$r = activate_plugin( $plugin );
	echo $plugin . ': ' . ( is_wp_error( $r ) ? 'ERROR ' . $r->get_error_message() : 'active' ) . "\n";
}
// WooCommerce normally creates its pages/tables on the first admin load.
if ( class_exists( 'WC_Install' ) ) {
	WC_Install::install();
	WC_Install::create_pages();
}
echo 'theme: ' . get_stylesheet() . "\n";
