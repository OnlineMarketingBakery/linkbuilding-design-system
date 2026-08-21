<?php
/**
 * Blog index (Artikelen page) — listing with sort + pagination.
 *
 * @package LBDS
 */

get_header();
?>
<div class="mb-page">
	<?php get_template_part('template-parts/cat-strip'); ?>
	<div class="cat-head">
		<span class="kicker"><?php esc_html_e('Archief', 'lbds'); ?></span>
		<h1><?php echo esc_html(get_the_title(get_option('page_for_posts')) ?: __('Artikelen', 'lbds')); ?></h1>
		<p><?php esc_html_e('Alle artikelen op een rij.', 'lbds'); ?></p>
	</div>

	<div class="toolbar">
		<div class="filters">
			<span class="chip active"><?php esc_html_e('Alles', 'lbds'); ?></span>
			<?php foreach (lbds_nav_categories() as $c) : ?>
				<a class="chip" href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?></a>
			<?php endforeach; ?>
		</div>
		<?php lbds_the_sort_select(); ?>
	</div>

	<div class="cards-grid reveal" style="padding-bottom:24px;">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php get_template_part('template-parts/content', 'card'); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>

	<?php lbds_the_pagination(); ?>
</div>
<?php
get_footer();
