<script type="text/javascript">
$(document).ready(function() {
	if($('#vimeo-content').length == 0)
	{
		return;
	}

	var $vid = $('#vimeo-content');
	var $iframe = $vid.find('iframe');
	$iframe.attr('id','vimeo_video');
	$iframe.attr('src',$iframe.attr('src')+"&api=1&player_id=vimeo_video");
	$.fancybox.open($('#vimeo-content'),{
		afterClose:function() {
			//make sure video shuts the fuck up
			$('#vimeo-content iframe').remove();
		},
		afterShow:function() {
			initVideo();
		}
	});
	
	function initVideo()
	{

		$('#vimeo_video').each(function(){
		  Froogaloop(this).addEvent('ready', ready);
		});

		function ready(playerID){
		  // Add event listerns
		  // http://vimeo.com/api/docs/player-js#events
		  Froogaloop(playerID).addEvent('finish', function() {
		  	$.fancybox.close();
		  });
		  
		}
	}
})
</script>
<div id='top-space'>
</div>
<?php if($project_info['video_embed'] != ''):?>
<div style="display:none;">
	<div id="vimeo-content">
		<?php echo $project_info['video_embed']?>
	</div>
</div>
<?php endif;?>
<h1>Instructions</h1>
<div>
	<ol>
		<li>Thanks for participating. You will be asked to brainstorm a list of issues, or 'moving parts', that you think are critical to solving a problem. We are interested in your perspective as an expert with an integrative understanding.</li>
		<li>Try to keep a relatively consistent level of 'detail', or resolution if you can. But don't stress about it too much.</li>
		<li>You can edit or delete entries at any time. You can leave part way through and return later to finish your work.</li>
		<li>Be sure to save each entry.</li>
	</ol>
</div>
<br/>
<div class='centered'>
<h2>I am ready to begin</h2>
<a href='<?php echo site_url();?>issues/listing'><span class='submit-but centered-but'>Start Submission Process</span></a>
</div>
