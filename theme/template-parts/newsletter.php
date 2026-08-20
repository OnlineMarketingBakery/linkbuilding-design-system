<?php
/**
 * Newsletter block — Masterblog.
 *
 * @package LBDS
 */
$variant = $args['variant'] ?? 'sidebar';
?>
<?php if ($variant === 'sidebar') : ?>
	<div class="newsletter">
		<div class="newsletter-in">
			<h3><?php esc_html_e('Mis geen artikel', 'lbds'); ?></h3>
			<p><?php esc_html_e('Elke week de beste tips in je inbox, gratis.', 'lbds'); ?></p>
			<form action="<?php echo esc_url(home_url('/contact/')); ?>" method="get">
				<input type="email" name="email" placeholder="jouw@email.nl" required>
				<button type="submit"><?php esc_html_e('Aanmelden', 'lbds'); ?></button>
			</form>
		</div>
	</div>
<?php else : ?>
	<div class="newsletter">
		<div>
			<h3><?php esc_html_e('Mis geen enkel artikel', 'lbds'); ?></h3>
			<p><?php esc_html_e('Elke week de beste tips in je inbox, gratis.', 'lbds'); ?></p>
		</div>
		<form action="<?php echo esc_url(home_url('/contact/')); ?>" method="get">
			<input type="email" name="email" placeholder="jouw@email.nl" required>
			<button type="submit"><?php esc_html_e('Aanmelden', 'lbds'); ?></button>
		</form>
	</div>
<?php endif; ?>
