<?php
/**
 * Post-import enrichment: categories + featured images.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * @return WP_Term[]
 */
function lbds_enrich_categories(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
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

/**
 * @param string     $text Haystack.
 * @param WP_Term[]  $categories Categories.
 */
function lbds_guess_category(string $text, array $categories): ?WP_Term {
	$text  = strtolower($text);
	$best  = null;
	$score = 0;

	foreach ($categories as $cat) {
		$cat_score = 0;
		$name      = strtolower($cat->name);
		if ($name !== '' && str_contains($text, $name)) {
			$cat_score += 4;
		}
		foreach (preg_split('/[\s\-_]+/', $cat->slug) ?: array() as $part) {
			if (strlen($part) < 4) {
				continue;
			}
			if (str_contains($text, $part)) {
				$cat_score += 2;
			}
		}
		if ($cat_score > $score) {
			$score = $cat_score;
			$best  = $cat;
		}
	}

	return $best;
}

/**
 * @return int|null Default category (most used on site).
 */
function lbds_default_category_id(): ?int {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 1,
		)
	);
	if (is_wp_error($terms) || !$terms) {
		return null;
	}
	$term = $terms[0];
	return ($term->slug === 'uncategorized') ? null : (int) $term->term_id;
}

$categories   = lbds_enrich_categories();
$default_cat  = lbds_default_category_id();
$cat_assigned = 0;

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ($posts as $post_id) {
	$post_id = (int) $post_id;
	$cats    = wp_get_post_categories($post_id);
	$real    = array_filter(
		$cats,
		static function (int $id): bool {
			$t = get_term($id, 'category');
			return $t instanceof WP_Term && $t->slug !== 'uncategorized' && $id !== 1;
		}
	);
	if ($real) {
		continue;
	}

	$post = get_post($post_id);
	if (!$post) {
		continue;
	}

	$text = $post->post_title . ' ' . wp_strip_all_tags($post->post_excerpt . ' ' . mb_substr($post->post_content, 0, 800));
	$pick = lbds_guess_category($text, $categories);
	if (!$pick && $default_cat) {
		wp_set_post_categories($post_id, array($default_cat), false);
		$cat_assigned++;
		continue;
	}
	if ($pick) {
		wp_set_post_categories($post_id, array((int) $pick->term_id), false);
		$cat_assigned++;
	}
}

echo "Categories assigned: {$cat_assigned}\n";

$old_domain = getenv('LBDS_OLD_DOMAIN') ?: '';
$limit      = (int) (getenv('LBDS_SIDeload_LIMIT') ?: 150);
$images     = 0;

$missing = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'meta_query'     => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			),
		),
	)
);

foreach ($missing as $post) {
	$url = '';
	if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->post_content, $m)) {
		$url = $m[1];
	}
	if ($url === '' && $old_domain !== '' && preg_match('/wp-content\/uploads\/[^\s"\']+/i', $post->post_content, $m2)) {
		$url = 'https://' . $old_domain . '/' . ltrim($m2[0], '/');
	}
	if ($url === '' || !preg_match('#^https?://#i', $url)) {
		continue;
	}

	$id = media_sideload_image($url, $post->ID, null, 'id');
	if (is_wp_error($id)) {
		continue;
	}
	set_post_thumbnail($post->ID, (int) $id);
	$images++;
}

echo "Featured images sideloaded: {$images}\n";

$remaining = count(
	get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	)
);
echo "Posts still missing featured image: {$remaining}\n";
