function get_selected_text()
{
	var sel = '';
	if (window.getSelection && !is_ie)
	{
		sel = window.getSelection().toString();
	}
	else if (document.getSelection && !is_ie)
	{
		sel = document.getSelection();
	}
	else if (document.selection)
	{
		sel = document.selection.createRange().text;
	}
	return jQuery.trim(sel);
}

(function($) {
phpbb.override_callback_vote_poll = function(res) {
	if (typeof res.success !== 'undefined') {
		var poll = $('.topic_poll');
		var panel = poll.find('.panel');
		var resultsVisible = poll.find('dl:first-child .resultbar').is(':visible');
		var mostVotes = 0;

		// Set min-height to prevent the page from jumping when the content changes
		var updatePanelHeight = function (height) {
			height = (typeof height === 'undefined') ? panel.find('.inner').outerHeight() : height;
			panel.css('min-height', height);
		};
		updatePanelHeight();

		// Remove the View results link
		if (!resultsVisible) {
			poll.find('.poll_view_results').hide(500);
		}

		poll.find('input:submit[data-clicked]').removeAttr('data-clicked');

		if (!res.can_vote) {
			poll.find('.polls, .poll_max_votes, .poll_vote, .poll_option_select').fadeOut(500, function () {
				poll.find('.resultbar, .poll_option_percent, .poll_total_votes, .poll_voters_box').show();
			});
		} else {
			// If the user can still vote, simply slide down the results
			poll.find('.resultbar, .poll_option_percent, .poll_total_votes, .poll_voters_box').show(500);
			poll.find('input[name="update"]').val(res.SUBMIT);
			if (res.unvote) {
				poll.find('input[name="unvote"]').hide();
			} else {
				poll.find('input[name="unvote"]').show();
			}
		}

		// Get the votes count of the highest poll option
		poll.find('[data-poll-option-id]').each(function() {
			var option = $(this);
			var optionId = option.attr('data-poll-option-id');
			mostVotes = (res.vote_counts[optionId] >= mostVotes) ? res.vote_counts[optionId] : mostVotes;
		});

		// Update the total votes count
		poll.find('.poll_total_vote_cnt').html(res.total_votes);

		// Update each option
		poll.find('[data-poll-option-id]').each(function() {
			var $this = $(this);
			var optionId = $this.attr('data-poll-option-id');
			var voted = (typeof res.user_votes[optionId] !== 'undefined');
			var mostVoted = (res.vote_counts[optionId] === mostVotes);
			var percent = (!res.total_votes) ? 0 : Math.round((res.vote_counts[optionId] / res.total_votes) * 100);
			var percentRel = (mostVotes === 0) ? 0 : Math.round((res.vote_counts[optionId] / mostVotes) * 100);

			$this.toggleClass('voted', voted);
			$this.toggleClass('most-votes', mostVoted);

			if (res.poll_voters[optionId]) {
				$this.next('.poll_voters_box').html(res.poll_voters[optionId]);
			} else {
				$this.next('.poll_voters_box').html('');
			}

			// Update the bars
			var bar = $this.find('.resultbar div');
			var barTimeLapse = (res.can_vote) ? 500 : 1500;
			var newBarClass = (percent === 100) ? 'pollbar5' : 'pollbar' + (Math.floor(percent / 20) + 1);

			setTimeout(function () {
				bar.animate({ width: percentRel + '%' }, 500)
					.removeClass('pollbar1 pollbar2 pollbar3 pollbar4 pollbar5')
					.addClass(newBarClass)
					.html(res.vote_counts[optionId]);

				var percentText = percent ? percent + '%' : res.NO_VOTES;
				$this.find('.poll_option_percent').html(percentText);
			}, barTimeLapse);
		});

		if (!res.can_vote) {
			poll.find('.polls').delay(400).fadeIn(500);
		}

		// Display "Your vote has been cast." message. Disappears after 5 seconds.
		var confirmationDelay = (res.can_vote) ? 300 : 900;
		poll.find('.vote-submitted').text(res.VOTED).delay(confirmationDelay).slideDown(200, function() {
			if (resultsVisible) {
				updatePanelHeight();
			}

			$(this).delay(5000).fadeOut(500, function() {
				resizePanel(300);
			});
		});

		// Remove the gap resulting from removing options
		setTimeout(function() {
			resizePanel(500);
		}, 1500);

		var resizePanel = function (time) {
			var panelHeight = panel.height();
			var innerHeight = panel.find('.inner').outerHeight();

			if (panelHeight !== innerHeight) {
				panel.css({ minHeight: '', height: panelHeight })
					.animate({ height: innerHeight }, time, function () {
						panel.css({ minHeight: innerHeight, height: '' });
					});
			}
		};
	}
};
})(jQuery);

jQuery(document).ready(function () {
	$('.poll_view_results a').on('click', function(e) {
		e.preventDefault();

		var $poll = $(this).parents('.topic_poll');

		$poll.find('.resultbar, .poll_option_percent, .poll_total_votes, .poll_voters_box').show(500);
		$poll.find('.poll_view_results').hide(500);
	});

	var $poll_bittons = $('#poll_bittons');
	if ($poll_bittons.length > 0) {
		$('.poll_vote > dd').html($poll_bittons.contents());
		$('.polls input[type="submit"]').on('click', function () {
			$(this).attr('data-clicked', 'true');
		});
		phpbb.addAjaxCallback('vote_poll', phpbb.override_callback_vote_poll);
	}
});
