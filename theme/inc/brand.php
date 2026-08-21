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
 * @return array{accent:string,tagline_override:string,about:string}
 */
function lbds_default_brand(): array {
	return array(
		'accent'           => '#E06B2D',
		'tagline_override' => '',
		'about'            => '',
	);
}

/**
 * @return array{accent:string,tagline_override:string,about:string}
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
		if (!empty($data['about']) && is_string($data['about'])) {
			$brand['about'] = $data['about'];
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
	return is_string($hex) && $hex !== '' ? $hex : '#E06B2D';
}

/**
 * Inline CSS variables — vivid modern blue + --brand hook.
 */
function lbds_brand_css_variables(): string {
	$hex = lbds_sanitize_brand_hex(lbds_get_brand()['accent']);
	return sprintf(
		':root{' .
		'--brand:%1$s;' .
		'--brand-hover:color-mix(in srgb,%1$s 82%%,black);' .
		'--brand-active:color-mix(in srgb,%1$s 68%%,black);' .
		'--brand-subtle:color-mix(in srgb,%1$s 10%%,white);' .
		'--accent:%1$s;' .
		'--topbar:#0B1220;' .
		'--btn-secondary:#64748B;' .
		'--ink-950:#0A0A0F;--ink-900:#141419;--ink-800:#22222B;--ink-700:#34343F;' .
		'--ink-500:#6B6B78;--ink-400:#8F8F9C;--ink-300:#B9B9C4;--ink-200:#D9D9E0;' .
		'--ink-100:#ECECF1;--ink-50:#F4F6FB;--white:#FFFFFF;' .
		'--text-strong:var(--ink-950);--text-body:#22222B;--text-muted:var(--ink-500);--text-faint:var(--ink-400);' .
		'--surface-page:#F4F6FB;--surface-sunken:#EEF1F8;--surface-raised:var(--white);' .
		'--border-subtle:var(--ink-100);--border-default:var(--ink-200);--border-strong:var(--ink-300);' .
		'--font-sans:"Quicksand",ui-rounded,"Segoe UI",system-ui,sans-serif;' .
		'--ls-tight:-0.03em;--ls-wide:0.02em;--ls-caps:0.12em;' .
		'--radius-xs:6px;--radius-sm:10px;--radius-md:14px;--radius-lg:20px;--radius-xl:28px;--radius-pill:999px;' .
		'--shadow-xs:0 1px 2px rgba(224,107,45,.08);--shadow-sm:0 2px 10px rgba(224,107,45,.10);' .
		'--shadow-md:0 10px 28px rgba(224,107,45,.14);--shadow-lg:0 20px 50px rgba(224,107,45,.18);' .
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
			'default'           => '#E06B2D',
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
