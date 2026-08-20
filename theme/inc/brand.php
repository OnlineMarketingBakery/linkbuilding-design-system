<?php
/**
 * Brand tokens (Customizer + optional brand.json in uploads or theme).
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Default brand values.
 *
 * @return array<string, string>
 */
function lbds_default_brand(): array {
	return array(
		'color_primary'   => '#1B5E4B',
		'color_accent'    => '#C45C26',
		'color_ink'       => '#1A1F1C',
		'color_paper'     => '#F3F0E8',
		'color_muted'     => '#5C675F',
		'font_display'    => 'Fraunces',
		'font_body'       => 'Figtree',
		'tagline_override'=> '',
	);
}

/**
 * Merge theme_mods + optional brand.json.
 *
 * @return array<string, string>
 */
function lbds_get_brand(): array {
	$defaults = lbds_default_brand();
	$brand    = $defaults;

	foreach (array_keys($defaults) as $key) {
		$mod = get_theme_mod('lbds_' . $key, null);
		if ($mod !== null && $mod !== '') {
			$brand[$key] = (string) $mod;
		}
	}

	$json_paths = array(
		WP_CONTENT_DIR . '/uploads/brand.json',
		LBDS_DIR . '/brand.json',
	);

	foreach ($json_paths as $path) {
		if (!is_readable($path)) {
			continue;
		}
		$data = json_decode((string) file_get_contents($path), true);
		if (!is_array($data)) {
			continue;
		}
		$map = array(
			'color_primary'    => 'color_primary',
			'color_accent'     => 'color_accent',
			'color_ink'        => 'color_ink',
			'color_paper'      => 'color_paper',
			'color_muted'      => 'color_muted',
			'font_display'     => 'font_display',
			'font_body'        => 'font_body',
			'tagline'          => 'tagline_override',
			'tagline_override' => 'tagline_override',
		);
		foreach ($map as $json_key => $brand_key) {
			if (!empty($data[$json_key]) && is_string($data[$json_key])) {
				$brand[$brand_key] = $data[$json_key];
			}
		}
		break;
	}

	return $brand;
}

/**
 * Inline CSS variables for the document.
 */
function lbds_brand_css_variables(): string {
	$b = lbds_get_brand();
	return sprintf(
		':root{--lbds-primary:%1$s;--lbds-accent:%2$s;--lbds-ink:%3$s;--lbds-paper:%4$s;--lbds-muted:%5$s;--lbds-font-display:"%6$s",Georgia,serif;--lbds-font-body:"%7$s",system-ui,sans-serif;}',
		esc_attr($b['color_primary']),
		esc_attr($b['color_accent']),
		esc_attr($b['color_ink']),
		esc_attr($b['color_paper']),
		esc_attr($b['color_muted']),
		esc_attr($b['font_display']),
		esc_attr($b['font_body'])
	);
}

/**
 * Register Customizer brand controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function lbds_customize_register(WP_Customize_Manager $wp_customize): void {
	$wp_customize->add_section(
		'lbds_brand',
		array(
			'title'    => __('Linkbuilding Brand', 'lbds'),
			'priority' => 30,
		)
	);

	$fields = array(
		'color_primary' => __('Primary color', 'lbds'),
		'color_accent'  => __('Accent color', 'lbds'),
		'color_ink'     => __('Ink / text color', 'lbds'),
		'color_paper'   => __('Paper / background', 'lbds'),
		'color_muted'   => __('Muted text', 'lbds'),
	);

	$defaults = lbds_default_brand();
	foreach ($fields as $key => $label) {
		$wp_customize->add_setting(
			'lbds_' . $key,
			array(
				'default'           => $defaults[$key],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'lbds_' . $key,
				array(
					'label'   => $label,
					'section' => 'lbds_brand',
				)
			)
		);
	}
}
add_action('customize_register', 'lbds_customize_register');
