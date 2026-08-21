<?php
/**
 * Author — Masterblog AuthorPage.html
 *
 * @package LBDS
 */

get_header();
$author = get_queried_object();
?>
<div class="mb-page">
	<div class="profile">
		<div class="avatar-lg"><?php echo esc_html(mb_substr(get_the_author_meta('display_name', $author->ID ?? 0), 0, 1)); ?></div>
		<div>
			<span class="kicker"><?php esc_html_e('Auteur', 'lbds'); ?></span>
			<h1><?php the_author(); ?></h1>
			<div class="role"><?php echo esc_html(get_the_author_meta('nickname')); ?></div>
			<?php if (get_the_author_meta('description')) : ?>
				<p class="bio"><?php echo esc_html(get_the_author_meta('description')); ?></p>
			<?php endif; ?>
			<div class="stat-row">
				<div class="stat">
					<b><?php echo esc_html((string) count_user_posts((int) get_the_author_meta('ID'))); ?></b>
					<span><?php esc_html_e('Artikelen', 'lbds'); ?></span>
				</div>
			</div>
		</div>
	</div>

	<section class="section">
		<div class="section-head">
			<h2 class="section-title"><?php echo esc_html(sprintf(__('Artikelen van %s', 'lbds'), get_the_author())); ?></h2>
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
