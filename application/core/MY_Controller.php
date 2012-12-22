<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	//VARS
	//id of user
	public $user = FALSE;
	//whether is logged in
	public $is_logged_in = FALSE;
	//data for view
	protected $view_data;

	//METHODS
	public function __construct(){
    parent::__construct();
  }

  //function for loading a view in the main template
  public function load_container_with($view,$title = '')
  {
	$this->view_data['page'] = $view;
	//add dash between TRU.NORTH and rest of title
	$this->view_data['page_title'] = $title;
	if($title != '')
	{
		$this->view_data['page_title'] = ' - ' . $title;	
	}
		$this->load->view('container_view',$this->view_data);	
  }
}
