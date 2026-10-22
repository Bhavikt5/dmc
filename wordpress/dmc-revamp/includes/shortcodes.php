<?php
/**
 * Shortcodes for the product-driven homepage sections.
 * Use them in Elementor's (free) "Shortcode" widget so the homepage always reflects the live catalogue.
 *
 *   [dmc_grade_circles]                      "Shop by grade" circle row
 *   [dmc_product_grid limit="8" chips="yes"] Bestsellers grid with type filter chips
 *   [dmc_grade_chart]                        Grade guide with grain / count bars
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'dmc_grade_circles', function ( $atts ) {
	$atts     = shortcode_atts( array( 'heading' => 'yes' ), $atts );
	$products = dmc_catalog();
	if ( ! $products ) {
		return '';
	}
	ob_start();
	?>
	<section class="dmc-section dmc-section--tight">
		<div class="dmc-wrap">
			<?php if ( 'yes' === $atts['heading'] ) : ?>
				<div class="dmc-head">
					<div>
						<div class="dmc-eyebrow"><?php esc_html_e( 'Choose your preferred bag', 'dmc-revamp' ); ?></div>
						<h2 class="dmc-h2" data-split><?php esc_html_e( 'Shop by', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'grade', 'dmc-revamp' ); ?></em></h2>
					</div>
					<a class="dmc-link" href="<?php echo esc_url( dmc_shop_url() ); ?>"><?php esc_html_e( 'View all varieties', 'dmc-revamp' ); ?> <?php echo dmc_icon( 'arrow' ); // phpcs:ignore ?></a>
				</div>
			<?php endif; ?>
			<?php echo dmc_grade_circles_html( $products ); // phpcs:ignore ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );

/**
 * The circle row itself (also used at the top of the shop archive).
 */
function dmc_grade_circles_html( $products, $current_id = 0 ) {
	ob_start();
	echo '<div class="dmc-cats" data-stagger>';
	foreach ( $products as $p ) {
		$i     = dmc_product_info( $p );
		$short = trim( preg_replace( '/\s*(almonds?)\s*$/i', '', $i['name'] ) );
		?>
		<a class="dmc-cat<?php echo (int) $current_id === (int) $i['id'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $i['url'] ); ?>">
			<span class="dmc-cat__img"><img src="<?php echo esc_url( $i['img'] ); ?>" alt="" loading="lazy" /></span>
			<span class="dmc-cat__name"><?php echo esc_html( $short ); ?><span class="dmc-cat__count"><?php echo esc_html( $i['count'] ? 'Count ' . $i['count'] : $i['size'] ); ?></span></span>
		</a>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
}

add_shortcode( 'dmc_product_grid', function ( $atts ) {
	$atts     = shortcode_atts( array( 'limit' => 8, 'chips' => 'yes', 'heading' => 'yes' ), $atts );
	$products = dmc_catalog( (int) $atts['limit'] );
	if ( ! $products ) {
		return '';
	}
	// Chips only for the variety types that actually exist.
	$types = array();
	foreach ( $products as $p ) {
		foreach ( dmc_split_attr( $p->get_attribute( 'Variety Type' ) ) as $t ) {
			$types[ sanitize_title( $t ) ] = $t;
		}
	}
	ob_start();
	?>
	<section class="dmc-section" data-chip-scope>
		<div class="dmc-wrap">
			<?php if ( 'yes' === $atts['heading'] ) : ?>
				<div class="dmc-head">
					<div>
						<div class="dmc-eyebrow"><?php esc_html_e( 'Our bestsellers', 'dmc-revamp' ); ?></div>
						<h2 class="dmc-h2" data-split><?php esc_html_e( 'Wholesale bags,', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'graded', 'dmc-revamp' ); ?></em> <?php esc_html_e( 'for every need', 'dmc-revamp' ); ?></h2>
					</div>
					<?php if ( 'yes' === $atts['chips'] && count( $types ) > 1 ) : ?>
						<div class="dmc-chips" role="tablist">
							<button class="dmc-chip is-active" type="button" data-chip="all"><?php esc_html_e( 'All grades', 'dmc-revamp' ); ?></button>
							<?php foreach ( $types as $key => $label ) : ?>
								<button class="dmc-chip" type="button" data-chip="type:<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="dmc-grid" data-stagger>
				<?php
				foreach ( $products as $n => $p ) {
					echo dmc_render_card( $p, array( 'order' => $n + 1, 'tilt' => true ) ); // phpcs:ignore
				}
				?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );

add_shortcode( 'dmc_grade_chart', function ( $atts ) {
	$atts     = shortcode_atts( array( 'current' => 0 ), $atts );
	$products = dmc_catalog();
	if ( ! $products ) {
		return '';
	}
	ob_start();
	?>
	<section class="dmc-section dmc-bg-cream2">
		<div class="dmc-wrap">
			<div class="dmc-head dmc-head--center">
				<div class="dmc-eyebrow dmc-eyebrow--center"><?php esc_html_e( 'Grade guide', 'dmc-revamp' ); ?></div>
				<h2 class="dmc-h2" data-split><?php esc_html_e( 'Find the right bag,', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'at a glance', 'dmc-revamp' ); ?></em></h2>
				<p class="dmc-lead"><?php esc_html_e( 'Every DMC bag is sorted and graded so your stock stays consistent, delivery after delivery. Compare grain and count across all our varieties.', 'dmc-revamp' ); ?></p>
			</div>
			<div class="dmc-grades" data-reveal>
				<div class="dmc-grades__head"><span></span><span><?php esc_html_e( 'Variety', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Count scale', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Grain', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Count', 'dmc-revamp' ); ?></span></div>
				<?php echo dmc_render_grade_rows( $products, (int) $atts['current'] ); // phpcs:ignore ?>
				<p class="dmc-grades__note"><?php esc_html_e( 'Grain and count as listed per bag. Need a full spec sheet for a grade?', 'dmc-revamp' ); ?> <a class="dmc-link" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Ask our team', 'dmc-revamp' ); ?></a></p>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );
