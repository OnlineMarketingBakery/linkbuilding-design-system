<?php
/**
 * 404.
 *
 * @package LBDS
 */

get_header();
?>
<header class="lbds-page-hero">
	<div class="lbds-wrap">
		<h1><?php esc_html_e('Pagina niet gevonden', 'lbds'); ?></h1>
		<p class="lbds-hero__lead"><?php esc_html_e('Deze pagina bestaat niet (meer). Ga terug naar de homepage of bekijk de artikelen.', 'lbds'); ?></p>
		<div class="lbds-actions">
			<a class="lbds-btn lbds-btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Naar home', 'lbds'); ?></a>
		</div>
	</div>
</header>
<?php
get_footer();
