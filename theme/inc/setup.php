<?php
/**
 * Theme supports and menus.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * After setup theme.
 */
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
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support('align-wide');
	add_theme_support('responsive-embeds');
	add_theme_support('editor-styles');

	register_nav_menus(
		array(
			'primary' => __('Primary', 'lbds'),
			'footer'  => __('Footer', 'lbds'),
		)
	);

	add_image_size('lbds-card', 720, 480, true);
	add_image_size('lbds-hero', 1600, 900, true);
}
add_action('after_setup_theme', 'lbds_setup');

/**
 * Widgets.
 */
function lbds_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __('Footer', 'lbds'),
			'id'            => 'footer-1',
			'before_widget' => '<section class="lbds-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="lbds-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'lbds_widgets_init');

/**
 * Excerpt length.
 *
 * @param int $length Length.
 */
function lbds_excerpt_length(int $length): int {
	return 28;
}
add_filter('excerpt_length', 'lbds_excerpt_length');

/**
 * Excerpt more.
 */
function lbds_excerpt_more(string $more): string {
	return '&hellip;';
}
add_filter('excerpt_more', 'lbds_excerpt_more');
