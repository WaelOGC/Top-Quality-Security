/**
 * TQS Cinematic Scroll — Lenis + GSAP (homepage front-end only)
 *
 * Reveal classes (add in front-page.php markup):
 *   .tqs-reveal       — single / full-width block: fade + slide up (y: 40 → 0)
 *   .tqs-reveal-left  — left column of a two-column section: fade + slide from left
 *   .tqs-reveal-right — right column of a two-column section: fade + slide from right
 *
 * Hero:
 *   .tqs-hero[data-tqs-hero-slider] — multi-slide burn/dissolve; wheel/swipe when pointer in bounds
 *
 * Guards: prefers-reduced-motion, Elementor editor/preview iframe, coarse pointers.
 */
(function () {
	'use strict';

	/* ------------------------------------------------------------------ */
	/* Guards                                                              */
	/* ------------------------------------------------------------------ */

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function isElementorActiveEditor() {
		if (window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function' && window.elementorFrontend.isEditMode()) {
			return true;
		}
		try {
			if (window.self !== window.top) {
				var frameEl = window.frameElement;
				if (frameEl && (
					frameEl.id === 'elementor-preview-iframe' ||
					(frameEl.className && String(frameEl.className).indexOf('elementor') !== -1)
				)) {
					return true;
				}
			}
		} catch (e) {
			/* cross-origin — ignore */
		}
		if (document.body && (
			document.body.classList.contains('elementor-editor-active') ||
			document.body.classList.contains('elementor-editor-preview')
		)) {
			return true;
		}
		return false;
	}

	if (isElementorActiveEditor()) {
		return;
	}

	var reduced = prefersReducedMotion();
	var lenis = null;

	/* ------------------------------------------------------------------ */
	/* Lenis + GSAP ticker sync                                            */
	/* ------------------------------------------------------------------ */

	function initLenis() {
		if (reduced || typeof Lenis === 'undefined') {
			return null;
		}

		var instance = new Lenis({
			duration: 1.1,
			smoothWheel: true,
		});

		if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
			instance.on('scroll', ScrollTrigger.update);
			gsap.ticker.add(function (time) {
				instance.raf(time * 1000);
			});
			gsap.ticker.lagSmoothing(0);
		} else {
			function raf(time) {
				instance.raf(time);
				requestAnimationFrame(raf);
			}
			requestAnimationFrame(raf);
		}

		return instance;
	}

	/* ------------------------------------------------------------------ */
	/* Hero multi-slide burn/dissolve (2+ slides only)                     */
	/* ------------------------------------------------------------------ */

	function initHeroSlider(hero) {
		if (!hero || !hero.hasAttribute('data-tqs-hero-slider')) {
			return;
		}

		var slides = Array.prototype.slice.call(hero.querySelectorAll('.tqs-hero-slide'));
		var count = slides.length;
		hero.classList.add('tqs-hero--ready');

		if (count < 2) {
			return;
		}

		var index = 0;
		var transitioning = false;
		var pointerInside = false;
		var lastInteractAt = Date.now();
		var autoTimer = null;
		var TRANSITION_MS = reduced ? 300 : 1000;
		var COOLDOWN_MS = reduced ? 320 : 1050;
		var AUTO_MS = 6000;
		var SWIPE_MIN = 48;
		var noise = hero.querySelector('.tqs-hero-noise');
		var hasGsap = typeof gsap !== 'undefined';

		slides.forEach(function (slide, i) {
			if (i === 0) {
				slide.classList.add('is-active');
				if (hasGsap) {
					gsap.set(slide, { opacity: 1 });
				} else {
					slide.style.opacity = '1';
				}
			} else {
				slide.classList.remove('is-active');
				if (hasGsap) {
					gsap.set(slide, { opacity: 0 });
				} else {
					slide.style.opacity = '0';
				}
			}
		});

		function updatePointer(clientX, clientY) {
			var rect = hero.getBoundingClientRect();
			pointerInside =
				clientX >= rect.left &&
				clientX <= rect.right &&
				clientY >= rect.top &&
				clientY <= rect.bottom;
		}

		function markInteracted() {
			lastInteractAt = Date.now();
			restartAutoTimer();
		}

		function goTo(nextIndex) {
			if (transitioning) {
				return;
			}
			nextIndex = ((nextIndex % count) + count) % count;
			if (nextIndex === index) {
				return;
			}

			var outgoing = slides[index];
			var incoming = slides[nextIndex];
			transitioning = true;

			outgoing.classList.remove('is-active');
			outgoing.classList.add('is-leaving');
			incoming.classList.add('is-entering');
			incoming.setAttribute('aria-hidden', 'false');
			outgoing.setAttribute('aria-hidden', 'true');

			var outBg = outgoing.querySelector('.tqs-hero-slide-bg img');
			var inBg = incoming.querySelector('.tqs-hero-slide-bg img');
			var outContent = outgoing.querySelector('.tqs-hero-content');
			var inContent = incoming.querySelector('.tqs-hero-content');
			var duration = TRANSITION_MS / 1000;
			var ease = reduced ? 'power1.out' : 'power2.inOut';

			function finish() {
				outgoing.classList.remove('is-leaving');
				incoming.classList.remove('is-entering');
				incoming.classList.add('is-active');
				if (hasGsap) {
					gsap.set(outgoing, { opacity: 0 });
					gsap.set(incoming, { opacity: 1 });
					if (outBg) {
						gsap.set(outBg, { scale: 1, clearProps: 'transform' });
					}
					if (inBg) {
						gsap.set(inBg, { scale: 1 });
					}
					if (outContent) {
						gsap.set(outContent, { opacity: 1, clearProps: 'opacity' });
					}
					if (inContent) {
						gsap.set(inContent, { opacity: 1, clearProps: 'opacity' });
					}
				} else {
					outgoing.style.opacity = '0';
					incoming.style.opacity = '1';
				}
				if (noise) {
					noise.hidden = true;
					noise.style.opacity = '0';
				}
				index = nextIndex;
				window.setTimeout(function () {
					transitioning = false;
				}, Math.max(0, COOLDOWN_MS - TRANSITION_MS));
			}

			if (!hasGsap) {
				if (noise && !reduced) {
					noise.hidden = false;
					noise.style.opacity = '0.35';
					window.setTimeout(function () {
						noise.style.opacity = '0';
					}, TRANSITION_MS / 2);
				}
				incoming.style.opacity = '0';
				outgoing.style.transition = 'opacity ' + duration + 's ease';
				incoming.style.transition = 'opacity ' + duration + 's ease';
				window.requestAnimationFrame(function () {
					outgoing.style.opacity = '0';
					incoming.style.opacity = '1';
				});
				window.setTimeout(finish, TRANSITION_MS);
				return;
			}

			var tl = gsap.timeline({ onComplete: finish });

			if (noise && !reduced) {
				noise.hidden = false;
				tl.fromTo(
					noise,
					{ opacity: 0 },
					{ opacity: 0.55, duration: duration * 0.45, ease: 'power1.in' },
					0
				);
				tl.to(noise, { opacity: 0, duration: duration * 0.55, ease: 'power1.out' }, duration * 0.45);
			}

			tl.to(outgoing, { opacity: 0, duration: duration, ease: ease }, 0);
			tl.fromTo(incoming, { opacity: 0 }, { opacity: 1, duration: duration, ease: ease }, 0);

			if (outContent) {
				tl.to(outContent, { opacity: 0, duration: duration * 0.55, ease: 'power1.in' }, 0);
			}
			if (inContent) {
				tl.fromTo(inContent, { opacity: 0 }, { opacity: 1, duration: duration * 0.7, ease: 'power1.out' }, duration * 0.25);
			}

			if (!reduced) {
				if (inBg) {
					tl.fromTo(inBg, { scale: 1.03 }, { scale: 1, duration: duration, ease: ease }, 0);
				}
				if (outBg) {
					tl.to(outBg, { scale: 1.04, duration: duration, ease: ease }, 0);
				}
			}
		}

		function next() {
			goTo(index + 1);
		}

		function prev() {
			goTo(index - 1);
		}

		function restartAutoTimer() {
			if (autoTimer) {
				window.clearInterval(autoTimer);
			}
			autoTimer = window.setInterval(function () {
				if (Date.now() - lastInteractAt < AUTO_MS - 50) {
					return;
				}
				if (transitioning) {
					return;
				}
				next();
			}, AUTO_MS);
		}

		function onWheel(e) {
			if (!pointerInside || transitioning) {
				return;
			}
			if (e.deltaY === 0) {
				return;
			}
			e.preventDefault();
			markInteracted();
			if (e.deltaY > 0) {
				next();
			} else {
				prev();
			}
		}

		document.addEventListener('mousemove', function (e) {
			updatePointer(e.clientX, e.clientY);
		}, { passive: true });

		hero.addEventListener('mouseenter', function (e) {
			updatePointer(e.clientX, e.clientY);
		}, { passive: true });

		hero.addEventListener('mouseleave', function () {
			pointerInside = false;
		}, { passive: true });

		/* passive:false required so preventDefault can block page scroll inside hero */
		hero.addEventListener('wheel', onWheel, { passive: false });

		var touchStartY = null;
		var touchStartX = null;
		var touchActive = false;

		hero.addEventListener('touchstart', function (e) {
			if (!e.touches || !e.touches.length) {
				return;
			}
			touchStartY = e.touches[0].clientY;
			touchStartX = e.touches[0].clientX;
			touchActive = true;
		}, { passive: true });

		hero.addEventListener('touchend', function (e) {
			if (!touchActive || touchStartY === null) {
				return;
			}
			touchActive = false;
			var t = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
			if (!t) {
				return;
			}
			var dy = touchStartY - t.clientY;
			var dx = touchStartX - t.clientX;
			touchStartY = null;
			touchStartX = null;

			if (Math.abs(dy) < SWIPE_MIN) {
				return;
			}
			if (Math.abs(dy) < Math.abs(dx)) {
				return;
			}
			if (transitioning) {
				return;
			}
			markInteracted();
			if (dy > 0) {
				next();
			} else {
				prev();
			}
		}, { passive: true });

		restartAutoTimer();
	}

	/* ------------------------------------------------------------------ */
	/* ScrollTrigger section reveals                                       */
	/* ------------------------------------------------------------------ */

	function initReveals() {
		var nodes = document.querySelectorAll('.tqs-reveal, .tqs-reveal-left, .tqs-reveal-right');

		if (reduced || typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
			nodes.forEach(function (el) {
				el.classList.add('is-revealed');
			});
			return;
		}

		gsap.registerPlugin(ScrollTrigger);

		var duration = 1.0;
		var ease = 'power2.out';
		var start = 'top 78%'; /* ~22% visibility */

		function revealIn(el, fromVars) {
			gsap.fromTo(
				el,
				fromVars,
				{
					opacity: 1,
					x: 0,
					y: 0,
					duration: duration,
					ease: ease,
					immediateRender: false,
					scrollTrigger: {
						trigger: el,
						start: start,
						once: true,
						onEnter: function () {
							el.classList.add('is-revealed');
						},
					},
				}
			);
		}

		document.querySelectorAll('.tqs-reveal').forEach(function (el) {
			revealIn(el, { opacity: 0, y: 40, x: 0 });
		});

		var paired = typeof WeakSet !== 'undefined' ? new WeakSet() : null;

		document.querySelectorAll('.tqs-reveal-left').forEach(function (left) {
			if (paired && paired.has(left)) {
				return;
			}

			var parent = left.parentElement;
			var right = parent ? parent.querySelector(':scope > .tqs-reveal-right') : null;

			if (right && parent) {
				if (paired) {
					paired.add(left);
					paired.add(right);
				}

				ScrollTrigger.create({
					trigger: parent,
					start: start,
					once: true,
					onEnter: function () {
						gsap.to(left, {
							opacity: 1,
							x: 0,
							duration: duration,
							ease: ease,
							overwrite: 'auto',
							onComplete: function () {
								left.classList.add('is-revealed');
							},
						});
						gsap.to(right, {
							opacity: 1,
							x: 0,
							duration: duration,
							ease: ease,
							overwrite: 'auto',
							onComplete: function () {
								right.classList.add('is-revealed');
							},
						});
					},
				});
				return;
			}

			revealIn(left, { opacity: 0, x: -56, y: 0 });
		});

		document.querySelectorAll('.tqs-reveal-right').forEach(function (right) {
			if (paired && paired.has(right)) {
				return;
			}
			revealIn(right, { opacity: 0, x: 56, y: 0 });
		});
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                                */
	/* ------------------------------------------------------------------ */

	function boot() {
		lenis = initLenis();

		var hero = document.querySelector('.tqs-hero');
		if (hero) {
			initHeroSlider(hero);
		}

		initReveals();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
