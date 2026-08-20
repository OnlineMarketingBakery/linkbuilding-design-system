<?php
/**
 * Blog index / fallback.
 *
 * @package LBDS
 */

get_header();
?>
<header class="lbds-page-hero">
	<div class="lbds-wrap">
		<h1><?php echo esc_html(get_the_title(get_option('page_for_posts')) ?: __('Artikelen', 'lbds')); ?></h1>
	</div>
</header>
<section class="lbds-section">
	<div class="lbds-wrap">
		<div class="lbds-grid">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : ?>
					<?php the_post(); ?>
					<?php get_template_part('template-parts/content', 'card'); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e('Geen berichten gevonden.', 'lbds'); ?></p>
			<?php endif; ?>
		</div>
		<div style="margin-top:2rem">
			<?php the_posts_pagination(); ?>
		</div>
	</div>
</section>
<?php
get_footer();
