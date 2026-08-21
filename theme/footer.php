<?php
/**
 * Footer — Masterblog site-footer.
 *
 * @package LBDS
 */
?>
</main>
<footer class="site-footer">
	<div class="mb-page">
		<div class="footer-grid">
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
				<h4><?php esc_html_e('Volgen', 'lbds'); ?></h4>
				<a href="<?php echo esc_url(home_url('/artikelen/')); ?>"><?php esc_html_e('Artikelen', 'lbds'); ?></a>
				<a href="<?php echo esc_url(home_url('/partners/')); ?>"><?php esc_html_e('Partners', 'lbds'); ?></a>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e('Over', 'lbds'); ?></h4>
				<?php
				if (has_nav_menu('footer')) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-over-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
							'items_wrap'     => '%3$s',
							'walker'         => new class() extends Walker_Nav_Menu {
								public function start_lvl( &$output, $depth = 0, $args = null ) {}
								public function end_lvl( &$output, $depth = 0, $args = null ) {}
								public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
									$output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
								}
								public function end_el( &$output, $item, $depth = 0, $args = null ) {}
							},
						)
					);
				} else {
					?>
					<a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'lbds'); ?></a>
					<a href="<?php echo esc_url(home_url('/partners/')); ?>"><?php esc_html_e('Partners', 'lbds'); ?></a>
					<a href="<?php echo esc_url(home_url('/privacy/')); ?>"><?php esc_html_e('Privacy', 'lbds'); ?></a>
					<a href="<?php echo esc_url(home_url('/adverteren/')); ?>"><?php esc_html_e('Adverteren', 'lbds'); ?></a>
					<?php
				}
				?>
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
