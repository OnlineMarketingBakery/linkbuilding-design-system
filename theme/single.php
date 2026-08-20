<?php
/**
 * Single article — Masterblog Article.html
 *
 * @package LBDS
 */

get_header();
?>
<?php while (have_posts()) : ?>
	<?php
	the_post();
	$cat     = lbds_primary_category();
	$content = (string) get_the_content();
	$content = apply_filters('the_content', $content);
	$content = lbds_content_with_toc_ids($content);
	$toc     = lbds_toc_items((string) get_post_field('post_content', get_the_ID()));
	?>

	<div class="page">
		<div class="crumbs">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'lbds'); ?></a>
			<?php if ($cat) : ?>
				<span class="sep">/</span>
				<a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
			<?php endif; ?>
			<span class="sep">/</span>
			<span class="current"><?php echo esc_html(wp_trim_words(get_the_title(), 8)); ?></span>
		</div>
		<div class="article-head">
			<?php if ($cat) : ?>
				<span class="tag-accent"><?php echo esc_html($cat->name); ?></span>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if (has_excerpt()) : ?>
				<p class="dek"><?php echo esc_html(get_the_excerpt()); ?></p>
			<?php endif; ?>
			<div class="byline">
				<div class="avatar"><?php echo esc_html(mb_substr(get_the_author(), 0, 1)); ?></div>
				<div>
					<div class="byline-name"><?php the_author(); ?></div>
					<div class="byline-meta">
						<?php echo esc_html(get_the_date()); ?>
						&middot;
						<?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min leestijd', 'lbds'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if (has_post_thumbnail()) : ?>
		<div class="hero-img"><?php the_post_thumbnail('lbds-hero'); ?></div>
	<?php else : ?>
		<div class="hero-img"><?php echo lbds_placeholder_svg(); // phpcs:ignore ?></div>
	<?php endif; ?>

	<div class="page">
		<div class="article-body-wrap">
			<aside class="toc">
				<?php if ($toc) : ?>
					<div class="toc-title"><?php esc_html_e('In dit artikel', 'lbds'); ?></div>
					<ol>
						<?php foreach ($toc as $i => $item) : ?>
							<li class="<?php echo 0 === $i ? 'active' : ''; ?>">
								<a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['text']); ?></a>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</aside>

			<article class="prose">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</article>

			<aside>
				<div class="share-rail" aria-hidden="true">
					<div class="share-icon"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="2.4"/><circle cx="6" cy="12" r="2.4"/><circle cx="18" cy="19" r="2.4"/><path d="M8.2 10.8l7.6-4.4M8.2 13.2l7.6 4.4"/></svg></div>
				</div>
				<div class="author-card">
					<div class="byline">
						<div class="avatar"><?php echo esc_html(mb_substr(get_the_author(), 0, 1)); ?></div>
						<div>
							<div class="byline-name">
								<a href="<?php echo esc_url(get_author_posts_url((int) get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a>
							</div>
						</div>
					</div>
					<?php if (get_the_author_meta('description')) : ?>
						<p><?php echo esc_html(get_the_author_meta('description')); ?></p>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</div>

	<div class="page" style="padding:0 56px 8px;">
		<div class="prevnext">
			<?php
			$prev = get_adjacent_post(false, '', true);
			$next = get_adjacent_post(false, '', false);
			?>
			<div class="pn-item">
				<?php if ($prev) : ?>
					<a href="<?php echo esc_url(get_permalink($prev)); ?>">
						<div class="pn-label"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg><?php esc_html_e('Vorige', 'lbds'); ?></div>
						<div class="pn-title"><?php echo esc_html(get_the_title($prev)); ?></div>
					</a>
				<?php endif; ?>
			</div>
			<div class="pn-item next">
				<?php if ($next) : ?>
					<a href="<?php echo esc_url(get_permalink($next)); ?>">
						<div class="pn-label"><?php esc_html_e('Volgende', 'lbds'); ?><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></div>
						<div class="pn-title"><?php echo esc_html(get_the_title($next)); ?></div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ($cat) : ?>
		<div class="related-section page">
			<h2 class="section-title"><?php echo esc_html(sprintf(__('Meer over %s', 'lbds'), $cat->name)); ?></h2>
			<div class="related-grid">
				<?php
				$related = new WP_Query(
					array(
						'posts_per_page' => 3,
						'cat'            => (int) $cat->term_id,
						'post__not_in'   => array(get_the_ID()),
						'post_status'    => 'publish',
					)
				);
				if ($related->have_posts()) :
					while ($related->have_posts()) :
						$related->the_post();
						get_template_part('template-parts/content', 'card');
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php get_template_part('template-parts/newsletter', null, array('variant' => 'wide')); ?>

<?php endwhile; ?>
<?php
get_footer();
