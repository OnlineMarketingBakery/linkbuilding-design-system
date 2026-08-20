<?php
/**
 * Page.
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
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="lbds-wrap">
			<div class="lbds-prose">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
<?php endwhile; ?>
<?php
get_footer();
