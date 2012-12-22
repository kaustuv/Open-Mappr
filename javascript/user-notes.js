$(document).ready(function() {
	var isNotesOpen = false;
	$('.user-notes-holder .notes-but').click(function() {
		if(isNotesOpen)
		{
			isNotesOpen = false;
			$(this).parent().animate({
				'left':$(this).parent().width()*-1-1
			})
		} else
		{
			isNotesOpen = true;
			$(this).parent().animate({
				'left':0
			})
		}
	})

	$('.user-notes-holder .user-notes-submit').click(function() {
		//show saving text
		$('.user-notes-holder .loading-text').text('Please Wait, Saving Notes...');
		$('.user-notes-holder .loading-text').show();
		//send notes to db via ajax
		var data = 'notes='+$('.user-notes-holder textarea').val();
		$.ajax({
			type:'POST',
			url:baseURL+'links/save_user_notes',
			data:data,
			success: function(d) {
				$('.user-notes-holder .loading-text').text('Notes Saved!');
				$('.user-notes-holder .loading-text').delay(500).fadeOut();
			}
		});
	})
})