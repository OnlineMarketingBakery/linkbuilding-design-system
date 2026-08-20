<?php
/**
 * Archives.
 *
 * @package LBDS
 */

get_header();
?>
<header class="lbds-page-hero">
	<div class="lbds-wrap">
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description('<div class="lbds-hero__lead">', '</div>'); ?>
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
			<?php endif; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</section>
<?php
get_footer();
