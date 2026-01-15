const BetterPostUi = {};
BetterPostUi.Publish = (($) => {
	function Publish() {
		if ($('#misc-publishing-actions').length === 0) {
			return;
		}

		this.initDatepicker();
		this.handleEvents();
	}

	Publish.prototype.handleEvents = () => {
		$('.save-timestamp', '#timestampdiv').on('click', (event) => {
			var attemptedDate,
				currentDate,
				postVisibility = $('#post-visibility-select').find('input:radio:checked').val(),
				aa = $('#aa').val(),
				mm = $('#mm').val(),
				jj = $('#jj').val(),
				hh = $('#hh').val(),
				mn = $('#mn').val();

			attemptedDate = new Date(aa, mm - 1, jj, hh, mn);
			currentDate = new Date(
				$('#cur_aa').val(),
				$('#cur_mm').val() - 1,
				$('#cur_jj').val(),
				$('#cur_hh').val(),
				$('#cur_mn').val(),
			);

			if (attemptedDate > currentDate && postVisibility == 'private') {
				setTimeout(() => {
					$('#publish').val(postL10n.schedule);
				}, 10);
			}
		});
	};

	Publish.prototype.initDatepicker = () => {
		$('#aa, #mm, #jj').hide();

		var timestamp_wrap_text = $('.misc-pub-curtime .timestamp-wrap').html();
		timestamp_wrap_text = timestamp_wrap_text.replace(/(,|@)/g, '');
		$('.misc-pub-curtime .timestamp-wrap').html(timestamp_wrap_text);

		$('#hh').before('<span class="municipio-admin-datepicker-time dashicons dashicons-clock"></span>');

		$('#timestampdiv').prepend('<div id="timestamp-datepicker" class="municipio-admin-datepicker"></div>');
		$('#timestamp-datepicker').datepicker({
			firstDay: 1,
			dateFormat: 'yy-mm-dd',
			onSelect: (selectedDate) => {
				selectedDate = selectedDate.split('-');

				$('#aa').val(selectedDate[0]);
				$('#mm').val(selectedDate[1]);
				$('#jj').val(selectedDate[2]);
			},
		});

		var initialDate = $('#aa').val() + '-' + $('#mm').val() + '-' + $('#jj').val();
		$('#timestamp-datepicker').datepicker('setDate', initialDate);
	};

	return new Publish();
})(jQuery);
