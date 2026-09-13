/**
 * TQS Cinematic Scroll — Lenis + GSAP (homepage front-end only)
 *
 * Reveal classes:
 *   .tqs-reveal / .tqs-reveal-left / .tqs-reveal-right
 *
 * Hero:
 *   .tqs-hero[data-tqs-hero-slider] — wheel/swipe + canvas burn transition
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
	/* Value noise (lightweight, no deps)                                  */
	/* ------------------------------------------------------------------ */

	function hash2(x, y) {
		var n = Math.sin(x * 127.1 + y * 311.7) * 43758.5453;
		return n - Math.floor(n);
	}

	function smoothstep(t) {
		return t * t * (3 - 2 * t);
	}

	function valueNoise2D(x, y) {
		var x0 = Math.floor(x);
		var y0 = Math.floor(y);
		var fx = smoothstep(x - x0);
		var fy = smoothstep(y - y0);
		var a = hash2(x0, y0);
		var b = hash2(x0 + 1, y0);
		var c = hash2(x0, y0 + 1);
		var d = hash2(x0 + 1, y0 + 1);
		var u = a + (b - a) * fx;
		var v = c + (d - c) * fx;
		return u + (v - u) * fy;
	}

	function fbm(x, y) {
		var sum = 0;
		var amp = 0.5;
		var freq = 1;
		for (var i = 0; i < 4; i++) {
			sum += valueNoise2D(x * freq, y * freq) * amp;
			freq *= 2;
			amp *= 0.5;
		}
		return sum;
	}

	/* ------------------------------------------------------------------ */
	/* Hero burn canvas                                                    */
	/* ------------------------------------------------------------------ */

	function createBurnController(hero) {
		var wrap = hero.querySelector('.tqs-hero-burn-canvas-wrap');
		var canvas = wrap ? wrap.querySelector('.tqs-hero-burn-canvas') : null;
		if (!wrap || !canvas || !canvas.getContext) {
			return null;
		}

		var ctx = canvas.getContext('2d');
		var maskCanvas = document.createElement('canvas');
		var maskCtx = maskCanvas.getContext('2d');
		var w = 0;
		var h = 0;
		var mw = 0;
		var mh = 0;
		var dpr = 1;
		var animId = 0;
		var embers = [];
		var noiseSeed = Math.random() * 1000;
		var activeOutgoing = null;

		function clearOutgoingMask() {
			if (!activeOutgoing) {
				return;
			}
			activeOutgoing.style.maskImage = '';
			activeOutgoing.style.webkitMaskImage = '';
			activeOutgoing.style.maskSize = '';
			activeOutgoing.style.webkitMaskSize = '';
			activeOutgoing.style.maskMode = '';
			activeOutgoing = null;
		}

		function resize() {
			dpr = Math.min(window.devicePixelRatio || 1, 2);
			var rect = hero.getBoundingClientRect();
			w = Math.max(1, Math.floor(rect.width));
			h = Math.max(1, Math.floor(rect.height));
			canvas.width = Math.floor(w * dpr);
			canvas.height = Math.floor(h * dpr);
			canvas.style.width = w + 'px';
			canvas.style.height = h + 'px';
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			mw = Math.max(96, Math.floor(w / 8));
			mh = Math.max(54, Math.floor(h / 8));
			maskCanvas.width = mw;
			maskCanvas.height = mh;
		}

		function stop() {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = 0;
			}
			wrap.classList.remove('is-active');
			ctx.clearRect(0, 0, w, h);
			embers = [];
			clearOutgoingMask();
		}

		function spawnEmbers(edgePoints, intensity) {
			var count = Math.min(8, Math.floor(edgePoints.length * 0.08 * intensity));
			for (var i = 0; i < count; i++) {
				var p = edgePoints[Math.floor(Math.random() * edgePoints.length)];
				if (!p) {
					continue;
				}
				embers.push({
					x: p.x + (Math.random() - 0.5) * 8,
					y: p.y + (Math.random() - 0.5) * 8,
					vx: (Math.random() - 0.5) * 0.6,
					vy: -0.8 - Math.random() * 1.4,
					life: 1,
					decay: 0.012 + Math.random() * 0.02,
					r: 1.2 + Math.random() * 2.2,
				});
			}
			if (embers.length > 80) {
				embers = embers.slice(embers.length - 80);
			}
		}

		function applyMaskToOutgoing(progress) {
			if (!activeOutgoing || !maskCtx) {
				return;
			}

			var threshold = 1.15 - progress * 1.35;
			var imageData = maskCtx.createImageData(mw, mh);
			var data = imageData.data;

			for (var y = 0; y < mh; y++) {
				for (var x = 0; x < mw; x++) {
					var nx = x / mw;
					var ny = y / mh;
					var n = fbm(nx * 3.2 + noiseSeed, ny * 2.4 + noiseSeed * 0.3);
					var radial = Math.sqrt(Math.pow(nx - 0.5, 2) + Math.pow(ny - 0.55, 2));
					var field = n * 0.72 + (1 - radial) * 0.28;
					var burn = field - threshold;
					/* Luminance mask: white = keep outgoing, black = reveal incoming */
					var keep = burn <= 0 ? 255 : Math.max(0, Math.floor(255 * (1 - Math.min(1, burn * 3.2))));
					var idx = (y * mw + x) * 4;
					data[idx] = keep;
					data[idx + 1] = keep;
					data[idx + 2] = keep;
					data[idx + 3] = 255;
				}
			}

			maskCtx.putImageData(imageData, 0, 0);

			var url = maskCanvas.toDataURL('image/png');
			activeOutgoing.style.maskImage = 'url("' + url + '")';
			activeOutgoing.style.webkitMaskImage = 'url("' + url + '")';
			activeOutgoing.style.maskSize = '100% 100%';
			activeOutgoing.style.webkitMaskSize = '100% 100%';
			activeOutgoing.style.maskMode = 'luminance';
		}

		function drawGlowFrame(progress) {
			ctx.clearRect(0, 0, w, h);
			var cols = Math.max(48, Math.floor(w / 10));
			var rows = Math.max(28, Math.floor(h / 10));
			var cellW = w / cols;
			var cellH = h / rows;
			var threshold = 1.15 - progress * 1.35;
			var edgePoints = [];

			for (var y = 0; y < rows; y++) {
				for (var x = 0; x < cols; x++) {
					var nx = x / cols;
					var ny = y / rows;
					var n = fbm(nx * 3.2 + noiseSeed, ny * 2.4 + noiseSeed * 0.3);
					var radial = Math.sqrt(Math.pow(nx - 0.5, 2) + Math.pow(ny - 0.55, 2));
					var field = n * 0.72 + (1 - radial) * 0.28;
					var burn = field - threshold;

					if (burn <= 0) {
						continue;
					}

					var intensity = Math.min(1, burn * 2.4);
					var px = x * cellW;
					var py = y * cellH;

					/* Leading edge only — thin burn rim */
					if (intensity > 0.55) {
						continue;
					}

					edgePoints.push({ x: px + cellW * 0.5, y: py + cellH * 0.5 });

					ctx.globalCompositeOperation = 'source-over';
					ctx.fillStyle = 'rgba(45, 10, 78, ' + (0.2 + intensity * 0.45) + ')';
					ctx.fillRect(px, py, cellW + 1, cellH + 1);

					ctx.globalCompositeOperation = 'lighter';
					var mid = Math.min(1, intensity / 0.45);
					var gold = Math.max(0, (intensity - 0.25) / 0.35);
					ctx.fillStyle = 'rgba(139, 47, 201, ' + (0.35 + mid * 0.5) + ')';
					ctx.fillRect(px, py, cellW + 1, cellH + 1);
					if (gold > 0) {
						ctx.fillStyle = 'rgba(201, 151, 58, ' + (gold * 0.7) + ')';
						ctx.fillRect(px + cellW * 0.1, py + cellH * 0.1, cellW * 0.8, cellH * 0.8);
					}
				}
			}

			if (progress > 0.08 && progress < 0.92) {
				spawnEmbers(edgePoints, progress);
			}

			ctx.globalCompositeOperation = 'lighter';
			for (var i = embers.length - 1; i >= 0; i--) {
				var e = embers[i];
				e.x += e.vx;
				e.y += e.vy;
				e.vy -= 0.02;
				e.life -= e.decay;
				if (e.life <= 0) {
					embers.splice(i, 1);
					continue;
				}
				var a = e.life;
				var grd = ctx.createRadialGradient(e.x, e.y, 0, e.x, e.y, e.r * 3);
				grd.addColorStop(0, 'rgba(201, 151, 58, ' + (0.9 * a) + ')');
				grd.addColorStop(0.45, 'rgba(139, 47, 201, ' + (0.55 * a) + ')');
				grd.addColorStop(1, 'rgba(45, 10, 78, 0)');
				ctx.fillStyle = grd;
				ctx.beginPath();
				ctx.arc(e.x, e.y, e.r * 3, 0, Math.PI * 2);
				ctx.fill();
			}
			ctx.globalCompositeOperation = 'source-over';
		}

		function play(durationMs, onDone, outgoingEl) {
			stop();
			noiseSeed = Math.random() * 1000;
			activeOutgoing = outgoingEl || null;
			resize();
			wrap.classList.add('is-active');
			var start = performance.now();
			var maskEvery = 2;
			var frame = 0;

			function tick(now) {
				var t = Math.min(1, (now - start) / durationMs);
				var eased = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
				if (frame % maskEvery === 0) {
					applyMaskToOutgoing(eased);
				}
				drawGlowFrame(eased);
				frame++;
				if (t < 1) {
					animId = requestAnimationFrame(tick);
				} else {
					clearOutgoingMask();
					wrap.classList.remove('is-active');
					ctx.clearRect(0, 0, w, h);
					embers = [];
					animId = 0;
					if (typeof onDone === 'function') {
						onDone();
					}
				}
			}

			animId = requestAnimationFrame(tick);
		}

		window.addEventListener('resize', function () {
			if (wrap.classList.contains('is-active')) {
				resize();
			}
		}, { passive: true });

		return { play: play, stop: stop, resize: resize };
	}

	/* ------------------------------------------------------------------ */
	/* Hero slider                                                         */
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
		var TRANSITION_MS = reduced ? 300 : 1200;
		var COOLDOWN_MS = reduced ? 320 : 1250;
		var heroCfg = (typeof window.tqsHeroData === 'object' && window.tqsHeroData) ? window.tqsHeroData : {};
		var autoplayEnabled = (typeof heroCfg.autoplay === 'undefined') ? true : !!heroCfg.autoplay;
		var AUTO_MS = Math.max(2000, parseInt(heroCfg.autoplayMs, 10) || 6000);
		var SWIPE_MIN = 48;
		var hasGsap = typeof gsap !== 'undefined';
		var burn = (!reduced) ? createBurnController(hero) : null;

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

		function crossfadeOnly(outgoing, incoming, duration, onDone) {
			var outContent = outgoing.querySelector('.tqs-hero-content');
			var inContent = incoming.querySelector('.tqs-hero-content');
			var ease = 'power1.out';

			if (!hasGsap) {
				incoming.style.opacity = '0';
				outgoing.style.transition = 'opacity ' + duration + 's ease';
				incoming.style.transition = 'opacity ' + duration + 's ease';
				window.requestAnimationFrame(function () {
					outgoing.style.opacity = '0';
					incoming.style.opacity = '1';
				});
				window.setTimeout(onDone, duration * 1000);
				return;
			}

			var tl = gsap.timeline({ onComplete: onDone });
			tl.to(outgoing, { opacity: 0, duration: duration, ease: ease }, 0);
			tl.fromTo(incoming, { opacity: 0 }, { opacity: 1, duration: duration, ease: ease }, 0);
			if (outContent) {
				tl.to(outContent, { opacity: 0, duration: duration * 0.55, ease: 'power1.in' }, 0);
			}
			if (inContent) {
				tl.fromTo(inContent, { opacity: 0 }, { opacity: 1, duration: duration * 0.7, ease: 'power1.out' }, duration * 0.25);
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
				index = nextIndex;
				window.setTimeout(function () {
					transitioning = false;
				}, Math.max(0, COOLDOWN_MS - TRANSITION_MS));
			}

			if (reduced || !burn) {
				crossfadeOnly(outgoing, incoming, duration, finish);
				return;
			}

			/* Incoming under; outgoing masked away to reveal burn gaps */
			if (hasGsap) {
				gsap.set(incoming, { opacity: 1 });
				gsap.set(outgoing, { opacity: 1 });
				if (inContent) {
					gsap.set(inContent, { opacity: 0 });
				}
				if (inBg) {
					gsap.fromTo(inBg, { scale: 1.04 }, { scale: 1, duration: duration, ease: 'power2.out' });
				}
				if (outContent) {
					gsap.to(outContent, { opacity: 0, duration: duration * 0.45, ease: 'power1.in', delay: duration * 0.2 });
				}
				if (inContent) {
					gsap.to(inContent, { opacity: 1, duration: duration * 0.5, ease: 'power1.out', delay: duration * 0.4 });
				}
			} else {
				incoming.style.opacity = '1';
				outgoing.style.opacity = '1';
			}

			burn.play(TRANSITION_MS, finish, outgoing);
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

		/* Refresh after layout/images settle so triggers fire correctly */
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
