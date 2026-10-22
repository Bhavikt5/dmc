<?php
/**
 * Inline SVG icons (stroke icons use currentColor).
 */

defined( 'ABSPATH' ) || exit;

function dmc_icon( $name, $class = '' ) {
	$s = 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
	$paths = array(
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"/>',
		'bag'       => '<path d="M5 7h14l-1.2 11.2a2 2 0 01-2 1.8H8.2a2 2 0 01-2-1.8z"/><path d="M9 7a3 3 0 016 0"/>',
		'menu'      => '<path d="M4 8h16M4 16h10"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-ne'  => '<path d="M7 17L17 7M8 7h9v9"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
		'filter'    => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'shield'    => '<path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/>',
		'refresh'   => '<path d="M20 12a8 8 0 11-3-6.2"/><path d="M20 4v5h-5"/>',
		'rupee'     => '<path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'lock'      => '<path d="M7 11V7a5 5 0 0110 0v4"/><rect x="4" y="11" width="16" height="10" rx="2"/>',
		'truck'     => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
		'check'     => '<path d="M5 12.5l4.5 4.5L19 7"/>',
		'x'         => '<path d="M6 6l12 12M18 6L6 18"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'whatsapp'  => '<path d="M4 20l1.3-4A8 8 0 1112 20a8 8 0 01-4-1z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 1a3.5 3.5 0 01-2-2l1-1-1-2z"/>',
		'pin'       => '<path d="M12 21s-7-6-7-11a7 7 0 0114 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'tag'       => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
		'zoom'      => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5M11 8v6M8 11h6"/>',
		'trend'     => '<path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/>',
		'drop'      => '<path d="M6 15a6 6 0 1012 0c0-4-6-11-6-11S6 11 6 15z"/>',
		'heart'     => '<path d="M12 21c-5-3-8-7-8-11a5 5 0 018-4 5 5 0 018 4c0 4-3 8-8 11z"/>',
		'leaf'      => '<path d="M4 20c8 0 14-6 14-14V4h-2C8 4 4 10 4 18z"/><path d="M4 20L14 10"/>',
		'sun'       => '<circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
	);
	if ( 'star' === $name ) {
		return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3 6.5 7 .8-5.2 4.8 1.4 7L12 17.6 5.8 21.1l1.4-7L2 9.3l7-.8z"/></svg>';
	}
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	$cls = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	return '<svg' . $cls . ' viewBox="0 0 24 24" ' . $s . ' aria-hidden="true">' . $paths[ $name ] . '</svg>';
}

function dmc_stars( $rating = 5 ) {
	$out = '<span class="dmc-stars" aria-label="' . esc_attr( sprintf( '%s out of 5 stars', $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$out .= $i <= round( $rating ) ? dmc_icon( 'star' ) : str_replace( 'fill="currentColor"', 'fill="currentColor" opacity=".25"', dmc_icon( 'star' ) );
	}
	return $out . '</span>';
}
