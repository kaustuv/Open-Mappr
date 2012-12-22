<script type='text/javascript'>
$(document).ready(function() {
  
  var scr = window.location.hash.substr(1);

  //initial value of project Name
  var initialProjectName = $('#project-name').val();
  var initialProjectURL = $('#project-url').val();

  //auto resize for tag-seed
  $('#tag-seed').elastic();

  //textarea formatting
  $('textarea.tinymce').tinymce({
    // Location of TinyMCE script
    script_url : baseURL+'javascript/tiny_mce/tiny_mce.js',
    // General options
    theme : "advanced",
    plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

    // Theme options
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,fullscreen",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal:false,

    // Example content CSS (should be your site CSS)
    content_css : "style.css",

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : "lists/template_list.js",
    external_link_list_url : "lists/link_list.js",
    external_image_list_url : "lists/image_list.js",
    media_external_list_url : "lists/media_list.js",
    oninit : function() {
      if(scr == 'scroll')
      {
        $.scrollTo('#bottom',500)
      }
    }

  });

  //if keypress in project name, see if name is same as initial name
  //and if not, then save again
  $('#project-name').bind("keypress focusout",function() {
    //check this value against original
    if($(this).val() != initialProjectName)
    {
      $('#project-url').val('The project name has changed. Please save this project to get the correct url.');
    } else
    {
      $('#project-url').val(initialProjectURL);
    }
  })




  
});
</script>
<div id='top-space'>
</div>
<!--PROJECT EDIT FORM-->
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'wide-form', 'id' => 'project-form');
echo form_open('projects/edit/' . $current_project . '/#scroll', $attributes); ?>
<h1>Project Edit: <?php echo $default['name']?></h1>
<div class='input-box-wide input-box light-box'>
  <!--NAME-->
    <label for="name">Project Name</label>
    <?php echo form_error('name'); ?>
    <input id='project-name' class='text-input text-input-wide' type='text' name='name' value='<?php echo $default['name']?>'/>
    <div class='clearer'></div>
   <!--QUESTION-->
    <label for="question">Question</label>
    <?php echo form_error('question'); ?>
    <?php echo form_textarea( array( 'name' => 'question', 'rows' => '5', 'cols' => '80', 'class' => 'tinymce', 'value' => set_value('question',$default['question']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--DESCRIPTION-->
    <label for="description">Description/Background Information</label>
    <?php echo form_error('description'); ?>
    <?php echo form_textarea( array( 'class'=>'tinymce', 'name' => 'description', 'rows' => '5', 'cols' => '80', 'value' => set_value('description',$default['description']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--LINK MAPPING INSTRUCTIONS-->
    <label for="description">Instructions for Link Mapping</label>
    <?php echo form_error('link_mapping_info'); ?>
    <?php echo form_textarea( array( 'class'=>'tinymce', 'name' => 'link_mapping_info', 'rows' => '5', 'cols' => '80', 'value' => set_value('link_mapping_info',$default['link_mapping_info']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--TAG SEED-->
    <label>Tag Seed (separate tags by commas)</label>
    <?php echo form_textarea( array('id'=>'tag-seed', 'name' => 'tag_seed', 'rows' => '5', 'cols' => '80', 'value' => set_value('tag_seed',$default['tag_seed']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--INTRO VIDEO EMBED-->
    <label>Issue Submission Video Embed Code (Copy and Paste from Youtube or Vimeo)</label>
    <?php echo form_textarea( array('name' => 'video_embed', 'rows' => '5', 'cols' => '80', 'value' => $default['video_embed'] ) )?>
    <div class='clearer'></div>
    <br/>
  <!--LINK MAPPING VIDEO EMBED-->
    <label>Link Mapping Video Embed Code (Copy and Paste from Youtube or Vimeo)</label>
    <?php echo form_textarea( array('name' => 'video_embed_link_mapping', 'rows' => '5', 'cols' => '80', 'value' => $default['video_embed_link_mapping'] ) )?>
    <div class='clearer'></div>
    <br/>

  <!--NUMBER OF ISSUES PER PARTICIPANT-->
  <div class='third-wide-form' style='border-left:none;'>
    <label for="numberOfIssuesPerParticipant">Max # of Issues/Participant for Issue Submission</label>
    <?php echo form_error('numberOfIssuesPerParticipant'); ?>
    <input class='number-text' type='text' name='numberOfIssuesPerParticipant' value='<?php echo $default['numberOfIssuesPerParticipant']?>'/>
  </div>
  <div class='third-wide-form'>
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
  <div class='third-wide-form'>
    <label for="numberOfIssuesPerParticipant">Min # of From Issues/Part. for Link Mapping</label>
    <?php echo form_error('numberOfFromIssuesPerParticipant'); ?>
    <input class='number-text' type='text' name='numberOfFromIssuesPerParticipant' value='<?php echo $default['numberOfFromIssuesPerParticipant']?>'/> of <?php echo $total_issues?>
  </div>
  <div class='clearer'></div>
  <br/>
   <!--URL-->
    <label for="name">Project URL for User Registration (Copy, Paste, and Email this URL to users for registration) </label>
    <input id='project-url' class='text-input text-input-wide' readonly="readonly" type='text' name='final-url' value='<?php echo base_url() . "login/project_login/" . $default['url']?>'/>
  <div class='clearer'></div>
  <br/>
  <br/>
  <br/>
  <?php if($isProjectSaveSuccess):?>
    <div class='form-success'>Project Successfully Saved!</div>
  <?php endif;?>
  <!--SUBMIT BUTTONS-->
  <a class='submit-but issues-csv-download' href='<?php echo base_url() . "projects/download_issues_csv/" . $default['id'] ?>'>Download Issues CSV</a>
  <?php $data = array(
    'id' => 'wide-submit',
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Save');
    ?>
  <?php echo form_submit($data); ?><div class='but-right'></div>
</div>




<?php echo form_close(); ?>
<div class='clearer'></div>

<!--USER CREATE/DELETE FOR PROJECT-->
<div class='left-form'>
<h2>Create or Delete Users for Project</h2>

<!--USER CREATE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/create_user/#scroll', $attributes); ?>
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
  <?php if($isCreateUserSuccess):?>
  <div class='form-success'>Users Successfully Created!</div>
  <?php endif;?>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>


  <div class='vert-line'></div>


<!--USER DELETE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/delete_user/#scroll', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box right-box' style='margin-left:-1px'>
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
  <p>
    <div class='submit'>
    <?php $data = array(
      'class'=>'submit-but inside-box-submit',
      'id'=>'user-delete',
      'name'=>'submit',
      'value'=>'Delete');
      ?>
    <?php echo form_submit($data); ?>
    </div>
  </p>
  <?php if($isDeleteUserSuccess):?>
  <div class='form-success'><?php echo $deleted_user?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>


<div class='left-form' style='margin-left:24px; margin-right:-24px;'>
<h2>Create or Delete Text Inputs for Users</h2>


<!--ADD/CREATE USER ATTRIBUTE FORM-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/add_user_attribute/#scroll', $attributes); ?>
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
  <p>
    <label for="new_user_attr">or New User Attribute Name</label>
    <?php echo form_error('new_user_attr'); ?>
    <input class='text-input' type='text' name='new_user_attr' value='<?php echo set_value('new_user_attr') ?>'/>

    <input type='hidden' name='new_user_attr_type' value='ta'/>
    <!--<label for="new_user_attr_type">Attribute Type</label>
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
  </div>-->
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
  <?php if($isAddUserAttrSuccess):?>
  <div class='form-success'>User Attribute Successfully Added!</div>
  <?php endif;?>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>

  <div class='vert-line'></div>

<!--DELETE USER PROJECT ATTRIBUTES-->
<?php
$attributes = array('class' => 'left-form one-wide-form');
echo form_open('projects/delete_user_attribute/#scroll', $attributes); ?>
<input type='hidden' name='current_project' value='<?php echo $current_project?>'/>
<div class='input-box light-box left-box' style='margin-left:-1px'>
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
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but inside-box-submit',
    'id'=>'attr-delete',
    'name'=>'submit',
    'value'=>'Delete');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
  <br/>
  <?php if($isDeleteUserAttrSuccess):?>
  <div class='form-success'><?php echo $deleted_user_attr?> Successfully Removed From Project!</div>
  <?php endif;?>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>

<div class='clearer'></div>
</div>

<!-- 

<div class='clearer'></div>
<div id='bottom'></div>
