<?php
/**
 * This class is used to redirect to users current state
 */
class Start extends User_Controller {

               
	public function __construct()
	{
	 	parent::__construct();
	 	$this->load->model('Login_model');
	}	

	/**
	 * index is not working for this controller
	 * could be a namespacing thing, I dunno, moving on...
	 */
	public function index()
	{
		if($this->session->userdata('isAdministrator'))
		{
			redirect('admin');
		} else
		{
			$rd = $this->Login_model->get_project_state_url();
			redirect($rd);
		}
	}
}