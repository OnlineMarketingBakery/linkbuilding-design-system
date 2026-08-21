<?php
/**
 * Topbar — Masterblog utility bar (tagline + Adverteren / Partners / Contact).
 *
 * @package LBDS
 */
$tagline = lbds_tagline();
$util    = array(
	array(
		'label' => __( 'Adverteren', 'lbds' ),
		'url'   => home_url( '/adverteren/' ),
	),
	array(
		'label' => __( 'Partners', 'lbds' ),
		'url'   => home_url( '/partners/' ),
	),
	array(
		'label' => __( 'Contact', 'lbds' ),
		'url'   => home_url( '/contact/' ),
	),
);
?>
<div class="topbar">
	<div class="mb-page">
		<span><?php echo $tagline !== '' ? esc_html( $tagline ) : esc_html__( 'Elke week nieuwe artikelen', 'lbds' ); ?></span>
		<nav class="topbar-util" aria-label="<?php esc_attr_e( 'Hulpprogramma\'s', 'lbds' ); ?>">
			<?php foreach ( $util as $i => $item ) : ?>
				<?php if ( $i > 0 ) : ?><span class="topbar-sep" aria-hidden="true">·</span><?php endif; ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</div>
