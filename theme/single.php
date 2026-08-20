<?php
/**
 * Single post.
 *
 * @package LBDS
 */

get_header();
?>
<?php while (have_posts()) : ?>
	<?php the_post(); ?>
	<article <?php post_class(); ?>>
		<header class="lbds-page-hero">
			<div class="lbds-wrap">
				<?php lbds_posted_on(); ?>
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="lbds-wrap">
			<?php if (has_post_thumbnail()) : ?>
				<figure class="lbds-featured"><?php the_post_thumbnail('lbds-hero'); ?></figure>
			<?php endif; ?>
			<div class="lbds-prose">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
<?php endwhile; ?>
<?php
get_footer();
