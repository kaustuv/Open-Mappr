
<script src="<?php echo site_url()?>javascript/jquery.circlepacker.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url()?>javascript/fancybox-transitions.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url()?>javascript/d3.v2.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url()?>javascript/link-viz.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo site_url()?>javascript/link-mapping.js" charset="utf-8"></script>


<script type="text/javascript">
	var linksJSON = <?php echo json_encode($user_links);?>;
	var totFroms = <?php echo $total_froms?>;
	var totNodes = <?php echo count($to_nodes)?>;
	var linkMapInstructions = <?php echo json_encode($link_mapping_instructions)?>;
	var adminEmail = "<?php echo $admin_email;?>";
</script>

<div id='top-space'>
</div>
<!--VIDEO EMBED-->
<?php if(trim($video_embed) != ''):?>
<div style="display:none;">
	<div id="vimeo-content">
		<?php echo $video_embed?>
	</div>
</div>
<?php endif;?>

<!--INSTRUCTION VIDEO-->
<div style="display:none;">
	<div id="instruction-video">
		<iframe id="vimeo_video" src="http://player.vimeo.com/video/41325834?title=1&byline=0&portrait=0&color=588A9E&autoplay=1" width="800" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
</div>


<!--CIRCLES FOR CHOOSING LINKS BASED ON SUBSETS-->
<h1 id='circle-title' class='zero'>Below are <?php echo count($subset_tags)?> major categories of critical nodes in the <?php echo $project_name;?> Ecosystem.<br/>Select at least <?php echo $total_froms?> nodes that interest you most from any one or more of these categories. Fewer is fine, you can always add more later.<br/><span class='below-circles'>Or if you prefer, click <a href='#random' class='random-froms-but'>here</a> and we'll choose <?php echo $total_froms?> for you!</span></h1>
<div id='circle-tags'>
	<?php $i = 1;?>
	<?php foreach($subset_tags as $key=>$value):?>
	<div class='circle' id='circle<?php echo $i?>'><div class='holder'><h1><?php echo $value?></h1><div class='tit'><?php echo $key?></div></div></div>
	<?php $i++;?>
	<?php endforeach;?>
</div>
<h1 id='circle-section-heading'></h1>



<!--FINISHED MAPPING CONTENT-->
<div style='display:none;'>
	<div id='finished-content'><h1 class='zero'>You're Done!</h1><p>Thanks for finishing the Link Mapping for <?php echo $project_name?>. Please make sure you have made all the necessary links from your list. Any time you wish to review or add to your current links, just revisit:<br/><br/> <a href='<?php echo site_url()?>'><?php echo site_url()?></a></p></div>
</div>

<!--ISSUE LISTING TO SEND TO EMAIL-->
<div style='display:none;'>
	<div id='issue-submission' class='single-box issue-box link-mapping-issue-box' style='display:block;'>
		<h2 style='margin:-10px 0 10px;'>Add a new node to be curated and added to Link Mapping</h2>
		<div class='input'>
			<form id='issue-form'>
				<label class='title-label'>Short node title <span class='parenth'>(<50 characters)</span></label>
				<input class='issue-input issue-name shadowy' name='name' type='text' value="" title='Provide a short descriptive title for this node'/>
					<label>Description <span class='parenth'>(<250 Words)</span></label>
				  <?php echo form_textarea( array( 'name' => 'description', 'class'=>'issue-input issue-description shadowy', 'title'=>'Describe and define this node in more detail. Why did you choose it? What does it influence? Is this a major constraint or an enabler?', 'rows' => '5', 'cols' => '80' ) )?>
				  <br/>
					<label>Units <span class='parenth'>(Optional <25 Words)</span></label>
					<input name='units' type='text' class='issue-input issue-units shadowy' title='How would you measure a change in this node? (optional)' value=''/>
				  <br/>
					<label>Tags <span class='parenth'>(up to 5, separated by commas)</span></label>
					<input name='categories' type='text' class='issue-input issue-tags shadowy' title='What broader tags or keywords, if any, help define this node or place it in context? If not applicable or you are uncertain, just enter "N/A"' value=''/>
					<div class='clearer'></div>
					<div class='right'>
						<div class='saving-anim'>Saving Node...</div>
						<input id='issue-submit-but' type='submit' class='submit-but save-button' value='Save' onclick='return false;' />
					</div>
					<div class='clearer'></div>
			</form>
		</div>
	</div>
</div>

<!--USER NOTES-->
<div class='user-notes-holder'>
	<div class='notes-but submit-but2'>My Notes</div>
	<div class='user-notes'>
		<form>
			<textarea name='user_notes'><?php echo $user_notes;?></textarea>
			<div class='submit-but user-notes-submit'>Save</div>
			<div class='loading-text'></div>
		</form>
	</div>
</div>

