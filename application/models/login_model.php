<?php

class Login_model extends CI_Model {

	function __construct()
	{
		parent::__construct();

	}
	
	// --------------------------------------------------------------------

	/**
	 * Wrapper for logging in
	 * @param  [type] $form_data [email and password of user]
	 * @return [Boolean]
	 */
	public function check_login($form_data)
	{
		return $this->simpleloginsecure->login($form_data['email'], $form_data['password']);
	}

	/**
	 * Checks registration url parameters against email and url 
	 * freindly password to make sure safe
	 * @param  [String] $em   [url encoded email]
	 * @param  [String] $pass [url encoded password]
	 * @return [Boolean]
	 */
	public function check_registration($em,$pass)
	{
		$this->db->limit(1);
		$this->db->where(array('user_email'=>urldecode($em),
											'user_register_pass'=>urldecode($pass)));
		$query = $this->db->get('users');
		if($query->num_rows() == 1)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}

	public function get_admin_email()
	{

    $pId = $this->session->userdata('user_project_id');
    $this->db->where('id',$pId);
    $this->db->select('admin_email');
    $this->db->limit(1);
    $query = $this->db->get('projects');
    return $query->row('admin_email');

	}

	public function get_number_of_users_projects()
	{
		$em = $this->session->userdata('user_email');
		$this->db->where('user_email',$em);
		$this->db->select('user_id');
		$query = $this->db->get('users');
		$uId = $query->row('user_id');

		$this->db->where('userId',$uId);
		$query = $this->db->get('usersProjects');
		return $query->num_rows();
	}

	public function get_users_projects()
	{
		$uId = $this->_get_user_id();
		$projectsAR = array();
		$this->db->where('userId',$uId);
		$query = $this->db->get('usersProjects');
		foreach($query->result() as $row)
		{
			$pId = $row->projectId;
			//get name of project
			$this->db->where('id',$pId);
			$this->db->select('name');
			$query2 = $this->db->get('projects');
			$pN = $query2->row('name');
			$projectsAR[] = array($pId,$pN);
		}
		return $projectsAR;
	}

	/**
	 * Gets info about a user based on email
	 * @param  [String] $em [email]
	 * @return [Array]
	 */
	public function get_user_info($em,$pId = "")
	{
		if($pId == "")
		{
			$pId = $this->session->userdata('user_project_id');
		}
		$projAtsAR = array();
		//get user id for email
		$this->db->limit(1);
		$this->db->where('user_email',$em);
		$query = $this->db->get('users');
		$uId = $query->row('user_id');
		$projAtsAR['first_name'] = $query->row('first_name');
		$projAtsAR['last_name'] = $query->row('last_name');


		return $projAtsAR;
	}


	private function _get_user_id($em = '')
	{
		if($em == '')
		{
			$em = $this->session->userdata('user_email');	
		}
		$this->db->limit(1);
		$this->db->where('user_email',$em);
		$this->db->select('user_id');
		$query = $this->db->get('users');
		$uId = $query->row('user_id');
		return $uId;
	}

	public function set_project_id($cP)
	{
		$ar = array('user_project_id'=>$cP);
		$this->session->set_userdata($ar);
	}

	public function set_project_id_from_url($url)
	{
		$this->db->limit(1);
		$this->db->where('url',$url);
		$query = $this->db->get('projects');
		if($query->num_rows() == 0)
		{
			return FALSE;
		}
		$id = $query->row('id');
		$this->set_project_id($id);
		return $id;
	}

	public function get_name_of_project()
	{
		$pId = $this->session->userdata('user_project_id');
		$this->db->limit(1);
		$this->db->where('id',$pId);
		$query = $this->db->get('projects');
		return $query->row('name');
		
	}

	//returns project id in session or latest if no session data
	public function get_project_id()
	{
		//see if project id is set
		$pId = $this->session->userdata('user_project_id');
		if($pId == FALSE)
		{
			//get user id
			$uId = $this->_get_user_id();
			//get latest project id from usersProjects
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$this->db->where('userId',$uId);
			$query = $this->db->get('usersProjects');
			$pId = $query->row('projectId');
			$this->set_project_id($pId);
		}
		return $pId;
	}

	public function get_project_id_by_email($em)
	{
		//see if project id is set
		$pId = $this->session->userdata('user_project_id');
		if($pId == FALSE)
		{
			//get user id
			$uId = $this->_get_user_id($em);
			//get latest project id from usersProjects
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$this->db->where('userId',$uId);
			$query = $this->db->get('usersProjects');
			$pId = $query->row('projectId');
			$this->set_project_id($pId);
		}
		return $pId;
	}

	public function get_registration_message()
	{
		$pId = $this->session->userdata('user_project_id');
		$this->db->where('id',$pId);
		$this->db->limit(1);
		$this->db->select('registration_message');
		$query = $this->db->get('projects');
		return $query->row('registration_message');
	}

	public function get_project_state_url()
	{
		$pId = $this->get_project_id();
		$this->db->where('id',$pId);
		$this->db->select('projectState');
		$query = $this->db->get('projects');
		if($query->num_rows() == 0)
		{
			redirect('login/logout');
		}
		$pS = $query->row('projectState');
		$this->db->where('id',$pS);
		$this->db->select('link');
		$query = $this->db->get('projectStates');
		return $query->row('link');
	}


