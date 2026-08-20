<?php
/**
 * Brand / --accent (Masterblog theming hook).
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @return array{accent:string,tagline_override:string}
 */
function lbds_default_brand(): array {
	return array(
		'accent'            => '#B5502E',
		'tagline_override'  => '',
	);
}

/**
 * @return array{accent:string,tagline_override:string}
 */
function lbds_get_brand(): array {
	$defaults = lbds_default_brand();
	$brand    = $defaults;

	$mod = get_theme_mod('lbds_accent', null);
	if (is_string($mod) && $mod !== '') {
		$brand['accent'] = $mod;
	}

	$paths = array(
		WP_CONTENT_DIR . '/uploads/brand.json',
		LBDS_DIR . '/brand.json',
	);
	foreach ($paths as $path) {
		if (!is_readable($path)) {
			continue;
		}
		$data = json_decode((string) file_get_contents($path), true);
		if (!is_array($data)) {
			continue;
		}
		if (!empty($data['accent']) && is_string($data['accent'])) {
			$brand['accent'] = $data['accent'];
		}
		if (!empty($data['color_accent']) && is_string($data['color_accent'])) {
			$brand['accent'] = $data['color_accent'];
		}
		if (!empty($data['tagline']) && is_string($data['tagline'])) {
			$brand['tagline_override'] = $data['tagline'];
		}
		break;
	}

	return $brand;
}

/**
 * Inline CSS variables for Masterblog tokens + accent.
 */
function lbds_brand_css_variables(): string {
	$b = lbds_get_brand();
	return sprintf(
		':root{--paper:#FAF7F2;--surface:#FFFFFF;--ink:#211D18;--ink-muted:#6B655C;--ink-faint:#948C7F;--border:#E3DDD3;--border-strong:#CFC6B8;--font-display:"Newsreader",Georgia,serif;--font-body:"Public Sans",-apple-system,"Segoe UI",sans-serif;--accent:%1$s;}',
		esc_attr($b['accent'])
	);
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function lbds_customize_register(WP_Customize_Manager $wp_customize): void {
	$wp_customize->add_section(
		'lbds_brand',
		array(
			'title'    => __('Masterblog Brand', 'lbds'),
			'priority' => 30,
		)
	);
	$wp_customize->add_setting(
		'lbds_accent',
		array(
			'default'           => '#B5502E',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'lbds_accent',
			array(
				'label'   => __('Accent (--accent)', 'lbds'),
				'section' => 'lbds_brand',
			)
		)
	);
}
add_action('customize_register', 'lbds_customize_register');
