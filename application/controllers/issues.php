<?php

class Issues extends User_Controller {

               
	public function __construct()
	{
	 	parent::__construct();
	 	$this->load->library('form_validation');
	 	$this->load->database();
	 	$this->load->helper('form');
	 	$this->load->model('Issues_model');
	}	

	public function index()
	{
		redirect('');
	}

	public function listing_instructions()
	{
		//check if done with listing
		$this->_check_project_state(1);

		//don't show instruction page if user has filled out at least two issues
		if(count($this->Issues_model->get_user_issues_list())>1)
		{
			redirect('issues/listing');
		}
		
		//show instructions page
		$this->view_data['project_info'] = $this->Issues_model->get_project_info();
		$this->load_container_with('issues_listing_instruction_view','ISSUES LISTING INSTRUCTIONS');
	}

	private function _check_project_state($pS)
	{
		$pd = $this->Issues_model->get_project_info();
		$pState = $pd['projectState'];
		if($pState != $pS)
		{
			redirect('');
		}
	}

	//form to list out issues
	public function listing()
	{
		//check if done with listing
		$this->_check_project_state(1);

		//set initial vars for page
		$this->view_data['project_info'] = $this->Issues_model->get_project_info();
		$this->view_data['user_notes'] = $this->Issues_model->get_user_notes();
		if($this->view_data['project_info'] == FALSE)
		{
			redirect('');
		}
		$this->view_data['issues_list'] = $this->Issues_model->get_user_issues_list();
		$this->view_data['tags'] = $this->Issues_model->get_project_issue_tags();
		$this->load_container_with('issues_listing_view',"ISSUE LISTING");

	}

	public function get_issue_tags()
	{
		$input = $this->input->get('q');
		$data = $this->Issues_model->get_issue_tags_like($input);
		$tJson = array();
		foreach($data as $key=>$value)
		{
			$jsn = array();
			$jsn['value'] = $value;
			$jsn['name'] = $value;
			$tJson[] = $jsn;
		}
		header("Content-type: application/json");
		echo json_encode($tJson);
	}

	public function get_node_tags()
	{
		$input = $this->input->get('q');
		$data = $this->Issues_model->get_node_tags_like($input);
		$tJson = array();
		foreach($data as $key=>$value)
		{
			$jsn = array();
			$jsn['value'] = $value;
			$jsn['name'] = $value;
			$tJson[] = $jsn;
		}
		header("Content-type: application/json");
		echo json_encode($tJson);
	}

	public function save_issue()
	{
		$form_data = array('id'=>trim($this->input->post('issue_id')),
											'name'=>trim($this->input->post('name')),
											'description'=>html_entity_decode(trim($this->input->post('description'))),
											'units'=>trim($this->input->post('units')),
											'issueOrderNum'=>trim($this->input->post('issue_order_num')));
		$cats = $this->input->post('categories');
		$id = $this->Issues_model->save_issue($form_data,$cats);
		echo $id;
	}

	public function delete_issue()
	{
		$id = $this->input->post('id');
		$this->Issues_model->delete_issue($id);
		echo $id;
	}

	public function curation()
	{
		//check if done with listing
		$this->_check_project_state(2);

		if($this->session->userdata('isAdministrator') == FALSE)
		//if(1 == 1)
		{
			redirect('issues/issue_curation_leaderboard');
		}
		//get all issues for project
		$this->view_data['issues'] = $this->Issues_model->get_project_nodes();
		//get info about project
		$this->view_data['project_info'] = $this->Issues_model->get_project_info();
		if($this->view_data['project_info'] == FALSE)
		{
			redirect('');
		}
		//get tags for all nodes in projects
		$this->view_data['tags'] = $this->Issues_model->get_project_node_tags();
		//load view
		$this->load_container_with('issues_curation_view',"ISSUE CURATION");
		
	}

	public function issue_curation_leaderboard()
	{
		//pass in project info
		//get info about project
		$this->view_data['project_info'] = $this->Issues_model->get_project_info();
		$this->view_data['user_notes'] = $this->Issues_model->get_user_notes();
		if($this->view_data['project_info'] == FALSE)
		{
			redirect('');
		}
		//just load view (all info loaded via javascript)
		$this->load_container_with('issue_curation_leaderboard_view',"ISSUE CURATION");

	}

	public function issue_submission_finished()
	{
		$this->load_container_with('issue_submission_finished_view',"ISSUE SUBMISSION IS FINISHED");
	}

	public function delete_node()
	{
		if($this->session->userdata('isAdministrator') == FALSE)
		{
			return;
		}
		$id = $this->input->post('id');
		$this->Issues_model->delete_node($id);
		echo $id;
	}

	public function save_node()
	{
		if($this->session->userdata('isAdministrator') == FALSE)
		{
			return;
		}
		$rv = $this->input->post('revisit_dd');
		if($rv == 'revisit')
		{
			$rev = 1;
		} else if($rv == 'complete')
		{
			$rev = 2;
		} else
		{
			$rev = 0;
		}

		/*if($this->input->post('is_goal') == 'goal')
		{
			$goal = 1;
		} else
		{
			$goal = 0;
		}*/
		$form_data = array('id'=>$this->input->post('issue_id'),
											'name'=>$this->input->post('name'),
											'description'=>html_entity_decode(trim($this->input->post('description'))),
											'units'=>$this->input->post('units'),
											'issueType'=>$this->input->post('issueType'),
											'votes'=>$this->input->post('votes'),
											'isRevisit'=>$rev,
											'notes'=>$this->input->post('notes'),
											/*'isGoal'=>$goal,*/
											'issueInd'=>$this->input->post('issue_ind'));
		$tID = 'as_values_'.$this->input->post('tag_id');
		$cats = $this->input->post($tID);
		$isId = $this->Issues_model->save_node($form_data,$cats);

		//delete issue if one
		$remId = $this->input->post('remove_id');
		if($remId != '')
		{
			$this->Issues_model->delete_node($remId);
		}

		echo $isId;
	}

	public function get_leaderboard_nodes()
	{
		//get all issues for project
		$issues = $this->Issues_model->get_project_nodes();
		//get tags for all nodes in projects
		$tags = $this->Issues_model->get_project_node_tags();
		$retAR = array('issues'=>$issues,'tags'=>$tags);
		echo json_encode($retAR);
	}

}