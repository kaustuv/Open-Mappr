<script type="text/javascript" src="<?php echo site_url();?>javascript/issueCurationLeaderboard.js"></script>
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

<div id='loading-issues' class='opening-closing-rows-status'>
	<div class='loading'></div>
	<p>Loading Nodes...</p>
</div>
<div id='question-box' class='above-box'>
	<div id='question'>
		<h1>Here is our question to you:</h1>
		<p><?php echo $project_info['question'] ?></p>
		<div id='question-arrow' style='display:block;'></div>
		
	</div>
	<div id='question-info-wrap'>
		<div id='question-info-holder'>
			<div id='question-background-info' title='<?php echo nl2br($project_info['description']) ?>'></div>
		</div>
	</div>
	<div id='issue-filters'>
			<input id='issue-text-filter' value='Filter nodes...'/>
			<select id='tag-filter'>
			<option value=''>Select Tag To Filter By...</option>
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
	<div class='light-box single-box instruction-box issue-box issue-curation-box leaderboard-box template-box'>
		<div class='arrow'></div>
		<div class='number'><span></span></div>
		<div class='input'>
			<form>
				<input type='hidden' class='issue-id' name='issue_id' value=''/>
				<label class='title-label'>Short issue title</label>
				<input class='issue-input issue-name' name='name' type='text' value=''/>
				<div class='issue-name-holder'>
					<div class='issue-name-tc'>
						<div class='issue-name-static'></div>
					</div>
				</div>
				<div class='issue-dds' style='margin-right:0'>
					<div class='goal-check'>
						<span class='small-input-text goal-text'>Goal</span>
						<span class='small-input-text complete-text'>Flagged</span>
						<span class='small-input-text type-text'>Positive</span>
					</div>
				</div>
				<div class='fading-inputs'>
					<label>Description</label>
					<textarea class='issue-input issue-description'></textarea>
				  <div class='notes'>
					<label>Notes</label>
				  <textarea class='issue-input issue-notes not-required'></textarea>
				  </div>
				  <br/>
					<label>Units</label>
					<input name='units' type='text' class='issue-input issue-units' value=''/>
				  <br/>
					<label>Tags</label>
					<div class='clearer'></div>
					<textarea name='categories'class='issue-tags'></textarea>
					<div class='clearer'></div>
					<label>Votes</label>
					<br/>
					<div class='votes-text'></div>
					<div class='clearer'></div>
					<br/>
					<br/>
				</div>
			</form>
		</div>
	</div>
</div>