	public function set_user_info($em,$pass,$fN,$lN,$pId)
	{
		$data = array('first_name'=>$fN,
									'last_name'=>$lN,
									'isConfirmed'=>1);
		//set session name
		$this->session->set_userdata('first_name',$fN);
		$this->session->set_userdata('last_name',$lN);
		if($pass != "")
		{
			$this->simpleloginsecure->update_pass($em, $pass, TRUE);

		}
		
		//so can't click on email link again if registering for first time
		$rand = random_string('alnum', 8);
		$data['user_register_pass'] = $rand;
		$this->db->limit(1);
		$this->db->where('user_email',$em);
		$this->db->update('users',$data);

		//get id of user with this email
		$this->db->limit(1);
		$this->db->where('user_email',$em);
		$this->db->select('user_id');
		$query = $this->db->get('users');
		$uId = $query->row('user_id');

		return TRUE;
	}

	public function is_email_in_database($em)
	{
		$this->db->where('user_email',$em);
		$this->db->limit(1);
		$query = $this->db->get('users');
		if($query->num_rows() == 1)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}

	public function send_forgot_pass_email($em)
	{
		//get actual password field for use in string for link
		$this->db->where('user_email',$em);
		$this->db->select('user_id,user_pass,user_register_pass');
		$this->db->limit(1);
		$query = $this->db->get('users');
		//get id of email
		$emId = $query->row('user_id');

		//remove slashes so can use in url 
		//(will check against pass with slashes removed)
		//see if registered with pass and if not, use old enc pass
		$uP = $query->row('user_pass');
		if($uP != "")
		{
			$enc_pass = str_replace(array('/','.'),'', $query->row('user_pass'));
		} else
		{
			$enc_pass = $query->row('user_register_pass');
		}
		$this->db->where('user_email',$em);
		$data = array('user_register_pass'=>$enc_pass);
		$this->db->update('users',$data);

		//email setup
		$this->load->library('email');
		$config['protocol'] = 'sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['crif'] = '\r\n';
		$config['wordwrap'] = TRUE;

		$admin_email = $this->get_admin_email();

		//send email to user to register 
		$this->email->from($admin_email, 'Mappr Administrator');
		$this->email->to($em);

		$this->email->subject('Mappr User Password Reset');
		$this->email->message("Click on the link below to reset your password:\n\n " . base_url() . "login/register/" . urlencode($em) . '/' . urlencode($enc_pass));	

		$this->email->send();
		//echo base_url() . "login/register/" . urlencode($em) . '/' . urlencode($enc_pass);
		
	}

	public function register_user_from_url($em,$pass)
	{
		
		$this->simpleloginsecure->create($em, $pass, FALSE);

		//get actual password field for use in string for link
		$this->db->where('user_email',$em);
		$this->db->select('user_id,user_pass,user_register_pass');
		$this->db->limit(1);
		$query = $this->db->get('users');
		//get id of email
		$emId = $query->row('user_id');

		$uP = $query->row('user_pass');
		if($uP != "")
		{
			$enc_pass = str_replace(array('/','.'),'', $query->row('user_pass'));
		}
		$this->db->where('user_email',$em);
		$data = array('user_register_pass'=>$enc_pass,
									'isConfirmed'=>0);
		$this->db->update('users',$data);


		//assign project to this user
		$pId = $this->session->userdata('user_project_id');
		if($pId == '')
		{
			redirect('');
		}
		$this->db->where('userId',$emId);
		$this->db->where('projectId',$pId);
		$query = $this->db->get('usersProjects');
		if($query->num_rows() == 0)
		{
			$data = array('userId'=>$emId,
										'projectId'=>$pId);
			$this->db->insert('usersProjects',$data);
		}



		//send email to user
		//email setup
		$this->load->library('email');
		$config['protocol'] = 'sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['crif'] = '\r\n';
		$config['wordwrap'] = TRUE;

		$admin_email = $this->get_admin_email();

		//send email to user to register 
		$this->email->from($admin_email, 'Mappr Administrator');
		$this->email->to($em);

		$this->email->subject('Mappr Registration for: ' . $this->get_name_of_project());
		$this->email->message("Click on the link below to register for this project: \n\n " . base_url() . "login/register/" . urlencode($em) . '/' . urlencode($enc_pass) . "/" . $pId);	
		$this->email->send();

		//echo base_url() . "login/register/" . urlencode($em) . '/' . urlencode($enc_pass) . "/" . $pId;


		return TRUE;
	}

	public function check_if_not_confirmed($em)
	{
		$this->db->where('user_email',$em);
		$this->db->limit(1);
		$query = $this->db->get('users');
		if(($query->row('isConfirmed') == TRUE && $query->row('user_pass') != "") || $query->row('isConfirmed') == FALSE)
		{
			return FALSE;
		} else
		{
			return TRUE;
		}

	}

	public function login_user($em)
	{
		//force login because coming from registering
		//Destroy old session
		$this->session->sess_destroy();
		
		//Create a fresh, brand new session
		$this->session->sess_create();

		$this->db->simple_query('UPDATE ' . 'users'  . ' SET user_last_login = NOW() WHERE user_id = ' . $this->_get_user_id());

		//Set session data
		$this->db->limit(1);
		$this->db->where('user_email',$em);
		$query = $this->db->get('users');
		$user_data = $query->row_array();
		unset($user_data['user_pass']);
		$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
		$user_data['logged_in'] = true;
		$this->session->set_userdata($user_data);
		

	}


}
?>