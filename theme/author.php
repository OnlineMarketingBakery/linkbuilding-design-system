<?php
/**
 * Author — shows site as publisher (site name + about).
 *
 * @package LBDS
 */

get_header();
?>
<div class="mb-page">
	<div class="profile">
		<div class="avatar-lg"><?php echo esc_html(lbds_author_initial()); ?></div>
		<div>
			<span class="kicker"><?php esc_html_e('Redactie', 'lbds'); ?></span>
			<h1><?php echo esc_html(lbds_author_display_name()); ?></h1>
			<div class="role"><?php esc_html_e('Website', 'lbds'); ?></div>
			<p class="bio"><?php echo esc_html(lbds_site_about()); ?></p>
			<div class="stat-row">
				<div class="stat">
					<b><?php echo esc_html((string) (int) wp_count_posts()->publish); ?></b>
					<span><?php esc_html_e('Artikelen', 'lbds'); ?></span>
				</div>
			</div>
		</div>
	</div>

	<section class="section">
		<div class="section-head">
			<h2 class="section-title"><?php echo esc_html(sprintf(__('Artikelen van %s', 'lbds'), lbds_author_display_name())); ?></h2>
		</div>
		<div class="cards-grid reveal">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : ?>
					<?php the_post(); ?>
					<?php get_template_part('template-parts/content', 'card'); ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</section>
</div>
<?php
get_footer();
