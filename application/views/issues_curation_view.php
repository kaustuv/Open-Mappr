
<script type="text/javascript" src="<?php echo site_url();?>javascript/issueCuration.js"></script>
<div id='top-space'>
</div>
<div class='opening-closing-rows-status'>
	<div class='loading'></div>
	<p>Please wait, opening rows...</p>
	<p>Please wait, closing rows...</p>
</div>
<div id='question-box' class='above-box'>
	<div id='question'>
		<h1>Here is our question to you:</h1>
		<?php echo $project_info['question'] ?>
		<div id='question-arrow' style='display:block;'></div>
		
	</div>
	<div id='question-info-wrap'>
		<div id='question-info-holder'>
			<div id='question-background-info' title='<?php echo nl2br($project_info["description"]) ?>'></div>
		</div>
	</div>
	<div id='issue-filters'>
		<input id='issue-text-filter' value='Search...'/>
		<select id='tag-filter'>
		<option value=''>Select Tag To Filter By...</option>
			<?php foreach($tags as $tag):?>
			<option value='<?php echo $tag?>'><?php echo $tag?></option>
			<?php endforeach;?>
		</select>
		<select id='issue-sort'>
			<option value=''>Sort By...</option>
			<option value='number'>Numeric</option>
			<option value='alpha'>Alphabetical</option>
			<option value='negative'>Negatives</option>
			<option value='enable'>Positives</option>
			<option value='flag'>Flag</option>
			<option value='complete'>Complete</option>
		</select>
		<div class='clearer'></div>
	</div>
	<div class='question-buttons'>
		<div class='submit-but close-issues-button'>Close Rows</div>
		<div class='submit-but open-issues-button'>Open Rows</div>
		<div id='hide-instructions' class='submit-but'>Hide Instructions</div>
	</div>	
</div>
<div id='question-space'></div>
<div id='issue-list'>
<div class='clearer' style='height:1px;'></div>
<?php $i = 0;?>
<?php foreach($issues as $issue):?>
		<div class='light-box single-box instruction-box issue-box issue-curation-box'>
			<div class='arrow'></div>
			<div class='number'><span><?php echo $issue['issueInd']?></span></div>
			<div class='delete-text'></div>
			<div class='close-but'></div>
			<div class='input'>
				<form>
					<input name='issue_ind' type='hidden' class='issue-ind' value='<?php echo $issue["issueInd"]?>'/>
					<input name='issue_id' type='hidden' class='issue-id' value='<?php echo $issue['id']?>'/>
					<label class='title-label'>Short node title <span class='parenth'>(<50 characters)</span></label>					
					<input class='issue-input issue-name' name='name' type='text' value="<?php echo $issue["name"]?>"/>
					<div class='issue-name-holder'>
						<div class='issue-name-tc'>
							<div class='issue-name-static'><?php echo $issue["name"]?></div>
						</div>
					</div>
					<input name='issueType' class='issue-type' type='hidden' value='<?php echo $issue["issueType"]?>'/>
					<input name='votes' class='votes' type='hidden' value='<?php echo $issue["votes"]?>'/>
					<input name='remove_id' type='hidden' class='remove-ind' value=''/>
					<div class='submit-but quick-save-but'>Save</div>
					<div class='issue-dds'>
						<div class='save-dds'>Saving Node Type...</div>
						<!-- <div class='goal-check'>
							<?php if($issue['isGoal']):?>
							<input type='checkbox' value='goal' name='is_goal' class='is-goal' checked='yes'/>
							<?php else:?>
							<input type='checkbox' value='goal' name='is_goal' class='is-goal'/>
							<?php endif;?>
							<span class='small-input-text'>Goal</span>
						</div> -->
						<select class='delete-dd'>
							<option value=''>Delete and Add Vote to Node&hellip;</option>
							<?php $j = 0;?>
							<?php foreach($issues as $issue2):?>
							<?php if($i != $j):?>
							<option value='<?php echo $issue2['id']?>'><?php echo $issue2['issueInd']?> - <?php echo $issue2['name']?></option>
							<?php endif;?>
							<?php $j++;?>
							<?php endforeach;?>
						</select>
						<select name='revisit_dd' class='flag-dd'>
							<option value=''>Status</option>
							<?php if($issue['isRevisit'] == 2):?>
							<option value='complete' selected='selected'>Complete</option>
							<?php else:?>
							<option value='complete'>Complete</option>
							<?php endif;?>
							<?php if($issue['isRevisit'] == 1):?>
							<option value='revisit' selected='selected'>Flag</option>
							<?php else:?>
							<option value='revisit'>Flag</option>
							<?php endif;?>
							<option value='delete'>Delete and&hellip;</option>
						</select>
						<select class='type-dd'>
							<option value=''>Phrasing</option>
							<?php if($issue['issueType'] == 1):?>
							<option value='1' selected='selected'>Positive</option>
							<?php else:?>
							<option value='1'>Positive</option>
							<?php endif;?>
							<?php if($issue['issueType'] == -1):?>
							<option value='-1' selected='selected'>Negative</option>
							<?php else:?>
							<option value='-1'>Negative</option>
							<?php endif;?>
							<?php if($issue['issueType'] == -2):?>
							<option value='-2' selected='selected'>Ambiguous</option>
							<?php else:?>
							<option value='-2'>Ambiguous</option>
							<?php endif;?>
						</select>
					</div>
					<div class='fading-inputs'>
						<label>Description <span class='parenth'>(<250 Words)</span></label>
					  <?php echo form_textarea( array( 'name' => 'description', 'class'=>'issue-input issue-description', 'rows' => '5', 'cols' => '80', 'value' => $issue['description'] ) )?>
					  <div class='notes'>
						<label>Notes</label>
					  <?php echo form_textarea( array( 'name' => 'notes', 'class'=>'issue-input issue-notes not-required', 'value' => set_value('notes_'.$i,$issue['notes']) ) )?>
					  </div>
					  <br/>
						<!-- <label>Units <span class='parenth'>(<25 Words)</span></label>
						<input name='units' type='text' class='issue-input issue-units' value='<?php echo $issue["units"]?>'/>
					  <br/> -->
						<label>Categories <span class='parenth'>(up to 5, separated by commas)</span></label>
						<input name='categories' type='text' class='issue-input issue-tags' value='<?php echo $issue["categories"]?>'/>
						<div class='clearer'></div>
						<label>Votes</label>
						<br/>
						<div class='votes-text'><?php echo $issue['votes']?></div>
						<div class='clearer'></div>
						<div class='right'>
							<div class='saving-anim'>Saving Node...</div>
							<div class='submit-but save-button'>Save</div>
							<div class='submit-but cancel-merge'>Cancel Merge</div>
							<div class='clearer'></div>
						</div>
						<div class='clearer'></div>
					</div>
				</form>
			</div>
		</div>
		<?php $i++;?>
<?php endforeach;?>
<div id='issue-list-bottom' class='clearer' style='height:1px;'></div>
</div>
<div class='submit-but save-all-button'>Save All Nodes</div>
<div class='submit-but add-new-button'>Add Node</div>
<br/>
<div id='finished-text'></div>