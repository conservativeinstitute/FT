/**
 * Federalist Times — Main JavaScript
 */
(function () {
	'use strict';

	/* ── Utility: date display ── */
	var utilDate = document.getElementById('util-date');
	if (utilDate) {
		var d = new Date();
		utilDate.textContent = d.toLocaleDateString('en-US', {
			weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
		});
	}

	/* ── Mobile hamburger toggle ── */
	var hamburger = document.getElementById('navHamburger');
	var navList = document.querySelector('.nav ul');

	if (hamburger && navList) {
		hamburger.addEventListener('click', function () {
			var isOpen = navList.classList.toggle('nav-open');
			hamburger.classList.toggle('open');
			hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});

		// Close nav when a link is clicked on mobile
		navList.addEventListener('click', function (e) {
			var link = e.target.closest('a');
			if (link && !link.closest('.subnav')) {
				navList.classList.remove('nav-open');
				hamburger.classList.remove('open');
				hamburger.setAttribute('aria-expanded', 'false');
			}
		});

		// Mobile subnav toggle
		navList.addEventListener('click', function (e) {
			if (window.innerWidth > 768) return;
			var topLink = e.target.closest('.nav > ul > li > a');
			if (topLink && topLink.nextElementSibling && topLink.nextElementSibling.classList.contains('sub-menu')) {
				e.preventDefault();
				topLink.parentElement.classList.toggle('subnav-open');
			}
		});
	}

	/* ── Sticky email bar ── */
	var stickyEmail = document.getElementById('stickyEmail');
	var stickyClose = document.getElementById('stickyEmailClose');
	var stickyDismissed = false;

	if (stickyClose) {
		stickyClose.addEventListener('click', function () {
			stickyEmail.classList.remove('visible');
			stickyDismissed = true;
		});
	}

	window.addEventListener('scroll', function () {
		if (stickyDismissed || !stickyEmail) return;
		if (window.scrollY > 600) {
			stickyEmail.classList.add('visible');
		} else {
			stickyEmail.classList.remove('visible');
		}
	});

	/* ── Subscribe modal ── */
	var subOverlay = document.getElementById('subOverlay');
	var openSubBtn = document.getElementById('openSubscribe');
	var closeSubBtn = document.getElementById('subModalClose');

	function openSubscribe() {
		if (subOverlay) subOverlay.classList.add('open');
	}

	function closeSubscribe() {
		if (subOverlay) subOverlay.classList.remove('open');
	}

	if (openSubBtn) openSubBtn.addEventListener('click', openSubscribe);
	if (closeSubBtn) closeSubBtn.addEventListener('click', closeSubscribe);
	if (subOverlay) {
		subOverlay.addEventListener('click', function (e) {
			if (e.target === subOverlay) closeSubscribe();
		});
	}

	/* ── Read Next popup (exit intent / scroll depth) ── */
	var rnOverlay = document.getElementById('rnOverlay');
	var rnShown = false;

	function openRN() {
		if (rnShown || !rnOverlay) return;
		rnShown = true;
		rnOverlay.classList.add('open');
		document.body.style.overflow = 'hidden';
	}

	function closeRN() {
		if (!rnOverlay) return;
		rnOverlay.classList.remove('open');
		document.body.style.overflow = '';
	}

	// Close button
	var rnClose = rnOverlay ? rnOverlay.querySelector('.rn-modal__close') : null;
	if (rnClose) rnClose.addEventListener('click', closeRN);
	// Backdrop close
	var rnBg = rnOverlay ? rnOverlay.querySelector('.rn-overlay__bg') : null;
	if (rnBg) rnBg.addEventListener('click', closeRN);

	// Scroll depth trigger (85%)
	if (document.body.classList.contains('single-post')) {
		window.addEventListener('scroll', function () {
			if (rnShown) return;
			var scrollPct = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
			if (scrollPct > 0.85) openRN();
		});

		// Exit intent (mouse leaves viewport top)
		document.addEventListener('mouseout', function (e) {
			if (rnShown) return;
			if (e.clientY <= 0 && e.relatedTarget === null) openRN();
		});
	}

	// ESC key closes modals
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			closeRN();
			closeSubscribe();
		}
	});

	/* ── Lazy loading observer ── */
	if ('IntersectionObserver' in window) {
		var lazyImages = document.querySelectorAll('img[data-src]');
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					var img = entry.target;
					img.src = img.dataset.src;
					if (img.dataset.srcset) img.srcset = img.dataset.srcset;
					img.removeAttribute('data-src');
					img.removeAttribute('data-srcset');
					observer.unobserve(img);
				}
			});
		}, { rootMargin: '200px' });

		lazyImages.forEach(function (img) {
			observer.observe(img);
		});
	}

})();
