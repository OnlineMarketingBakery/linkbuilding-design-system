<?php
/**
 * After WXR import: for partners/adverteren/contact, keep pack content on the
 * canonical slug page (merge duplicates), and normalize CF7 shortcodes to title="Contact".
 *
 * Invoked via: wp eval-file scripts/apply-pack-pages-wp.php
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

$slugs = array( 'partners', 'adverteren', 'contact' );

/**
 * Prefer the page whose content looks like real pack content (longer / has links or CF7).
 *
 * @param WP_Post $a A.
 * @param WP_Post $b B.
 */
$score = static function ( WP_Post $p ): int {
	$content = (string) $p->post_content;
	$score   = strlen( $content );
	if ( str_contains( $content, 'contact-form-7' ) ) {
		$score += 5000;
	}
	if ( str_contains( $content, '<ul>' ) || str_contains( $content, '<li>' ) ) {
		$score += 2000;
	}
	return $score;
};

foreach ( $slugs as $slug ) {
	$pages = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 20,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	if ( ! $pages ) {
		echo "No page /{$slug}/\n";
		continue;
	}

	usort(
		$pages,
		static function ( WP_Post $a, WP_Post $b ) use ( $score ): int {
			return $score( $b ) <=> $score( $a );
		}
	);

	$keeper = $pages[0];
	$content = (string) $keeper->post_content;

	// Normalize any CF7 shortcode to title="Contact".
	$content = preg_replace(
		'/\[contact-form-7\b[^\]]*\]/i',
		'[contact-form-7 title="Contact"]',
		$content
	);

	$update = array(
		'ID'           => $keeper->ID,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_name'    => $slug,
	);
	wp_update_post( $update );
	echo "Kept /{$slug}/ as ID {$keeper->ID} (score {$score( $keeper )})\n";

	foreach ( array_slice( $pages, 1 ) as $dup ) {
		wp_delete_post( (int) $dup->ID, true );
		echo "Deleted duplicate {$slug} ID {$dup->ID}\n";
	}
}

echo "Pack page overrides done.\n";
