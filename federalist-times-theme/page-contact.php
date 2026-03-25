<?php
/**
 * Template Name: Contact Page
 *
 * @package FederalistTimes
 */

get_header();

// FAQPage JSON-LD
$faqs = array(
	array( 'q' => 'How do I submit a news tip?', 'a' => 'Email tips@federalisttimes.com with as much detail as possible. We protect sources and take confidentiality seriously. You can also use our contact form on this page.' ),
	array( 'q' => 'How do I report an error in an article?', 'a' => 'Email corrections@federalisttimes.com with the article URL, the specific claim you believe is inaccurate, and any supporting evidence. We review every submission and respond within 48 hours.' ),
	array( 'q' => 'How do I submit an opinion piece?', 'a' => 'Send your pitch or completed piece to opinion@federalisttimes.com. We accept submissions of 800 to 1,500 words on topics relevant to our coverage areas. Please include a brief author bio.' ),
	array( 'q' => 'How do I advertise with The Federalist Times?', 'a' => 'Contact our advertising team at advertising@federalisttimes.com or visit our Advertise page for rate cards, audience data, and available formats.' ),
	array( 'q' => 'How do I cancel my newsletter subscription?', 'a' => 'Click the unsubscribe link at the bottom of any email, or visit our Unsubscribe page to remove your email from all mailing lists within 24 hours.' ),
	array( 'q' => 'What is your response time for inquiries?', 'a' => 'We aim to respond to all substantive inquiries within two business days. Advertising inquiries are typically answered within one business day.' ),
);

$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $faqs as $faq ) {
	$faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $faq['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $faq['a'],
		),
	);
}
echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>';
?>

<div class="inner-page">
	<div class="inner-page__hero">
		<p class="sect-label">Contact Us</p>
		<h1>We Read Every Letter</h1>
		<p class="lead-p">Whether you have a tip, a correction, a subscription question, or simply a view you'd like us to consider, we want to hear from you. A member of our staff responds to every substantive inquiry.</p>
	</div>

	<div class="contact-grid">
		<div class="contact-info">
			<h3>Editorial</h3>
			<p>For news tips, corrections, letters to the editor, and story pitches, contact our editorial desk. We protect sources and take confidentiality seriously.</p>

			<div class="contact-dept">
				<p class="contact-dept__name">News Tips &amp; Secure Contact</p>
				<p class="contact-dept__email"><a href="mailto:tips@federalisttimes.com">tips@federalisttimes.com</a></p>
			</div>
			<div class="contact-dept">
				<p class="contact-dept__name">Letters to the Editor</p>
				<p class="contact-dept__email"><a href="mailto:letters@federalisttimes.com">letters@federalisttimes.com</a></p>
			</div>
			<div class="contact-dept">
				<p class="contact-dept__name">Corrections</p>
				<p class="contact-dept__email"><a href="mailto:corrections@federalisttimes.com">corrections@federalisttimes.com</a></p>
			</div>
			<div class="contact-dept">
				<p class="contact-dept__name">Opinion Submissions</p>
				<p class="contact-dept__email"><a href="mailto:opinion@federalisttimes.com">opinion@federalisttimes.com</a></p>
			</div>

			<h3 style="margin-top:2.5rem;">Business</h3>
			<p>For subscription support, advertising inquiries, licensing, and reprint permissions.</p>

			<div class="contact-dept">
				<p class="contact-dept__name">Subscriptions &amp; Customer Service</p>
				<p class="contact-dept__email"><a href="mailto:support@federalisttimes.com">support@federalisttimes.com</a></p>
			</div>
			<div class="contact-dept">
				<p class="contact-dept__name">Advertising</p>
				<p class="contact-dept__email"><a href="mailto:advertising@federalisttimes.com">advertising@federalisttimes.com</a></p>
			</div>

			<h3 style="margin-top:2.5rem;">Mailing Address</h3>
			<p>Conservative Institute, LLC<br>1328 Highpoint Way<br>Roanoke, TX 76262</p>
		</div>
		<div>
			<h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;margin-bottom:1.2rem;">Send a Message</h3>
			<!-- Contact Form 7 shortcode placeholder -->
			<p style="font-size:.85rem;color:var(--ink-3);margin-bottom:1rem;">This form requires the Contact Form 7 plugin. Place your shortcode below:</p>
			<?php echo do_shortcode( '[contact-form-7 id="contact-form" title="Contact Form"]' ); ?>
		</div>
	</div>

	<h2>Frequently Asked Questions</h2>
	<?php foreach ( $faqs as $faq ) : ?>
		<h3><?php echo esc_html( $faq['q'] ); ?></h3>
		<p><?php echo esc_html( $faq['a'] ); ?></p>
	<?php endforeach; ?>
</div>

<?php get_footer(); ?>
