<?php
/**
 * Best-effort: for posts missing thumbnails, try first image URL from content or skip.
 * Full media-manifest sideload of hundreds of files is optional / rate-limited.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	return;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'meta_query'     => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			),
		),
	)
);

$done = 0;
foreach ($posts as $post) {
	if (!preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->post_content, $m)) {
		continue;
	}
	$url = $m[1];
	if (!preg_match('#^https?://#i', $url)) {
		continue;
	}
	$id = media_sideload_image($url, $post->ID, null, 'id');
	if (is_wp_error($id)) {
		continue;
	}
	set_post_thumbnail($post->ID, (int) $id);
	$done++;
	if ($done >= 25) {
		break;
	}
}

echo "Sideloaded featured images: {$done}\n";
