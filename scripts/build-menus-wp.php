<?php
/**
 * Build primary/footer menus — primary includes top categories.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

/**
 * Top categories for nav (exclude uncategorized).
 *
 * @param int $limit Max categories.
 * @return WP_Term[]
 */
function lbds_menu_categories(int $limit = 6): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => $limit + 4,
		)
	);
	if (is_wp_error($terms) || !is_array($terms)) {
		return array();
	}
	$out = array();
	foreach ($terms as $term) {
		if ($term->slug === 'uncategorized' || (int) $term->term_id === 1) {
			continue;
		}
		$out[] = $term;
		if (count($out) >= $limit) {
			break;
		}
	}
	return $out;
}

/**
 * Ensure menu and assign location.
 *
 * @param string            $name Menu name.
 * @param string            $location Theme location.
 * @param array<int, array> $items Items.
 */
function lbds_sync_menu(string $name, string $location, array $items): void {
	$menus = wp_get_nav_menus();
	$menu_id = 0;
	foreach ($menus as $m) {
		if ($m->name === $name) {
			$menu_id = (int) $m->term_id;
			break;
		}
	}
	if (!$menu_id) {
		$menu_id = (int) wp_create_nav_menu($name);
	}

	$existing = wp_get_nav_menu_items($menu_id);
	if (is_array($existing)) {
		foreach ($existing as $item) {
			wp_delete_post((int) $item->ID, true);
		}
	}

	$order = 1;
	foreach ($items as $item) {
		$type = $item['type'] ?? 'custom';
		$args = array(
			'menu-item-title'    => $item['title'] ?? 'Item',
			'menu-item-status'   => 'publish',
			'menu-item-position' => $order++,
		);
		if ($type === 'page' && !empty($item['slug'])) {
			$pages = get_posts(
				array(
					'name'        => $item['slug'],
					'post_type'   => 'page',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			);
			if (!$pages) {
				continue;
			}
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $pages[0]->ID;
		} elseif ($type === 'taxonomy' && !empty($item['term_id'])) {
			$args['menu-item-type']      = 'taxonomy';
			$args['menu-item-object']    = 'category';
			$args['menu-item-object-id'] = (int) $item['term_id'];
		} else {
			$url = $item['url'] ?? '/';
			if (str_starts_with($url, '/')) {
				$url = home_url($url);
			}
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = $url;
		}
		wp_update_nav_menu_item($menu_id, 0, $args);
	}

	$locations = get_theme_mod('nav_menu_locations', array());
	if (!is_array($locations)) {
		$locations = array();
	}
	$locations[$location] = $menu_id;
	set_theme_mod('nav_menu_locations', $locations);
	echo "Menu synced: {$name} -> {$location}\n";
}

$repo = getenv('LBDS_REPO') ?: dirname(__DIR__);
$json = $repo . '/blueprint/menus.json';
$footer = array(
	array(
		'title' => 'Artikelen',
		'url'   => '/artikelen/',
		'type'  => 'custom',
	),
	array(
		'title' => 'Privacy',
		'slug'  => 'privacy',
		'type'  => 'page',
	),
);

if (is_readable($json)) {
	$data = json_decode((string) file_get_contents($json), true);
	if (is_array($data['footer'] ?? null)) {
		$footer = $data['footer'];
	}
}

$primary = array(
	array(
		'title' => 'Home',
		'url'   => '/',
		'type'  => 'custom',
	),
);

foreach (lbds_menu_categories(6) as $cat) {
	$primary[] = array(
		'title'   => $cat->name,
		'type'    => 'taxonomy',
		'term_id' => (int) $cat->term_id,
	);
}

$primary[] = array(
	'title' => 'Artikelen',
	'url'   => '/artikelen/',
	'type'  => 'custom',
);

lbds_sync_menu('Primary', 'primary', $primary);
lbds_sync_menu('Footer', 'footer', $footer);
