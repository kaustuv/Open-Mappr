<?php

class Admin_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------

	/**
	 * gets the list of projects and id
	 */
	public function get_projects_list()
	{
		$this->db->select('id,name');
		$this->db->order_by('name','asc');
		$query = $this->db->get('projects');
		return $query->result_array();
	}

	/**
	 * get list of admin users 
	 */
	public function get_admin_users()
	{
		$this->db->where('isAdministrator',1);
		$this->db->order_by('user_email','asc');
		$this->db->select('user_id,user_email');
		$query = $this->db->get('users');
		return $query->result_array();
	}

	
	public function get_admin_email()
	{
		
    $pId = $this->session->userdata('user_project_id');
    $this->db->where('id',$pId);
    $this->db->select('admin_email');
    $this->db->limit(1);
    $query = $this->db->get('projects');
    if($query->num_rows() == 0)
    {
    	$email = "admin";
    } else
    {
    	$email = $query->row('admin_email');
    }
    return $email;

	}

	public function get_admin_email_by_id($id)
	{
		$this->db->where('user_id',$id);
		$this->db->select('user_email');
		$this->db->limit(1);
		$query = $this->db->get('users');
		return $query->row('user_email');
	}

	/**
	 * Form validation to make sure admin email is unique
	 */
	public function check_is_admin_user($em)
	{
		$this->db->where(array('user_email'=>$em,'isAdministrator'=>1));
		$this->db->limit(1);
		$query = $this->db->get('users');
		if($query->num_rows() >= 1)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}

	/**
	 * Creates admin user or edits normal user to be admin
	 */
	public function create_admin_user($nUE)
	{
		$query = $this->db->get_where('users',array('user_email'=>$nUE));
		if($query->num_rows() == 1)
		{
			$this->db->where('user_email',$nUE);
			$this->db->update('users',array('isAdministrator'=>1));
		} else
		{
			//create user with simple login secure
			//create random password
			$this->load->helper('string');
			$pass = random_string('alnum', 8);
			$this->simpleloginsecure->create($nUE, $pass, FALSE);
			//update user data
			$data = array('isAdministrator'=>1);
			$this->db->where('user_email',$nUE);
			$this->db->limit(1);
			$this->db->update('users',$data);
			//send email to user to register 
			//get actual password field for use in string for link
			$this->db->where('user_email',$nUE);
			$this->db->select('user_pass');
			$this->db->limit(1);
			$query = $this->db->get('users');
			$enc_pass = $query->row('user_pass');
			//remove slashes so can use in url 
			//(will check against pass with slashes removed)
			$enc_pass = str_replace(array('/','.'),'', $query->row('user_pass'));
			$this->db->where('user_email',$nUE);
			$this->db->limit(1);
			$data = array('user_pass'=>'',
										'user_register_pass'=>$enc_pass);
			$this->db->update('users',$data);

			//
			$this->load->library('email');
			$config['protocol'] = 'sendmail';
			$config['charset'] = 'iso-8859-1';
			$config['crif'] = '\r\n';
			$config['wordwrap'] = TRUE;

			$this->email->initialize($config);

			$admin_email = $this->Admin_model->get_admin_email();
			$this->email->from($admin_email, 'Mappr Administrator');
			$this->email->to($nUE);

			$this->email->subject('Mappr User Registration');
			$this->email->message("You have been registered as an Administrator for Vibrant Data's remote facilitation software.\n" .
															"To complete your registration, click the link below: " . base_url() . "login/register/" . urlencode($nUE) . '/' . urlencode($enc_pass) . ' \n\n' . 
															"To login again once you have registered, please visit the following url: " . base_url());	

			$this->email->send();
		}
	}

	public function delete_project($pId)
	{
		$this->db->where('id',$pId);
		$this->db->limit(1);
		$this->db->delete('projects');
		return TRUE;
	}

	public function get_project_by_id($pId)
	{
		$this->db->where('id',$pId);
		$this->db->select('name');
		$this->db->limit(1);
		$query = $this->db->get('projects');
		return $query->row('name');
	}

	/**
	 * delete admin user by id
	 */
	public function delete_user($aId)
	{
		$this->db->where('user_id',$aId);
		$this->db->limit(1);
		$this->db->delete('users');
		//also delete links to user in linking table
		$this->db->where('userId',$aId);
		$this->db->delete('usersProjects');
	}

	public function add_new_project($nPN)
	{
		$url = url_title($nPN);
		$data = array('name'=>$nPN,
									'url'=>$url);
		$this->db->insert('projects',$data);
		return $this->db->insert_id();
	}

	public function check_project_url($name)
	{

		if($name == "")
		{
			return TRUE;
		}

		$url = url_title($name);


		$this->db->where('url',$url);
		$query = $this->db->get('projects');
		if($query->num_rows() == 0)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}

	/**
	 * Get current projects that the admin
	 * can work with in a workshop
	 */
	public function get_current_projects()
	{
		$this->db->order_by('name','asc');
		$query = $this->db->get('projects');
		return $query->result_array();
	}


}
?>