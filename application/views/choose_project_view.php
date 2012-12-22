<div id='top-space'></div>
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'left-form one-wide-form single-form narrow-form');
echo form_open('login/project_chosen', $attributes); ?>
<h2>Choose Project</h2>
<div class='input-box dark-box single-box'>
<?php if($isError):?>
<span class='error'>Please Choose a project on which to work.</span>
<?php endif;?>
<p>
		<label for="chosen_project">Choose the project on which to work</label>
		<select id='choose-project-select' name='chosen_project' class='text-input' >
			<option value=''>Choose the project</option>
			<?php foreach($projects as $p):?>
			<option value='<?php echo $p[0] ?>'><?php echo $p[1]?></option>
			<?php endforeach;?>
		</select>
	</p>
<br/>
<p>
	<div class='submit'>
  <?php $data = array(
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Submit');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>