<!--FEEDBACK-->
<div class='user-feedback-holder'>
	<div class='feedback-but submit-but2'>Feedback</div>
	<div class='user-feedback'>
	<h3>Please send us feedback about this Software.</h3>
	<p>Feedback can help us fix any issues you might find with the software, or simply as a way for you to give us advice on how to better the software.</p>
		<form>
			<textarea name='user_feedback'></textarea>
			<div class='submit-but user-feedback-submit'>Send</div>
			<div class='loading-text'></div>
		</form>
	</div>
</div>

<!--LINK WEB-->
<div class='link-web-holder'>
	<div class='link-web-but submit-but2'>My Links</div>
	<div class='link-web'>
		<h3>Your links visualized</h3>
		<div id='link-viz'></div>
	</div>
</div>

<div class='top-link-buttons'>
		<div class='instruction-video-but' title='Click to rewatch instructional video.'></div>
		<?php if($video_embed != ''):?>
		<div class='intro-video-but'></div>
		<?php endif;?>
	<div class='set1-buttons'>
		<div class='finished-buts'>
			<div class='finished-links-but submit-but'>I&lsquo;m done</div>
			<div class='finish-later-but submit-but'>I&lsquo;ll finish later</div>
		</div>
		<div class='another-issue-but submit-but' title='Find a glaring gap in the list? Submit a new node.'>Submit New Node</div>
	</div>
	<div class='set2-buttons'>
		<div class='back-to-mapping submit-but'>Back to Mapping</div>
	</div>
</div>
<!--LINK MAPPING-->
<div id='link-mapping'>
	<div id='arrow-titles'>
		<h1>My Nodes</h1><h1 class='links-header'>Links</h1><h1 class='right to-header'>Full Node List</h1>
	</div>
	<div id='arrow-filters'>
		<div>
			<select id='link-sort'>
				<option value=''>Sort Links...</option>
				<option value='incoming'>Incoming</option>
				<option value='outgoing'>Outgoing</option>
				<option value='both'>Both</option>
			</select>
		</div>
		<div id='to-filter'>
			<select id='tag-filter'>
				<option value=''>Filter by Tag...</option>
				<?php foreach($tags as $tag):?>
				<option value='<?php echo $tag ?>'><?php echo $tag ?></option>
				<?php endforeach;?>
			</select>
			<select id='issue-sort'>
				<option value=''>Sort By...</option>
				<option value='numeric'>Numerical</option>
				<option value='alpha'>Alphabetical</option>
				<!--<option value='constraint'>Constraints</option>
				<option value='enable'>Enablers</option>
				<option value='goal'>Goals</option>-->
			</select>
			<input id='issue-text-filter' value='Search'/>
		</div>
		<br/>
	</div>

	<div class='from-instructions'>
		<?php if($total_froms == 0):?>

		<h1 class='from-init-heading'>SELECT NODES FROM THE LEFT THAT YOU KNOW MOST ABOUT. CLICK ON THE CIRCLES TO SELECT MORE NODES FROM ANOTHER CATEGORY.</h1>
		<?php else:?>
		<h1 class='from-init-heading'>SELECT UP TO <?php echo $total_froms?> NODES FROM THE LEFT THAT YOU KNOW MOST ABOUT. CLICK ON THE CIRCLES TO SELECT MORE NODES FROM ANOTHER CATEGORY.</h1>
		<?php endif;?>
		<div class='from-instruct-begin'>
			<h1 class='init'>You have selected <span>1 node</span>.</h1>
			<h1 class='after'>Please wait, loading nodes...</h1>
			<div class='from-finished-holder'>
				<a href='#' class='submit-but from-instruct-finished'>I'M READY TO MAP LINKS!</a>
			</div>
		</div>
	</div>
	<?php $i = 0;?>
	<?php $tot_from = count($from_nodes);?>
	<div class='from-nodes-holder'>
	<div class='from-nodes'>
		<div class='from-nodes-cover'></div>
		<?php foreach($from_nodes as $from_node):?>
		<?php if(in_array($from_node['id'], $user_from_nodes)):?>
		<div class='from-holder from-holder-<?php echo $from_node["id"]?> chosen'>
		<?php else:?>
		<div class='from-holder from-holder-<?php echo $from_node["id"]?>'>
		<?php endif;?>
		<?php 
			//convert array to string so can search
			//when deciding whether to show when clicking a circle
			$sub_tags = implode('|', $from_node['subset_tags']);
		?>
			<input type='hidden' class='subset-tags' name='subset_tags' value='<?php echo $sub_tags?>'/>
			<input type='hidden' class='from-id' name='from_id_<?php echo $i?>' value='<?php echo $from_node["id"]?>'/>
			<input type='hidden' class='from-issue-type' value='<?php echo $from_node['issueType']?>'/>
			<div class='from-node'>
				<?php if($from_node['isDone']):?>
				<div class='from-check checked' title='Use to Mark Completion'></div>
				<?php else:?>
				<div class='from-check' title='Use to Mark Completion'></div>
				<?php endif;?>
				<div class='from-node-content'>
					<div class='from-index'><?php echo $i+1?></div>
					<?php $tit = htmlspecialchars($from_node["description"],ENT_QUOTES);?>
					<div class='name' title='<?php echo $tit?>'><?php echo trim($from_node['name'])?></div>
					<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
				</div>
				<?php if($from_node['issueType'] == 1):?>
					<!--<div class='phrasing-over' title='Positive Phrasing, Increase is Desirable'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background enabler is-begun'></div>
					<?php else:?>
					<div class='from-node-background enabler'></div>
					<?php endif;?>
				<?php elseif($from_node['issueType'] == -1):?>
					<!--<div class='phrasing-over' title='Negative Phrasing, Increase is Not Desirable'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background constraint is-begun'></div>
					<?php else:?>
					<div class='from-node-background constraint'></div>
					<?php endif;?>
				<?php elseif($from_node['issueType'] == 2):?>
					<!--<div class='phrasing-over' title='One of the High Level Goals'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background goal is-begun'></div>
					<?php else:?>
					<div class='from-node-background goal'></div>
					<?php endif;?>
				<?php else:?>
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background is-begun'></div>
					<?php else:?>
					<div class='from-node-background'></div>
					<?php endif;?>
				<?php endif;?>
			</div>
		</div>
	<?php $i++;?>
	<?php endforeach;?>
	</div>
