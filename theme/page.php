<?php
/**
 * Page — Masterblog utility/about-style content.
 *
 * @package LBDS
 */

get_header();
?>
<?php while (have_posts()) : ?>
	<?php the_post(); ?>
	<div class="mb-page">
		<div class="crumbs" style="padding-top:20px;">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'lbds'); ?></a>
			<span class="sep">/</span>
			<span class="current"><?php the_title(); ?></span>
		</div>
		<div class="cat-head">
			<h1><?php the_title(); ?></h1>
		</div>
		<article class="prose" style="max-width:720px;margin:8px 0 64px;font-size:18px;line-height:1.72;">
			<?php the_content(); ?>
		</article>
	</div>
<?php endwhile; ?>
<?php
get_footer();
