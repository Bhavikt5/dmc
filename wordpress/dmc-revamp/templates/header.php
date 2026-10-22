<?php
/**
 * Site header (printed on wp_body_open).
 */

defined( 'ABSPATH' ) || exit;

$dmc_nav = array(
	array( __( 'Home', 'dmc-revamp' ), home_url( '/' ), is_front_page() ),
	array( __( 'Varieties', 'dmc-revamp' ), dmc_shop_url(), function_exists( 'is_woocommerce' ) && ( is_shop() || is_product() || is_product_taxonomy() ) ),
	array( __( 'Bulk Buy', 'dmc-revamp' ), home_url( '/#quote' ), false ),
	array( __( 'About Us', 'dmc-revamp' ), dmc_page_url( 'about-us' ), is_page( 'about-us' ) ),
	array( __( 'Contact', 'dmc-revamp' ), '#contact', false ),
);
$dmc_logo       = DMC_REVAMP_URL . 'assets/img/logo.webp';
$dmc_cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<div class="dmc-announce dmc-marquee" aria-label="<?php esc_attr_e( 'Announcements', 'dmc-revamp' ); ?>">
	<div class="dmc-marquee__track" data-speed="40">
		<span><?php esc_html_e( 'Free shipping on bulk orders', 'dmc-revamp' ); ?></span>
		<span><?php esc_html_e( 'Wholesale almonds · 15 Kg & 30 Kg bags', 'dmc-revamp' ); ?></span>
		<span><?php echo esc_html( sprintf( __( "Call or WhatsApp for today's rate: %s", 'dmc-revamp' ), DMC_PHONE_DISPLAY ) ); ?></span>
		<span><?php esc_html_e( 'Supplying retailers, distributors & importers since 2018', 'dmc-revamp' ); ?></span>
	</div>
</div>

<header class="dmc-header">
	<div class="dmc-wrap dmc-header__row">
		<a class="dmc-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'DMC Nuts home', 'dmc-revamp' ); ?>">
			<img src="<?php echo esc_url( $dmc_logo ); ?>" alt="DMC Nuts" width="62" height="62" />
			<span class="dmc-logo__text">DMC Nuts<small><?php esc_html_e( 'Since 2018', 'dmc-revamp' ); ?></small></span>
		</a>
		<nav class="dmc-nav" aria-label="<?php esc_attr_e( 'Main', 'dmc-revamp' ); ?>">
			<?php foreach ( $dmc_nav as $item ) : ?>
				<a href="<?php echo esc_url( $item[1] ); ?>"<?php echo $item[2] ? ' class="is-current"' : ''; ?>><?php echo esc_html( $item[0] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="dmc-header__icons">
			<a href="<?php echo esc_url( dmc_shop_url() ); ?>" aria-label="<?php esc_attr_e( 'Search', 'dmc-revamp' ); ?>"><?php echo dmc_icon( 'search' ); // phpcs:ignore ?></a>
			<a href="tel:<?php echo esc_attr( DMC_PHONE ); ?>" aria-label="<?php esc_attr_e( 'Call us', 'dmc-revamp' ); ?>"><?php echo dmc_icon( 'phone' ); // phpcs:ignore ?></a>
			<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'dmc-revamp' ); ?>"><?php echo dmc_icon( 'bag' ); // phpcs:ignore ?><span class="dmc-cart-count"><?php echo (int) $dmc_cart_count; ?></span></a>
			<?php endif; ?>
			<button class="dmc-burger" type="button" aria-label="<?php esc_attr_e( 'Menu', 'dmc-revamp' ); ?>" aria-expanded="false"><?php echo dmc_icon( 'menu' ); // phpcs:ignore ?></button>
		</div>
	</div>
</header>
<nav class="dmc-drawer" aria-label="<?php esc_attr_e( 'Mobile', 'dmc-revamp' ); ?>">
	<?php foreach ( $dmc_nav as $item ) : ?>
		<a href="<?php echo esc_url( $item[1] ); ?>"><?php echo esc_html( $item[0] ); ?></a>
	<?php endforeach; ?>
	<div class="dmc-drawer__meta"><?php echo esc_html( DMC_PHONE_DISPLAY . ' · ' . DMC_EMAIL ); ?></div>
</nav>
