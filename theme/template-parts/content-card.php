<?php
/**
 * Card partial.
 *
 * @package LBDS
 */
?>
<article <?php post_class('lbds-card'); ?>>
	<a class="lbds-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('lbds-card'); ?>
		<?php endif; ?>
	</a>
	<div class="lbds-card__body">
		<?php lbds_posted_on(); ?>
		<h2 class="lbds-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="lbds-card__excerpt"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
	</div>
</article>
