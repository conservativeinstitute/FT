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

	// Allow any element with .sub-open-btn to open the subscribe modal
	document.querySelectorAll('.sub-open-btn').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			openSubscribe();
		});
	});
	if (subOverlay) {
		subOverlay.addEventListener('click', function (e) {
			if (e.target === subOverlay) closeSubscribe();
		});
	}

	/* ── Read Next popup (exit intent / scroll depth) ── */
	var rnOverlay = document.getElementById('rnOverlay');
	var rnShown = false;
	var rnOpenTrigger = null; // "scroll" | "exit_intent"

	// GA4 / GTM event helper — pushes to dataLayer (for future GTM) and
	// calls gtag() directly so Site Kit's gtag.js forwards events to GA4 today.
	window.dataLayer = window.dataLayer || [];
	function rnTrack(eventName, params) {
		var payload = params ? Object.assign({}, params) : {};
		try {
			var dlPayload = Object.assign({ event: eventName }, payload);
			window.dataLayer.push(dlPayload);
		} catch (err) { /* noop */ }
		try {
			if (typeof window.gtag === 'function') {
				window.gtag('event', eventName, payload);
			}
		} catch (err) { /* noop */ }
	}

	// 24-hour cross-page cooldown via localStorage
	var RN_COOLDOWN_KEY = 'ft_rn_last_shown';
	var RN_COOLDOWN_MS = 24 * 60 * 60 * 1000;
	try {
		var last = parseInt(localStorage.getItem(RN_COOLDOWN_KEY) || '0', 10);
		if (last && (Date.now() - last) < RN_COOLDOWN_MS) {
			rnShown = true; // suppress for this page view
		}
	} catch (err) { /* localStorage unavailable — fall through */ }

	function openRN(triggerType) {
		if (rnShown || !rnOverlay) return;
		rnShown = true;
		rnOpenTrigger = triggerType || 'unknown';
		rnOverlay.classList.add('open');
		document.body.style.overflow = 'hidden';
		try { localStorage.setItem(RN_COOLDOWN_KEY, String(Date.now())); } catch (err) { /* noop */ }
		rnTrack('ft_popup_opened', { popup_name: 'read_next', trigger: rnOpenTrigger });
	}

	function closeRN(method) {
		if (!rnOverlay) return;
		rnOverlay.classList.remove('open');
		document.body.style.overflow = '';
		rnTrack('ft_popup_dismissed', {
			popup_name: 'read_next',
			dismiss_method: method || 'unknown',
			trigger: rnOpenTrigger
		});
	}

	// Close button
	var rnClose = rnOverlay ? rnOverlay.querySelector('.rn-modal__close') : null;
	if (rnClose) rnClose.addEventListener('click', function () { closeRN('close_button'); });
	// Backdrop close
	var rnBg = rnOverlay ? rnOverlay.querySelector('.rn-overlay__bg') : null;
	if (rnBg) rnBg.addEventListener('click', function () { closeRN('backdrop'); });

	// Recommendation click tracking
	if (rnOverlay) {
		var primaryCta = rnOverlay.querySelector('.rn-cta');
		var primaryTitle = rnOverlay.querySelector('.rn-primary__title');
		if (primaryCta) primaryCta.addEventListener('click', function () {
			rnTrack('ft_popup_recommendation_clicked', { popup_name: 'read_next', position: 'primary_cta', destination: this.href, trigger: rnOpenTrigger });
		});
		if (primaryTitle) primaryTitle.addEventListener('click', function () {
			rnTrack('ft_popup_recommendation_clicked', { popup_name: 'read_next', position: 'primary_title', destination: this.href, trigger: rnOpenTrigger });
		});
		rnOverlay.querySelectorAll('.rn-story').forEach(function (el, idx) {
			el.addEventListener('click', function () {
				rnTrack('ft_popup_recommendation_clicked', { popup_name: 'read_next', position: 'secondary_' + (idx + 1), destination: this.href, trigger: rnOpenTrigger });
			});
		});
		var rnForm = rnOverlay.querySelector('form.ft-email-form');
		if (rnForm) rnForm.addEventListener('submit', function () {
			rnTrack('ft_popup_form_submitted', { popup_name: 'read_next', source: 'read-next-popup', trigger: rnOpenTrigger });
		});
	}

	// Scroll depth trigger (85%) — passive for INP win on mobile
	if (document.body.classList.contains('single-post')) {
		window.addEventListener('scroll', function () {
			if (rnShown) return;
			var scrollPct = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
			if (scrollPct > 0.85) openRN('scroll');
		}, { passive: true });

		// Exit intent (mouse leaves viewport top)
		document.addEventListener('mouseout', function (e) {
			if (rnShown) return;
			if (e.clientY <= 0 && e.relatedTarget === null) openRN('exit_intent');
		});
	}

	// ESC key closes modals
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			if (rnOverlay && rnOverlay.classList.contains('open')) closeRN('escape');
			closeSubscribe();
		}
	});

	/* ── Auto-classify article links as internal/external ── */
	var artBody = document.querySelector('.art-body');
	if (artBody) {
		var siteHost = window.location.hostname;
		artBody.querySelectorAll('a[href]').forEach(function (a) {
			try {
				var url = new URL(a.href);
				if (url.hostname === siteHost || url.hostname === 'www.' + siteHost) {
					a.classList.add('int');
				} else {
					a.classList.add('ext');
				}
			} catch (e) {
				// relative URLs are internal
				a.classList.add('int');
			}
		});
	}

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
