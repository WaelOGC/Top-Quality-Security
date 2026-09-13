/**
 * TQS Cinematic Scroll — Lenis + GSAP (homepage front-end only)
 *
 * Reveal classes (add in front-page.php markup):
 *   .tqs-reveal       — single / full-width block: fade + slide up (y: 40 → 0)
 *   .tqs-reveal-left  — left column of a two-column section: fade + slide from left
 *   .tqs-reveal-right — right column of a two-column section: fade + slide from right
 *
 * Hero:
 *   .tqs-hero — wheel capture + internal parallax/fade while pointer is inside bounds
 *
 * Guards: prefers-reduced-motion, Elementor editor/preview iframe, touch/coarse pointers.
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

	function isCoarsePointer() {
		if (window.matchMedia && window.matchMedia('(pointer: coarse)').matches) {
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
	/* Hero wheel capture (desktop fine pointer only)                      */
	/* ------------------------------------------------------------------ */

	function getActiveHeroLayers(hero) {
		var active = hero.querySelector('.tqs-hero-slide.is-active') || hero;
		return {
			bg: active.querySelector('.tqs-hero-slide-bg'),
			content: active.querySelector('.tqs-hero-content'),
			illustration: active.querySelector('.tqs-hero-illustration'),
		};
	}

	function initHeroCapture(hero) {
		if (reduced || !hero || typeof gsap === 'undefined' || isCoarsePointer()) {
			return;
		}

		var pointerInside = false;
		var progress = 0;
		var tickBudget = 3;
		var ticksUsed = 0;
		var locked = false;
		var releasing = false;

		function updatePointer(clientX, clientY) {
			var rect = hero.getBoundingClientRect();
			pointerInside =
				clientX >= rect.left &&
				clientX <= rect.right &&
				clientY >= rect.top &&
				clientY <= rect.bottom;
		}

		function tweenLayers(p, duration) {
			var layers = getActiveHeroLayers(hero);
			var t = Math.max(0, Math.min(1, p));
			var d = typeof duration === 'number' ? duration : 0.65;

			if (layers.bg) {
				gsap.to(layers.bg, {
					y: t * 48,
					scale: 1 + t * 0.06,
					duration: d,
					ease: 'power2.out',
					overwrite: 'auto',
				});
			}
			if (layers.content) {
				gsap.to(layers.content, {
					y: t * -28,
					opacity: 1 - t * 0.45,
					scale: 1 - t * 0.04,
					duration: d,
					ease: 'power2.out',
					overwrite: 'auto',
				});
			}
			if (layers.illustration) {
				gsap.to(layers.illustration, {
					y: t * 36,
					opacity: 1 - t * 0.35,
					duration: d,
					ease: 'power2.out',
					overwrite: 'auto',
				});
			}
		}

		function resetLayers(onDone) {
			var layers = getActiveHeroLayers(hero);
			var remaining = 0;
			function doneOne() {
				remaining -= 1;
				if (remaining <= 0 && typeof onDone === 'function') {
					onDone();
				}
			}

			['bg', 'content', 'illustration'].forEach(function (key) {
				if (layers[key]) {
					remaining += 1;
					gsap.to(layers[key], {
						y: 0,
						x: 0,
						scale: 1,
						opacity: 1,
						duration: 0.85,
						ease: 'power2.out',
						overwrite: 'auto',
						onComplete: doneOne,
					});
				}
			});

			if (remaining === 0 && typeof onDone === 'function') {
				onDone();
			}
		}

		function releaseAndContinue(scrollDown) {
			if (releasing) {
				return;
			}
			releasing = true;
			locked = true;

			resetLayers(function () {
				progress = 0;
				ticksUsed = 0;
				releasing = false;

				if (lenis && typeof lenis.start === 'function') {
					lenis.start();
				}

				if (scrollDown) {
					var distance = Math.min(420, window.innerHeight * 0.55);
					if (lenis && typeof lenis.scrollTo === 'function') {
						lenis.scrollTo(window.scrollY + distance, { duration: 1.05 });
					} else {
						window.scrollBy({ top: distance, behavior: 'smooth' });
					}
				}

				/* Brief cool-down so the same gesture does not re-capture immediately */
				window.setTimeout(function () {
					locked = false;
				}, 700);
			});
		}

		function onWheel(e) {
			if (locked || releasing) {
				return;
			}
			if (!pointerInside) {
				return;
			}

			var delta = e.deltaY;
			if (delta === 0) {
				return;
			}

			/* Allow native scroll-up when hero progress is still at rest */
			if (delta < 0 && progress <= 0) {
				return;
			}

			e.preventDefault();

			if (lenis && typeof lenis.stop === 'function') {
				lenis.stop();
			}

			ticksUsed += 1;
			var step = Math.min(0.4, Math.abs(delta) / 900);
			if (delta > 0) {
				progress = Math.min(1, progress + step);
			} else {
				progress = Math.max(0, progress - step);
			}

			tweenLayers(progress, 0.55);

			if (progress >= 1 || ticksUsed >= tickBudget) {
				releaseAndContinue(delta > 0);
			} else if (progress <= 0 && delta < 0) {
				if (lenis && typeof lenis.start === 'function') {
					lenis.start();
				}
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

		/* Pair left/right siblings so they animate together from the shared parent */
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
			initHeroCapture(hero);
		}

		initReveals();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
