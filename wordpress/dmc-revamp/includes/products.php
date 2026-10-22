<?php
/**
 * Product data helpers and the shared product card.
 *
 * Product data comes from standard WooCommerce fields:
 *   Attributes  "Grain", "Count", "Bag Size", "Variety Type" (comma-separated, e.g. "3D, Independent")
 *   Meta        _dmc_swatch (hex colour of the bag), _dmc_badge (e.g. "Bestseller")
 * Both meta fields are editable on the product screen (see includes/admin.php).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Splits a multi-value attribute string. WooCommerce joins custom (non-taxonomy)
 * attribute values with " | " and taxonomy terms with ", ", so accept both.
 *
 * @return string[]
 */
function dmc_split_attr( $value ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/[,|]/', (string) $value ) ) ) );
}

/**
 * Normalised display data for one product.
 *
 * @param WC_Product $product
 * @return array
 */
function dmc_product_info( $product ) {
	$count     = (string) $product->get_attribute( 'Count' );
	$count_max = 0;
	if ( preg_match_all( '/\d+/', $count, $m ) ) {
		$count_max = (int) end( $m[0] );
	}
	$band = $count_max >= 26 ? 'high' : ( $count_max >= 23 ? 'mid' : 'low' );

	$types = array();
	foreach ( dmc_split_attr( $product->get_attribute( 'Variety Type' ) ) as $t ) {
		$types[] = sanitize_title( $t );
	}

	$size     = (string) $product->get_attribute( 'Bag Size' );
	$size_key = preg_match( '/\d+/', $size, $sm ) ? $sm[0] : '';

	$gallery = $product->get_gallery_image_ids();
	$img_id  = $product->get_image_id();

	return array(
		'id'        => $product->get_id(),
		'name'      => $product->get_name(),
		'url'       => $product->get_permalink(),
		'img'       => $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_single' ) : wc_placeholder_img_src( 'woocommerce_single' ),
		'img_alt'   => $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : '',
		'hover'     => $gallery ? wp_get_attachment_image_url( $gallery[0], 'woocommerce_single' ) : '',
		'grain'     => (string) $product->get_attribute( 'Grain' ),
		'count'     => $count,
		'count_max' => $count_max,
		'band'      => $band,
		'size'      => $size,
		'size_key'  => $size_key,
		'type_label'=> implode( ' / ', dmc_split_attr( $product->get_attribute( 'Variety Type' ) ) ),
		'types'     => $types,
		'swatch'    => get_post_meta( $product->get_id(), '_dmc_swatch', true ) ?: '#c8894a',
		'badge'     => (string) get_post_meta( $product->get_id(), '_dmc_badge', true ),
		'has_price' => '' !== (string) $product->get_price(),
	);
}

/**
 * All published products, in menu order.
 *
 * @return WC_Product[]
 */
function dmc_catalog( $limit = -1, $exclude = array() ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	return wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => $limit,
			'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'exclude' => $exclude,
		)
	);
}

/**
 * Short spec line, e.g. "15 Kg · Classic".
 */
function dmc_card_kicker( $info ) {
	$bits = array_filter( array( $info['size'], $info['type_label'] ) );
	return implode( ' · ', $bits );
}

/**
 * Product card used on the shop archive, homepage grid and related products.
 *
 * @param WC_Product $product
 * @param array      $args  order (int) for client-side sort, quick (bool) to show the hover enquiry button.
 */
