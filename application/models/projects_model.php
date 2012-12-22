<?php

class Projects_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------


	/**
	 * Get project info by 
	 */
	public function get_project_info($pId)
	{
		$this->db->where('id',$pId);
		$query = $this->db->get('projects');
		$rAR = $query->result_array();
		//also get project states
		$query2 = $this->db->get('projectStates');
		$rAR[0]['projectStates'] = $query2->result_array();
		//also get tag seed
		$this->db->where('projectId',$pId);
		$query = $this->db->get('projectsIssueTags');
		$tgs = '';
		$isFirst = TRUE;
		foreach($query->result() as $row)
		{
			//get tag corresponding to row
			$this->db->where('id',$row->tagId);
			$query2 = $this->db->get('issueTags');
			if($isFirst == TRUE)
			{
				$isFirst = FALSE;
				$tgs .= $query2->row('name');
			} else
			{
				$tgs .= ', ' . $query2->row('name');
			}
		}
		$rAR[0]['tag_seed'] = $tgs;
		return $rAR[0];
	}


	/**
	 * update data for specific project
	 */
	public function update_project_data($form_data,$tag_seed,$cPID)
	{
		//get old project state
		$this->db->where('id',$cPID);
		$query = $this->db->get('projects');
		$curProjectState = $query->row('projectState');
		$this->db->where('id',$cPID);
		$this->db->update('projects',$form_data);

		//remove all project tags first
		$this->db->where('projectId',$cPID);
		$this->db->delete('projectsIssueTags');

		//update tags from tag seed
		$tag_seedAR = explode(',',$tag_seed);
		foreach($tag_seedAR as $tag)
		{
			$tag = strtolower(trim($tag));
			//see if tag already exists in issueTags
			$this->db->where('name',$tag);
			$query = $this->db->get('issueTags');
			if($query->num_rows() == 0)
			{
				//insert tag into db
				$data = array('name'=>$tag);
				$this->db->insert('issueTags',$data);
				$tagId = $this->db->insert_id();
			} else
			{
				$tagId = $query->row('id');
			}
			//insert tag and project ids into linking table
			$this->db->where('tagId',$tagId);
			$this->db->where('projectId',$cPID);
			$query = $this->db->get('projectsIssueTags');
			if($query->num_rows() == 0)
			{
				$data = array('tagId'=>$tagId,
											'projectId'=>$cPID);
				$this->db->insert('projectsIssueTags',$data);
			}
		}

		//if moving from issue listing to issue list curation, copy issues to nodes
		//see if already has issues from this project in nodes
		if($form_data['projectState'] == 2)
		{
				
			$this->db->where('projectId',$cPID);
			$query = $this->db->get('nodes');
			$issue_ind = 1;
			if($query->num_rows() == 0)
			{
				//copy all issues to nodes in this project
				$this->db->where('projectId',$cPID);
				$query = $this->db->get('issues');
				foreach($query->result() as $row)
				{
					$data = array('projectId'=>$row->projectId,
												'name'=>$row->name,
												'description'=>$row->description,
												'userId'=>$row->userId,
												'units'=>$row->units,
												'issueInd'=>$issue_ind);
					//increase issue index for next row
					$issue_ind++;
					$this->db->insert('nodes',$data);
					$nodeId = $this->db->insert_id();
					//also move all issue tags to node tags
					$this->db->where('issueId',$row->id);
					$query2 = $this->db->get('issuesIssueTags');
					foreach($query2->result() as $row)
					{
						$tagId = $row->tagId;
						$this->db->where('id',$tagId);
						$query3 = $this->db->get('issueTags');
						$tagName = $query3->row('name');
						//insert tag into nodes tag
						//check to see it doesn't already exist
						$this->db->where('name',$tagName);
						$query3 = $this->db->get('nodeTags');
						if($query3->num_rows() == 0)
						{
							//no tag names in nodeTags like this one
							//insert name into nodeTags
							$this->db->insert('nodeTags',array('name'=>$tagName));
							$tagId = $this->db->insert_id();
						} else
						{
							//get id of 
							$tagId = $query3->row('id');
						}
						//now insert tag and node id into nodesNodeTags
						$data = array('tagId'=>$tagId,
													'nodeId'=>$nodeId);
						$this->db->insert('nodesNodeTags',$data);
					}
				}
			}
		} else if($form_data['projectState'] == 3 && $curProjectState == 2)
		{
			$pId = $cPID;
			//delete any previous data in usersFromNodes table
			$this->db->where('projectId',$pId);
			$this->db->delete('usersFromNodes');
			
			//if moving to link mapping, then assign specific
			//issues to each participant using the linking table "userFromIssues"
			$issPerPart = $form_data['numberOfFromIssuesPerParticipant'];
			//get total issues for this project
			$issuesIds = $this->get_nodes_ids($cPID);
			$tot_issues = count($issuesIds);
			//get total participants
			$participants = $this->get_participants($cPID);
			$tot_parts = count($participants);
			//total issues going to be looked at
			$tot_overlap_iss = $issPerPart*$tot_parts;
			//get number of issues each part should do so each issue is dealt with
			$issDiv = floor($tot_issues/$tot_parts);
			$issRem = $tot_issues%$tot_parts;
			//array for remembering user issues
			$usersIssAR = array();
			//offset in case issues duplicated to user
			$offset = 0;
			//loop number of times that should create issue
			for($i=0;$i<$tot_overlap_iss;$i++)
			{
				//assign to a specific participant
				$uInd = ($i+$offset)%$tot_parts;
				//user to assign the node id to
				$userId = $participants[$uInd]['userId'];
				//get nodeId
				$nodeInd = $i%$tot_issues;
				$nodeId = $issuesIds[$nodeInd]['id'];
				if(isset($usersIssAR[$userId][$nodeId]))
				{
					//add to offset and recalculate user id
					$offset++;
					//assign to a specific participant
					$uInd = ($i+$offset)%$tot_parts;
					//user to assign the node id to
					$userId = $participants[$uInd]['userId'];
				}
				$usersIssAR[$userId][$nodeId] = 'done';
				//insert into usersFromNodes
				$data = array('userId'=>$userId,
											'nodeId'=>$nodeId,
											'projectId'=>$pId);
				$this->db->insert('usersFromNodes',$data);
			}

		}

		return TRUE;
	}

	public function get_nodes_ids($pId)
	{
		$this->db->where('projectId',$pId);
		$this->db->select('id');
		$query = $this->db->get('nodes');
		return $query->result_array();
	}

	public function get_participants($pId)
	{
		$this->db->where('projectId',$pId);
		$query = $this->db->get('usersProjects');
		$pAR = array();
		foreach($query->result() as $row)
		{
			$this->db->where('user_id',$row->userId);
			$query = $this->db->get('users');
			if($query->num_rows() == 1)
			{
				$pAR[]['userId'] = $row->userId;
			}
		}
		return $pAR;
	}

	/**
	 * Get all users for a specific project
	 */
	public function get_users_for_project($pId)
	{
		$this->db->where('projectId',$pId);
		$query = $this->db->get('usersProjects');
		$usersAR = array();
		foreach($query->result() as $row)
		{
			$this->db->where('user_id',$row->userId);
			$this->db->limit(1);
			$this->db->select('user_email');
			$query = $this->db->get('users');
			if($query->num_rows() == 1)
			{
				$uAR = array('user_id'=>$row->userId,
											'user_email'=>$query->row('user_email'));
				$usersAR[] = $uAR;
			}

		}
		return $usersAR;
		
	}


	public function get_user_email_by_id($id)
	{
		$this->db->where('user_id',$id);
		$this->db->select('user_email');
		$this->db->limit(1);
		$query = $this->db->get('users');
		return $query->row('user_email');
	}

	/**
	 * Create users for a project with an array of emails
	 */
	public function create_users_for_project($emAR,$pId)
	{
		//email setup
		$this->load->library('email');

		for($i=0;$i<count($emAR);$i++)
		{

			$em = trim(strtolower($emAR[$i]));
			//boolean if already an email
			$this->db->where('user_email',$em);
			$query = $this->db->get('users');
			//check to see if email already in db
			if($query->num_rows() >= 1)
			{
				$emId = $query->row('user_id');


				//see if already assigned to project and ignore
				$this->db->where('userId',$emId);
				$this->db->where('projectId',$pId);
				$query = $this->db->get('usersProjects');
				if($query->num_rows() >= 1)
				{
					//already assigned to project, so do nothing
					//get actual password field for use in string for link
					$this->db->where('user_email',$em);
					$this->db->select('user_id');
					$this->db->limit(1);
					$query = $this->db->get('users');
					//get id of email
					$emId = $query->row('user_id');


					//remove slashes so can use in url 
					//(will check against pass with slashes removed)
					$this->load->helper('string');
					$pass = random_string('alnum', 32);
					$enc_pass = str_replace(array('/','.'),'', $pass);
					$this->db->where('user_email',$em);
					$data = array('user_register_pass'=>$enc_pass);
					$this->db->update('users',$data);

					//insert project id and user id into linking table if not there
					$this->db->where('userId',$emId);
					$this->db->where('projectId',$pId);
					$query = $this->db->get('usersProjects');
					if($query->num_rows() == 0)
					{
						$data = array('userId'=>$emId,
													'projectId'=>$pId);
						$this->db->insert('usersProjects',$data);	
					}

					//get project name
					$this->db->where('id',$pId);
					$this->db->select('name,projectState');
					$query = $this->db->get('projects');
					$pName = $query->row('name');
					//get state of current project
					$pState = $query->row('projectState');
					$this->db->where('id',$pState);
					$query = $this->db->get('projectStates');
					$stateName = $query->row('name');

					//send email to user to register 
					$this->email->from('admin@vibrantdatalabs.org', 'Vibrant Data Administrator');
					$this->email->to($em);

					
					$this->email->subject("Vibrant Data Labs' Project: ".$pName);
					$this->email->message("You have been registered as a User for the " . $stateName . " phase of TruNorth's project:\n\n".$pName."\n\n" .
																	"Please login here with your current account email to begin: " . base_url() . "login");	

					$this->email->send();
				} else
				{
					//insert project id and user id into linking table
					$data = array('userId'=>$emId,
												'projectId'=>$pId);
					$this->db->insert('usersProjects',$data);

					//get project name
					$this->db->where('id',$pId);
					$this->db->select('name,projectState');
					$query = $this->db->get('projects');
					$pName = $query->row('name');
					//get state of current project
					$pState = $query->row('projectState');
					$this->db->where('id',$pState);
					$query = $this->db->get('projectStates');
					$stateName = $query->row('name');

					//send email to user letting know joined current project
					$this->email->from('admin@vibrantdatalabs.org', 'Vibrant Data Administrator');
					$this->email->to($em);

					$this->email->subject("TruNorth's Project: ".$pName);
					$this->email->message("You have been registered as a User for the " . $stateName . " phase of TruNorth's project:\n\n".$pName."\n\n" .
																	"Please login here with your current account email to begin: " . base_url() . "login");	

					$this->email->send();


				}
			} else
			{
				//user not in db yet
				//create user with simple login secure
				//create random password
				$this->load->helper('string');
				$pass = random_string('alnum', 8);
				$this->simpleloginsecure->create($em, $pass, FALSE);
				//update user data
				$data = array('isAdministrator'=>0);
				$this->db->where('user_email',$em);
				$this->db->update('users',$data);

				//get actual password field for use in string for link
				$this->db->where('user_email',$em);
				$this->db->select('user_id,user_pass');
				$this->db->limit(1);
				$query = $this->db->get('users');
				//get id of email
				$emId = $query->row('user_id');

				//remove slashes so can use in url 
				//(will check against pass with slashes removed)
				$enc_pass = str_replace(array('/','.'),'', $query->row('user_pass'));
				$this->db->where('user_email',$em);
				$data = array('user_pass'=>'',
											'user_register_pass'=>$enc_pass);
				$this->db->update('users',$data);

				//insert project id and user id into linking table
				$data = array('userId'=>$emId,
											'projectId'=>$pId);
				$this->db->insert('usersProjects',$data);

				//get project name
				$this->db->where('id',$pId);
				$this->db->select('name,projectState');
				$query = $this->db->get('projects');
				$pName = $query->row('name');
				//get state of current project
				$pState = $query->row('projectState');
				$this->db->where('id',$pState);
				$query = $this->db->get('projectStates');
				$stateName = $query->row('name');
				//send email to user to register 
				$this->email->from('admin@vibrantdatalabs.org', 'Vibrant Data Administrator');
				$this->email->to($em);

				$this->email->subject('Vibrant Data User Registration for: '.$pName);
				$this->email->message("You have been registered as a User for the " . $stateName . " phase of TruNorth's project:\n\n".$pName."\n\n" .
															"To complete your registration, click the link below: " . base_url() . "login/register/" . urlencode($em) . "/" . urlencode($enc_pass) . '/' . $pId . "\n\n" . 
															"To login again once you have registered, please visit the following url: " . base_url());	

				$this->email->send();
				//echo base_url() . "login/register/" . urlencode($em) . "/" . urlencode($enc_pass) . '/' . $pId;
				
			}

		}
		return TRUE;
	}


	public function remove_user_from_project($uId,$pId)
	{
		$this->db->where(array('userId'=>$uId,
											'projectId'=>$pId));
		$this->db->limit(1);
		$this->db->delete('usersProjects');
		$this->db->where('userId',$uId);
		$query = $this->db->get('usersProjects');
		//see if memeber of other projects
		if($query->num_rows() > 0)
		{
			//don't delete user because member of other projects
		} else
		{
			//find and delete user if not admin
			$this->db->where(array('user_id'=>$uId,
														'isAdministrator'=>0));
			$this->db->delete('users');
		}
	}


	//
	//
	///////GETS FOR CREATING ATTRIBUTES////////

	public function get_user_atts_ar($pId)
	{
		$query = $this->db->get('usersAttributes');
		$attsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('userAttrId',$id);
			$this->db->where('projectId',$pId);
			$query2 = $this->db->get('usersProjectsAttributes');
			if($query2->num_rows() == 0)
			{
				$attsAR[] = $row;
			}
		}
		return $attsAR;
	}

	//user atts for a specific project
	public function get_project_user_atts($pId)
	{
		//get list of attributes from table that 
		//are used in this project
		$this->db->where('projectId',$pId);
		$query = $this->db->get('usersProjectsAttributes');
		$pUAAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->userAttrId;
			$this->db->where('id',$id);
			$query2 = $this->db->get('usersAttributes');
			$pUAAR[] = $query2->row();
		}
		return $pUAAR;
	}

	public function get_node_atts_ar($pId)
	{
		$query = $this->db->get('nodesAttributes');
		$attsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('nodeAttrId',$id);
			$this->db->where('projectId',$pId);
			$query2 = $this->db->get('nodesProjectsAttributes');
			if($query2->num_rows() == 0)
			{
				$attsAR[] = $query->row();
			}
		}
		return $attsAR;
	}

	//node atts for specific project
	public function get_project_node_atts($pId)
	{
		//get list of attributes from table that 
		//are used in this project
		$this->db->where('projectId',$pId);
		$query = $this->db->get('nodesProjectsAttributes');
		$pUAAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->nodeAttrId;
			$this->db->where('id',$id);
			$query2 = $this->db->get('nodesAttributes');
			$pUAAR[] = $query2->row();
		}
		return $pUAAR;
	}

	public function get_link_atts_ar($pId)
	{
		$query = $this->db->get('linksAttributes');
		$attsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('linkAttrId',$id);
			$this->db->where('projectId',$pId);
			$query2 = $this->db->get('linksProjectsAttributes');
			if($query2->num_rows() == 0)
			{
				$attsAR[] = $query->row();
			}
		}
		return $attsAR;
	}

	//link atts for specific project
	public function get_project_link_atts($pId)
	{
		//get list of attributes from table that 
		//are used in this project
		$this->db->where('projectId',$pId);
		$query = $this->db->get('linksProjectsAttributes');
		$pUAAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->linkAttrId;
			$this->db->where('id',$id);
			$query2 = $this->db->get('linksAttributes');
			$pUAAR[] = $query2->row();
		}
		return $pUAAR;
	}

	public function get_user_attr_by_id($atId)
	{
		$this->db->where('id',$atId);
		$this->db->select('name');
		$query = $this->db->get('usersAttributes');
		return $query->row('name');
	}

	public function get_node_attr_by_id($atId)
	{
		$this->db->where('id',$atId);
		$this->db->select('name');
		$query = $this->db->get('nodesAttributes');
		return $query->row('name');
	}

	public function get_link_attr_by_id($atId)
	{
		$this->db->where('id',$atId);
		$this->db->select('name');
		$query = $this->db->get('linksAttributes');
		return $query->row('name');
	}

	//
	//
	//SETS FOR CREATING/DELETING ATTRIBUTES
	

	public function assign_prev_user_attr_to_project($oldAt,$pId)
	{
		//check to make sure not already in db
		$data = array('projectId'=>$pId,
									'userAttrId'=>$oldAt);
		$query = $this->db->get_where('usersProjectsAttributes',$data);
		if($query->num_rows() == 0)
		{
			$this->db->insert('usersProjectsAttributes',$data);	
		}
		return TRUE;
	}

	public function assign_new_user_attr_to_project($pId,$form_data)
	{
		//first insert new attribute to db
		$this->db->insert('usersAttributes',$form_data);

		//then assign this id to usersProjectsAttributes
		$atId = $this->db->insert_id();
		$data = array('projectId'=>$pId,
		              'userAttrId'=>$atId);
		$this->db->insert('usersProjectsAttributes',$data);	
		return TRUE;
	}

	public function remove_user_attr_from_project($atId,$pId)
	{
		$this->db->where('userAttrId',$atId);
		$this->db->where('projectId',$pId);
		$this->db->delete('usersProjectsAttributes');

		$this->db->where('userAttrId',$atId);
		$query = $this->db->get('usersProjectsAttributes');
		if($query->num_rows() == 0)
		{
			//TODO:
			//remove this section once enable foreign keys
			//also remove attribute
			$this->db->where('id',$atId);
			$this->db->delete('usersAttributes');
		}
		return TRUE;
	}


	public function assign_prev_node_attr_to_project($oldAt,$pId)
	{
		//check to make sure not already in db
		$data = array('projectId'=>$pId,
									'nodeAttrId'=>$oldAt);
		$query = $this->db->get_where('nodesProjectsAttributes',$data);
		if($query->num_rows() == 0)
		{
			$this->db->insert('nodesProjectsAttributes',$data);	
		}
		return TRUE;
	}

	public function assign_new_node_attr_to_project($pId,$form_data)
	{
		//first insert new attribute to db
		$this->db->insert('nodesAttributes',$form_data);

		//then assign this id to usersProjectsAttributes
		$atId = $this->db->insert_id();
		$data = array('projectId'=>$pId,
		              'nodeAttrId'=>$atId);
		$this->db->insert('nodesProjectsAttributes',$data);	
		return TRUE;
	}

	public function remove_node_attr_from_project($atId,$pId)
	{
		$this->db->where('nodeAttrId',$atId);
		$this->db->where('projectId',$pId);
		$this->db->delete('nodesProjectsAttributes');

		$this->db->where('nodeAttrId',$atId);
		$query = $this->db->get('nodesProjectsAttributes');
		if($query->num_rows() == 0)
		{
			//TODO:
			//remove this section once enable foreign keys
			//also remove attribute
			$this->db->where('id',$atId);
			$this->db->delete('nodesAttributes');
		}
		return TRUE;
	}


	public function assign_prev_link_attr_to_project($oldAt,$pId)
	{
		//check to make sure not already in db
		$data = array('projectId'=>$pId,
									'linkAttrId'=>$oldAt);
		$query = $this->db->get_where('linksProjectsAttributes',$data);
		if($query->num_rows() == 0)
		{
			$this->db->insert('linksProjectsAttributes',$data);	
		}
		return TRUE;
	}

	public function assign_new_link_attr_to_project($pId,$form_data)
	{
		//first insert new attribute to db
		$this->db->insert('linksAttributes',$form_data);

		//then assign this id to usersProjectsAttributes
		$atId = $this->db->insert_id();
		$data = array('projectId'=>$pId,
		              'linkAttrId'=>$atId);
		$this->db->insert('linksProjectsAttributes',$data);	
		return TRUE;
	}

	public function remove_link_attr_from_project($atId,$pId)
	{
		$this->db->where('linkAttrId',$atId);
		$this->db->where('projectId',$pId);
		$this->db->delete('linksProjectsAttributes');

		$this->db->where('linkAttrId',$atId);
		$query = $this->db->get('linksProjectsAttributes');
		if($query->num_rows() == 0)
		{
			//TODO:
			//remove this section once enable foreign keys
			//also remove attribute
			$this->db->where('id',$atId);
			$this->db->delete('linksAttributes');
		}
		return TRUE;
	}

	public function get_total_nodes($pId)
	{
		$this->db->where('projectId',$pId);
		$query = $this->db->get('nodes');
		return $query->num_rows();
	}



}
?>