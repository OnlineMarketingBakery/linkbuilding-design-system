<?php
/**
 * Build primary/footer menus from blueprint/menus.json.
 * Invoked via: wp eval-file scripts/build-menus-wp.php
 * Or loaded by provision after bootstrapping WP.
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	// Allow CLI bootstrap when called as wp eval-file from WP root context — otherwise exit.
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

$repo = getenv('LBDS_REPO') ?: dirname(__DIR__);
$json = $repo . '/blueprint/menus.json';
if (!is_readable($json)) {
	fwrite(STDERR, "Missing menus.json\n");
	return;
}

$data = json_decode((string) file_get_contents($json), true);
if (!is_array($data)) {
	return;
}

/**
 * Ensure menu and assign location.
 *
 * @param string               $name Menu name.
 * @param string               $location Theme location.
 * @param array<int, array>    $items Items.
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
			'menu-item-title'  => $item['title'] ?? 'Item',
			'menu-item-status' => 'publish',
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

lbds_sync_menu('Primary', 'primary', $data['primary'] ?? array());
lbds_sync_menu('Footer', 'footer', $data['footer'] ?? array());
