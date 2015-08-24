<?php
class Work_flow_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
     * 创建工单
     * @param int $flow_id
     * @param int $form_id
     * @param int $this_node_id
     * @param int $next_node_id
     * @param array $insert
     * @return bool
     */
	public function insert_work_model($flow_id=0,$form_id=0,$this_node_id=0,$next_node_id=0,$insert=array())
	{
		if(empty($insert)||empty($flow_id)||empty($form_id)||empty($this_node_id)||empty($next_node_id))
		{
			return false;
		}
		$flow_number = $this->new_flow_number();//流程编码
		$user_id = $this->session->userdata('user_id');
		$dept_id = $this->session->userdata('dept_id');
		$insert_time = time();
		$cle_id = empty($insert['cle_id'])?0:$insert['cle_id'];

		$this->db_write->trans_start();

		//获取节点信息
		$this->load->model('flow_model');
		$this_node_info = $this->flow_model->get_node_info($this_node_id);
		$this_node_over_time = "";
		if(!empty($this_node_info['node_use_time']))
		{
			$this_node_over_time = $insert_time+($this_node_info['node_use_time'])*3600;
		}
		//流程信息
		$insert_flow = array(
		'flow_number'=>$flow_number,
		'flow_start_time'=>$insert_time,
		'cle_id'=>$cle_id,
		'node'.$this_node_id=>$this_node_id,
		'node'.$this_node_id.'_start_time'=>$insert_time,
		'node'.$this_node_id.'_end_time'=>$insert_time,
		'node'.$this_node_id.'_over_time'=>$this_node_over_time,
		'node'.$this_node_id.'_user_id'=>$user_id,
		'node'.$this_node_id.'_status'=>2,
		'node'.$this_node_id.'_remark'=>''
		);
		$insert_flow['flow_status'] = 1;
		if(!is_numeric($next_node_id)&&$next_node_id=='结束')
		{
			$insert_flow['flow_status'] = 2;
			$insert_flow['flow_end_time'] = $insert_time;
		}
		else
		{
			$insert_flow['node'.$next_node_id] = $next_node_id;
			$insert_flow['node'.$next_node_id.'_status'] = 0;
		}
		$this->db_write->insert('est_flow_info'.$flow_id,$insert_flow);
		$flow_info_id = $this->db_write->insert_id();
		//流程详细信息
		//当前节点
		$insert_this_detail = array(
		'flow_id'=>$flow_id,
		'cle_id'=>$cle_id,
		'flow_number'=>$flow_number,
		'flow_info_id'=>$flow_info_id,
		'node_id'=>$this_node_id,
		'node_status'=>2,
		'form_info_id'=>$form_id,
		'node_start_time'=>$insert_time,
		'node_end_time'=>$insert_time,
		'node_over_time'=>$this_node_over_time,
		'dept_id'=>$dept_id,
		'deal_user_id'=>$user_id
		);
		$this->db_write->insert('est_flow_detail',$insert_this_detail);
		$flow_detail_id = $this->db_write->insert_id();
		//下一个节点
		if(is_numeric($next_node_id)&&$next_node_id!='结束')
		{
			$insert_next_detail = array(
			'flow_id'=>$flow_id,
			'cle_id'=>$cle_id,
			'flow_number'=>$flow_number,
			'flow_info_id'=>$flow_info_id,
			'node_id'=>$next_node_id,
			'node_status'=>0
			);
			$this->db_write->insert('est_flow_detail',$insert_next_detail);
		}
		//表单信息
		$this->load->model('form_model');
		$form_fields_info = $this->form_model->get_form_fields_info($form_id);
		$form_fields = array();
		foreach($form_fields_info as $form)
		{
			$form_fields[] = $form['fields'];
		}
		if(!empty($form_fields))
		{
			$insert_form = array();
			foreach($form_fields as $field)
			{
				if(isset($insert[$field]))
				{
					if(is_array($insert[$field]))
					{
						$insert_form[$field] = implode(',',$insert[$field]);
					}
					else
					{
						$insert_form[$field] = empty($insert[$field]) ? "" : replace_illegal_string($insert[$field]);
					}
				}
			}
			if(!empty($insert_form))
			{
				$insert_form['flow_id'] = $flow_id;
				$insert_form['node_id'] = $this_node_id;
				$insert_form['flow_info_id'] = $flow_info_id;
				$insert_form['create_user_id'] = $user_id;
				$insert_form['create_time'] = $insert_time;
				$this->db_write->insert('est_form_info'.$form_id,$insert_form);
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			return $flow_detail_id;
		}
		else
		{
			return false;
		}

	}

	/**
     * 占用未处理工单
     * @param int $detail_id
     * @param int $flow_info_id
     * @param int $node_id
     * @return bool
     */
	public function update_node_statues_user($detail_id=0,$flow_info_id=0,$node_id=0)
	{
		if(empty($detail_id)||empty($flow_info_id)||empty($node_id))
		{
			return false;
		}
		$user_id = $this->session->userdata('user_id');
		$dept_id = $this->session->userdata('dept_id');
		$time = time();
		//获取节点信息
		$this->load->model('flow_model');
		$node_info = $this->flow_model->get_node_info($node_id);
		$node_over_time = '';
		if(!empty($node_info['node_use_time']))
		{
			$node_over_time = $time+($node_info['node_use_time'])*3600;
		}
		//流程详细记录
		$update_detail = array('deal_user_id'=>$user_id,'node_start_time'=>$time,'node_over_time'=>$node_over_time,'dept_id'=>$dept_id);
		$this->db_write->update('est_flow_detail',$update_detail,array('id'=>$detail_id));
		//流程记录
		$update_flow_info = array('node'.$node_id.'_user_id'=>$user_id,'node'.$node_id.'_start_time'=>$time,'node'.$node_id.'_over_time'=>$node_over_time);
		$this->db_write->update('est_flow_info'.$node_info['flow_id'],$update_flow_info,array('flow_info_id'=>$flow_info_id));
		return true;
	}

	/**
     * 处理工单
     * @param int $flow_detail_id
     * @param int $flow_info_id
     * @param int $flow_id
     * @param int $form_id
     * @param int $this_node_id
     * @param int $next_node_id
     * @param array $update
     * @return bool
     */
	public function update_work_model($flow_detail_id=0,$flow_info_id=0,$flow_id=0,$form_id=0,$this_node_id=0,$next_node_id=0,$update=array())
	{
		if(empty($flow_detail_id)||empty($flow_info_id)||empty($flow_id)||empty($form_id)||empty($this_node_id)||empty($next_node_id)||empty($update))
		{
			return false;
		}
		$user_id = $this->session->userdata('user_id');
		$now_time = time();
		$node_status = 2; //正常完成
		$reason = '';//超时原因
		$cle_id = empty($update['cle_id'])?0:$update['cle_id'];
		if(is_numeric($next_node_id))
		{
			if($next_node_id<$this_node_id)
			{
				$node_status = 3;//退回
			}
		}
		$detail_info = $this->get_flow_detail_info($flow_detail_id);
		if(!empty($detail_info['node_over_time']))
		{
			if($now_time>$detail_info['node_over_time'])
			{
				$node_status = 4;//超时完成
				$reason = !isset($update['over_time_reason']) ? '' : $update['over_time_reason'];
			}
		}
		$this->db_write->trans_start();

		//流程详细记录
		if($node_status==3)
		{
			//上一节点状态变为退回
			$update_detail = array('node_status'=>3,'node_end_time'=>'','back_reason'=>'','back_reason'=>!isset($update['back_reason']) ? '' : $update['back_reason']);
			$update_detail['node_id'] = $next_node_id;
			$this->db_write->update('est_flow_detail',$update_detail,array('node_id'=>$next_node_id,'flow_id'=>$flow_id,'flow_info_id'=>$flow_info_id));
			//下一节点正常完成
			$update_this_detail = array('node_status'=>2,'node_end_time'=>$now_time,'reason'=>$reason,'back_reason'=>'');
			$this->db_write->update('est_flow_detail',$update_this_detail,array('id'=>$flow_detail_id));
		}
		else
		{
			$update_this_detail = array('node_status'=>$node_status,'node_end_time'=>$now_time,'reason'=>$reason,'back_reason'=>'');
			$this->db_write->update('est_flow_detail',$update_this_detail,array('id'=>$flow_detail_id));
		}
		if(is_numeric($next_node_id)&&$next_node_id!='结束'&&$node_status!=3)
		{
			$insert_next_detail = array(
			'flow_id'=>$flow_id,
			'cle_id'=>$cle_id,
			'flow_number'=>$detail_info['flow_number'],
			'flow_info_id'=>$flow_info_id,
			'form_info_id'=>$form_id,
			'node_id'=>$next_node_id,
			'node_status'=>0
			);
			$this->db_write->insert('est_flow_detail',$insert_next_detail);
		}
		//流程记录
		if($node_status==3)
		{
			$update_flow_info = array('node'.$next_node_id.'_status'=>3,'node'.$next_node_id.'_end_time'=>'','node'.$this_node_id.'_status'=>2,'node'.$this_node_id.'_end_time'=>$now_time,'node'.$this_node_id.'_remark'=>$reason);
		}
		else
		{
			$update_flow_info = array('node'.$this_node_id.'_status'=>$node_status,'node'.$this_node_id.'_end_time'=>$now_time,'node'.$this_node_id.'_remark'=>$reason);
			if(!is_numeric($next_node_id)&&$next_node_id=='结束')
			{
				$update_flow_info['flow_status'] = 2;
				$update_flow_info['flow_end_time'] = $now_time;
			}
		}
		$this->db_write->update('est_flow_info'.$flow_id,$update_flow_info,array('flow_info_id'=>$flow_info_id));
		//表单
		if($node_status!=3)
		{
			$this->load->model('form_model');
			$form_fields_info = $this->form_model->get_form_fields_info($form_id);
			$form_fields = array();
			foreach($form_fields_info as $form)
			{
				$form_fields[] = $form['fields'];
			}
			if(!empty($form_fields))
			{
				$update_form = array();
				foreach($form_fields as $field)
				{
					if(isset($update[$field]))
					{
						if(is_array($update[$field]))
						{
							$update_form[$field] = implode(',',$update[$field]);
						}
						else
						{
							$update_form[$field] = empty($update[$field]) ? "" : replace_illegal_string($update[$field]);
						}
					}
				}
				if(!empty($update_form))
				{
					$update_form['flow_id'] = $flow_id;
					$update_form['node_id'] = $this_node_id;
					$update_form['flow_info_id'] = $flow_info_id;
					$update_form['create_user_id'] = $user_id;
					$update_form['create_time'] = $now_time;
					$this->db_write->insert('est_form_info'.$form_id,$update_form);
				}
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
     * 退回后保存节点
     * @param int $flow_detail_id
     * @param int $flow_info_id
     * @param int $flow_id
     * @param int $form_id
     * @param int $this_node_id
     * @param int $next_node_id
     * @param array $update
     * @return bool
     */
	public function update_back_work_flow($flow_detail_id=0,$flow_info_id=0,$flow_id=0,$form_id=0,$this_node_id=0,$next_node_id=0,$update=array())
	{
		if(empty($flow_detail_id)||empty($flow_info_id)||empty($flow_id)||empty($form_id)||empty($this_node_id)||empty($next_node_id)||empty($update))
		{
			return false;
		}
		$now_time = time();
		$this->db_write->trans_start();
		
		$back_reason = date('Y-m-d H:i:s').'已处理退回工单';
		if(!is_numeric($next_node_id)&&$next_node_id=='结束')
		{
			$back_reason = date('Y-m-d H:i:s').'已处理退回工单，并结束工单';
			//流程记录
			$update_flow_info = array('node'.$this_node_id.'_status'=>2,'node'.$this_node_id.'_end_time'=>$now_time,'node'.$this_node_id.'_remark'=>!isset($update['over_time_reason']) ? '' : $update['over_time_reason'],'flow_status'=>2,'flow_end_time'=>$now_time);
			$this->db_write->update('est_flow_info'.$flow_id,$update_flow_info,array('flow_info_id'=>$flow_info_id));
		}
		else
		{
			//详细记录 - 下一节点
			$update_next_detail = array('node_status'=>0,'node_start_time'=>$now_time,'node_end_time'=>'');
			$this->db_write->update('est_flow_detail',$update_next_detail,array('node_id'=>$next_node_id,'flow_id'=>$flow_id,'flow_info_id'=>$flow_info_id));
			
			//流程记录
			$update_flow_info = array('node'.$next_node_id.'_status'=>0,'node'.$next_node_id.'_end_time'=>'','node'.$this_node_id.'_status'=>2,'node'.$this_node_id.'_end_time'=>$now_time,'node'.$this_node_id.'_remark'=>!isset($update['over_time_reason']) ? '' : $update['over_time_reason']);
			$this->db_write->update('est_flow_info'.$flow_id,$update_flow_info,array('flow_info_id'=>$flow_info_id));
		}
		//详细记录 - 当前节点
		$update_detail = array('node_status'=>2,'node_end_time'=>$now_time,'back_reason'=>$back_reason);
		$this->db_write->update('est_flow_detail',$update_detail,array('id'=>$flow_detail_id));

		//表单
		$this->load->model('form_model');
		$form_fields_info = $this->form_model->get_form_fields_info($form_id);
		$form_fields = array();
		foreach($form_fields_info as $form)
		{
			$form_fields[] = $form['fields'];
		}
		if(!empty($form_fields))
		{
			$update_form = array();
			foreach($form_fields as $field)
			{
				if(isset($update[$field]))
				{
					if(is_array($update[$field]))
					{
						$update_form[$field] = implode(',',$update[$field]);
					}
					else
					{
						$update_form[$field] = empty($update[$field]) ? "" : replace_illegal_string($update[$field]);
					}
				}
			}
			if(!empty($update_form))
			{
				$this->db_write->update('est_form_info'.$form_id,$update_form,array('flow_id'=>$flow_id,'node_id'=>$this_node_id,'flow_info_id'=>$flow_info_id));
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	/**
	 * 获取指定流程详细记录信息
	 *
	 * @param int $id
	 * @return array
	 */
	public function get_flow_detail_info($id=0)
	{
		if(empty($id))
		{
			return false;
		}
		$this->db_read->where('id',$id);
		$query = $this->db_read->get('est_flow_detail');
		$info = $query->row_array();
		if(!empty($info['deal_user_id']))
		{
			$this->load->model('user_model');
			$user_info = $this->user_model->get_user_info($info['deal_user_id']);
			if($user_info && isset($user_info['user_name']) && isset($user_info['user_num']))
			{
				$info['user_name'] = $user_info['user_name'];
				$info['user_num'] = $user_info['user_num'];
			}
		}
		return $info;
	}

	/**
	 * 获取指定流程的指定流程记录信息
	 *
	 * @param int $flow_id
	 * @param int $flow_info_id
	 * @return array
	 */
	public function get_work_flow_info($flow_id=0,$flow_info_id=0)
	{
		if(empty($flow_id)||empty($flow_info_id))
		{
			return false;
		}
		$this->db_read->where('flow_info_id',$flow_info_id);
		$query = $this->db_read->get('est_flow_info'.$flow_id);
		return $query->row_array();
	}

	/**
     * 获取流程节点中表单填写的数据
     *
     * @param int $form_id
     * @param int $flow_id
     * @param int $flow_info_id
     * @param int $node_id
     * @return array
     */
	public function get_form_value_info($form_id=0,$flow_id=0,$flow_info_id=0,$node_id=0)
	{
		if(empty($form_id)||empty($flow_id)||empty($node_id)||empty($flow_info_id))
		{
			return false;
		}

        $info = array();
		$table = 'est_form_info'.$form_id;
        if ($this->db_read->table_exists($table))
        {
            $this->db_read->where('flow_id',$flow_id);
            $this->db_read->where('flow_info_id',$flow_info_id);
            $this->db_read->where('node_id',$node_id);
            $query = $this->db_read->get($table);
            $info = array();
            foreach($query->row_array() as $fields=>$value)
            {
                $info[$fields."_".$node_id] = empty($value) ? "" : $value;
            }
        }

		return $info;
	}

	/**
	 * 待处理列表数据
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param string $sort
	 * @param string $order
	 * @return mixed
	 */
	public function get_work_flow_nodeal_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $FLOWNODE_STATUS;
		$wheres = array();
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		if(!empty($condition['flow_number']))
		{
			$wheres[] = "flow_number LIKE '%".$condition['flow_number']."%'";
		}
		if(!empty($condition['flow_id']))
		{
			$wheres[] = "est_flow_detail.flow_id = ".$condition['flow_id'];
		}
		$wheres[] = "est_flow_detail.node_status IN(0,1,3)";
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$this->db_read->join('est_flow_nodes', 'est_flow_nodes.node_id = est_flow_detail.node_id AND est_flow_nodes.flow_id = est_flow_detail.flow_id','LEFT');
		$total_query = $this->db_read->get('est_flow_detail');
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$this->db_read->join('est_flow_nodes', 'est_flow_nodes.node_id = est_flow_detail.node_id AND est_flow_nodes.flow_id = est_flow_detail.flow_id','LEFT');
		$data = $this->db_read->get('est_flow_detail');
		$this->db_read->flush_cache();//清除缓存
		if($data)
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user_result as $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
			//流程信息
			$this->load->model("flow_model");
			$flow_info = $this->flow_model->get_all_flow_name();

			$i = 0;
			foreach($data->result_array() AS $value)
			{
				$value['flow_name'] = empty($flow_info[$value["flow_id"]]) ? "" : $flow_info[$value["flow_id"]];
				$value["node_start_time"] = $value["node_start_time"] ? date("Y-m-d H:i:s",$value["node_start_time"]) : ""; //创建时间
				$value["node_end_time"] = $value["node_end_time"] ? date("Y-m-d H:i:s",$value["node_end_time"]) : ""; //
				$value["node_over_time"] = $value["node_over_time"] ? date("Y-m-d H:i:s",$value["node_over_time"]) : ""; //修改时间
				$value["deal_user_name"]   = empty($user_info[$value["deal_user_id"]]) ? "" : $user_info[$value["deal_user_id"]];
				$value["node_status"]   = !isset($FLOWNODE_STATUS[$value["node_status"]]) ? "" : $FLOWNODE_STATUS[$value["node_status"]];

				$role_type = $this->session->userdata("role_type");
				if($role_type == DATA_DEPARTMENT)
				{
					$responce -> rows[$i] = $value;
					$i++;
				}
				else
				{
					//权限判断
					$participant_type = isset($value['participant_type']) ? $value['participant_type'] : '';
					$node_participant = isset($value['node_participant']) ? explode(',',$value['node_participant']) : '';
					$node_limit_result = $this->check_node_limit($participant_type,$node_participant);
					if($node_limit_result)
					{
						$responce -> rows[$i] = $value;
						$i++;
					}
				}
			}
		}
		return $responce;
	}

	/**
	 * 获取已完成工单列表数据
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param string $sort
	 * @param string $order
	 * @return mixed
	 */
	public function get_work_flow_complete_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = array();
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		if(!empty($condition['flow_number']))
		{
			$wheres[] = "flow_number LIKE '%".$condition['flow_number']."%'";
		}
		if(!empty($condition['flow_id']))
		{
			$wheres[] = "flow_id = ".$condition['flow_id'];
		}
		if(!empty($condition['user_id']))
		{
			$wheres[] = "deal_user_id IN(".implode(',',$condition['user_id']).")";
		}
		if(isset($condition['start_time'])&&!empty($condition['start_time']))
		{
			$wheres[] = "node_end_time >= '".strtotime($condition['start_time'])."'";
		}

		if(isset($condition['end_time'])&&!empty($condition['end_time']))
		{
			$wheres[] = "node_end_time <= '".strtotime($condition['end_time'])."'";
		}

		$wheres[] = "node_status IN(2,4)";
		$role_type = $this->session->userdata("role_type");
		if($role_type == DATA_DEPARTMENT)
		{
			$wheres[] = data_permission();//数据权限
		}
		else
		{
			$user_id = $this->session->userdata('user_id');
			$wheres[] = "deal_user_id = ".$user_id;
		}
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_flow_detail');
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$data = $this->db_read->get('est_flow_detail');
		$this->db_read->flush_cache();//清除缓存
		if($data)
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user_result as $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
			//流程信息
			$this->load->model("flow_model");
			$flow_info = $this->flow_model->get_all_flow_name();
			//节点信息
			$node_info = $this->flow_model->get_all_node_info();

			foreach($data->result_array() AS $i=>$value)
			{
				$value['flow_name'] = empty($flow_info[$value["flow_id"]]) ? "" : $flow_info[$value["flow_id"]];
				$value['node_name'] = empty($node_info[$value["node_id"]]['node_name']) ? "" : $node_info[$value["node_id"]]['node_name'];
				$value["node_start_time"] = $value["node_start_time"] ? date("Y-m-d H:i:s",$value["node_start_time"]) : ""; //创建时间
				$value["node_end_time"] = $value["node_end_time"] ? date("Y-m-d H:i:s",$value["node_end_time"]) : ""; //修改时间
				$value["node_over_time"] = $value["node_over_time"] ? date("Y-m-d H:i:s",$value["node_over_time"]) : ""; //修改时间
				$value["deal_user_name"]   = empty($user_info[$value["deal_user_id"]]) ? "" : $user_info[$value["deal_user_id"]];
				$responce -> rows[$i] = $value;
			}
		}
		return $responce;
	}

	/**
	 * 业务受理中工单列表数据
	 *
     * @param array $condition
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $order
     * @return mixed
	 */
	public function get_work_flow_client($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $FLOWNODE_STATUS;
		$wheres = array();
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		if(isset($condition['cle_id']))
		{
			$wheres[] = "cle_id = ".$condition['cle_id'];
		}
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_flow_detail');
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$data = $this->db_read->get('est_flow_detail');
		$this->db_read->flush_cache();//清除缓存
		if($data)
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user_result as $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
			//流程信息
			$this->load->model("flow_model");
			$flow_info = $this->flow_model->get_all_flow_name();
			//节点信息
			$node_info = $this->flow_model->get_all_node_info();

			foreach($data->result_array() AS $i=>$value)
			{
				$value['flow_name'] = empty($flow_info[$value["flow_id"]]) ? "" : $flow_info[$value["flow_id"]];
				$value['node_name'] = empty($node_info[$value["node_id"]]['node_name']) ? "" : $node_info[$value["node_id"]]['node_name'];
				$value["node_start_time"] = $value["node_start_time"] ? date("Y-m-d H:i:s",$value["node_start_time"]) : ""; //创建时间
				$value["node_end_time"] = $value["node_end_time"] ? date("Y-m-d H:i:s",$value["node_end_time"]) : ""; //
				$value["node_over_time"] = $value["node_over_time"] ? date("Y-m-d H:i:s",$value["node_over_time"]) : ""; //修改时间
				$value["deal_user_name"]   = empty($user_info[$value["deal_user_id"]]) ? "" : $user_info[$value["deal_user_id"]];
				$value["node_status"]   = !isset($FLOWNODE_STATUS[$value["node_status"]]) ? "" : $FLOWNODE_STATUS[$value["node_status"]];

				$responce -> rows[$i] = $value;
			}
		}
		return $responce;
	}

	/**
	 * 生成新流程编号
	 * 编号的标准是 年月日0000 比如：201210080001
	 */
	public function new_flow_number()
	{
		//判断lock文件夹是否存在
        $this->config->load('myconfig');
        $cfg = $this->config->config;
        $lock_dir = isset($cfg['lock_path']) ? $cfg['lock_path'] : './public/lock/';
		if (!file_exists($lock_dir))
		{
			@mkdir($lock_dir, 0777);
			@chmod($lock_dir, 0777);
		}

		$vcc_id = $this->session->userdata('vcc_id');
		$type_name = 'flow'.$vcc_id;
		$count_file = $lock_dir.$type_name;//计数文件
		$lock_file     = $lock_dir.$type_name.'.lck';//锁文件
		//判断临时文件有没有生成没有的话创建临时文件
		if ( ! file_exists($count_file))
		{
			file_put_contents($count_file,0);
			@chmod($count_file, 0777);
		}

        if (!file_exists($lock_file)) {
            touch($lock_file);
            @chmod($lock_file, 0777);
        } elseif (!is_writable($lock_file)) {
            @chmod($lock_file, 0777);
        }
		$fp = fopen($lock_file, "w+");
		$count_num = '';
		// 执行文件锁定
		if (flock($fp, LOCK_EX))
		{
			//取得文件修改时间如果不是今天那么从新计数
			if(filemtime($count_file) < strtotime(date('Y-m-d')))
			{
				$count_num = 1;
			}
			else
			{
				//读取文件
				$str = file_get_contents($count_file);
				$count_num = intval($str);
				if(is_int($count_num)&&$count_num>0)
				{
					$count_num += 1;
				}
				else
				{
					$count_num = 1;
				}
			}
			file_put_contents($count_file,$count_num);
			flock($fp, LOCK_UN); // 释放锁定
		}
		fclose($fp);
		$serial_number = date('Ymd').sprintf('%04d',$count_num);
		return $serial_number;
	}


	/**
     * 修改前节点html中id和name
     *
     * @param string $form_html
     * @param int $node_id
     * @return array
     */
	public function change_html_id_name($form_html='',$node_id=0)
	{
		if(empty($form_html)||empty($node_id))
		{
			return false;
		}
		require_once('./vendor/simple_html_dom/simple_html_dom.php');
		$html   = str_get_html($form_html);
		$fields = array();
		$control_group = $html->find('div[class=control-group]');
		foreach($control_group AS $div)
		{
			switch($div->type)
			{
				case 'input':
					$name = $div->find('div[class=controls] input',0)->name;//输入框
					$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
					break;
				case 'textarea':
					$name = $div->find('div[class=controls] textarea',0)->name;//文本框
					$div->find('div[class=controls] textarea',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] textarea',0)->id = $name."_".$node_id;
					break;
				case 'checkbox':
					$name = $div->find('div[class=controls] input',0)->name;//多选框
					$name = explode('[]',$name);
					foreach($div->find('div[class=controls] input') as $value)
					{
						$value->name = $name[0]."_".$node_id."[]";
					}
					break;
				case 'radio':
					$name = $div->find('div[class=controls] input',0)->name;//单选框
					foreach($div->find('div[class=controls] input') as $value)
					{
						$value->name = $name."_".$node_id;
					}
					break;
				case 'select':
					$name = $div->find('div[class=controls] select',0)->name;//下拉框
					$div->find('div[class=controls] select',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] select',0)->id = $name."_".$node_id;
					break;
				case 'cascade':
					$name1 = $div->find('div[class=controls] select',0)->name;
					$div->find('div[class=controls] select',0)->name = $name1."_".$node_id;
					$div->find('div[class=controls] select',0)->id = $name1."_".$node_id;
					$name2 = $div->find('div[class=controls] select',1)->name;
					$div->find('div[class=controls] select',1)->name = $name2."_".$node_id;
					$div->find('div[class=controls] select',1)->id = $name2."_".$node_id;
					$series = $div->series;
					if($series == 3)
					{
						$name3 = $div->find('div[class=controls] select',2)->name;
						$div->find('div[class=controls] select',2)->name = $name3."_".$node_id;
						$div->find('div[class=controls] select',2)->id = $name3."_".$node_id;
					}
					break;
				case 'date':
					$name = $div->find('div[class=controls] input',0)->name;//日期框
					$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
					$div->find('div[class=controls] button',0)->inputid = $name."_".$node_id;
					break;
				case 'attachment'://附件框
				case 'name'://姓名
				$name = $div->find('div[class=controls] input',0)->name;
				$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
				$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
				break;
				case 'mobile':
					$name = $div->find('div[class=controls] input',0)->name;
					$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
					$div->find('div[class=controls] button',0)->telinput = $name."_".$node_id;
					break;
				case 'phone':
					$name = $div->find('div[class=controls] input',0)->name;
					$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
					$div->find('div[class=controls] button',0)->telinput = $name."_".$node_id;
					break;
				case 'gender':
					$name = $div->find('div[class=controls] input',0)->name;//性别
					foreach($div->find('div[class=controls] input') as $value)
					{
						$value->name = $name."_".$node_id;
					}
					break;
				case 'address':
					$name = $div->find('div[class=controls] input',0)->name;//地址
					$div->find('div[class=controls] input',0)->name = $name."_".$node_id;
					$div->find('div[class=controls] input',0)->id = $name."_".$node_id;
					$usessq = 0;
					if($div->hasAttribute('usessq'))
					{
						$usessq = $div->usessq;
					}
					if($usessq == 1)
					{
						$province = $div->find('div[class=controls] select',0)->name;
						$div->find('div[class=controls] select',0)->name = $province."_".$node_id;//省
						$div->find('div[class=controls] select',0)->id = $province."_".$node_id;
						$city = $div->find('div[class=controls] select',1)->name;
						$div->find('div[class=controls] select',1)->name = $city."_".$node_id;//市
						$div->find('div[class=controls] select',1)->id = $city."_".$node_id;
						$town = $div->find('div[class=controls] select',2)->name;
						$div->find('div[class=controls] select',2)->name = $town."_".$node_id;//区
						$div->find('div[class=controls] select',2)->id = $town."_".$node_id;
					}
					break;
			}
		}
		$new_html = $html->save();
		$html->clear();
		return array('html' => $new_html);
	}

	/**
	 * 获取工单列表数据
     * @param array $condition
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $order
     * @return stdClass
     */
	public function get_all_work_flow_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $WORKFLOW_STATUS;
		$wheres = array();
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();
		$tablename = '';
		$flow_id = 0;

		if(isset($condition['flow_id']))
		{
			$flow_id = $condition['flow_id'];
			$tablename = 'est_flow_info'.$condition['flow_id'];
		}

		//判断表是否存在
		if(!$this->db_read->table_exists($tablename))
		{
			$responce->error = '数据库表【'.$tablename.'】不存在';

			return $responce;
		}

		if(isset($condition['start_time'])&&!empty($condition['start_time']))
		{
			$wheres[] = "flow_start_time >= '".strtotime($condition['start_time'])."'";
		}

		if(isset($condition['end_time'])&&!empty($condition['end_time']))
		{
			$wheres[] = "flow_start_time <= '".strtotime($condition['end_time'])."'";
		}
		if(isset($condition['cle_phone'])&&!empty($condition['cle_phone']))
        {
            $condition['cle_phone'] = trim($condition['cle_phone']);
            $wheres[] = "cle_phone LIKE '%".$condition['cle_phone']."%' OR cle_phone2  LIKE '%".$condition['cle_phone']."%' OR cle_phone3  LIKE '%".$condition['cle_phone']."%'";
        }

		$where = implode(" AND ",$wheres);
		if(!empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		if(isset($condition['cle_phone'])&&!empty($condition['cle_phone']))
        {
            $this->db_read->join('est_client', $tablename.'.cle_id = est_client.cle_id ','LEFT');
        }
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get($tablename);
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if(!empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		if(isset($condition['cle_phone'])&&!empty($condition['cle_phone']))
        {
            $this->db_read->join('est_client', $tablename.'.cle_id = est_client.cle_id ','LEFT');
        }
		$data = $this->db_read->get($tablename);
		$this->db_read->flush_cache();//清除缓存
		if($data)
		{
			//员工信息
			$user_info = array();
			$this->load->model('user_model');
			$user_array = $this->user_model->get_all_users_without_dept();
			foreach($user_array as $user)
			{
				$user_info[$user['user_id']] = $user['user_name'];
			}

			//查询所有的流程节点
			$this->load->model('flow_model');
			$nodes = $this->flow_model->get_all_nodes_by_flow_id($flow_id);
			$this->load->model('form_model');
			$cascade = array();
			$forms = array();
			foreach($nodes as $node_id=>$node)
			{
				$form_id = empty($node['form_id']) ? 0 : $node['form_id'];
				$forms[$node_id] = $form_id;
				$cascade[$form_id] = $this->form_model->get_echo_form_cascade_name($form_id);
			}

			//加载客户模块
			$this->load->model('client_model');

			foreach($data->result_array() AS $i=>$value)
			{
				$cle_id = $value['cle_id'];
				$cle_info = $this->client_model->get_client_info($cle_id);
				$value['cle_name'] = isset($cle_info['cle_name']) ? $cle_info['cle_name'] : '不存在';
				$value['cle_phone'] = isset($cle_info['cle_phone']) ? $cle_info['cle_phone'] : '';
				$value['cle_province_name'] = isset($cle_info['cle_province_name']) ? $cle_info['cle_province_name'] : '';
				$value['cle_city_name'] = isset($cle_info['cle_city_name']) ? $cle_info['cle_city_name'] : '';
				$value['flow_status'] = isset($WORKFLOW_STATUS[$value['flow_status']]) ? $WORKFLOW_STATUS[$value['flow_status']] : '未定义';
				$value['flow_start_time'] = empty($value['flow_start_time']) ? '' : date('Y-m-d H:i:s', $value['flow_start_time']);

				//查询表单详细信息
				$form_data = array();
				foreach($forms as $nid=>$form)
				{
					$this_node_form_data = $this->get_form_value_info($form,$flow_id,$value['flow_info_id'],$nid);
					if(!empty($this_node_form_data))
					{
						$this_node_form_data['user_name'.$form.'_'.$nid] = empty($user_info[$this_node_form_data['create_user_id_'.$nid]])?'':$user_info[$this_node_form_data['create_user_id_'.$nid]];
						foreach($this_node_form_data as $key=>$v)
						{
							$key_value = explode('_',$key);
							if(count($key_value)==3 && !empty($v))
							{
								$cascade_key = $key_value[0].'_'.$key_value[1];
								if(isset($cascade[$form][$cascade_key]) && !empty($cascade[$form][$cascade_key][$v]))
								{
									$this_node_form_data[$key] = $cascade[$form][$cascade_key][$v];
								}
							}
						}
						$form_data = array_merge($form_data,$this_node_form_data);
					}
				}
				$value = array_merge($value, $form_data);
				$responce -> rows[$i] = $value;
			}
		}
		return $responce;
	}

	/**
     * @param string $to_charset
     * @param string $from_charset
     * @param string $string
     * @return string
     */
	public function convertEncoding($to_charset, $from_charset, $string)
	{
		if (function_exists('mb_convert_encoding')) {
			$string = mb_convert_encoding($string, $to_charset, $from_charset);
		} else {
			$string = iconv($from_charset, $to_charset."//TRANSLIT", $string);
		}

		return $string;
	}

	/**
	 * 获取工单数据并导出工单
	 *
	 * @param int $flow_id
	 * @param string $start_time
	 * @param string $end_time
	 */
	public function export_over_work_flow($flow_id,$start_time='',$end_time='')
	{
		set_time_limit(0);
		@ini_set('memory_limit', '1024M');
		if(empty($flow_id))
		{
			die('流程id不能为空');
		}
        $tablename = "est_flow_info" . $flow_id;
		global $WORKFLOW_STATUS;
		$wheres = array();
		if(!empty($start_time))
		{
			$wheres[] = "flow_start_time >= '".strtotime($start_time)."'";
		}
		if(!empty($end_time))
		{
			$wheres[] = "flow_start_time <= '".strtotime($end_time)."'";
		}
        if (!empty($wheres))
        {
            $where = implode(" AND ",$wheres);
            $this->db_read->where($where);
        }
		$query = $this->db_read->get($tablename);
		$data = $query->result_array();

        //员工信息
        $user_info = array();
        $this->load->model('user_model');
        $user_array = $this->user_model->get_all_users_without_dept();
        foreach($user_array as $user)
        {
            $user_info[$user['user_id']] = $user['user_name'];
        }
        $title = array('流程名称','流程编号','客户姓名','客户电话','所属省','所属市','流程状态','创建时间');
        //查询所有的流程节点
        $this->load->model('flow_model');
        $nodes = $this->flow_model->get_all_nodes_by_flow_id($flow_id);
        $this->load->model('form_model');
        $cascade = array();
        $forms = array();
        $fields = array();
        foreach($nodes as $node_id=>$node)
        {
            $form_id = empty($node['form_id']) ? 0 : $node['form_id'];
            $forms[$node_id] = $form_id;
            $cascade[$form_id] = $this->form_model->get_echo_form_cascade_name($form_id);
            $form_fields = $this->form_model->get_form_fields_info($form_id);
            foreach($form_fields as $field)
            {
                array_push($title, $field['field_name']);
                $fields[] = $field['fields'].'_'.$node_id;
            }
            $fields[] = 'create_user_id_'.$node_id;
            array_push($title,'操作人');
            $fields[] = 'node'.$node_id.'_description';
            array_push($title,'追加说明');
        }
        $content = '"' . implode('","', $title) . "\"\n";
        $content = $this->convertEncoding('GBK', 'UTF-8', $content);
        if(count($data)>0)
        {
			$flow_name = $this->flow_model->get_flow_name_by_flow_id($flow_id); //获取流程名称
			$this->load->library("json");
            $this->load->model('client_model');
			foreach($data AS $i => $value)
			{
				$result = array();
				$result[] = $flow_name;
				$result[] = isset($value['flow_number']) ? $value['flow_number']: '';
                $cle_info = $this->client_model->get_client_info($value['cle_id']);
				$result[] = isset($cle_info['cle_name']) ? $cle_info['cle_name'] : '不存在';
				$result[] = isset($cle_info['cle_phone']) ? $cle_info['cle_phone'] : '';
				$result[] = isset($cle_info['cle_province_name']) ? $cle_info['cle_province_name'] : '';
				$result[] = isset($cle_info['cle_city_name']) ? $cle_info['cle_city_name'] : '';
				$result[] = isset($WORKFLOW_STATUS[$value['flow_status']]) ? $WORKFLOW_STATUS[$value['flow_status']] : '未定义';
				$result[] = empty($value['flow_start_time']) ? '' : date('Y-m-d H:i:s', $value['flow_start_time']);
                $form_data = array();
                //查询表单详细信息
                foreach($forms as $nid=>$form)
                {
                    $this_node_form_data = $this->get_form_value_info($form,$flow_id,$value['flow_info_id'],$nid);
                    if(!empty($this_node_form_data))
                    {
                        $this_node_form_data['create_user_id_'.$nid] = empty($user_info[$this_node_form_data['create_user_id_'.$nid]])?'':$user_info[$this_node_form_data['create_user_id_'.$nid]];
                        foreach($this_node_form_data as $key=>$v)
                        {
                            $key_value = explode('_',$key);
                            if(count($key_value)==3 && !empty($v))
                            {
                                $cascade_key = $key_value[0].'_'.$key_value[1];
                                if(isset($cascade[$form][$cascade_key]) && !empty($cascade[$form][$cascade_key][$v]))
                                {
                                    $this_node_form_data[$key] = $cascade[$form][$cascade_key][$v];
                                }
                            }
                        }
                        $form_data = array_merge($form_data, $this_node_form_data);
                    }
                }
                foreach ($fields as $key1=>$val1) {
                        $result[] = isset($form_data[$val1]) ? $form_data[$val1] : '';
                }
				$orgin_content = implode('","', $result);
				$content .= '"' . $this->convertEncoding('GBK', 'UTF-8', $orgin_content) . "\"\r\n";
			}
        }
        unset($title);
        unset($result);
        $filename = "workflow.csv";
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream;charset:gbk");
        header("Cache-control: private");
        die($content);
	}

    /**
     * 根据节点参与类型及参与人，判断当前坐席是否可操作节点
     *
     * @param integer $participant_type
     * @param array $node_participant
     * @return bool
     */
	public function check_node_limit($participant_type,$node_participant)
	{
        $node_participant = empty($node_participant) ? array() : $node_participant;
		switch($participant_type)
		{
			case 1:
				$user_id = $this->session->userdata("user_id");
				if(!in_array($user_id,$node_participant))
				{
					return FALSE;
				}
				break;
			case 2:
				$dept_id = $this->session->userdata("dept_id");
				if(!in_array($dept_id,$node_participant))
				{
					return FALSE;
				}
				break;
			case 3:
				$role_id = $this->session->userdata("role_id");
				if(!in_array($role_id,$node_participant))
				{
					return FALSE;
				}
				break;
			default:
				break;
		}
		return TRUE;
	}
}