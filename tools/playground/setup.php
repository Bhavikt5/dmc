<?php
/**
 * Test-site setup for WordPress Playground: products from the CSV, Home + About pages from the
 * generated Elementor JSON. Run by tools/playground/blueprint.json. Never run this on a live site.
 */
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );
register_shutdown_function( function () {
	$e = error_get_last();
	if ( $e && in_array( $e['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ), true ) ) {
		echo "LAST ERROR: {$e['message']} in {$e['file']}:{$e['line']}\n";
	}
} );
set_exception_handler( function ( $ex ) {
	echo 'EXCEPTION: ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . "\n" . $ex->getTraceAsString();
} );
ini_set( 'display_errors', '1' );
error_reporting( E_ALL );
require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
// Elementor refuses page-settings writes without a user who can edit the page.
wp_set_current_user( 1 );
update_option( 'blogname', 'DMC Nuts' );
update_option( 'woocommerce_currency', 'INR' );
update_option( 'elementor_onboarded', true );

$base = '/wordpress/wp-content/dmc-test/';

// --- products ---------------------------------------------------------------
$cat = term_exists( 'Almonds', 'product_cat' );
if ( ! $cat ) {
	$cat = wp_insert_term( 'Almonds', 'product_cat' );
}
$cat_id = (int) ( is_array( $cat ) ? $cat['term_id'] : $cat );

// fgetcsv, not file()+str_getcsv: descriptions contain line breaks inside quoted fields.
$fh   = fopen( $base . 'dmc-products-import.csv', 'r' );
$head = fgetcsv( $fh );
$imgs = array( 'orange-bag', 'white-bag', 'yellow-bag', 'pink-bag', 'purple-bag', 'blue-bag', 'golden-bag', 'independent-3d-bag', 'independent-bag' );
$n    = 0;
while ( ( $r = fgetcsv( $fh ) ) !== false ) {
	$row = array_combine( $head, $r );
	$p   = new WC_Product_Simple();
	$p->set_name( $row['Name'] );
	$p->set_sku( $row['SKU'] );
	$p->set_status( 'publish' );
	$p->set_featured( '1' === $row['Is featured?'] );
	$p->set_short_description( $row['Short description'] );
	$p->set_description( $row['Description'] );
	$p->set_menu_order( (int) $row['Position'] );
	$p->set_category_ids( array( $cat_id ) );
	$p->set_reviews_allowed( true );
	$attrs = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$a = new WC_Product_Attribute();
		$a->set_name( $row[ "Attribute $i name" ] );
		$a->set_options( array_map( 'trim', explode( ',', $row[ "Attribute $i value(s)" ] ) ) );
		$a->set_visible( true );
		$a->set_variation( false );
		$attrs[] = $a;
	}
	$p->set_attributes( $attrs );
	$p->update_meta_data( '_dmc_swatch', $row['Meta: _dmc_swatch'] );
	$p->update_meta_data( '_dmc_badge', $row['Meta: _dmc_badge'] );

	// Local copies of the bag photos instead of downloading from dmcnuts.com.
	$file = $base . 'img/' . $imgs[ $n ] . '.jpg';
	$up   = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
	if ( empty( $up['error'] ) ) {
		$att = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => $row['Name'], 'post_status' => 'inherit' ), $up['file'] );
		wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $up['file'] ) );
		$p->set_image_id( $att );
	}
	$p->save();
	$n++;
}
fclose( $fh );

// One review so the reviews block has real data.
$orange = wc_get_product_id_by_sku( 'DMC-ORANGE-15' );
$cid    = wp_insert_comment( array( 'comment_post_ID' => $orange, 'comment_author' => 'Ramesh Patel', 'comment_content' => 'We have been sourcing almonds from this company for over a year now, and the Orange Bag almonds are our best-sellers.', 'comment_type' => 'review', 'comment_approved' => 1 ) );
add_comment_meta( $cid, 'rating', 5 );
WC_Comments::clear_transients( $orange );

// --- Elementor pages ----------------------------------------------------------
function dmc_test_page( $title, $slug, $json ) {
	$tpl = json_decode( file_get_contents( $json ), true );
	$id  = wp_insert_post( array( 'post_title' => $title, 'post_name' => $slug, 'post_type' => 'page', 'post_status' => 'publish' ) );
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
	update_post_meta( $id, '_wp_page_template', 'elementor_full_width' );
	update_post_meta( $id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $tpl['content'] ) ) );
	return $id;
}
$home = dmc_test_page( 'Home', 'home', $base . 'dmc-home.json' );
dmc_test_page( 'About Us', 'about-us', $base . 'dmc-about.json' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules();

echo "Created $n products, home page $home\n";
