<?php
/**
 * Front page.
 *
 * @package LBDS
 */

get_header();
?>
<section class="lbds-hero">
	<div class="lbds-wrap">
		<span class="lbds-hero__eyebrow"><?php esc_html_e('Gids & inspiratie', 'lbds'); ?></span>
		<h1 class="lbds-hero__title"><?php lbds_site_name(); ?></h1>
		<p class="lbds-hero__lead">
			<?php
			$tagline = lbds_tagline();
			echo $tagline !== ''
				? esc_html($tagline)
				: esc_html__('Duidelijke artikelen die je verder helpen — van eerste idee tot uitvoering.', 'lbds');
			?>
		</p>
		<div class="lbds-actions">
			<a class="lbds-btn lbds-btn--primary" href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>">
				<?php esc_html_e('Bekijk artikelen', 'lbds'); ?>
			</a>
			<a class="lbds-btn lbds-btn--ghost" href="<?php echo esc_url(home_url('/contact/')); ?>">
				<?php esc_html_e('Contact', 'lbds'); ?>
			</a>
		</div>
	</div>
</section>

<section class="lbds-section">
	<div class="lbds-wrap">
		<div class="lbds-section__head">
			<h2 class="lbds-section__title"><?php esc_html_e('Laatste artikelen', 'lbds'); ?></h2>
		</div>
		<div class="lbds-grid">
			<?php
			$q = new WP_Query(
				array(
					'posts_per_page' => 6,
					'post_status'    => 'publish',
					'ignore_sticky_posts' => true,
				)
			);
			if ($q->have_posts()) :
				while ($q->have_posts()) :
					$q->the_post();
					get_template_part('template-parts/content', 'card');
				endwhile;
				wp_reset_postdata();
			else :
				echo '<p>' . esc_html__('Nog geen artikelen.', 'lbds') . '</p>';
			endif;
			?>
		</div>
	</div>
</section>
<?php
get_footer();
