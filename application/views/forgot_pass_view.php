
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'left-form one-wide-form single-form narrow-form');
echo form_open('login/forgot_pass_submit', $attributes); ?>
<h2>Forgot Password</h2>
<div class='input-box dark-box single-box'>
<p>
  <label for="email">Email</label>
  <?php echo form_error('email'); ?>
  <input class='text-input' id="email" type="text" name="email" maxlength="128" value="<?php echo set_value('email'); ?>"  />
  <span class='form-desc'>An email will be sent to this address, allowing you to reset your password.</span>
</p>
<br/>
<p>
  <div class='submit'>
  <?php $data = array(
    'class'=>'submit-but inside-box-submit',
    'name'=>'submit',
    'value'=>'Send Email');
    ?>
  <?php echo form_submit($data); ?>
  </div>
</p>
</div>
<div class='clearer'></div>
<?php echo form_close(); ?>
