<?php

class Links extends User_Controller {
               
	public function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Links_model');
	}	

	public function index()
	{
		redirect('');
	}

	public function mapping_instructions()
	{
		$from_nodes = $this->Links_model->get_user_from_nodes();
		if(count($from_nodes) > 0)
		{
			redirect('links/mapping');
		}
		$this->view_data['link_mapping_instructions'] = $this->Links_model->get_link_mapping_instructions();
		$this->view_data['video_embed'] = $this->Links_model->get_video_instructions();
		$this->load_container_with('link_mapping_instructions_view','LINK MAPPING INSTRUCTIONS');
	}

	public function mapping()
	{
		//eventually get these tags from admin
    $pId = $this->session->userdata('user_project_id');

    //go get tags for this project
    $subset_tags = $this->Links_model->get_project_node_tags();
		
		$project_name = $this->Links_model->get_project_name();
		$this->view_data['project_name'] = $project_name;
		$from_nodes = $this->Links_model->get_from_nodes($subset_tags);
		$this->view_data['from_nodes'] = $from_nodes;
		//get number of each subset tags
		$this->view_data['subset_tags'] = $this->Links_model->organize_subset_tags($subset_tags,$from_nodes);
		$this->view_data['user_from_nodes'] = $this->Links_model->get_user_from_nodes();
		$this->view_data['to_nodes'] = $this->Links_model->get_to_nodes();
		$this->view_data['user_links'] = $this->Links_model->get_user_links();
		$this->view_data['user_notes'] = $this->Links_model->get_user_notes();
		$this->view_data['total_froms'] = $this->Links_model->get_total_froms();
		$this->view_data['link_mapping_instructions'] = $this->Links_model->get_link_mapping_instructions();
		$this->view_data['video_embed'] = $this->Links_model->get_video_instructions();
		$this->view_data['admin_email'] = $this->Links_model->get_admin_email();
		//get tags for all nodes in projects
		$this->view_data['tags'] = $this->Links_model->get_project_node_tags();
		$this->load_container_with('link_mapping_view','LINK MAPPING FOR '.strtoupper($project_name));
	}

	public function set_from_nodes()
	{
		$ret = $this->Links_model->set_user_from_nodes($this->input->post());
		echo $ret;
	}

	public function remove_to_node()
	{
		$to_id = $this->input->post('to_id');
		$ret = $this->Links_model->remove_to_node($to_id);
		echo $ret;
	}

	//send new issue to Eric's email
	public function create_new_issue()
	{
		$tit = $this->input->post('name');
		$desc = $this->input->post('description');
		$units = $this->input->post('units');
		$tags = $this->input->post('categories');
		$ret = $this->Links_model->send_issue_to_email($tit,$desc,$units,$tags);
		echo $ret;
	}

	public function create_user_link()
	{
		$fId = $this->input->post('from_id');
		$tId = $this->input->post('to_id');
		$dir = $this->input->post('direction');
		$lId = $this->Links_model->create_user_link($fId,$tId);
		$ret = array('link_id'=>$lId);
		if($this->input->post('both') == 'true')
		{
			$tId = $this->input->post('from_id');
			$fId = $this->input->post('to_id');
			$lId = $this->Links_model->create_user_link($fId,$tId);
			$ret['link_id2'] = $lId;
		}
		//also set so know, has begun drawing links on this arrow
		$this->Links_model->set_is_begun($dir,$fId,$tId);

		echo json_encode($ret);
	}

	public function delete_user_link() 
	{
		$lId = $this->input->post('link_id');
		$lId2 = $this->input->post('link_id2');
		$ret = $this->Links_model->delete_user_link($lId,$lId2);
		echo $ret;
	}

	public function save_user_link()
	{
		$lId = $this->input->post('link_id');
		$form_data = array('sign'=>$this->input->post('sign'),
												'modified'=>date( 'Y-m-d H:i:s'));
		$this->Links_model->save_user_link($lId,$form_data);
		echo $lId;
	}

	public function save_comment()
	{
		$lId = $this->input->post('link_id');
		$comment = $this->input->post('comment');
		$this->Links_model->save_comment($lId,$comment);
	}

	public function save_user_notes()
	{
		$uN = $this->input->post('notes');
		$this->Links_model->save_user_notes($uN);
	}

	public function save_user_feedback()
	{
		$fb = $this->input->post('feedback');
		$url = $this->input->post('url');
		$user_agent = $this->input->post('useragent');
		$this->Links_model->save_user_feedback($fb,$url,$user_agent);
		echo "saved";
	}

	public function save_done_from()
	{
		$from_id = $this->input->post('from_id');
		$is_checked = $this->input->post('is_checked');
		$this->Links_model->save_done_from($from_id,$is_checked);
		echo "saved";
	}

	public function reduce_tags()
	{
		$this->Links_model->reduce_tags();
	}

	public function nodes_by_tags()
	{
		$subset_tags = array('access/circulate',
												'analyze/discover',
												'sense/create',
												'store/organize',
												'derived value',
												'act/collaborate');
		print_r($this->Links_model->get_nodes_by_tags($subset_tags));
	}


	public function export_links_to_csv($pId)
	{
		$fields = array('id',
										'issueFromId',
										'issueToId',
										'sign',
										'totPosVotes',
										'totNegVotes',
										'totPropPos',
										'totPropNeg',
										'totVotes',
										'totFocalEvaluated',
										'propTotVotes',
										'totalEvaluated',
										'tot_notes',
										'notes');


		//get total number of users that have at least chosen from nodes
		$this->db->where('projectId',$pId);
		$this->db->select('userId');
		$this->db->distinct();
		$query = $this->db->get('usersChosenFromNodes');
		$tot_users = $query->num_rows();

		$this->db->where('projectId',$pId);
		$query = $this->db->get('userLinks');
		$resultAR = array();
		foreach($query->result() as $row)
		{
			//see if this link has already been entered
			$fId = $row->issueFromId;
			$tId = $row->issueToId;
			$id = $row->id;
			$si = $row->sign;
			$note = $row->comment;
			$ind = -1;
			for($i=0;$i<count($resultAR);$i++)
			{
				$r = $resultAR[$i];
				if($fId == $r['issueFromId'] && $tId == $r['issueToId'])
				{
					$ind = $i;
					break;
				}
			}
			//is new link
			if($ind == -1)
			{
				$resultAR[] = array('id'=>$id,
														'issueFromId'=>$fId,
														'issueToId'=>$tId,
														'sign'=>0,
														'totPosVotes'=>0,
														'totNegVotes'=>0,
														'totPropPos'=>0,
														'totPropNeg'=>0,
														'totVotes'=>0,
														'totFocalEvaluated'=>0,
														'propTotVotes'=>0,
														'totalEvaluated'=>0,
														'tot_notes'=>0,
														'notes'=>'');
				$ind = count($resultAR)-1;


				//get focal evaluated
				$this->db->where('projectId',$pId);
				$this->db->where('nodeId',$fId);
				$this->db->or_where('nodeId',$tId);
				$query2 = $this->db->get('usersChosenFromNodes');
				$resultAR[$ind]['totFocalEvaluated'] = $query2->num_rows();

				//get total evaluated
				//get 
				//(total users, tehn subtract total deleted)
				$this->db->where('projectId',$pId);
				$this->db->select('id');
				$this->db->where('nodeId',$fId);
				$this->db->or_where('nodeId',$tId);
				$query2 = $this->db->get('userDeletedToNodes');
				$resultAR[$ind]['totalEvaluated'] = $tot_users-$query2->num_rows();

			}

			$resultAR[$ind]['totVotes']+=1;
			//increase sign
			if($si == 1)
			{
				$resultAR[$ind]['totPosVotes']+=1; 
			} else if($si == -1)
			{
				$resultAR[$ind]['totNegVotes']+=1; 
			}
			$delt = $resultAR[$ind]['totPosVotes']-$resultAR[$ind]['totNegVotes'];
			if($delt > 1)
			{
				$sign = 1;
			} else if($delt < 1)
			{
				$sign = -1;
			} else
			{
				$sign = 0;
			}

			$resultAR[$ind]['sign'] = $sign;

			//reset proportion
			$resultAR[$ind]['totPropPos'] = $resultAR[$ind]['totPosVotes']/$resultAR[$ind]['totVotes'];
			$resultAR[$ind]['totPropNeg'] = $resultAR[$ind]['totNegVotes']/$resultAR[$ind]['totVotes'];

			if($note != "")
			{
				$resultAR[$ind]['tot_notes']+=1;
				if($resultAR[$ind]['notes'] == "")
				{
					$resultAR[$ind]['notes'] = $note;
				} else
				{
					$resultAR[$ind]['notes'] .= "|" . $note;
				}
			}

			//proportion
			if($resultAR[$ind]['totFocalEvaluated'] == 0)
			{
				$resultAR[$ind]['propTotVotes'] = 0;
			} else
			{
				$resultAR[$ind]['propTotVotes'] = $resultAR[$ind]['totVotes']/$resultAR[$ind]['totFocalEvaluated'];
			}

		}

		//convert to csv
		$csv =& $this->csv_from_result($fields,$resultAR);
		$this->load->helper('download');
		force_download('links.csv', $csv);

	}

	public function create_excel($resultAR,$fields,$name)
	{

		$headers = ''; // just creating the var for field headers to append to below
		$data = ''; // just creating the var for field data to append to below


    foreach ($fields as $field) {
       $headers .= $field . "\t";
    }

    foreach ($resultAR as $row) {
         $line = '';
         foreach($row as $value) {

          		if($value === 0)
          		{
          			$value = '0';
          		}                                            
              if ((!isset($value)) OR ($value == "")) {
                   $value = "\t";
              } else {
                   $value = str_replace('"', '""', $value);
                   $value = '"' . $value . '"' . "\t";
              }
              $line .= $value;
         }
         $data .= trim($line)."\n";
    }

    $data = str_replace("\r","",$data);

    header('Content-type: application/ms-excel');
    header("Content-Disposition: attachment; filename=$name.xls");
    echo "$headers\n$data";  
	}


	public function export_nodes_to_excel()
	{
    $pId = 8;

    $fields = array('id',
    								'name',
    								'description',
    								'tot_votes',
    								'units',
    								'tot_foc',
    								'tot_foc_eval',
    								'tot_eval',
    								'tot_rem',
    								'access_circulate',
    								'analyze_discover',
    								'sense_create',
    								'store_organize',
    								'derived_value',
    								'act_collaborate');

    $this->db->where('projectId',$pId);
    $query = $this->db->get('nodes');
    $resultAR = array();
    foreach($query->result() as $row)
    {
    	//set up correct order
    	$resultAR[] = array('id'=>'',
    								'name'=>'',
    								'description'=>'',
    								'tot_votes'=>'',
    								'units'=>'',
    								'tot_foc'=>'',
    								'tot_foc_eval'=>'',
    								'tot_eval'=>'',
    								'tot_rem'=>'',
    								'access_circulate'=>0,
    								'analyze_discover'=>0,
    								'sense_create'=>0,
    								'store_organize'=>0,
    								'derived_value'=>0,
    								'act_collaborate'=>0);
    	$ind = count($resultAR)-1;
    	$resultAR[$ind]['id'] = $row->id;
    	$resultAR[$ind]['name'] = $row->name;
    	$resultAR[$ind]['description'] = $row->description;
    	$resultAR[$ind]['units'] = $row->units;
    	$resultAR[$ind]['tot_votes'] = $row->votes;
    	//get total times this node in focal set
    	$this->db->where('nodeId',$row->id);
    	$query2 = $this->db->get('usersChosenFromNodes');
    	$resultAR[$ind]['tot_foc'] = $query2->num_rows();

    	$this->db->where('nodeId',$row->id);
    	$this->db->where('isBegun',1);
    	$query2 = $this->db->get('usersChosenFromNodes');
    	$resultAR[$ind]['tot_foc_eval'] = $query2->num_rows();

    	//get total links for this node
    	$this->db->where('issueFromId',$row->id);
    	$this->db->or_where('issueToId',$row->id);
    	$query2 = $this->db->get('userLinks');
    	$resultAR[$ind]['tot_eval'] = $query2->num_rows();

    	//total times removed
    	$this->db->where('nodeId',$row->id);
    	$query2 = $this->db->get('userDeletedToNodes');
    	$resultAR[$ind]['tot_rem'] = $query2->num_rows();

      //get tags matching this row id
      $this->db->where('nodeId',$row->id);
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
        switch ($sub_tag) {
        	case 'access/circulate':
        		$resultAR[$ind]['access_circulate'] = 1;
        		break;
        	case 'analyze/discover':
        		$resultAR[$ind]['analyze_discover'] = 1;
        		break;
        	case 'sense/create':
        		$resultAR[$ind]['sense_create'] = 1;
        		break;
        	case 'store/organize':
        		$resultAR[$ind]['store_organize'] = 1;
        		break;
        	case 'derived value':
        		$resultAR[$ind]['derived_value'] = 1;
        		break;
        	case 'act/collaborate':
        		$resultAR[$ind]['act_collaborate'] = 1;
        		break;
        	default:
        		break;
        }

      }

    }
		$this->create_excel($resultAR,$fields,'nodes');
	}

	public function export_users_to_csv($pId)
	{
		$fields = array('id',
										'email',
										'first_name',
										'last_name',
										'gender',
										'date_began',
										'tot_links',
										'tot_links_eval',
										'tot_focal_sel',
										'tot_focal_eval',
										'tot_rem',
										'new_issues');

		$resultAR = array();
		$this->db->from('usersProjects');
		$this->db->join('users', 'usersProjects.userId = users.user_id');
		$this->db->where('projectId',$pId);
		$query = $this->db->get();

		foreach($query->result() as $row)
		{
			$resultAR[] = array('id'=>'',
													'email'=>'',
													'first_name'=>'',
													'last_name'=>'',
													'gender'=>'',
													'date_began'=>'',
													'tot_links'=>'',
													'tot_links_eval'=>'',
													'tot_focal_sel'=>'',
													'tot_focal_eval'=>'',
													'tot_rem'=>'',
													'new_issues'=>0);
    	$ind = count($resultAR)-1;
			$resultAR[$ind]['id'] = $row->user_id;
			$resultAR[$ind]['email'] = $row->user_email;
			$resultAR[$ind]['first_name'] = $row->first_name;
			$resultAR[$ind]['last_name'] = $row->last_name;
			//get earliest date of link created by user
			$this->db->where('projectId',$pId);
			$this->db->where('userId',$row->user_id);
			$this->db->select('modified');
			$this->db->order_by('modified','ASC');
			$query2 = $this->db->get('userLinks');

			$resultAR[$ind]['tot_links'] = $query2->num_rows();
			//make sure there's at least one link drawn
			foreach($query2->result() as $row2)
			{
				if($resultAR[$ind]['date_began'] == '' && $row2->modified != '0000-00-00 00:00:00')
				{
					$resultAR[$ind]['date_began'] = $row2->modified;
					break;
				}
			}

			//get total nodes minus deleted and total chosen nodes
	    $this->db->where('projectId',$pId);
	    $query2 = $this->db->get('nodes');
	    $totNodes = $query2->num_rows();
	    //nodes deleted by this user
	    $this->db->where('userId',$row->user_id);
	    $query2 = $this->db->get('userDeletedToNodes');
	    $resultAR[$ind]['tot_rem'] = $query2->num_rows();
	    $totNodes-=$query2->num_rows();
	    //total chosen nodes
	    $this->db->where('userId',$row->user_id);
	    $query2 = $this->db->get('usersChosenFromNodes');
	    $resultAR[$ind]['tot_links_eval'] = $totNodes*$query2->num_rows();
	    $resultAR[$ind]['tot_focal_sel'] = $query2->num_rows();

	    $this->db->where('userId',$row->user_id);
	    $this->db->where('isBegun',1);
	    $query2 = $this->db->get('usersChosenFromNodes');
	    $resultAR[$ind]['tot_focal_eval'] = $query2->num_rows();


		}
		//convert to csv
		$csv =& $this->csv_from_result($fields,$resultAR);
		$this->load->helper('download');
		force_download('users.csv', $csv);
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

	//create json feed of links
	public function get_users_json_links($num = -1)
	{
		echo json_encode($this->Links_model->get_users_json_links($num));
	}

	public function get_users_json_nodes($num = -1)
	{
		echo json_encode($this->Links_model->get_users_json_nodes($num));
	}
}
?>