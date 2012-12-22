<div id='top-space'>
</div>
<h1><span class='blue'><?php echo $user_email?>'s</span> User Information</h1>
<!--PROJECT EDIT FORM-->
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'wide-form', 'id' => 'register-form','style'=>'float:left; width:auto;');
echo form_open('login/edit_user', $attributes); ?>
<input type='hidden' name='is_new_user' value='<?php echo $isNewUser?>'/>
<input type='hidden' name='user_email' value='<?php echo $user_email?>'/>
<input type='hidden' name='project_id' value='<?php echo $project_id?>'/>
<!--PASSWORD-->
<div class='left-form'>
<h2>Basic Information</h2>
<div class='one-wide-form left-form'>
  <div class='input-box left-box dark-box'>
      <?php echo form_error('first_name'); ?>
      <label for="first_name">First Name</label>
      <input name="first_name" class="text-input" value="<?php echo set_value('first_name',$default['first_name']);?>"/>
      <label for="last_name">Last Name</label>
      <?php echo form_error('last_name'); ?>
      <input name="last_name" class="text-input" value="<?php echo set_value('last_name',$default['last_name']);?>"/>
    <div class="clearer"></div>
  </div>
</div>


<div class='vert-line'></div>


<div class='one-wide-form left-form'>
  <div class='input-box left-box dark-box' style='margin-left:-1px'>
      <?php if($isNewUser):?>
      <label for="user_pass">Create Password</label>
      <?php else:?>
      <label for="user_pass">Change Password</label>
      <?php endif;?>
      <input class='text-input' type='password' name='user_pass' value=''/>
      <?php if($isNewUser == FALSE):?>
      <div class='small-text'>(Leave Blank for No Change)</div>
      <?php endif;?>
      <label for="user_pass_confirm">Password Confirm</label>
      <input class='text-input' type='password' name='user_pass_confirm' value=''/>
    <?php echo form_error('user_pass'); ?>
  </div>
  <?php if(isset($default['inputs']) == false):?>
  <!--SUBMIT BUTTONS-->
  <p>
    <?php $data = array(
      'id'=>'wide-submit',
      'class'=>'submit-but',
      'name'=>'submit',
      'value'=>'Save',
      'style'=>'margin-right:11px;');
      ?>
    <?php echo form_submit($data); ?><div class='but-right'></div>
  </p>
  <?php endif;?>
</div>
</div>

<?php
//1, not 0 because first box is static
$i = 1;
if(array_key_exists('inputs', $default))
{
foreach($default['inputs'] as $inp)
{
?>
<?php if($i%2 == 0):?>
<div class='two-wide-form'>
<?php else:?>
<div class='two-wide-form two-wide-right'>
<?php endif;?>
<h2><?php echo $inp->name; ?></h2>
  <?php if($i%2 == 1):?>
    <?php if($i%4 == 3):?>
    <div class='input-box light-box right-box'>
    <?php else:?>
    <div class='input-box light-box'>
    <?php endif;?>
  <?php else:?>
    <?php if($i%4 == 0):?>
    <div class='input-box dark-box left-box'>
    <?php else:?>
    <div class='input-box dark-box'>
    <?php endif;?>
  <?php endif;?>

  <?php echo form_textarea( array( 'name' => 'input_' . $inp->id, 'rows' => '5', 'cols' => '80', 'value' => set_value('input_' . $inp->id,$inp->value) ) )?>
  
  </div>

  <?php if($i == count($default['inputs'])):?>
  <!--SUBMIT BUTTONS-->
  <p>
    <?php $data = array(
      'id'=>'wide-submit',
      'class'=>'submit-but',
      'name'=>'submit',
      'value'=>'Save');
      ?>
    <?php echo form_submit($data); ?><div class='but-right'></div>
  </p>
  <?php endif;?>
</div>


<?php
$i++;
}
}
?>
<div class='clearer'></div>

<?php if($isUserSaveSuccess):?>
<div class='form-success'>User Information Saved!</div>
<?php endif;?>


<?php echo form_close(); ?>
<div class='clearer'></div>