</div>

		<div class='to-nodes'>
			<div class='to-node-set'>
				<input type='hidden' class='from-id' value=''/>
			<?php $j=0;?>
				<?php foreach($to_nodes as $to_node):?>
				<?php
				if($to_node['isDeleted'])
				{
					$j++;
					continue;
				}
				?>
				<div class='to-holder to-holder-<?php echo $to_node["id"]?>'>
					<div class='link-holder'>
						<div class='loading-link'>Please Wait, Creating Link...</div>
							<input type='hidden' class='link-id' value=''/>
							<input type='hidden' class='link-id2' value=''/>
							<div class='create-links-buts'>
								<div class='incoming-link-but'></div>
								<div class='both-link-but'></div>
								<div class='outgoing-link-but'></div>
							</div>
							<div class='link-but-filled-background'>
								<div class='link-close'></div>
								<div class='link-comment'>
									<div class='link-comment-box'>
										<h3>Link Comment</h3>
										<div class='close-but'></div>
										<textarea class='comment-textfield'></textarea>
										<div class='saving-link-comment'>Please wait, saving comment...</div>
										<div class='save-but submit-but'>Save</div>
									</div>
								</div>
								<div class='link-positive-corr'></div>
								<div class='link-negative-corr'></div>
							</div>
							<div class='link-but-set'>
								<div class='link-close'></div>
								<div class='link-comment'>
									<div class='link-comment-box'>
										<h3>Link Comment</h3>
										<div class='close-but'></div>
										<textarea class='comment-textfield'></textarea>
										<div class='saving-link-comment'>Please wait, saving comment...</div>
										<div class='save-but submit-but'>Save</div>
									</div>
								</div>
								<div class='link-positive-corr-set'></div>
								<div class='link-negative-corr-set'></div>
							</div>
					</div>
						<div class='to-node'>
						<div class='to-close' title='Outside your knowledge area?  Remove it from the list'></div>
						<input type='hidden' class='to-id' name='to_id_<?php echo $j?>' value='<?php echo $to_node["id"]?>'/>
						<input type='hidden' class='to-issue-type' value='<?php echo $to_node['issueType']?>'/>
						<div class='to-node-content'>
							<div class='to-index'><?php echo $j+1?></div>
							<?php $tit = htmlspecialchars($to_node["description"],ENT_QUOTES)?>
							<div class='name' title='<?php echo $tit;?>'><?php echo trim($to_node['name'])?></div>
							<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
							<input type='hidden' class='issue-tag-list' value='<?php echo $to_node["categories"]?>'/>
						</div>
						<input type='hidden' class='issue-type' value='<?php echo $to_node["issueType"]?>'/>
						<div class='to-node-over'></div>
						<?php if($to_node['issueType'] == 1):?>
						<div class='phrasing-over' title='Positive Phrasing, Increase is Desirable'></div>
						<div class='to-node-background enabler'></div>
						<?php elseif($to_node['issueType'] == -1):?>
						<div class='phrasing-over' title='Negative Phrasing, Increase is Not Desirable'></div>
						<div class='to-node-background constraint'></div>
						<?php elseif($to_node['issueType'] == 2):?>
						<div class='phrasing-over' title='One of the High Level Goals'></div>
						<div class='to-node-background goal'></div>
						<?php elseif($to_node['issueType'] == -2):?>
						<div class='to-node-background na'></div>
						<?php else:?>
						<div class='to-node-background'></div>
						<?php endif;?>
					</div>
					<div class='clearer'></div>
				</div>
				<?php $j++;?>
				<?php endforeach;?>
			</div>
		</div>
</div>