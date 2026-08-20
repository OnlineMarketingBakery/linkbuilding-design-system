<?php
/**
 * Search — Masterblog Utility search section.
 *
 * @package LBDS
 */

get_header();
$q = get_search_query();
?>
<div class="page">
	<section class="section">
		<span class="kicker"><?php esc_html_e('Zoekresultaten', 'lbds'); ?></span>
		<div class="search-head">
			<h1>
				<?php
				echo $q
					? esc_html(sprintf(__('Resultaten voor “%s”', 'lbds'), $q))
					: esc_html__('Zoeken', 'lbds');
				?>
			</h1>
			<form class="search-bar" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
				<input type="search" name="s" value="<?php echo esc_attr($q); ?>" placeholder="<?php esc_attr_e('Zoeken…', 'lbds'); ?>">
				<button type="submit"><?php esc_html_e('Zoeken', 'lbds'); ?></button>
			</form>
		</div>
		<div class="result-count">
			<?php
			global $wp_query;
			echo esc_html(sprintf(_n('%d artikel gevonden', '%d artikelen gevonden', (int) $wp_query->found_posts, 'lbds'), (int) $wp_query->found_posts));
			?>
		</div>
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<a class="result-row" href="<?php the_permalink(); ?>">
					<div class="ph">
						<?php if (has_post_thumbnail()) : ?>
							<?php the_post_thumbnail('lbds-list'); ?>
						<?php else : ?>
							<?php echo lbds_placeholder_svg(); // phpcs:ignore ?>
						<?php endif; ?>
					</div>
					<div>
						<h3><?php the_title(); ?></h3>
						<div class="meta" style="margin-top:6px;">
							<?php
							$c = lbds_primary_category();
							if ($c) {
								echo esc_html($c->name);
							}
							?>
							<span class="dot"></span>
							<span><?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min', 'lbds'); ?></span>
						</div>
					</div>
				</a>
			<?php endwhile; ?>
		<?php endif; ?>
	</section>
</div>
<?php
get_footer();
