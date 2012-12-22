<div class='top-space'></div>
<h1 style='margin-left:0px;'>Hi!</h1>
<p>Thanks for helping us map the ‘ecosystem’ of Vibrant Data. </p>
<p>YOUR expert experience and perception is critical to this project.</p>
<br/>
<p>By participating, you’re also helping us develop an open complex problem mapping platform to better understand the world’s most pressing problems.</p>
<br/>
<p>Here you and other experts will help us convert a 'laundry list' of critical issues related to Vibrant Data into a complex 'ecosystem' of where and how those issues influence one another. </p>
<br/>
<p>From the network structure of that collectively-mapped ecosystem we hope to identify a subset of core challenges that if solved could unleash an egalitarian Vibrant Data revolution.</p>
<br/>
<p>To do that we need human brain power from experts like you and others.</p>
<p>If you know someone else who would be an amazing contributor, please contact us at <a href="mailto:participate@vibrantdata.org">participate@vibrantdata.org</a>. </p>
<br/>
<p>We realize this is work on your part, and we thank you for your time to help us and others change the world. </p>
<br/>
<p>If you have ANY questions please don’t hesitate to contact me at <a href="mailto:eric@vibrantdatalabs.org">eric@vibrantdatalabs.org</a>.</p>
<br/>
<p>We realize this is work on your part, and we thank you for your time to help us and others change the world. </p>
<br/>
<br/>
<p>Sincerely,</p>
<br/>
<p>eric l berlow, founder Vibrant Data Labs</p>
<br/>
<br/>
<div class='centerer'>
<?php // Change the css classes to suit your needs    

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
