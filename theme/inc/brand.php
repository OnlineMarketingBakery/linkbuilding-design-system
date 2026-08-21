<?php
/**
 * Brand / --brand (Masterblog theming hook).
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
		'accent'           => '#7FA8C5',
		'tagline_override' => '',
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
		if (!empty($data['brand']) && is_string($data['brand'])) {
			$brand['accent'] = $data['brand'];
		}
		if (!empty($data['tagline']) && is_string($data['tagline'])) {
			$brand['tagline_override'] = $data['tagline'];
		}
		break;
	}

	return $brand;
}

/**
 * Sanitize hex for CSS custom properties.
 */
function lbds_sanitize_brand_hex(string $hex): string {
	$hex = sanitize_hex_color($hex);
	return is_string($hex) && $hex !== '' ? $hex : '#7FA8C5';
}

/**
 * Inline CSS variables — old-site blue/gray palette + --brand hook.
 */
function lbds_brand_css_variables(): string {
	$hex = lbds_sanitize_brand_hex(lbds_get_brand()['accent']);
	return sprintf(
		':root{' .
		'--brand:%1$s;' .
		'--brand-hover:color-mix(in srgb,%1$s 82%%,black);' .
		'--brand-active:color-mix(in srgb,%1$s 68%%,black);' .
		'--brand-subtle:color-mix(in srgb,%1$s 12%%,white);' .
		'--accent:%1$s;' .
		'--topbar:#2A4565;' .
		'--btn-secondary:#9E9E9E;' .
		'--ink-950:#0A0A0F;--ink-900:#141419;--ink-800:#22222B;--ink-700:#34343F;' .
		'--ink-500:#6B6B78;--ink-400:#8F8F9C;--ink-300:#B9B9C4;--ink-200:#D9D9E0;' .
		'--ink-100:#ECECF1;--ink-50:#F0F3F5;--white:#FFFFFF;' .
		'--text-strong:var(--ink-950);--text-body:#22222B;--text-muted:var(--ink-500);--text-faint:var(--ink-400);' .
		'--surface-page:#E9EDF0;--surface-sunken:#F0F3F5;--surface-raised:var(--white);' .
		'--border-subtle:var(--ink-100);--border-default:var(--ink-200);--border-strong:var(--ink-300);' .
		'--font-sans:"Quicksand",ui-rounded,"Segoe UI",system-ui,sans-serif;' .
		'--ls-tight:-0.03em;--ls-wide:0.02em;--ls-caps:0.12em;' .
		'--radius-xs:6px;--radius-sm:10px;--radius-md:14px;--radius-lg:20px;--radius-xl:28px;--radius-pill:999px;' .
		'--shadow-xs:0 1px 2px rgba(42,69,101,.06);--shadow-sm:0 2px 8px rgba(42,69,101,.07);' .
		'--shadow-md:0 8px 24px rgba(42,69,101,.09);--shadow-lg:0 18px 48px rgba(42,69,101,.12);' .
		'--dur-fast:120ms;--dur-base:200ms;--dur-slow:320ms;' .
		'--ease-out:cubic-bezier(0.22,1,0.36,1);--ease-spring:cubic-bezier(0.34,1.56,0.64,1);' .
		'}',
		esc_attr($hex)
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
			'default'           => '#7FA8C5',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'lbds_accent',
			array(
				'label'   => __('Brand (--brand)', 'lbds'),
				'section' => 'lbds_brand',
			)
		)
	);
}
add_action('customize_register', 'lbds_customize_register');
