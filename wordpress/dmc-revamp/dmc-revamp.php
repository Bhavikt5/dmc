<?php
/**
 * Plugin Name:       DMC Nuts Revamp
 * Description:       Design system, header/footer, GSAP + Three.js motion and WooCommerce shop/product templates for the DMC Nuts redesign. Works with Elementor (free) and the Hello Elementor theme.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            DMC Nuts
 * Text Domain:       dmc-revamp
 */

defined( 'ABSPATH' ) || exit;

define( 'DMC_REVAMP_VERSION', '1.0.0' );
define( 'DMC_REVAMP_DIR', plugin_dir_path( __FILE__ ) );
define( 'DMC_REVAMP_URL', plugin_dir_url( __FILE__ ) );

// Business contact details used across the header, footer and enquiry buttons.
define( 'DMC_PHONE', '+917977635556' );
define( 'DMC_PHONE_DISPLAY', '+91 79776 35556' );
define( 'DMC_WHATSAPP', '917977635556' );
define( 'DMC_EMAIL', 'deepakmangeandco@gmail.com' );
define( 'DMC_ADDRESS', 'L-41, Phase-2, APMC Market, Turbhe, Vashi, Navi Mumbai 400703' );
define( 'DMC_MAPS', 'https://maps.app.goo.gl/W9XjvdCmkQWjnR6T6' );
define( 'DMC_INSTAGRAM', 'https://www.instagram.com/dmc.nuts' );

require_once DMC_REVAMP_DIR . 'includes/icons.php';
require_once DMC_REVAMP_DIR . 'includes/products.php';
require_once DMC_REVAMP_DIR . 'includes/shortcodes.php';
require_once DMC_REVAMP_DIR . 'includes/admin.php';

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', 'dmc_enqueue_assets', 999 );
function dmc_enqueue_assets() {
	wp_enqueue_style(
		'dmc-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:ital,opsz,wght@1,9..144,400;1,9..144,500&family=Outfit:wght@500;600;700;800&display=swap',
		array(),
		null
	);

	// Load after Elementor / Hello / WooCommerce so equal-specificity rules resolve in our favour.
	$deps = array( 'dmc-fonts' );
	foreach ( array( 'elementor-frontend', 'hello-elementor', 'hello-elementor-theme-style', 'woocommerce-general' ) as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			$deps[] = $handle;
		}
	}
	wp_enqueue_style( 'dmc-revamp', DMC_REVAMP_URL . 'assets/css/dmc.css', $deps, DMC_REVAMP_VERSION );
	wp_enqueue_style( 'dmc-revamp-wp', DMC_REVAMP_URL . 'assets/css/dmc-wp.css', array( 'dmc-revamp' ), DMC_REVAMP_VERSION );

	wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );

	// Smooth scrolling fights the Elementor editor's own scrolling, so skip it there.
	$in_editor = isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$js_deps   = array( 'gsap', 'gsap-scrolltrigger' );
	if ( ! $in_editor ) {
		wp_enqueue_script( 'lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js', array(), '1.1.13', true );
		$js_deps[] = 'lenis';
	}
	wp_enqueue_script( 'dmc-revamp', DMC_REVAMP_URL . 'assets/js/dmc.js', $js_deps, DMC_REVAMP_VERSION, true );

	// Three.js is ~600 KB: only load it on pages that contain a 3D scene.
	if ( dmc_page_needs_three() ) {
		wp_enqueue_script( 'three', 'https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js', array(), '0.149.0', true );
		wp_enqueue_script( 'dmc-three', DMC_REVAMP_URL . 'assets/js/dmc-three.js', array( 'three', 'dmc-revamp' ), DMC_REVAMP_VERSION, true );
	}
}

/**
 * True when the current page's content (or Elementor data) contains a Three.js mount point.
 */
function dmc_page_needs_three() {
	if ( ! is_singular() ) {
		return false;
	}
	$post_id = get_queried_object_id();
	$haystack = (string) get_post_field( 'post_content', $post_id ) . (string) get_post_meta( $post_id, '_elementor_data', true );
	return false !== strpos( $haystack, 'data-dmc-nuts' ) || false !== strpos( $haystack, 'data-dmc-particles' );
}

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'dmc-body';
	return $classes;
} );

/* -------------------------------------------------------------------------
 * Header & footer
 *
 * Hello Elementor: its own header/footer is switched off and ours is printed
 * instead. Other themes: set "Theme header/footer" off in the theme, or turn
 * the option below off and keep the theme's header.
 * ---------------------------------------------------------------------- */

add_filter( 'hello_elementor_header_footer', '__return_false' );

add_action( 'wp_body_open', 'dmc_render_header', 5 );
function dmc_render_header() {
	if ( dmc_skip_chrome() ) {
		return;
	}
	include DMC_REVAMP_DIR . 'templates/header.php';
}

add_action( 'wp_footer', 'dmc_render_footer', 5 );
function dmc_render_footer() {
	if ( dmc_skip_chrome() ) {
		return;
	}
	include DMC_REVAMP_DIR . 'templates/footer.php';
}

/**
 * Elementor Canvas pages and the editor preview iframe for templates get no header/footer.
 */
function dmc_skip_chrome() {
	if ( is_singular() && 'elementor_canvas' === get_page_template_slug( get_queried_object_id() ) ) {
		return true;
	}
	return (bool) apply_filters( 'dmc_skip_chrome', false );
}

/* -------------------------------------------------------------------------
 * WooCommerce: shop archive + single product use our templates
 * ---------------------------------------------------------------------- */

add_filter( 'template_include', 'dmc_woo_templates', 99 );
function dmc_woo_templates( $template ) {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return $template;
	}
	if ( is_product() ) {
		return DMC_REVAMP_DIR . 'templates/single-product.php';
	}
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		return DMC_REVAMP_DIR . 'templates/archive-product.php';
	}
	return $template;
}

// The catalogue is small and filtered client-side, so show it on one page.
add_filter( 'loop_shop_per_page', function () {
	return 48;
}, 20 );

/**
 * Helper for page links: the page with the given slug, or a fallback URL.
 */
function dmc_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	return $fallback ? $fallback : home_url( '/' . $slug . '/' );
}

function dmc_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

function dmc_whatsapp_url( $text = '' ) {
	return 'https://wa.me/' . DMC_WHATSAPP . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}
