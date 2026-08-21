<?php
/**
 * Header — Masterblog site-header (category-filled nav).
 *
 * @package LBDS
 */
$brand         = lbds_sanitize_brand_hex(lbds_get_brand()['accent']);
$artikelen_url = get_permalink((int) get_option('page_for_posts')) ?: home_url('/artikelen/');
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
				<?php lbds_site_logo_text(); ?>
			<?php endif; ?>
		</a>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-nav',
				'menu_id'        => 'lbds-primary-nav',
				'fallback_cb'    => static function () use ($artikelen_url): void {
					echo '<ul class="primary-nav" id="lbds-primary-nav">';
					echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'lbds') . '</a></li>';
					foreach (array_slice(lbds_nav_categories(), 0, 6) as $cat) {
						echo '<li><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></li>';
					}
					echo '<li><a href="' . esc_url($artikelen_url) . '">' . esc_html__('Artikelen', 'lbds') . '</a></li>';
					echo '</ul>';
				},
			)
		);
		?>
		<div class="header-actions">
			<a href="<?php echo esc_url(home_url('/?s=')); ?>" aria-label="<?php esc_attr_e('Zoeken', 'lbds'); ?>">
				<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
			</a>
			<a class="btn-cta" href="<?php echo esc_url($artikelen_url); ?>"><?php esc_html_e('Alle artikelen', 'lbds'); ?></a>
			<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="lbds-primary-nav" aria-label="<?php esc_attr_e('Menu', 'lbds'); ?>">
				<span class="nav-toggle-bars" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>
<div class="nav-backdrop" data-nav-backdrop hidden></div>
</div><!-- .site-chrome -->

<main id="content">
