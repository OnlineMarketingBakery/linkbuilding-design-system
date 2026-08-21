<?php
/**
 * Front page — hero, featured, filterable cards, brand CTA (no split section).
 *
 * @package LBDS
 */

get_header();

$artikelen_url = get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/');

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
			<h1>
				<?php
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
				<a class="btn btn-primary" href="<?php echo esc_url($artikelen_url); ?>">
					<?php esc_html_e('Ontdek de nieuwste artikelen', 'lbds'); ?>
				</a>
				<a class="btn btn-secondary" href="#nieuwste">
					<?php esc_html_e('Bekijk categorieën', 'lbds'); ?>
				</a>
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
						<span><?php echo esc_html(lbds_author_display_name()); ?></span>
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
			<a class="section-link" href="<?php echo esc_url($artikelen_url); ?>">
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
					'posts_per_page' => 9,
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
</div>

<section class="cta-band">
	<div class="mb-page cta-band-in reveal">
		<h2><?php esc_html_e('Klaar om verder te lezen?', 'lbds'); ?></h2>
		<p><?php esc_html_e('Blader door alle artikelen over verbouwen, klussen en wonen.', 'lbds'); ?></p>
		<a class="btn btn-cta-band" href="<?php echo esc_url($artikelen_url); ?>"><?php esc_html_e('Alle artikelen', 'lbds'); ?></a>
	</div>
</section>
<?php
get_footer();
