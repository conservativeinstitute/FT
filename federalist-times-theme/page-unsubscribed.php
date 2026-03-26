<?php
/**
 * Template Name: Unsubscribed
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="inner-page">
	<div class="inner-page__hero">
		<p class="sect-label">Subscription Updated</p>
		<h1>You've Been Unsubscribed</h1>
		<p class="lead-p">Your email address has been removed from all Federalist Times mailing lists.</p>
	</div>

	<p>You will no longer receive newsletters, breaking news alerts, or promotional emails from The Federalist Times. Please allow up to 24 hours for all communications to fully stop.</p>

	<p>If you unsubscribed by mistake, you can <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--gold);text-decoration:underline;">return to the homepage</a> and resubscribe using any of our newsletter forms.</p>

	<div style="margin:2.5rem 0;padding:1.8rem;background:var(--bg-warm);border-left:3px solid var(--gold);">
		<p style="font-family:'Lato',sans-serif;font-size:.82rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;">Before you go</p>
		<p style="margin:0;">You can still visit The Federalist Times anytime for the latest news and analysis — no subscription required. We hope to see you again.</p>
	</div>

	<p>If you have questions or need further assistance, please <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--gold);text-decoration:underline;">contact our support team</a>.</p>
</div>

<?php get_footer(); ?>
