/**
 * Federalist Times — Email Capture Handler
 * Handles all newsletter forms on the page via AJAX.
 */
(function () {
	'use strict';

	var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	function showMsg(form, text, isError) {
		var existing = form.querySelector('.ft-email-msg');
		if (existing) existing.remove();

		var msg = document.createElement('p');
		msg.className = 'ft-email-msg';
		msg.style.fontFamily = "'Lato', sans-serif";
		msg.style.fontSize = '.78rem';
		msg.style.marginTop = '.4rem';
		msg.style.color = isError ? '#9E2B2B' : '#1E7A4A';
		msg.textContent = text;
		form.appendChild(msg);
	}

	function handleSubmit(e) {
		e.preventDefault();
		var form = e.target;

		// Prevent double submission
		if (form.dataset.submitting === 'true') return;

		var emailInput = form.querySelector('input[type="email"]');
		if (!emailInput) return;

		var email = emailInput.value.trim();

		// Client-side validation
		if (!email) {
			showMsg(form, 'Please enter your email address.', true);
			emailInput.focus();
			return;
		}

		if (!EMAIL_REGEX.test(email)) {
			showMsg(form, 'Please enter a valid email address.', true);
			emailInput.focus();
			return;
		}

		// Mark as submitting
		form.dataset.submitting = 'true';
		var submitBtn = form.querySelector('button[type="submit"]');
		var origText = submitBtn ? submitBtn.textContent : '';
		if (submitBtn) submitBtn.textContent = 'Sending...';

		// AJAX POST
		var data = new FormData();
		data.append('action', 'ft_capture_email');
		data.append('nonce', ftAjax.nonce);
		data.append('email', email);
		data.append('source', form.dataset.source || 'unknown');

		var xhr = new XMLHttpRequest();
		xhr.open('POST', ftAjax.url, true);
		xhr.onreadystatechange = function () {
			if (xhr.readyState !== 4) return;
			form.dataset.submitting = 'false';

			if (xhr.status === 200) {
				try {
					var resp = JSON.parse(xhr.responseText);
					if (resp.success) {
						showMsg(form, "You're in! Check your inbox.", false);
						emailInput.value = '';
						if (submitBtn) submitBtn.textContent = 'Subscribed';

						// Handle modal success state
						var subFormFields = document.getElementById('subFormFields');
						var subSuccess = document.getElementById('subSuccess');
						if (form.dataset.source === 'modal' && subFormFields && subSuccess) {
							subFormFields.style.display = 'none';
							subSuccess.style.display = 'block';
						}
					} else {
						showMsg(form, resp.data || 'Something went wrong. Please try again.', true);
						if (submitBtn) submitBtn.textContent = origText;
					}
				} catch (err) {
					showMsg(form, 'Something went wrong. Please try again.', true);
					if (submitBtn) submitBtn.textContent = origText;
				}
			} else {
				showMsg(form, 'Network error. Please try again.', true);
				if (submitBtn) submitBtn.textContent = origText;
			}
		};
		xhr.send(data);
	}

	// Bind to all email capture forms
	var forms = document.querySelectorAll('.ft-email-form');
	forms.forEach(function (form) {
		form.addEventListener('submit', handleSubmit);
	});

	// Unsubscribe form handler
	var unsubForm = document.querySelector('.ft-unsub-form');
	if (unsubForm) {
		unsubForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var form = e.target;

			if (form.dataset.submitting === 'true') return;

			var emailInput = form.querySelector('input[type="email"]');
			if (!emailInput) return;

			var email = emailInput.value.trim();

			if (!email || !EMAIL_REGEX.test(email)) {
				showMsg(form, 'Please enter a valid email address.', true);
				emailInput.focus();
				return;
			}

			form.dataset.submitting = 'true';
			var submitBtn = form.querySelector('button[type="submit"]');
			var origText = submitBtn ? submitBtn.textContent : '';
			if (submitBtn) submitBtn.textContent = 'Processing...';

			var data = new FormData();
			data.append('action', 'ft_unsubscribe_email');
			data.append('nonce', ftAjax.nonce);
			data.append('email', email);

			var xhr = new XMLHttpRequest();
			xhr.open('POST', ftAjax.url, true);
			xhr.onreadystatechange = function () {
				if (xhr.readyState !== 4) return;
				form.dataset.submitting = 'false';

				if (xhr.status === 200) {
					try {
						var resp = JSON.parse(xhr.responseText);
						if (resp.success) {
							showMsg(form, resp.data || 'You have been successfully unsubscribed.', false);
							emailInput.value = '';
							if (submitBtn) submitBtn.textContent = 'Unsubscribed';
						} else {
							showMsg(form, resp.data || 'That email was not found in our list.', true);
							if (submitBtn) submitBtn.textContent = origText;
						}
					} catch (err) {
						showMsg(form, 'Something went wrong. Please try again.', true);
						if (submitBtn) submitBtn.textContent = origText;
					}
				} else {
					showMsg(form, 'Network error. Please try again.', true);
					if (submitBtn) submitBtn.textContent = origText;
				}
			};
			xhr.send(data);
		});
	}

})();
