<script type='text/javascript'>
$(document).ready(function() {

  $('#user-delete').click(function() {
    return confirm("Are you sure you want to delete user: "+$('#delete-user-select option:selected').text()+"?");
  })

  $('#user-attr-type').click(function() {
    if($(this).val() == 'int')
    {
      $('#user-integer-inputs').show(); 
    } else
    {
      $('#user-integer-inputs').hide();
    }
  })

  $('#node-attr-type').click(function() {
    if($(this).val() == 'int')
    {
      $('#node-integer-inputs').show(); 
    } else
    {
      $('#node-integer-inputs').hide();
    }
  })

  $('#link-attr-type').click(function() {
    if($(this).val() == 'int')
    {
      $('#link-integer-inputs').show(); 
    } else
    {
      $('#link-integer-inputs').hide();
    }
  })
});
</script>
<div id='top-space'>
</div>
<!--PROJECT EDIT FORM-->
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'wide-form', 'id' => 'project-form');
echo form_open('projects/edit/' . $current_project, $attributes); ?>

<h1>Project Edit: <?php echo $default['name']?></h1>
<!--NAME-->
<div class='input-box dark-box left-box'>
  <label for="name">Project Name</label>
  <?php echo form_error('name'); ?>
  <input class='text-input' type='text' name='name' value='<?php echo $default['name']?>'/>
</div>

<!--QUESTION-->
<div class='input-box light-box'>
  <?php echo form_error('question'); ?>
  <?php echo form_textarea( array( 'name' => 'question', 'rows' => '5', 'cols' => '80', 'value' => set_value('question',$default['question']) ) )?>
  <label class='textarea-label' for="question">Question</label>
</div>     

<!--DESCRIPTION-->
<div class='input-box dark-box'>
  <?php echo form_error('description'); ?>
  <?php echo form_textarea( array( 'class'=>'large', 'name' => 'description', 'rows' => '5', 'cols' => '80', 'value' => set_value('description',$default['description']) ) )?>
  <label class='textarea-label' for="description">Description/Background Information</label>
</div>                                          

<!--NUMBER OF ISSUES PER PARTICIPANT-->
<div class='input-box right-box light-box'>
  <label for="numberOfIssuesPerParticipant"># of Issues/Participant</label>
  <?php echo form_error('numberOfIssuesPerParticipant'); ?>
  <input class='number-text' type='text' name='numberOfIssuesPerParticipant' value='<?php echo $default['numberOfIssuesPerParticipant']?>'/>
  <label for="numberOfIssuesPerParticipant"># of From Issues/Participant for Linking</label>
  <?php echo form_error('numberOfFromIssuesPerParticipant'); ?>
  <input class='number-text' type='text' name='numberOfFromIssuesPerParticipant' value='<?php echo $default['numberOfFromIssuesPerParticipant']?>'/> of <?php echo $total_issues?>
  <label for='projectState'>Current Project State</label>
  <select name='projectState' class='text-input'>
    <?php foreach($default['projectStates'] as $state):?>
      <?php if($default['projectState'] == $state['id']):?>
        <option selected='selected' value='<?php echo $state["id"]?>'><?php echo $state['name']?></option>
      <?php else:?>
        <option value='<?php echo $state["id"]?>'><?php echo $state['name']?></option>
      <?php endif;?>
    <?php endforeach;?>
  </select>
</div>
<?php if($isProjectSaveSuccess):?>
<div class='form-success'>Project Successfully Saved!</div>
<?php endif;?>

