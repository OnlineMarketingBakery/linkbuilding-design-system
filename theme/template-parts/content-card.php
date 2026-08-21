<?php
/**
 * Article card — Masterblog .card
 *
 * @package LBDS
 */
$cat  = lbds_primary_category();
$slug = $cat ? $cat->slug : '';
?>
<article <?php post_class('card'); ?> <?php echo $slug ? 'data-cat="' . esc_attr($slug) . '"' : ''; ?>>
	<a class="ph" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('lbds-card'); ?>
		<?php else : ?>
			<span class="ph-in"><?php echo lbds_placeholder_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
	</a>
	<?php if ($cat) : ?>
		<a class="badge-cat" href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<p class="excerpt"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
	<div class="meta">
		<span><?php echo esc_html(lbds_author_display_name()); ?></span>
		<span class="dot"></span>
		<span><?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min', 'lbds'); ?></span>
	</div>
</article>
