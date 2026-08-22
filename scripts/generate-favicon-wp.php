<?php
/**
 * Generate per-site favicon.svg from brand.json accent + site initial.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

$brand_path = WP_CONTENT_DIR . '/uploads/brand.json';
$accent     = '#E06B2D';

if (is_readable($brand_path)) {
	$data = json_decode((string) file_get_contents($brand_path), true);
	if (is_array($data) && !empty($data['accent']) && is_string($data['accent'])) {
		$maybe = sanitize_hex_color($data['accent']);
		if (is_string($maybe) && $maybe !== '') {
			$accent = $maybe;
		}
	}
}

$name    = get_bloginfo('name');
$initial = mb_strtoupper(mb_substr(trim($name), 0, 1));
if ($initial === '') {
	$initial = 'B';
}

$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none">
  <rect width="32" height="32" rx="8" fill="#0B1220"/>
  <text x="16" y="22" text-anchor="middle" font-family="Quicksand, Arial, sans-serif" font-size="17" font-weight="700" fill="{$accent}">{$initial}</text>
</svg>
SVG;

$out = WP_CONTENT_DIR . '/uploads/favicon.svg';
if (!is_dir(dirname($out))) {
	wp_mkdir_p(dirname($out));
}
file_put_contents($out, $svg);
echo "Wrote {$out} (accent {$accent}, initial {$initial})\n";
