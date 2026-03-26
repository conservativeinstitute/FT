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

	<?php
	$leunsub_endpoint = get_option( 'leunsub_endpoint_url', '' );
	$leunsub_redirect = get_option( 'leunsub_redirect_url', '' );

	// Handle form submission server-side
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['leunsub_email'] ) && ! empty( $leunsub_endpoint ) ) {
		$unsub_email = sanitize_email( wp_unslash( $_POST['leunsub_email'] ) );
		if ( is_email( $unsub_email ) ) {
			wp_remote_post( $leunsub_endpoint, array( 'body' => array( 'email' => $unsub_email ) ) );
			if ( ! empty( $leunsub_redirect ) ) {
				wp_redirect( $leunsub_redirect );
				exit;
			}
		}
	}
	?>

	<div style="max-width:500px;margin:2rem 0;">
		<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" style="display:flex;">
			<label for="unsub-email" class="sr-only">Email Address</label>
			<input type="email" id="unsub-email" name="leunsub_email" placeholder="you@example.com" required style="flex:1;font-family:'Libre Baskerville',serif;font-size:.9rem;padding:.7rem .9rem;border:1px solid var(--rule);border-right:none;background:var(--white);outline:none;">
			<button type="submit" style="font-family:'Lato',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.7rem 1.5rem;background:var(--ink);color:var(--bg-content);border:1px solid var(--ink);cursor:pointer;">Unsubscribe</button>
		</form>
	</div>

	<p>If you have questions about your subscription or are experiencing issues, please <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--gold);text-decoration:underline;cursor:pointer;">contact our support team</a>. We're here to help.</p>
</div>

<?php get_footer(); ?>
