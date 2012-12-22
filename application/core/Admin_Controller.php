<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {

	public function __construct(){
    parent::__construct();

    //see if logged in
    if($this->session->userdata('logged_in') && $this->session->userdata('isAdministrator'))
    {
    	$this->is_logged_in = TRUE;
    } else if($this->uri->segment(1) != 'login')
    {
			//if not logged in, redirect to login
			if($this->session->userdata('logged_in'))
			{
				redirect('');
			} else
			{
	      redirect('login');
			}
    }
  }
}

/* End of file Admin_Controller.php */
/* Location: ./application/controllers/Admin_Controller.php */