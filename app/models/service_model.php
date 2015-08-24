<?php
class Service_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除服务缓存
	 *
	 * @param int $serv_id  服务ID
	 * @return bool
	 */
	private function _clear_server_cache($serv_id = 0)
	{
		if ($serv_id)
		{
			$this->cache->delete('a_service_info'.$serv_id);
		}

		return true;
	}

	/**
	 * 构造客服服务的检索条件
	 *
	 * @param array $condition 检索内容
	 * @return array
	 */
	public function get_condition($condition = array() )
	{
		$wheres = array();
		/*对应客户*/
		if ( !empty($condition["cle_id"]) )
		{
			$wheres[] = "cle_id = '".$condition["cle_id"]."'";
		}
		/*主题*/
		if ( !empty($condition["search_serv_content"]) )
		{
			$wheres[] = "serv_content LIKE '%".$condition["search_serv_content"]."%'";
		}
		/*1:全部数据/2:我的数据/3:我受理的数据/4:我处理的数据*/
		$user_id = $this->session->userdata("user_id");
		$search_all_type = empty($condition["all_type"]) ? 0 : $condition["all_type"];
		switch ($search_all_type) {
			case 1:{
				//1:全部数据
				break;
			}
			case 2:{
				//2:我的数据
				$wheres[] = "(user_id = '".$user_id."' OR create_user_id = '".$user_id."' )";
				break;
			}
			case 3:{
				//3:我受理的数据
				$wheres[] = "create_user_id = '".$user_id."'";
				break;
			}
			case 4:{
				//4:转交给我的数据
				$wheres[] = "user_id = '".$user_id."'";
				break;
			}
			default:{
				if ( !isset( $condition["cle_id"] )  )
				{
					//客服服务(全部数据)
					$power_service_alldata = check_authz("kffwseralldata");
					$power_service_alldata = empty($power_service_alldata) ? 0 : $power_service_alldata;
					//没有全部数据权限时，只能看到  受理人为当前坐席的数据
					if ( $power_service_alldata == 0 )
					{
						$wheres[] = "user_id = '".$user_id."'";
					}
				}
				break;
			}
		}
		/*服务类型*/
		if ( !empty($condition["service_type"]) )
		{
			$wheres[] = "serv_type = '".$condition["service_type"]."'";
		}
		/*服务状态*/
		if ( !empty($condition["service_state"]) )
		{
			$wheres[] = "serv_state = '".$condition["service_state"]."'";
		}
		/*客户姓名*/
		if ( !empty($condition["cle_name"]) )
		{
			$wheres[] = "cle_name LIKE '%".$condition["cle_name"]."%'";
		}
		/*客户电话*/
		if ( !empty($condition["cle_phone"]))
		{
			$wheres[] = "cle_phone LIKE '%".$condition["cle_phone"]."%'";
		}
		/*联系人姓名*/
		if ( !empty($condition["con_name"]))
		{
			$wheres[] = "con_name LIKE '%".$condition["con_name"]."%'";
		}
		/*联系人电话*/
		if ( !empty($condition["con_mobile"]))
		{
			$wheres[] = "con_mobile LIKE '%".$condition["con_mobile"]."%'";
		}
		/*受理时间 - 开始*/
		if ( !empty($condition["accept_time_search_start"]))
		{
			$wheres[] = "serv_accept_time >= '".strtotime($condition["accept_time_search_start"])."'";
		}
		/*受理时间 - 结束*/
		if ( !empty($condition["accept_time_search_end"])  )
		{
			$wheres[] = "serv_accept_time <= '".strtotime($condition["accept_time_search_end"])."'";
		}
		/*受理人*/
		if ( !empty($condition["user_id_search"]) )
		{
			$wheres[] = "create_user_id = '".$condition["user_id_search"]."'";
		}
		/*处理时间 - 开始*/
		if ( !empty($condition["deal_time_search_start"]))
		{
			$wheres[] = "serv_deal_time >= '".strtotime($condition["deal_time_search_start"])."'";
		}
		/*处理时间 - 结束*/
		if ( !empty($condition["deal_time_search_end"]) )
		{
			$wheres[] = "serv_deal_time <= '".strtotime($condition["deal_time_search_end"])."'";
		}
		/*转交人/处理人*/
		if ( !empty($condition["deal_user_id_search"]) )
		{
			$wheres[] = "user_id = '".$condition["deal_user_id_search"]."'";
		}
		/*处理部门*/
		if ( !empty($condition["deal_dept_id_search"]))
		{
			$wheres[] = "dept_id = '".$condition["deal_dept_id_search"]."'";
		}
		/*内容*/
		if ( !empty($condition["serv_content"]) )
		{
			$wheres[] = "serv_content LIKE '%".$condition["serv_content"]."%'";
		}
		/*备注*/
		if ( !empty($condition["serv_remark"]) )
		{
			$wheres[] = "serv_remark LIKE '%".$condition["serv_remark"]."%'";
		}

		/*高级搜索 - 自定义字段*/
		if(!empty($condition['field_confirm_values']))
		{
			$this->load->model('field_confirm_model');
			$wheres_field = $this->field_confirm_model->get_field_confirm_condition($condition['field_confirm_values'],FIELD_TYPE_SERVICE);
			$wheres = array_merge($wheres,$wheres_field);
		}

		$wheres[] = data_permission();//数据权限

		return $wheres;
	}

	/**
	 * 得到  客服服务 列表显示数据
	 *
	 * @param array $condition
	 * <pre>
	 * 传递搜索条件的数组
	 * name：查询客户和联系人的姓名
	 * phone：查询客户和联系人的电话
	 * cle_id：关联的客户ID
	 * </pre>
	 * @param array $select 检索的字段 数组
	 * @param int $page 第几页
	 * @param int $limit 每页显示几个
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @return object
 	 * 根据select 中的字段返回结果
 	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [serv_id]=>10
	 *        [serv_type] =>  类型
	 *       … 
	 *          )
	 *     )
	 * </code>
	 * @author yan
	 * @package service_model
	 */
	public function get_service_list($condition=array(),$select=array(),$page=0, $limit=10, $sort=null, $order=null)
	{
		$wheres = $this->get_condition($condition);
		$this->db_read->start_cache();
		if(!empty($wheres))
		{
			$where = implode(" AND ",$wheres);
			$this->db_read->where($where);
		}
		$this->db_read->stop_cache();

		//处理检索字段
		$this->load->model('datagrid_confirm_model');
		if(empty($select))
		{
			$select = $this->datagrid_confirm_model->get_available_select_fields(LIST_COMFIRM_SERVICE);
		}
		//级联
		$jl_field_names = array();
		$this->load->model('field_confirm_model');
		$field_names = $this->field_confirm_model->get_jl_field_name(FIELD_TYPE_SERVICE);
		$if_get_jl_info = false;
		foreach($field_names as $field)
		{
            $name = isset($field[0]) ? $field[0] : '';
            if(in_array($name,$select))
            {
                $jl_field_names[] = $field;
                $if_get_jl_info = true;
                $select[] = $name.'_1';
                $select[] = $name.'_2';
                $select[] = $name.'_3';
            }
		}

		$responce = new stdClass();

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_service');
		$total = $total_query->row()->total;
		$responce -> total = $total;
		$responce -> rows = array();
		if($total == 0)
		{
			return $responce;
		}

		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if (!empty($sort))
		{
			$this->load->model("datagrid_confirm_model");
			$sort = $this->datagrid_confirm_model->replace_sort_field($sort);
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->select($select);
		$this->db_read->limit($limit,$start);
		$data_rows = $this->db_read->get('est_service',$limit,$start);
		$this->db_read->flush_cache();
		$data_rows = $data_rows->result_array();

		$show_user_name = $show_dept_name = $show_create_user_id = false;
		if(in_array('user_id',$select))
		{
			$show_user_name = true;
		}
		if (in_array("dept_id",$select))
		{
			$show_dept_name = true;
		}
		if(in_array('create_user_id',$select))
		{
			$show_create_user_id = true;
		}

		if( $show_user_name || $show_create_user_id )			//坐席信息
		{
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result AS $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
		}

		if($show_dept_name)	//部门信息
		{
			$this->load->model("department_model");
			$dept_result = $this->department_model->get_all_department();
			$dept_info   = array();
			foreach ($dept_result AS $value)
			{
				$dept_info[$value["dept_id"]] = $value["dept_name"];
			}
		}
		//级联
		if(count($data_rows)>0)
		{
			$jl_info = array();
			if($if_get_jl_info == true)
			{
				$jl_info = $this->field_confirm_model->get_all_jl_info(FIELD_TYPE_SERVICE);
			}
		}
		$responce -> rows = array();
		foreach($data_rows AS $i=>$data)
		{
			//转交人
			if ( $show_user_name )
			{
				$data["user_name"] = empty($user_info[$data["user_id"]]) ? "" : $user_info[$data["user_id"]];
			}
			//转交部门
			if ($show_dept_name)
			{
				$data["dept_name"] = empty($dept_info[$data["dept_id"]]) ? "" : $dept_info[$data["dept_id"]];
			}
			//受理人
			if ($show_create_user_id)
			{
				$data["create_user_name"] = empty($user_info[$data["create_user_id"]]) ? "" : $user_info[$data["create_user_id"]];
			}
			$data["serv_deal_time"] = empty($data["serv_deal_time"]) ? "" : date("Y-m-d H:i:s",$data["serv_deal_time"]);//处理时间
			$data["serv_accept_time"] = empty($data["serv_accept_time"]) ? "" : date("Y-m-d H:i:s",$data["serv_accept_time"]);//受理时间
			//级联
			if(!empty($jl_field_names))
			{
				foreach($jl_field_names as $jl_field)
				{
					if($jl_field[1]==DATA_TYPE_JL)
					{
						$data[$jl_field[0]] = '';
						$jl_name = array();
						if(!empty($jl_info[$data[$jl_field[0].'_1']]))
						{
							$data[$jl_field[0]] = $jl_info[$data[$jl_field[0].'_1']];
							if(!empty($jl_info[$data[$jl_field[0].'_2']]))
							{
								$data[$jl_field[0]] .= '-'.$jl_info[$data[$jl_field[0].'_2']];
								if(!empty($jl_info[$data[$jl_field[0].'_3']]))
								{
									$data[$jl_field[0]] .= '-'.$jl_info[$data[$jl_field[0].'_3']];
								}
							}
						}
					}
					else
					{
						$data[$jl_field[0]] = '';
						if(!empty($data[$jl_field[0].'_2']))
						{
							foreach(explode(',',$data[$jl_field[0].'_2']) as $box)
							{
								if(!empty($jl_info[$box]))
								{
									$data[$jl_field[0]] .= $jl_info[$box].'，';
								}
							}
						}
					}
				}
			}
			$responce -> rows[$i] = $data;
		}
		return $responce;
	}

	/**
	 * 添加新 客户服务  数据
	 *
	 * @param array $service_info 服务信息
	 * @return bool
	 */
	public function insert_service($service_info)
	{
		//对应客户ID
		$cle_id     = empty($service_info["cle_id"]) ? 0 : $service_info["cle_id"];
		if ($cle_id == 0 )
		{
			return FALSE;
		}
		$user_id    = $this->session->userdata("user_id");//坐席id
		$dept_id    = $this->session->userdata("dept_id");//部门id
		$serv_state = empty($service_info["serv_state"]) ? "" : $service_info["serv_state"];//服务状态
		//客户电话
		$this->load->model('phone_location_model');
		$cle_phone  =  empty($service_info["cle_phone"]) ? "" : $service_info["cle_phone"];
		if ($cle_phone)
		{
			$cle_phone   = $this->phone_location_model->remove_prefix_zero($cle_phone);
		}
		//联系人姓名、电话
		$con_name   = empty($service_info["con_name"]) ? "" : $service_info["con_name"];
		$con_mobile = empty($service_info["con_mobile"]) ? "" : $service_info["con_mobile"];
		if ($con_mobile)
		{
			$con_mobile   = $this->phone_location_model->remove_prefix_zero($con_mobile);
		}
		//转交人 - 没有转交人，默认为当前坐席
		$deal_user_id = empty($service_info["user_id"]) ? $user_id : $service_info["user_id"];
		//转交部门
		$deal_dept_id = $dept_id;
		if ( $deal_user_id && $deal_user_id != $user_id )
		{
			$this->load->model("user_model");
			$user_info    = $this->user_model->get_user_info($deal_user_id);
			$deal_dept_id = empty($user_info["dept_id"]) ? 0 : $user_info["dept_id"];
		}

		$data = array(
		"serv_type"   => empty($service_info["serv_type"]) ? "" : $service_info["serv_type"],
		"cle_id"      => $cle_id,
		"cle_name"    => empty($service_info["cle_name"]) ? "" : $service_info["cle_name"],
		"cle_phone"   => $cle_phone,
		"con_name"    => $con_name,
		"con_mobile"  => $con_mobile,
		"serv_state"  => $serv_state,
		"dept_id"     => $deal_dept_id,
		"user_id"     => $deal_user_id,
		"serv_content"  => empty($service_info["serv_content"]) ? "" : $service_info["serv_content"],
		"serv_remark"   => empty($service_info["serv_remark"]) ? "" : $service_info["serv_remark"],
		"serv_accept_time" => time(),
		"create_user_id"   => $user_id
		);

		//得到 客服服务 自定义字段
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_SERVICE);
		foreach ($confirm_field AS $item)
		{
			if($item["data_type"]==4 || $item["data_type"]==7)//级联自定义字段
			{
				if(!empty($service_info[$item["fields"]."_1"]))
				$data[$item["fields"]."_1"] = $service_info[$item["fields"]."_1"];
				if(!empty($service_info[$item["fields"]."_2"]))
				$data[$item["fields"]."_2"] = $service_info[$item["fields"]."_2"];
				if(!empty($service_info[$item["fields"]."_3"]))
				$data[$item["fields"]."_3"] = $service_info[$item["fields"]."_3"];
			}
			else
			{
				$data[$item["fields"]] = empty($service_info[$item["fields"]]) ? "" : $service_info[$item["fields"]];
			}
		}

		$result = $this->db_write->insert("est_service",$data);
		//检测新联系人 - 检测号码，存在则更新，否则插入
		if (  $result && !empty($service_info["save_new_contact"]) && $cle_id && $con_mobile  )
		{
			$this->load->model("contact_model");
			$this->contact_model->insert_update_contact($cle_id,$con_mobile,$con_name);
		}
		//信息提醒转接人
		if ($deal_user_id)
		{
			if(($serv_state!='无需处理') && $serv_state!='处理完成')
			$this->service_message($user_id,$deal_user_id);
		}

		return $result;
	}

	/**
	 * 更新 客户服务  数据
	 *
	 * @param string $service_info 服务信息
	 * @return bool
	 */
	public function update_service($service_info)
	{
		$serv_id    = empty($service_info["serv_id"]) ? 0 : $service_info["serv_id"];
		if ($serv_id == 0 || ( isset($service_info["cle_id"]) && empty($service_info["cle_id"]) )  )
		{
			return FALSE;
		}
		$serv_state = '';

		//得到 客服服务 使用字段
		$this->load->model("field_confirm_model");
		$service_filed = $this->field_confirm_model->get_available_fields(FIELD_TYPE_SERVICE);
		//添加缺少字段
		$service_filed[]["fields"] = "cle_id";

		//当前坐席信息
		$user_id    = $this->session->userdata("user_id");
		$dept_id    = $this->session->userdata("dept_id");

		//更新数据
		$this->load->model("user_model");
		$this->load->model('phone_location_model');
		$data = array();
		foreach ($service_filed AS $field_info)
		{
			if(isset($field_info["data_type"]) && ($field_info["data_type"]==4 || $field_info["data_type"]==7))//级联自定义字段
			{
				if(isset($service_info[$field_info["fields"]."_1"]))
				{
					$data[$field_info["fields"].'_1'] = $service_info[$field_info["fields"]."_1"];
					if(empty($service_info[$field_info["fields"]."_1"]))
					{
						$data[$field_info["fields"].'_2'] = '';
						$data[$field_info["fields"].'_3'] = '';
					}
				}
				if(isset($service_info[$field_info["fields"]."_2"]))
				{
					$data[$field_info["fields"].'_2'] = $service_info[$field_info["fields"]."_2"];
					if(empty($service_info[$field_info["fields"]."_2"]))
					{
						$data[$field_info["fields"].'_3'] = '';
					}
				}
				if(isset($service_info[$field_info["fields"]."_3"]))
				{
					$data[$field_info["fields"].'_3'] = $service_info[$field_info["fields"]."_3"];
				}
			}
			elseif (isset($service_info[$field_info["fields"]]))
			{
				if ( $field_info["fields"] == "con_mobile" )//联系人电话
				{
					$con_mobile = empty($service_info["con_mobile"]) ? "" : $service_info["con_mobile"];
					if ($con_mobile)
					{
						$con_mobile   = $this->phone_location_model->remove_prefix_zero($con_mobile);
						$service_info["con_mobile"] = $con_mobile;
					}
				}
				elseif ( $field_info["fields"] == "cle_phone" )//客户电话
				{
					$cle_phone = empty($service_info["cle_phone"]) ? "" : $service_info["cle_phone"];
					if ($cle_phone)
					{
						$cle_phone   = $this->phone_location_model->remove_prefix_zero($cle_phone);
						$service_info["cle_phone"] = $cle_phone;
					}
				}
				elseif ( $field_info["fields"] == "user_id" )//转接人
				{
					//转交人 - 没有转交人，默认为当前坐席
					$deal_user_id = empty($service_info["user_id"]) ? $user_id : $service_info["user_id"];
					//转交部门
					$deal_dept_id = $dept_id;
					if ( $deal_user_id && $deal_user_id != $user_id )
					{
						$user_info    = $this->user_model->get_user_info($deal_user_id);
						$deal_dept_id = empty($user_info["dept_id"]) ? 0 : $user_info["dept_id"];
					}
					$service_info["user_id"] = $deal_user_id;
					$service_info["dept_id"] = $deal_dept_id;
				}
				elseif($field_info["fields"] == "serv_state")
				{
					$serv_state = empty($service_info[$field_info["fields"]]) ? "" : $service_info[$field_info["fields"]];
				}

				$data[$field_info["fields"]] = empty($service_info[$field_info["fields"]]) ? "" : $service_info[$field_info["fields"]];
			}
		}

		//转接部门
		if ( isset($data["user_id"]) )
		{
			$data["dept_id"] = $service_info["dept_id"];

			//信息提醒转接人
			if(($serv_state!='无需处理') && $serv_state!='处理完成')
			$this->service_message($user_id,$data["user_id"]);
		}

		if (empty($data))
		{
			return TRUE;
		}
		//处理时间
		$data["serv_deal_time"] = time();
		$data["update_user_id"] = $this->session->userdata("user_id");

		$this->db_write->where("serv_id",$serv_id);
		$result = $this->db_write->update("est_service",$data);

		//检测新联系人 - 检测号码，存在则更新，否则插入
		if (  $result && !empty($service_info["save_new_contact"]) &&  ( isset($service_info["con_name"]) || isset($service_info["con_mobile"]) ) )
		{
			$current_cle_id     = empty($service_info["current_cle_id"]) ? 0 : $service_info["current_cle_id"];
			$current_con_mobile = empty($service_info["current_con_mobile"]) ? "" : $service_info["current_con_mobile"];
			if ( $current_cle_id && $current_con_mobile)
			{
				$current_con_mobile = $this->phone_location_model->remove_prefix_zero($current_con_mobile);
				$current_con_name   = empty($service_info["current_con_name"]) ? "" : $service_info["current_con_name"];

				$this->load->model("contact_model");
				$this->contact_model->insert_update_contact($current_cle_id,$current_con_mobile,$current_con_name);
			}
		}
		$this->_clear_server_cache($serv_id);

		return $result;
	}

	/**
	 * 删除 客服服务 信息
	 *
	 * @param int $serv_id  耽搁客服服务ID或者 以逗号分隔的ID字符串
	 * @return bool
	 */
	public function delete_service($serv_id=0)
	{
		if (!$serv_id)
		{
			return FALSE;
		}

		$result = $this->db_write->query("DELETE FROM est_service WHERE serv_id IN ($serv_id)");

		return $result;
	}

	/**
	 * 得到一条服务信息
	 *
	 * @param int $serv_id   服务ID
	 * @return array
	 */
	public function get_a_service_info($serv_id = 0)
	{
		if (empty($serv_id))
		{
			return array();
		}

		if( ! $this->cache->get('a_service_info'.$serv_id))
		{
			$this->db_read->where("serv_id",$serv_id);
			$result       = $this->db_read->get("est_service");
			$service_info = $result->row_array();

			if (empty($service_info))
			{
				$this->cache->delete('a_service_info'.$serv_id);
			}
			else
			{
				$this->cache->save('a_service_info'.$serv_id,$service_info,600);
			}
		}
		else
		{
			$service_info = $this->cache->get('a_service_info'.$serv_id);
		}

		return $service_info;
	}

	/**
	 * 改变坐席受理/处理客服数据的受理部门
	 *
	 * @param int $user_id  员工ID
	 * @param int $dept_id  部门ID
	 * @return bool
	 */
	public function update_service_dept_id($user_id = 0,$dept_id = 0)
	{
		if ( empty($user_id) || empty($dept_id) )
		{
			return false;
		}

		$this->db_write->where("user_id",$user_id);
		return $this->db_write->update("est_service",array("dept_id"=>$dept_id));
	}

	/**
	 * 客服服务 - 提醒转接人
	 *
	 * @param int $msg_send_user_id  发送人
	 * @param int $msg_receive_user_id  接收人
	 * @return bool
	 */
	private function service_message($msg_send_user_id=0,$msg_receive_user_id=0)
	{
		//写入消息
		$this->load->model('notice_model');
		$content = "您有一条客服信息需要处理";
		$this->notice_model->write_notice('system',$msg_receive_user_id,'',$content,0,$msg_send_user_id);
		return TRUE;
	}

	/**
	 * 根据客户id，修改客服客户信息
	 *
	 * @param int $cle_id
	 * @param array $update_data
	 * @return bool
	 * @author zgx
	 */
	public function update_service_client_by_cleid($cle_id=0,$update_data=array())
	{
		if(empty($cle_id)||empty($update_cle_data))
		{
			return false;
		}
		$update_cle_data = array();
		if(isset($update_data['cle_name']))
		{
			$update_cle_data['cle_name'] = $update_data['cle_name'];
		}
		if(isset($update_data['cle_phone']))
		{
			$update_cle_data['cle_phone'] = $update_data['cle_phone'];
		}
		if(!empty($update_cle_data))
		{
			$this->db_write->where("cle_id",$cle_id);
			$result = $this->db_write->update("est_service",$update_cle_data);
			return $result;
		}
		return false;
	}
}