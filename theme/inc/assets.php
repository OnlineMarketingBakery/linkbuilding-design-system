<?php
/**
 * Enqueue Masterblog assets.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

function lbds_enqueue_assets(): void {
	$fonts = 'https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Public+Sans:wght@400;500;600;700&display=swap';
	wp_enqueue_style('lbds-fonts', $fonts, array(), null);
	wp_enqueue_style('lbds-main', LBDS_URI . '/assets/css/main.css', array('lbds-fonts'), LBDS_VERSION);
	wp_add_inline_style('lbds-main', lbds_brand_css_variables());
	wp_enqueue_script('lbds-main', LBDS_URI . '/assets/js/main.js', array(), LBDS_VERSION, array('strategy' => 'defer', 'in_footer' => true));
}
add_action('wp_enqueue_scripts', 'lbds_enqueue_assets');
