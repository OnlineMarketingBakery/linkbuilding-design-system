<?php
/**
 * Template helpers.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Print site brand name.
 */
function lbds_site_name(): void {
	echo esc_html(get_bloginfo('name'));
}

/**
 * Tagline (option or brand override).
 */
function lbds_tagline(): string {
	$brand = lbds_get_brand();
	if (!empty($brand['tagline_override'])) {
		return $brand['tagline_override'];
	}
	return (string) get_bloginfo('description');
}

/**
 * Posted on meta.
 */
function lbds_posted_on(): void {
	$time = sprintf(
		'<time class="lbds-meta__date" datetime="%1$s">%2$s</time>',
		esc_attr(get_the_date(DATE_W3C)),
		esc_html(get_the_date())
	);
	$cats = get_the_category_list(', ');
	echo '<div class="lbds-meta">';
	echo $time; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ($cats) {
		echo ' <span class="lbds-meta__sep">·</span> <span class="lbds-meta__cats">' . $cats . '</span>'; // phpcs:ignore
	}
	echo '</div>';
}
