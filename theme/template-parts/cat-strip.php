<?php
/**
 * Category strip — real verbouwingaanhuis categories.
 *
 * @package LBDS
 */

$cats   = lbds_nav_categories();
$active = is_category() ? get_queried_object_id() : 0;
?>
<div class="cat-strip">
	<a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo !is_category() && is_front_page() ? 'active' : ''; ?>"><?php esc_html_e('Alles', 'lbds'); ?></a>
	<?php foreach ($cats as $cat) : ?>
		<a href="<?php echo esc_url(get_term_link($cat)); ?>" class="<?php echo (int) $cat->term_id === (int) $active ? 'active' : ''; ?>">
			<?php echo esc_html($cat->name); ?>
		</a>
	<?php endforeach; ?>
</div>
