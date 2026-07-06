/**
 * TQS Gallery — show/hide "Na-afbeelding" field on tqs_gallery_item edit screen.
 */
(function ($) {
	'use strict';

	$(function () {
		var $styleSelect = $('#tqs_gallery_display_style');
		var $afterWrap = $('#tqs_gallery_after_image_wrap');

		if (!$styleSelect.length || !$afterWrap.length) {
			return;
		}

		function toggleAfterImageField() {
			$afterWrap.toggle($styleSelect.val() === 'before_after');
		}

		$styleSelect.on('change', toggleAfterImageField);
		toggleAfterImageField();
	});
})(jQuery);
