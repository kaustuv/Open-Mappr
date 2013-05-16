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


  //for showing error message if trying to download csv that doesn't
  //correspond to current project state
  $('.csv-error').click(function() {
    alert($(this).attr('title'));
    return false;
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
  <?php if(isset($form_errors)):?>
  <div style='margin-top:-20px;'></div>
  <label class='error'><?php echo $form_errors;?></label>
  <?php endif;?>
  <!--SUBMIT BUTTON-->
  <?php $data = array(
    'class'=>'submit-but inside-box-submit-top',
    'name'=>'submit',
    'value'=>'Save');
    ?>
  <?php echo form_submit($data); ?><div class='but-right'></div>
  <!--NAME-->
    <label for="name">Project Name</label>
    <?php echo form_error('name'); ?>
    <input id='project-name' class='text-input text-input-wide' type='text' name='name' value='<?php echo $default['name']?>'/>
    <div class='clearer'></div>
    <br/>

  <!--NUMBER OF ISSUES PER PARTICIPANT-->
  <div class='third-wide-form' style='border-left:none;'>
    <label for="numberOfIssuesPerParticipant">Max # of Nodes/Participant for Node Submission</label>
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
    <label for="numberOfIssuesPerParticipant">Min # of Focal Nodes/Participant for Link Mapping</label>
    <?php echo form_error('numberOfFromIssuesPerParticipant'); ?>
    <input class='number-text' type='text' name='numberOfFromIssuesPerParticipant' value='<?php echo $default['numberOfFromIssuesPerParticipant']?>'/> of <?php echo $total_issues?>
  </div>
  <div class='clearer'></div>
  <br/>

    <label>Download Nodes/Links Spreadsheets</label>
    <a class='submit-but issues-csv-download' href='<?php echo base_url() . "projects/download_issues_csv/" . $default['id'] ?>'>Download Submitted Nodes CSV</a>
    <?php if($default['projectState'] > 1):?>
    <a class='submit-but issues-csv-download' href='<?php echo base_url() . "projects/download_nodes_csv/" . $default['id'] ?>'>Download Final Curated Nodes CSV</a>
    <?php else:?>
    <a class='submit-but issues-csv-download' href='<?php echo base_url() . "projects/download_nodes_csv/" . $default['id'] ?>'>Download Nodes Template CSV</a>
    <?php endif;?>
    <?php if($default['projectState'] > 2):?>
      <a class='submit-but issues-csv-download' href='<?php echo base_url() . "links/export_links_to_csv/" . $default['id'] ?>'>Download Links CSV</a>
    <?php else:?>
      <a class='submit-but issues-csv-download csv-error' href='#' title='Sorry, but this csv is not available until you change the project state to &lsquo;Remote Link Mapping&rsquo;'>Download Links CSV</a>
    <?php endif;?>
    <a class='submit-but issues-csv-download' href='<?php echo base_url() . "links/export_users_to_csv/" . $default['id'] ?>'>Download Participants CSV</a>
    <div class='clearer'></div>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>

  <!--ADMIN EMAIL-->
    <label for="admin_email">Admin Email</label>
    <div class="small-text">(This email must be a mail account on the same server that this software is installed.)</div>
    <?php echo form_error('admin_email'); ?>
    <input id='admin-email' class='text-input text-input-wide' type='text' name='admin_email' value='<?php echo $default['admin_email']?>'/>
    <div class='clearer'></div>
   <!--QUESTION-->
    <label for="question">Question</label>
    <div class="small-text">(This question will show up in the Node Submission and Node Curation steps.)</div>
    <?php echo form_error('question'); ?>
    <?php echo form_textarea( array( 'name' => 'question', 'rows' => '5', 'cols' => '80', 'class' => 'tinymce', 'value' => set_value('question',$default['question']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--DESCRIPTION-->
    <label for="description">Description/Background Information</label>
    <div class="small-text">(This description will show up in the Node Submission and Node Curation steps.)</div>
    <?php echo form_error('description'); ?>
    <?php echo form_textarea( array( 'class'=>'tinymce', 'name' => 'description', 'rows' => '5', 'cols' => '80', 'value' => set_value('description',$default['description']) ) )?>
    <div class='clearer'></div>
    <br/>
  <!--REGISTRATION MESSAGE-->
    <label for="registration_message">Registration Message</label>
    <div class="small-text">(This message will be shown to users on this project's registration page.)</div>
    <?php echo form_error('registration_message'); ?>
    <?php echo form_textarea( array( 'class'=>'tinymce', 'name' => 'registration_message', 'rows' => '5', 'cols' => '80', 'value' => set_value('registration_message',$default['registration_message']) ) )?>
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
    <label>Node Submission Video Embed Code (Copy and Paste from Youtube or Vimeo)</label>
    <?php echo form_textarea( array('name' => 'video_embed', 'rows' => '5', 'cols' => '80', 'value' => $default['video_embed'] ) )?>
    <div class='clearer'></div>
    <br/>
  <!--LINK MAPPING VIDEO EMBED-->
    <label>Link Mapping Video Embed Code (Copy and Paste from Youtube or Vimeo)</label>
    <?php echo form_textarea( array('name' => 'video_embed_link_mapping', 'rows' => '5', 'cols' => '80', 'value' => $default['video_embed_link_mapping'] ) )?>
    <div class='clearer'></div>
    <br/>

   <!--URL-->
    <label for="name">Project URL for User Registration (Copy, Paste, and Email this URL to users for registration) </label>
    <input id='project-url' class='text-input text-input-wide' readonly="readonly" type='text' name='final-url' value='<?php echo base_url() . "login/project_login/" . $default['url']?>'/>
  <div class='clearer'></div>
  <br/>
  <br/>

  <!--SUBMIT BUTTON-->
  <?php $data = array(
    'id' => 'wide-submit',
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Save');
    ?>
  <?php echo form_submit($data); ?><div class='but-right'></div>




<?php echo form_close(); ?>
<div id='upload-status'></div>

<div class='upload-form'>
<?php echo form_open_multipart('projects/upload_nodes_csv/' . $current_project . '/#upload-status');?>
<label>Upload a spreadsheet of nodes for link Mapping</label>
<div class='small-text' style='margin-bottom:5px;'>(use &ldquo;Download Curated Nodes CSV&rdquo; as a template and do not change the column order or delete the first 12 columns)</div>
<?php if(isset($upload_error)):?>
  <?php echo $upload_error;?>
<?php endif;?>
<input class='upload-input' type="file" name="userfile" size="20" />
<div class='clearer'></div>
<input type='submit' class='submit-but issues-csv-download' value='Upload Curated Nodes CSV'/>
<div class='clearer'></div>
<br/>
<?php if($isProjectSaveSuccess):?>
  <div class='form-success'>Project Successfully Saved!</div>
<?php endif;?>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>

</div>


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



<!-- 

<div class='clearer'></div>
<div id='bottom'></div>
