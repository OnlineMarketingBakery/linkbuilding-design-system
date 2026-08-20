<?php
/**
 * Front page — Masterblog Home.html
 *
 * @package LBDS
 */

get_header();

$featured = new WP_Query(
	array(
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
	)
);
$featured_id = 0;
?>
<div class="page">
	<?php get_template_part('template-parts/cat-strip'); ?>

	<?php if ($featured->have_posts()) : ?>
		<?php while ($featured->have_posts()) : ?>
			<?php
			$featured->the_post();
			$featured_id = get_the_ID();
			$cat         = lbds_primary_category();
			?>
			<section class="hero">
				<a class="ph" href="<?php the_permalink(); ?>">
					<?php if (has_post_thumbnail()) : ?>
						<?php the_post_thumbnail('lbds-hero'); ?>
					<?php else : ?>
						<?php echo lbds_placeholder_svg(); // phpcs:ignore ?>
					<?php endif; ?>
				</a>
				<div>
					<?php if ($cat) : ?>
						<span class="tag-accent"><?php esc_html_e('Uitgelicht', 'lbds'); ?> &middot; <?php echo esc_html($cat->name); ?></span>
					<?php endif; ?>
					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
					<p class="dek"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
					<div class="meta">
						<span><?php the_author(); ?></span>
						<span class="dot"></span>
						<span><?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min leestijd', 'lbds'); ?></span>
						<span class="dot"></span>
						<span><?php echo esc_html(get_the_date()); ?></span>
					</div>
				</div>
			</section>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<section class="section">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e('Nieuwste artikelen', 'lbds'); ?></h2>
			<a class="section-link" href="<?php echo esc_url(get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/')); ?>">
				<?php esc_html_e('Alle artikelen', 'lbds'); ?> &rarr;
			</a>
		</div>
		<div class="cards-grid">
			<?php
			$latest = new WP_Query(
				array(
					'posts_per_page' => 3,
					'post_status'    => 'publish',
					'post__not_in'   => $featured_id ? array($featured_id) : array(),
				)
			);
			if ($latest->have_posts()) :
				while ($latest->have_posts()) :
					$latest->the_post();
					get_template_part('template-parts/content', 'card');
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</section>

	<section class="section">
		<div class="split">
			<div>
				<?php
				$spotlight = null;
				foreach (lbds_nav_categories() as $c) {
					$spotlight = $c;
					break; // highest count (Wonen)
				}
				?>
				<?php if ($spotlight) : ?>
					<div class="section-head">
						<h2 class="section-title"><?php echo esc_html(sprintf(__('Uit de categorie %s', 'lbds'), $spotlight->name)); ?></h2>
						<a class="section-link" href="<?php echo esc_url(get_term_link($spotlight)); ?>">
							<?php esc_html_e('Bekijk categorie', 'lbds'); ?> &rarr;
						</a>
					</div>
					<?php
					$cat_q = new WP_Query(
						array(
							'posts_per_page' => 3,
							'cat'            => (int) $spotlight->term_id,
							'post_status'    => 'publish',
							'post__not_in'   => $featured_id ? array($featured_id) : array(),
						)
					);
					if ($cat_q->have_posts()) :
						while ($cat_q->have_posts()) :
							$cat_q->the_post();
							get_template_part('template-parts/content', 'list-row');
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				<?php endif; ?>
			</div>
			<aside>
				<div class="sidebar-block">
					<div class="sidebar-title"><?php esc_html_e('Meest gelezen', 'lbds'); ?></div>
					<?php
					$popular = new WP_Query(
						array(
							'posts_per_page' => 3,
							'post_status'    => 'publish',
							'orderby'        => 'comment_count',
							'order'          => 'DESC',
						)
					);
					$n = 1;
					if ($popular->have_posts()) :
						while ($popular->have_posts()) :
							$popular->the_post();
							?>
							<div class="popular-row">
								<span class="num"><?php echo esc_html(str_pad((string) $n, 2, '0', STR_PAD_LEFT)); ?></span>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</div>
							<?php
							$n++;
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
				<?php get_template_part('template-parts/newsletter', null, array('variant' => 'sidebar')); ?>
			</aside>
		</div>
	</section>
</div>
<?php
get_footer();
