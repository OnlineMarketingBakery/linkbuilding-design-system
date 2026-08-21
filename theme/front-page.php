<?php
/**
 * Front page — Masterblog with cinematic contrast hero.
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
<div class="mb-page">
	<?php get_template_part('template-parts/cat-strip'); ?>
</div>

<?php if ($featured->have_posts()) : ?>
	<?php while ($featured->have_posts()) : ?>
		<?php
		$featured->the_post();
		$featured_id = get_the_ID();
		$cat         = lbds_primary_category();
		$bg          = get_the_post_thumbnail_url(get_the_ID(), 'lbds-hero');
		$style       = $bg ? ' style="--hero-bg:url(\'' . esc_url($bg) . '\')"' : '';
		?>
		<section class="hero-cinema reveal"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="hero-cinema__media" aria-hidden="true"></div>
			<div class="hero-cinema__shade" aria-hidden="true"></div>
			<div class="mb-page hero-cinema__inner">
				<?php if ($cat) : ?>
					<span class="tag-accent tag-accent--on-dark"><?php esc_html_e('Uitgelicht', 'lbds'); ?> &middot; <?php echo esc_html($cat->name); ?></span>
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
				<a class="hero-cinema__cta" href="<?php the_permalink(); ?>"><?php esc_html_e('Lees artikel', 'lbds'); ?> &rarr;</a>
			</div>
		</section>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>

<section class="section section-band section-band--surface">
	<div class="mb-page">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e('Nieuwste artikelen', 'lbds'); ?></h2>
			<a class="section-link" href="<?php echo esc_url(get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/')); ?>">
				<?php esc_html_e('Alle artikelen', 'lbds'); ?> &rarr;
			</a>
		</div>
		<div class="cards-grid reveal">
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
	</div>
</section>

<section class="section section-band section-band--warm">
	<div class="mb-page">
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
				<div class="sidebar-block sidebar-panel">
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
	</div>
</section>
<?php
get_footer();
