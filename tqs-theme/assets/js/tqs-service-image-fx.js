/**
 * Service content image effects — mouse tracking + IntersectionObserver.
 * Loaded only on tqs_service singles. No jQuery / GSAP.
 */
(function () {
	'use strict';

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	var medias = document.querySelectorAll('.tqs-service-hero-media');
	if (!medias.length) {
		return;
	}

	/* ------------------------------------------------------------------ */
	/* IntersectionObserver — scroll-triggered FX + badge pop-in           */
	/* ------------------------------------------------------------------ */
	var scrollTargets = document.querySelectorAll(
		'.tqs-img-fx-slice-reveal, .tqs-img-fx-clarity-focus, .tqs-img-fx-badge'
	);

	if (scrollTargets.length && typeof IntersectionObserver !== 'undefined') {
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

		scrollTargets.forEach(function (el) {
			observer.observe(el);
		});
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                             */
	/* ------------------------------------------------------------------ */
	function setMouseVars(el, e) {
		var rect = el.getBoundingClientRect();
		var x = ((e.clientX - rect.left) / rect.width) * 100;
		var y = ((e.clientY - rect.top) / rect.height) * 100;
		el.style.setProperty('--mx', Math.max(0, Math.min(100, x)) + '%');
		el.style.setProperty('--my', Math.max(0, Math.min(100, y)) + '%');
		return { x: x, y: y, rect: rect };
	}

	function clamp(n, min, max) {
		return Math.max(min, Math.min(max, n));
	}

	/* ------------------------------------------------------------------ */
	/* Per-media mouse FX                                                  */
	/* ------------------------------------------------------------------ */
	medias.forEach(function (media) {
		var isDual = media.classList.contains('tqs-img-fx-dual-layer-reveal');
		var isTilt = media.classList.contains('tqs-img-fx-tilt-3d');
		var isSpot = media.classList.contains('tqs-img-fx-spotlight-follow');
		var isWave = media.classList.contains('tqs-img-fx-wave-distort');
		var isSpark = media.classList.contains('tqs-img-fx-particle-sparkle');
		var isGlass = media.classList.contains('tqs-img-fx-glass-shatter');
		var badge = media.querySelector('.tqs-img-fx-badge');
		var needsMouse =
			isDual || isTilt || isSpot || isWave || isSpark || isGlass || badge;

		if (!needsMouse) {
			return;
		}

		var waveAnimId = null;
		var sparkleAt = 0;
		var turbulence = media.querySelector('feTurbulence');
		var img = media.querySelector('img:not(.tqs-img-fx-dual-duotone)');
		var tiles = media.querySelectorAll('.tqs-img-fx-glass-tile');

		media.addEventListener('mousemove', function (e) {
			var pos = setMouseVars(media, e);
			media.classList.add('is-tracking');

			if (isTilt) {
				var rx = clamp((50 - pos.y) * 0.18, -12, 12);
				var ry = clamp((pos.x - 50) * 0.18, -12, 12);
				media.style.transform =
					'perspective(800px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg)';
			}

			if (badge) {
				var bx = clamp((pos.x - 80) * 0.12, -8, 8);
				var by = clamp((pos.y - 15) * 0.12, -8, 8);
				badge.classList.add('is-magnetic');
				if (badge.classList.contains('is-in-view')) {
					badge.style.transform =
						'scale(1) translate(' + bx + 'px, ' + by + 'px)';
				}
			}

			if (isGlass && tiles.length) {
				tiles.forEach(function (tile) {
					var tr = tile.getBoundingClientRect();
					var cx = tr.left + tr.width / 2;
					var cy = tr.top + tr.height / 2;
					var dx = e.clientX - cx;
					var dy = e.clientY - cy;
					var dist = Math.sqrt(dx * dx + dy * dy);
					if (dist < 90) {
						tile.classList.add('is-near');
					} else {
						tile.classList.remove('is-near');
					}
				});
			}

			if (isSpark) {
				var now = Date.now();
				if (now - sparkleAt >= 60) {
					sparkleAt = now;
					var spark = document.createElement('span');
					spark.className = 'tqs-img-fx-particle';
					spark.style.left = e.clientX - pos.rect.left - 3 + 'px';
					spark.style.top = e.clientY - pos.rect.top - 3 + 'px';
					media.appendChild(spark);
					spark.addEventListener('animationend', function () {
						spark.remove();
					});
				}
			}

			if (isWave && turbulence && !waveAnimId) {
				var t0 = performance.now();
				if (img) {
					var filterId = media.getAttribute('data-wave-filter');
					if (filterId) {
						img.style.filter = 'url(#' + filterId + ')';
					}
				}
				function tick(nowTs) {
					var elapsed = (nowTs - t0) / 1000;
					var freq = 0.012 + Math.sin(elapsed * 4) * 0.006;
					turbulence.setAttribute('baseFrequency', freq.toFixed(4) + ' 0.04');
					waveAnimId = requestAnimationFrame(tick);
				}
				waveAnimId = requestAnimationFrame(tick);
			}
		});

		media.addEventListener('mouseleave', function () {
			media.classList.remove('is-tracking');
			media.style.setProperty('--mx', '50%');
			media.style.setProperty('--my', '50%');

			if (isTilt) {
				media.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg)';
			}

			if (badge) {
				badge.classList.remove('is-magnetic');
				if (badge.classList.contains('is-in-view')) {
					badge.style.transform = 'scale(1) translate(0, 0)';
				}
			}

			if (isGlass) {
				tiles.forEach(function (tile) {
					tile.classList.remove('is-near');
				});
			}

			if (isWave) {
				if (waveAnimId) {
					cancelAnimationFrame(waveAnimId);
					waveAnimId = null;
				}
				if (img) {
					img.style.filter = '';
				}
				if (turbulence) {
					turbulence.setAttribute('baseFrequency', '0.02 0.04');
				}
			}
		});
	});
})();
