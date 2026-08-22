<?php
/**
 * Remove dummy / spam posts after pack import.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

$junk_slugs = array(
	'hello-world',
	'sample-page',
	'world',
);

$junk_title_patterns = array(
	'/^hello world!?\.?$/i',
	'/^sample page$/i',
);

$spam_patterns = array(
	'/casino/i',
	'/gokken/i',
	'/slot(s|machine)?/i',
	'/online casino/i',
	'/poker/i',
	'/blackjack/i',
	'/roulette/i',
	'/viagra/i',
	'/cialis/i',
	'/crypto giveaway/i',
);

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$deleted = 0;
foreach ($posts as $post_id) {
	$post = get_post((int) $post_id);
	if (!$post) {
		continue;
	}

	$slug  = (string) $post->post_name;
	$title = (string) $post->post_title;
	$text  = $title . ' ' . wp_strip_all_tags((string) $post->post_content);

	$is_junk = in_array($slug, $junk_slugs, true);
	foreach ($junk_title_patterns as $pattern) {
		if (preg_match($pattern, trim($title))) {
			$is_junk = true;
			break;
		}
	}
	if (str_contains(strtolower((string) $post->post_content), 'welcome to wordpress. this is your first post')) {
		$is_junk = true;
	}

	$is_spam = false;
	foreach ($spam_patterns as $pattern) {
		if (preg_match($pattern, $text)) {
			$is_spam = true;
			break;
		}
	}

	if (!$is_junk && !$is_spam) {
		continue;
	}

	wp_delete_post((int) $post_id, true);
	$deleted++;
	echo 'Deleted: ' . $title . " (#{$post_id})\n";
}

echo "Purge complete: {$deleted} post(s) removed.\n";
