<?php

class Issues_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------

	public function get_project_info()
	{
		$pId = $this->session->userdata('user_project_id');
		$this->db->where('id',$pId);
		$query = $this->db->get('projects');
		$rAR = $query->result_array();
		if(isset($rAR[0]))
		{
			return $rAR[0];	
		} else
		{
			return FALSE;
		}
	}

  public function get_user_notes()
  {
    $uId = $this->_get_user_id();
    $this->db->where('user_id',$uId);
    $query = $this->db->get('users');
    $notes = $query->row('notes');
    return $notes;
  }

	public function get_user_issues_list()
	{
		$this->db->where('userId',$this->_get_user_id());
		$this->db->where('projectId',$this->session->userdata('user_project_id'));
		$this->db->order_by('issueOrderNum','asc');
		$this->db->order_by('id','asc');
		$query = $this->db->get('issues');
		$issue_list = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$ar['id'] = $id;
			$ar['name'] = $row->name;
			$ar['description'] = $row->description;
			$ar['units'] = $row->units;
			$ar['issueOrderNum'] = $row->issueOrderNum;
			//get all tags for issue
			$this->db->where('issueId',$id);
			$query2 = $this->db->get('issuesIssueTags');
			$cats = '';
			$isFirst = TRUE;
			foreach($query2->result() as $row2)
			{
				$tagId = $row2->tagId;
				$this->db->where('id',$tagId);
				$this->db->select('name');
				$query = $this->db->get('issueTags');
				if($query->num_rows() == 1 && $query->row('name') !='')
				{
					if($isFirst == FALSE)
					{
						$cats .= ',';
					}
					$isFirst = FALSE;
					$cats .= $query->row('name');	
				}
			}
			$ar['categories'] = $cats;
			$issue_list[] = $ar;
		}
		return $issue_list;

		
	}


	public function save_issue($form_data,$cats)
	{
		$form_data['userId'] = $this->_get_user_id();
		$form_data['projectId'] = $this->session->userdata('user_project_id');
		//see if this issue is already in db
		$this->db->where('userId',$form_data['userId']);
		$this->db->where('projectId',$form_data['projectId']);
		$this->db->where('id',$form_data['id']);
		$query = $this->db->get('issues');
		if($query->num_rows() == 0 || $form_data['id'] == '')
		{
			$this->db->insert('issues',$form_data);
			$issueId = $this->db->insert_id();
		} else
		{
			$issueId = $query->row('id');
			$this->db->where('userId',$form_data['userId']);
			$this->db->where('projectId',$form_data['projectId']);
			$this->db->where('id',$form_data['id']);
			$this->db->update('issues',$form_data);
		}
	
		//first remove all cats for this id
		$this->db->where('issueId',$issueId);
		$this->db->delete('issuesIssueTags');

		//now deal with categories
		$catsAR = explode(',',$cats);
		for($i=0;$i<count($catsAR);$i++){
			$cat = $catsAR[$i];
			$cat = trim(strtolower($cat));
			//see if category exists in db
			$this->db->where('name',$cat);
			$query = $this->db->get('issueTags');
			if($query->num_rows() == 0)
			{
				//insert
				$data = array('name'=>$cat);
				$this->db->insert('issueTags',$data);
				$tagId = $this->db->insert_id();
			} else
			{
				//get id
				$tagId = $query->row('id');
			}


			//insert into linking table
			$this->db->where('tagId',$tagId);
			$this->db->where('issueId',$issueId);
			$query = $this->db->get('issuesIssueTags');

			if($query->num_rows() == 0)
			{
				$data = array('tagId'=>$tagId,
											'issueId'=>$issueId);
				$this->db->insert('issuesIssueTags',$data);
			}
			
		}
		return $issueId;
	}

	public function save_node($form_data,$cats)
	{
		$form_data['projectId'] = $this->session->userdata('user_project_id');
		$form_data['updateTime'] = date( 'Y-m-d H:i:s');
		//see if this issue is already in db
		$this->db->where('projectId',$form_data['projectId']);
		$this->db->where('id',$form_data['id']);
		$query = $this->db->get('nodes');
		if($query->num_rows() == 0 || $form_data['id'] == '')
		{
			$this->db->insert('nodes',$form_data);
			$issueId = $this->db->insert_id();
		} else
		{
			$issueId = $query->row('id');
			$this->db->where('projectId',$form_data['projectId']);
			$this->db->where('id',$issueId);
			$this->db->update('nodes',$form_data);
		}

		//first remove all cats for this id
		if(is_numeric($issueId) && $issueId != 0)
		{
			$this->db->where('nodeId',$issueId);
			$this->db->delete('nodesNodeTags');	
		}

		//now deal with categories
		$catsAR = explode(',',$cats);
		for($i=0;$i<count($catsAR);$i++){
			$cat = $catsAR[$i];
			$cat = trim(strtolower($cat));
			if($cat == '')
			{
				continue;
			}
			//see if category exists in db
			$this->db->where('name',$cat);
			$query = $this->db->get('nodeTags');
			if($query->num_rows() == 0)
			{
				//insert
				$data = array('name'=>$cat);
				$this->db->insert('nodeTags',$data);
				$tagId = $this->db->insert_id();
			} else
			{
				//get id
				$tagId = $query->row('id');
			}


			//insert into linking table
			$this->db->where('tagId',$tagId);
			$this->db->where('nodeId',$issueId);
			$query = $this->db->get('nodesNodeTags');

			if($query->num_rows() == 0)
			{
				$data = array('tagId'=>$tagId,
											'nodeId'=>$issueId);
				$this->db->insert('nodesNodeTags',$data);
			}
			
		}
		return $issueId;
	}

	private function _get_user_id($em = '')
	{
		if($em == '')
		{
			$em = $this->session->userdata('user_email');	
		}
		$this->db->where('user_email',$em);
		$this->db->select('user_id');
		$query = $this->db->get('users');
		$uId = $query->row('user_id');
		return $uId;
	}

	public function delete_issue($id)
	{
		$uId = $this->_get_user_id();
		$pId = $this->session->userdata('user_project_id');
		$this->db->where('userId',$uId);
		$this->db->where('projectid',$pId);
		$this->db->where('id',$id);
		$this->db->delete('issues');
		$this->db->where('issueId',$id);
		$this->db->delete('issuesIssueTags');
	}

	public function delete_node($id)
	{
		if(is_numeric($id) == false)
		{
			return;
		}
		$pId = $this->session->userdata('user_project_id');
		$this->db->where('projectid',$pId);
		$this->db->where('id',$id);
		$this->db->delete('nodes');
		$this->db->where('nodeId',$id);
		$this->db->delete('nodesNodeTags');
	}

	public function get_project_nodes()
	{
		$this->db->where('projectId',$this->session->userdata('user_project_id'));
		$this->db->order_by('id','asc');
		$query = $this->db->get('nodes');
		$issue_list = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$ar['name'] = $row->name;
			$ar['description'] = $row->description;
			$ar['units'] = $row->units;
			$ar['id'] = $id;
			$ar['votes'] = $row->votes;
			$ar['issueType'] = $row->issueType;
			$ar['updateTime'] = $row->updateTime;
			$ar['isRevisit'] = $row->isRevisit;
			$ar['notes'] = $row->notes;
			$ar['isGoal'] = $row->isGoal;
			$ar['issueInd'] = $row->issueInd;
			//get all tags for issue
			$this->db->where('nodeId',$id);
			$query2 = $this->db->get('nodesNodeTags');
			$cats = '';
			$isFirst = TRUE;
			foreach($query2->result() as $row2)
			{
				$tagId = $row2->tagId;
				$this->db->where('id',$tagId);
				$this->db->select('name');
				$query = $this->db->get('nodeTags');
				if($isFirst == FALSE)
				{
					$cats .= ',';
				}
				$isFirst = FALSE;
				$cats .= $query->row('name');
			}
			$ar['categories'] = $cats;
			$issue_list[] = $ar;
		}
		return $issue_list;		
	}

	public function get_project_node_tags()
	{
		$this->db->where('projectId',$this->session->userdata('user_project_id'));
		$this->db->order_by('id','asc');
		$this->db->select('id');
		$query = $this->db->get('nodes');
		$tagsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('nodeId',$id);
			$query2 = $this->db->get('nodesNodeTags');
			foreach($query2->result() as $row2)
			{
				$tId = $row2->tagId;
				$this->db->where('id',$tId);
				$query2 = $this->db->get('nodeTags');
				$nm = $query2->row('name');
				if($nm != '')
				{
					$tagsAR[] = $nm;	
				}
			}
		}
		sort($tagsAR);
		return array_unique($tagsAR);
		
	}

	public function get_project_issue_tags()
	{
		$this->db->where('projectId',$this->session->userdata('user_project_id'));
		$this->db->order_by('id','asc');
		$this->db->select('id');
		$query = $this->db->get('issues');
		$tagsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('issueId',$id);
			$query2 = $this->db->get('issuesIssueTags');
			foreach($query2->result() as $row2)
			{
				$tId = $row2->tagId;
				$this->db->where('id',$tId);
				$query2 = $this->db->get('issueTags');
				$nm = $query2->row('name');
				if($nm != '')
				{
					$tagsAR[$tId] = $nm;	
				}
			}
		}
		return array_unique($tagsAR);
		
	}

	//TODO
	//use join for faster
	public function get_issue_tags_like($inp)
	{
		$pId = $this->session->userdata('user_project_id');
		$this->db->where('projectId',$pId);
		$this->db->order_by('id','asc');
		$this->db->select('id');
		$query = $this->db->get('issues');
		$tagsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('issueId',$id);
			$query2 = $this->db->get('issuesIssueTags');
			foreach($query2->result() as $row2)
			{
				$tId = $row2->tagId;
				$this->db->where('id',$tId);
				//only difference in this function and above
				$this->db->like('name',$inp);
				$query2 = $this->db->get('issueTags');
				if($query2->num_rows() == 1)
				{
					$nm = $query2->row('name');
					if($nm != '')
					{
						$tagsAR[$tId] = $nm;	
					}	
				}
			}
		}

		$this->db->where('projectId',$pId);
		$query = $this->db->get('projectsIssueTags');
		foreach($query->result() as $row)
		{
			$tId = $row->tagId;
			$this->db->where('id',$tId);
			$this->db->like('name',$inp);
			$query2 = $this->db->get('issueTags');
			if($query2->num_rows() == 1)
			{
				$nm = $query2->row('name');
				if($nm != '')
				{
					$tagsAR[$tId] = $nm;	
				}	
			}

		}
		return array_unique($tagsAR);

	}

	public function get_node_tags_like($inp)
	{
		$this->db->where('projectId',$this->session->userdata('user_project_id'));
		$this->db->order_by('id','asc');
		$this->db->select('id');
		$query = $this->db->get('nodes');
		$tagsAR = array();
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$this->db->where('nodeId',$id);
			$query2 = $this->db->get('nodesNodeTags');
			foreach($query2->result() as $row2)
			{
				$tId = $row2->tagId;
				$this->db->where('id',$tId);
				//only difference in this function and above
				$this->db->like('name',$inp);
				$query2 = $this->db->get('nodeTags');
				if($query2->num_rows() == 1)
				{
					$nm = $query2->row('name');
					if($nm != '')
					{
						$tagsAR[$tId] = $nm;	
					}	
				}
			}
		}
		return array_unique($tagsAR);

	}



}
?>