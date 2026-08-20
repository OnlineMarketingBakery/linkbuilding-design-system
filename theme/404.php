<?php
/**
 * 404 — Masterblog Utility empty state.
 *
 * @package LBDS
 */

get_header();
?>
<div class="page">
	<div class="empty">
		<div class="code">404</div>
		<h2><?php esc_html_e('Deze pagina bestaat niet (meer)', 'lbds'); ?></h2>
		<p><?php esc_html_e('De link is kapot of de pagina is verplaatst. Ga terug naar home of bekijk de artikelen.', 'lbds'); ?></p>
		<a class="btn-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Naar home', 'lbds'); ?></a>
	</div>
</div>
<?php
get_footer();
