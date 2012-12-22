<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Controller extends MY_Controller {

	public function __construct(){
    parent::__construct();

    //see if logged in
    $this->user = $this->session->userdata('user');
    if($this->session->userdata('logged_in'))
    {
    	$this->is_logged_in = TRUE;
    } else if($this->uri->segment(1) != 'login')
    {
      //show login (regardless of whether admin user or normal user)
      redirect('login');
    }
  }
}

/* End of file User_Controller.php */
/* Location: ./application/controllers/User_Controller.php */