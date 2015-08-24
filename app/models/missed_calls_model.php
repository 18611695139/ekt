<?php
class Missed_calls_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 列表排序 - 得到实际字段
	 *
	 * @param string $sort 原始排序
	 * @return string 实际排序
	 * @author zgx
	 */
	private function get_real_field($sort)
	{
		$new_sort = "";
		switch ($sort) {
			case "date":{
				//来电时间
				$new_sort = "start_time";
				break;
			}
			case "dept_name":{
				$new_sort = "dept_id";
				break;
			}
			case "user_name":{
				$new_sort = "user_id";
				break;
			}
			case "miss_reason":{
				$new_sort = "reason";
				break;
			}
			default:{
				$new_sort = $sort;
				break;
			}
		}
		return $new_sort;
	}

	/**
	 * 获取检索条件
	 * 
	 * @param array $condition
	 * @return array 返回检索条件一维数组
	 * @author zgx
	 */
	private function get_missed_calls_condition($condition=array())
	{
		$wheres = array();

		if(!empty($condition['deal_date_search_start']))
		{
			$wheres[] = "start_time >= '".strtotime($condition['deal_date_search_start'])."'";
		}
		if(!empty($condition['deal_date_search_end']))
		{
			$wheres[] = "start_time <= '".strtotime($condition['deal_date_search_end'].' 23:59:59')."'";
		}
		//来电号
		if (!empty($condition["caller"]))
		{
			$wheres[] = "caller = '".$condition["caller"]."'";
		}

		/*1全部数据  2我的数据*/
		if(!empty($condition['all_type']) && ($condition['all_type'] == 2))
		{
			$wheres[] = "user_id = ".$this->session->userdata('user_id');
		}
		/*1未处理  2已处理*/
		if (!empty($condition["deal_type"]))
		{
			if ( $condition["deal_type"] == 1 )
			{
				$wheres[] = "state = 0";
			}
			elseif ( $condition["deal_type"] == 2 )
			{
				$wheres[] = "state = 1";
			}
		}
		/*1已分配   2未分配*/
		if (  !empty($condition["locate_type"]))
		{
			if ($condition["locate_type"] == 1)
			{
				$wheres[] = "user_id != 0";
			}
			elseif ($condition["locate_type"] == 2)
			{
				$wheres[] = "user_id = 0";
			}
		}

		if(     isset($condition['all_type']) && $condition['all_type'] == 1 )
		{
			$wheres[] = "(".data_permission()." OR dept_id =0)";
		}
		else
		{
			$wheres[] = data_permission();
		}

		return array_unique($wheres);
	}

	/**
	 * 获取未接来电列表数据
	 * 
	 * @param array $condition 检索字段信息
	 * @param int $page 第几页
	 * @param int $limit 每页显示几条
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @return object responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [id]=>10
 	 *        [reason] => 原因
	 *       	… 
	 *    )
	 *  ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_missed_calls_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_missed_calls_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_lost_cdr');
		$total = $total_query->row()->total;
		$responce = new stdClass();
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$sort = $this->get_real_field($sort);
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$cdr_data = $this->db_read->get('est_lost_cdr',$limit,$start);
		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息

		if($cdr_data)
		{
			//坐席信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result AS $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
			//部门信息
			$this->load->model("department_model");
			$dept_result = $this->department_model->get_all_department();
			$dept_info   = array();
			foreach ($dept_result AS $value)
			{
				$dept_info[$value["dept_id"]] = $value["dept_name"];
			}

			foreach($cdr_data->result_array() AS $i=>$callrecord)
			{
				if($callrecord['user_id'] != 0)
				{
					$callrecord['user_name'] = empty($user_info[$callrecord['user_id']]) ? '' : $user_info[$callrecord['user_id']];
				}
				if($callrecord['dept_id'] != 0)
				{
					$callrecord['dept_name'] = empty($dept_info[$callrecord['dept_id']]) ? '' : $dept_info[$callrecord['dept_id']];
				}
				$callrecord['date'] = date('Y-m-d H:i:s',$callrecord['start_time']);

				$callrecord['miss_reason'] = $this->miss_reason_chinese($callrecord['reason']);
				$responce -> rows[$i] = $callrecord;
			}
		}
		return $responce;
	}

	/**
	 * 处理未接来电
	 * @param int $id 未接来电id
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function change_missed_calls_state($id=0)
	{
		if(empty($id))
		{
			return '缺少参数';
		}
		$dept_id = $this->session->userdata('dept_id');
		$user_id = $this->session->userdata('user_id');
		$result = $this->db_write->update('est_lost_cdr',array('state'=>1,'dept_id'=>$dept_id,'user_id'=>$user_id),array('id'=>$id));
		return $result;
	}

	/**
	 * 指定分配未接来电
	 *
	 * @param int $user_id 员工id
	 * @param string $ids 未接来电id
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function missed_calls_distribution($user_id=0,$ids='')
	{
		if(empty($user_id) || empty($ids))
		{
			return false;
		}

		//坐席信息
		$this->load->model("user_model");
		$user_info = $this->user_model->get_user_info($user_id);
		$result = $this->db_write->query("UPDATE est_lost_cdr SET user_id = ".$user_id.",dept_id = ".$user_info['dept_id']." WHERE id IN (".$ids.")");
		if($result)
		{
			//给提示
			if($this->db_write->affected_rows()>0)
			{
				$this->load->model('notice_model');
				$this->notice_model->write_notice('system',$user_id,'','您有新的未接来电');
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 按坐席分组，统计总数和未处理数
	 *
	 * @param int $dept_id 部门id
	 * @return array
	 * <code>
	 * array(
	 * 		[0]=>array(
	 * 			[user_id]=> 员工id
	 * 			[data_total]=> 未接来电总数
	 * 			[data_left]=> 未处理数
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_user_static_num($dept_id = 0)
	{
		$missed_data = array();
		if(empty($dept_id))
		{
			return $missed_data;
		}
		if(is_array($dept_id))
		{
			$dept_id = implode(',',$dept_id);
		}
		//得到坐席未接来电总数、未处理数
		$missed_result = $this->db_read->query("SELECT user_id,COUNT( * ) AS data_total,SUM( IF( state = '0', 1, 0 ) ) AS data_left  FROM est_lost_cdr WHERE user_id >0 AND dept_id IN(".$dept_id.") GROUP BY user_id");
		foreach ($missed_result->result_array() AS $value)
		{
			if (!empty($value["user_id"]))
			{
				$missed_data[$value["user_id"]]["data_total"] = $value["data_total"];//坐席数据总数
				$missed_data[$value["user_id"]]["data_left"]  = $value["data_left"];//坐席未处理数据
			}
		}
		return $missed_data;
	}

	/**
	 * 部门总数、未拨打数
	 *
	 * @param int $dept_id 部门id
	 * @return array
	 * <code>
	 * array(
	 * 		[0]=>array(
	 * 			[dept_id]=> 部门id
	 * 			[data_total]=> 未接来电总数
	 * 			[data_left]=> 未处理数
	 * 			[data_leave]=> 待分配数
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_dept_static_num($dept_id = 0)
	{
		$missed_data = array();
		if(empty($dept_id))
		{
			return $missed_data;
		}
		if(is_array($dept_id))
		{
			$dept_id = implode(',',$dept_id);
		}
		$dept_result = $this->db_read->query("SELECT dept_id,COUNT( * ) AS data_total,SUM( IF( state = '0', 1, 0 ) ) AS data_left  FROM est_lost_cdr WHERE dept_id >0 AND dept_id IN(".$dept_id.") GROUP BY dept_id");
		foreach ($dept_result->result_array() AS $value)
		{
			if (!empty($value["dept_id"]))
			{
				$missed_data[$value["dept_id"]]["data_total"] = $value["data_total"];//部门数据总数
				$missed_data[$value["dept_id"]]["data_left"]  = $value["data_left"];//部门未处理数据
			}
		}
		return $missed_data;
	}

	/**
	 * 未接来电 - 批量分配 树数据
	 * 
	 * @param int $dept_id 部门id
	 * @return array
	 *  (
	 *      [0] => Array
	 *          (
	 *              [id] => 1
	 *              [pid] => 0
	 *              [text] => 华夏成讯
	 *              [attributes] => 1
	 *              [children] => Array
	 *                  (
	 *                    [0] => Array
	 *                        (
	 *                            [id] => 4
	 *                            [pid] => 1
	 *                            [text] => 研发部
	 *                           [attributes] => 2
	 *                            [data_total] => 146
	 *                            [data_left] => 136
	 *                            [data_leave] => 146
	 *                        )
	 *                 )
	 *           )
	 *   )
	 * 
	 * @author zgx
	 */
	public function deployment_batch_query($dept_id = 0 )
	{
		$dept_session_id = $this->session->userdata('dept_id');
		$this->load->model('department_model');
		$this->load->model('user_model');
		if(empty($dept_id))
		{
			$dept_id = $dept_session_id;
			$dept_parent_info = $this->department_model->get_department_info($dept_id);
			$dept_tree_arr[$dept_parent_info['dept_id']] =  new stdClass();
			$dept_tree_arr[$dept_parent_info['dept_id']] -> id = $dept_parent_info['dept_id'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> pid = $dept_parent_info['dept_pid'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> text = $dept_parent_info['dept_name'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> attributes = $dept_parent_info['dept_deep'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> iconCls= 'icon-depart';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> data_total = '-';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> data_left = '-';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> state = "open";
		}
		//下一级 部门
		$dept_info = $this->department_model->get_next_level_depatements($dept_id);
		if($dept_info)
		{
			$dept_level_children_ids = array();
			$if_have_children_ids = array();
			foreach($dept_info AS $dept)
			{
				if(empty($dept['state']))
				$dept_level_children_ids[] = $dept['id']; //下一级子部门 id（没有下下部门的）
				else
				{
					$if_have_children_ids[] = $dept['id'];//下一级子部门 id（有下下部门的）
				}
			}

			//获取下级部门已分配的未接来电信息（没有下下级部门的）
			$client_dept = $this->get_dept_static_num($dept_level_children_ids);

			//获取下级部门已分配的未接来电信息（有下下级部门的）
			foreach($if_have_children_ids AS $id)
			{
				//下级及下下级部门的id
				$dept_level_children_ids = $this->department_model->get_department_children_ids($id);
				//下级及下下级的部门统计信息
				$client_dept_child = $this->get_dept_static_num($dept_level_children_ids);
				foreach($client_dept_child AS $one_child_info)
				{
					$client_dept[$id]['data_total'] = empty($client_dept[$id]['data_total'])?0:$client_dept[$id]['data_total'];
					$client_dept[$id]['data_total'] += $one_child_info['data_total'];
					$client_dept[$id]['data_left'] = empty($client_dept[$id]['data_left'])?0:$client_dept[$id]['data_left'];
					$client_dept[$id]['data_left'] += $one_child_info['data_left'];
				}
			}

			foreach($dept_info AS $dept)
			{
				if(empty($dept['state']))
				{
					$if_have_user = $this->user_model->get_user_totalnum($dept['id']);
					if($if_have_user > 0)
					{
						$dept['state'] = "closed";
						$dept['children'] = '';
					}
				}
				$dept['data_total'] = empty($client_dept[$dept['id']]['data_total']) ? 0 : $client_dept[$dept['id']]['data_total'];
				$dept['data_left'] = empty($client_dept[$dept['id']]['data_left']) ? 0 : $client_dept[$dept['id']]['data_left'];
				$dept_tree_arr[$dept_id] -> children[] = $dept;
			}
		}
		//下一级 员工
		$user_info = $this->user_model->get_users(array('dept_id_only'=>$dept_id));
		if($user_info)
		{
			//客户未分配
			$client_info = $this->get_user_static_num($dept_id);

			foreach($user_info as $user)
			{
				$tmp_user = new stdClass();
				$tmp_user -> id = 'user'.$user['user_id'];
				$tmp_user -> pid= $user['dept_id'];
				$tmp_user -> text = $user['user_name']."[".$user['user_num']."]";
				$tmp_user -> attributes = 'last';
				$tmp_user -> iconCls= 'icon-user';
				$tmp_user -> data_total = empty($client_info[$user['user_id']]['data_total']) ? 0 : $client_info[$user['user_id']]['data_total'];
				$tmp_user -> data_left = empty($client_info[$user['user_id']]['data_left']) ? 0 : $client_info[$user['user_id']]['data_left'];
				$dept_tree_arr[$user['dept_id']] -> children[] = $tmp_user;
			}
		}
		if(!empty($dept_tree_arr))
		{
			if($dept_id == $dept_session_id)
			return array(object_to_array($dept_tree_arr[$dept_id]));
			else
			return object_to_array($dept_tree_arr[$dept_id]->children);
		}
		else
		{
			return $dept_tree_arr;
		}
	}

	/**
	 *批量分配 - 实际分配
	 *                         检索条件
	 * @param array $condition = array(
									  [user_id]  =>  所属人
									  [dept_id]  =>  部门
									  [cle_name] => 客户姓名
									  [cle_phone] => 客户电话
									  [all_type]  => 数据分类
									)
	 * @param string $assign_str = “部门ID?坐席ID-分配数量#”
	 * @param int    $total_limit
	 * @return int 返回剩下可分配数
	 */
	public function missed_calls_batch_deployment($condition = array(),$assign_str = '',$total_limit = 0)
	{
		if (empty($assign_str) || $total_limit <= 0)
		{
			return FALSE;
		}

		/*页面分配结果----------------------------------------------------*/
		$deploy_dept_id = array();
		$deploy_user_id = array();     //所有参与分配的坐席ID
		$balance_all    = array();  //分配数量 - 与页面对应的数量

		$str_user_ids   = array(); //坐席
		$balance_user   = array();  //坐席分配的数量
		//坐席对应部门
		$user_dept  = array(); //  array("user_id"=>"dept_id")

		$str_dept_ids   = array(); //部门
		$balance_dept   = array();  //部门分配的数量

		$control = 0; //分配了的数据

		$deploy_missed_id  = array(); //坐席实际分配到的未接来电ID
		/*----------------------------------------------------*/

		$where     = "";
		$sortName  = "start_date";
		$sortOrder = "DESC";
		if ($condition)
		{
			$sortName  = empty($condition["sortName"]) ? "start_date" : $condition["sortName"];
			$sortOrder = empty($condition["sortOrder"]) ? "DESC" : $condition["sortOrder"];

			$wheres = $this->get_missed_calls_condition($condition);
			$where  = implode(" AND ",$wheres);
		}
		//排序
		if ($sortName)
		{
			$sortName = $this->get_real_field($sortName);
		}

		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		$this->db_read->order_by($sortName,$sortOrder);
		$this->db_read->limit($total_limit);
		$this->db_read->select("id AS missed_id");
		$missed_query = $this->db_read->get("est_lost_cdr");
		$missed_num   = $missed_query->num_rows();//可分配的数据总数
		if ($missed_num != $total_limit )
		{
			return FALSE;
		}
		$missed_info = $missed_query->result_array();

		//部门分配ID   坐席分配ID   分配数量
		$assign_str = explode("#",$assign_str);
		foreach ($assign_str AS $assign_info)
		{
			if ($assign_info)
			{
				list($d_dept_id,$d_info) = explode("?",$assign_info);
				list($d_user_id,$d_num) = explode("-",$d_info);
				if($d_dept_id)
				{
					/*所有参与分配的部门ID*/
					$deploy_dept_id[] = $d_dept_id;
					/*所有参与分配的坐席ID*/
					$deploy_user_id[] = $d_user_id;
					/*差额（页面调配数量）*/
					$balance_all[]        = $d_num;
				}
			}
		}

		foreach($deploy_user_id AS $k=>$user_value)
		{
			if($user_value == 0)
			{
				//部门信息
				$str_dept_ids[] = $deploy_dept_id[$k];
				$balance_dept[$deploy_dept_id[$k]] = $balance_all[$k];
			}
			else
			{
				//坐席信息
				$str_user_ids[] = $user_value;
				$balance_user[$user_value] = $balance_all[$k];
			}
		}

		//坐席
		if(!empty($str_user_ids))
		{
			//坐席对应部门
			$this->load->model("user_model");
			$_user_ids = implode(',',$str_user_ids);
			$user_query = $this->user_model->get_users(array('more_user'=>$_user_ids));
			foreach ($user_query AS $value)
			{
				if ($value["user_id"])
				{
					$user_dept[$value["user_id"]]     = $value["dept_id"];
				}
			}

			foreach ($balance_user AS $cle_user_id => $deploy_num)
			{
				if ($deploy_num > 0)
				{
					for ($i=0;$i<$deploy_num;$i++)
					{
						$deploy_missed_id['user'][$cle_user_id][] = $missed_info[$control]["missed_id"];
						$control ++;
					}
				}
			}
		}

		//部门
		if(!empty($str_dept_ids))
		{
			foreach ($balance_dept  AS $cle_dept_id => $deploy_num_dept)
			{
				if ($deploy_num_dept > 0)
				{
					for ($i=0;$i<$deploy_num_dept;$i++)
					{
						$deploy_missed_id['dept'][$cle_dept_id][] = $missed_info[$control]["missed_id"];
						$control ++;
					}
				}
			}
		}

		//系统提示
		$this->load->model('notice_model');
		//坐席
		if (!empty($deploy_missed_id['user']))
		{
			//每个坐席的进行分配
			foreach ($deploy_missed_id['user'] AS $missed_user_id => $missed_ids)
			{
				if (!empty($missed_ids))
				{
					$missed_dept_id    = empty($user_dept[$missed_user_id]) ? 0 : $user_dept[$missed_user_id];
					$str_missed_id = implode(",",$missed_ids);

					$result = $this->db_write->query("UPDATE est_lost_cdr SET dept_id = '$missed_dept_id',user_id = '$missed_user_id' WHERE id IN ($str_missed_id) ");
					/*写入通知坐席的信息*/
					if($result)
					{
						//给提示
						if($this->db_write->affected_rows()>0)
						{
							$this->notice_model->write_notice('system',$missed_user_id,'','您有新的未接来电');
						}
					}
				}
			}
		}
		//部门
		if (!empty($deploy_missed_id['dept']))
		{
			foreach ($deploy_missed_id['dept'] AS $missed_dept_id => $missed_ids)
			{
				if (!empty($missed_ids))
				{
					$str_missed_id = implode(",",$missed_ids);
					$this->db_write->query("UPDATE est_lost_cdr SET dept_id = '$missed_dept_id',user_id = '0' WHERE id IN ($str_missed_id) ");
				}
			}
		}

		if($control == 0)
		{
			$control = '-1';
		}

		return $control;
	}

	/**
	 * 工作桌面 - 我的未接来电
	 *
	 * @return array
	 * <code>
	 * array(
	 * 	 [0]=> array(
	 * 			[id]=> 未接来电id
	 * 			[start_time]=> 来电时间
	 * 			[caller] => 来电电话号
	 * 			[state] => 状态
	 * 			[reason] => 原因
	 * 			[cle_id] => 客户id
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function my_miss_calls_workbench()
	{
		$user_id = $this->session->userdata('user_id');

		//获取半小时通过话的电话
		$this->load->model('callrecords_model');
		$half_hour_call = $this->callrecords_model->get_phone_on_half_hour();

		$query = $this->db_read->query("SELECT id,start_time,caller,state,reason,cle_id FROM est_lost_cdr WHERE user_id='$user_id' AND state=0 LIMIT 10");
		$my_misscall_info = array();
		if($query->result_array())
		{
			foreach($query->result_array() AS $value)
			{
				if(empty($half_hour_call) || !in_array($value['caller'],$half_hour_call))
				{
					$value['miss_reason'] = $this->miss_reason_chinese($value['reason']);
					$value['date'] = date('Y-m-d H:i:s',$value['start_time']);
					$my_misscall_info[] = $value;
				}
			}
		}
		return $my_misscall_info;
	}

	/**
	 * 未接来电原因 - 中文
	 * 
	 * @param string $reason 原因
	 * @return string
	 * 
	 * @author zgx
	 */
	private function miss_reason_chinese($reason='')
	{
		if(empty($reason))
		$reason = 0;
		switch($reason)
		{
			case 0:$reason = '';break;
			case 1:$reason = 'ivr超限';break;
			case 2:$reason = '未启用';break;
			case 3:$reason = '过期';break;
			case 4:$reason = '余额不足';break;
			case 5:$reason = '不在接通时间内';break;
			case 6:$reason = '未设置日程';break;
			case 7:$reason = '企业不存在';break;
			case 8:$reason = '黑名单';break;
			case 101:$reason = 'IVR挂机';break;
			case 102:$reason = '留言';break;
			case 103:$reason = '未接通';break;
			case 104:$reason = '未接通留言';break;
			default:
				break;
		}
		return $reason;
	}
}