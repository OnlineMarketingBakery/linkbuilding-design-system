<?php
/**
 * Topbar — Masterblog.
 *
 * @package LBDS
 */
$tagline = lbds_tagline();
?>
<div class="topbar">
	<div class="page">
		<span><?php echo $tagline !== '' ? esc_html($tagline) : esc_html__('Elke week nieuwe artikelen', 'lbds'); ?></span>
		<span><?php esc_html_e('Online Marketing Bakery', 'lbds'); ?></span>
	</div>
</div>
