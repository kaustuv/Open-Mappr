<?php

ini_set("auto_detect_line_endings", true);
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
		$this->form_validation->set_rules('admin_email', 'Admin Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('description', 'Project Description', 'trim|xss_clean');
		$this->form_validation->set_rules('registration_message', 'Registration Message', 'trim|xss_clean');
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
				$this->view_data['form_errors'] = 'There were errors. Please scroll down and fix them before saving again.';
				$this->view_data['default']['name'] = set_value('name');
				$this->view_data['default']['admin_email'] = set_value('admin_email');
				$this->view_data['default']['description'] = set_value('description');
				$this->view_data['default']['registration_message'] = set_value('registration_message');
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
			$registration_message = htmlspecialchars_decode(set_value('registration_message'),ENT_QUOTES);
			$link_info = htmlspecialchars_decode(set_value('link_mapping_info'),ENT_QUOTES);
			$url = url_title(set_value('name'));
			$form_data = array(
						'name' => set_value('name'),
						'admin_email' => set_value('admin_email'),
						'url' => $url,
						'description' => $desc,
						'registration_message' => $registration_message,
						'question' => $quest,
						'numberOfIssuesPerParticipant' => set_value('numberOfIssuesPerParticipant'),
						'numberOfFromIssuesPerParticipant' => set_value('numberOfFromIssuesPerParticipant'),
						'projectState' => set_value('projectState'),
						'video_embed' => html_entity_decode($this->input->post('video_embed')),
						'video_embed_link_mapping' => html_entity_decode($this->input->post('video_embed_link_mapping')),
						'link_mapping_info' => $link_info
						);
			$tag_seed = htmlspecialchars_decode(set_value('tag_seed'),ENT_QUOTES);

			$this->view_data['form_errors'] = 'Project Successfully Saved!';
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



	private function _set_form_errors()
	{
		$this->view_data['isProjectSaveSuccess'] = FALSE;
		$this->view_data['isCreateUserSuccess'] = FALSE;
		$this->view_data['isDeleteUserSuccess'] = FALSE;
		$this->view_data['isCreateUserError'] = FALSE;
		$this->view_data['isDeleteUserError'] = FALSE;
		

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
		$headers = $this->db->list_fields('issues');
		
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
			$ind = 0;
			foreach($results as &$row2)
			{
				if($row2['id'] == $row['id'])
				{
					$isNew = FALSE;
					if(trim($tN != ""))
					{

						$results[$ind][$tN] = 1;	
					}

				}
				$ind++;
			}
			if($isNew)
			{
				unset($row['tagName']);
				$row[$tN] = 1;
				$results[] = $row;	
			}
		}
		//fill in missing keys in rows
		$ind = 0;
		foreach($results as &$row)
		{
			foreach($headers as $head)
			{
				if(array_key_exists($head,$row) == FALSE && trim($head != ""))
				{
					$results[$ind][$head] = 0;
				}	
			}
			$ind++;
		}


		//convert to csv
		$csv =& $this->csv_from_result($headers,$results);
		$this->load->helper('download');
		force_download('submitted_nodes.csv', $csv);
	}


	public function download_nodes_csv($pId)
	{
		//$pId = 2;
		$this->db->select('nodes.*, nodeTags.name AS tagName');
		$this->db->where('projectId',$pId);
		$this->db->from('nodes');
		$this->db->join('nodesNodeTags', 'nodesNodeTags.nodeId = nodes.id');
		$this->db->join('nodeTags','nodeTags.id = nodesNodeTags.tagId');
		$query = $this->db->get();

		//modify query to convert tags to columns
		$headers = $this->db->list_fields('nodes');

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
			$ind = 0;
			foreach($results as &$row2)
			{
				if($row2['id'] == $row['id'])
				{
					$isNew = FALSE;
					if(trim($tN != ""))
					{

						$results[$ind][$tN] = 1;	
					}

				}
				$ind++;
			}
			if($isNew)
			{
				unset($row['tagName']);
				$row[$tN] = 1;
				$results[] = $row;	
			}
		}
		//fill in missing keys in rows
		$ind = 0;
		foreach($results as &$row)
		{
			foreach($headers as $head)
			{
				if(array_key_exists($head,$row) == FALSE && trim($head != ""))
				{
					$results[$ind][$head] = 0;
				}	
			}
			$ind++;
		}

		//convert to csv
		$csv =& $this->csv_from_result($headers,$results);
		$this->load->helper('download');
		force_download('curated_nodes.csv', $csv);
	}

	public function upload_nodes_csv($pId)
	{
		$config['upload_path'] = 'temp/';
    $config['allowed_types'] = '*';
    $config['max_size'] = '5000';
    $config['file_name'] = 'upload' . time();

    $this->load->library('upload', $config);

    if(!$this->upload->do_upload()) 
    {

			$this->view_data['upload_error'] = $this->upload->display_errors('<p class="error">','</p>');	
			$this->edit($pId);

    } else {
      $file_info = $this->upload->data();
      $csvfilepath = "temp/" . $file_info['file_name'];
      //add to db
      $row = 1;
			if (($handle = fopen($csvfilepath, "r")) !== FALSE) {
				//delete all linked tags to nodes
				$this->db->where('projectId',$pId);
				$query = $this->db->get('nodes');
				foreach ($query->result() as $node) {
					$this->db->where('nodeId',$node->id);
					$this->db->delete('nodesNodeTags');
				}
				//delete all nodes in this project
				$this->db->where('projectId',$pId);
				$this->db->delete('nodes');
				$fields = array();
				$tags = array();
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        $num = count($data);
	        //set up first row as fields
	        if($row == 1)
	        {
	        	//fields
		        for ($c=0; $c < 12; $c++) {
	            $fields[] = $data[$c];
		        }
		        //tags
		        for ($c=12; $c < $num; $c++) {
		        	$d = $data[$c];
		        	if($d != '')
		        	{
		            $tags[] = $d;
		        	}
		        }
	        } else
	        {
	        	$tag_ar = array();
	        	for($c=0;$c<$num;$c++)
	        	{
	        		//don't insert id into data so won't try to add blank id
	        		if($c < 12 && $fields[$c] != 'id')
	        		{
		        		$node_data[$fields[$c]] = $data[$c];
	        		} else if($data[$c] == 1)
	        		{
	        			//add tag of this node to tag db if doesn't exist yet
	        			$tag_ar[] = trim($tags[$c-12]);

	        		}
	        	}

	        	//insert or update rows
	        	$this->db->insert('nodes',$node_data);
      			$node_id = $this->db->insert_id();
	        	//add tags for this insert
	        	for($i=0;$i<count($tag_ar);$i++)
	        	{
		        	//first look for tags
		        	$this->db->limit(1);
	        		$this->db->where('name',$tag_ar[$i]);
	        		$query = $this->db->get('nodeTags');
	        		if($query->num_rows() == 1)
	        		{
	        			$tag_id = $query->row('id');
	        		
	        		} else
	        		{
	        			$this->db->insert('nodeTags',array('name'=>$tag_ar[$i]));
	        			$tag_id = $this->db->insert_id();

	        		}
        			//link node to tag
      				$nodesNode_data = array('tagId'=>$tag_id,
      																'nodeId'=>$node_id);
      				$this->db->insert('nodesNodeTags',$nodesNode_data);
	        	}

	        }
	        $row++;
		    }
		    fclose($handle);
		    $this->view_data['upload_error'] = "<p class='error'>Successfully uploaded CSV. Change the &ldquo;Current Project State&rdquo; to &ldquo;Link Mapping&rdquo;.</p>";

			} else
			{
				//error opening csv
				$this->view_data['upload_error'] = '<p class="error">Error opening CSV file. Please contact the administrator.</p>';	
			}
			//delete csv file
	    unlink($csvfilepath);

			$this->edit($pId);
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
			//sort row according to headers
			$new_row = array();
			foreach($headers as $name)
			{
				$new_row[$name] = $row[$name];
			}
			foreach ($new_row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}


}