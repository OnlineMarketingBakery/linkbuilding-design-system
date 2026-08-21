<?php
/**
 * Template helpers — Masterblog.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Categories for cat-strip / footer (exclude uncategorized).
 *
 * @return WP_Term[]
 */
function lbds_nav_categories(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	if (is_wp_error($terms) || !is_array($terms)) {
		return array();
	}
	return array_values(
		array_filter(
			$terms,
			static function (WP_Term $t): bool {
				return $t->slug !== 'uncategorized' && (int) $t->term_id !== 1;
			}
		)
	);
}

function lbds_site_name(): void {
	echo esc_html(get_bloginfo('name'));
}

/**
 * Logo wordmark: last word + period in brand color (Masterblog pattern).
 */
function lbds_site_logo_text(): void {
	$name = trim((string) get_bloginfo('name'));
	if ($name === '') {
		echo '<span>.</span>';
		return;
	}
	$parts = preg_split('/\s+/u', $name) ?: array();
	if (count($parts) < 2) {
		echo '<span>' . esc_html($name) . '.</span>';
		return;
	}
	$last = array_pop($parts);
	echo esc_html(implode(' ', $parts) . ' ');
	echo '<span>' . esc_html($last) . '.</span>';
}

/**
 * Public byline name — always the site name (never WP "admin").
 */
function lbds_author_display_name(): string {
	$name = trim((string) get_bloginfo('name'));
	return $name !== '' ? $name : __('Redactie', 'lbds');
}

/**
 * First character for avatar initials.
 */
function lbds_author_initial(): string {
	$name = lbds_author_display_name();
	$ch   = mb_substr($name, 0, 1);
	return $ch !== '' ? mb_strtoupper($ch) : 'R';
}

/**
 * Short about blurb for author card — per-site via brand.json `about`, else tagline, else default.
 */
function lbds_site_about(): string {
	$brand = lbds_get_brand();
	if (!empty($brand['about']) && is_string($brand['about'])) {
		return trim($brand['about']);
	}
	$tagline = lbds_tagline();
	if ($tagline !== '') {
		return $tagline;
	}
	return sprintf(
		/* translators: %s: site name */
		__('Praktische artikelen en tips van %s — helder, bruikbaar en up-to-date.', 'lbds'),
		lbds_author_display_name()
	);
}

function lbds_tagline(): string {
	$brand = lbds_get_brand();
	if ($brand['tagline_override'] !== '') {
		return $brand['tagline_override'];
	}
	return (string) get_bloginfo('description');
}

function lbds_reading_time(?int $post_id = null): int {
	$post_id = $post_id ?: get_the_ID();
	$content = (string) get_post_field('post_content', $post_id);
	$words   = str_word_count(wp_strip_all_tags($content));
	return max(1, (int) ceil($words / 200));
}

/**
 * Primary category for a post.
 */
function lbds_primary_category(?int $post_id = null): ?WP_Term {
	$cats = get_the_category($post_id ?: get_the_ID());
	if (!$cats) {
		return null;
	}
	foreach ($cats as $c) {
		if ($c->slug !== 'uncategorized') {
			return $c;
		}
	}
	return $cats[0];
}

/**
 * Simple TOC from h2 in content.
 *
 * @return array<int, array{id:string,text:string}>
 */
function lbds_toc_items(string $content): array {
	$items = array();
	if (!preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $content, $m)) {
		return $items;
	}
	foreach ($m[1] as $i => $html) {
		$text = wp_strip_all_tags($html);
		$id   = 'section-' . ($i + 1);
		$items[] = array('id' => $id, 'text' => $text);
	}
	return $items;
}

/**
 * Inject ids into h2 for TOC anchors.
 */
function lbds_content_with_toc_ids(string $content): string {
	$i = 0;
	return (string) preg_replace_callback(
		'/<h2([^>]*)>/i',
		static function (array $m) use (&$i): string {
			$i++;
			$attrs = $m[1];
			if (stripos($attrs, 'id=') !== false) {
				return $m[0];
			}
			return '<h2 id="section-' . $i . '"' . $attrs . '>';
		},
		$content
	);
}

function lbds_placeholder_svg(): string {
	return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 16l5-6 4 5 3-4 6 7"/><circle cx="8" cy="7" r="2"/></svg>';
}
