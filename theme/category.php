<?php
/**
 * Category archive — Masterblog Category.html
 *
 * @package LBDS
 */

get_header();

$term = get_queried_object();
?>
<div class="page">
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
			<?php
			// Sibling categories as chips (same parent level = all nav cats)
			foreach (lbds_nav_categories() as $c) :
				if ($term instanceof WP_Term && (int) $c->term_id === (int) $term->term_id) {
					continue;
				}
				?>
				<a class="chip" href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?></a>
			<?php endforeach; ?>
		</div>
		<div class="sort">
			<?php esc_html_e('Sorteren op', 'lbds'); ?>
			<select onchange="if(this.value) location.href=this.value;">
				<option value=""><?php esc_html_e('Nieuwste eerst', 'lbds'); ?></option>
			</select>
		</div>
	</div>

	<div class="cards-grid">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php get_template_part('template-parts/content', 'card'); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e('Geen artikelen in deze categorie.', 'lbds'); ?></p>
		<?php endif; ?>
	</div>

	<?php
	global $wp_query;
	$pagination = paginate_links(
		array(
			'total'     => (int) $wp_query->max_num_pages,
			'current'   => max(1, get_query_var('paged')),
			'type'      => 'array',
			'prev_text' => __('Vorige', 'lbds'),
			'next_text' => __('Volgende', 'lbds'),
		)
	);
	?>
	<?php if ($pagination) : ?>
		<div class="pagination">
			<?php foreach ($pagination as $link) : ?>
				<?php
				$class = 'page-btn';
				if (str_contains($link, 'current')) {
					$class .= ' active';
				}
				if (str_contains($link, 'prev') || str_contains($link, 'next')) {
					$class = 'page-arrow';
				}
				echo '<span class="' . esc_attr($class) . '">' . $link . '</span>'; // phpcs:ignore
				?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
