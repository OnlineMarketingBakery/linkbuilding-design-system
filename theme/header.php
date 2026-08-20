<?php
/**
 * Header.
 *
 * @package LBDS
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="lbds-skip" href="#content"><?php esc_html_e('Skip to content', 'lbds'); ?></a>
<header class="lbds-header">
	<div class="lbds-wrap lbds-header__inner">
		<a class="lbds-brand" href="<?php echo esc_url(home_url('/')); ?>">
			<?php if (has_custom_logo()) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="lbds-brand__mark" aria-hidden="true"></span>
				<span class="lbds-brand__name"><?php lbds_site_name(); ?></span>
			<?php endif; ?>
		</a>
		<button class="lbds-nav-toggle" type="button" aria-expanded="false" aria-controls="lbds-primary-nav">
			<?php esc_html_e('Menu', 'lbds'); ?>
		</button>
		<nav id="lbds-primary-nav" aria-label="<?php esc_attr_e('Primary', 'lbds'); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'lbds-nav',
					'fallback_cb'    => static function (): void {
						echo '<ul class="lbds-nav">';
						echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'lbds') . '</a></li>';
						echo '<li><a href="' . esc_url(home_url('/blog/')) . '">' . esc_html__('Blog', 'lbds') . '</a></li>';
						echo '<li><a href="' . esc_url(home_url('/contact/')) . '">' . esc_html__('Contact', 'lbds') . '</a></li>';
						echo '</ul>';
					},
				)
			);
			?>
		</nav>
	</div>
</header>
<main id="content">
