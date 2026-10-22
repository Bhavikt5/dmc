<?php
/**
 * Site footer (printed early on wp_footer, before scripts).
 */

defined( 'ABSPATH' ) || exit;

$dmc_logo = DMC_REVAMP_URL . 'assets/img/logo.webp';
$dmc_shop = dmc_shop_url();
?>
<footer class="dmc-footer" id="contact">
	<div class="dmc-wrap">
		<div class="dmc-footer__top">
			<div class="dmc-footer__brand">
				<a class="dmc-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $dmc_logo ); ?>" alt="DMC Nuts" width="62" height="62" /><span class="dmc-logo__text">DMC Nuts<small>Deepak Mange &amp; Co.</small></span></a>
				<p><?php esc_html_e( "Premium almond wholesaler & exporter from India — supplying retailers, distributors and global importers from Navi Mumbai's APMC Market.", 'dmc-revamp' ); ?></p>
				<div class="dmc-social">
					<a href="<?php echo esc_url( DMC_INSTAGRAM ); ?>" target="_blank" rel="noopener" aria-label="Instagram"><?php echo dmc_icon( 'instagram' ); // phpcs:ignore ?></a>
					<a href="<?php echo esc_url( dmc_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php echo dmc_icon( 'whatsapp' ); // phpcs:ignore ?></a>
					<a href="<?php echo esc_url( DMC_MAPS ); ?>" target="_blank" rel="noopener" aria-label="Google Maps"><?php echo dmc_icon( 'pin' ); // phpcs:ignore ?></a>
				</div>
			</div>
			<div>
				<h4><?php esc_html_e( 'Shop', 'dmc-revamp' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( $dmc_shop ); ?>"><?php esc_html_e( 'All varieties', 'dmc-revamp' ); ?></a></li>
					<?php foreach ( dmc_catalog( 3 ) as $p ) : ?>
						<li><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php echo esc_html( $p->get_name() ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Company', 'dmc-revamp' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( dmc_page_url( 'about-us' ) ); ?>"><?php esc_html_e( 'About us', 'dmc-revamp' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Bulk buy', 'dmc-revamp' ); ?></a></li>
					<li><a href="<?php echo esc_url( dmc_page_url( 'contact-us' ) ); ?>"><?php esc_html_e( 'Contact us', 'dmc-revamp' ); ?></a></li>
					<?php if ( get_privacy_policy_url() ) : ?>
						<li><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy policy', 'dmc-revamp' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Reach us', 'dmc-revamp' ); ?></h4>
				<ul class="dmc-footer__contact">
					<li><?php echo dmc_icon( 'pin' ); // phpcs:ignore ?><span><?php echo esc_html( DMC_ADDRESS ); ?></span></li>
					<li><?php echo dmc_icon( 'phone' ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( DMC_PHONE ); ?>"><?php echo esc_html( DMC_PHONE_DISPLAY ); ?></a></li>
					<li><?php echo dmc_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( DMC_EMAIL ); ?>"><?php echo esc_html( DMC_EMAIL ); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="dmc-footer__word" aria-hidden="true">DMC NUTS</div>
	<div class="dmc-wrap dmc-footer__bottom">
		<span><?php echo esc_html( sprintf( '©%s DMC Nuts · Deepak Mange & Co. All rights reserved.', gmdate( 'Y' ) ) ); ?></span>
		<span><?php esc_html_e( 'Wholesale almonds · Navi Mumbai, India', 'dmc-revamp' ); ?></span>
	</div>
</footer>
