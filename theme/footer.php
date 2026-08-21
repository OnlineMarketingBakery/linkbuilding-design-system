<?php
/**
 * Footer — 3-col: brand | categories | over (Privacy + Artikelen).
 *
 * @package LBDS
 */
$artikelen_url = get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/');
?>
</main>
<footer class="site-footer">
	<div class="mb-page">
		<div class="footer-grid footer-grid--3">
			<div class="footer-col">
				<h4><?php lbds_site_name(); ?>.</h4>
				<p>
					<?php
					$t = lbds_tagline();
					echo $t !== '' ? esc_html($t) : esc_html__('Praktische artikelen over verbouwen, klussen en wonen.', 'lbds');
					?>
				</p>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e('Categorieën', 'lbds'); ?></h4>
				<?php foreach (lbds_nav_categories() as $cat) : ?>
					<a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e('Over', 'lbds'); ?></h4>
				<a href="<?php echo esc_url($artikelen_url); ?>"><?php esc_html_e('Artikelen', 'lbds'); ?></a>
				<a href="<?php echo esc_url(home_url('/privacy/')); ?>"><?php esc_html_e('Privacy', 'lbds'); ?></a>
			</div>
		</div>
		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php lbds_site_name(); ?>.</span>
			<span><?php esc_html_e('Onderdeel van Online Marketing Bakery', 'lbds'); ?></span>
		</div>
	</div>
</footer>
</div><!-- .mb-root -->
<?php wp_footer(); ?>
</body>
</html>
