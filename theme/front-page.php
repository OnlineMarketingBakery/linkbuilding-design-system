<?php
/**
 * Front page — Masterblog Home.html (no forms / no cinema hero).
 *
 * @package LBDS
 */

get_header();

$post_count = (int) wp_count_posts()->publish;
$cat_count  = count(lbds_nav_categories());

$featured = new WP_Query(
	array(
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
	)
);
$featured_id = 0;
$tagline     = lbds_tagline();
$name        = get_bloginfo('name');
?>
<div class="mb-page">
	<section class="hero">
		<div class="hero-in reveal">
			<span class="badge-brand">
				<svg viewBox="0 0 24 24"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.6 1.5 6.8L12 17l-6.1 3.4 1.5-6.8L2.2 9l6.9-.7z"/></svg>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: post count */
						_n('%d+ artikel', '%d+ artikelen', max(1, $post_count), 'lbds'),
						max(1, $post_count)
					)
				);
				?>
			</span>
			<h1>
				<?php
				/* Split site name for brand span on last word when possible */
				$parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
				if ($parts && count($parts) > 1) {
					$last = array_pop($parts);
					echo esc_html(implode(' ', $parts) . ' ');
					echo '<span>' . esc_html($last) . '.</span>';
				} else {
					echo esc_html($name);
					echo '<span>.</span>';
				}
				?>
			</h1>
			<p class="dek">
				<?php
				echo $tagline !== ''
					? esc_html($tagline)
					: esc_html__('Praktische artikelen over verbouwen, klussen en wonen — helder, bruikbaar en up-to-date.', 'lbds');
				?>
			</p>
			<div class="hero-actions">
				<a class="btn btn-primary" href="<?php echo esc_url(get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/')); ?>">
					<?php esc_html_e('Ontdek de nieuwste artikelen', 'lbds'); ?>
				</a>
				<a class="btn btn-secondary" href="#nieuwste">
					<?php esc_html_e('Bekijk categorieën', 'lbds'); ?>
				</a>
			</div>
			<div class="stat-row">
				<div class="stat"><b><?php echo esc_html((string) $post_count); ?>+</b><span><?php esc_html_e('Artikelen', 'lbds'); ?></span></div>
				<div class="stat"><b><?php echo esc_html((string) max(1, $cat_count)); ?></b><span><?php esc_html_e('Categorieën', 'lbds'); ?></span></div>
				<div class="stat"><b>NL</b><span><?php esc_html_e('Praktisch', 'lbds'); ?></span></div>
			</div>
		</div>
	</section>

	<?php if ($featured->have_posts()) : ?>
		<?php
		while ($featured->have_posts()) :
			$featured->the_post();
			$featured_id = get_the_ID();
			$cat         = lbds_primary_category();
			?>
			<a class="featured reveal" href="<?php the_permalink(); ?>">
				<div class="ph">
					<?php if (has_post_thumbnail()) : ?>
						<?php the_post_thumbnail('lbds-hero'); ?>
					<?php else : ?>
						<div class="ph-in"><?php echo lbds_placeholder_svg(); // phpcs:ignore ?></div>
					<?php endif; ?>
				</div>
				<div class="featured-body">
					<span class="badge-cat">
						<?php esc_html_e('Uitgelicht', 'lbds'); ?>
						<?php if ($cat) : ?>
							&middot; <?php echo esc_html($cat->name); ?>
						<?php endif; ?>
					</span>
					<h2><?php the_title(); ?></h2>
					<p style="font-size:15px;color:var(--text-muted);font-weight:500;line-height:1.55;">
						<?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?>
					</p>
					<div class="meta">
						<span><?php the_author(); ?></span>
						<span class="dot"></span>
						<span><?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min leestijd', 'lbds'); ?></span>
						<span class="dot"></span>
						<span><?php echo esc_html(get_the_date()); ?></span>
					</div>
				</div>
			</a>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<section class="section" id="nieuwste" data-tabs>
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e('Nieuwste artikelen', 'lbds'); ?></h2>
			<a class="section-link" href="<?php echo esc_url(get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/')); ?>">
				<?php esc_html_e('Alle artikelen', 'lbds'); ?> &rarr;
			</a>
		</div>
		<div class="cat-strip">
			<button type="button" class="active" data-cat="all"><?php esc_html_e('Alles', 'lbds'); ?></button>
			<?php foreach (array_slice(lbds_nav_categories(), 0, 8) as $c) : ?>
				<button type="button" data-cat="<?php echo esc_attr($c->slug); ?>"><?php echo esc_html($c->name); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="cards-grid reveal">
			<?php
			$latest = new WP_Query(
				array(
					'posts_per_page' => 6,
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
					break;
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
							'posts_per_page' => 5,
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
			</aside>
		</div>
	</section>
</div>
<?php
get_footer();
