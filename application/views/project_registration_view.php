<div class='top-space'></div>
<?php echo $registration_message;?>
<div class='centerer'>
<?php 

$attributes = array('class' => 'left-form one-wide-form single-form narrow-form');
echo form_open('login', $attributes); ?>
<h2>Login</h2>
<div class='input-box dark-box single-box'>
<?php if($isError):?>
<span class='error'>This login is incorrect. Please try again.</span>
<?php endif;?>
<p>
  <label for="email">Email</label>
  <?php echo form_error('email'); ?>
  <input class='text-input' id="email" type="text" name="email" maxlength="128" value="<?php echo set_value('email'); ?>"  />
</p>
<p>
  <label for="password">Password</label>
  <?php echo form_error('password'); ?>
  <input class='text-input' id="password" type="password" name="password" maxlength="32" value="<?php if($isError != TRUE) { echo set_value('password'); } ?>"  />
</p>
<a class='forgot-pass' href='<?php echo site_url()?>login/forgot_password'>Forgot Password?</a>
<br/>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Login');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>
<div class='or-holder'>
  <div class='or-text'>
    OR
  </div>
</div>
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'left-form one-wide-form single-form narrow-form');
echo form_open('login/register_user', $attributes); ?>
<h2>Register</h2>
<div class='input-box dark-box single-box'>
<?php if($isError):?>
<span class='error'>This login is incorrect. Please try again.</span>
<?php endif;?>
<p>
  <label for="email">Email</label>
  <?php echo form_error('user_email'); ?>
  <input class='text-input' id="email" type="text" name="user_email" maxlength="128" value="<?php echo set_value('user_email'); ?>"  />
</p>
<p>
  <label for="password">Password</label>
  <?php echo form_error('user_pass'); ?>
  <input class='text-input' id="password" type="password" name="user_pass" maxlength="32" value="<?php if($isError != TRUE) { echo set_value('user_pass'); } ?>"  />
</p>
<p>
  <label for="password">Password Confirm</label>
  <input class='text-input' id="password-confirm" type="password" name="user_pass_conf" maxlength="32" value="<?php if($isError != TRUE) { echo set_value('user_pass_conf'); } ?>"  />
</p>
<br/>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Register');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>
</div>
