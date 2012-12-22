<?php

class Projects extends Admin_Controller {

	//vars
	var $attribute_type;
               
	public function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->model('Projects_model');
	}	

	/**
	 * index is not working for this controller
	 * could be a namespacing thing, I dunno, moving on...
	 */
	public function index()
	{
		redirect('admin');
	}

	public function edit($pId = -1)
	{
		if($pId == -1)
		{
			redirect('login');
		}

		$this->_set_index_data($pId);

		$this->_set_form_errors();

		$this->view_data['current_project'] = $pId;

		$this->form_validation->set_rules('name', 'Project Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('description', 'Project Description', 'trim|xss_clean');
		$this->form_validation->set_rules('question', 'Project Question', 'required|trim|xss_clean');
		$this->form_validation->set_rules('numberOfIssuesPerParticipant', '# of Issues/Participant', 'required|trim|xss_clean');
		$this->form_validation->set_rules('numberOfFromIssuesPerParticipant', '# of From Issues/Participant', 'trim|xss_clean');
		$this->form_validation->set_rules('projectState','Current Project State','trim|xss_clean');
		$this->form_validation->set_rules('tag_seed','Tag Seed','trim|xss_clean');
		$this->form_validation->set_rules('video_embed','Video Embed','trim|xss_clean');
		$this->form_validation->set_rules('link_mapping_info','Link Mapping Instructions','trim|xss_clean');
		$this->form_validation->set_rules('video_embed_link_mapping','Video Embed','trim|xss_clean');


		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			if($this->input->post('name'))
			{
				$this->view_data['default']['name'] = set_value('name');
				$this->view_data['default']['description'] = set_value('description');
				$this->view_data['default']['question'] = set_value('question');
				$this->view_data['default']['numberOfIssuesPerParticipant'] = set_value('numberOfIssuesPerParticipant');	
				$this->view_data['default']['numberOfFromIssuesPerParticipant'] = set_value('numberOfFromIssuesPerParticipant');	
				$this->view_data['default']['projectState'] = set_value('projectState');
				$this->view_data['default']['tag_seed'] = set_value('tag_seed');
				$this->view_data['default']['video_embed'] = set_value('video_embed');
				$this->view_data['default']['link_mapping_info'] = set_value('link_mapping_info');
				$this->view_data['default']['video_embed_link_mapping'] = set_value('video_embed_link_mapping');
			}

			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		}
		else // passed validation proceed to post success logic
		{
			// build array for the model
			$quest = htmlspecialchars_decode(set_value('question'),ENT_QUOTES); 
			$desc = htmlspecialchars_decode(set_value('description'),ENT_QUOTES);
			$link_info = htmlspecialchars_decode(set_value('link_mapping_info'),ENT_QUOTES);
			$url = url_title(set_value('name'));
			$form_data = array(
						'name' => set_value('name'),
						'url' => $url,
						'description' => $desc,
						'question' => $quest,
						'numberOfIssuesPerParticipant' => set_value('numberOfIssuesPerParticipant'),
						'numberOfFromIssuesPerParticipant' => set_value('numberOfFromIssuesPerParticipant'),
						'projectState' => set_value('projectState'),
						'video_embed' => html_entity_decode($this->input->post('video_embed')),
						'video_embed_link_mapping' => html_entity_decode($this->input->post('video_embed_link_mapping')),
						'link_mapping_info' => $link_info
						);
			$tag_seed = htmlspecialchars_decode(set_value('tag_seed'),ENT_QUOTES);

			if ($this->Projects_model->update_project_data($form_data,$tag_seed,$pId) == TRUE) // the information has therefore been successfully saved in the db
			{

				$this->view_data['isProjectSaveSuccess'] = TRUE;
				//get new data
				$this->view_data['default'] = $this->Projects_model->get_project_info($pId);
				//load view for editing project
				$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
			}
			else
			{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
			}
		}
	}


	public function create_user()
	{
		
		$pId = $this->input->post('current_project');
		$this->view_data['current_project'] = $pId;
		$this->_set_form_errors();

		$this->form_validation->set_rules('new_user_emails', 'User Emails', 'required|trim|xss_clean|callback_check_emails');
		if($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//get current project data
			$this->_set_index_data($pId);
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		} else
		{
			//move email list to array
			$emAR = explode(',',$this->input->post('new_user_emails'));
			//insert new users into db
			if($this->Projects_model->create_users_for_project($emAR,$pId))
			{

				//get current project data
				$this->_set_index_data($pId);
				$this->view_data['isCreateUserSuccess'] = TRUE;
				//load view for editing project
				$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
				
			} else
			{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
			}
		}
	}

	public function check_emails($str)
	{
		$emAR = explode(",",$str);

		$this->load->helper('email');
		foreach($emAR as $em)
		{
			if(!valid_email(trim($em)))
			{
				$this->form_validation->set_message('check_emails','&lsquo;'. $em . '&rsquo; is not a valid email');
				return FALSE;
			}
		}
		return TRUE;
	}

	public function delete_user()
	{
		$pId = $this->input->post('current_project');
		$this->view_data['current_project'] = $pId;
		$this->_set_form_errors();
		$uId = $this->input->post('delete_project_user');
		if($uId == '')
		{
			//get current project data
			$this->_set_index_data($pId);
			$this->view_data['isDeleteUserError'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));

		} else
		{
			$this->view_data['deleted_user'] = $this->Projects_model->get_user_email_by_id($uId);
			$this->Projects_model->remove_user_from_project($uId,$pId);
			$this->view_data['isDeleteUserSuccess'] = TRUE;
			//get current project data
			$this->_set_index_data($pId);
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		}
	}


	public function add_user_attribute()
	{
		$pId = $this->input->post('current_project');
		$oldAt = $this->input->post('user_attr');
		$newAt = $this->input->post('new_user_attr');
		$atType = $this->input->post('new_user_attr_type');
		$this->view_data['current_project'] = $pId;

		//set to global so that callback function can check against
		//for min and max values if int
		$this->attribute_type = $atType;

		$this->_set_form_errors();

		if($oldAt != '')
		{
			$this->_set_index_data($pId);
			//using predefined attribute
			//assign this attribute to this project
			$this->Projects_model->assign_prev_user_attr_to_project($oldAt,$pId);
					$this->_set_index_data($pId);
					//successfully saved to db
					$this->view_data['isAddUserAttrSuccess'] = TRUE;
					$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		} else
		{
			//creating new attribute
			//set error validation

			$this->form_validation->set_rules('new_user_attr', 'Attribute Name', 'required|trim|xss_clean|is_unique[usersAttributes.name]');
			$this->form_validation->set_rules('new_user_attr_type', 'Attribute Type', 'required|trim|xss_clean');
			$this->form_validation->set_rules('min_user_attr', 'Min Value', 'trim|integer|xss_clean|callback_min_attr_check');
			$this->form_validation->set_rules('max_user_attr', 'Max Value', 'trim|integer|xss_clean|callback_max_attr_check');


			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{

				$this->_set_index_data($pId);
				$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
			} else
			{
				//successful
				$form_data = array('name'=>set_value('new_user_attr'),
													'type'=>set_value('new_user_attr_type'),
													'min'=>set_value('min_user_attr'),
													'max'=>set_value('max_user_attr'));

				if($this->Projects_model->assign_new_user_attr_to_project($pId,$form_data) == TRUE)
				{
					$this->_set_index_data($pId);
					//successfully saved to db
					$this->view_data['isAddUserAttrSuccess'] = TRUE;
					$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
				} else
				{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
				}
			}
		}
	}


	public function delete_user_attribute()
	{
		$pId = $this->input->post('current_project');
		$this->view_data['current_project'] = $pId;
		$this->_set_form_errors();
		$atId = $this->input->post('delete_project_user_attr');
		if($atId == '')
		{
			$this->_set_index_data($pId);
			//didn't choose anything
			$this->view_data['isDeleteUserAttrError'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));

		} else
		{
			$this->view_data['deleted_user_attr'] = $this->Projects_model->get_user_attr_by_id($atId);
			$this->Projects_model->remove_user_attr_from_project($atId,$pId);
			$this->_set_index_data($pId);
			$this->view_data['isDeleteUserAttrSuccess'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		}
	}


	public function add_node_attribute()
	{
		$pId = $this->input->post('current_project');
		$oldAt = $this->input->post('node_attr');
		$newAt = $this->input->post('new_node_attr');
		$atType = $this->input->post('new_node_attr_type');
		$this->view_data['current_project'] = $pId;

		//set to global so that callback function can check against
		//for min and max values if int
		$this->attribute_type = $atType;

		$this->_set_form_errors();

		if($oldAt != '')
		{
			$this->_set_index_data($pId);
			//using predefined attribute
			//assign this attribute to this project
			$this->Projects_model->assign_prev_node_attr_to_project($oldAt,$pId);
					$this->_set_index_data($pId);
					//successfully saved to db
					$this->view_data['isAddNodeAttrSuccess'] = TRUE;
					$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		} else
		{
			//creating new attribute
			//set error validation

			$this->form_validation->set_rules('new_node_attr', 'Attribute Name', 'required|trim|xss_clean|is_unique[nodesAttributes.name]');
			$this->form_validation->set_rules('new_node_attr_type', 'Attribute Type', 'required|trim|xss_clean');
			$this->form_validation->set_rules('min_node_attr', 'Min Value', 'trim|integer|xss_clean|callback_min_attr_check');
			$this->form_validation->set_rules('max_node_attr', 'Max Value', 'trim|integer|xss_clean|callback_max_attr_check');


			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->_set_index_data($pId);
				$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
			} else
			{
				//successful
				$form_data = array('name'=>set_value('new_node_attr'),
													'type'=>set_value('new_node_attr_type'),
													'min'=>set_value('min_node_attr'),
													'max'=>set_value('max_node_attr'));

				if($this->Projects_model->assign_new_node_attr_to_project($pId,$form_data) == TRUE)
				{
					$this->_set_index_data($pId);
					//successfully saved to db
					$this->view_data['isAddNodeAttrSuccess'] = TRUE;
					$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
				} else
				{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
				}
			}
		}
	}


	public function delete_node_attribute()
	{
		$pId = $this->input->post('current_project');
		$this->view_data['current_project'] = $pId;
		$this->_set_form_errors();
		$atId = $this->input->post('delete_project_node_attr');
		if($atId == '')
		{
			$this->_set_index_data($pId);
			//didn't choose anything
			$this->view_data['isDeleteNodeAttrError'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));

		} else
		{
			$this->view_data['deleted_node_attr'] = $this->Projects_model->get_node_attr_by_id($atId);
			$this->Projects_model->remove_node_attr_from_project($atId,$pId);
			$this->_set_index_data($pId);
			$this->view_data['isDeleteNodeAttrSuccess'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		}
	}


	public function add_link_attribute()
	{
		$pId = $this->input->post('current_project');
		$oldAt = $this->input->post('link_attr');
		$newAt = $this->input->post('new_link_attr');
		$atType = $this->input->post('new_link_attr_type');
		$this->view_data['current_project'] = $pId;

		//set to global so that callback function can check against
		//for min and max values if int
		$this->attribute_type = $atType;

		$this->_set_form_errors();

		if($oldAt != '')
		{
			$this->_set_index_data($pId);
			//using predefined attribute
			//assign this attribute to this project
			$this->Projects_model->assign_prev_link_attr_to_project($oldAt,$pId);
			$this->_set_index_data($pId);
			//successfully saved to db
			$this->view_data['isAddLinkAttrSuccess'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		} else
		{
			//creating new attribute
			//set error validation

			$this->form_validation->set_rules('new_link_attr', 'Attribute Name', 'required|trim|xss_clean|is_unique[linksAttributes.name]');
			$this->form_validation->set_rules('new_link_attr_type', 'Attribute Type', 'required|trim|xss_clean');
			$this->form_validation->set_rules('min_link_attr', 'Min Value', 'trim|integer|xss_clean|callback_min_attr_check');
			$this->form_validation->set_rules('max_link_attr', 'Max Value', 'trim|integer|xss_clean|callback_max_attr_check');


			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->_set_index_data($pId);
				$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
			} else
			{
				//successful
				$form_data = array('name'=>set_value('new_link_attr'),
													'type'=>set_value('new_link_attr_type'),
													'min'=>set_value('min_link_attr'),
													'max'=>set_value('max_link_attr'));

				if($this->Projects_model->assign_new_link_attr_to_project($pId,$form_data) == TRUE)
				{
					$this->_set_index_data($pId);
					//successfully saved to db
					$this->view_data['isAddLinkAttrSuccess'] = TRUE;
					$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
				} else
				{
				$this->view_data['error_message'] = "Error saving to the database. Please try again later.";
				$this->load_container_with('error_view','ERROR');
				}
			}
		}
	}


	public function delete_link_attribute()
	{
		$pId = $this->input->post('current_project');
		$this->view_data['current_project'] = $pId;
		$this->_set_form_errors();
		$atId = $this->input->post('delete_project_link_attr');
		if($atId == '')
		{
			$this->_set_index_data($pId);
			//didn't choose anything
			$this->view_data['isDeleteLinkAttrError'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));

		} else
		{
			$this->view_data['deleted_link_attr'] = $this->Projects_model->get_link_attr_by_id($atId);
			$this->Projects_model->remove_link_attr_from_project($atId,$pId);
			$this->_set_index_data($pId);
			$this->view_data['isDeleteLinkAttrSuccess'] = TRUE;
			$this->load_container_with('project_edit_view','PROJECT EDIT: '.strtoupper($this->view_data['default']['name']));
		}
	}

	public function min_attr_check($str)
	{
		if($this->attribute_type == "int" && $str == "")
		{
			$this->form_validation->set_message('min_attr_check','Please enter in a min value for the attribute');
			return FALSE;
		}
		return TRUE;
	}

	public function max_attr_check($str)
	{
		if($this->attribute_type == "int" && $str == "")
		{
			$this->form_validation->set_message('max_attr_check','Please enter in a max value for the attribute');
			return FALSE;
		}
		return TRUE;
	}



	private function _set_form_errors()
	{
		$this->view_data['isProjectSaveSuccess'] = FALSE;
		$this->view_data['isCreateUserSuccess'] = FALSE;
		$this->view_data['isDeleteUserSuccess'] = FALSE;
		$this->view_data['isDeleteUserAttrSuccess'] = FALSE;
		$this->view_data['isDeleteNodeAttrSuccess'] = FALSE;
		$this->view_data['isDeleteLinkAttrSuccess'] = FALSE;
		$this->view_data['isAddUserAttrSuccess'] = FALSE;
		$this->view_data['isAddNodeAttrSuccess'] = FALSE;
		$this->view_data['isAddLinkAttrSuccess'] = FALSE;
		$this->view_data['isCreateUserError'] = FALSE;
		$this->view_data['isDeleteUserError'] = FALSE;
		$this->view_data['isDeleteUserAttrError'] = FALSE;
		$this->view_data['isDeleteNodeAttrError'] = FALSE;
		$this->view_data['isDeleteLinkAttrError'] = FALSE;
		

		$this->form_validation->set_error_delimiters('<span class="error">Error: ', '</span>');
	}

	private function _set_index_data($pId)
	{
		//get project info
		$this->view_data['default'] = $this->Projects_model->get_project_info($pId);
		$this->view_data['new_user_emails'] = "";
		$this->view_data['users'] = $this->Projects_model->get_users_for_project($pId);
		$this->view_data['deleted_user'] = "";
		$this->view_data['new_user_attr'] = "";
		$this->view_data['user_atts_ar'] = $this->Projects_model->get_user_atts_ar($pId);
		$this->view_data['project_user_atts'] = $this->Projects_model->get_project_user_atts($pId);
		$this->view_data['node_atts_ar'] = $this->Projects_model->get_node_atts_ar($pId);
		$this->view_data['project_node_atts'] = $this->Projects_model->get_project_node_atts($pId);
		$this->view_data['link_atts_ar'] = $this->Projects_model->get_link_atts_ar($pId);
		$this->view_data['project_link_atts'] = $this->Projects_model->get_project_link_atts($pId);
		//get total number of nodes for this project
		$this->view_data['total_issues'] = $this->Projects_model->get_total_nodes($pId);
		
	}

	public function download_issues_csv($pId)
	{
		//$pId = 2;
		$this->db->select('issues.*, issueTags.name AS tagName');
		$this->db->where('projectId',$pId);
		$this->db->from('issues');
		$this->db->join('issuesIssueTags', 'issuesIssueTags.issueId = issues.id');
		$this->db->join('issueTags','issueTags.id = issuesIssueTags.tagId');
		$query = $this->db->get();

		//modify query to convert tags to columns
		$headers = $query->list_fields();
		$result_array = $query->result_array();
		$results = array();
		foreach ($result_array as $row) {
			//see if column exists as tag name
			$tN = $row['tagName'];
			if(in_array($tN,$headers) == FALSE && trim($tN) != "")
			{
				$headers[] = $tN;
			}
			$isNew = TRUE;
			foreach($results as &$row2)
			{
				if($row2['id'] == $row['id'])
				{
					$isNew = FALSE;
					if(trim($tN != ""))
					{
						$row2[$tN] = 1;	
					}

				}
			}
			if($isNew)
			{;
				unset($row['tagName']);
				$results[] = $row;	
			}
		}
		//fill in missing keys in rows
		foreach($results as &$row)
		{
			foreach($headers as $head)
			if(array_key_exists($head,$row) == FALSE && trim($head != ""))
			{
				$row[$head] = 0;
			}
		}


		//convert to csv
		$csv =& $this->csv_from_result($headers,$results);
		//$this->load->helper('file');
		//write_file(getcwd() . 'temp/issues.csv', $csv);
		$this->load->helper('download');
		force_download('issues.csv', $csv);
	}

	public function download_participants_csv()
	{
		//get all users and insert into participants if there
		$query = $this->db->get('users');
		foreach($query->result() as $row)
		{
			//get info from each user to possibly enter into participants
			$id = $row->user_id;
			//get number of links in mappr
			$this->db->where('userId',$id);
			$this->db->select('id');
			$query2 = $this->db->get('userLinks');
			$user_number_links = $query2->num_rows();
			$did_map = true;
			if($user_number_links == 0)
			{
				$did_map = false;
			}
			$data = array('links_mapped'=>$user_number_links,
										'did_map'=>$did_map,
										'user_last_login'=>$row->user_modified);

			$this->db->where('email',$row->user_email);
			$query2 = $this->db->get('possible_participants');
			if($query2->num_rows() == 0)
			{
				//insert
				$data['email'] = $row->user_email;
				$data['name'] = $row->first_name . " " . $row->last_name;
				$this->db->insert('possible_participants',$data);

			} else
			{
				//update
				$this->db->where('email',$row->user_email);
				$this->db->update('possible_participants',$data);

			}



		}
	}

	//custom copy from DB_util so I can modify for adding columns
	function csv_from_result($headers, $results, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		$out = '';

		// First generate the headings from the table column names
		foreach ($headers as $name)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
		}

		$out = rtrim($out);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($results as $row)
		{
			foreach ($row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}


}