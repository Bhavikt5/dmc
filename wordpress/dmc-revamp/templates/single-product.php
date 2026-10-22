<?php
/**
 * Single product template (structure modelled on the reference store:
 * sticky gallery | info column with size pills, buy row and accordions,
 * then benefits, story, grade comparison, reviews and related products).
 *
 * Products without a price are enquiry-only (quote + WhatsApp).
 * Products with a price get WooCommerce's normal add-to-cart form.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	if ( ! $product ) {
		continue;
	}
	$GLOBALS['product'] = $product;
	$dmc        = dmc_product_info( $product );
	$dmc_label  = $dmc['name'] . ( $dmc['size'] ? ' (' . $dmc['size'] . ')' : '' );
	$dmc_images = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
	$dmc_rating = (float) $product->get_average_rating();
	$dmc_revs   = (int) $product->get_review_count();
	$dmc_cats   = wc_get_product_terms( $product->get_id(), 'product_cat' );
	$dmc_cat    = $dmc_cats ? $dmc_cats[0] : null;
	$dmc_short  = $product->get_short_description();

	do_action( 'woocommerce_before_single_product' );
	?>
	<main id="content" class="dmc-main">
		<section class="dmc-pdp" data-product>
			<div class="dmc-wrap">
				<nav class="dmc-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dmc-revamp' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'dmc-revamp' ); ?></a><span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( dmc_shop_url() ); ?>"><?php esc_html_e( 'Varieties', 'dmc-revamp' ); ?></a><span aria-hidden="true">/</span>
					<?php if ( $dmc_cat && 'uncategorized' !== $dmc_cat->slug ) : ?>
						<a href="<?php echo esc_url( get_term_link( $dmc_cat ) ); ?>"><?php echo esc_html( $dmc_cat->name ); ?></a><span aria-hidden="true">/</span>
					<?php endif; ?>
					<span><?php echo esc_html( $dmc['name'] ); ?></span>
				</nav>

				<div class="dmc-pdp__grid">
					<!-- gallery -->
					<div class="dmc-gallery<?php echo count( $dmc_images ) > 1 ? '' : ' dmc-gallery--single'; ?>" data-reveal="fade">
						<?php if ( count( $dmc_images ) > 1 ) : ?>
							<div class="dmc-gallery__thumbs">
								<?php foreach ( $dmc_images as $n => $img_id ) : ?>
									<button class="dmc-gallery__thumb<?php echo 0 === $n ? ' is-active' : ''; ?>" type="button" data-full="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_single' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'dmc-revamp' ), $n + 1 ) ); ?>">
										<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_gallery_thumbnail' ) ); ?>" alt=""<?php echo $n ? ' style="object-fit: cover"' : ''; ?> />
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<div class="dmc-gallery__main">
							<img src="<?php echo esc_url( $dmc['img'] ); ?>" alt="<?php echo esc_attr( $dmc['img_alt'] ?: $dmc['name'] ); ?>" />
							<?php if ( $product->is_on_sale() && $product->get_regular_price() ) : ?>
								<span class="dmc-card__badge"><?php echo esc_html( round( ( 1 - (float) $product->get_price() / (float) $product->get_regular_price() ) * 100 ) . '% OFF' ); ?></span>
							<?php endif; ?>
						</div>
						<p class="dmc-gallery__note"><?php echo dmc_icon( 'zoom' ); // phpcs:ignore ?><?php esc_html_e( 'Hover to zoom', 'dmc-revamp' ); ?></p>
					</div>

					<!-- info -->
					<div class="dmc-info">
						<div class="dmc-eyebrow"><?php echo esc_html( 'DMC Nuts' . ( $dmc['size'] ? ' · ' . $dmc['size'] . ' ' . __( 'wholesale bag', 'dmc-revamp' ) : '' ) ); ?></div>
						<h1 data-split><?php echo esc_html( $dmc['name'] ); ?></h1>
						<div class="dmc-rating">
							<?php if ( $dmc_revs ) : ?>
								<?php echo dmc_stars( $dmc_rating ); // phpcs:ignore ?>
								<a href="#reviews"><?php echo esc_html( sprintf( _n( '%d review', '%d reviews', $dmc_revs, 'dmc-revamp' ), $dmc_revs ) ); ?></a>
							<?php elseif ( comments_open() ) : ?>
								<a href="#reviews"><?php esc_html_e( 'Be the first to review', 'dmc-revamp' ); ?></a>
							<?php endif; ?>
							<?php if ( $dmc['badge'] ) : ?><span class="dmc-tag dmc-tag--brown"><?php echo esc_html( $dmc['badge'] ); ?></span><?php endif; ?>
						</div>

						<?php if ( $dmc['has_price'] ) : ?>
							<div class="dmc-info__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
							<p class="dmc-taxnote"><?php esc_html_e( 'Inclusive of all taxes · Free shipping on bulk orders', 'dmc-revamp' ); ?></p>
						<?php else : ?>
							<div class="dmc-request">
								<span class="dmc-request__icon"><?php echo dmc_icon( 'tag' ); // phpcs:ignore ?></span>
								<div><strong><?php esc_html_e( 'Price on request', 'dmc-revamp' ); ?></strong><span><?php esc_html_e( "Wholesale rates move with the market — call or WhatsApp for today's rate.", 'dmc-revamp' ); ?></span></div>
							</div>
						<?php endif; ?>

						<?php if ( $dmc_short ) : ?>
							<div class="dmc-short"><?php echo wp_kses_post( wpautop( $dmc_short ) ); ?></div>
						<?php endif; ?>

						<?php if ( $dmc['grain'] || $dmc['count'] || $dmc['size'] ) : ?>
							<div class="dmc-specchips">
								<?php if ( $dmc['grain'] ) : ?><div class="dmc-specchip"><small><?php esc_html_e( 'Grain', 'dmc-revamp' ); ?></small><strong><?php echo esc_html( $dmc['grain'] ); ?></strong></div><?php endif; ?>
								<?php if ( $dmc['count'] ) : ?><div class="dmc-specchip"><small><?php esc_html_e( 'Count', 'dmc-revamp' ); ?></small><strong><?php echo esc_html( $dmc['count'] ); ?></strong></div><?php endif; ?>
								<?php if ( $dmc['size'] ) : ?><div class="dmc-specchip"><small><?php esc_html_e( 'Pack', 'dmc-revamp' ); ?></small><strong><?php echo esc_html( $dmc['size'] ); ?></strong></div><?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="dmc-perks">
							<div class="dmc-perk"><?php echo dmc_icon( 'globe' ); // phpcs:ignore ?><?php esc_html_e( 'Direct import', 'dmc-revamp' ); ?></div>
							<div class="dmc-perk"><?php echo dmc_icon( 'shield' ); // phpcs:ignore ?><?php esc_html_e( 'FSSAI licensed', 'dmc-revamp' ); ?></div>
							<div class="dmc-perk"><?php echo dmc_icon( 'lock' ); // phpcs:ignore ?><?php esc_html_e( 'Hygienic packing', 'dmc-revamp' ); ?></div>
							<div class="dmc-perk"><?php echo dmc_icon( 'truck' ); // phpcs:ignore ?><?php esc_html_e( 'Free bulk shipping', 'dmc-revamp' ); ?></div>
						</div>

						<?php
						$dmc_siblings = dmc_catalog();
						if ( count( $dmc_siblings ) > 1 ) :
							?>
							<div class="dmc-optlabel"><?php esc_html_e( 'Grade:', 'dmc-revamp' ); ?> <span><?php echo esc_html( $dmc['name'] . ( $dmc['count'] ? ' · count ' . $dmc['count'] : '' ) ); ?></span></div>
							<div class="dmc-swatches">
								<?php foreach ( $dmc_siblings as $s ) : ?>
									<?php $si = dmc_product_info( $s ); ?>
									<a class="dmc-swatch<?php echo (int) $si['id'] === (int) $dmc['id'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $si['url'] ); ?>" style="--c: <?php echo esc_attr( $si['swatch'] ); ?>" aria-label="<?php echo esc_attr( $si['name'] ); ?>"><span><?php echo esc_html( $si['name'] . ( $si['count'] ? ' · ' . $si['count'] : '' ) ); ?></span></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $dmc['has_price'] && $product->is_purchasable() ) : ?>
							<div class="dmc-wc-cart dmc-buybox--wc">
								<?php woocommerce_template_single_add_to_cart(); ?>
							</div>
							<a class="dmc-whatsapp" href="#" data-wa-product="<?php echo esc_attr( $dmc_label ); ?>"><?php echo dmc_icon( 'whatsapp' ); // phpcs:ignore ?><?php esc_html_e( 'Bulk order? Get a quote on WhatsApp', 'dmc-revamp' ); ?></a>
						<?php else : ?>
							<div class="dmc-optlabel"><?php esc_html_e( 'Quantity', 'dmc-revamp' ); ?> <span><?php echo esc_html( $dmc['size'] ? '(' . $dmc['size'] . ' ' . __( 'bags', 'dmc-revamp' ) . ')' : '' ); ?></span></div>
							<div class="dmc-buybox">
								<div class="dmc-qty"><button type="button" data-step="-1" aria-label="<?php esc_attr_e( 'Decrease', 'dmc-revamp' ); ?>">−</button><input type="number" value="1" min="1" aria-label="<?php esc_attr_e( 'Number of bags', 'dmc-revamp' ); ?>" /><button type="button" data-step="1" aria-label="<?php esc_attr_e( 'Increase', 'dmc-revamp' ); ?>">+</button></div>
								<a class="dmc-btn dmc-btn--almond" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Request Quote', 'dmc-revamp' ); ?></a>
								<a class="dmc-btn dmc-btn--green" href="#" data-wa-product="<?php echo esc_attr( $dmc_label ); ?>"><?php echo dmc_icon( 'whatsapp' ); // phpcs:ignore ?><?php esc_html_e( 'WhatsApp Order', 'dmc-revamp' ); ?></a>
							</div>
							<a class="dmc-whatsapp" href="tel:<?php echo esc_attr( DMC_PHONE ); ?>"><?php echo dmc_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( sprintf( __( "Call %s for today's rate", 'dmc-revamp' ), DMC_PHONE_DISPLAY ) ); ?></a>
						<?php endif; ?>

						<div class="dmc-acc">
							<?php if ( $product->get_description() ) : ?>
								<div class="dmc-acc__item is-open">
									<button class="dmc-acc__head" type="button" aria-expanded="true"><?php esc_html_e( 'Description', 'dmc-revamp' ); ?> <span class="dmc-acc__icon"></span></button>
									<div class="dmc-acc__body"><div class="dmc-acc__inner"><?php the_content(); ?></div></div>
								</div>
							<?php endif; ?>
							<?php
							$dmc_attrs = array_filter(
								$product->get_attributes(),
								function ( $a ) {
									return $a->get_visible();
								}
							);
							if ( $dmc_attrs ) :
								?>
								<div class="dmc-acc__item">
									<button class="dmc-acc__head" type="button" aria-expanded="false"><?php esc_html_e( 'Specifications', 'dmc-revamp' ); ?> <span class="dmc-acc__icon"></span></button>
									<div class="dmc-acc__body"><div class="dmc-acc__inner">
										<table class="dmc-nutri">
											<?php foreach ( $dmc_attrs as $attr ) : ?>
												<tr><td><?php echo esc_html( wc_attribute_label( $attr->get_name(), $product ) ); ?></td><td><?php echo esc_html( $product->get_attribute( $attr->get_name() ) ); ?></td></tr>
											<?php endforeach; ?>
											<?php if ( $product->get_sku() ) : ?>
												<tr><td><?php esc_html_e( 'SKU', 'dmc-revamp' ); ?></td><td><?php echo esc_html( $product->get_sku() ); ?></td></tr>
											<?php endif; ?>
										</table>
									</div></div>
								</div>
							<?php endif; ?>
							<div class="dmc-acc__item">
								<button class="dmc-acc__head" type="button" aria-expanded="false"><?php esc_html_e( 'Nutrition (typical)', 'dmc-revamp' ); ?> <span class="dmc-acc__icon"></span></button>
								<div class="dmc-acc__body"><div class="dmc-acc__inner">
									<table class="dmc-nutri">
										<caption><?php esc_html_e( 'Typical values per 100 g of raw almonds (USDA)', 'dmc-revamp' ); ?></caption>
										<tr><td><?php esc_html_e( 'Energy', 'dmc-revamp' ); ?></td><td>579 kcal</td></tr>
										<tr><td><?php esc_html_e( 'Protein', 'dmc-revamp' ); ?></td><td>21.2 g</td></tr>
										<tr><td><?php esc_html_e( 'Total fat', 'dmc-revamp' ); ?></td><td>49.9 g</td></tr>
										<tr><td><?php esc_html_e( 'Carbohydrate', 'dmc-revamp' ); ?></td><td>21.6 g</td></tr>
										<tr><td><?php esc_html_e( 'Dietary fibre', 'dmc-revamp' ); ?></td><td>12.5 g</td></tr>
										<tr><td><?php esc_html_e( 'Vitamin E', 'dmc-revamp' ); ?></td><td>25.6 mg</td></tr>
										<tr><td><?php esc_html_e( 'Magnesium', 'dmc-revamp' ); ?></td><td>270 mg</td></tr>
									</table>
								</div></div>
							</div>
							<div class="dmc-acc__item">
								<button class="dmc-acc__head" type="button" aria-expanded="false"><?php esc_html_e( 'Storage', 'dmc-revamp' ); ?> <span class="dmc-acc__icon"></span></button>
								<div class="dmc-acc__body"><div class="dmc-acc__inner"><ul>
									<li><?php esc_html_e( 'Store in a cool, dry place away from direct sunlight.', 'dmc-revamp' ); ?></li>
									<li><?php esc_html_e( 'Once opened, transfer to airtight containers to keep the crunch.', 'dmc-revamp' ); ?></li>
									<li><?php esc_html_e( 'Keep bags off the floor and away from strong odours.', 'dmc-revamp' ); ?></li>
								</ul></div></div>
							</div>
							<div class="dmc-acc__item">
								<button class="dmc-acc__head" type="button" aria-expanded="false"><?php esc_html_e( 'Shipping & returns', 'dmc-revamp' ); ?> <span class="dmc-acc__icon"></span></button>
								<div class="dmc-acc__body"><div class="dmc-acc__inner"><ul>
									<li><?php esc_html_e( 'Free shipping on bulk buy orders.', 'dmc-revamp' ); ?></li>
									<li><?php esc_html_e( 'Dispatched from APMC Market, Turbhe, Navi Mumbai.', 'dmc-revamp' ); ?></li>
									<li><?php esc_html_e( "Easy returns — talk to our team if anything isn't right.", 'dmc-revamp' ); ?></li>
								</ul></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<div class="dmc-ticker dmc-marquee" aria-label="<?php esc_attr_e( 'Why buy from us', 'dmc-revamp' ); ?>">
			<div class="dmc-marquee__track" data-speed="34">
				<span class="dmc-pill dmc-pill--1"><?php esc_html_e( 'Free Shipping · For Bulk Buy', 'dmc-revamp' ); ?></span>
				<span class="dmc-pill dmc-pill--2"><?php esc_html_e( 'FSSAI Licensed', 'dmc-revamp' ); ?></span>
				<span class="dmc-pill dmc-pill--3"><?php esc_html_e( 'Sorted & Graded', 'dmc-revamp' ); ?></span>
				<span class="dmc-pill dmc-pill--4"><?php esc_html_e( 'Direct Import', 'dmc-revamp' ); ?></span>
				<span class="dmc-pill dmc-pill--1"><?php esc_html_e( 'Hygienic, Hands-free Packing', 'dmc-revamp' ); ?></span>
				<span class="dmc-pill dmc-pill--2"><?php esc_html_e( 'Easy Returns', 'dmc-revamp' ); ?></span>
			</div>
		</div>

		<section class="dmc-section">
			<div class="dmc-wrap">
				<div class="dmc-head dmc-head--center">
					<div class="dmc-eyebrow dmc-eyebrow--center"><?php esc_html_e( 'Why almonds', 'dmc-revamp' ); ?></div>
					<h2 class="dmc-h2" data-split><?php esc_html_e( 'Goodness in', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'every bite', 'dmc-revamp' ); ?></em></h2>
					<p class="dmc-lead"><?php esc_html_e( 'Rich in protein, fiber, and essential nutrients, almonds make the perfect choice for a healthy lifestyle — and an easy sell for your customers.', 'dmc-revamp' ); ?></p>
				</div>
				<div class="dmc-benefits" data-stagger>
					<div class="dmc-benefit"><div class="dmc-benefit__icon"><?php echo dmc_icon( 'drop' ); // phpcs:ignore ?></div><h3><?php esc_html_e( 'Plant protein', 'dmc-revamp' ); ?></h3><p><?php esc_html_e( 'Around 21 g of protein per 100 g for lasting energy.', 'dmc-revamp' ); ?></p></div>
					<div class="dmc-benefit"><div class="dmc-benefit__icon"><?php echo dmc_icon( 'heart' ); // phpcs:ignore ?></div><h3><?php esc_html_e( 'Good fats', 'dmc-revamp' ); ?></h3><p><?php esc_html_e( 'Mostly mono-unsaturated fats, naturally present.', 'dmc-revamp' ); ?></p></div>
					<div class="dmc-benefit"><div class="dmc-benefit__icon"><?php echo dmc_icon( 'leaf' ); // phpcs:ignore ?></div><h3><?php esc_html_e( 'Dietary fibre', 'dmc-revamp' ); ?></h3><p><?php esc_html_e( 'About 12 g of fibre per 100 g supports digestion.', 'dmc-revamp' ); ?></p></div>
					<div class="dmc-benefit"><div class="dmc-benefit__icon"><?php echo dmc_icon( 'sun' ); // phpcs:ignore ?></div><h3><?php esc_html_e( 'Vitamin E', 'dmc-revamp' ); ?></h3><p><?php esc_html_e( 'One of the richest natural sources of vitamin E.', 'dmc-revamp' ); ?></p></div>
				</div>
			</div>
		</section>

		<?php $dmc_story_img = isset( $dmc_images[1] ) ? wp_get_attachment_image_url( $dmc_images[1], 'large' ) : DMC_REVAMP_URL . 'assets/img/almond-bowl.jpg'; ?>
		<section class="dmc-section" style="padding-top: 0">
			<div class="dmc-wrap">
				<div class="dmc-pstory" data-reveal="scale">
					<div class="dmc-pstory__media"><img src="<?php echo esc_url( $dmc_story_img ); ?>" alt="" data-parallax="0.08" loading="lazy" /></div>
					<div class="dmc-pstory__text">
						<div class="dmc-eyebrow"><?php esc_html_e( 'Farm fresh almond', 'dmc-revamp' ); ?></div>
						<h2 class="dmc-h2" data-split><?php esc_html_e( 'Handpicked, sun-dried, packed fresh', 'dmc-revamp' ); ?></h2>
						<p><?php esc_html_e( 'Sourced from the finest farms and carefully handpicked, each almond is naturally sun-dried to preserve its authentic taste and crunch, then packed fresh to seal in flavor for longer shelf life.', 'dmc-revamp' ); ?></p>
						<dl class="dmc-specs">
							<?php if ( $dmc['grain'] ) : ?><div><dt><?php esc_html_e( 'Grain', 'dmc-revamp' ); ?></dt><dd><?php echo esc_html( $dmc['grain'] ); ?></dd></div><?php endif; ?>
							<?php if ( $dmc['count'] ) : ?><div><dt><?php esc_html_e( 'Count', 'dmc-revamp' ); ?></dt><dd><?php echo esc_html( $dmc['count'] ); ?></dd></div><?php endif; ?>
							<?php if ( $dmc['size'] ) : ?><div><dt><?php esc_html_e( 'Bag', 'dmc-revamp' ); ?></dt><dd><?php echo esc_html( $dmc['size'] ); ?></dd></div><?php endif; ?>
							<div><dt><?php esc_html_e( 'Packed by', 'dmc-revamp' ); ?></dt><dd>Deepak Mange &amp; Co.</dd></div>
						</dl>
					</div>
				</div>
			</div>
		</section>

		<?php if ( $dmc['count'] && count( $dmc_siblings ) > 1 ) : ?>
			<section class="dmc-section dmc-bg-cream2">
				<div class="dmc-wrap">
					<div class="dmc-head">
						<div>
							<div class="dmc-eyebrow"><?php esc_html_e( 'Compare grades', 'dmc-revamp' ); ?></div>
							<h2 class="dmc-h2" data-split><?php esc_html_e( 'How it', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'stacks up', 'dmc-revamp' ); ?></em></h2>
						</div>
						<a class="dmc-link" href="<?php echo esc_url( dmc_shop_url() ); ?>"><?php esc_html_e( 'See all varieties', 'dmc-revamp' ); ?> <?php echo dmc_icon( 'arrow' ); // phpcs:ignore ?></a>
					</div>
					<div class="dmc-grades" data-reveal>
						<div class="dmc-grades__head"><span></span><span><?php esc_html_e( 'Variety', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Count scale', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Grain', 'dmc-revamp' ); ?></span><span><?php esc_html_e( 'Count', 'dmc-revamp' ); ?></span></div>
						<?php echo dmc_render_grade_rows( $dmc_siblings, $dmc['id'] ); // phpcs:ignore ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( comments_open() || $dmc_revs ) : ?>
			<section class="dmc-section" id="reviews">
				<div class="dmc-wrap dmc-reviews">
					<div class="dmc-reviews__summary" data-reveal>
						<div class="dmc-eyebrow"><?php esc_html_e( 'Buyer reviews', 'dmc-revamp' ); ?></div>
						<?php if ( $dmc_revs ) : ?>
							<div class="dmc-reviews__score"><?php echo esc_html( number_format( $dmc_rating, 1 ) ); ?></div>
							<?php echo dmc_stars( $dmc_rating ); // phpcs:ignore ?>
							<p style="font-size: 14px; color: var(--dmc-muted); margin-top: 6px"><?php echo esc_html( sprintf( _n( 'Based on %d review', 'Based on %d reviews', $dmc_revs, 'dmc-revamp' ), $dmc_revs ) ); ?></p>
							<div class="dmc-bars">
								<?php
								$dmc_rc = $product->get_rating_counts();
								for ( $r = 5; $r >= 1; $r-- ) :
									$pct = $dmc_revs ? round( ( isset( $dmc_rc[ $r ] ) ? $dmc_rc[ $r ] : 0 ) / $dmc_revs * 100 ) : 0;
									?>
									<div><span><?php echo (int) $r; ?>★</span><i><b data-bar="<?php echo (int) $pct; ?>" style="width: 0; transition: width 1.2s"></b></i><span><?php echo (int) $pct; ?>%</span></div>
								<?php endfor; ?>
							</div>
						<?php else : ?>
							<p style="color: var(--dmc-muted); margin: 10px 0 0"><?php esc_html_e( 'No reviews yet. Bought this grade from us? Share your experience to help other buyers.', 'dmc-revamp' ); ?></p>
						<?php endif; ?>
					</div>
					<div class="dmc-woo-reviews"><?php comments_template(); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<?php
		$dmc_related = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), 4 ) ) );
		if ( $dmc_related ) :
			?>
			<section class="dmc-section dmc-bg-white">
				<div class="dmc-wrap">
					<div class="dmc-head">
						<div>
							<div class="dmc-eyebrow"><?php esc_html_e( 'You may also like', 'dmc-revamp' ); ?></div>
							<h2 class="dmc-h2" data-split><?php esc_html_e( 'More', 'dmc-revamp' ); ?> <em class="dmc-accent"><?php esc_html_e( 'bestsellers', 'dmc-revamp' ); ?></em></h2>
						</div>
						<a class="dmc-link" href="<?php echo esc_url( dmc_shop_url() ); ?>"><?php esc_html_e( 'View all', 'dmc-revamp' ); ?> <?php echo dmc_icon( 'arrow' ); // phpcs:ignore ?></a>
					</div>
					<div class="dmc-grid" data-stagger>
						<?php
						foreach ( $dmc_related as $rp ) {
							echo dmc_render_card( $rp ); // phpcs:ignore
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<div class="dmc-sticky-atc" data-product>
			<img src="<?php echo esc_url( $dmc['img'] ); ?>" alt="" />
			<div class="dmc-sticky-atc__name"><?php echo esc_html( $dmc_label ); ?><small><?php echo $dmc['has_price'] ? wp_kses_post( wp_strip_all_tags( $product->get_price_html() ) ) : esc_html( trim( ( $dmc['grain'] ? 'Grain ' . $dmc['grain'] . ' · ' : '' ) . ( $dmc['count'] ? 'Count ' . $dmc['count'] . ' · ' : '' ) . __( 'Price on request', 'dmc-revamp' ) ) ); ?></small></div>
			<a class="dmc-btn dmc-btn--green dmc-btn--sm" href="#" data-wa-product="<?php echo esc_attr( $dmc_label ); ?>"><?php esc_html_e( 'WhatsApp Order', 'dmc-revamp' ); ?></a>
			<a class="dmc-btn dmc-btn--almond dmc-btn--sm" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Request Quote', 'dmc-revamp' ); ?></a>
		</div>
	</main>
	<?php
	// Product schema (WooCommerce normally prints this from the default summary hooks we don't use).
	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}
	do_action( 'woocommerce_after_single_product' );
endwhile;

get_footer();
