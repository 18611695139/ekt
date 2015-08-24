<?php
class Client_resource_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 客户调配 - 选中调配  - 通过客户ID调配客户数据
	 *
	 * @param string $cle_id  客户ID（单个客户ID或数组）
	 * @param int    $cle_dept_id   数据要分配的部门ID
	 * @param int    $cle_user_id   数据要分配的坐席ID
	 * @return bool
	 * 
	 * @author yan
	 */
	public function deployment_by_id($cle_id,$cle_dept_id=0,$cle_user_id = 0)
	{
		if (empty($cle_id))
		{
			return FALSE;
		}
		if(!is_array($cle_id))
		{
			$cle_id = array($cle_id);
		}

		//坐席信息
		$cle_user_name = "";
		$cle_user_num  = "";
		//指定人员
		if ($cle_user_id != 0)
		{
			$this->load->model("user_model");
			$user_info = $this->user_model->get_user_info($cle_user_id);
			$cle_dept_id   = empty($user_info["dept_id"]) ? 0 : $user_info["dept_id"];
			$cle_user_name = empty($user_info["user_name"]) ? "" : $user_info["user_name"];
			$cle_user_num  = empty($user_info["user_num"]) ? "" : $user_info["user_num"];
		}

		$user_id     = $this->session->userdata("user_id");
		$user_name   = $this->session->userdata("user_name");
		$user_num    = $this->session->userdata("user_num");
		$date        = date("Y-m-d");
		$datetime    = date("Y-m-d H:i:s");
		$cle_id_str  = implode(',',$cle_id);

		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action))
		{
			$this->load->model('order_model');
		}
		//指定人员
		if ($cle_user_id!=0)
		{
			$result = $this->db_write->query("UPDATE est_client SET last_user_id = CONCAT(IFNULL(last_user_id,''),',',user_id) , dployment_num = dployment_num+1  ,user_id = '$cle_user_id', cle_executor_time = '$datetime',dept_id = '$cle_dept_id', cle_update_time = '$date' , cle_update_user_id = '$user_id' ,cle_public_type = 0 WHERE cle_id IN ($cle_id_str) ");
			/*写入通知坐席的信息*/
			$this->resource_message($cle_id,$user_id,$cle_user_id);

			if(in_array('ddgl',$action))
			{
				$this->order_model->update_order_user_id_when_client_resource($cle_id,$cle_user_id);
			}
		}
		//指定部门
		elseif($cle_user_id==0 && $cle_dept_id != 0)
		{
			$result = $this->db_write->query("UPDATE est_client SET dployment_num = dployment_num+1,cle_executor_time = '$datetime',dept_id = '$cle_dept_id', cle_update_time = '$date' , cle_update_user_id = '$user_id' WHERE cle_id IN ($cle_id_str) ");

			if(in_array('ddgl',$action))
			{
				$this->order_model->update_order_user_id_when_client_resource($cle_id,0,$cle_dept_id);
			}
		}
		// 部门名称
		$this->load->model('department_model');
		$cle_dept_info = $this->department_model->get_department_info($cle_dept_id);
		$cle_dept_name = empty($cle_dept_info) ? '' : $cle_dept_info['dept_name'];

		//得到需要调配客户信息
		$cle_result = $this->db_read->query("SELECT cle_id,cle_name,cle_stat FROM est_client WHERE cle_id IN ($cle_id_str)");
		//日志
		$log_contact = array();
		$log_cle_id  = array();
		foreach ($cle_result->result_array() AS $value)
		{
			if($cle_user_id==0 && $cle_dept_id != 0)
			{
				$log_contact[] = "调配部门(选中调配)|".$user_name."[".$user_num."]  分配客户到  ".$cle_dept_name." 部门]";
			}
			else
			{
				$log_contact[] = "调配客户(选中调配)|".$user_name."[".$user_num."]  分配客户到  ".$cle_user_name."[".$cle_user_num."]";
			}
			$log_cle_id[]  = $value["cle_id"];
		}
		//记入日志
		$this->load->model("log_model");
		$this->log_model->write_client_log($log_contact,$log_cle_id);
		return $result;
	}

	/**
	 * 得到分配的检索条件
	 *
	 * @param array $condition = array(    检索字段及对应值
	 *    [name] => 姓名
	 *    [phone] => 电话
	 *    ......
	 *    )
	 * @return array(
	 * 				where	=>	检索条件
	 * 				condition_contact =>	是否检索联系人
	 * 				)
	 *
	 * @author yan
	 */
	private function get_deployment_condition($condition=array())
	{
		//处理检索条件
		$this->load->model("client_model");
		$where_responce = $this->client_model->get_client_condition($condition);
		$wheres = $where_responce->wheres;
		//没有所属人
		$wheres[] = "user_id = 0";
		//本部门(不包括子部门)
		$wheres[] = "dept_id = ".$this->session->userdata('dept_id');;

		//检索联系人
		$condition_contact = $where_responce->condition_contact;

		$where  = implode(" AND ",$wheres);
		return array('where'=>$where,'condition_contact'=>$condition_contact);
	}

	/**
	 * 批量分配 - 获取搜索到的数据中未分配数据（无所属人）总数
	 *
	 * @param array $condition = array(
	 *    [user_id]  => 所属人ID
	 *    [cle_name] => 客户姓名
	 *    ...
	 * )
	 * @return int 符合条件的客户数量	 * 
	 * @author yan
	 */
	public function get_batch_total($condition=array())
	{
		//处理检索
		$batch_condition = $this->get_deployment_condition($condition);
		$sql_join = 'AND est_contact.con_if_main = 1';

		if($batch_condition['where'])
		{
			$this->db_read->where($batch_condition['where']);
		}
		$this->db_read->select('count(*) as total',FALSE);
		$this->db_read->from('est_client');
		if($batch_condition['condition_contact'])
		{
			//检索联系人信息
			$this->db_read->distinct();
			$sql_join = "";
		}
		$this->db_read->join('est_contact', 'est_contact.cle_id = est_client.cle_id '.$sql_join,'LEFT');
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;

		return $total;
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
	 * @param string $assign_str = “部门ID-坐席ID-分配数量#”
	 * @param int    $total_limit
	 * @param int    $special_deploy  数据不分配给曾经占有过的坐席
	 * @return int 返回剩下可分配数
	 * @author yan
	 */
	public function batch_deployment($condition = array(),$assign_str = '',$total_limit = 0,$special_deploy = 0)
	{
		$this->load->library("json");
		if(!empty($condition['field_confirm_values']))
		{
			$condition['field_confirm_values'] = $this->json->encode($condition['field_confirm_values'],1);
		}
		if (empty($assign_str) || $total_limit <= 0)
		{
			return FALSE;
		}
		//当前坐席信息
		$dept_id   = $this->session->userdata("dept_id");
		$user_id   = $this->session->userdata("user_id");
		$user_name = $this->session->userdata("user_name");
		$user_num  = $this->session->userdata("user_num");

		//日志
		$log_contact = array();
		$log_cle_id  = array();

		//处理检索条件
		$batch_condition = $this->get_deployment_condition($condition);
		$sql_join = 'AND est_contact.con_if_main = 1';

		/*数据中查询需要分配的客户信息*/
		if ($batch_condition['where'])
		{
			$this->db_read->where($batch_condition['where']);
		}

		$this->db_read->order_by("cle_creat_time","DESC");
		$this->db_read->from('est_client');
		$this->db_read->select("est_client.cle_id,est_client.last_user_id,est_client.cle_name,est_client.cle_stat");

		if($batch_condition['condition_contact'])
		{
			//检索联系人信息
			$this->db_read->distinct();
			$sql_join = "";
		}
		$this->db_read->join('est_contact', 'est_contact.cle_id = est_client.cle_id '.$sql_join,'LEFT');
		$this->db_read->limit($total_limit);
		$client_query = $this->db_read->get();
		$client_num   = $client_query->num_rows();//可分配的数据总数
		if ($client_num != $total_limit )
		{
			return FALSE;
		}
		$client_info = $client_query->result_array();

		/*页面分配结果*/
		$deploy_dept_id = array();
		$deploy_user_id = array();     //所有参与分配的坐席ID
		$balance        = array();  //差额（页面调配数量）
		$balance_all        = array();  //差额（页面调配数量）
		$balance_dept        = array();  //差额（页面调配数量）
		$deploy_cle_id  = array(); //坐席实际分配到的客户ID

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

		$str_dept_ids = array(); //部门
		$str_user_ids = array(); //坐席
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
				$balance[$user_value] = $balance_all[$k];
			}
		}

		$control = 0; //分配了的数据

		//坐席
		if(!empty($str_user_ids))
		{
			$this->load->model("user_model");
			$_user_ids = implode(',',$str_user_ids);
			$user_query = $this->user_model->get_users(array('more_user'=>$_user_ids));
			$user_dept  = array();
			$cle_user_name = array();
			$cle_user_num  = array();
			foreach ($user_query AS $value)
			{
				if ($value["user_id"])
				{
					$user_dept[$value["user_id"]]     = $value["dept_id"];
					$cle_user_name[$value["user_id"]] = $value["user_name"];
					$cle_user_num[$value["user_id"]]  = $value["user_num"];
				}
			}
			//数据不分配给曾经占有过的坐席
			if ($special_deploy == 1 )
			{
				for($k=0;$k<count($client_info);$k++)
				{
					if ($client_info[$control])
					{
						/*得到当前数据曾经的占有人信息*/
						$last_user_id = $client_info[$control]["last_user_id"];
						$last_user_id = explode(",",$last_user_id);
						/*计算数组差集，键名保留不变,得到未占用过该数据的坐席信息*/
						$free_user = array_diff($str_user_ids,$last_user_id);
						if (empty($free_user))
						{
							//该数据没有坐席可分
							continue;
						}
						//比较可用坐席，找出差额最大的一个
						$number     = 0;
						$special_id = 0;
						foreach ($free_user AS $free_user_id)
						{
							if ($balance[$free_user_id] > $number)
							{
								$number = $balance[$free_user_id];
								$special_id = $free_user_id;
							}
						}

						if ($special_id)
						{
							//分配
							$balance[$special_id] = $balance[$special_id] -1;
							$deploy_cle_id['user'][$special_id][] = $client_info[$control]["cle_id"];
							//构造日志
							$log_cle_id[]  = $client_info[$control]["cle_id"];
							$log_contact[] = "调配客户(批量调配)：".$user_name."[".$user_num."]  分配客户 ".$client_info[$control]["cle_name"]."[".$client_info[$control]["cle_id"].",".$client_info[$control]["cle_stat"]."]  给".@$cle_user_name[$special_id]."[".@$cle_user_num[$special_id]."]";

							$control++;
						}
					}
				}

			}
			else
			{
				foreach ($balance AS $cle_user_id => $deploy_num)
				{
					if ($deploy_num > 0)
					{
						for ($i=0;$i<$deploy_num;$i++)
						{
							$deploy_cle_id['user'][$cle_user_id][] = $client_info[$control]["cle_id"];

							//构造日志
							$log_cle_id[]  = $client_info[$control]["cle_id"];
							$log_contact[] = "调配客户(批量调配)|".$user_name."[".$user_num."]  分配客户到给  ".@$cle_user_name[$cle_user_id]."[".@$cle_user_num[$cle_user_id]."]";

							$control ++;
						}
					}
				}
			}
		}

		//部门
		if(!empty($str_dept_ids))
		{
			$this->load->model('department_model');
			$dept_query = $this->department_model->get_all_department();
			$dept_name = array();
			foreach($dept_query AS $k=>$v)
			{
				if($v['dept_id'])
				{
					$dept_name[$v['dept_id']] = $v['dept_name'];
				}
			}
			foreach ($balance_dept  AS $cle_dept_id => $deploy_num_dept)
			{
				if ($deploy_num_dept > 0)
				{
					for ($i=0;$i<$deploy_num_dept;$i++)
					{
						$deploy_cle_id['dept'][$cle_dept_id][] = $client_info[$control]["cle_id"];

						//构造日志
						$log_cle_id[]  = $client_info[$control]["cle_id"];
						$log_contact[] = "调配部门(批量调配)|".$user_name."[".$user_num."]  分配客户到给  ".@$dept_name[$cle_dept_id].'部门';
						$control ++;
					}
				}
			}
		}
		//分配
		$date      = date("Y-m-d");
		$datetime  = date("Y-m-d H:i:s");
		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action))
		{
			$this->load->model('order_model');
		}
		//坐席
		if (!empty($deploy_cle_id['user']))
		{
			//每个坐席的进行分配
			foreach ($deploy_cle_id['user'] AS $cle_user_id => $cle_ids)
			{
				if (!empty($cle_ids))
				{
					$dept_id    = empty($user_dept[$cle_user_id]) ? 0 : $user_dept[$cle_user_id];
					$str_cle_id = implode(",",$cle_ids);
					$this->db_write->query("UPDATE est_client SET last_user_id = CONCAT(IFNULL(last_user_id,''),',',user_id) , dployment_num = dployment_num+1  ,user_id = '$cle_user_id', cle_executor_time = '$datetime',dept_id = '$dept_id', cle_update_time = '$date' , cle_update_user_id = '$user_id',cle_public_type = 0 WHERE cle_id IN ($str_cle_id) ");

					/*写入通知坐席的信息*/
					$this->resource_message($cle_ids,$user_id,$cle_user_id);
					if(in_array('ddgl',$action))
					{
						$this->order_model->update_order_user_id_when_client_resource($cle_ids,$cle_user_id);
					}
				}
			}
		}
		//部门
		if (!empty($deploy_cle_id['dept']))
		{
			foreach ($deploy_cle_id['dept'] AS $cle_dept_id => $cle_ids)
			{
				if (!empty($cle_ids))
				{
					$str_cle_id = implode(",",$cle_ids);
					$this->db_write->query("UPDATE est_client SET last_user_id = CONCAT(IFNULL(last_user_id,''),',',user_id) , dployment_num = dployment_num+1  ,user_id = 0, cle_executor_time = '$datetime',dept_id = '$cle_dept_id', cle_update_time = '$date' , cle_update_user_id = '$user_id' WHERE cle_id IN ($str_cle_id) ");

					if(in_array('ddgl',$action))
					{
						$this->order_model->update_order_user_id_when_client_resource($cle_ids,0,$cle_dept_id);
					}
				}
			}
		}
		if(!empty($deploy_cle_id))
		{
			//记入日志
			$this->load->model("log_model");
			$this->log_model->write_client_log($log_contact,$log_cle_id);
		}
		if($control == 0)
		{
			$control = '-1';
		}

		return $control;
	}


	/**
	 * 批量分配   得到部门-员工树，对应‘已有数据’和‘未拨打数据’
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
	 * @author yan zgx
	 */
	public function deployment_batch($dept_id=0)
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
			$dept_tree_arr[$dept_parent_info['dept_id']] ->  iconCls= 'icon-depart';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> data_total = '-';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> data_left = '-';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> state = "open";
		}
		//下一级 部门
		$dept_info = $this->department_model->get_next_level_depatements($dept_id);
		if($dept_info)
		{
			$dept_level_children_ids = array();
			foreach($dept_info AS $dept)
			{
				$dept_level_children_ids[] = $dept['id']; //下一级子部门 id
			}
			//部门总数，未拨打数。。。
			$client_dept = $this->get_dept_classification_num($dept_level_children_ids);
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
			//客户信息，按坐席分组，统计总数和未拨打数
			$client_info = $this->get_user_classification_num($dept_id);

			//客户限制
			$client_amount = 0;
			$this->load->model("system_config_model");
			$system = $this->system_config_model->get_system_config();
			if($system['client_amount']!=0)
			{
				$client_amount = $system['client_amount'];
			}

			foreach($user_info as $user)
			{
				$tmp_user = new stdClass();
				$tmp_user -> id = 'user'.$user['user_id'];
				$tmp_user -> pid= $user['dept_id'];
				$tmp_user -> text = $user['user_name']."[".$user['user_num']."]";
				$tmp_user -> attributes = 'last';
				$tmp_user -> iconCls= 'icon-user';
				$tmp_user -> data_total = empty($client_info[$user['user_id']]['data_total']) ? 0 : $client_info[$user['user_id']]['data_total'];//客户数
				$tmp_user -> data_left = empty($client_info[$user['user_id']]['data_left']) ? 0 : $client_info[$user['user_id']]['data_left'];//待打数
				if($client_amount==0)
				{
					$tmp_user -> data_amount = '无限制';
				}
				else
				{
					$stage_success = empty($client_info[$user['user_id']]['stage_success']) ? 0 : $client_info[$user['user_id']]['stage_success'];//成交客户数
					$tmp_user -> data_amount = ($client_amount - ($tmp_user -> data_total) + $stage_success);
				}
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
	 * 客户调配 - 选中收回
	 *
	 * @param string   $cle_id  客户ID(多个ID以逗号分隔)
	 * @return boolen  数据库执行结果
	 * 
	 * @author yan
	 */
	public function take_back_client($cle_id = "")
	{
		$user_id     = $this->session->userdata("user_id");
		$user_name   = $this->session->userdata("user_name");
		$user_num    = $this->session->userdata("user_num");
		$dept_id   = $this->session->userdata("dept_id");
		$date        = date("Y-m-d");
		$datetime    = date("Y-m-d H:i:s");
		//日志
		$log_contact = array();
		$log_cle_id  = array();

		//开始收回
		$array_cle_id = explode(",",$cle_id);
		foreach ($array_cle_id AS $value)
		{

			$result = $this->db_write->query("UPDATE est_client SET last_user_id=CONCAT(IFNULL(last_user_id,''),',',user_id),dployment_num=dployment_num+1,user_id=0,dept_id='".$dept_id."',cle_executor_time='$datetime',cle_update_time='$date',cle_update_user_id='$user_id',cle_public_type=2 WHERE cle_id = $value ");

			$role_action = $this->session->userdata('role_action');
			$action = explode(',',$role_action);
			if(in_array('ddgl',$action))
			{
				$this->load->model('order_model');
				$this->order_model->update_order_user_id_when_client_resource($value,0,$dept_id);
			}
			//收回到部门 --日志
			$log_contact[] = "选中收回|操作人：".$user_name."[".$user_num."]";
			$log_cle_id[]  = $value;
		}

		//记入日志
		$this->load->model("log_model");
		$this->log_model->write_client_log($log_contact,$log_cle_id);

		return $result;
	}

	/**
	 * 批量回收
	 *
	 * @param array $condition  检索条件
     * @param string $content_str 日志用的
	 * @return bool
	 * @author yan zgx
	 */
	public function take_more_client_back($condition=array(),$content_str='批量收回')
	{
		$user_id     = $this->session->userdata("user_id");
		$user_name   = $this->session->userdata("user_name");
		$user_num    = $this->session->userdata("user_num");
		$dept_id   = $this->session->userdata("dept_id");
		$date        = date("Y-m-d");
		$datetime    = date("Y-m-d H:i:s");
		//日志
		$log_contact = array();
		$log_cle_id  = array();

        $select_contact = true;// 检索联系人字段
        $join_contact = true; //连接联系人表
        $sql_join = 'AND est_contact.con_if_main = 1';

		//处理检索条件
		$this->load->model("client_model");
		$where_responce = $this->client_model->get_client_condition($condition);
		$wheres = $where_responce->wheres;
		$condition_contact = $where_responce->condition_contact;
		if($condition_contact)
		{
			$sql_join = "";
		}
        //处理检索字段
        $this->load->model('datagrid_confirm_model');
        if(empty($select))
        {
            $select = $this->datagrid_confirm_model->get_available_select_fields(LIST_CONFIRM_CLIENT_RESOURCE);
        }
        $select[] = 'cle_public_type';
        $fields_contact = $this->datagrid_confirm_model->get_available_select_fields(LIST_CONFIRM_CONTACT);
        $select_fields_contact_intersect = array_intersect($select,$fields_contact);//select 和 联系人字段的交集
        if(empty($select_fields_contact_intersect))//如果检索的字段中没有联系人的字段
        {
            $select_contact = false;
        }
        if(!$select_contact && !$condition_contact)//不关联联系人表
        {
            $join_contact = false;
        }

		/*数据中查询需要分配的客户信息*/
		if ($wheres)
		{
			$where  = implode(" AND ",$wheres);
			$this->db_read->where($where);
		}
		$this->db_read->order_by("cle_creat_time","DESC");
		$this->db_read->from('est_client');
		$this->db_read->distinct();
		$this->db_read->select("est_client.cle_id");
		if($join_contact)
		{
			$this->db_read->join('est_contact', 'est_contact.cle_id = est_client.cle_id '.$sql_join,'LEFT');
		}
		$query = $this->db_read->get();

		$cle_id_array = array();
		if($query)
		{
			foreach($query->result_array() AS $client)
			{
				$cle_id_array[] = $client['cle_id'];
			}
			if(!empty($cle_id_array))
			{
				/*写入日志*/
				foreach($cle_id_array AS $value)
				{
					$log_contact[] = $content_str."|操作人：".$user_name."[".$user_num."]";
					$log_cle_id[]  = $value;
				}
				//记入日志
				$this->load->model("log_model");
				$this->log_model->write_client_log($log_contact,$log_cle_id);

				//批量收回
				$result = $this->db_write->query("UPDATE est_client SET last_user_id=CONCAT(IFNULL(last_user_id,''),',',user_id),dployment_num=dployment_num+1,user_id=0,dept_id='".$dept_id."',cle_executor_time='$datetime',cle_update_time='$date',cle_update_user_id='$user_id',cle_public_type=2 WHERE cle_id IN (".implode(',',$cle_id_array).")");

				$role_action = $this->session->userdata('role_action');
				$action = explode(',',$role_action);
				if(in_array('ddgl',$action))
				{
					$this->load->model('order_model');
					$this->order_model->update_order_user_id_when_client_resource($cle_id_array,0,$dept_id);
				}
				return $result;
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 * 员工部门改变 - 2数据所属部门改变为新部门
	 *
	 * @param int $owner_user_id  调配的员工ID
	 * @param int $owner_dept_id  新部门ID
	 * @param string $owner_dept_name 新部门名称
	 * @return bool
	 *
	 * @author yan zgx
	 */
	public function user_dept_change_client($owner_user_id,$owner_dept_id,$owner_dept_name)
	{
		if ( !$owner_user_id || !$owner_dept_id )
		{
			return FALSE;
		}

		$this->load->model("user_model");
		$user_info = $this->user_model->get_user_info($owner_user_id);
		$log_str  = "员工".$user_info["user_name"]."[".$user_info["user_num"]."]部门信息改变，数据所属部门改变为新部门：".$owner_dept_name;

		$this->db_read->select("cle_id");
		$this->db_read->where("user_id",$owner_user_id);
		$this->db_read->where("dept_id !=",$owner_dept_id);
		$query = $this->db_read->get("est_client");
		$client_info = $query->result_array();

		//更新部门信息
		$this->db_write->where("user_id",$owner_user_id);
		$this->db_write->where("dept_id !=",$owner_dept_id);
		$result = $this->db_write->update("est_client",array("dept_id"=>$owner_dept_id));

		if ($result)
		{
			$content    = array();
			$log_cle_id = array();
			
//			$role_action = $this->session->userdata('role_action');
//			$action = explode(',',$role_action);
//			if(in_array('ddgl',$action))
//			{
//				$this->load->model('order_model');
//			}
			foreach ($client_info AS $value)
			{
				if (!empty($value["cle_id"]))
				{
					$content[] = $log_str;
					$log_cle_id[] = $value["cle_id"];

//					if(in_array('ddgl',$action))
//					{
//						$this->order_model->update_order_user_id_when_client_resource($value["cle_id"],$owner_user_id);
//					}
				}
			}

			if (!empty($log_cle_id))
			{
				//操作日志
				$this->load->model("log_model");
				$this->log_model->write_client_log($content,$log_cle_id);
			}
		}
		return true;
	}

	/**
	 * 分配客户数据 - 提示坐席
	 *
	 * @param array $array_cle_id  相关客户ID
	 * @param string $msg_send_user_id  发送人ID
	 * @param string $msg_receive_user_id  接收人ID
	 * @return bool
	 * 
	 * @author yan
	 */
	private function resource_message($array_cle_id=array(),$msg_send_user_id='',$msg_receive_user_id='')
	{
		//写入消息
		$this->load->model('notice_model');
		$content = "管理员给您分配了".count($array_cle_id)."个客户";
		$this->notice_model->write_notice('system',$msg_receive_user_id,'',$content,0,$msg_send_user_id);
		return TRUE;
	}
	/**
	 * 客户信息，按坐席分组，统计总数和未拨打数
	 *
	 * @param int $dept_id 部门id
	 * @return array 
	 * @author yan zgx
	 */
	private function get_user_classification_num($dept_id=0)
	{
		$client_data = array();
		if(empty($dept_id))
		{
			return $client_data;
		}
		if(is_array($dept_id))
		{
			$dept_id = implode(',',$dept_id);
		}
		//等级为终结 的 客户阶段 ZJKH终结
		$this->load->model('client_type_model');
		$stages = $this->client_type_model->get_stage_by_cle_type(ZJKH);
		$stage_sql = '';
		if($stages)
		{
			foreach($stages as $k=>$s)
			{
				$stage_sql .= ",SUM( IF( cle_stage = '".$s."', 1, 0 ) ) AS stage".$k;
			}
		}

		//得到处理的客户数据总数、未拨打总数
		$cle_result = $this->db_read->query("SELECT user_id,COUNT( * ) AS data_total,SUM( IF( cle_stat = '未拨打', 1, 0 ) ) AS data_left".$stage_sql." FROM est_client WHERE user_id >0 AND dept_id IN(".$dept_id.") GROUP BY user_id");

		foreach ($cle_result->result_array() AS $value)
		{
			if (!empty($value["user_id"]))
			{
				$client_data[$value["user_id"]]["data_total"] = $value["data_total"];//坐席数据总数
				$client_data[$value["user_id"]]["data_left"]  = $value["data_left"];//坐席未拨打数据
				$client_data[$value["user_id"]]["stage_success"] = 0;//根据阶段 得到 成交客户
				if(!empty($stage_sql))
				{
					foreach($stages as $key=>$stage_value)
					{
						$client_data[$value["user_id"]]["stage_success"] += $value["stage".$key];
					}
				}
			}
		}
		return $client_data;
	}

	/**
	 * 部门的客户信息 按部门分组 ，统计总数 和未拨打数
	 *
	 * @param int $dept_id 部门id
	 * @return array
	 * @author yan zgx
	 */
	private function get_dept_classification_num($dept_id = 0)
	{
		$client_data = array();
		if(empty($dept_id))
		{
			return $client_data;
		}
		$this->load->model("department_model");
		$dept_ids = array();
		if(is_array($dept_id))
		{
			foreach($dept_id as $id)
			{
				$dept_array = $this->department_model->get_department_children_ids($id);
				if(!empty($dept_array))
				{
					$dept_ids[$id] = implode(",",$dept_array);
				}
			}
			$dept_id = implode(',',$dept_ids);
		}
		$dept_result = $this->db_read->query("SELECT dept_id, COUNT( * ) AS data_total, SUM( IF( cle_stat = '未拨打', 1, 0 ) ) AS data_left  FROM est_client WHERE dept_id >0 AND dept_id IN(".$dept_id.") GROUP BY dept_id");

		$i_c_data = array();
		foreach ($dept_result->result_array() AS $value)
		{
			if (!empty($value["dept_id"]))
			{
				$i_c_data[$value["dept_id"]]["data_total"] = $value["data_total"];//坐席数据总数
				$i_c_data[$value["dept_id"]]["data_left"]  = $value["data_left"];//坐席未拨打数据
			}
		}

		foreach($dept_ids AS $own_id=>$c_id)
		{
			$child_own_ids = explode(',',$c_id);
			$client_data[$own_id] = array('data_total'=>0,'data_left'=>0);
			foreach($child_own_ids AS $value)
			{
				if(!empty($i_c_data[$value]))
				{
					$client_data[$own_id]['data_total'] += $i_c_data[$value]["data_total"];
					$client_data[$own_id]['data_left'] += $i_c_data[$value]["data_left"];
				}
			}
		}

		return $client_data;
	}

	/**
	 * 根据客户限制，获取某坐席现拥有多少客户数据，还可分配多少客户数据给他 （客户限制为0时，不受限制）
	 *
	 * @param int $user_id
	 * @return array|bool （user_total 是除成功客户外的客户数 batch_amount 根据客户限制还可分配的客户数）
	 * @author zgx
	 */
	public function get_user_batch_by_client_amount($user_id=0)
	{
		if(empty($user_id))
		{
			return FALSE;
		}

		$this->load->model("system_config_model");
		$system = $this->system_config_model->get_system_config();
		if($system['client_amount']==0)
		{
			return TRUE;
		}
		else
		{
			$this->load->model('client_model');
			$user_total = $this->client_model->get_user_client_total_without_success($user_id);
			if($user_total >= $system['client_amount'])
			{
				return array('user_total'=>$user_total,'batch_amount'=>0);
			}
			else
			{
				$batch_amount = $system['client_amount'] - $user_total;
				return array('user_total'=>$user_total,'batch_amount'=>$batch_amount);
			}
		}
	}
}