<!--SUBMIT BUTTONS-->
<p>
  <?php $data = array(
    'id' => 'wide-submit',
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Save');
    ?>
  <?php echo form_submit($data); ?><div class='but-right'></div>
</p>

<?php echo form_close(); ?>
<div class='clearer'></div>

<!--USER CREATE/DELETE FOR PROJECT-->
<div class='left-form'>
<h2>Create or Delete Users from this Project</h2>

<!--USER CREATE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/create_user', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box left-box dark-box'>
	<p>
    <?php echo form_error('new_user_emails'); ?>
    <?php if($isCreateUserSuccess):?>
    <?php echo form_textarea( array( 'name' => 'new_user_emails', 'rows' => '5', 'cols' => '80', 'value' => '' ) )?>
    <?php else:?>
    <?php echo form_textarea( array( 'name' => 'new_user_emails', 'rows' => '5', 'cols' => '80', 'value' => set_value('new_user_emails') ) )?>
    <?php endif;?>
    <label class='textarea-label' for="new_admin_user_email">Separate Emails with Commas</label>
	</p>
	<br/>
  <?php if($isCreateUserSuccess):?>
  <div class='form-success'>Users Successfully Created!</div>
  <?php endif;?>
</div>
<p>
	<div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Create');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>


<!--USER DELETE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/delete_user', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box right-box'>
	<?php if($isDeleteUserError):?>
	<span class='error'>Please select a user email to remove.</span>
	<?php endif;?>
	<p>
		<label for="delete_project_user">User Email to Remove</label>
		<select id='delete-user-select' name='delete_project_user' class='text-input' >
			<option value=''>Select User To Remove</option>
			<?php foreach($users as $us):?>
			<option value='<?php echo $us['user_id'] ?>'><?php echo $us['user_email']?></option>
			<?php endforeach;?>
		</select>
	</p>
	<br/>
  <?php if($isDeleteUserSuccess):?>
  <div class='form-success'><?php echo $deleted_user?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<p>
	<div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'id'=>'user-delete',
    'name'=>'submit',
    'value'=>'Delete');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>


<div class='right-form'>
<h2>Create or Delete Attributes for Users</h2>
<!--DELETE USER PROJECT ATTRIBUTES-->
<?php
$attributes = array('class' => 'right-form one-wide-form');
echo form_open('projects/delete_user_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box right-box'>
  <?php if($isDeleteUserAttrError):?>
  <span class='error'>Please select a user attribute to remove.</span>
  <?php endif;?>
  <p>
    <label for="delete_project_user_attr">User Attribute to Remove</label>
    <select name='delete_project_user_attr' class='text-input' >
      <option value=''>Select Attribute To Remove</option>
      <?php foreach($project_user_atts as $a):?>
      <option value='<?php echo $a->id ?>'><?php echo $a->name?> - <?php echo $a->type?></option>
      <?php endforeach;?>
    </select>
  </p>
  <br/>
  <?php if($isDeleteUserAttrSuccess):?>
  <div class='form-success'><?php echo $deleted_user_attr?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'id'=>'attr-delete',
    'name'=>'submit',
    'value'=>'Delete');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>


