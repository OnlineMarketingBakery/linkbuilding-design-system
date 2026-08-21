<?php
/**
 * Header — Masterblog site-header (Home.html).
 *
 * @package LBDS
 */
$brand = lbds_sanitize_brand_hex(lbds_get_brand()['accent']);
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
<div class="mb-root" style="--brand: <?php echo esc_attr($brand); ?>; --brand-hover: color-mix(in srgb, <?php echo esc_attr($brand); ?> 82%, black); --brand-active: color-mix(in srgb, <?php echo esc_attr($brand); ?> 68%, black); --brand-subtle: color-mix(in srgb, <?php echo esc_attr($brand); ?> 12%, white);">

<div class="site-chrome">
<?php get_template_part('template-parts/topbar'); ?>

<header class="site-header">
	<div class="site-header-in mb-page">
		<a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
			<?php if (has_custom_logo()) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php lbds_site_name(); ?><span>.</span>
			<?php endif; ?>
		</a>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-nav',
				'fallback_cb'    => static function (): void {
					echo '<ul class="primary-nav">';
					echo '<li><a href="' . esc_url(get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/')) . '">' . esc_html__('Artikelen', 'lbds') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/artikelen/')) . '">' . esc_html__('Categorieën', 'lbds') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/partners/')) . '">' . esc_html__('Partners', 'lbds') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/contact/')) . '">' . esc_html__('Contact', 'lbds') . '</a></li>';
					echo '</ul>';
				},
			)
		);
		?>
		<div class="header-actions">
			<a href="<?php echo esc_url(home_url('/?s=')); ?>" aria-label="<?php esc_attr_e('Zoeken', 'lbds'); ?>">
				<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
			</a>
			<a class="btn-cta" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'lbds'); ?></a>
		</div>
	</div>
</header>
</div><!-- .site-chrome -->

<main id="content">
