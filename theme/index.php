<?php
/**
 * Blog index (Artikelen page).
 *
 * @package LBDS
 */

get_header();
?>
<div class="page">
	<?php get_template_part('template-parts/cat-strip'); ?>
	<div class="cat-head">
		<span class="kicker"><?php esc_html_e('Archief', 'lbds'); ?></span>
		<h1><?php echo esc_html(get_the_title(get_option('page_for_posts')) ?: __('Artikelen', 'lbds')); ?></h1>
		<p><?php esc_html_e('Alle artikelen op een rij.', 'lbds'); ?></p>
	</div>
	<div class="cards-grid" style="padding-bottom:48px;">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php get_template_part('template-parts/content', 'card'); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
	<?php the_posts_pagination(); ?>
</div>
<?php
get_footer();
