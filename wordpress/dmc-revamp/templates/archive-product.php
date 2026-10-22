<?php
/**
 * Shop archive / product category template.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Products in the current query, plus counts for the filter sidebar.
$dmc_products = array();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$p = wc_get_product( get_the_ID() );
		if ( $p && $p->is_visible() ) {
			$dmc_products[] = $p;
		}
	}
	rewind_posts();
}
$dmc_counts = array( 'type' => array(), 'size' => array(), 'band' => array( 'high' => 0, 'mid' => 0, 'low' => 0 ) );
$dmc_labels = array( 'type' => array(), 'size' => array() );
foreach ( $dmc_products as $p ) {
	$i = dmc_product_info( $p );
	foreach ( dmc_split_attr( $p->get_attribute( 'Variety Type' ) ) as $t ) {
		$k                          = sanitize_title( $t );
		$dmc_counts['type'][ $k ]   = isset( $dmc_counts['type'][ $k ] ) ? $dmc_counts['type'][ $k ] + 1 : 1;
		$dmc_labels['type'][ $k ]   = $t;
	}
	if ( $i['size_key'] ) {
		$dmc_counts['size'][ $i['size_key'] ] = isset( $dmc_counts['size'][ $i['size_key'] ] ) ? $dmc_counts['size'][ $i['size_key'] ] + 1 : 1;
		$dmc_labels['size'][ $i['size_key'] ] = $i['size'];
	}
	if ( $i['count_max'] ) {
		$dmc_counts['band'][ $i['band'] ]++;
	}
}
$dmc_band_labels = array( 'high' => '26 – 30', 'mid' => '23 – 25', 'low' => '19 – 22' );

$dmc_is_shop = is_shop();
$dmc_title   = $dmc_is_shop ? '' : woocommerce_page_title( false );
$dmc_desc    = $dmc_is_shop ? __( 'Nine sorted and graded almond varieties in 15 Kg and 30 Kg bags, supplied in bulk to retailers, distributors and importers.', 'dmc-revamp' ) : wp_strip_all_tags( term_description() );
?>
<main id="content" class="dmc-main">
	<section class="dmc-shop-hero">
		<div class="dmc-wrap">
			<nav class="dmc-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dmc-revamp' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'dmc-revamp' ); ?></a><span aria-hidden="true">/</span>
				<?php if ( $dmc_is_shop ) : ?>
					<span><?php esc_html_e( 'Varieties', 'dmc-revamp' ); ?></span>
				<?php else : ?>
					<a href="<?php echo esc_url( dmc_shop_url() ); ?>"><?php esc_html_e( 'Varieties', 'dmc-revamp' ); ?></a><span aria-hidden="true">/</span><span><?php echo esc_html( $dmc_title ); ?></span>
				<?php endif; ?>
			</nav>
			<div class="dmc-shop-hero__row">
				<div>
					<?php if ( $dmc_is_shop ) : ?>
						<h1 class="dmc-h2" data-split><?php esc_html_e( 'Choose your', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'preferred', 'dmc-revamp' ); ?></em> <?php esc_html_e( 'bag', 'dmc-revamp' ); ?></h1>
					<?php else : ?>
						<h1 class="dmc-h2" data-split><?php echo esc_html( $dmc_title ); ?></h1>
					<?php endif; ?>
					<?php if ( $dmc_desc ) : ?>
						<p class="dmc-lead" style="margin-top: 14px" data-reveal><?php echo esc_html( $dmc_desc ); ?></p>
					<?php endif; ?>
				</div>
				<a class="dmc-btn dmc-btn--almond" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>" data-magnetic><?php esc_html_e( 'Get Bulk Quote', 'dmc-revamp' ); ?> <?php echo dmc_icon( 'arrow', 'dmc-arrow' ); // phpcs:ignore ?></a>
			</div>
			<?php echo dmc_grade_circles_html( dmc_catalog() ); // phpcs:ignore ?>
		</div>
	</section>

	<div class="dmc-wrap">
		<?php woocommerce_output_all_notices(); ?>

		<div class="dmc-deal" data-reveal>
			<div><strong><?php esc_html_e( 'Deal of the day — discount on bulk orders', 'dmc-revamp' ); ?></strong><br /><?php esc_html_e( "Free shipping on every bulk buy. Ask for today's rate on WhatsApp.", 'dmc-revamp' ); ?></div>
			<a class="dmc-btn dmc-btn--light dmc-btn--sm" href="<?php echo esc_url( dmc_whatsapp_url( "Hello DMC Nuts, I'd like today's wholesale rates." ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'dmc-revamp' ); ?></a>
		</div>

		<div class="dmc-shop">
			<aside class="dmc-filters" aria-label="<?php esc_attr_e( 'Filters', 'dmc-revamp' ); ?>">
				<div class="dmc-filters__head"><?php esc_html_e( 'Filters', 'dmc-revamp' ); ?> <button type="button" data-filter-close aria-label="<?php esc_attr_e( 'Close filters', 'dmc-revamp' ); ?>">×</button></div>
				<?php if ( count( $dmc_counts['type'] ) > 1 ) : ?>
					<details class="dmc-fgroup" open>
						<summary><?php esc_html_e( 'Variety type', 'dmc-revamp' ); ?></summary>
						<div class="dmc-fgroup__body">
							<?php foreach ( $dmc_counts['type'] as $k => $n ) : ?>
								<label class="dmc-check"><input type="checkbox" data-filter="type" value="<?php echo esc_attr( $k ); ?>" /> <?php echo esc_html( $dmc_labels['type'][ $k ] ); ?> <em><?php echo (int) $n; ?></em></label>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endif; ?>
				<?php if ( count( $dmc_counts['size'] ) > 1 ) : ?>
					<details class="dmc-fgroup" open>
						<summary><?php esc_html_e( 'Bag size', 'dmc-revamp' ); ?></summary>
						<div class="dmc-fgroup__body">
							<?php foreach ( $dmc_counts['size'] as $k => $n ) : ?>
								<label class="dmc-check"><input type="checkbox" data-filter="size" value="<?php echo esc_attr( $k ); ?>" /> <?php echo esc_html( $dmc_labels['size'][ $k ] ); ?> <em><?php echo (int) $n; ?></em></label>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endif; ?>
				<?php if ( array_sum( $dmc_counts['band'] ) ) : ?>
					<details class="dmc-fgroup" open>
						<summary><?php esc_html_e( 'Count', 'dmc-revamp' ); ?></summary>
						<div class="dmc-fgroup__body">
							<?php foreach ( $dmc_counts['band'] as $k => $n ) : ?>
								<?php if ( $n ) : ?>
									<label class="dmc-check"><input type="checkbox" data-filter="band" value="<?php echo esc_attr( $k ); ?>" /> <?php echo esc_html( $dmc_band_labels[ $k ] ); ?> <em><?php echo (int) $n; ?></em></label>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endif; ?>
				<button class="dmc-link" type="button" data-clear-filters style="border: 0; background: none; padding: 6px 0 18px; cursor: pointer"><?php esc_html_e( 'Clear all filters', 'dmc-revamp' ); ?></button>
				<div class="dmc-filters__help">
					<strong><?php esc_html_e( 'Not sure which grade?', 'dmc-revamp' ); ?></strong>
					<?php esc_html_e( "Tell us how you sell — retail, gifting or processing — and we'll suggest the right bag.", 'dmc-revamp' ); ?>
					<a class="dmc-btn dmc-btn--green dmc-btn--sm dmc-btn--block" style="margin-top: 14px" href="tel:<?php echo esc_attr( DMC_PHONE ); ?>"><?php echo esc_html( sprintf( __( 'Call %s', 'dmc-revamp' ), DMC_PHONE_DISPLAY ) ); ?></a>
				</div>
			</aside>

			<div>
				<div class="dmc-toolbar">
					<p class="dmc-toolbar__count"><?php esc_html_e( 'Showing', 'dmc-revamp' ); ?> <strong data-result-count><?php echo count( $dmc_products ); ?></strong> <?php esc_html_e( 'varieties', 'dmc-revamp' ); ?></p>
					<div class="dmc-toolbar__right">
						<button class="dmc-btn dmc-btn--ghost dmc-btn--sm dmc-filter-btn" type="button" data-filter-toggle><?php echo dmc_icon( 'filter' ); // phpcs:ignore ?><?php esc_html_e( 'Filters', 'dmc-revamp' ); ?></button>
						<label class="dmc-sr" for="dmc-sort"><?php esc_html_e( 'Sort by', 'dmc-revamp' ); ?></label>
						<select class="dmc-select" id="dmc-sort" data-sort>
							<option value="default"><?php esc_html_e( 'Sort: Featured', 'dmc-revamp' ); ?></option>
							<option value="count-desc"><?php esc_html_e( 'Count: high to low', 'dmc-revamp' ); ?></option>
							<option value="count-asc"><?php esc_html_e( 'Count: low to high', 'dmc-revamp' ); ?></option>
						</select>
						<div class="dmc-view" role="group" aria-label="<?php esc_attr_e( 'Grid columns', 'dmc-revamp' ); ?>">
							<button type="button" data-cols="2" aria-label="<?php esc_attr_e( '2 columns', 'dmc-revamp' ); ?>"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="2" /><rect x="13" y="3" width="8" height="8" rx="2" /><rect x="3" y="13" width="8" height="8" rx="2" /><rect x="13" y="13" width="8" height="8" rx="2" /></svg></button>
							<button type="button" class="is-active" data-cols="3" aria-label="<?php esc_attr_e( '3 columns', 'dmc-revamp' ); ?>"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="3" width="5.5" height="8" rx="1.5" /><rect x="9.25" y="3" width="5.5" height="8" rx="1.5" /><rect x="16.5" y="3" width="5.5" height="8" rx="1.5" /><rect x="2" y="13" width="5.5" height="8" rx="1.5" /><rect x="9.25" y="13" width="5.5" height="8" rx="1.5" /><rect x="16.5" y="13" width="5.5" height="8" rx="1.5" /></svg></button>
						</div>
					</div>
				</div>

				<?php if ( $dmc_products ) : ?>
					<div class="dmc-grid" style="--cols: 3" data-filterable data-stagger>
						<?php
						foreach ( $dmc_products as $n => $p ) {
							echo dmc_render_card( $p, array( 'order' => $n + 1, 'quick' => true ) ); // phpcs:ignore
						}
						?>
					</div>
					<div class="dmc-pagination-wrap"><?php woocommerce_pagination(); ?></div>
				<?php else : ?>
					<?php do_action( 'woocommerce_no_products_found' ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( $dmc_is_shop ) : ?>
		<section class="dmc-section dmc-bg-white" style="border-top: 1px solid var(--dmc-line)">
			<div class="dmc-wrap dmc-seo" data-reveal>
				<h2><?php esc_html_e( 'Wholesale almonds, graded for every business', 'dmc-revamp' ); ?></h2>
				<p><?php esc_html_e( 'At DMC, we bring you premium quality almonds sourced directly from trusted farms. Known for their rich taste, superior crunch, and natural freshness, our almonds are supplied in bulk to businesses across India and global markets.', 'dmc-revamp' ); ?></p>
				<p><?php esc_html_e( 'Whether it’s snacking, gifting, or industrial use, our colorful bag varieties ensure consistency, trust, and excellence with every delivery. Every bag lists its grain and count so you can stock exactly the size your customers expect.', 'dmc-revamp' ); ?></p>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer();
