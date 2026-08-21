<?php
/**
 * List row — Masterblog .list-row
 *
 * @package LBDS
 */
?>
<a class="list-row" href="<?php the_permalink(); ?>">
	<div class="ph">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('lbds-list'); ?>
		<?php else : ?>
			<?php echo lbds_placeholder_svg(); // phpcs:ignore ?>
		<?php endif; ?>
	</div>
	<div>
		<h3><?php the_title(); ?></h3>
		<div class="meta" style="margin-top:6px;">
			<span><?php echo esc_html((string) lbds_reading_time()); ?> <?php esc_html_e('min', 'lbds'); ?></span>
			<span class="dot"></span>
			<span><?php echo esc_html(lbds_author_display_name()); ?></span>
		</div>
	</div>
</a>
