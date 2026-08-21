<?php
/**
 * Template Name: Business (local)
 * Local-business layout — Masterblog Business.html (no contact form).
 *
 * @package LBDS
 */

get_header();
?>
<?php while (have_posts()) : ?>
	<?php the_post(); ?>
	<div class="mb-page">
		<section class="hero">
			<div class="hero-in reveal">
				<span class="badge-cat"><?php esc_html_e('Lokaal', 'lbds'); ?></span>
				<h1><?php the_title(); ?></h1>
				<?php if (has_excerpt()) : ?>
					<p class="dek"><?php echo esc_html(get_the_excerpt()); ?></p>
				<?php endif; ?>
				<div class="hero-actions">
					<a class="btn btn-primary" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Neem contact op', 'lbds'); ?></a>
					<a class="btn btn-secondary" href="#diensten"><?php esc_html_e('Bekijk diensten', 'lbds'); ?></a>
				</div>
			</div>
		</section>

		<section class="section" id="diensten">
			<div class="section-head">
				<h2 class="section-title"><?php esc_html_e('Diensten', 'lbds'); ?></h2>
			</div>
			<div class="services-grid">
				<?php
				$services = array(
					array(__('Intake & advies', 'lbds'), __('Persoonlijk gesprek over jouw situatie en doelen.', 'lbds')),
					array(__('Behandeling', 'lbds'), __('Praktische aanpak op maat, met duidelijke vervolgstappen.', 'lbds')),
					array(__('Nazorg', 'lbds'), __('Begeleiding na afloop zodat resultaat blijft.', 'lbds')),
				);
				foreach ($services as $s) :
					?>
					<div class="service reveal">
						<div class="icon" aria-hidden="true">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/></svg>
						</div>
						<h3><?php echo esc_html($s[0]); ?></h3>
						<p><?php echo esc_html($s[1]); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="section">
			<div class="section-head">
				<h2 class="section-title"><?php esc_html_e('Wat klanten zeggen', 'lbds'); ?></h2>
			</div>
			<div class="testimonial-row">
				<div class="testimonial">
					<div class="stars" aria-hidden="true">★★★★★</div>
					<p><?php esc_html_e('Heldere uitleg en een plan dat echt werkt. Aanrader.', 'lbds'); ?></p>
					<div class="t-name"><?php esc_html_e('Anoniem', 'lbds'); ?></div>
					<div class="t-role"><?php esc_html_e('Klant', 'lbds'); ?></div>
				</div>
				<div class="testimonial">
					<div class="stars" aria-hidden="true">★★★★★</div>
					<p><?php esc_html_e('Fijne begeleiding en snelle resultaten. Zeer tevreden.', 'lbds'); ?></p>
					<div class="t-name"><?php esc_html_e('Anoniem', 'lbds'); ?></div>
					<div class="t-role"><?php esc_html_e('Klant', 'lbds'); ?></div>
				</div>
			</div>
		</section>

		<div class="split-cta reveal">
			<div>
				<h2><?php esc_html_e('Klaar om te starten?', 'lbds'); ?></h2>
				<p><?php esc_html_e('Neem contact op voor een vrijblijvende intake. We denken graag met je mee.', 'lbds'); ?></p>
			</div>
			<div>
				<a class="btn btn-primary" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'lbds'); ?></a>
			</div>
		</div>

		<article class="prose" style="margin-bottom:64px;">
			<?php the_content(); ?>
		</article>
	</div>
<?php endwhile; ?>
<?php
get_footer();
