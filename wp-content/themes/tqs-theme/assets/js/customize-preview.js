/**
 * TQS Theme — Customizer live preview (postMessage).
 *
 * @package tqs-theme
 */
(function ($) {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) {
		return;
	}

	function galleryGrid() {
		return $('#tqsGalleryGrid');
	}

	wp.customize('tqs_gallery_columns', function (value) {
		value.bind(function (newVal) {
			var cols = parseInt(newVal, 10) === 3 ? 3 : 4;
			var $grid = galleryGrid();
			if (!$grid.length) {
				return;
			}
			$grid.removeClass('cols-3 cols-4').addClass('cols-' + cols);
		});
	});

	wp.customize('tqs_gallery_show_filters', function (value) {
		value.bind(function (newVal) {
			$('#tqsGalleryFilters').toggle(!!newVal);
		});
	});

	wp.customize('tqs_gallery_lightbox_enabled', function (value) {
		value.bind(function (newVal) {
			var $grid = galleryGrid();
			if (!$grid.length) {
				return;
			}
			$grid.attr('data-lightbox', newVal ? '1' : '0');
		});
	});
})(jQuery);
