<?php

class Links_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------

  public function get_project_name()
  {
  	$pId = $this->session->userdata('user_project_id');
  	$this->db->where('id',$pId);
  	$this->db->select('name');
  	$query = $this->db->get('projects');
    if($query->num_rows() == 0)
    {
      redirect('');
    }
  	return $query->row('name');
  }

  public function get_link_mapping_instructions() 
  {
    $pId = $this->session->userdata('user_project_id');
    $this->db->where('id',$pId);
    $this->db->select('link_mapping_info');
    $query = $this->db->get('projects');
    return $query->row('link_mapping_info'); 
  }

  public function get_video_instructions()
  {
    $pId = $this->session->userdata('user_project_id');
    $this->db->where('id',$pId);
    $this->db->select('video_embed_link_mapping');
    $query = $this->db->get('projects');
    return $query->row('video_embed_link_mapping');  
  }

  public function send_issue_to_email($tit,$desc,$units,$tags)
  {
    $this->load->library('email');
    $config['protocol'] = 'sendmail';
    $config['charset'] = 'iso-8859-1';
    $config['crif'] = '\r\n';
    $config['wordwrap'] = TRUE;

    $this->email->initialize($config);

    $this->email->from('admin@vibrantdatalabs.org', 'Vibrant Data Administrator');
    $this->email->to('eric@vibrantdatalabs.org');

    $this->email->subject('Mappr Issue Submission from Link Mapping');

    if(trim($this->session->userdata('first_name')) == '')
    {
      $usr = $this->session->userdata('user_email');
    } else
    {
      $usr = $this->session->userdata('first_name') . " " . $this->session->userdata('last_name');
    }
    $this->email->message("User: " . $usr . " has added the following issue while Link Mapping.\n\n" .
                          "Title: " . $tit . "\n" .
                          "Description: " . $desc . "\n" .
                          "Units: " . $units . "\n" .
                          "Tags: " . $tags);  

    $this->email->send();

    return 'true';
  }

  /*public function get_user_from_nodes()
  {
  	$pId = $this->session->userdata('user_project_id');
  	$uId = $this->_get_user_id();
  	$this->db->where('projectId',$pId);
  	$this->db->where('userId',$uId);
  	$query = $this->db->get('usersFromNodes');
  	$returnAR = array();
  	foreach($query->result() as $row)
  	{
  		$nId = $row->nodeId;
  		//now get node info for this id
  		$this->db->where('id',$nId);
  		$query2 = $this->db->get('nodes');
  		foreach($query2->result_array() as $row2)
  		{
  			$returnAR[] = $row2;
  		}
  	}
  	return $returnAR;
  }*/


  public function get_user_from_nodes()
  {
    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    $data = array('projectId'=>$pId,
                  'userId'=>$uId);
    $this->db->select('nodeId');
    $query = $this->db->get_where('usersChosenFromNodes',$data);
    $retAR = array();
    foreach($query->result() as $row)
    {
      $retAR[] = $row->nodeId;
    }
    return $retAR;
  }


  public function set_user_from_nodes($post)
  {
    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    //get all issue nodes from 
    $data = array('projectId'=>$pId,
                    'userId'=>$uId);
    $query = $this->db->get_where('usersChosenFromNodes',$data);
    foreach($query->result() as $row)
    {
      if(in_array($row->nodeId, $post) == FALSE)
      {
        //remove row
        $this->db->where('id',$row->id);
        $this->db->delete('usersChosenFromNodes');
        //TODO:
        //remove links with any removed nodes (maybe via foreign keys)
      }
    }

    foreach($post as $fromId)
    {
      $data = array('projectId'=>$pId,
                    'userId'=>$uId,
                    'nodeId'=>$fromId);
      $query = $this->db->get_where('usersChosenFromNodes',$data);
      if($query->num_rows() == 0)
      {
        
        $this->db->insert('usersChosenFromNodes',$data);
      }
    }
    return 'true';
  }

  /*public function get_from_nodes($subset_tags)
  {

    $pId = $this->session->userdata('user_project_id');
    $this->db->select('nodes.*, nodeTags.name AS tagName');
    $this->db->where('projectId',$pId);
    $this->db->from('nodes');
    $this->db->join('nodesNodeTags', 'nodesNodeTags.nodeId = nodes.id');
    $this->db->join('nodeTags','nodeTags.id = nodesNodeTags.tagId');
    $query = $this->db->get();
    print_r($query->result_array());

  }*/

  public function get_nodes_by_tags($tags)
  {
    $tagsAR = array();
    foreach($tags as $tag)
    {
      $tagsAR[$tag] = array();
      //get id of tag
      $this->db->where('name',$tag);
      $this->db->select('id');
      $query = $this->db->get('nodeTags');
      foreach ($query->result() as $row) {
        //get node id for each tag
        $this->db->where('tagId',$row->id);
        $query2 = $this->db->get('nodesNodeTags');
        foreach($query2->result() as $row2)
        {
          //get node name from id
          $this->db->where('id',$row2->nodeId);
          $this->db->select('name');
          $query3 = $this->db->get('nodes');
          $tagsAR[$tag][] = $query3->row('name');
        }
      }
    }
    return $tagsAR;
  }

  public function get_from_nodes($subset_tags)
  {
    $pId = $this->session->userdata('user_project_id');
    $this->db->where('nodes.projectId',$pId);
    $this->db->from('usersChosenFromNodes');
    $this->db->join('nodes', 'nodes.id = usersChosenFromNodes.nodeId','right');
    $this->db->group_by('nodes.id');
    $this->db->order_by('COUNT(usersChosenFromNodes.nodeId)','asc');

    $query = $this->db->get();

    //$pId = $this->session->userdata('user_project_id');
    //$this->db->where('projectId',$pId);
    //$query = $this->db->get('nodes');
    $retAR = array();
    //also add on tags if they are part of subset tags
    foreach($query->result_array() as $row)
    {
      //setup array for tags
      $row['subset_tags'] = array();
      //see if has any tags

      //get tags matching this row id
      $this->db->where('nodeId',$row['id']);
      $this->db->select('tagId');
      $query2 = $this->db->get('nodesNodeTags');
      foreach($query2->result() as $row2)
      {
        //get tag for this id
        $this->db->where('id',$row2->tagId);
        $this->db->select('name');
        $this->db->limit(1);
        $query3 = $this->db->get('nodeTags');
        $sub_tag = strtolower($query3->row('name'));
        if(in_array($sub_tag,$subset_tags))
        {
          $row['subset_tags'][] = strtolower($sub_tag);
        }

      }
      //get whether checked this one from user nodes
      $pId = $this->session->userdata('user_project_id');
      $uId = $this->_get_user_id();
      $data = array('userId'=>$uId,
                    'projectId'=>$pId,
                    'nodeId'=>$row['id']);
      $this->db->where($data);
      $this->db->select('isDone,isBegun');
      $query2 = $this->db->get('usersChosenFromNodes');
      $row['isDone'] = $query2->row('isDone');
      $row['isBegun'] = $query2->row('isBegun');
      $retAR[] = $row;
    }
    return $retAR;
  }

  public function organize_subset_tags($subset_tags,$from_nodes)
  {
    $retAR = array();
    foreach($subset_tags as $tag)
    {
      $i = 0;
      foreach($from_nodes as $row)
      {
        if(in_array(strtolower($tag), $row['subset_tags']))
        {
          $i++;
        }
      }
      $retAR[$tag] = $i;
    }
    return $retAR;
  }

  public function get_total_froms()
  {
    $pId = $this->session->userdata('user_project_id');
    $this->db->where('id',$pId);
    $this->db->select('numberOfFromIssuesPerParticipant');
    $query = $this->db->get('projects');
    return $query->row('numberOfFromIssuesPerParticipant');
  }

  public function get_to_nodes()
  {
  	$pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
  	$this->db->where('projectId',$pId);
  	$this->db->order_by('id','asc');
    $query = $this->db->get('nodes');
  	$issue_list = array();
    foreach($query->result() as $row)
    {
      $id = $row->id;
      //see if this node is a removed to node
      $data = array('userId'=>$uId,
                    'projectId'=>$pId,
                    'nodeId'=>$id);
      $query3 = $this->db->get_where('userDeletedToNodes',$data);
      $ar['isDeleted'] = FALSE;
      if($query3->num_rows() != 0)
      {
        $ar['isDeleted'] = TRUE;
      }
      $ar['name'] = $row->name;
      $ar['description'] = $row->description;
      $ar['units'] = $row->units;
      $ar['id'] = $id;
      $ar['votes'] = $row->votes;
      $ar['issueType'] = $row->issueType;
      $ar['updateTime'] = $row->updateTime;
      $ar['isRevisit'] = $row->isRevisit;
      $ar['notes'] = $row->notes;
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

  public function remove_to_node($to_id)
  {

    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    $data = array('projectId'=>$pId,
                  'userId'=>$uId,
                  'nodeId'=>$to_id);
    $query = $this->db->get_where('userDeletedToNodes',$data);
    if($query->num_rows() == 0)
    {
      //insert to node id into 
      $this->db->insert('userDeletedToNodes',$data);
    }
    return TRUE;
  }

  public function get_user_links()
  {
  	$pId = $this->session->userdata('user_project_id');
  	$uId = $this->_get_user_id();
  	$this->db->where('projectId',$pId);
  	$this->db->where('userId',$uId);
  	$this->db->order_by('issueFromId','asc');
  	$this->db->order_by('issueToId','asc');
  	$query = $this->db->get('userLinks');
  	$returnAR = array();
  	foreach($query->result() as $row)
  	{
  		$fId = $row->issueFromId;
  		$tId = $row->issueToId;
  		$returnAR[$fId][$tId] = $row;
  	}
  	return $returnAR;
  	
  }

  public function set_is_begun($dir,$fId,$tId)
  {
    if($dir == 'incoming')
    {
      //set to id as from id
      $fId = $tId;
    }
    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    $this->db->where('projectId',$pId);

    $this->db->where('userId',$uId);
    $this->db->where('nodeId',$fId);
    $this->db->update('usersChosenFromNodes',array('isBegun'=>TRUE));
  }

  public function create_user_link($fId,$tId)
  {
  	$uId = $this->_get_user_id();
  	$pId = $this->session->userdata('user_project_id');
  	//make sure link not already created
  	$this->db->where('userId',$uId);
  	$this->db->where('projectId',$pId);
  	$this->db->where('issueFromId',$fId);
  	$this->db->where('issueToId',$tId);
    $query = $this->db->get('userLinks');
    //now create link
    $data = array('userId'=>$uId,
                  'projectId'=>$pId,
                  'issueFromId'=>$fId,
                  'issueToId'=>$tId);
    if($query->num_rows() == 1)
    {
      $id = $query->row('id');
      $this->db->where('id',$id);
      $this->db->update('userLinks',$data);
    } else 
    {
      $this->db->insert('userLinks',$data); 
    }
  	return $this->db->insert_id();
  }

  public function delete_user_link($lId,$lId2)
  {
    if($lId == "")
    {
      return;
    }
  	//make sure link not already created
    $this->db->where('id',$lId);
  	$this->db->delete('userLinks');
    if($lId2 != "")
    {
      $this->db->where('id',$lId2);
      $this->db->delete('userLinks');
    }
  	return TRUE;
  }

  public function save_user_link($lId,$data)
  {
    $this->db->where('id',$lId);
    $this->db->update('userLinks',$data);
    return TRUE;
  }

  public function save_comment($lId,$comment)
  {
    $data = array('comment'=>$comment);
    $this->db->where('id',$lId);
    $this->db->update('userLinks',$data);
    return TRUE;

  }

  public function save_done_from($from_id,$is_checked)
  {

    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    //get all issue nodes from 
    $data = array('projectId'=>$pId,
                    'userId'=>$uId,
                    'nodeId'=>$from_id);
    $this->db->where($data);
    $isD = 0;
    if($is_checked == 'true')
    {
      $isD = 1;
    }
    $data = array('isDone'=>$isD);
    $this->db->update('usersChosenFromNodes',$data);
    return TRUE;

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
        if($nm != '' && $query2->num_rows() == 1)
        {
          $tagsAR[] = strtolower($nm);  
        }
      }
    }
    $nAR = array_unique($tagsAR);
    sort($nAR);
    return $nAR;
    
  }


  /**
   * USER NOTES
   */

  public function save_user_notes($uN)
  {
    $uId = $this->_get_user_id();
    $this->db->where('user_id',$uId);
    $data = array('notes'=>$uN);
    $this->db->update('users',$data);
    return TRUE;
  }

  public function get_user_notes()
  {
    $uId = $this->_get_user_id();
    $this->db->where('user_id',$uId);
    $query = $this->db->get('users');
    $notes = $query->row('notes');
    return $notes;
  }

  public function save_user_feedback($fb,$url,$ua)
  {
    $em = $this->session->userdata('user_email');

    //email setup
    $this->load->library('email');

    //send email to user letting know joined current project
    $this->email->from('admin@vibrantdatalabs.org', 'Vibrant Data Administrator');
    $this->email->to('sundev@brainvise.com');

    $this->email->subject("User Feedback");
    $this->email->message("Feedback from User:\n\n".$em."\n\n" .
                            "At URL: " . $url . "\n\n" . 
                            "Useragent: " . $ua . "\n\n" . 
                            "Feedback: \n" . $fb); 

    $this->email->send();
    
  }

  public function reduce_tags()
  {
    $query = $this->db->get('nodes');
    $tagsAR = array();
    foreach($query->result() as $row)
    {
      $nId = $row->id;
      $this->db->where('nodeId',$nId);
      $query2 = $this->db->get('nodesNodeTags');
      foreach($query2->result() as $row)
      {
        $tId = $row->tagId;
        $this->db->where('id',$tId);
        $query3 = $this->db->get('nodeTags');
        $tagsAR[] = strtolower($query3->row('name'));

      }
    }
    $result = array_unique($tagsAR);
    sort($result);
    echo implode('<br/>', $result);
  }

  public function set_up_links()
  {
    $query = $this->db->get('usersFromNodes');
    foreach ($query->result() as $row) {
      //see if link is defined in db
      $this->db->where('issueFromId',$row->nodeId);
      //first go through all the links and if there is a link, then see if more than
      //one user was assigned to this issue, if so, then set tot issues to 2
      $query2 = $this->db->get('userLinks');
      foreach ($query2->result() as $row2) {
        //see if more than one user assigned from link
        $this->db->where('nodeId',$row2->issueFromId);
        $query3 = $this->db->get('usersFromNodes');
        //total number of possible links to this issue
        $totObs = $query3->num_rows();

        //now go through and get number of actual observers
        $this->db->where('issueFromId',$row->nodeId);
        $this->db->where('issueToId',$row2->issueToId);
        $query3 = $this->db->get('userLinks');
        $numObs = $query3->num_rows();
        //insert num observers and total observers
        $data = array('num_observers'=>$numObs,
                      'tot_observers'=>$totObs);
        $this->db->where('id',$row2->id);
        $this->db->update('userLinks',$data);
      }

      //now get the mean of double links
      $this->db->where('num_observers',1);
      $query = $this->db->get('userLinks');
      foreach($query->result() as $row)
      {
        //set max min and med to same
        $str = $row->strength;
        $sig = $row->sign;
        $cer = $row->certainty;

        $data = array('min_strength'=>$row->strength,
                      'max_strength'=>$row->strength,
                      'med_strength'=>$row->strength,
                      'min_sign'=>$row->sign,
                      'max_sign'=>$row->sign,
                      'med_sign'=>$row->sign,
                      'min_certainty'=>$row->certainty,
                      'max_certainty'=>$row->certainty,
                      'med_certainty'=>$row->certainty);

        $this->db->where('id',$row->id);
        $this->db->update('userLinks',$data);
      }

    }
  
    //now go through all duplicates and set min max and med
    $this->db->where('num_observers >',1);
    $query = $this->db->get('userLinks');
    $statusAR = array();
    foreach($query->result() as $row)
    {
      //see if already set
      if(isset($statusAR[$row->issueFromId][$row->issueToId]))
      {
        //already used, so delete
        $this->db->where('id',$row->id);
        $this->db->delete('userLinks');
        
      } else
      {
        //vars for attributes
        $minStr = '';
        $maxStr = '';
        $medStr = '';
        $minSi = '';
        $maxSi = '';
        $medSi = '';
        $minCert = '';
        $maxCert = '';
        $medCert = '';

        //get all rows that have same from and to id and average
        $this->db->where('issueFromId',$row->issueFromId);
        $this->db->where('issueToId',$row->issueToId);
        $query2 = $this->db->get('userLinks');

        $j = 0;
        foreach($query2->result() as $row2)
        {
          if($row2->strength != -9)
          {
            if($minStr != '')
            {
              echo $row2->id . " min str: " . $minStr . '<br/>';
              $minStr = min($minStr,$row2->strength);
            } else {
              $minStr = $row2->strength;
              echo $row2->id . " min str: " . $minStr . '<br/>';
            }

            if($maxStr != '')
            {
              $maxStr = max($maxStr,$row2->strength);
            } else {
              $maxStr = $row2->strength;
            }
            if($medStr != '')
            {
              $medStr = ($medStr*$j + $row2->strength)/($j+1);
            } else {
              $medStr = $row2->strength;
            }
          }

          if($row2->sign != -9)
          {
            if($minSi != '')
            {
              $minSi = min($minSi,$row2->sign);
            } else {
              $minSi = $row2->sign;
            }

            if($maxSi != '')
            {
              $maxSi = max($maxSi,$row2->sign);
            } else {
              $maxSi = $row2->sign;
            }

            if($medSi != '')
            {
              $medSi = ($medSi*$j + $row2->sign)/($j+1);
            } else {
              $medSi = $row2->sign;
            }  
          }
          
          if($row2->certainty != -9)
          {
            if($minCert != '')
            {
              $minCert = min($minCert,$row2->certainty);
            } else {
              $minCert = $row2->certainty;
            }

            if($maxCert != '')
            {
              $maxCert = max($maxCert,$row2->certainty);
            } else {
              $maxCert = $row2->certainty;
            }

            if($medCert != '')
            {
              $medCert = ($medCert*$j + $row2->certainty)/($j+1);
            } else {
              $medCert = $row2->certainty;
            }
          }
          $j++;
        }

        //now insert vals
        $data = array('min_strength'=>$minStr,
                      'max_strength'=>$maxStr,
                      'med_strength'=>$medStr,
                      'min_sign'=>$minSi,
                      'max_sign'=>$maxSi,
                      'med_sign'=>$medSi,
                      'min_certainty'=>$minCert,
                      'max_certainty'=>$maxCert,
                      'med_certainty'=>$medCert);

        $this->db->where('id',$row->id);
        $this->db->update('userLinks',$data);
        
        //set status array to done
        $statusAR[$row->issueFromId][$row->issueToId] = 1;
        
      }
    }


  }


  public function set_up_nodes()
  {
    //first remove all spaces in names
    $query = $this->db->get('nodes');
    foreach($query->result() as $row)
    {
      //get name
      $nm = $row->name;
      $nm = str_replace(' ', '', $nm);
      $this->db->where('id',$row->id);
      $data = array('name'=>$nm);
      $this->db->update('nodes',$data);
    }

    $this->db->where('projectId',9);
    $query = $this->db->get('nodes');
    foreach($query->result() as $row)
    {
      //get all tags for issue
      $this->db->where('nodeId',$row->id);
      $query2 = $this->db->get('nodesNodeTags');
      foreach($query2->result() as $row2)
      {
        $tagId = $row2->tagId;
        $this->db->where('id',$tagId);
        $this->db->select('name');
        $query = $this->db->get('nodeTags');
        $tagName = strtolower($query->row('name'));
        $tagName = str_replace(' ', '_', $tagName);
        //get field list
        $field_list = $this->db->list_fields('nodes');
        if(in_array($tagName, $field_list))
        {
          //update row with 1 in tag field
          $data = array($tagName=>1);
          $this->db->where('id',$row->id);
          $this->db->update('nodes',$data);
          echo "setting: ".$tagName." for row: ".$row->id.'<br/>';
        }

      }
    }


  }

  public function get_users_json_links($num)
  {
    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    //$uId = 48;
    $this->db->where('projectId',$pId);
    $this->db->where('userId',$uId);
    $query = $this->db->get('userLinks');
    $returnAR = array();
    $ind = 0;
    foreach($query->result() as $row)
    {
      if($ind > $num && $num != -1)
      {
        break;
      }
      $issFrom = $row->issueFromId;
      $issTo = $row->issueToId;
      $sign = $row->sign;
      if($sign == 1)
      {
        $sign = 'positive';
      } else if($sign == -1)
      {
        $sign = 'negative';
      } else
      {
        $sign = 'unspecified';
      }
      //get from name
      $this->db->limit(1);
      $this->db->select('name');
      $this->db->where('id',$issFrom);
      $query2 = $this->db->get('nodes');
      $fromName = $query2->row('name');
      //get to name
      $this->db->limit(1);
      $this->db->select('name');
      $this->db->where('id',$issTo);
      $query2 = $this->db->get('nodes');
      $toName = $query2->row('name');
      $returnAR[] = array('source'=>$fromName,
                          'target'=>$toName,
                          'type'=>$sign);


      $ind++;
    }
    return $returnAR;
  }

  public function get_users_json_nodes($num)
  {
    $pId = $this->session->userdata('user_project_id');
    $uId = $this->_get_user_id();
    //$uId = 48;
    //get users chosen from nodes
    $this->db->where('userId',$uId);
    $this->db->where('projectId',$pId);
    $this->db->select('nodeId');
    $query = $this->db->get('usersChosenFromNodes');
    $chosenAR = array();
    foreach($query->result() as $row)
    {
      $chosenAR[] = $row->nodeId;
    }

    //now get all nodes and push into array
    $returnAR = array();
    $this->db->where('projectId',$pId);
    $this->db->select('name');
    $this->db->select('id');
    $query = $this->db->get('nodes');
    foreach($query->result() as $row)
    {
      $nodeId = $row->id;
      $nodeName = $row->name;
      if(in_array($nodeId, $chosenAR))
      {
        $nodeFoc = 'focal';
      } else
      {
        $nodeFoc = 'nonfocal';
      }

      $returnAR[] = array('name'=>$nodeName,
                          'type'=>$nodeFoc);
    }
    return $returnAR;
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


  /*public function set_dummy_data()
  {
    for($i=0;$i<100;$i++)
    {
      $data = array('projectId'=>9,
                    'issueInd'=>$i,
                    'name'=>'Species'.$i,
                    'description'=>'species description, common names, or other notes',
                    'userId'=>1);

      $this->db->insert('nodes',$data);
    }
  }*/
}
?>