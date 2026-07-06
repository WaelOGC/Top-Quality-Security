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

		/* Desktop dropdown (hover) */
		$('.tqs-nav-dropdown-wrap').on('mouseenter', function () {
			$(this).find('.tqs-nav-dropdown').show();
		}).on('mouseleave', function () {
			$(this).find('.tqs-nav-dropdown').hide();
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
		   6. GALLERY LIGHTBOX (Fotogalerij)
		   ================================================================== */
		var $galleryTiles = $('#tqsGalleryGrid .tqs-gallery-tile:not([data-placeholder="1"])');
		var $lightbox = $('#tqsLightbox');
		var $lightboxImg = $('#tqsLightboxImg');
		var lightboxIndex = 0;

		function openLightbox(index) {
			if (!$galleryTiles.length) return;
			lightboxIndex = index;
			var full = $galleryTiles.eq(lightboxIndex).data('full');
			$lightboxImg.attr('src', full);
			$lightbox.addClass('is-open');
		}
		function closeLightbox() {
			$lightbox.removeClass('is-open');
		}

		$galleryTiles.on('click', function () {
			openLightbox($(this).data('index'));
		});
		$('#tqsLightboxClose').on('click', closeLightbox);
		$lightbox.on('click', function (e) {
			if (e.target === this) closeLightbox();
		});
		$('#tqsLightboxPrev').on('click', function () {
			openLightbox((lightboxIndex - 1 + $galleryTiles.length) % $galleryTiles.length);
		});
		$('#tqsLightboxNext').on('click', function () {
			openLightbox((lightboxIndex + 1) % $galleryTiles.length);
		});
		$(document).on('keydown', function (e) {
			if (!$lightbox.hasClass('is-open')) return;
			if (e.key === 'Escape') closeLightbox();
			if (e.key === 'ArrowLeft') $('#tqsLightboxPrev').click();
			if (e.key === 'ArrowRight') $('#tqsLightboxNext').click();
		});

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

	});

})(jQuery);
