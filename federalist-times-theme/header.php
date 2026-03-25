<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content">Skip to content</a>

<!-- Sticky Email Footer -->
<div class="sticky-email" id="stickyEmail" aria-hidden="true">
	<span class="sticky-email__text">Get the Morning Brief — free, daily, no spin.</span>
	<form class="sticky-email__form ft-email-form" data-source="sticky-footer">
		<label for="sticky-email-input" class="sr-only">Email address</label>
		<input type="email" class="sticky-email__input" id="sticky-email-input" placeholder="Your email" required>
		<button type="submit" class="sticky-email__submit">Subscribe</button>
	</form>
	<button class="sticky-email__close" id="stickyEmailClose" aria-label="Close newsletter banner">&times;</button>
</div>

<!-- Subscribe Modal -->
<div class="sub-overlay" id="subOverlay" role="dialog" aria-modal="true" aria-label="Subscribe to newsletter">
	<div class="sub-modal">
		<button class="sub-modal__close" id="subModalClose" aria-label="Close subscribe modal">&times;</button>
		<div id="subFormFields">
			<p class="sub-modal__label">Subscribe</p>
			<h2>Get the Morning Brief</h2>
			<p>The news that matters — sourced, verified, and delivered before the spin begins. Join readers every weekday at 6 AM ET.</p>
			<form class="ft-email-form" data-source="modal">
				<label for="sub-name" class="sr-only">Your name</label>
				<input type="text" class="sub-modal__field" id="sub-name" placeholder="Your name">
				<label for="sub-email" class="sr-only">Email address</label>
				<input type="email" class="sub-modal__field" id="sub-email" placeholder="Your email address" required>
				<button type="submit" class="sub-modal__submit">Subscribe — It's Free</button>
			</form>
			<p class="sub-modal__fine">No spam. No ads. Unsubscribe anytime.</p>
		</div>
		<div class="sub-modal__success" id="subSuccess">
			<h3>You're in.</h3>
			<p>Check your inbox tomorrow morning at 6 AM ET. Welcome to The Federalist Times.</p>
		</div>
	</div>
</div>

<!-- Utility Bar -->
<div class="util" aria-label="Utility bar">
	<div class="util__inner">
		<div class="util__left">
			<span>Roanoke, TX</span>
			<span aria-hidden="true">|</span>
			<span id="util-date"></span>
		</div>
		<div class="util__right">
			<button class="util__btn" id="openSubscribe" type="button">Subscribe</button>
		</div>
	</div>
</div>

<div class="channel">

	<!-- Masthead -->
	<header class="mast" role="banner">
		<div class="mast__bar" aria-hidden="true"></div>
		<div class="mast__row">
			<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>">The Federalist Times</a></h1>
			<button class="nav__hamburger" id="navHamburger" aria-label="Toggle navigation menu" aria-expanded="false">
				<span aria-hidden="true"></span>
			</button>
		</div>
		<p class="mast__sub">&ldquo;A republic, if you can keep it.&rdquo; ~Benjamin Franklin</p>
	</header>

	<!-- Primary Navigation -->
	<nav class="nav" aria-label="Primary navigation">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_id'        => 'navList',
			'fallback_cb'    => 'ft_fallback_nav',
		) );
		?>
	</nav>

	<!-- Ad slot: header leaderboard -->
	<div class="ad-slot ad-slot-leaderboard" data-ad-slot="header-leaderboard" aria-hidden="true"></div>

	<main id="main-content" role="main">
