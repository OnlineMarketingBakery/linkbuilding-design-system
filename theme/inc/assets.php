<?php
/**
 * Enqueue styles and scripts.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Front assets.
 */
function lbds_enqueue_assets(): void {
	$brand = lbds_get_brand();
	$display = rawurlencode($brand['font_display']);
	$body    = rawurlencode($brand['font_body']);
	$fonts   = "https://fonts.googleapis.com/css2?family={$display}:opsz,wght@9..144,500;9..144,700&family={$body}:wght@400;500;600;700&display=swap";

	wp_enqueue_style('lbds-fonts', $fonts, array(), null);
	wp_enqueue_style(
		'lbds-main',
		LBDS_URI . '/assets/css/main.css',
		array('lbds-fonts'),
		LBDS_VERSION
	);
	wp_add_inline_style('lbds-main', lbds_brand_css_variables());

	wp_enqueue_script(
		'lbds-main',
		LBDS_URI . '/assets/js/main.js',
		array(),
		LBDS_VERSION,
		array('strategy' => 'defer', 'in_footer' => true)
	);
}
add_action('wp_enqueue_scripts', 'lbds_enqueue_assets');

/**
 * Do not print Google Fonts as render-blocking with media=all quirks — keep default.
 *
 * @param string $tag HTML.
 * @param string $handle Handle.
 */
function lbds_style_loader_tag(string $tag, string $handle): string {
	return $tag;
}
add_filter('style_loader_tag', 'lbds_style_loader_tag', 10, 2);
