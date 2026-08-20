<?php
/**
 * Linkbuilding Design System theme.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

define('LBDS_VERSION', '1.0.0');
define('LBDS_DIR', get_template_directory());
define('LBDS_URI', get_template_directory_uri());

require_once LBDS_DIR . '/inc/brand.php';
require_once LBDS_DIR . '/inc/setup.php';
require_once LBDS_DIR . '/inc/assets.php';
require_once LBDS_DIR . '/inc/template-tags.php';
