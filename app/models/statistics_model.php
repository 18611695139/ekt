<?php

use Guzzle\Http\Client;

class Statistics_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	//======================工作量统计====================================
	/**
	 * 坐席添加客户（有阶段信息）：转化量、客户新增量、阶段分布 加1
	 *
	 * @param string $cle_stage  客户阶段
     * @param int $cle_id
	 * @return boolen
	 */
	public function  update_statistics_stage($cle_stage='',$cle_id=0)
	{
		if (empty($cle_stage)||empty($cle_id))
		{
			return FALSE;
		}

		//数据字典信息
		$this->load->model("dictionary_model");
		$dic_stage = $this->dictionary_model->get_dictionary_stage_by_name($cle_stage);
		$stage_id  = empty($dic_stage) ? 0 : $dic_stage;
		if (!empty($stage_id))
		{
			//当前阶段在统计表中对应的字段
			$stage_field = "cle_stage_".$stage_id;
			if ($this->db_read->field_exists($stage_field,"est_statistics_stage"))
			{
				$date    = date("Y-m-d");
				$user_id = $this->session->userdata("user_id");
				$dept_id = $this->session->userdata("dept_id");
				$sql     = "INSERT INTO est_statistics_stage(deal_date,user_id,dept_id,transformation,new_increment,$stage_field) VALUES('$date','$user_id','$dept_id','1','1','1') ON DUPLICATE KEY UPDATE dept_id=$dept_id,transformation = transformation+1, new_increment = new_increment+1,$stage_field = $stage_field+1";
				$result = $this->db_write->query($sql);
				if($result)
				{
					//客户表，标记客户数据为新增量
					$this->db_write->query("UPDATE est_client SET cle_if_increment = 1,cle_stage_change_time='".date('Y-m-d')."' WHERE cle_id = ".$cle_id);
				}

				return  $result;
			}
		}

		return FALSE;
	}

	/**
	 * 编辑客户，阶段发生变化，且新阶段不为空:处理转化量、客户新增量、阶段分布
	 *
	 * @param array  $original 更新前数据
	 * @param string $new_cle_stage 修改的新阶段数据
	 * @return boolen
	 */
	public function edit_statistics_stage($original = array(),$new_cle_stage='')
	{
		if (  empty($original) )
		{
			return false;
		}

		//数据的所属人(如果数据没有所属人，则不进行统计)
		$belong_user_id = empty($original["user_id"]) ? 0 : $original["user_id"];
		if ( $belong_user_id == 0 )
		{
			return TRUE;
		}

		//阶段改变历史
		$cle_last_stage = empty($original["cle_last_stage"]) ? array() : $original["cle_last_stage"];
		if ($cle_last_stage)
		{
			$cle_last_stage = explode(",",$cle_last_stage);
		}

		//数据字典 - 客户阶段
		$this->load->model("dictionary_model");
		$dic_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		$result_stage = array();
		foreach ($dic_stage AS $stage_info)
		{
			if (!empty($stage_info["name"]))
			{
				$result_stage[$stage_info["name"]] = $stage_info;
			}
		}

		//修改前阶段（旧阶段）
		$old_stage      = empty($original["cle_stage"]) ? "" : $original["cle_stage"];
		$old_stage_id   = empty($result_stage[$old_stage]["id"]) ? 0 : $result_stage[$old_stage]["id"];
		//排序
		$old_stage_sort = isset($result_stage[$old_stage]["sorts"]) ? $result_stage[$old_stage]["sorts"] : -1;

		//新阶段ID
		$new_stage_id   = empty($result_stage[$new_cle_stage]["id"]) ? 0 : $result_stage[$new_cle_stage]["id"];
		//排序
		$new_stage_sort = isset($result_stage[$new_cle_stage]["sorts"]) ? $result_stage[$new_cle_stage]["sorts"] : -1;

		$date    = date("Y-m-d");
		//指定坐席的部门信息
		$this->load->model("user_model");
		$the_user_info = $this->user_model->get_user_info($belong_user_id);
		$belog_dept_id = empty($the_user_info["dept_id"]) ? 0 : $the_user_info["dept_id"];

		$cle_condition = array();

		//SQl
		$sql_field = "INSERT INTO est_statistics_stage(deal_date,user_id,dept_id";
		$sql_value = " VALUES('$date','$belong_user_id','$belog_dept_id'";
		$sql_expan = " ON DUPLICATE KEY UPDATE dept_id = $belog_dept_id";

		//上一次阶段改变时间
		$cle_stage_change_time = $original["cle_stage_change_time"];
		//======进阶========================

		if ( $old_stage_sort <  $new_stage_sort )
		{
			if (empty($new_cle_stage))
			{
				return FALSE;
			}

			//转化量
			if ($cle_stage_change_time != $date|| ($cle_stage_change_time == $date && $original["cle_recede"] == 1)  )
			{
				$sql_field .= ",transformation";
				$sql_value .= ",'1'";
				$sql_expan .= " ,transformation = transformation+1";
			}
			//首次联系时间为空/今天 - 新增客户量 且 今天第一个阶段
			if ( ($original["cle_first_connecttime"] == "0000-00-00" || $original["cle_first_connecttime"] == $date) && ($original["cle_if_increment"] == 0) && empty($original['cle_stage']))
			{
				$sql_field .= ",new_increment";
				$sql_value .= ",'1'";
				$sql_expan .= ", new_increment = new_increment+1";

				//新增量: 0否，1是
				$cle_condition["cle_if_increment"] = "cle_if_increment = 1";
			}

			//客户表退阶标志:0进阶，1退阶
			$cle_condition["cle_recede"] = "cle_recede = 0";
		}
		//========退阶=================================
		else
		{
			if ($cle_stage_change_time == $date )
			{
				//今天第一个阶段
				$first_stage = empty($cle_last_stage[0]) ? "" : $cle_last_stage[0];

				$first_stage_sort = isset($result_stage[$first_stage]["sorts"]) ?  $result_stage[$first_stage]["sorts"] : -1;
				if (($new_stage_sort <= $first_stage_sort) && ($original["cle_recede"] == 0))
				{
					//退阶: 转化量减1，退化量加1
					$sql_field .= ",transformation,recede_num";
					$sql_value .= ",'0','1'";
					$sql_expan .= " ,transformation = transformation-1,recede_num = recede_num+1";

					//新增客户量减1
					if ($original["cle_if_increment"] == 1)
					{
						$sql_field .= ",new_increment";
						$sql_value .= ",'0'";
						$sql_expan .= ", new_increment = new_increment - 1";

						//新增量: 0否，1是
						$cle_condition["cle_if_increment"] = "cle_if_increment = 0";
					}

					//客户表退阶标志:0进阶，1退阶
					$cle_condition["cle_recede"] = "cle_recede = 1";
				}
			}
			else
			{
				//退阶: 退化量加1
				$sql_field .= ",recede_num";
				$sql_value .= ",'1'";
				$sql_expan .= ",recede_num = recede_num+1";

				//客户表退阶标志:0进阶，1退阶
				$cle_condition["cle_recede"] = "cle_recede = 1";
				//新增量: 0否，1是
				$cle_condition["cle_if_increment"] = "cle_if_increment = 0";
			}
		}

		/*更新客户信息*/
		if (!empty($cle_condition))
		{
			$cle_condition = implode(",",$cle_condition);
			//客户表，标记客户数据退阶
			$this->db_write->query("UPDATE est_client SET $cle_condition WHERE cle_id = ".$original["cle_id"]);
		}

		//旧阶段减1
		if ($old_stage_id > 0 && $cle_stage_change_time==$date)
		{
			$old_stage_field = "cle_stage_".$old_stage_id;
			if ($this->db_read->field_exists($old_stage_field,"est_statistics_stage"))
			{
				$sql_field .= ",$old_stage_field";
				$sql_value .= ",'0'";
				$sql_expan .= ",$old_stage_field = $old_stage_field-1";
			}
		}

		//新阶段加1
		$new_stage_field = "cle_stage_".$new_stage_id;
		if ($this->db_read->field_exists($new_stage_field,"est_statistics_stage"))
		{
			$sql_field .= ",$new_stage_field";
			$sql_value .= ",'1'";
			$sql_expan .= ",$new_stage_field = $new_stage_field+1";
		}

		$sql_field .= ")";
		$sql_value .= ")";
		//SQL
		$sql = $sql_field.$sql_value.$sql_expan;
		return  $this->db_write->query($sql);
	}

	/**
	 * 获取统计检索条件
	 *
	 * @param array $condition 检索内容
	 * @return array 返回检索条件(一维数组)
	 */
	public function get_statistics_condition($condition = array())
	{
		$wheres = array();
		if(empty($condition))
		{
			return $wheres;
		}
		//部门（包括子部门）
		if(!empty($condition['dept_id']))
		{
			$this->load->model('department_model');
			$dept_children = $this->department_model->get_department_children_ids($condition['dept_id']);

			$wheres[] = 'dept_id IN ('.implode(',',$dept_children).')';
		}
		//本部门id(不包括子部门)
		if(!empty($condition['dept_id_only']))
		{
			$wheres[] = "dept_id = ".$condition['dept_id_only'];
		}
		//父id一样的部门id数组
		if(!empty($condition['dept_level_children_ids']))
		{
			if(is_array($condition['dept_level_children_ids']))
			{
				$condition['dept_level_children_ids'] = implode(',',$condition['dept_level_children_ids']);
			}
			$wheres[] = "dept_id IN(".$condition['dept_level_children_ids'].")";
		}
		//处理时间
		if(!empty($condition['deal_date_start']))
		{
			$wheres[] = "deal_date >= '".$condition['deal_date_start']."'";
		}
		if(!empty($condition['deal_date_end']))
		{
			$wheres[] = "deal_date <= '".$condition['deal_date_end']."'";
		}

		return $wheres;
	}

	/**
	 * 获取统计树列表数据(包括部门及员工)
	 * 
	 * @param int $dept_id 部门id
	 * @param string $start_date 处理时间start
	 * @param string $end_date 处理时间end
	 * @return array(
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
	 * @author zgx
	 */
	public function get_statistics_tree_info($dept_id=0,$start_date='',$end_date='')
	{
		if(empty($start_date) || empty($end_date))
		{
			return '';
		}
		//整理检索内容 - 时间
		$condition = array();
		$condition['deal_date_start'] = $start_date;
		$condition['deal_date_end'] = $end_date;

		//整理时间
		$deal_date = $start_date.'~'.$end_date;

		$dept_session_id = $this->session->userdata('dept_id');
		$this->load->model('department_model');
		$this->load->model('user_model');

		$dept_tree_arr = array();

		//统计权限:全部
		$power_statistics_all = check_authz("tjfxqbqx");
		if($power_statistics_all)
		{
			$dept_session_id = 1;
		}

		//部门id
		if(empty($dept_id)||$dept_id == $dept_session_id)
		{
			$dept_id = $dept_session_id;
			$dept_parent_info = $this->department_model->get_department_info($dept_id);
			$dept_tree_arr[$dept_id] =  new stdClass();
			$dept_tree_arr[$dept_id] -> id = $dept_id;
			$dept_tree_arr[$dept_id] -> pid = $dept_parent_info['dept_pid'];
			$dept_tree_arr[$dept_id] -> text = $dept_parent_info['dept_name'];
			$dept_tree_arr[$dept_id] -> iconCls= 'icon-depart';
			$dept_tree_arr[$dept_id] -> attributes = $dept_parent_info['dept_deep'];
			$dept_tree_arr[$dept_id] -> deal_date = $deal_date;
			$dept_tree_arr[$dept_id] -> state = "open";
		}

		//获取部门的统计信息
		$statistics_dept = $this->_get_statistics_dept_info($dept_id,$start_date,$end_date);

		//一、下一级 部门
		$dept_info = $this->department_model->get_next_level_depatements($dept_id);
		if($dept_info)
		{
			$if_have_children_ids = array();//有下下级的部门
			foreach($dept_info AS $dept)
			{
				if(!empty($dept['state']))
				{
					$if_have_children_ids[] = $dept['id']; //下一级子部门 id(有下下级部门的)
				}
			}
			/*************************部门统计信息 start*******************/
			//获取字段信息
			$stages = array();//阶段信息 数据字典信息
			$stages_select='';
			$this->load->model("dictionary_model");
			$dic_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
			foreach($dic_stage as $value)
			{
				$stages[] = 'cle_stage_'.$value['id'];
			}
			$statistics_info_fields = array('conn_secs','conn_num','conn_success','transformation','new_increment','recede_num','conn_in_num','conn_out_num','conn_success_in','conn_success_out');
			$statistics_info_fields = array_merge($statistics_info_fields,$stages); //字段信息

			//获取有下下级部门的统计信息
			if(!empty($if_have_children_ids))
			{
				foreach($if_have_children_ids AS $id)
				{
					//获取下级及下下级部门的id
					$dept_children_ids = $this->department_model->get_department_children_ids($id);
					foreach($dept_children_ids AS $clild_id)
					{
						if(!empty($statistics_dept[$clild_id])&&$clild_id!=$id)
						{
							foreach($statistics_info_fields AS $field)
							{
								if(isset($statistics_dept[$clild_id][$field]))
								{
									$statistics_dept[$id][$field] = empty($statistics_dept[$id][$field]) ? 0 : $statistics_dept[$id][$field];
									$statistics_dept[$id][$field] += $statistics_dept[$clild_id][$field];
								}
							}
							unset($statistics_dept[$clild_id]);
						}

					}
					if(!empty($statistics_dept[$id]['conn_secs']))
					{
						$statistics_dept[$id]['conn_secs_timeFormate'] = timeFormate($statistics_dept[$id]['conn_secs']);
					}
				}
			}
			/*********************部门统计信息 end **************************/

			//判断部门下是否有员工
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
				$dept['deal_date'] = $deal_date;
				$dept['row'] = empty($statistics_dept[$dept['id']])?'':$statistics_dept[$dept['id']];
				$dept_tree_arr[$dept_id] -> children[] = $dept;
				//首级用的
				if($dept_id == $dept_session_id)
				{
					if(!empty($statistics_dept[$dept['id']]))
					{
						foreach($statistics_info_fields AS $field)
						{
							if(isset($statistics_dept[$dept['id']][$field]))
							{
								$statistics_dept[$dept_id][$field] = empty($statistics_dept[$dept_id][$field]) ? 0 : $statistics_dept[$dept_id][$field];
								$statistics_dept[$dept_id][$field] += $statistics_dept[$dept['id']][$field];
							}
						}
					}
				}
			}
		}
		//首级部门统计信息
		if($dept_id == $dept_session_id)
		{
			if(!empty($statistics_dept[$dept_id]['conn_secs']))
			{
				$statistics_dept[$dept_id]['conn_secs_timeFormate'] = timeFormate($statistics_dept[$dept_id]['conn_secs']);
			}
			$dept_tree_arr[$dept_id] -> row = empty($statistics_dept[$dept_id])?'':$statistics_dept[$dept_id];
		}

		//二、下一级 员工
		$user_info = $this->user_model->get_users(array('dept_id_only'=>$dept_id));
		if($user_info)
		{
			$condition_user = $condition;
			$condition_user['dept_id_only'] = $dept_id;

			//==============从后台获取员工的登录时长、就绪时长、置忙时长
			$user_ids = array();
			$agents_user = array();
			foreach($user_info as $v)
			{
				$user_ids[] = $v['user_id'];
			}

			if(!empty($user_ids))
			{
				//企业ID
				$vcc_id   = $this->session->userdata("vcc_id");
                $vcc_code = $this->session->userdata("vcc_code");
                $this->config->load('myconfig');
                $api = $this->config->item('api_wintelapi');
                $client = new Client();
                $params = array(
                    'vcc_code' => $vcc_code,
                    'info' => json_encode(array(
                        'filter' =>array(
                        'start_date' => $start_date,
                        'end_date' => $end_date
                        )
                    ))
                );

                $request = $client->post($api.'/api/report/agent/day', array(), $params);
                $response = $request->send()->json();
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return array(
                        'signin' => false,
                        'msg' => '解析结果出错，错误为【'.json_last_error().'】'
                    );
                }
                $code    = isset($response['code']) ? $response['code'] : 0;
                $message = isset($response['message']) ? $response['message'] : '';
                $data    = isset($response['data']) ? $response['data'] : array();

                if ($code == 200) {
                    foreach($data as $v)
                    {
                        $agents_user[$v['ag_id']]['login_secs'] = empty($v['login_secs']) ? 0 :$v['login_secs'];
                        $agents_user[$v['ag_id']]['ready_secs'] = empty($v['ready_secs']) ? 0 :$v['ready_secs'];
                        $agents_user[$v['ag_id']]['busy_secs'] = empty($v['busy_secs']) ? 0 :$v['busy_secs'];
                        $agents_user[$v['ag_id']]['login_secs_timeFormate'] = empty($v['login_secs']) ? 0 :timeFormate($v['login_secs']);
                        $agents_user[$v['ag_id']]['ready_secs_timeFormate'] = empty($v['ready_secs']) ? 0 :timeFormate($v['ready_secs']);
                        $agents_user[$v['ag_id']]['busy_secs_timeFormate'] = empty($v['busy_secs']) ? 0 :timeFormate($v['busy_secs']);
                    }
                }
			}
			//=======================================

			//获取坐席统计信息
			$statistics_user = $this->_get_statistics_user_info($condition_user);

			foreach($user_info as $user)
			{
				$tmp_user = new stdClass();
				$tmp_user -> id = 'user'.$user['user_id'];
				$tmp_user -> pid= $user['dept_id'];
				$tmp_user -> iconCls= 'icon-user';
				$tmp_user -> text = $user['user_name']."[".$user['user_num']."]";
				$tmp_user -> attributes = 'last';
				$tmp_user -> deal_date = $deal_date;
				$tmp_user -> user_id = $user['user_id'];
				$tmp_user -> login_secs = empty($agents_user[$user['user_id']]['login_secs'])?'0':$agents_user[$user['user_id']]['login_secs'];
				$tmp_user -> login_secs_timeFormate = empty($agents_user[$user['user_id']]['login_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['login_secs_timeFormate'];
				$tmp_user -> ready_secs = empty($agents_user[$user['user_id']]['ready_secs'])?'0':$agents_user[$user['user_id']]['ready_secs'];
				$tmp_user -> ready_secs_timeFormate = empty($agents_user[$user['user_id']]['ready_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['ready_secs_timeFormate'];
				$tmp_user -> busy_secs = empty($agents_user[$user['user_id']]['busy_secs'])?'0':$agents_user[$user['user_id']]['busy_secs'];
				$tmp_user -> busy_secs_timeFormate = empty($agents_user[$user['user_id']]['busy_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['busy_secs_timeFormate'];
				$tmp_user -> row = empty($statistics_user[$user['user_id']])?'':$statistics_user[$user['user_id']];
				$dept_tree_arr[$user['dept_id']] -> children[] = $tmp_user;
			}
		}
		//返回
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
	 * 员工部门改变 - 数据所属部门改变为新部门
	 *
	 * @param int $owner_user_id  所属员工ID
	 * @param int $owner_dept_id  新部门ID
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function user_dept_change_statistics($owner_user_id=0,$owner_dept_id=0)
	{
		if ( !$owner_user_id || !$owner_dept_id )
		{
			return FALSE;
		}
		//更新部门信息
		$this->db_write->where("user_id",$owner_user_id);
		$this->db_write->where("dept_id !=",$owner_dept_id);
		$result = $this->db_write->update("est_statistics_stage",array("dept_id"=>$owner_dept_id));
		return $result;
	}

	/**
	 * 获取导出数据
	 * @param string $start_date 处理时间start
	 * @param string $end_date 处理时间end
	 * @param int $timeFormate 时长格式
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 				[0] => 部门,
	 * 				[1] => 坐席,
	 * 				...
	 * 	)
	 * 	...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_output_statistics_data($start_date='',$end_date='',$timeFormate=1)
	{
		if(empty($start_date) || empty($end_date))
		{
			return '';
		}
		//整理检索内容 - 时间
		$condition = array();
		$condition['deal_date_start'] = $start_date;
		$condition['deal_date_end'] = $end_date;

		$dept_id = $this->session->userdata('dept_id');
		$condition['dept_id'] = $dept_id;

		//客户基本可用字段信息
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);

		$statistics_data = array();
		if(isset($client_base['cle_stage']))
		{
			$statistics_data[0] = array('部门','坐席','登录时长','就绪时长','置忙时长','通话时长','坐席呼通量','坐席未呼通量','坐席呼出量','坐席呼出接通量','坐席呼入量','坐席呼入接通量','新增客户','回访客户','退化量');
			//客户阶段
			$this->load->model("dictionary_model");
			$cle_stage =  $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
			foreach($cle_stage AS $stage)
			{
				$statistics_data[0][] = $stage['name'];
			}
		}
		else
		{
			$statistics_data[0] = array('部门','坐席','登录时长','就绪时长','置忙时长','通话时长','坐席呼通量','坐席未呼通量','坐席呼出量','坐席呼出接通量','坐席呼入量','坐席呼入接通量');
		}

		$this->load->model("user_model");
		$user_array = $this->user_model->get_users(array('dept_id'=>$dept_id),'dept_id');
		if($user_array)
		{
			//==============从后台获取员工的登录时长、就绪时长、置忙时长
			$user_ids = array();
			$agents_user = array();
			foreach($user_array as $v)
			{
				$user_ids[] = $v['user_id'];
			}
			if(!empty($user_ids))
			{
				//企业ID
				$vcc_id = $this->session->userdata("vcc_id");
                $vcc_code = $this->session->userdata("vcc_code");
                $this->config->load('myconfig');
                $api = $this->config->item('api_wintelapi');
                $client = new Client();
                $params = array(
                    'vcc_code' => $vcc_code,
                    'info' => json_encode(array(
                        'filter' =>array(
                            'start_date' => $start_date,
                            'end_date' => $end_date
                        )
                    ))
                );

                $request = $client->post($api.'/api/report/agent/day', array(), $params);
                $response = $request->send()->json();
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return array(
                        'signin' => false,
                        'msg' => '解析结果出错，错误为【'.json_last_error().'】'
                    );
                }
                $code    = isset($response['code']) ? $response['code'] : 0;
                $message = isset($response['message']) ? $response['message'] : '';
                $data    = isset($response['data']) ? $response['data'] : array();

                if ($code == 200) {
                    foreach($data as $v)
                    {
                        $agents_user[$v['ag_id']]['login_secs_timeFormate'] = empty($v['login_secs']) ? 0 :timeFormate($v['login_secs']);
                        $agents_user[$v['ag_id']]['ready_secs_timeFormate'] = empty($v['ready_secs']) ? 0 :timeFormate($v['ready_secs']);
                        $agents_user[$v['ag_id']]['busy_secs_timeFormate'] = empty($v['busy_secs']) ? 0 :timeFormate($v['busy_secs']);
                    }
                }
			}
			//=======================================
			//获取坐席统计信息
			$statistics_user = $this->_get_statistics_user_info($condition);

			foreach($user_array AS $key=>$user)
			{
				if(!empty($statistics_user[$user['user_id']]))
				{
					$statistics = $statistics_user[$user['user_id']];
					if($timeFormate == 1)
					{
						$login_secs = empty($agents_user[$user['user_id']]['login_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['login_secs_timeFormate'];
						$ready_secs = empty($agents_user[$user['user_id']]['ready_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['ready_secs_timeFormate'];
						$busy_secs = empty($agents_user[$user['user_id']]['busy_secs_timeFormate'])?'00:00:00':$agents_user[$user['user_id']]['busy_secs_timeFormate'];
						$conn_secs = $statistics['conn_secs_timeFormate'];
					}
					else
					{
						$login_secs = empty($agents_user[$user['user_id']]['login_secs'])?'0':$agents_user[$user['user_id']]['login_secs'];
						$ready_secs = empty($agents_user[$user['user_id']]['ready_secs'])?'0':$agents_user[$user['user_id']]['ready_secs'];
						$busy_secs = empty($agents_user[$user['user_id']]['busy_secs'])?'0':$agents_user[$user['user_id']]['busy_secs'];
						$conn_secs =  $statistics['conn_secs'];
					}
					$statistics_data[$key+1] = array($user['dept_name'],$user['user_name'],$login_secs,$ready_secs,$busy_secs,$conn_secs,
					$statistics['conn_success'],($statistics['conn_num']-$statistics['conn_success']),
					$statistics['conn_out_num'],$statistics['conn_success_out'],$statistics['conn_in_num'],$statistics['conn_success_in']);
					//判断是否启用客户阶段
					if(isset($client_base['cle_stage']))
					{
						$statistics_data[$key+1][] = $statistics['new_increment']; //新增量
						$statistics_data[$key+1][] = ($statistics['transformation'] - $statistics['new_increment']);//回访量
						$statistics_data[$key+1][] = $statistics['recede_num'];//退化量
						foreach($cle_stage AS $stage)
						{
							$stage['stage_id'] = 'cle_stage_'.$stage['id'];
							$statistics_data[$key+1][] = $statistics[$stage['stage_id']];
						}
					}
				}
				else
				{
					if($timeFormate == 1)
					{
						$statistics_data[$key+1] = array($user['dept_name'],$user['user_name'],'00:00:00','00:00:00','00:00:00','00:00:00','0','0','0','0','0','0');
					}
					else
					{
						$statistics_data[$key+1] = array($user['dept_name'],$user['user_name'],'0','0','0','0','0','0','0','0','0','0');
					}
					//判断是否启用客户阶段
					if(isset($client_base['cle_stage']))
					{
						$statistics_data[$key+1][] = '0'; //新增量
						$statistics_data[$key+1][] = '0';//回访量
						$statistics_data[$key+1][] = '0';//退化量
						foreach($cle_stage AS $stage)
						{
							$statistics_data[$key+1][] = 0;
						}
					}
				}
			}
		}
		return $statistics_data;
	}

	/**
	 * 删除员工同时删除 统计相关数据
	 * 
	 * @param int $owner_user_id 所属员工id
	 * @return int
	 * 
	 * @author zgx
	 */
	public function delete_statistics_by_delete_user($owner_user_id=0)
	{
		if(!$owner_user_id)
		{
			return FALSE;
		}
		$result = $this->db_write->query("DELETE FROM est_statistics_stage WHERE user_id = $owner_user_id");
		return $result;
	}

	/**
	 * 统计分析 - 工作桌面（部门）
	 * 
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 			[user_name] => 坐席名称
	 * 			[dept_name] =>部门名称
	 * 			[deal_date] => 处理时间
	 * 			[transformation] => 转换量
	 * 			[cle_visit] => 回访客户数
	 * 			[conn_num] => 通话数
	 * 			[conn_success] => 成功数
	 * 			[new_increment] => 新增客户数
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function statistics_dept_workbench()
	{
		//坐席信息
		$this->load->model("user_model");
		$user_result = $this->user_model->get_all_users();
		$user_info   = array();
		foreach ($user_result AS $user)
		{
			$user_info[$user["user_id"]]["user_name"] = $user["user_name"];
			$user_info[$user["user_id"]]["user_num"]  = $user["user_num"];
		}

		$dept_id = $this->session->userdata("dept_id");
		$this->load->model('department_model');
		$dept_children = $this->department_model->get_department_children_ids($dept_id);
		if(!empty($dept_children))
		{
			$dept_ids = 'dept_id IN ('.implode(',',$dept_children).')';
		}
		else
		{
			$dept_ids = 'dept_id='.$dept_id;
		}

		$today = date("Y-m-d");
		$query = $this->db_read->query("SELECT user_id,dept_id,deal_date,conn_num,conn_success,transformation,new_increment FROM est_statistics_stage WHERE deal_date='$today' AND ".$dept_ids." LIMIT 10");

		$statistics_dept = $query->result_array();
		$statistics = array();
		foreach($statistics_dept as $stat)
		{
			$stat['user_name'] = empty($user_info[$stat["user_id"]]["user_name"]) ? "" : $user_info[$stat["user_id"]]["user_name"];
			$stat['user_num']  = empty($user_info[$stat["user_id"]]["user_num"]) ? "" : $user_info[$stat["user_id"]]["user_num"];
			//回访客户 = 转化量 - 新增客户
			$stat["cle_visit"] = 0;
			if (  isset($stat["transformation"]) && isset($stat["new_increment"]) && $stat["transformation"] > $stat["new_increment"]  )
			{
				$stat["cle_visit"] = $stat["transformation"] - $stat["new_increment"];
			}
			$statistics[] = $stat;
		}
		return $statistics;
	}

	/**
	 * 统计桌面 - 坐席
	 * 
	 * @return array
	 * <code>
	 * array(
	 * 		[cle_visit] => 回访客户数
	 * 		[new_increment] => 新增客户数
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function statistics_workbench()
	{
		$statistics = array();
		$today = date("Y-m-d");
		$user_id = $this->session->userdata("user_id");
		$query = $this->db_read->query("SELECT * FROM est_statistics_stage WHERE deal_date='$today' AND user_id=$user_id ORDER BY transformation desc LIMIT 1");
		$statistics = $query->row_array();
		if(!empty($statistics))
		{
			//回访客户 = 转化量 - 新增客户
			$statistics["cle_visit"] = 0;
			if (  isset($statistics["transformation"]) && isset($statistics["new_increment"]) && $statistics["transformation"] > $statistics["new_increment"]  )
			{
				$statistics["cle_visit"] = $statistics["transformation"] - $statistics["new_increment"];
			}
		}
		return $statistics;
	}

	/****************************************************************************************/

	/**
	 * 获取下一级部门统计信息
	 *
     * @param int $dept_id 部门id
     * @param string $start_date 开始时间
	 * @param string $end_date 结束时间
	 * @return array
	 * <code>
	 * array(
	 * 		[dept_id] => array(
	 * 				[conn_secs] = 通话时长
	 * 				...
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	private function _get_statistics_dept_info($dept_id=0,$start_date='',$end_date='')
	{
		$statistics_dept = array();
		if(empty($dept_id) || empty($start_date) || empty($end_date))
		{
			return $statistics_dept;
		}
		//检索条件
		$wheres = $this->get_statistics_condition(array('dept_id'=>$dept_id,'deal_date_start'=>$start_date,'deal_date_end'=>$end_date));
		$where = implode(" AND ",$wheres);

		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		//阶段信息 数据字典信息
		$stages = array();
		$stages_select='';
		$this->load->model("dictionary_model");
		$dic_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		foreach($dic_stage as $value)
		{
			$value['stage_id'] = 'cle_stage_'.$value['id'];
			$stages[] = "SUM(".$value['stage_id'].") AS ".$value['stage_id'];
		}
		$stages_select = implode(',',$stages);

		$this->db_read->group_by("dept_id");
		$this->db_read->select("dept_id,SUM(conn_secs) AS conn_secs,SUM(conn_num) AS conn_num,SUM(conn_success) AS conn_success,SUM(conn_out_num) AS conn_out_num,SUM(conn_in_num) AS conn_in_num,SUM(conn_success_in) AS conn_success_in,SUM(conn_success_out) AS conn_success_out,SUM(transformation) AS transformation,SUM(new_increment) AS new_increment,SUM(recede_num) AS recede_num,$stages_select");
		$data_dept = $this->db_read->get('est_statistics_stage');
		$data_dept = $data_dept->result_array();
		$this->db_read->flush_cache();//清除缓存细信息
		foreach($data_dept AS $statistics_dept_info)
		{
			//通话时长
			$statistics_dept_info['conn_secs_timeFormate'] = timeFormate($statistics_dept_info['conn_secs']);
			$statistics_dept[$statistics_dept_info['dept_id']] = $statistics_dept_info;
		}
		return $statistics_dept;
	}

	/**
	 * 获取某部门坐席的统计信息
	 * 
	 * @param array $condition 检索内容（dept_id_only,deal_date_start,deal_date_end）
	 * @return array
	 * <code>
	 * array(
	 * 		[user_id] => array(
	 * 				[conn_secs] = 通话时长
	 * 				...
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	private function _get_statistics_user_info($condition=array())
	{
		$statistics_user = array();
		if(empty($condition))
		{
			return $statistics_user;
		}
		//检索条件
		$wheres = $this->get_statistics_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		//阶段信息 数据字典信息
		$stages = array();
		$stages_select='';
		$this->load->model("dictionary_model");
		$dic_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		foreach($dic_stage as $value)
		{
			$value['stage_id'] = 'cle_stage_'.$value['id'];
			$stages[] = "SUM(".$value['stage_id'].") AS ".$value['stage_id'];
		}
		$stages_select = implode(',',$stages);

		$this->db_read->group_by("user_id");
		$this->db_read->select("user_id,dept_id,SUM(conn_secs) AS conn_secs,SUM(conn_num) AS conn_num,SUM(conn_success) AS conn_success,SUM(conn_out_num) AS conn_out_num,SUM(conn_in_num) AS conn_in_num,SUM(conn_success_in) AS conn_success_in,SUM(conn_success_out) AS conn_success_out,SUM(transformation) AS transformation,SUM(new_increment) AS new_increment,SUM(recede_num) AS recede_num,$stages_select");
		$data_user = $this->db_read->get('est_statistics_stage');
		$data_user = $data_user->result_array();
		$this->db_read->flush_cache();//清除缓存细信息
		if (!empty($data_user))
		{
			foreach($data_user AS $statistics_info)
			{
				//通话时长
				$statistics_info['conn_secs_timeFormate'] = timeFormate($statistics_info['conn_secs']);
				$statistics_user[$statistics_info['user_id']] = $statistics_info;
			}
		}
		return $statistics_user;
	}

		/**
	 * 从后台数据库获取坐席置忙、登录操作详情
	 *
	 * @param int $user_id
	 * @param string $start_date
	 * @param string $end_date
	 * @return array
     * @author zgx
	 */
private function get_sta_detail($user_id=0,$start_date='',$end_date='')
{
	if(empty($user_id) || empty($start_date) || empty($end_date))
	{
		return false;
	}
    $detail_info = array();
    if( !$this->cache->get('sta_detail_info'.$user_id.$start_date.$end_date))
    {
        //$user_id = $this->session->userdata('user_id');
        $this->load->model('user_model');
        $user_info = $this->user_model->get_user_info($user_id);

        $vcc_id = $this->session->userdata("vcc_id");//企业ID

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_code' => $this->session->userdata("vcc_code"),
            'info' => json_encode(array(
                'filter'=>array(
                    'ag_num'=>$user_info['user_num'],
                    'start_time'=>$start_date . " 00:00:00",
                    'end_time' => $end_date . " 23:59:59",
                )
            ))
        );

        $request = $client->post($api.'/api/monitor/detaildata', array(), $params);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data    = isset($response['data']) ? $response['data'] : array();

        if ($code = 200) {
            foreach($data as $v)
            {
                $v['user_name']   = $v['ag_name'] . "[" .$v['ag_num'] ."]";
                $v['ag_sta_type'] = $v['ag_sta_type'];
                $v['start_time']  = $v['start_time'];
                $v['duration']    = timeFormate($v['duration']);
                $detail_info[]    = $v;
            }
        }

        $this->cache->save('sta_detail_info'.$user_id.$start_date.$end_date,$detail_info,600);
    }
    else
    {
        $detail_info = $this->cache->get('sta_detail_info'.$user_id.$start_date.$end_date);
    }
    return $detail_info;
	}

	/**
	 * 获取坐席置忙、登录操作详情列表信息
	 *
	 * @param array $condition 检索条件
	 * @param  $page
	 * @param string $limit
	 * @param string $sort
	 * @param string $order
	 * @return object
     * @author zgx
	 */
	public function get_sta_detail_list($condition=array(),$page='', $limit='', $sort=NULL, $order=NULL)
	{
		$responce = new stdClass();
		if(empty($condition['user_id'])||empty($condition['start_time'])||empty($condition['end_time']))
		{
			$responce -> total = 0;
			$responce -> rows = array();
			return $responce;
		}
		$detail_info = $this->get_sta_detail($condition['user_id'],$condition['start_time'],$condition['end_time']);

		$total = count($detail_info);
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}

		$start = get_list_start($total,$page,$limit);
		$responce -> rows = array();
		$i=0;
		foreach($detail_info AS $k=>$detail)
		{
			if($k>=$start && $k<($start+$limit))
			{
				$responce -> rows[$i] = $detail;
				$i++;
			}
		}
		return $responce;
	}

	//=========== 未接来电统计 ==================================
	/**
	 * 获取未接来电统计信息
	 *
	 * @param array $condition 检索条件
	 * @param string $page
	 * @param string $limit
	 * @param string $sort
	 * @param string $order
	 * @return object
     * @author zgx
	 */
	public function get_statistics_missed_calls($condition=array(),$page='',$limit='',$sort=NULL, $order=NULL)
	{
		$wheres = array();
		if(!empty($condition['start_date']))
		{
			$wheres[] = "lost_date >='".$condition['start_date']."'";
		}
		if(!empty($condition['end_date']))
		{
			$wheres[] = "lost_date <='".$condition['end_date']."'";
		}
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_statistics_lost_cdr');
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
		$cdr_data = $this->db_read->get('est_statistics_lost_cdr',$limit,$start);
		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息

		if($cdr_data)
		{
			foreach($cdr_data->result_array() AS $i=>$callrecord)
			{
				$responce -> rows[$i] = $callrecord;
			}
		}
		return $responce;
	}
}
