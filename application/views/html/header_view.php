<div id='header'>
	<?php if($this->session->userdata('logged_in')):?>
	<div class='menu-buttons'><a class='submit-but2' href='<?php echo site_url()?>login/logout'>Logout</a>
		<?php if($this->session->userdata('isAdministrator')):?>
			<?php if(uri_string() == 'admin'):?>
			<div class='submit-but2 submit-but-inactive'>Admin</div>
			<?php else:?>
			<a class='submit-but2' href='<?php echo site_url()?>admin'>Admin</a>
			<?php endif;?>
		<?php endif;?>
		<?php if(uri_string() == "login/edit_user"):?>
		<div class='submit-but2 submit-but-inactive'>Profile</div>
		<?php else: ?>
		<a class='submit-but2' href='<?php echo site_url()?>login/edit_user'>Profile</a>
		<?php endif;?>
		<div class='clearer'></div>
		<?php
		if(trim($this->session->userdata('first_name')) == '')
		{
			$str = $this->session->userdata('user_email');
		} else
		{
			$str = $this->session->userdata('first_name') . " " . $this->session->userdata('last_name');
		}
		?>
		<h2 class='user-name'>Welcome <a href='<?php echo site_url()?>login/edit_user'><?php echo $str?></a></h2>
	</div>
	<?php endif;?>
</div>
