/**
 * TQS Cinematic Scroll — Lenis + GSAP (homepage front-end only)
 *
 * Reveal classes:
 *   .tqs-reveal / .tqs-reveal-left / .tqs-reveal-right
 *
 * Hero:
 *   .tqs-hero[data-tqs-hero-slider] — wheel/swipe + varied crossfade
 *
 * Guards: prefers-reduced-motion, Elementor editor/preview.
 */
(function () {
	'use strict';

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
		} catch (e) { /* ignore */ }
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
	/* Hero slider — varied crossfade                                      */
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
		var TRANSITION_MS = reduced ? 0 : 1400;
		var COOLDOWN_MS = reduced ? 0 : 1500;
		var heroCfg = (typeof window.tqsHeroData === 'object' && window.tqsHeroData) ? window.tqsHeroData : {};
		var autoplayEnabled = (typeof heroCfg.autoplay === 'undefined') ? true : !!heroCfg.autoplay;
		var AUTO_MS = Math.max(2000, parseInt(heroCfg.autoplayMs, 10) || 6000);
		var SWIPE_MIN = 48;
		var hasGsap = typeof gsap !== 'undefined';
		var variantIndex = 0;
		var slideDir = 1;

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

		function nextVariant() {
			/* Cycle: plain → zoom settle → slide-in (L/R alternate) */
			var kind = variantIndex % 3;
			variantIndex += 1;
			if (kind === 1) {
				return { type: 'zoom' };
			}
			if (kind === 2) {
				var dir = slideDir;
				slideDir *= -1;
				return { type: 'slide', dir: dir };
			}
			return { type: 'fade' };
		}

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

		function runCrossfade(outgoing, incoming, duration, onDone) {
			var outContent = outgoing.querySelector('.tqs-hero-content');
			var inContent = incoming.querySelector('.tqs-hero-content');
			var inBg = incoming.querySelector('.tqs-hero-slide-bg img');
			var outBg = outgoing.querySelector('.tqs-hero-slide-bg img');
			var ease = 'power2.inOut';
			var variant = reduced ? { type: 'fade' } : nextVariant();

			/* Reduced motion: instant swap, no animation */
			if (reduced) {
				if (hasGsap) {
					gsap.set(outgoing, { opacity: 0 });
					gsap.set(incoming, { opacity: 1 });
					if (outContent) {
						gsap.set(outContent, { clearProps: 'opacity' });
					}
					if (inContent) {
						gsap.set(inContent, { clearProps: 'opacity' });
					}
				} else {
					outgoing.style.opacity = '0';
					incoming.style.opacity = '1';
				}
				if (typeof onDone === 'function') {
					onDone();
				}
				return;
			}

			if (!hasGsap) {
				/* Incoming fades over opaque outgoing — no mid-fade dip to white */
				incoming.style.opacity = '0';
				outgoing.style.opacity = '1';
				incoming.style.transition = 'opacity ' + duration + 's ease-in-out';
				outgoing.style.transition = 'opacity ' + (duration * 0.25) + 's ease-in';
				window.requestAnimationFrame(function () {
					incoming.style.opacity = '1';
				});
				window.setTimeout(function () {
					outgoing.style.opacity = '0';
				}, duration * 750);
				window.setTimeout(onDone, duration * 1000);
				return;
			}

			if (inBg) {
				gsap.set(inBg, { clearProps: 'transform' });
			}
			if (outBg) {
				gsap.set(outBg, { clearProps: 'transform' });
			}

			/*
			 * Flash-free dissolve: keep outgoing fully opaque underneath while
			 * incoming fades in on top, then drop outgoing only at the end.
			 */
			gsap.set(outgoing, { opacity: 1 });
			gsap.set(incoming, { opacity: 0 });

			var tl = gsap.timeline({ onComplete: onDone });
			tl.fromTo(incoming, { opacity: 0 }, { opacity: 1, duration: duration, ease: ease }, 0);
			tl.to(outgoing, { opacity: 0, duration: duration * 0.28, ease: 'power1.in' }, duration * 0.72);

			if (outContent) {
				tl.to(outContent, { opacity: 0, duration: duration * 0.45, ease: 'power1.in' }, duration * 0.15);
			}
			if (inContent) {
				tl.fromTo(
					inContent,
					{ opacity: 0 },
					{ opacity: 1, duration: duration * 0.55, ease: 'power2.out' },
					duration * 0.35
				);
			}

			if (inBg && variant.type === 'zoom') {
				tl.fromTo(
					inBg,
					{ scale: 1.04 },
					{ scale: 1, duration: duration, ease: 'power2.out' },
					0
				);
			} else if (inBg && variant.type === 'slide') {
				tl.fromTo(
					inBg,
					{ xPercent: variant.dir * -3.5, scale: 1.02 },
					{ xPercent: 0, scale: 1, duration: duration, ease: 'power2.out' },
					0
				);
			}
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

			function finish() {
				outgoing.classList.remove('is-leaving');
				incoming.classList.remove('is-entering');
				incoming.classList.add('is-active');
				if (hasGsap) {
					gsap.set(outgoing, { opacity: 0 });
					gsap.set(incoming, { opacity: 1 });
					if (outBg) {
						gsap.set(outBg, { clearProps: 'transform' });
					}
					if (inBg) {
						gsap.set(inBg, { clearProps: 'transform' });
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
				index = nextIndex;
				window.setTimeout(function () {
					transitioning = false;
				}, Math.max(0, COOLDOWN_MS - TRANSITION_MS));
			}

			runCrossfade(outgoing, incoming, duration, finish);
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
				autoTimer = null;
			}
			if (!autoplayEnabled) {
				return;
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
			if (Math.abs(dy) < SWIPE_MIN || Math.abs(dy) < Math.abs(dx) || transitioning) {
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
	/* ScrollTrigger reveals                                               */
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

		var duration = 1.15;
		var ease = 'power2.out';
		var start = 'top 82%';

		function staggerChildren(container, selector) {
			var kids = container.querySelectorAll(selector);
			if (!kids.length) {
				return;
			}
			gsap.set(kids, { opacity: 0, y: 36 });
			gsap.to(kids, {
				opacity: 1,
				y: 0,
				duration: 0.85,
				ease: ease,
				stagger: 0.12,
				overwrite: 'auto',
				onComplete: function () {
					kids.forEach(function (k) {
						k.classList.add('is-card-revealed');
					});
				},
			});
		}

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
							if (el.classList.contains('tqs-reviews-section') || el.querySelector('.tqs-reviews-grid')) {
								staggerChildren(el, '.tqs-reviews-grid > *');
							}
						},
					},
				}
			);
		}

		document.querySelectorAll('.tqs-reveal').forEach(function (el) {
			revealIn(el, { opacity: 0, y: 60, x: 0 });
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
						if (right.classList.contains('tqs-whyus-cards')) {
							staggerChildren(right, '.tqs-whyus-card');
						}
					},
				});
				return;
			}

			revealIn(left, { opacity: 0, x: -64, y: 0 });
		});

		document.querySelectorAll('.tqs-reveal-right').forEach(function (right) {
			if (paired && paired.has(right)) {
				return;
			}
			revealIn(right, { opacity: 0, x: 64, y: 0 });
		});

		window.setTimeout(function () {
			ScrollTrigger.refresh();
		}, 200);
		window.addEventListener('load', function () {
			ScrollTrigger.refresh();
		}, { once: true });
	}

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
