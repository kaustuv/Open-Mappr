$(document).ready(function() {
	var isFeedbackOpen = false;
	$('.user-feedback-holder .feedback-but').click(function() {
		if(isFeedbackOpen)
		{
			isFeedbackOpen = false;
			$(this).parent().animate({
				'left':$(this).parent().width()*-1-1
			})
		} else
		{
			isFeedbackOpen = true;
			$(this).parent().animate({
				'left':0
			})
		}
	})

	$('.user-feedback-holder .user-feedback-submit').click(function() {
		var fb = $('.user-feedback-holder textarea').val();
		if(fb == '')
		{
			alert('Please enter in some feedback before sending.')
			return;
		}
		//show saving text
		$('.user-feedback-holder .loading-text').text('Please Wait, Saving Feedback...');
		$('.user-feedback-holder .loading-text').show();
		//send feedback to db via ajax
		var data = 'feedback='+fb;
		data += '&url='+window.location.href;
		data += '&useragent='+navigator.userAgent;
		$.ajax({
			type:'POST',
			url:baseURL+'links/save_user_feedback',
			data:data,
			success: function(d) {
				$('.user-feedback-holder .loading-text').text('Feedback Saved!');
				//clear textarea
				$('.user-feedback-holder textarea').val('');
				$('.user-feedback-holder .loading-text').delay(500).fadeOut();
				isFeedbackOpen = false;
				$('.user-feedback-holder').animate({
					'left':$('.user-feedback-holder').width()*-1-1
				})
			}
		});
	})
})