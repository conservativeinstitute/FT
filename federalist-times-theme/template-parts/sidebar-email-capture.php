<?php
/**
 * Sidebar email capture widget partial
 *
 * @package FederalistTimes
 */
?>
<div class="subscribe-box" style="margin-bottom:1.5rem;">
	<p class="subscribe-box__label">Newsletter</p>
	<h3>The Morning Brief</h3>
	<p class="subscribe-box__desc">The news that matters, before the spin begins. Every weekday at 6 AM ET.</p>
	<form class="subscribe-box__row ft-email-form" data-source="sidebar">
		<label for="sidebar-email" class="sr-only">Email address</label>
		<input type="email" class="subscribe-box__input" id="sidebar-email" placeholder="Your email address" required>
		<button type="submit" class="subscribe-box__go">Subscribe</button>
	</form>
	<p class="subscribe-box__fine">Free. No spam. Unsubscribe anytime.</p>
</div>
