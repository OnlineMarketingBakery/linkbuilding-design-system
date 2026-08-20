<?php
/**
 * Footer.
 *
 * @package LBDS
 */
?>
</main>
<footer class="lbds-footer">
	<div class="lbds-wrap lbds-footer__grid">
		<div>
			<p class="lbds-footer__title"><?php lbds_site_name(); ?></p>
			<p class="lbds-footer__copy">
				<?php
				$tagline = lbds_tagline();
				echo $tagline !== '' ? esc_html($tagline) : esc_html__('Praktische gidsen en tips.', 'lbds');
				?>
			</p>
			<p class="lbds-footer__copy" style="margin-top:1.25rem">
				&copy; <?php echo esc_html(gmdate('Y')); ?> <?php lbds_site_name(); ?>
			</p>
		</div>
		<nav aria-label="<?php esc_attr_e('Footer', 'lbds'); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
