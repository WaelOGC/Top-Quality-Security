/**
 * TQS Hero Settings meta box — show/hide groups + media uploader.
 * Enqueued only on Page edit screens (see inc/meta-boxes/hero-meta-box.php).
 */
(function ($) {
	'use strict';

	$(function () {
		var $root = $('#tqs-hero-settings');
		if (!$root.length) {
			return;
		}

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

		/* Media uploader — one frame reused per field */
		$root.on('click', '.tqs-hero-upload-btn', function (e) {
			e.preventDefault();

			var $field = $(this).closest('[data-tqs-media-field]');
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

		$root.on('click', '.tqs-hero-remove-btn', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-tqs-media-field]');
			$field.find('.tqs-hero-image-id').val('');
			$field.find('.tqs-hero-image-preview').empty();
			$(this).prop('hidden', true);
		});
	});
})(jQuery);
