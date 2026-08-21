<?php
/**
 * Ensure a Contact Form 7 form titled "Contact" exists (Dutch fields).
 * Invoked via: wp eval-file scripts/provision-cf7-wp.php
 *
 * @package LBDS
 */

if (!defined('ABSPATH')) {
	fwrite(STDERR, "Run via wp eval-file from WordPress.\n");
	return;
}

if (!class_exists('WPCF7_ContactForm')) {
	fwrite(STDERR, "Contact Form 7 not active — skip form create.\n");
	return;
}

$existing = get_posts(
	array(
		'post_type'      => 'wpcf7_contact_form',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

foreach ($existing as $post) {
	if ($post->post_title === 'Contact') {
		echo "CF7 form Contact already exists (ID {$post->ID})\n";
		return;
	}
}

$form = WPCF7_ContactForm::get_template(
	array(
		'title' => 'Contact',
	)
);

$properties = $form->get_properties();
$properties['form'] = implode(
	"\n",
	array(
		'<label> Je naam',
		'    [text* your-name autocomplete:name] </label>',
		'',
		'<label> Je e-mailadres',
		'    [email* your-email autocomplete:email] </label>',
		'',
		'<label> Onderwerp',
		'    [text* your-subject] </label>',
		'',
		'<label> Je bericht',
		'    [textarea your-message] </label>',
		'',
		'[submit "Verzenden"]',
	)
);
$properties['mail']['subject'] = '[_site_title] "[your-subject]"';
$properties['mail']['body']    = "Van: [your-name] <[your-email]>\nOnderwerp: [your-subject]\n\nBericht:\n[your-message]\n\n--\nDit bericht is verzonden via een contactformulier op [_site_title] ([_site_url])";
$properties['messages']['mail_sent_ok'] = 'Bedankt voor je bericht. We nemen zo snel mogelijk contact met je op.';

$form->set_properties($properties);
$form->set_title('Contact');
$id = $form->save();

echo "CF7 form Contact created (ID {$id})\n";
