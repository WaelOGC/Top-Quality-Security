/**
 * Service content image effects — IntersectionObserver for scroll FX.
 * Loaded only on tqs_service singles. No jQuery / GSAP.
 */
(function () {
	'use strict';

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	var targets = document.querySelectorAll(
		'.tqs-img-fx-fade-scroll, .tqs-img-fx-slide-in'
	);
	if (!targets.length || typeof IntersectionObserver === 'undefined') {
		return;
	}

	var observer = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}
				entry.target.classList.add('is-in-view');
				observer.unobserve(entry.target);
			});
		},
		{ threshold: 0.2 }
	);

	targets.forEach(function (el) {
		observer.observe(el);
	});
})();
