<?php
/**
 * Template Name: Unsubscribe
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="inner-page">
	<div class="inner-page__hero">
		<p class="sect-label">Manage Subscription</p>
		<h1>Unsubscribe</h1>
		<p class="lead-p">We're sorry to see you go. If you'd like to stop receiving emails from The Federalist Times, you can unsubscribe below.</p>
	</div>

	<p>Enter the email address you'd like to unsubscribe. You will be removed from all Federalist Times mailing lists within 24 hours. If you only want to adjust which newsletters you receive rather than unsubscribe entirely, please use the "Manage Preferences" link at the bottom of any email we've sent you.</p>

	<?php echo do_shortcode( '[le-unsubscribe]' ); ?>

	<p>If you have questions about your subscription or are experiencing issues, please <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--gold);text-decoration:underline;cursor:pointer;">contact our support team</a>. We're here to help.</p>
</div>

<?php get_footer(); ?>