<!--ADD/CREATE USER ATTRIBUTE FORM-->
<?php
$attributes = array('class' => 'right-form one-wide-form');
echo form_open('projects/add_user_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box dark-box left-box'>
  <p>
    <label for="user_attr">User Attribute</label>
    <?php echo form_error('user_attr'); ?>
      <select class='text-input' name='user_attr'>
      <option value=''>Choose a User Attribute</option>
        <?php foreach($user_atts_ar as $a):?>
        <option value='<?php echo $a->id?>'><?php echo $a->name?></option>
        <?php endforeach;?>
      </select>
  </p>
  <div class='input-line'></div>
  <p>
    <label for="new_user_attr">or New User Attribute Name</label>
    <?php echo form_error('new_user_attr'); ?>
    <input class='text-input' type='text' name='new_user_attr' value='<?php echo set_value('new_user_attr') ?>'/>
    <label for="new_user_attr_type">Attribute Type</label>
    <?php echo form_error('new_user_attr_type'); ?>
    <select id='user-attr-type' class='text-input' name='new_user_attr_type'>
      <option value=''>Choose an Attribute Type</option>
      <?php if(set_value('new_user_attr_type') == "int"):?>
      <option selected='selected' value='int'>Integer</option>
      <?php else:?>
      <option value='int'>Integer</option>
      <?php endif;?>
      <?php if(set_value('new_user_attr_type') == 'ta'):?>
      <option selected='selected' value='ta'>Text Area</option>
      <?php else:?>
      <option value='ta'>Text Area</option>
      <?php endif;?>
    </select>
  </p>
  <div <?php if(set_value('new_user_attr_type') != 'int'):?>class='hidden-input'<?php endif;?> id='user-integer-inputs'>
    <p>
      <div class='half'>
        <label class='left half' for='min_user_attr'>Min</label>
        <input class='number-text left half' type='text' name='min_user_attr' value='<?php echo set_value("min_user_attr")?>'/>
        <?php echo form_error('min_user_attr'); ?>
      </div>
      <div class='half'>
        <label class='left half' for='max_user_attr'>Max</label>
        <input class='number-text left half' type='text' name='max_user_attr' value='<?php echo set_value("max_user_attr")?>'/>
        <?php echo form_error('min_user_attr'); ?>
      </div>
    </p>
  </div>
  <br/>
  <?php if($isAddUserAttrSuccess):?>
  <div class='form-success'>User Attribute Successfully Added!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Add');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>

<div class='clearer'></div>
</div>


<!--NODE ATTRIBUTE CREATE/DELETE FOR PROJECT-->
<div class='left-form'>
<h2>Create or Delete Attributes for Nodes/Issues</h2>

<!--ADD/CREATE NODE ATTRIBUTE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/add_node_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box dark-box left-box'>
  <p>
    <label for="new_node_attr">Node Attribute</label>
    <?php echo form_error('node_attr'); ?>
      <select class='text-input' name='node_attr'>
        <option value=''>Choose a Node Attribute</option>
        <?php foreach($node_atts_ar as $a):?>
        <option value='<?php echo $a->id?>'><?php echo $a->name?></option>
        <?php endforeach;?>
      </select>
  </p>
  <div class='input-line'></div>
  <p>
    <label for="new_node_attr">or New Node Attribute Name</label>
    <?php echo form_error('new_node_attr'); ?>
    <input class='text-input' type='text' name='new_node_attr' value='<?php echo set_value('new_node_attr') ?>'/>
    <label for="new_node_attr_type">Attribute Type</label>
    <?php echo form_error('new_node_attr_type'); ?>
    <select id='node-attr-type' class='text-input' name='new_node_attr_type'>
      <option value=''>Choose an Attribute Type</option>
      <?php if(set_value('new_node_attr_type') == "int"):?>
      <option selected='selected' value='int'>Integer</option>
      <?php else:?>
      <option value='int'>Integer</option>
      <?php endif;?>
      <?php if(set_value('new_node_attr_type') == 'ta'):?>
      <option selected='selected' value='ta'>Text Area</option>
      <?php else:?>
      <option value='ta'>Text Area</option>
      <?php endif;?>
    </select>
  </p>
  <div <?php if(set_value('new_node_attr_type') != 'int'):?>class='hidden-input'<?php endif;?> id='node-integer-inputs'>
    <p>
      <div class='half'>
        <label class='left half' for='min_node_attr'>Min</label>
        <input class='number-text left half' type='text' name='min_node_attr' value='<?php echo set_value("min_node_attr")?>'/>
        <?php echo form_error('min_node_attr'); ?>
      </div>
      <div class='half'>
        <label class='left half' for='max_node_attr'>Max</label>
        <input class='number-text left half' type='text' name='max_node_attr' value='<?php echo set_value("max_node_attr")?>'/>
        <?php echo form_error('min_node_attr'); ?>
      </div>
    </p>
  </div>
  <br/>
  <?php if($isAddNodeAttrSuccess):?>
  <div class='form-success'>Node Attribute Successfully Added!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Add');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>

<!--DELETE PROJECT NODE ATTRIBUTES-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/delete_node_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box right-box'>
  <?php if($isDeleteNodeAttrError):?>
  <span class='error'>Please select a node attribute to remove.</span>
  <?php endif;?>
  <p>
    <label for="delete_project_node_attr">Node Attribute to Remove</label>
    <select name='delete_project_node_attr' class='text-input' >
      <option value=''>Select Attribute To Remove</option>
      <?php foreach($project_node_atts as $a):?>
      <option value='<?php echo $a->id ?>'><?php echo $a->name?> - <?php echo $a->type?></option>
      <?php endforeach;?>
    </select>
  </p>
  <br/>
  <?php if($isDeleteNodeAttrSuccess):?>
  <div class='form-success'><?php echo $deleted_node_attr?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'id'=>'attr-delete',
    'name'=>'submit',
    'value'=>'Delete');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>


