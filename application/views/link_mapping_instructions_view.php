<script type="text/javascript">
$(document).ready(function() {

	var vimeoContent = $('#vimeo-content').detach();

	if(vimeoContent.length == 1)
	{
		//show instructional video if one
		$.fancybox.open(vimeoContent.clone(),{
			afterClose:function() {
				//make sure video shuts the fuck up
				$('.fancybox-wrap #vimeo-content iframe').detach();
			}
		});	
	}
})
</script>

<!--VIDEO EMBED-->
<?php if(trim($video_embed) != ''):?>
<div style="display:none;">
	<div id="vimeo-content">
		<?php echo $video_embed?>
	</div>
</div>
<?php endif;?>

<div id='top-space'>
</div>
<h1>Instructions</h1>
<div class='spacer'></div>
<div class='instruction-block'>
	<?php echo $link_mapping_instructions?>
</div>
<br/>
<div class='centered'>
<h2>I am ready to begin</h2>
<a href='<?php echo site_url();?>links/mapping'><span class='submit-but centered-but'>Start Mapping Process</span></a>
</div>