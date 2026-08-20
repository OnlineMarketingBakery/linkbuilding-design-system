<?php
/**
 * Generic archive fallback → category-like layout.
 *
 * @package LBDS
 */

get_header();
?>
<div class="page">
	<div class="cat-head">
		<span class="kicker"><?php esc_html_e('Archief', 'lbds'); ?></span>
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description('<p>', '</p>'); ?>
	</div>
	<div class="cards-grid" style="padding-bottom:48px;">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php get_template_part('template-parts/content', 'card'); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
