<script type='text/javascript'>
$(document).ready(function() {
	$('#project-delete').click(function() {
		return confirm("Are you sure you want to delete project: "+ $('#project-delete-select option:selected').text() +"?");
	})

	$('#admin-delete').click(function() {
		return confirm("Are you sure you want to delete admin user: "+ $('#admin-delete-select option:selected').text() +"?");
	})
})
</script>
<div id='top-space'>
</div>

<!--PROJECT CREATE FORM-->
<div class='left-form'>
	<h2>Create, Edit or Delete a Project</h2>
	<?php
	$attributes = array('class' => 'one-wide-form left-form');
	echo form_open('admin/projects', $attributes); ?>
	<div class='input-box left-box dark-box'>
		<?php if($isProjectError):?>
		<span class='error'>Please Choose or Create a Project to Edit.</span>

		<?php endif;?>
		<p>
			<label for="projects">Choose a Project</label>
			<select class='text-input' name='project'>
				<option value=''>Choose a Project</option>
				<?php foreach($projects_ar as $p):?>
				<option value='<?php echo $p['id']?>'><?php echo $p['name']?></option>
				<?php endforeach;?>
			</select>
		</p>
		<p>
			<label for="new_project">or New Project Name</label>
			<input class='text-input' type='text' name='new_project' value='<?php echo set_value('new_project'); ?>'/>
		</p>
		<span class='error'><?php echo validation_errors(); ?></span>
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
		<br/>
	</div>
	<div class='clearer'></div>
	<?php echo form_close(); ?>

	<div class='vert-line'></div>

	<!--PROJECT DELETE FORM-->
	<?php
	$attributes = array('class' => 'one-wide-form left-form');
	echo form_open('admin/delete_project', $attributes); ?>
	<div class='input-box right-box light-box' style='margin-left:-1px'>
		<?php if($isDeleteProjectError):?>
		<span class='error'>Please select a project to delete.</span>
		<?php endif;?>
		<p>
			<label for="delete_project">Project to Delete</label>
			<select id='project-delete-select' name='delete_project' class='text-input' >
				<option value=''>Select Project To Delete</option>
				<?php foreach($projects_ar as $p):?>
				<option value='<?php echo $p['id']?>'><?php echo $p['name']?></option>
				<?php endforeach;?>
			</select>
		</p>
		<p>
		<div class='submit'>
	  <?php $data = array(
	    'class'=>'submit-but inside-box-submit',
	    'id'=>'project-delete',
	    'name'=>'submit',
	    'value'=>'Delete');
	    ?>
	  <?php echo form_submit($data); ?>
	  </div>
	</p>
		<br/>
		<?php if($isDeleteProjectSuccess):?>
		<div class='form-success'>Project '<?php echo $deletedProject?>' Successfully Deleted!</div>
		<?php endif;?>
	</div>
	
	<div class='clearer'></div>
	<?php echo form_close(); ?>
</div>



<!--CURRENT PROJECT FORM-->
<div class='left-form single-form' style='margin-left:15px'>
	<h2>Work on a Project</h2>
	<?php
	$attributes = array('class' => 'one-wide-form left-form single-form narrow-form');
	echo form_open('admin/work_on_project', $attributes); ?>
	<div class='input-box left-box dark-box single-box'>
		<?php if($isCurrentProjectError):?>
		<span class='error'>Please Choose a Project on which to work.</span>
		<?php endif;?>
		<p>
			<label for="projects">Choose a Project</label>
			<select class='text-input' name='project'>
				<option value=''>Choose a Project</option>
				<?php foreach($current_projects as $p):?>
				<option value='<?php echo $p['id']?>'><?php echo $p['name']?></option>
				<?php endforeach;?>
			</select>
		</p>
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
	</div>





<div class='left-form'>
	<h2>Create or Delete an Admin User</h2>


	<!--ADMIN USER CREATE FORM-->
	<?php
	$attributes = array('class' => 'one-wide-form left-form');
	echo form_open('admin/create_admin', $attributes); ?>
	<div class='input-box left-box dark-box'>
		<p>
			<label for="new_admin_user_email">New User Email</label>
			<?php echo form_error('new_admin_user_email'); ?>
		<input class='text-input' type='text' name='new_admin_user_email' value='<?php if(!$isCreateAdminSuccess){ echo set_value('new_admin_user_email');}?>'/>
		</p>
		<p>
			<div class='submit'>
		  <?php $data = array(
		    'class'=>'submit-but inside-box-submit',
		    'name'=>'submit',
		    'value'=>'Create');
		    ?>
		  <?php echo form_submit($data); ?>
		  </div>
		</p>
		<br/>
	<?php if($isCreateAdminSuccess):?>
	<div class='form-success'>Admin User <?php echo $createdAdminUser?> Successfully Created!</div>
	<?php endif;?>
	</div>
	
	<div class='clearer'></div>
	<?php echo form_close(); ?>

	<div class='vert-line'></div>

	
	<!--ADMIN USER DELETE FORM-->
	<?php
	$attributes = array('class' => 'one-wide-form left-form');
	echo form_open('admin/delete_admin', $attributes); ?>
	<div class='input-box right-box light-box' style='margin-left:-1px'>
		<?php if($isDeleteAdminError):?>
		<span class='error'>Please select an admin email to delete.</span>
		<?php endif;?>
		<p>
			<label for="delete_admin_user">User Email to Delete</label>
			<select id='admin-delete-select' name='delete_admin_user' class='text-input' >
				<option value=''>Select User To Delete</option>
				<?php foreach($admin_users as $us):?>
				<option value='<?php echo $us['user_id'] ?>'><?php echo $us['user_email']?></option>
				<?php endforeach;?>
			</select>
		</p>
	<p>
		<div class='submit'>
	  <?php $data = array(
	    'class'=>'submit-but inside-box-submit',
	    'id'=>'admin-delete',
	    'name'=>'submit',
	    'value'=>'Delete');
	    ?>
	  <?php echo form_submit($data); ?>
	  </div>
	</p>
		<br/>
	<?php if($isDeleteAdminSuccess):?>
	<div class='form-success'>Admin User '<?php echo $deletedAdminUser?>' Successfully Deleted!</div>
	<?php endif;?>
	</div>
	<div class='clearer'></div>
	<?php echo form_close(); ?>