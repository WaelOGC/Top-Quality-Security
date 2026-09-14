/**
 * TQS Hero Settings meta box — show/hide groups + media uploader.
 * Also handles media fields in About Page Content (shared [data-tqs-media-field]).
 */
(function ($) {
	'use strict';

	$(function () {
		var $root = $('#tqs-hero-settings');

		if ($root.length) {
			var $typeSelect = $('#tqs_hero_type');
			var $homepage = $('#tqs-hero-group-homepage');
			var $page = $('#tqs-hero-group-page');

			function syncHeroTypeGroups() {
				var type = $typeSelect.val();
				$homepage.prop('hidden', type !== 'homepage_hero');
				$page.prop('hidden', type !== 'page_hero');
			}

			$typeSelect.on('change', syncHeroTypeGroups);
			syncHeroTypeGroups();

			$root.on('change', '.tqs-hero-override-btns', function () {
				var targetId = $(this).data('tqs-override-target');
				var $fields = targetId ? $('#' + targetId) : $();
				if (!$fields.length) {
					return;
				}
				$fields.prop('hidden', !this.checked);
			});
		}

		/* Media uploader — works for Hero Settings and About story image */
		$(document).on('click', '.tqs-hero-upload-btn', function (e) {
			e.preventDefault();

			var $field = $(this).closest('[data-tqs-media-field]');
			if (!$field.length) {
				return;
			}
			var $input = $field.find('.tqs-hero-image-id');
			var $preview = $field.find('.tqs-hero-image-preview');
			var $remove = $field.find('.tqs-hero-remove-btn');
			var frame = $field.data('tqsMediaFrame');

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Kies afbeelding',
				button: { text: 'Gebruik afbeelding' },
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
				$input.val(att.id);
				$preview.html('<img src="' + url + '" alt="">');
				$remove.prop('hidden', false);
			});

			$field.data('tqsMediaFrame', frame);
			frame.open();
		});

		$(document).on('click', '.tqs-hero-remove-btn', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-tqs-media-field]');
			$field.find('.tqs-hero-image-id').val('');
			$field.find('.tqs-hero-image-preview').empty();
			$(this).prop('hidden', true);
		});
	});
})(jQuery);
