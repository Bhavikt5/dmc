<?php
/**
 * Product edit screen: "Bag colour" and "Badge" fields in the General tab.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'woocommerce_product_options_general_product_data', function () {
	echo '<div class="options_group">';
	woocommerce_wp_text_input(
		array(
			'id'          => '_dmc_swatch',
			'label'       => __( 'Bag colour (hex)', 'dmc-revamp' ),
			'placeholder' => '#f39233',
			'description' => __( 'Colour dot shown on cards, the grade selector and the grade chart.', 'dmc-revamp' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_dmc_badge',
			'label'       => __( 'Badge', 'dmc-revamp' ),
			'placeholder' => __( 'Bestseller', 'dmc-revamp' ),
			'description' => __( 'Optional label shown on the product card and product page.', 'dmc-revamp' ),
			'desc_tip'    => true,
		)
	);
	echo '</div>';
} );

add_action( 'woocommerce_admin_process_product_object', function ( $product ) {
	// WooCommerce verifies the product-save nonce before this hook runs.
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['_dmc_swatch'] ) ) {
		$hex = sanitize_hex_color( wp_unslash( $_POST['_dmc_swatch'] ) );
		$product->update_meta_data( '_dmc_swatch', $hex ? $hex : '' );
	}
	if ( isset( $_POST['_dmc_badge'] ) ) {
		$product->update_meta_data( '_dmc_badge', sanitize_text_field( wp_unslash( $_POST['_dmc_badge'] ) ) );
	}
	// phpcs:enable
} );
