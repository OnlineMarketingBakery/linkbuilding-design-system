<?php
/**
 * Category archive — Masterblog Category.html
 *
 * @package LBDS
 */

get_header();

$term = get_queried_object();
?>
<div class="mb-page">
	<div class="crumbs">
		<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'lbds'); ?></a>
		<span class="sep">/</span>
		<span class="current"><?php single_cat_title(); ?></span>
	</div>

	<div class="cat-head">
		<span class="kicker"><?php esc_html_e('Categorie', 'lbds'); ?></span>
		<h1><?php single_cat_title(); ?></h1>
		<?php if ($term instanceof WP_Term && $term->description) : ?>
			<p><?php echo esc_html($term->description); ?></p>
		<?php else : ?>
			<p><?php echo esc_html(sprintf(__('Alle artikelen in %s.', 'lbds'), single_cat_title('', false))); ?></p>
		<?php endif; ?>
	</div>

	<div class="toolbar">
		<div class="filters">
			<span class="chip active">
				<?php
				$count = ($term instanceof WP_Term) ? (int) $term->count : 0;
				echo esc_html(sprintf(__('Alles · %d', 'lbds'), $count));
				?>
			</span>
			<?php foreach (lbds_nav_categories() as $c) : ?>
				<?php
				if ($term instanceof WP_Term && (int) $c->term_id === (int) $term->term_id) {
					continue;
				}
				?>
				<a class="chip" href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?></a>
			<?php endforeach; ?>
		</div>
		<?php lbds_the_sort_select(); ?>
	</div>

	<div class="cards-grid reveal">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php get_template_part('template-parts/content', 'card'); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e('Geen artikelen in deze categorie.', 'lbds'); ?></p>
		<?php endif; ?>
	</div>

	<?php lbds_the_pagination(); ?>
</div>
<?php
get_footer();
