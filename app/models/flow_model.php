<?php
class Flow_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除缓存
	 *
	 * @param array $where
	 * @param int $user_id 员工id
	 * @return bool
	 */
	private function _clear_flow_cache($flow_id=0)
	{
		$this->cache->delete('all_flow');
		$this->cache->delete('all_node_info');
		if(!empty($flow_id))
		{
			$this->cache->delete('flow_info'.$flow_id);
			$this->cache->delete('flow_all_nodes'.$flow_id);
			$query = $this->db_read->query("SELECT node_id FROM est_flow_nodes WHERE flow_id=".$flow_id);
			foreach ($query->result_array() as $node)
			{
				$this->cache->delete('node_info'.$node['node_id']);
			}
		}
		return true;
	}

	/**
	 * 添加流程
	 *
	 * @param array $insert
	 * @return boolean|integer
	 */
	public function insert_flow($insert=array())
	{
		if(empty($insert))
		{
			return false;
		}

		$this->db_write->trans_start();

		$data = array();
		foreach (array('flow_name','flow_description','flow_status','valid_from','valid_to') as $key)
		{
			$data[$key] = empty($insert[$key])?'':$insert[$key];
		}
		$data['create_user_id'] = $data['update_user_id'] = $this->session->userdata('user_id');
		$data['create_time'] = $data['update_time'] = time();
		$this->db_write->insert("est_flow",$data);
		$flow_id = $this->db_write->insert_id();

		//添加节点
		$node_field = array();
		if(!empty($insert['node_info']))
		{
			$node_info = $insert['node_info'];
			$action = array();
			$change_nodeid = array();
			foreach($node_info as $k=>$v)
			{
				$nodes = array(
				'flow_id'=>$flow_id,
				'node_name'=>$v['node_name'],
				'form_id'=>$v['form_id'],
				'node_use_time'=>$v['node_use_time'],
				'participant_type'=>$v['participant_type'],
				'node_participant'=>empty($v['node_participant'])?'':implode(',',$v['node_participant'])
				);
				$this->db_write->insert('est_flow_nodes', $nodes);
				$node_id = $this->db_write->insert_id();
				//动作处理
				$change_nodeid[$v['node_id']] = $node_id;
				if(!empty($v['action']))
				{
					$action[$node_id] = $v['action'];
				}
				//流程信息表记录字段信息
				$node_field[] = " `node".$node_id."` int NOT NULL COMMENT '节点".$node_id."'";
				$node_field[] = " `node".$node_id."_start_time` int NOT NULL COMMENT '节点".$node_id."开始时间'";
				$node_field[] = " `node".$node_id."_end_time` int NOT NULL COMMENT '节点".$node_id."结束时间'";
				$node_field[] = " `node".$node_id."_over_time` int NOT NULL COMMENT '节点".$node_id."超时时间'";
				$node_field[] = " `node".$node_id."_user_id` int NOT NULL COMMENT '节点".$node_id."处理人id'";
				$node_field[] = " `node".$node_id."_status` tinyint(1) NOT NULL COMMENT '节点".$node_id."状态(0未开始1运行中2正常完成3退回4超时完成)'";
				$node_field[] = " `node".$node_id."_remark` varchar(200) NOT NULL COMMENT '节点".$node_id."备注'";
				$node_field[] = " `node".$node_id."_description` varchar(500) NOT NULL COMMENT '节点".$node_id."追加说明'";
			}

			//修改action中jump(即节点id) 动作处理
			if(!empty($action))
			{
				$update_action = array();
				foreach($action as $nid=>$act)
				{
					foreach($act as $k=>$a)
					{
						if($a['jump']!='结束')
						{
							$act[$k]['jump'] = empty($change_nodeid[$a['jump']])?'':$change_nodeid[$a['jump']];
						}
					}
					$update_action[$nid] = array('node_id'=>$nid,'action'=>$this->json->encode($act));
				}
				if(!empty($update_action))
				{
					$this->db_write->update_batch("est_flow_nodes",$update_action,"node_id");
				}
			}
		}
		//创建流程信息记录表
		$sql  = "CREATE TABLE IF NOT EXISTS `est_flow_info".$flow_id."` (`flow_info_id` int(11) NOT NULL auto_increment PRIMARY KEY,";
		$sql .= " `flow_number` varchar(30) NOT NULL COMMENT '流程编号',";
		$sql .= " `cle_id` int NOT NULL default 0 COMMENT '客户id',";
		$sql .= " `flow_status` tinyint NOT NULL default 0 COMMENT '工单状态（0未开始1运行中2已完成3已结束4已删除）',";
		$sql .= " `flow_start_time` int NOT NULL default 0 COMMENT '工单开始时间',";
		$sql .= " `flow_end_time` int NOT NULL default 0 COMMENT '工单结束时间',";
		$sql .= " `flow_fag` tinyint(1) NOT NULL default 0 COMMENT '工单整理标记位0未整理或未完成的工单1已整理完成的工单',";
		if(!empty($node_field))
		{
			$sql .= implode(",", $node_field).',';
		}
		$sql .= " KEY `flow_number` (`flow_number`),";
		$sql .= " KEY `flow_fag` (`flow_fag`),";
		$sql .= " KEY `flow_start_time` (`flow_start_time`)";
		$sql .= ") ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='流程信息记录".$flow_id."' AUTO_INCREMENT=1 ;";
		$this->db_write->query($sql);

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			$this->_clear_flow_cache();//清除缓存
			return $flow_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 修改流程
	 *
	 * @param int $flow_id
	 * @param array $update
	 * @return boolean|int
	 */
	public function update_flow($flow_id=0,$update=array())
	{
		if(empty($flow_id) || empty($update))
		{
			return false;
		}
		$this->db_write->trans_start();

		$data = array();
		foreach (array('flow_name','flow_description','flow_status','valid_from','valid_to') as $key)
		{
			$data[$key] = empty($update[$key])?'':$update[$key];
		}
		$data['update_user_id'] = $this->session->userdata('user_id');
		$data['update_time'] = time();
		$this->db_write->where("flow_id",$flow_id);
		$this->db_write->update("est_flow",$data);

		$flow_info_table = 'est_flow_info'.$flow_id; //流程信息记录表

		if(!empty($update['node_info']))
		{
			$this->load->library("json");
			//节点表增加
			$change_nodeid = array();
			if(!empty($update['addNodeId']))
			{
				$update['addNodeId'] = $this->json->decode($update['addNodeId'],1);
				$action = array();
				foreach($update['addNodeId'] as $add_key=>$add_node)
				{
					if(!empty($update['node_info'][$add_node]))
					{
						$nodes = array(
						'flow_id'=>$flow_id,
						'node_name'=>$update['node_info'][$add_node]['node_name'],
						'form_id'=>$update['node_info'][$add_node]['form_id'],
						'node_use_time'=>$update['node_info'][$add_node]['node_use_time'],
						'participant_type'=>$update['node_info'][$add_node]['participant_type'],
						'node_participant'=>empty($update['node_info'][$add_node]['node_participant']) ?
						'':implode(',',$update['node_info'][$add_node]['node_participant']),
						'action'=>$this->json->encode($update['node_info'][$add_node]['action'])
						);
						$this->db_write->insert('est_flow_nodes', $nodes);
						$node_id = $this->db_write->insert_id();
						//动作处理
						$change_nodeid[$add_node] = $node_id;
						if(!empty($update['node_info'][$add_node]['action']))
						{
							$action[$node_id] = $update['node_info'][$add_node]['action'];
						}
						//流程信息表记录字段信息
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."` int NOT NULL COMMENT '节点".$node_id."'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_start_time` int NOT NULL COMMENT '节点".$node_id."开始时间'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_end_time` int NOT NULL COMMENT '节点".$node_id."结束时间'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_over_time` int NOT NULL COMMENT '节点".$node_id."超时时间'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_user_id` int NOT NULL COMMENT '节点".$node_id."处理人id'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_status` tinyint(1) NOT NULL COMMENT '节点".$node_id."状态(0未开始1运行中2正常完成3退回4超时完成)'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_remark` varchar(200) NOT NULL COMMENT '节点".$node_id."备注'");
						$this->db_write->query("ALTER TABLE $flow_info_table ADD `node".$node_id."_description` varchar(500) NOT NULL COMMENT '节点".$node_id."追加说明'");

						unset($update['node_info'][$add_node]);
					}
				}
				//动作处理
				if(!empty($action))
				{
					$update_action = array();
					foreach($action as $nid=>$act)
					{
						foreach($act as $k=>$a)
						{
							if($a['jump']!='结束'&&!is_numeric($a['jump']))
							{
								$act[$k]['jump'] = empty($change_nodeid[$a['jump']])?'':$change_nodeid[$a['jump']];
							}
						}
						$update_action[$nid] = array('node_id'=>$nid,'action'=>$this->json->encode($act));
					}
					if(!empty($update_action))
					{
						$this->db_write->update_batch("est_flow_nodes",$update_action,"node_id");
					}
				}
			}
			//节点删除
			if(!empty($update['delNodeId']))
			{
				$update['delNodeId'] = $this->json->decode($update['delNodeId'],1);
				foreach($update['delNodeId'] as $node)
				{
					$this->db_write->where('flow_id', $flow_id);
					$this->db_write->where('node_id', $node);
					$this->db_write->delete('est_flow_nodes');
					if(isset($update['node_info'][$node]))
					{
						unset($update['node_info'][$node]);
					}
				}
			}
			//节点修改
			$nodes_to_update = array();
			foreach($update['node_info'] AS $id => $value)
			{
				if(isset($value['action']))
				{
					foreach($value['action'] as $akey=>$avalue)
					{
						if($avalue['jump']!='结束'&&!is_numeric($avalue['jump']))
						{
							$value['action'][$akey]['jump'] = empty($change_nodeid[$avalue['jump']])?'':$change_nodeid[$avalue['jump']];
						}
					}
					$value['action'] = $this->json->encode($value['action']);
				}
				if(isset($value['node_participant']))
				{
					$value['node_participant'] = empty($value['node_participant'])?'':implode(',',$value['node_participant']);
				}
				$nodes_to_update[$id] = $value;

			}
			if(!empty($nodes_to_update))
			{
				$this->db_write->update_batch("est_flow_nodes",$nodes_to_update,"node_id");
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			$this->_clear_flow_cache($flow_id);//清除缓存
			return $flow_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除流程
	 *
	 * @param int $flow_id
	 * @return boolean|mixed
	 */
	public function delete_flow($flow_id=0)
	{
		if (empty($flow_id))
		{
			return FALSE;
		}
		$flow_ids = array();
		if(is_array($flow_id))
		{
			foreach ($flow_id as $item)
			{
				$flow_ids[] = (int)$item;
			}
		}
		else
		{
			$flow_ids = array($flow_id);
		}
		$this->db_write->trans_start();//使用事务
		$this->db_write->where_in('flow_id',$flow_ids);
		$this->db_write->delete(array('est_flow','est_flow_nodes','est_flow_detail'));
		foreach($flow_ids AS $id)
		{
			if(!empty($id))
			{
				$table = 'est_flow_info'.$id;
				if($this->db_read->table_exists($table))
				{
					$this->db_write->query("DROP TABLE $table");
				}
				$this->_clear_flow_cache($id);//清除缓存
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
	 * 获取流程列表数据
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param string $sort
	 * @param string $order
	 * @return object
	 */
	public function get_flow_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $FLOW_STATUS;
		$wheres = array();
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		if(isset($condition['flow_name']))
		{
			$wheres[] = "flow_name LIKE '%".$condition['flow_name']."%'";
		}
		if(isset($condition['flow_status']))
		{
			$wheres[] = "flow_status = '".$condition['flow_status']."'";
		}
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_flow');
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
		$this->db_read->select('flow_id,flow_name,flow_description,flow_status,valid_from,valid_to,create_user_id,create_time,update_user_id,update_time');
		$data = $this->db_read->get('est_flow');
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

			foreach($data->result_array() AS $i=>$value)
			{
				$value["create_time"] = $value["create_time"] ? date("Y-m-d H:i:s",$value["create_time"]) : ""; //创建时间
				$value["create_user_name"]   = empty($user_info[$value["create_user_id"]]) ? "" : $user_info[$value["create_user_id"]];
				$value["update_time"] = $value["update_time"] ? date("Y-m-d H:i:s",$value["update_time"]) : ""; //修改时间
				$value["update_user_name"]   = empty($user_info[$value["update_user_id"]]) ? "" : $user_info[$value["update_user_id"]];
				$value["flow_status"]   = !isset($FLOW_STATUS[$value["flow_status"]]) ? "" : $FLOW_STATUS[$value["flow_status"]];

				$responce -> rows[$i] = $value;
			}
		}
		return $responce;
	}

	/**
	 * 获取所有流程信息
	 */
	public function get_all_flow()
	{
		if( !$this->cache->get('all_flow'))
		{
			$flows = array();
			$this->db_read->select('flow_id,flow_name,flow_status');
            $this->db_read->order_by('update_time','desc');
			$query = $this->db_read->get('est_flow');
			$flows = $query->result_array();
			$this->cache->save('all_flow',$flows,600);
		}
		else
		{
			$flows = $this->cache->get('all_flow');
		}
		return $flows;
	}

	/**
	 * 获取所有流程名称
	 *
	 * @return array
	 */
	public function get_all_flow_name()
	{
		$flow_names = array();
		$flows = $this->get_all_flow();
		foreach($flows as $flow)
		{
			$flow_names[$flow['flow_id']] = $flow['flow_name'];
		}
		return $flow_names;
	}

	/**
	 * 获取所有节点信息
	 */
	public function get_all_node_info()
	{
		if( ! $this->cache->get('all_node_info'))
		{
			$this->db_read->select('flow_id,node_id,node_name,node_use_time');
			$query = $this->db_read->get('est_flow_nodes');
			$node_names = array();
			foreach($query->result_array() as $node)
			{
				$node_names[$node['node_id']] = $node;
			}
			$this->cache->save('all_node_info',$node_names,600);
		}
		else
		{
			$node_names = $this->cache->get('all_node_info');
		}
		return $node_names;
	}

	/**
	 * 获取一节点信息
	 *
	 * @param int $node_id
	 * @return array
	 */
	public function get_node_info($node_id=0)
	{
		if(empty($node_id))
		{
			return false;
		}
		if( ! $this->cache->get('node_info'.$node_id))
		{
			$node_info = array();
			$this->db_read->where('node_id',$node_id);
			$query = $this->db_read->get('est_flow_nodes');
			$node_info = $query->row_array();
			$this->cache->save('node_info'.$node_id,$node_info,600);
		}
		else
		{
			$node_info = $this->cache->get('node_info'.$node_id);
		}
		return $node_info;
	}

	/**
	 * 获取一流程信息(包括底下节点信息)
	 *
	 * @param int $flow_id
	 * @return boolean|mixed
	 */
	public function get_flow_info($flow_id=0)
	{
		if(empty($flow_id))
		{
			return false;
		}
		if( ! $this->cache->get('flow_info'.$flow_id))
		{
			//流程信息
			$this->db_read->where('flow_id',$flow_id);
			$query = $this->db_read->get('est_flow');
			$flow_info = $query->row_array();
			//流程节点信息
			$node_info = $this->get_all_nodes_by_flow_id($flow_id);
			$this->load->library("json");
			foreach($node_info as $node_id=>$node)
			{
				if(!empty($node['node_participant']))
				{
					$node['node_participant'] = explode(',',$node['node_participant']);
				}
				$node['action'] = $this->json->decode($node['action'],1);
				$node_info[$node_id] = $node;
			}
			$flow_info['node_info'] = $this->json->encode($node_info);
			$this->cache->save('flow_info'.$flow_id,$flow_info,600);
		}
		else
		{
			$flow_info = $this->cache->get('flow_info'.$flow_id);
		}
		return $flow_info;
	}

	/**
     * 获取指定流程的节点信息
     *
     * @param int $flow_id
     * @return array|bool
     */
	public function get_all_nodes_by_flow_id($flow_id = 0)
	{
		if(empty($flow_id))
		{
			return false;
		}
		if( ! $this->cache->get('flow_all_nodes'.$flow_id))
		{
			$nodes = array();
			$this->db_read->select('node_id,flow_id,node_name,node_use_time,form_id,participant_type,node_participant,action');
			if(!empty($flow_id))
			{
				$this->db_read->where('flow_id', $flow_id);
			}
			$query = $this->db_read->get('est_flow_nodes');
			foreach($query->result_array() as $row)
			{
				$nodes[$row['node_id']] = $row;
			}
			$this->cache->save('flow_all_nodes'.$flow_id,$nodes,600);
		}
		else
		{
			$nodes = $this->cache->get('flow_all_nodes'.$flow_id);
		}

		return $nodes;
	}

	/**
     * 根据流程id获取流程名称
     * @param int $flow_id
     * @return bool
     */
	public function get_flow_name_by_flow_id($flow_id=0)
	{
		if(empty($flow_id))
		{
			return false;
		}
		$this->db_read->where('flow_id',$flow_id);
		$this->db_read->select('flow_name');
		$query =$this->db_read->get('est_flow');
		return $query->row()->flow_name;
	}
}