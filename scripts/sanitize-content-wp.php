<?php
/**
 * Strip common Elementor wrapper noise; keep inner text/HTML where possible.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	return;
}

$q = new WP_Query(
	array(
		'post_type'      => array('post', 'page'),
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$updated = 0;
foreach ($q->posts as $id) {
	$content = (string) get_post_field('post_content', $id);
	if ($content === '' || (stripos($content, 'elementor') === false && strpos($content, 'data-elementor') === false)) {
		continue;
	}

	$clean = $content;
	// Remove Elementor data attributes noise in tags.
	$clean = preg_replace('/\sdata-elementor-[a-z0-9_-]+="[^"]*"/i', '', $clean) ?? $clean;
	$clean = preg_replace('/\sdata-id="[^"]*"/i', '', $clean) ?? $clean;
	$clean = preg_replace('/\sdata-element_type="[^"]*"/i', '', $clean) ?? $clean;
	$clean = preg_replace('/\sdata-widget_type="[^"]*"/i', '', $clean) ?? $clean;
	// Unwrap empty elementor div spam somewhat.
	$clean = preg_replace('/<div[^class>]*class="[^"]*elementor[^"]*"[^>]*>/i', '<div>', $clean) ?? $clean;
	// Strip shortcodes that do nothing without Elementor.
	$clean = preg_replace('/\[elementor-template[^\]]*\]/i', '', $clean) ?? $clean;

	if ($clean !== $content) {
		wp_update_post(
			array(
				'ID'           => (int) $id,
				'post_content' => $clean,
			)
		);
		$updated++;
	}
}

echo "Sanitized posts/pages: {$updated}\n";
