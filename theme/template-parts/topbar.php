<?php
/**
 * Topbar — tagline only (utility pages removed).
 *
 * @package LBDS
 */
$tagline = lbds_tagline();
?>
<div class="topbar">
	<div class="mb-page">
		<span><?php echo $tagline !== '' ? esc_html( $tagline ) : esc_html__( 'Elke week nieuwe artikelen', 'lbds' ); ?></span>
	</div>
</div>