function dmc_render_card( $product, $args = array() ) {
	$args = wp_parse_args( $args, array( 'order' => 0, 'quick' => false, 'tilt' => false ) );
	$i    = dmc_product_info( $product );
	ob_start();
	?>
	<article class="dmc-card" data-order="<?php echo (int) $args['order']; ?>" data-count="<?php echo (int) $i['count_max']; ?>" data-type="<?php echo esc_attr( implode( ' ', $i['types'] ) ); ?>" data-size="<?php echo esc_attr( $i['size_key'] ); ?>" data-band="<?php echo esc_attr( $i['band'] ); ?>"<?php echo $args['tilt'] ? ' data-tilt' : ''; ?>>
		<a class="dmc-card__media" href="<?php echo esc_url( $i['url'] ); ?>">
			<img src="<?php echo esc_url( $i['img'] ); ?>" alt="<?php echo esc_attr( $i['img_alt'] ?: $i['name'] ); ?>" loading="lazy" />
			<?php if ( $i['hover'] ) : ?><img src="<?php echo esc_url( $i['hover'] ); ?>" alt="" loading="lazy" /><?php endif; ?>
			<span class="dmc-card__swatch" style="--c: <?php echo esc_attr( $i['swatch'] ); ?>"></span>
		</a>
		<?php if ( $i['badge'] ) : ?>
			<div class="dmc-card__tags"><span class="dmc-tag dmc-tag--brown"><?php echo esc_html( $i['badge'] ); ?></span></div>
		<?php endif; ?>
		<?php if ( $args['quick'] && ! $i['has_price'] ) : ?>
			<div class="dmc-card__quick"><a class="dmc-btn dmc-btn--sm dmc-btn--block" href="#" data-wa-product="<?php echo esc_attr( $i['name'] . ( $i['size'] ? ' (' . $i['size'] . ')' : '' ) ); ?>"><?php esc_html_e( 'Quick enquiry', 'dmc-revamp' ); ?></a></div>
		<?php endif; ?>
		<div class="dmc-card__body">
			<?php if ( dmc_card_kicker( $i ) ) : ?><span class="dmc-card__cat"><?php echo esc_html( dmc_card_kicker( $i ) ); ?></span><?php endif; ?>
			<h3 class="dmc-card__title"><a href="<?php echo esc_url( $i['url'] ); ?>"><?php echo esc_html( $i['name'] ); ?></a></h3>
			<?php if ( $i['grain'] || $i['count'] ) : ?>
				<div class="dmc-card__specs">
					<?php if ( $i['grain'] ) : ?><span><?php echo esc_html( 'Grain ' . $i['grain'] ); ?></span><?php endif; ?>
					<?php if ( $i['count'] ) : ?><span><?php echo esc_html( 'Count ' . $i['count'] ); ?></span><?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="dmc-card__foot">
				<?php if ( $i['has_price'] ) : ?>
					<span class="dmc-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					<?php
					// Standard WooCommerce loop button; it reads the global $product.
					$prev               = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
					$GLOBALS['product'] = $product;
					woocommerce_template_loop_add_to_cart(
						array(
							'class' => implode( ' ', array_filter( array( 'dmc-add', 'button', 'product_type_' . $product->get_type(), $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '', $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '' ) ) ),
						)
					);
					$GLOBALS['product'] = $prev;
					?>
				<?php else : ?>
					<span class="dmc-card__price"><?php esc_html_e( 'Price on request', 'dmc-revamp' ); ?></span>
					<a class="dmc-add" href="<?php echo esc_url( $i['url'] ); ?>"><?php esc_html_e( 'View', 'dmc-revamp' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Grade comparison rows (homepage + product page).
 *
 * @param WC_Product[] $products
 * @param int          $current_id Product to highlight.
 */
function dmc_render_grade_rows( $products, $current_id = 0 ) {
	$max = 0;
	$rows = array();
	foreach ( $products as $p ) {
		$i = dmc_product_info( $p );
		if ( ! $i['count'] ) {
			continue;
		}
		$max    = max( $max, $i['count_max'] );
		$rows[] = $i;
	}
	usort( $rows, function ( $a, $b ) {
		return $b['count_max'] - $a['count_max'];
	} );
	ob_start();
	foreach ( $rows as $i ) {
		$width = $max ? round( $i['count_max'] / $max * 100 ) : 0;
		$cur   = (int) $i['id'] === (int) $current_id;
		?>
		<a class="dmc-grade<?php echo $cur ? ' is-current' : ''; ?>" href="<?php echo esc_url( $i['url'] ); ?>">
			<span class="dmc-grade__dot" style="--c: <?php echo esc_attr( $i['swatch'] ); ?>"></span>
			<span class="dmc-grade__name"><?php echo esc_html( $i['name'] ); ?><small><?php echo esc_html( $cur ? __( "You're viewing this", 'dmc-revamp' ) : $i['size'] ); ?></small></span>
			<span class="dmc-grade__bar"><span data-bar="<?php echo (int) $width; ?>"></span></span>
			<span class="dmc-grade__val"><?php echo esc_html( $i['grain'] ); ?></span>
			<span class="dmc-grade__val"><?php echo esc_html( $i['count'] ); ?></span>
		</a>
		<?php
	}
	return ob_get_clean();
}
