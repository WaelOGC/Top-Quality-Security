/**
 * TQS Theme — main frontend JavaScript
 * Depends on jQuery (WordPress bundled)
 */
(function ($) {
	'use strict';

	$(function () {

		/* ==================================================================
		   1. MOBILE MENU (hamburger)
		   ================================================================== */
		var $hamburger = $('#tqsHamburger');
		var $mobileMenu = $('#tqsMobileMenu');

		$hamburger.on('click', function () {
			var isOpen = $mobileMenu.hasClass('is-open');
			$mobileMenu.toggleClass('is-open', !isOpen);
			$hamburger.attr('aria-expanded', !isOpen);
		});

		$('#tqsMobileServicesToggle').on('click', function () {
			var $sub = $('#tqsMobileServicesSub');
			var isOpen = $sub.hasClass('is-open');
			$sub.toggleClass('is-open', !isOpen);
			$('#tqsMobileServicesChevron').text(isOpen ? '▼' : '▲');
		});

		/* Desktop nav dropdowns (click toggle) */
		var $navDropdownWraps = $('.tqs-nav-dropdown-wrap');

		function closeNavDropdowns() {
			$navDropdownWraps.removeClass('is-open');
			$navDropdownWraps.each(function () {
				var $toggle = $(this).children('a, button').first();
				if ($toggle.length) {
					$toggle.attr('aria-expanded', 'false');
				}
			});
		}

		$navDropdownWraps.each(function () {
			var $wrap = $(this);
			var $toggle = $wrap.children('a, button').first();
			if (!$toggle.length) {
				return;
			}
			if (!$toggle.attr('aria-expanded')) {
				$toggle.attr('aria-expanded', 'false');
			}

			$toggle.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var isOpen = $wrap.hasClass('is-open');
				closeNavDropdowns();
				if (!isOpen) {
					$wrap.addClass('is-open');
					$toggle.attr('aria-expanded', 'true');
				}
			});
		});

		$navDropdownWraps.on('click', '.tqs-nav-dropdown a', function () {
			closeNavDropdowns();
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('.tqs-nav-dropdown-wrap').length) {
				closeNavDropdowns();
			}
		});

		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') {
				closeNavDropdowns();
			}
		});

		/* ==================================================================
		   2. HERO SLIDER
		   ================================================================== */
		var $heroSlides = $('#tqsHero .tqs-hero-slide');
		var $heroDots = $('#tqsHeroDots .tqs-slide-dot');
		var heroIndex = 0;
		var heroTimer = null;
		var heroTransitions = ['tqs-anim-crossfade', 'tqs-anim-from-right', 'tqs-anim-from-left'];
		var heroPaused = false;

		function goToHeroSlide(i) {
			if (!$heroSlides.length) return;
			heroIndex = (i + $heroSlides.length) % $heroSlides.length;
			$heroSlides.removeClass('is-active tqs-anim-crossfade tqs-anim-from-right tqs-anim-from-left');
			var $target = $heroSlides.eq(heroIndex);
			$target.addClass('is-active ' + heroTransitions[heroIndex % heroTransitions.length]);
			$heroDots.removeClass('is-active').eq(heroIndex).addClass('is-active');
		}

		function startHeroTimer() {
			clearInterval(heroTimer);
			var interval = (window.tqsData && tqsData.heroAutoplayMs) ? tqsData.heroAutoplayMs : 5500;
			var autoplay = (window.tqsData && typeof tqsData.heroAutoplay !== 'undefined') ? tqsData.heroAutoplay : true;
			if (!autoplay) return;
			heroTimer = setInterval(function () {
				if (!heroPaused) goToHeroSlide(heroIndex + 1);
			}, interval);
		}

		if ($heroSlides.length) {
			$('#tqsHeroPrev').on('click', function () { goToHeroSlide(heroIndex - 1); });
			$('#tqsHeroNext').on('click', function () { goToHeroSlide(heroIndex + 1); });
			$heroDots.on('click', function () { goToHeroSlide($(this).data('goto')); });
			$('#tqsHero').on('mouseenter', function () { heroPaused = true; })
				.on('mouseleave', function () { heroPaused = false; });
			startHeroTimer();
		}

		/* ==================================================================
		   3. SERVICES SLIDER (homepage) — responsive re-chunking
		   ================================================================== */
		var $serviceTracks = $('#tqsServicesTrack .tqs-services-track');
		var $serviceDots = $('#tqsServiceDots .tqs-service-dot');
		var serviceTransitions = ['tqs-anim-crossfade', 'tqs-anim-from-right', 'tqs-anim-zoom'];
		var servicePage = 0;

		function goToServicePage(i) {
			if (!$serviceTracks.length) return;
			servicePage = (i + $serviceTracks.length) % $serviceTracks.length;
			$serviceTracks.removeClass('is-active tqs-anim-crossfade tqs-anim-from-right tqs-anim-zoom');
			$serviceTracks.eq(servicePage).addClass('is-active ' + serviceTransitions[servicePage % serviceTransitions.length]);
			$serviceDots.removeClass('is-active').eq(servicePage).addClass('is-active');
			$('#tqsServiceCounter').text(servicePage + 1);
		}

		if ($serviceTracks.length) {
			$('#tqsServicePrev').on('click', function () { goToServicePage(servicePage - 1); });
			$('#tqsServiceNext').on('click', function () { goToServicePage(servicePage + 1); });
			$serviceDots.on('click', function () { goToServicePage($(this).data('goto')); });

			// On mobile, split any 2-card slide into two single-card slides.
			var mql = window.matchMedia('(max-width: 767px)');
			function applyMobileServiceLayout(isMobile) {
				if (isMobile) {
					$serviceTracks.addClass('single-col');
				} else {
					$serviceTracks.removeClass('single-col');
				}
			}
			applyMobileServiceLayout(mql.matches);
			if (mql.addEventListener) {
				mql.addEventListener('change', function (e) { applyMobileServiceLayout(e.matches); });
			}
		}

		/* ==================================================================
		   4. GDPR COOKIE BANNER
		   ================================================================== */
		var $cookieBanner = $('#tqsCookieBanner');
		if ($cookieBanner.length) {
			var cookieName = 'tqs_cookie_consent';
			function getCookie(name) {
				var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
				return match ? match[2] : null;
			}
			function setCookie(name, value, days) {
				var d = new Date();
				d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
				document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/';
			}
			if (!getCookie(cookieName)) {
				$cookieBanner.addClass('is-visible');
			}
			$('#tqsCookieAccept').on('click', function () {
				setCookie(cookieName, 'accepted', (window.tqsData && tqsData.cookieExpiry) || 180);
				$cookieBanner.removeClass('is-visible');
			});
			$('#tqsCookieDecline').on('click', function () {
				setCookie(cookieName, 'declined', (window.tqsData && tqsData.cookieExpiry) || 180);
				$cookieBanner.removeClass('is-visible');
			});
		}

		/* ==================================================================
		   5. BACK TO TOP
		   ================================================================== */
		var $backToTop = $('#tqsBackToTop');
		if ($backToTop.length) {
			$(window).on('scroll', function () {
				$backToTop.toggleClass('is-visible', $(window).scrollTop() > 500);
			});
			$backToTop.on('click', function () {
				$('html, body').animate({ scrollTop: 0 }, 400);
			});
		}

		/* Sticky header shadow intensifies on scroll (subtle enhancement) */
		var $header = $('.tqs-header');
		if ($header.length) {
			$(window).on('scroll', function () {
				if ($(window).scrollTop() > 20) {
					$header.css('box-shadow', '0 6px 26px rgba(0,0,0,0.25)');
				} else {
					$header.css('box-shadow', '0 4px 20px rgba(0,0,0,0.15)');
				}
			});
		}

		/* ==================================================================
		   5b. GALLERY CATEGORY FILTER (Fotogalerij)
		   ================================================================== */
		var $galleryFilters = $('#tqsGalleryFilters');
		if ($galleryFilters.length) {
			$galleryFilters.on('click', '.tqs-gallery-filter', function () {
				var filter = $(this).data('filter');
				$galleryFilters.find('.tqs-gallery-filter').removeClass('is-active');
				$(this).addClass('is-active');

				var $tiles = $('#tqsGalleryGrid .tqs-gallery-tile').not('[data-placeholder="1"]');
				if (filter === 'all') {
					$tiles.removeClass('is-hidden');
					return;
				}

				$tiles.each(function () {
					var cats = String($(this).attr('data-category') || '').split(/\s+/).filter(Boolean);
					$(this).toggleClass('is-hidden', cats.indexOf(filter) === -1);
				});
			});
		}

		/* ==================================================================
		   5c. GALLERY BEFORE/AFTER SLIDER (Fotogalerij)
		   ================================================================== */
		var $beforeAfterSliders = $('.tqs-gallery-tile--before-after .tqs-ba-slider');
		if ($beforeAfterSliders.length) {
			$beforeAfterSliders.each(function () {
				var $slider = $(this);
				var $handle = $slider.find('.tqs-ba-handle');
				var dragging = false;

				function setPosition(clientX) {
					var rect = $slider[0].getBoundingClientRect();
					if (!rect.width) {
						return;
					}
					var x = clientX - rect.left;
					var pct = Math.max(0, Math.min(100, (x / rect.width) * 100));
					$slider[0].style.setProperty('--ba-pos', pct + '%');
					$handle.css('left', pct + '%');
				}

				function pointerX(e) {
					if (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches.length) {
						return e.originalEvent.touches[0].clientX;
					}
					if (e.touches && e.touches.length) {
						return e.touches[0].clientX;
					}
					return e.clientX;
				}

				function onStart(e) {
					dragging = true;
					e.preventDefault();
					e.stopPropagation();
					setPosition(pointerX(e));
				}

				function onMove(e) {
					if (!dragging) {
						return;
					}
					e.preventDefault();
					setPosition(pointerX(e));
				}

				function onEnd() {
					dragging = false;
				}

				$handle.on('mousedown touchstart', onStart);
				$slider.on('mousedown touchstart', function (e) {
					if ($(e.target).closest('.tqs-ba-handle').length) {
						return;
					}
					onStart(e);
				});

				$(document).on('mousemove touchmove', onMove);
				$(document).on('mouseup touchend touchcancel', onEnd);
			});
		}

		/* ==================================================================
		   6. GALLERY LIGHTBOX (Fotogalerij) — grid/featured tiles only when enabled
		   ================================================================== */
		var $galleryGrid = $('#tqsGalleryGrid');
		var $lightbox = $('#tqsLightbox');
		if ($galleryGrid.length && $lightbox.length) {
			var $galleryTiles = $galleryGrid.find('.tqs-gallery-tile:not([data-placeholder="1"]):not([data-style="before_after"])');
			var $lightboxImg = $('#tqsLightboxImg');
			var lightboxIndex = 0;

			function galleryLightboxEnabled() {
				return String($galleryGrid.attr('data-lightbox')) !== '0';
			}

			function openLightbox(index) {
				if (!galleryLightboxEnabled() || !$galleryTiles.length) {
					return;
				}
				lightboxIndex = index;
				var full = $galleryTiles.eq(lightboxIndex).data('full');
				$lightboxImg.attr('src', full);
				$lightbox.addClass('is-open');
			}
			function closeLightbox() {
				$lightbox.removeClass('is-open');
			}

			$galleryTiles.on('click', function () {
				if (!galleryLightboxEnabled()) {
					return;
				}
				openLightbox($(this).data('index'));
			});
			$('#tqsLightboxClose').on('click', closeLightbox);
			$lightbox.on('click', function (e) {
				if (e.target === this) {
					closeLightbox();
				}
			});
			$('#tqsLightboxPrev').on('click', function () {
				openLightbox((lightboxIndex - 1 + $galleryTiles.length) % $galleryTiles.length);
			});
			$('#tqsLightboxNext').on('click', function () {
				openLightbox((lightboxIndex + 1) % $galleryTiles.length);
			});
			$(document).on('keydown', function (e) {
				if (!$lightbox.hasClass('is-open')) {
					return;
				}
				if (e.key === 'Escape') {
					closeLightbox();
				}
				if (e.key === 'ArrowLeft') {
					$('#tqsLightboxPrev').click();
				}
				if (e.key === 'ArrowRight') {
					$('#tqsLightboxNext').click();
				}
			});
		}

		/* ==================================================================
		   7. CONTACT FORM — AJAX SUBMIT
		   ================================================================== */
		var $contactForm = $('#tqsContactForm');
		if ($contactForm.length && window.tqsData) {
			$contactForm.on('submit', function (e) {
				e.preventDefault();
				var $submit = $('#tqsFormSubmit');
				var $message = $('#tqsFormMessage');
				var originalText = $submit.text();

				$submit.prop('disabled', true).text('Verzenden...');
				$message.removeClass('is-success is-error').hide();

				$.ajax({
					url: tqsData.ajaxUrl,
					method: 'POST',
					data: {
						action: 'tqs_contact_submit',
						nonce: tqsData.nonce,
						first_name: $('#tqs_first_name').val(),
						last_name: $('#tqs_last_name').val(),
						email: $('#tqs_email_field').val(),
						phone: $('#tqs_phone_field').val(),
						service: $('#tqs_service_field').val(),
						message: $('#tqs_message_field').val()
					}
				}).done(function (response) {
					if (response.success) {
						$message.addClass('is-success').text(response.data.message).show();
						$contactForm[0].reset();
					} else {
						$message.addClass('is-error').text(response.data.message).show();
					}
				}).fail(function () {
					$message.addClass('is-error').text((window.tqsData && tqsData.formErrorMsg) || 'Er ging iets mis. Probeer het later opnieuw of bel ons direct.').show();
				}).always(function () {
					$submit.prop('disabled', false).text(originalText);
				});
			});
		}

		/* ==================================================================
		   8. REVIEW FORM — AJAX SUBMIT
		   ================================================================== */
		var $reviewForm = $('#tqsReviewForm');
		if ($reviewForm.length && window.tqsData) {
			var $reviewText = $('#tqs_review_text');
			var $charCount = $('#tqsReviewCharCount');

			$reviewText.on('input', function () {
				var val = $(this).val();
				if (val.length > 1000) {
					val = val.substring(0, 1000);
					$(this).val(val);
				}
				$charCount.text(val.length);
			});

			$reviewForm.on('submit', function (e) {
				e.preventDefault();
				var $submit = $('#tqsReviewFormSubmit');
				var $message = $('#tqsReviewFormMessage');
				var originalText = $submit.text();

				$submit.prop('disabled', true).text('Verzenden...');
				$message.removeClass('is-success is-error').hide().attr('hidden', 'hidden');

				$.ajax({
					url: tqsData.ajaxUrl,
					method: 'POST',
					data: {
						action: 'tqs_submit_review',
						nonce: tqsData.reviewNonce,
						name: $('#tqs_review_name').val(),
						email: $('#tqs_review_email').val(),
						service_id: $('#tqs_review_service').val(),
						rating: $reviewForm.find('input[name="rating"]:checked').val(),
						text: $reviewText.val(),
						consent: $('#tqs_review_consent').is(':checked') ? 1 : 0,
						tqs_review_hp: $('#tqs_review_hp').val()
					}
				}).done(function (response) {
					if (response.success) {
						var successText = (response.data && response.data.message) ? response.data.message : tqsData.reviewSuccessMsg;
						$reviewForm.replaceWith(
							'<div class="tqs-form-message is-success" role="status">' + $('<div>').text(successText).html() + '</div>'
						);
					} else {
						var errorText = (response.data && response.data.message) ? response.data.message : tqsData.reviewErrorMsg;
						$message.addClass('is-error').text(errorText).show().removeAttr('hidden');
					}
				}).fail(function () {
					$message.addClass('is-error').text(tqsData.reviewErrorMsg || tqsData.formErrorMsg).show().removeAttr('hidden');
				}).always(function () {
					if ($submit.length) {
						$submit.prop('disabled', false).text(originalText);
					}
				});
			});
		}

	});

})(jQuery);
