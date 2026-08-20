<?php
/**
 * Theme supports.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

function lbds_setup(): void {
	load_theme_textdomain('lbds', LBDS_DIR . '/languages');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support(
		'html5',
		array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script')
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support('align-wide');
	add_theme_support('responsive-embeds');

	register_nav_menus(
		array(
			'primary' => __('Primary', 'lbds'),
			'footer'  => __('Footer Over', 'lbds'),
		)
	);

	add_image_size('lbds-card', 720, 540, true);
	add_image_size('lbds-hero', 1200, 600, true);
	add_image_size('lbds-list', 208, 152, true);
}
add_action('after_setup_theme', 'lbds_setup');

function lbds_excerpt_length(int $length): int {
	return 22;
}
add_filter('excerpt_length', 'lbds_excerpt_length');

function lbds_excerpt_more(string $more): string {
	return '&hellip;';
}
add_filter('excerpt_more', 'lbds_excerpt_more');
