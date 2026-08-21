<?php
/**
 * Template Name: Directory (A–Z)
 * Directory / guide layout — Masterblog Directory.html (no search form).
 *
 * @package LBDS
 */

get_header();

$letters = range('A', 'Z');
$grouped = array();
foreach ($letters as $L) {
	$grouped[ $L ] = array();
}

$q = new WP_Query(
	array(
		'posts_per_page' => 200,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
if ($q->have_posts()) {
	while ($q->have_posts()) {
		$q->the_post();
		$title = get_the_title();
		$first = mb_strtoupper(mb_substr($title, 0, 1));
		if (!isset($grouped[ $first ])) {
			$first = '#';
			if (!isset($grouped['#'])) {
				$grouped['#'] = array();
			}
		}
		$grouped[ $first ][] = array(
			'title' => $title,
			'url'   => get_permalink(),
		);
	}
	wp_reset_postdata();
}
?>
<?php while (have_posts()) : ?>
	<?php the_post(); ?>
	<div class="mb-page">
		<div class="dir-head reveal">
			<span class="kicker"><?php esc_html_e('Gids', 'lbds'); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if (has_excerpt()) : ?>
				<p><?php echo esc_html(get_the_excerpt()); ?></p>
			<?php else : ?>
				<p><?php esc_html_e('Blader alfabetisch door alle items in deze gids.', 'lbds'); ?></p>
			<?php endif; ?>
		</div>

		<nav class="az-nav" aria-label="<?php esc_attr_e('Alfabet', 'lbds'); ?>">
			<?php foreach ($letters as $L) : ?>
				<?php
				$has = !empty($grouped[ $L ]);
				?>
				<a class="az-btn<?php echo $has ? '' : ' disabled'; ?>" href="<?php echo $has ? esc_url('#letter-' . $L) : '#'; ?>" <?php echo $has ? '' : 'aria-disabled="true" tabindex="-1"'; ?>><?php echo esc_html($L); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="az-list">
			<?php foreach ($letters as $L) : ?>
				<?php if (empty($grouped[ $L ])) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<section class="az-row" id="letter-<?php echo esc_attr($L); ?>">
					<div class="az-letter"><?php echo esc_html($L); ?></div>
					<div class="az-items">
						<?php foreach ($grouped[ $L ] as $item) : ?>
							<a class="az-item" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>
		</div>

		<?php if (get_the_content()) : ?>
			<article class="prose" style="margin-bottom:48px;">
				<?php the_content(); ?>
			</article>
		<?php endif; ?>
	</div>
<?php endwhile; ?>
<?php
get_footer();
