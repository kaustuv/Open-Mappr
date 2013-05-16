<?php $numIssues = $project_info['numberOfIssuesPerParticipant']?>

<script type="text/javascript" src="<?php echo site_url();?>javascript/issueBox.js"></script>
<script type="text/javascript" src="<?php echo site_url();?>javascript/issueSubmission.js"></script>

<div id='top-space'>
</div>

<!--USER NOTES-->
<div class='user-notes-holder'>
	<div class='notes-but submit-but'>My Notes</div>
	<div class='user-notes'>
		<form>
			<textarea name='user_notes'><?php echo $user_notes;?></textarea>
			<div class='submit-but user-notes-submit'>Save</div>
			<div class='loading-text'></div>
		</form>
	</div>
</div>

<div id='question-box' class='above-box'>
	<div id='question'>
		<h1>Here is our question to you:</h1>
		<p><?php echo $project_info['question'] ?></p>
		<div id='question-arrow'></div>
	</div>
	<div id='question-info-wrap'>
		<div id='question-info-holder'>
			<div id='question-background-info' title='<?php echo htmlspecialchars(nl2br($project_info["description"]),ENT_QUOTES) ?>'></div>
		</div>
	</div>
	<div class='clearer'></div>
	<div id='question-background'>
	<br/>
		<h1>CONTEXT AND BACKGROUND INFORMATION</h1>
		<?php echo nl2br($project_info['description']) ?>
		<br/>
		<div class='centered'>
			<span id='begin-but' class='submit-but centered-but'>Begin</span>
		</div>
	</div>
	
</div>
<div id='question-space'></div>
<div id='issue-list' class='issue-list-submission'>
	<div class='clearer' style='height:1px;'></div>
	<?php for($i=0;$i<max(count($issues_list),1);$i++):?>
		<?php 
		if(isset($issues_list[$i]))
		{
			$isOpen = FALSE;
			$issue = $issues_list[$i];
		} else
		{
			$isOpen = FALSE;
			$issue = array('name'=>'',
											'description'=>'',
											'units'=>'',
											'categories'=>'',
											'id'=>'',
											'issueOrderNum'=>0);
		}
		?>
		<div class='dark-box single-box instruction-box issue-box'>
			<div class='arrow'></div>
			<div class='number'><span><?php echo $i+1?></span></div>
			<div class='delete-text'></div>
			<div class='close-but'></div>
			<div class='input'>
				<form>
					<?php if($isOpen):?>
					<div class='is-open'></div>
					<?php endif;?>
					<input name='issue_order_num' type='hidden' class='issue-order-num' value='<?php echo $issue['issueOrderNum']?>'/>
					<input name='issue_id' type='hidden' class='issue-id' value='<?php echo $issue['id']?>'/>
					<label class='title-label'>Short node title <span class='parenth'>(<50 characters)</span></label>
					<?php if($isOpen):?>
					<input class='issue-input issue-name' name='name' type='text' value="<?php echo $issue["name"]?>" title='Provide a short descriptive title for this node'/>
					<div class='issue-name-holder'>
						<div class='issue-name-tc'>
							<div class='issue-name-static'><?php echo $issue["name"]?></div>
						</div>
					</div>
					<?php else:?>
					<input class='issue-input issue-name' name='name' type='text' value="<?php echo $issue["name"]?>" style='display:none;' title='Provide a short descriptive title for this node'/>
					<div class='issue-name-holder'>
						<div class='issue-name-tc'>
							<div class='issue-name-static' style='display:block;'><?php echo $issue["name"]?></div>
						</div>
					</div>
					<?php endif;?>
					<div class='submit-but quick-save-but'>Save</div>
					<div class='fading-inputs'>
						<label>Description <span class='parenth'>(<250 Words)</span></label>
					  <?php echo form_textarea( array( 'name' => 'description', 'class'=>'issue-input issue-description', 'title'=>'Describe and define this node in more detail. Why did you choose it? What does it influence? Is this a major constraint or an enabler?', 'rows' => '5', 'cols' => '80', 'value' => $issue['description'] ) )?>
					  <br/>
						<!-- <label>Units <span class='parenth'>(Optional <25 Words)</span></label>
						<input name='units' type='text' class='issue-input issue-units' title='How would you measure a change in this node? (optional)' value='<?php echo $issue["units"]?>'/>
					  <br/> -->
						<label>Categories <span class='parenth'>(up to 5, separated by commas)</span></label>
						<input name='categories' type='text' class='issue-input issue-tags' title='What broader tags or keywords, if any, help define this node or place it in context? If not applicable or you are uncertain, just enter "N/A"' value='<?php echo $issue["categories"]?>'/>
						<div class='clearer'></div>
						<div class='right'>
							<div class='saving-anim'>Saving Nodes...</div>
							<input type='submit' class='submit-but save-button' value='Save' onclick='return false;' />
						</div>
						<div class='clearer'></div>
					</div>
				</form>
			</div>
		</div>
	<?php endfor;?>
	<div id='issue-list-bottom' class='clearer' style='height:1px;'></div>
</div>
<div id='add-new'>
	<div class='submit-but add-new-button'>Add Node</div>
	<div id='issue-number'>So far, you have added <span class='issue-num'>1</span> of <span class='tot-issues'><?php echo $numIssues?></span> Max Nodes</div>
	<div class='clearer'></div>
	<div class='submit-but finished-button'>Finished Adding Nodes</div>
	<div class='submit-but finish-later-button'>Save Nodes And Continue Later</div>
	<div id='finished-text'>Thank you for completing the Node Submission process. </div>
</div>
<div class='clearer'></div>
<div id='page-bottom'></div>