<!--LINK ATTRIBUTE CREATE/DELETE FOR PROJECT-->
<div class='right-form'>
<h2>Create or Delete Attributes for Links</h2>

<!--DELETE LINK PROJECT ATTRIBUTES-->
<?php
$attributes = array('class' => 'right-form one-wide-form');
echo form_open('projects/delete_link_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box right-box'>
  <?php if($isDeleteLinkAttrError):?>
  <span class='error'>Please select a link attribute to remove.</span>
  <?php endif;?>
  <p>
    <label for="delete_project_link_attr">Link Attribute to Remove</label>
    <select name='delete_project_link_attr' class='text-input' >
      <option value=''>Select Attribute To Remove</option>
      <?php foreach($project_link_atts as $a):?>
      <option value='<?php echo $a->id ?>'><?php echo $a->name?> - <?php echo $a->type?></option>
      <?php endforeach;?>
    </select>
  </p>
  <br/>
  <?php if($isDeleteLinkAttrSuccess):?>
  <div class='form-success'><?php echo $deleted_link_attr?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'id'=>'attr-delete',
    'name'=>'submit',
    'value'=>'Delete');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>


<!--ADD/CREATE LINK ATTRIBUTE FORM-->
<?php
$attributes = array('class' => 'right-form one-wide-form');
echo form_open('projects/add_link_attribute', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box dark-box left-box'>
  <p>
    <label for="link_attr">Link Attribute</label>
    <?php echo form_error('link_attr'); ?>
      <select class='text-input' name='link_attr'>
        <option value=''>Choose a Link Attribute</option>
        <?php foreach($link_atts_ar as $a):?>
        <option value='<?php echo $a->id?>'><?php echo $a->name?></option>
        <?php endforeach;?>
      </select>
  </p>
  <div class='input-line'></div>
  <p>
    <label for="new_link_attr">or New Link Attribute Name</label>
    <?php echo form_error('new_link_attr'); ?>
    <input class='text-input' type='text' name='new_link_attr' value='<?php echo set_value('new_link_attr') ?>'/>
    <label for="new_link_attr_type">Attribute Type</label>
    <?php echo form_error('new_link_attr_type'); ?>
    <select id='link-attr-type' class='text-input' name='new_link_attr_type'>
      <option value=''>Choose an Attribute Type</option>
      <?php if(set_value('new_link_attr_type') == "int"):?>
      <option selected='selected' value='int'>Integer</option>
      <?php else:?>
      <option value='int'>Integer</option>
      <?php endif;?>
      <?php if(set_value('new_link_attr_type') == 'ta'):?>
      <option selected='selected' value='ta'>Text Area</option>
      <?php else:?>
      <option value='ta'>Text Area</option>
      <?php endif;?>
    </select>
  </p>
  <div <?php if(set_value('new_link_attr_type') != 'int'):?>class='hidden-input'<?php endif;?> id='link-integer-inputs'>
    <p>
      <div class='half'>
        <label class='left half' for='min_link_attr'>Min</label>
        <input class='number-text left half' type='text' name='min_link_attr' value='<?php echo set_value("min_link_attr")?>'/>
        <?php echo form_error('min_link_attr'); ?>
      </div>
      <div class='half'>
        <label class='left half' for='max_link_attr'>Max</label>
        <input class='number-text left half' type='text' name='max_link_attr' value='<?php echo set_value("max_link_attr")?>'/>
        <?php echo form_error('min_link_attr'); ?>
      </div>
    </p>
  </div>
  <br/>
  <?php if($isAddLinkAttrSuccess):?>
  <div class='form-success'>Link Attribute Successfully Added!</div>
  <?php endif;?>
</div>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Add');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>

<div class='clearer'></div>

