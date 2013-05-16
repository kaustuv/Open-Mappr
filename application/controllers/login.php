<?php

class Login extends User_Controller {
               
	public function __construct()
	{
	 	parent::__construct();
	 	$this->load->library('form_validation');
	 	$this->load->database();
	 	$this->load->helper('form');
	 	$this->load->model('Login_model');
	}	
	public function index()
	{
		$this->view_data['isError'] = FALSE;
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[128]|callback_check_email_confirmed');			
		$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[32]');
			
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load_container_with('login_view','LOGIN');
		}
		else // passed validation proceed to post success logic
		{
			// build array for the model
			$form_data = array(
						'email' => set_value('email'),
						'password' => set_value('password'));
					
			// run login check
			if ($this->Login_model->check_login($form_data))
			{
				//see if admin
				if($this->session->userdata('isAdministrator'))
				{
					redirect('admin');
				} else if($this->session->userdata('user_project_id'))
				{
					$rd = $this->Login_model->get_project_state_url();
					redirect($rd);
				} else if($this->Login_model->get_number_of_users_projects() > 1)
				{
					redirect('login/choose_project');
				} else
				{
					$rd = $this->Login_model->get_project_state_url();
					redirect($rd);
				}
			}
			else
			{
				$this->view_data['isError'] = TRUE;
				$this->load_container_with('login_view','LOGIN');
			}
		}
	}

	public function check_email_confirmed($em)
	{
		$admin_email = $this->Login_model->get_admin_email();
		$isNC = $this->Login_model->check_if_not_confirmed($em);
		if($isNC)
		{
			$this->form_validation->set_message('check_email_confirmed', 'This email has not been confirmed. Please check your email for a confirmation link from '+$admin_email);
			return FALSE;
		} else
		{
			return TRUE;
		}
	}

	public function choose_project()
	{
		//get users projects for user to choose in dropdown
		$this->view_data['projects'] = $this->Login_model->get_users_projects();
		$this->view_data['isError'] = FALSE;
		$this->load_container_with('choose_project_view','CHOOSE YOUR PROJECT');
	}

	public function project_chosen()
	{
		$cP = $this->input->post('chosen_project');
		if($cP == "")
		{
			$this->view_data['isError'] = TRUE;
			$this->view_data['projects'] = $this->Login_model->get_users_projects();
			$this->view_data['isError'] = FALSE;
			$this->load_container_with('choose_project_view','CHOOSE YOUR PROJECT');
		} else
		{
			$this->Login_model->set_project_id($cP);
			//redirect to current place for project
			$rd = $this->Login_model->get_project_state_url();
			redirect($rd);
		}
	}

	public function register_user()
	{
		//get email from hidden value in form
		$em = $this->input->post('user_email');
		if($em == '')
		{
			$em = $this->session->userdata('user_email');
		}

		$pId = $this->Login_model->get_project_id();
		//email for saving off of
		$this->view_data['user_email'] = $em;
		//whether successfully saved
		$this->view_data['isUserSaveSuccess'] = FALSE;


		//password required if initial user
		$this->form_validation->set_rules('user_email', 'Email', 'required|trim|xss_clean|valid_email|max_length[128]');
		$this->form_validation->set_rules('user_pass', 'User Password', 'required|trim|xss_clean|min_length[5]|max_length[12]|matches[user_pass_conf]');	
		
		$this->form_validation->set_rules('user_pass_conf', 'Password Confirm', 'trim|xss_clean|min_length[5]|max_length[12]');	
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');

		//get name for this project
		$nm = $this->Login_model->get_name_of_project();
		$this->view_data['project_name'] = $nm;

		if ($this->form_validation->run() == FALSE)
		{
			$this->view_data['isError'] = FALSE;
			$this->view_data['registration_message'] = $this->Login_model->get_registration_message();
			//show registration or login page for this project
			$this->load_container_with('project_registration_view','REGISTRATION FOR &lsquo;' . strtoupper($nm) . '&rsquo;');
		} else
		{
			$pass = $this->input->post('user_pass');
			//insert into db and send email
			if($this->Login_model->register_user_from_url($em,$pass))
			{
				//redirect to check mail page
				$this->load_container_with('registration_success_view','REGISTRATION SUCCESSFUL');

			}


		}

		
		
	}

	public function project_login($pURL)
	{
		//set project id via url
		$pId = $this->Login_model->set_project_id_from_url($pURL);
		//error checking if not a valid url
		if($pId == FALSE)
		{
			show_404($pURL , 'log_error');
		}
		//see if user is logged in
		if($this->session->userdata('logged_in'))
		{
			//go to project directly
			//redirect to current place for project
			$rd = $this->Login_model->get_project_state_url();
			redirect($rd);

		} else
		{
			//get name for this project
			$nm = $this->Login_model->get_name_of_project();
			$this->view_data['isError'] = FALSE;
			$this->view_data['registration_message'] = $this->Login_model->get_registration_message();

			//show registration or login page for this project
			$this->load_container_with('project_registration_view','REGISTRATION FOR &lsquo;' . strtoupper($nm) . '&rsquo;');

		}
	}

	public function logout()
	{
		$this->simpleloginsecure->logout();
		redirect('login');
		//clear all session data
		$this->session->sess_destroy();
	}

	public function register($em = "",$pass = "",$pId = "")
	{
		//check to see if email and password match
		if($this->Login_model->check_registration($em,$pass) && $em != "" && $pass != "")
		{
			$em = urldecode($em);
			//email for saving off of
			$this->view_data['user_email'] = $em;
			$this->view_data['project_id'] = $pId;
			//whether successfully saved
			$this->view_data['isUserSaveSuccess'] = FALSE;
			//initial time, so  must use password unless not confirmed
			//because registered on own, so already entered a pass
			/*if($this->Login_model->check_if_not_confirmed($em))
			{
				$isNewUser = FALSE;
			} else
			{
				$isNewUser = TRUE;
			}*/
			$isNewUser = $this->Login_model->check_if_not_confirmed($em);
			$this->view_data['isNewUser'] = $isNewUser;

			$this->view_data['default'] = $this->Login_model->get_user_info($em,$pId);	
			
			//load view for user to register
			$this->load_container_with('register_view','USER REGISTRATION');

		} else
		{
			//registration is invalid (old value)
			if($this->session->userdata('logged_in'))
			{
				redirect('');
			} else
			{
				$admin_email = $this->Login_model->get_admin_email();
				$this->view_data['error_message'] = "You seem like you're trying to do something you shouldn't be doing. ";
				$this->view_data['error_message'] .= "If you feel you've reached this message in a legitimate fashion, please contact ".$admin_email." for help. or <a href='" . base_url() . "'>Login</a>";
				$this->load_container_with('error_view','ERROR');	
			}
		}
	}

	public function edit_user()
	{

		//get email from hidden value in form
		$em = $this->input->post('user_email');
		if($em == '')
		{
			$em = $this->session->userdata('user_email');
		}

		$pId = $this->input->post('project_id');
		if($pId == "")
		{
			$pId = $this->Login_model->get_project_id();	
		}
		//email for saving off of
		$this->view_data['user_email'] = $em;
		//whether successfully saved
		$this->view_data['isUserSaveSuccess'] = FALSE;
		//get whether initial time
		if($this->Login_model->check_if_not_confirmed($em) == FALSE)
		{
			$isNewUser = FALSE;
		} else
		{
			$isNewUser = $this->input->post('is_new_user');	
		}

		//special case for user login
		/*if(!$isNewUser && !$this->session->userdata('logged_in'))
		{
			redirect('login');
		}*/

		//password required if initial user
		if($isNewUser)
		{
			$this->form_validation->set_rules('user_pass', 'User Password', 'required|trim|xss_clean|min_length[5]|max_length[12]|matches[user_pass_confirm]');	
		} else
		{
			$this->form_validation->set_rules('user_pass', 'User Password', 'trim|xss_clean|min_length[5]|max_length[12]|matches[user_pass_confirm]');	
		}

		$this->form_validation->set_rules('user_pass_confirm', 'User Password Confirm', 'trim|xss_clean|min_length[5]|max_length[12]');	
		foreach($_POST as $key=>$value)
		{
		 	if(strpos($key,'input_') !== false)
		 	{
		 		$this->form_validation->set_rules($key,'','trim|xss_clean');	
		 	}
		}	

		$this->form_validation->set_error_delimiters('<span class="error">Error: ', '</span>');

		//get current user info

		if($this->form_validation->run() == FALSE)
		{


			if($this->input->post('user_email') != '')
			{
				$this->view_data['default'] = $_POST;
				$def2 = $this->Login_model->get_user_info($em,$pId);	
				if(array_key_exists('inputs', $def2))
				{
					$this->view_data['default']['inputs'] = $def2['inputs'];	
				}
			} else
			{
				$this->view_data['default'] = $this->Login_model->get_user_info($em);
			}
				
			$this->view_data['isNewUser'] = $isNewUser;
			//load view
			//load view for user to register
			$this->load_container_with('register_view','USER PROFILE EDIT');

		} else
		{
			$uP = set_value('user_pass');

			$fN = $this->input->post('first_name');
			$lN = $this->input->post('last_name');

			if($this->Login_model->set_user_info($em,$uP,$fN,$lN,$pId))
			{	
			 	$this->view_data['isNewUser'] = FALSE;
			 	$this->view_data['isUserSaveSuccess'] = TRUE;

			 	//if user not logged in, log user in
			 	$this->Login_model->login_user($em);

				//redirect to current project
				redirect('');
				
			} else
			{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
			}
			
		}

	}


	public function forgot_password()
	{
		//show view for email address
		$this->load_container_with('forgot_pass_view','FORGOT PASSWORD');
		
	}

	public function forgot_pass_submit()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_user_email_check');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load_container_with('forgot_pass_view','FORGOT PASSWORD');
		} else
		{
			//set up email row for resetting and send email to user
			$this->Login_model->send_forgot_pass_email($this->input->post('email'));
			//show successful view
			$this->load_container_with('forgot_pass_success_view','CHECK YOUR EMAIL');
			
		}
	}

	public function user_email_check($em)
	{
		$isInDB = $this->Login_model->is_email_in_database($em);
		if($isInDB)
		{
			return TRUE;
		} else
		{
			$admin_email = $this->Login_model->get_admin_email();
			$this->form_validation->set_message('user_email_check', 'This email is not in our records. Please contact '.$admin_email.' for assistance.');
			return FALSE;
		}
	}

}
?>