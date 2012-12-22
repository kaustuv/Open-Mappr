<?php

class Admin extends Admin_Controller {
               
	public function __construct()
	{
	 	parent::__construct();
	 	$this->load->library('form_validation');
	 	$this->load->database();
	 	$this->load->helper('form');
	 	$this->load->model('Admin_model');
	 	$this->load->model('Login_model');
	}	

	/**
	 * Show project list and admin user creator form
	 */
	public function index()
	{
		$this->_set_form_errors();

		//get data for page
		$this->_set_index_data();
		
		//load initial admin view
		$this->load_container_with('admin_view','ADMINISTRATION');
	}

	/**
	 * Create admin user
	 */
	public function create_admin()
	{
		$this->_set_form_errors();

		//validation for creating new user
		$this->form_validation->set_rules('new_admin_user_email', 'User Email', 'required|valid_email|callback_user_admin_check');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
	
		if($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//reload form
			//get data for page
			$this->_set_index_data();
			//load initial admin view
			$this->load_container_with('admin_view','ADMINISTRATION');
		} else
		{
			$nUE = $this->input->post('new_admin_user_email');
			//create admin user for db
			$this->Admin_model->create_admin_user($nUE);
			$this->view_data['createdAdminUser'] = $nUE;
			//show success view
			$this->view_data['isCreateAdminSuccess'] = TRUE;
			//get data for page
			$this->_set_index_data();
			//load initial admin view
			$this->load_container_with('admin_view','ADMINISTRATION');
		}
		
	}

	public function delete_admin()
	{
		$this->_set_form_errors();

		$this->form_validation->set_rules('delete_admin_user','Admin User','trim|xss_clean');	
		$aId = $this->input->post('delete_admin_user');
		if($aId == '')
		{
			$this->view_data['isDeleteAdminError'] = TRUE;
			//get data for page
			$this->_set_index_data();
			//load initial admin view
			$this->load_container_with('admin_view','ADMINISTRATION');
		} else
		{
			$this->view_data['deletedAdminUser'] = $this->Admin_model->get_admin_email_by_id($aId);
			//delete user
			$this->Admin_model->delete_user($aId);
			//get data for page
			$this->_set_index_data();
			$this->view_data['isDeleteAdminSuccess'] = TRUE;
			$this->load_container_with('admin_view','ADMINISTRATION');			
		}
	}

	/**
	 * Checks to make sure not duplicating an admin user
	 * (can duplicate a normal user if assigned to more than one project)
	 */
	public function user_admin_check($em)
	{
		if($this->Admin_model->check_is_admin_user($em))
		{
			$this->form_validation->set_message('user_admin_check', '%s is already an administrator');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		
	}

	/**
	 * View for editing a project
	 */
	public function projects()
	{
		$this->_set_form_errors();
		$this->form_validation->set_rules('project','Project','trim|xss_clean');	
		$this->form_validation->set_rules('new_project','New Project','trim|xss_clean|callback_check_project_url');


		$nPN = trim($this->input->post('new_project'));
		$pId = $this->input->post('project');

		//see if has entered something into project form
		if(($pId == "" && $nPN == "") || $this->form_validation->run() == FALSE)
		{
			//if not, then reshow admin view with error
			$this->view_data['isProjectError'] = TRUE;
			//get data for page
			$this->_set_index_data();
			//load initial admin view
			$this->load_container_with('admin_view','ADMINISTRATION');

		} else
		{
			//if new project, insert and get back 
			if($nPN != "")
			{
				$pId = $this->Admin_model->add_new_project($nPN);
			}
			//show project editing view
			redirect('projects/edit/'.$pId);

		}
		
	}

	public function check_project_url($name)
	{
		$isUnique = $this->Admin_model->check_project_url($name);
		$this->form_validation->set_message('check_project_url', 'Another project has this name or a very similar name. Please choose another name.');
		return $isUnique;
	}

	public function delete_project()
	{
		$this->_set_form_errors();
		$this->_set_index_data();

		$pId = $this->input->post('delete_project');
		if($pId == '')
		{
			$this->view_data['isDeleteProjectError'] = TRUE;
			$this->load_container_with('admin_view','ADMINISTRATION');
			
		} else
		{
			$this->view_data['deletedProject'] = $this->Admin_model->get_project_by_id($pId);
			if($this->Admin_model->delete_project($pId))
			{
				//reget data for page minus project
				$this->_set_index_data();
				$this->view_data['isDeleteProjectSuccess'] = TRUE;
				//reload view
				$this->load_container_with('admin_view','ADMINISTRATION');
				
			} else
			{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');	
			}
		}
	}

	public function work_on_project()
	{
		$pId = $this->input->post('project');
		if($pId == "")
		{
			$this->_set_form_errors();
			$this->_set_index_data();
			$this->view_data['isCurrentProjectError'] = TRUE;
			$this->load_container_with('admin_view','ADMINISTRATION');
			
		} else
		{
			//set current pID and redirect to correct state for project
			$this->session->set_userdata('user_project_id',$pId);
			$rd = $this->Login_model->get_project_state_url();
			redirect($rd);
		}
	}

	/**
	 * Set initial values (FALSE) for form errors
	 */
	private function _set_form_errors()
	{
		$this->view_data['isProjectError'] = FALSE;
		$this->view_data['isDeleteAdminError'] = FALSE;
		$this->view_data['isCreateAdminSuccess'] = FALSE;
		$this->view_data['isDeleteAdminSuccess'] = FALSE;
		$this->view_data['isDeleteProjectSuccess'] = FALSE;
		$this->view_data['isDeleteProjectError'] = FALSE;
		$this->view_data['isCurrentProjectError'] = FALSE;
		
		
	}

	/**
	 * Set data common for index page of admin
	 */
	private function _set_index_data()
	{
		//get project list
		$this->view_data['projects_ar'] = $this->Admin_model->get_projects_list();
		//get admin users
		$this->view_data['admin_users'] = $this->Admin_model->get_admin_users();	
		//get projects that admin can work on
		$this->view_data['current_projects'] = $this->Admin_model->get_current_projects();
	}





}
?>