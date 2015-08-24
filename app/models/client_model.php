<?php
class Client_model extends CI_Model {
	private $_can_not_update_fields = array('user_id','dept_id','cle_pingyin','cle_last_stage','cle_stage_change_time','cle_recede','cle_if_increment','cle_first_connecttime','cle_last_connecttime','con_rec_next_time','last_user_id','dployment_num','cle_executor_time','cle_creat_time','cle_creat_user_id','cle_update_time','cle_update_user_id','impt_id','cle_dial_number');

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 通过$condition返回where条件
	 *自动判断有无检索联系人，如果无检索联系人的条件 默认还是只显示主联系人
	 * @param array $condition 检索条件
	 * @return array
	 * @author zt
	 */
	public function get_client_condition($condition = array())
	{
		$wheres = array();
		$condition_contact = false;//是否检索联系人

		//关键字（基本搜索）
		if(!empty($condition['search_key']))
		{
			$condition['search_key'] = trim($condition['search_key']);
			if((is_numeric($condition['search_key']) && (strlen($condition['search_key'])>=4)))
			{
				$condition['phone'] = $condition['search_key'];
			}
			else
			{
				$condition['name'] = $condition['search_key'];
			}
		}

		/*全局查电话*/
		$phone = '';
		if(!empty($condition['phone']))
		{
			if(strlen($condition['phone'])<=6)
			{
				$wheres[] = "(est_client.cle_phone LIKE '%".$condition['phone']."%' OR est_contact.con_mobile LIKE '%".$condition['phone']."%')";
				$condition_contact = true;
			}
			else
			{
				$phone = $condition['phone'];
			}
		}

		/*全局查名称*/
		$name = '';
		if(!empty($condition['name']))
		{
			$name = $condition['name'];
		}
		//根据全局姓名 或 电话 获取客户id
		if(!empty($name) || !empty($phone))
		{
			$if_own = 0;
			if(isset($condition["user_id"]) && is_numeric($condition["user_id"]))
			{
				$if_own = 1;
			}
			$condition['cle_id'] = $this->search_client_by_name_phone($name,$phone,empty($condition['gl_all_data'])?false:$condition['gl_all_data'],$if_own);
			if(empty($condition['cle_id']))
			{
				$condition['cle_id'] = -1;
			}
		}

		/*自定义字段*/
		if(!empty($condition['field_confirm_values']))
		{
			$this->load->model("field_confirm_model");
			$wheres_field_confirm = $this->field_confirm_model->get_field_confirm_condition($condition['field_confirm_values'],FIELD_TYPE_CLIENT_CONTACT);
			$wheres = $wheres_field_confirm["wheres"];
			$condition_contact = $wheres_field_confirm["condition_contact"];
		}
		/*客户名称*/
		if (!empty($condition["cle_name"]))
		{
			if(ctype_alnum($condition["cle_name"]) && !is_numeric($condition["cle_name"]))
			{
				$wheres[] = "cle_pingyin LIKE '%".$condition["cle_name"]."%'";
			}
			else
			{
				$wheres[] = "cle_name LIKE '%".$condition["cle_name"]."%'";
			}
		}
		/*仅客户名称不带拼音*/
		if (!empty($condition["cle_name_only"]))
		{
			$wheres[] = "cle_name LIKE '%".$condition["cle_name_only"]."%'";
		}
		/*客户电话*/
		if (!empty($condition["cle_phone"]))
		{
			$wheres[] = "cle_phone LIKE '%".trim($condition["cle_phone"])."%'";
		}
		/*办公电话*/
		if (!empty($condition["cle_phone2"]))
		{
			$wheres[] = "cle_phone2 LIKE '%".trim($condition["cle_phone2"])."%'";
		}
		/*其他电话*/
		if (!empty($condition["cle_phone3"]))
		{
			$wheres[] = "cle_phone3 LIKE '%".trim($condition["cle_phone3"])."%'";
		}
		/*部门*/
		if (!empty($condition["dept_id"]))
		{
			$this->load->model("department_model");
			$dept_array = $this->department_model->get_department_children_ids($condition["dept_id"]);
			if(!empty($dept_array))
			{
				$dept_ids = implode(",",$dept_array);
				$wheres[] = "dept_id IN (".$dept_ids.")";
			}
		}
		/*所属人*/
		if (isset($condition["user_id"]) && is_numeric($condition["user_id"]))
		{
			$wheres[] = "user_id = '".$condition["user_id"]."'";
		}
		/*信息来源*/
		if(!empty($condition['cle_info_source']))
		{
			$wheres[] = "cle_info_source = '".$condition['cle_info_source']."'";
		}
		/*客户阶段*/
		if (!empty($condition["cle_stage"]))
		{
			$wheres[] = "cle_stage = '".$condition["cle_stage"]."'";
		}
		/*号码状态*/
		if (!empty($condition["cle_stat"]))
		{
			$wheres[] = "cle_stat = '".$condition["cle_stat"]."'";
		}
		/*省*/
		if (!empty($condition["cle_province_id"]))
		{
			$wheres[] = "cle_province_id = '".$condition["cle_province_id"]."'";
		}
		/*市*/
		if (!empty($condition["cle_city_id"]))
		{
			$wheres[] = "cle_city_id = '".$condition["cle_city_id"]."'";
		}
		/*详细地址*/
		if(!empty($condition['cle_address']))
		{
			$wheres[] = "cle_address LIKE '%".$condition['cle_address']."%'";
		}
		/*备注*/
		if(!empty($condition['cle_remark']))
		{
			$wheres[] = "cle_remark LIKE '%".$condition['cle_remark']."%'";
		}
		/*导入批次号*/
		if(!empty($condition['impt_id']))
		{
			$wheres[] = "impt_id = ".$condition['impt_id'];
		}
		/*通话次数*/
		if(!empty($condition['cle_dial_number']))
		{
			$wheres[] = "cle_dial_number = ".$condition['cle_dial_number'];
		}
		/*联系人名*/
		if(!empty($condition['con_name']))
		{
			$wheres[] = "con_name LIKE '%".$condition['con_name']."%'";
			$condition_contact = true;
		}
		/*联系人电话*/
		if(!empty($condition['con_mobile']))
		{
			$wheres[] = "con_mobile LIKE '".$condition['con_mobile']."%'";
			$condition_contact = true;
		}
		/*联系人邮箱*/
		if(!empty($condition['con_mail']))
		{
			$wheres[] = "con_mail LIKE '%".$condition['con_mail']."%'";
			$condition_contact = true;
		}
		/*是否退阶*/
		if(isset($condition['cle_recede']))
		{
			$wheres[] = "cle_recede = '".$condition['cle_recede']."'";
		}
		/*是否新增*/
		if(isset($condition['cle_if_increment']))
		{
			$wheres[] = "cle_if_increment = '".$condition['cle_if_increment']."'";
		}
		/*创建时间*/
		if(!empty($condition['cle_creat_time_start']))
		{
			$wheres[] = "cle_creat_time >= '".$condition['cle_creat_time_start']."'";
		}
		if(!empty($condition['cle_creat_time_end']))
		{
			$wheres[] = "cle_creat_time <= '".$condition['cle_creat_time_end']."'";
		}
		/*更新时间*/
		if(!empty($condition['cle_update_time_start']))
		{
			$wheres[] = "cle_update_time >= '".$condition['cle_update_time_start']."'";
		}
		if(!empty($condition['cle_update_time_end']))
		{
			$wheres[] = "cle_update_time <= '".$condition['cle_update_time_end']."'";
		}
		/*最近联系时间*/
		if(!empty($condition['cle_last_connecttime_start']))
		{
			$wheres[] = "cle_last_connecttime >= '".$condition['cle_last_connecttime_start']."'";
		}
		if(!empty($condition['cle_last_connecttime_end']))
		{
			$wheres[] = "cle_last_connecttime <= '".$condition['cle_last_connecttime_end']."'";
		}
		/*下次联系时间*/
		if(!empty($condition['con_rec_next_time_start']))
		{
			$wheres[] = "con_rec_next_time >= '".$condition['con_rec_next_time_start']."'";
		}
		if(!empty($condition['con_rec_next_time_end']))
		{
			$wheres[] = "con_rec_next_time <= '".$condition['con_rec_next_time_end']."'";
		}
		/*客户阶段改变时间*/
		if(!empty($condition['cle_stage_change_time_start']))
		{
			$wheres[] = "cle_stage_change_time >= '".$condition['cle_stage_change_time_start']."'";
		}
		if(!empty($condition['cle_stage_change_time_end']))
		{
			$wheres[] = "cle_stage_change_time <= '".$condition['cle_stage_change_time_end']."'";
		}
		/*首次联系时间*/
		if(!empty($condition['cle_first_connecttime']))
		{
			$wheres[] = "cle_first_connecttime = '".$condition['cle_first_connecttime']."'";
		}
		/*分配次数*/
		if(isset($condition['dployment_num']))
		{
			$wheres[] = "dployment_num = '".$condition['dployment_num']."'";
		}
		/*分配过的数据*/
		if(!empty($condition['assigned']))
		{
			//$wheres[] = "dployment_num > 0";
			$wheres[] = "cle_public_type IN(1,2)";
		}
		/*数据类型 0有所属人 1放弃 2收回 3新导入*/
		if(isset($condition['cle_public_type']))
		{
			$wheres[] = "cle_public_type = ".$condition['cle_public_type'];
		}
		/*一个或多个客户id*/
		if(!empty($condition['cle_id']))
		{
			if(!is_array($condition['cle_id']))
			{
				$cle_ids = array($condition['cle_id']);
			}
			else
			{
				$cle_ids = $condition['cle_id'];
			}
			foreach ($cle_ids as $key=>$cle_id)
			{
				$cle_ids[$key] = (int)$cle_id;
			}
			$wheres[] = "est_client.cle_id IN (".implode(',',$cle_ids).")";
		}
		/*需要去除的客户id 多个id传递数组*/
		if(!empty($condition['remove_cle_id']))
		{
			if(!is_array($condition['remove_cle_id']))
			{
				$cle_ids = array($condition['remove_cle_id']);
			}
			else
			{
				$cle_ids = $condition['remove_cle_id'];
			}
			foreach ($cle_ids as $key=>$cle_id)
			{
				$cle_ids[$key] = (int)$cle_id;
			}
			$wheres[] = "est_client.cle_id NOT IN (".implode(',',$cle_ids).")";
		}
		/*全局查询，不设置数据权限*/
		if(empty($condition['gl_all_data']) || $condition['gl_all_data'] !== true)
		{
			$wheres[] = data_permission();//数据权限
		}
		//仅检索主联系人
		$responce = new stdClass();
		$responce -> wheres =  $wheres;
		$responce -> condition_contact = $condition_contact;
		return $responce;
	}

	/**
	 * 得到客户管理列表显示数据
	 *
	 * @param array $condition
	 * <pre>
	 * 传递搜索条件的数组
	 * gl_all_data 全局查询，跳过数据权限
	 * name：查询客户和联系人的姓名
	 * phone：查询客户和联系人的电话
	 * remove_cle_id：从查询结果中剔除的cle_id。单个值或者数组都可
	 * </pre>
	 * @param array $select 检索的字段 数组
	 * @param int $page 第几页
	 * @param int $limit 每页显示几个
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @param bool $get_all_users 是否获取全部员工信息
	 * @return object
 	 * 根据select 中的字段返回结果
 	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [cle_id]=>10
 	 *        [cle_number] => 客户编号
	 *        [cle_name] =>  客户姓名
	 *       … 
	 *          )
	 *     )
	 * </code>
	 * @author zt
	 * @package clent_model
	 */
	public function get_client_list($condition=array(),$select=array(),$page=0, $limit=10, $sort=null, $order=null,$get_all_users=false)
	{
		global $CLIENT_PUBLIC_TYPE;
		$select_contact = true;// 检索联系人字段
		$join_contact = true; //连接联系人表
		$sql_join = 'AND est_contact.con_if_main = 1';
		//处理检索条件
		$where_responce = $this->get_client_condition($condition);
		$wheres = $where_responce->wheres;
		$condition_contact = $where_responce->condition_contact;
		if($condition_contact)
		{
			$sql_join = '';
		}
		//处理检索字段
		$this->load->model('datagrid_confirm_model');
		if(empty($select))
		{
			$select = $this->datagrid_confirm_model->get_available_select_fields(LIST_CONFIRM_CLIENT);
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
		$where = '';
		if(!empty($wheres))
		{
			$where = implode(" AND ",$wheres);
		}
		//====级联==========================
		$jl_field_names = array();
		$this->load->model('field_confirm_model');
		if($select_contact==true)
		{
			$field_type = FIELD_TYPE_CLIENT_CONTACT;
		}
		else
		{
			$field_type = FIELD_TYPE_CLIENT;
		}
		$field_names = $this->field_confirm_model->get_jl_field_name($field_type);
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
		//======================
		$responce = new stdClass();
		$responce -> rows = array();
		$responce -> total = 0;

		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}

		//如果是第一页，则可先不计算总数
		if($page == 1)
		{
			$start = 0;
		}
		else
		{
			$total = $this->_list_get_total($where,$join_contact,$sql_join);
			$responce -> total = $total;
			if($total == 0)
			{
				return $responce;
			}
			$start = get_list_start($total,$page,$limit);
		}

		if (!empty($sort))
		{
			$this->load->model("datagrid_confirm_model");
			$sort = $this->datagrid_confirm_model->replace_sort_field($sort);
			$this->db_read->order_by($sort,$order);
		}

		$this->db_read->where($where);
		$this->db_read->from('est_client');
		if($join_contact)
		{
			$this->db_read->join('est_contact', 'est_contact.cle_id = est_client.cle_id '.$sql_join,'LEFT');
		}
		$this->db_read->select($select);
		$this->db_read->limit($limit,$start);
		$data = $this->db_read->get();
		$data = $data->result_array();

		$show_user_name = $show_cle_creat_user_name = $show_cle_update_user_name = $show_dept_name = false;
		if(in_array('user_id',$select))
		{
			$show_user_name = true;
		}
		if(in_array('cle_creat_user_id',$select))
		{
			$show_cle_creat_user_name = true;
		}
		if(in_array('cle_update_user_id',$select))
		{
			$show_cle_update_user_name = true;
		}
		if(in_array('dept_id',$select))
		{
			$show_dept_name = true;
		}

		if($show_user_name || $show_cle_creat_user_name || $show_cle_update_user_name)			//坐席信息
		{

			$this->load->model("user_model");
			if($get_all_users==true)
			{
				$user_result = $this->user_model->get_all_users_without_dept();
			}
			else
			{
				$user_result = $this->user_model->get_all_users();
			}
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
		//级联信息
		if(count($data)>0)
		{
			$jl_info = array();
			if($if_get_jl_info == true)
			{
				$jl_info = $this->field_confirm_model->get_all_jl_info($field_type);
			}
		}
		$i = 0;
		foreach($data AS $i=>$client_info)
		{
			if($show_user_name)//数据所属人
			{
				$client_info["user_name"]   = empty($user_info[$client_info["user_id"]]) ? "" : $user_info[$client_info["user_id"]];
			}
			if($show_cle_creat_user_name)//创建人
			{
				$client_info["cle_creat_user_name"]   = empty($user_info[$client_info["cle_creat_user_id"]]) ? "" : $user_info[$client_info["cle_creat_user_id"]];
			}
			if($show_cle_update_user_name)//更新人
			{
				$client_info["cle_update_user_name"]   = empty($user_info[$client_info["cle_update_user_id"]]) ? "" : $user_info[$client_info["cle_update_user_id"]];
			}
			if($show_dept_name)//所属部门
			{
				$client_info["dept_name"]   = empty($dept_info[$client_info["dept_id"]]) ? "公司数据" : $dept_info[$client_info["dept_id"]];
			}
			//级联
			if(!empty($jl_field_names))
			{
				foreach($jl_field_names as $jl_field)
				{
					if($jl_field[1]==DATA_TYPE_JL)
					{
						$client_info[$jl_field[0]] = '';
						$jl_name = array();
						if(!empty($jl_info[$client_info[$jl_field[0].'_1']]))
						{
							$client_info[$jl_field[0]] = $jl_info[$client_info[$jl_field[0].'_1']];
							if(!empty($jl_info[$client_info[$jl_field[0].'_2']]))
							{
								$client_info[$jl_field[0]] .= '-'.$jl_info[$client_info[$jl_field[0].'_2']];
								if(!empty($jl_info[$client_info[$jl_field[0].'_3']]))
								{
									$client_info[$jl_field[0]] .= '-'.$jl_info[$client_info[$jl_field[0].'_3']];
								}
							}
						}
					}
					else
					{
						$client_info[$jl_field[0]] = '';
						if(!empty($client_info[$jl_field[0].'_2']))
						{
							foreach(explode(',',$client_info[$jl_field[0].'_2']) as $box)
							{
								if(!empty($jl_info[$box]))
								{
									$client_info[$jl_field[0]] .= $jl_info[$box].'，';
								}
							}
						}
					}
				}	
			}
			$client_info["cle_public_type"]   = !isset($CLIENT_PUBLIC_TYPE[$client_info["cle_public_type"]]) ? "" : $CLIENT_PUBLIC_TYPE[$client_info["cle_public_type"]];
			$responce -> rows[$i]           = $client_info;
			$i ++;
		}
		//用于某些时候少执行一次sql语句
		if($responce -> total == 0)
		{
			if($i == $limit)
			{
				$responce -> total = $this->_list_get_total($where,$join_contact,$sql_join);
			}
			else
			{
				$responce -> total = $i;
			}

		}
		return $responce;
	}

	private function _list_get_total($where,$join_contact,$sql_join)
	{
		$this->db_read->where($where);
		$this->db_read->from('est_client');
		if($join_contact)
		{
			$this->db_read->join('est_contact', 'est_contact.cle_id = est_client.cle_id '.$sql_join,'LEFT');
		}
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		return $total;
	}

	/**
	 * 插入一条新客户数据，可同时插入一条主联系人信息
	 *
	 * @param array $client_info 客户、主联系人（可选）的信息数组
	 * @return bool 是否成功
	 * @author zt
	 */
	public function insert_client($client_info=array())
	{
		$user_id = $this->session->userdata("user_id");
		$dept_id = $this->session->userdata("dept_id");

		$cle_name    = empty($client_info["cle_name"]) ? "" : replace_illegal_string($client_info["cle_name"]);
		//拼音处理
		if(ctype_alnum($cle_name))
		{
			$cle_pingyin = $cle_name;
		}
		else if(preg_match('/^[0-9][0-9]+/',$cle_name,$first_str))
		{
			$last_str = explode($first_str[0],$cle_name);
			$cle_pingyin = $first_str[0].pinyin($last_str[1],TRUE);
		}
		else
		{
			$cle_pingyin = pinyin($cle_name,TRUE);
		}

		$cle_data = array(
		"cle_name"        => $cle_name,
		"cle_pingyin"     => $cle_pingyin,
		"cle_phone"       => (!empty($client_info["cle_phone"])&&is_numeric($client_info["cle_phone"]))?$client_info["cle_phone"]:'',
		"cle_phone2"      => (!empty($client_info["cle_phone2"])&&is_numeric($client_info["cle_phone2"]))?$client_info["cle_phone2"]:'',
		"cle_phone3"      => (!empty($client_info["cle_phone3"])&&is_numeric($client_info["cle_phone3"]))?$client_info["cle_phone3"]:'',
		"cle_address"     => empty($client_info["cle_address"]) ? "" : $client_info["cle_address"],
		"cle_info_source" => empty($client_info["cle_info_source"]) ?  "": $client_info["cle_info_source"],
		"cle_stage"       => empty($client_info["cle_stage"]) ?  "": $client_info["cle_stage"],
		"cle_stat"        => empty($client_info["cle_stat"]) ? (empty($client_info["cle_stage"]) ?"未拨打":"呼通"): $client_info["cle_stat"],
		"cle_province_name"  => empty($client_info["cle_province_name"]) ? "" : $client_info["cle_province_name"],
		"cle_province_id"    => empty($client_info["cle_province_id"]) ? 0 : $client_info["cle_province_id"],
		"cle_city_id"     => empty($client_info["cle_city_id"]) ? 0 : $client_info["cle_city_id"],
		"cle_city_name"   => empty($client_info["cle_city_name"]) ? "" : $client_info["cle_city_name"],
		"cle_remark"      => empty($client_info["cle_remark"]) ? "" : $client_info["cle_remark"],
		"user_id" => $user_id,
		"dept_id" => $dept_id,
		"cle_executor_time" => date("Y-m-d H:i:s"),
		"cle_creat_time"    => date("Y-m-d"),
		"cle_creat_user_id" => $user_id
		);

		$con_data = array(
		"con_name"   => empty($client_info["con_name"]) ? "" : $client_info["con_name"],
		"con_mobile" => (!empty($client_info["con_mobile"])&&is_numeric($client_info["con_mobile"]))?$client_info["con_mobile"]:'',
		"con_mail"   => empty($client_info["con_mail"]) ? "" : $client_info["con_mail"],
		"con_remark" => empty($client_info["con_remark"]) ? "" : $client_info["con_remark"]
		);

		//得到自定义字段( 客户 +  联系人)
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_CLIENT_CONTACT);
		foreach ($confirm_field AS $field)
		{
			$tmp_field_name = $field["fields"];
			if($field["data_type"]==DATA_TYPE_JL || $field["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if ($field["field_type"] == FIELD_TYPE_CLIENT)
				{
					if(!empty($client_info[$tmp_field_name.'_1']))
					$cle_data[$tmp_field_name.'_1'] = $client_info[$tmp_field_name.'_1'];
					if(!empty($client_info[$tmp_field_name.'_2']))
					$cle_data[$tmp_field_name.'_2'] = $client_info[$tmp_field_name.'_2'];
					if(!empty($client_info[$tmp_field_name.'_3']))
					$cle_data[$tmp_field_name.'_3'] = $client_info[$tmp_field_name.'_3'];
				}
				elseif ($field["field_type"] == FIELD_TYPE_CONTACT)
				{
					if(!empty($client_info[$tmp_field_name.'_1']))
					$con_data[$tmp_field_name.'_1'] = $client_info[$tmp_field_name.'_1'];
					if(!empty($client_info[$tmp_field_name.'_2']))
					$con_data[$tmp_field_name.'_2'] = $client_info[$tmp_field_name.'_2'];
					if(!empty($client_info[$tmp_field_name.'_3']))
					$con_data[$tmp_field_name.'_3'] = $client_info[$tmp_field_name.'_3'];
				}
			}
			else if(!empty($client_info[$tmp_field_name]))
			{
				if ($field["field_type"] == FIELD_TYPE_CLIENT)
				{
					$cle_data[$tmp_field_name] = $client_info[$tmp_field_name];
				}
				elseif ($field["field_type"] == FIELD_TYPE_CONTACT)
				{
					$con_data[$tmp_field_name] = $client_info[$tmp_field_name];
				}
			}
		}
		$result = $this->db_write->insert("est_client",$cle_data);
		if (!$result)
		{
			return FALSE;
		}
		$cle_id = $this->db_write->insert_id();
		//检查联系人信息是否为空
		$con_data = array_diff($con_data,array(""));
		if (!empty($con_data))
		{
			$con_data["cle_id"] = $cle_id;
			$con_data["con_if_main"] = 1;
			$result = $this->db_write->insert("est_contact",$con_data);
		}
		//添加客户信息，阶段信息存在，转化量/新增客户量 加1
		if (!empty($client_info["cle_stage"]))
		{
			$this->load->model("statistics_model");
			$this->statistics_model->update_statistics_stage($client_info["cle_stage"],$cle_id);
		}
		//日志
		$lon_contact = "添加客户";
		$this->load->model("log_model");
		$this->log_model->write_client_log($lon_contact,$cle_id);
		return $cle_id;
	}

	/**
	 * 删除客户信息 会同时删除和客户相关的联系人
	 *
	 * @param int|array $cle_id  单个客户id传客户id 多个客户传的id的数组
	 * @return bool
	 * @author zt
	 * @package clent_model
	 */
	function delete_client($cle_id)
	{
		if (empty($cle_id))
		{
			return FALSE;
		}
		$cle_ids = array();
		if(is_array($cle_id))
		{
			foreach ($cle_id as $cle_id_item)
			{
				$cle_ids[] = (int)$cle_id_item;
			}
		}
		else
		{
			$cle_ids = array($cle_id);
		}
		$this->db_write->start_cache();
		$this->db_write->where_in('cle_id',$cle_ids);
		$this->db_write->stop_cache();

		$this->db_write->select('cle_id,cle_name');
		$cle_info_query = $this->db_write->get('est_client');
		$cle_infos = $cle_info_query->result_array();
		$this->db_write->delete(array('est_client','est_contact'));

		$_relative = '';
		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action) || in_array('kffw',$action))
		{
			//获取系统配置参数
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			//删除客户时，相应数据处理 1不作处理 2一同删除
			if($config_info["delete_client_relative"]==2)
			{
				$_relative = ',其他模块与该客户相关的数据也一同删除';
				$this->db_write->delete(array('est_service','est_order'));
			}
		}
		$this->db_write->flush_cache();
		//操作日志
		$content    = array();
		$log_cle_id = array();
		foreach ($cle_infos AS $cle_info)
		{
			$content[]    = "删除客户,".$_relative."|".$cle_info["cle_name"];
			$log_cle_id[] = $cle_info["cle_id"];
		}
		$this->load->model("log_model");
		$this->log_model->write_client_log($content,$log_cle_id);
		return true;
	}

	/**
	 * 放弃客户数据 只能放弃自己的数据
	 *
	 * @param int/array $cle_id  单个客户id传客户id 多个客户传的id的数组
	 * @param int $user_id 员工id
     * @param string $content_str 日志信息
	 * @return bool/int affected_rows 放弃数据的条数
	 * @author zt
	 */
	public function release_client($cle_id=0,$user_id=0,$content_str='坐席放弃客户')
	{
		if (empty($cle_id) && empty($user_id))
		{
			return false;
		}
		//操作日志
		$content    = array();
		$log_cle_id = array();

		$wheres = array();
		//客户id
		if(!empty($cle_id))
		{
			$cle_ids = array();
			if(is_array($cle_id))
			{
				foreach ($cle_id as $cle_id_item)
				{
					$cle_ids[] = (int)$cle_id_item;
				}
			}
			else
			{
				$cle_ids = array($cle_id);
			}
			$wheres[] = "cle_id IN(".implode(',',$cle_ids).")";
		}
		//用户id
		if(empty($user_id))
		{
			$user_id = $this->session->userdata("user_id");
		}
		$wheres[] = "user_id = ".$user_id;

		if(!empty($wheres))
		{
			$where = implode(' AND ',$wheres);
			$this->db_read->where($where);
			$this->db_read->select("cle_id,cle_name");
			$client_info_query = $this->db_read->get('est_client');
			$client_info = $client_info_query->result_array();
			$update_cle_ids = array();
			foreach ($client_info AS $value)
			{
				$update_cle_ids[] = $value["cle_id"];
				$content[]    = $content_str;
				$log_cle_id[] = $value["cle_id"];
			}
			if(!empty($update_cle_ids))
			{
				$cle_ids_str = implode(',',$update_cle_ids);
				$this->db_write->query("UPDATE est_client SET user_id='0',cle_update_time=CURDATE(),cle_update_user_id='$user_id',last_user_id=CONCAT(IFNULL(last_user_id,''),',','$user_id'),cle_public_type=1 WHERE user_id=".$user_id." AND cle_id IN (".$cle_ids_str.") AND cle_public_type!=1");
				$affected_rows =  $this->db_write->affected_rows();//放弃总数

				$role_action = $this->session->userdata('role_action');
				$action = explode(',',$role_action);
				if(in_array('ddgl',$action))
				{
					//订单所属人改变
					$this->load->model('order_model');
					$this->order_model->update_order_user_id_when_client_resource($update_cle_ids,0);
				}

				//写入日志
				$this->load->model("log_model");
				$this->log_model->write_client_log($content,$log_cle_id);
				return $affected_rows;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * 得到客户信息返 回所有客户信息 包括所属人姓名、所属部门名称、
	 *
	 * @param int $cle_id  客户ID
	 * @return array 客户信息的数组
	 * @author zt
	 */
	public function get_client_info($cle_id = 0)
	{
		if(empty($cle_id))
		{
			return false;
		}

		$this->db_read->where("cle_id",$cle_id);
		$query = $this->db_read->get("est_client");
		$client_info =  $query->row_array();
		if($client_info)
		{
			//所属人
			if (!empty($client_info["user_id"]))
			{
				$this->load->model("user_model");
				$user_info = $this->user_model->get_user_info($client_info["user_id"]);

				$client_info["user_name"] = empty($user_info["user_name"]) ? "" : $user_info["user_name"];
				$client_info["dept_name"] = empty($user_info["dept_name"]) ? "" : $user_info["dept_name"];
			}
			else
			{
				$client_info["user_name"] = "";
			}
			//部门
			if (!empty($client_info["dept_id"]))
			{
				$this->load->model("department_model");
				$dept_info = $this->department_model->get_department_info($client_info["dept_id"]);

				$client_info["dept_name"] = empty($dept_info["dept_name"]) ? "" : $dept_info["dept_name"];
			}
			else
			{
				$client_info["dept_name"] = "公司数据";
			}
		}
		return $client_info;
	}

	/**
	 * 得到客户可编辑的字段
	 *@author zt
	 */
	public function get_update_available_fields()
	{
		//得到客户使用字段
		$this->load->model("field_confirm_model");
		$available_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CLIENT);
		$fields = array();
		foreach ($available_fields as $field)
		{
			if(!in_array($field['fields'],$this->_can_not_update_fields))
			{
				$fields[] = $field;
			}
		}
		return $fields;
	}
	/**
	 * 更新客户数据。更新客户数据同时可能会改变当天的统计数据
	 *
	 * @param int $cle_id 客户id
	 * @param array $cle_info 需要改变的客户信息的数组
     * @return bool
	 * @author zt
	 */
	public function update_client($cle_id = 0,$cle_info = array())
	{
		if (empty($cle_id) || empty($cle_info))
		{
			return false;
		}

		$update_available_fileds = $this->get_update_available_fields();
		$original = array();//修改前的客户信息
		$cle_stage_flag = 0 ;//标记阶段是否改变
		$new_cle_stage  = "";//新阶段信息
		$user_id = $this->session->userdata("user_id");

		$update_data    = array();//需要更新的客户信息
		//日志
		$log_str        = "编辑客户";
		foreach ($update_available_fileds AS $field_info)
		{
			$field_name = $field_info["fields"];
			if($field_info["data_type"]==DATA_TYPE_JL || $field_info["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if(isset($cle_info[$field_name."_1"]))
				{
					$update_data[$field_name.'_1'] = $cle_info[$field_name."_1"];
					if(empty($cle_info[$field_name."_1"]))
					{
						$update_data[$field_name.'_2'] = '';
						$update_data[$field_name.'_3'] = '';
					}
				}
				if(isset($cle_info[$field_name."_2"]))
				{
					$update_data[$field_name.'_2'] = $cle_info[$field_name."_2"];
					if(empty($cle_info[$field_name."_2"]))
					{
						$update_data[$field_name.'_3'] = '';
					}
				}
				if(isset($cle_info[$field_name."_3"]))
				{
					$update_data[$field_name.'_3'] = $cle_info[$field_name."_3"];
				}
			}
			elseif ( isset($cle_info[$field_name]))
			{
				if ($field_name == 'cle_name')//客户名称
				{
					$cle_name = empty($cle_info["cle_name"]) ? "" : replace_illegal_string($cle_info["cle_name"]);
					//拼音处理
					if(ctype_alnum($cle_name))
					{
						$cle_pingyin = $cle_name;
					}
					else if(preg_match('/^[0-9][0-9]+/',$cle_name,$first_str))
					{
						$last_str = explode($first_str[0],$cle_name);
						$cle_pingyin = $first_str[0].pinyin($last_str[1],TRUE);
					}
					else
					{
						$cle_pingyin = pinyin($cle_name,TRUE);
					}

					$update_data["cle_name"]    = $cle_name;
					$update_data["cle_pingyin"] = $cle_pingyin;
				}
				elseif ($field_name == 'cle_phone' || $field_name == 'cle_phone2' || $field_name == 'cle_phone3')	//客户电话
				{
					$cle_phone   = $cle_info[$field_name];
					if ($cle_phone)
					{
						$cle_phone   = is_numeric($cle_phone)?$cle_phone:'';
					}
					$update_data[$field_name] = $cle_phone;

					//获取系统配置参数
					$this->load->model("system_config_model");
					$config_info = $this->system_config_model->get_system_config();
					//过滤电话号码重复： 0不过滤，允许重复；1不允许重复，过滤号码
					$phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
					if ($phone_ifrepeat == 1 && $cle_phone )
					{
						$cle_ids = $this->search_client_by_name_phone("",$cle_phone,true);
						$cle_more = array_diff($cle_ids,array($cle_id));
						if (!empty($cle_more))
						{
							return 2;
						}
					}
				}
				elseif ($field_name == 'cle_stage')//客户阶段
				{

					$new_cle_stage  = $cle_info["cle_stage"];//新阶段信息
					$cle_stage_flag = 1;//标记阶段是否改变
					$original       = $this->get_client_info($cle_id);//修改前的客户信息
					$org_cle_stage  = $original["cle_stage"];
					$cle_stage_change_time = $original["cle_stage_change_time"];//上一次阶段改变时间
					$current_date          = date("Y-m-d");

					$update_data["cle_stage"] = $new_cle_stage;
					if ($cle_stage_change_time == $current_date)
					{
						$update_data["cle_last_stage"]    = $original["cle_last_stage"].",".$org_cle_stage;
					}
					else
					{
						$update_data["cle_last_stage"]    = $org_cle_stage;
						$update_data["cle_recede"]        = 0;//退阶
						$original["cle_recede"]           = 0;
						$update_data["cle_if_increment"]  = 0;//新增
						$original["cle_if_increment"]     = 0;
					}
					$update_data["cle_stage_change_time"] = date("Y-m-d");
				}
				else
				{
					$update_data[$field_name] = $cle_info[$field_name];
				}
				//日志
				$log_str .= "|".$field_info["name"].":".$cle_info[$field_name];
			}
		}
		if(isset($cle_info["cle_province_id"]))
		{
			$update_data['cle_province_id'] = empty($cle_info["cle_province_id"]) ? 0 : $cle_info["cle_province_id"];//省id
		}
		if(isset($cle_info["cle_city_id"]))
		{
			$update_data['cle_city_id'] = empty($cle_info["cle_city_id"]) ? 0 : $cle_info["cle_city_id"];//市id
		}
		//更新数据
		if (!empty($update_data))
		{
			$update_data["cle_update_time"]    = date("Y-m-d");
			$update_data["cle_update_user_id"]    = $user_id;

			$this->db_write->where("cle_id",$cle_id);
			$result = $this->db_write->update("est_client",$update_data);
			if ($result)
			{
				if($cle_stage_flag == 1)
				{
					//统计 编辑客户，阶段发生变化
					$this->load->model("statistics_model");
					$this->statistics_model->edit_statistics_stage($original,$new_cle_stage);
				}
			}
			$this->load->model("log_model");
			$this->log_model->write_client_log($log_str,$cle_id);
		}
		return true;
	}

	/**
	 * 通过姓名和电话检索客户信息 同时检索联系人和客户的
	 *
	 * @param string $name  名称
	 * @param string $phone  电话
	 * @param bool $global_search   true:检索所有客户信息  false:检索数权限内的数据
	 * @param int $if_own 0根据权限来 1自己的
	 * @return array 成功返回客户ID数组
	 * @author zt
	 */
	public function search_client_by_name_phone($name = "",$phone = "",$global_search = false,$if_own=0)
	{
		if (empty($name) && empty($phone) )
		{
			return false;
		}
		$wheres_cle = array();//客户表检索条件
		$wheres_con = array();//联系人表检索条件

		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];//是否使用联系人模块

		//姓名
		if(!empty($name))
		{
			if(ctype_alnum($name) && !is_numeric($name))
			{
				$wheres_cle[] = "cle_pingyin LIKE '%".$name."%'";
			}
			else
			{
				$wheres_cle[] = "cle_name LIKE '%".$name."%'";
			}
			if($power_use_contact!=1)
			{
				$wheres_con[] = "con_name LIKE '%".$name."%'";
			}
		}
		//电话
		if(!empty($phone))
		{
			if(strlen($phone)>=11)
			{
				$wheres_cle[] = "cle_phone = '".$phone."' OR cle_phone2 = '".$phone."' OR cle_phone3 = '".$phone."'";
				if($power_use_contact!=1)
				{
					$wheres_con[] = "con_mobile = '".$phone."'";
				}
			}
			else
			{
				$wheres_cle[] = "cle_phone LIKE '%".$phone."%' OR cle_phone2 LIKE '%".$phone."%' OR cle_phone3 LIKE '%".$phone."%'";
				if($power_use_contact!=1)
				{
					$wheres_con[] = "con_mobile LIKE '%".$phone."%'";
				}
			}
		}
		/*全局查询，不设置数据权限*/
		if(empty($global_search) || $global_search !== true)
		{
			$wheres_cle[] = data_permission();//数据权限
		}
		if($if_own==1)
		{
			$wheres_cle[] = "user_id = ".$this->session->userdata('user_id');
		}

		$cle_ids    = array();
		if(!empty($wheres_cle))
		{
			if(!empty($wheres_con))
			{
				$data_query = $this->db_read->query("(SELECT cle_id FROM est_client WHERE ".implode(" AND ",$wheres_cle).") UNION (SELECT cle_id FROM est_contact WHERE ".implode(" AND ",$wheres_con).") ORDER BY `cle_id` DESC LIMIT 10");
			}
			else
			{
				$data_query = $this->db_read->query("SELECT cle_id FROM est_client WHERE ".implode(" AND ",$wheres_cle)." ORDER BY `cle_id` DESC LIMIT 10");
			}
			$data =  $data_query->result_array();
			foreach ($data AS $value)
			{
				if(!in_array($value["cle_id"],$cle_ids))
				{
					$cle_ids[] = $value["cle_id"];
				}
			}
		}
		return $cle_ids;
	}


	/**
	 * 得到指定坐席所属的客户数据总数
     *
	 * @param int $user_id
	 * @return int
	 * @author zt
	 */
	public function get_user_client_total_num($user_id = 0)
	{
		if(empty($user_id))
		{
			return false;
		}

		$total = 0;
		$this->db_read->select('count(*) as total',false);
		$this->db_read->where("user_id",$user_id);
		$this->db_read->from("est_client");
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		return $total;
	}

	/**
	 * 判断当前用户有没有权限操作指定客户数据
	 *
	 * @param int $cle_id 客户id
	 * @param int $cle_user_id 客户所有人id（可选参数）
	 * @param int $cle_dept_id 客户所在部门id（可选参数）
	 * @return bool true表示可以操作 false表示不能操作
	 * @author zgx
	 */
	public function check_client_permission($cle_id,$cle_user_id=0,$cle_dept_id=0)
	{
		if(empty($cle_id))
		{
			return false;
		}
		$cle_info =  $this->get_client_info($cle_id);
		if(empty($cle_info))
		{
			sys_msg('该客户已被删除');
		}
		$role_type = $this->session->userdata('role_type');
		switch ($role_type)
		{
			case DATA_PERSON:
				$user_id = $this->session->userdata('user_id');
				if(empty($cle_user_id))
				{
					$cle_user_id = $cle_info['user_id'];
				}
				if($cle_user_id == $user_id)
				{
					return true;
				}
				else
				{
					return false;
				}
				break;
			case DATA_DEPARTMENT:
				$dept_id = $this->session->userdata('dept_id');
				if(empty($cle_dept_id))
				{
					$cle_dept_id = $cle_info['dept_id'];
				}
				$this->load->model('department_model');
				$dept_ids = $this->department_model->get_department_children_ids($dept_id);
				if(in_array($cle_dept_id,$dept_ids))
				{
					return true;
				}
				else
				{
					return false;
				}
				break;
			default:
				break;
		}
		return false;
	}

	/**
	 * 判断当前登陆坐席客户数量是否超过客户限制 的（客户限制 0代表无限制  客户限制数不包括 终结客户）
	 * @param int $user_id 员工id
	 * @return bool (TRUE 没超过客户限制 FALSE 超过客户限制)
	 * @author zgx
	 */
	public function check_client_amount($user_id=0)
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
			$user_total = $this->get_user_client_total_without_success($user_id);
			if($user_total >= $system['client_amount'])
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}

	/**
	 * 获取某坐席现有客户数（除成交客户数）
	 *
	 * @param int $user_id
	 * @return int/bool
	 */
	public function get_user_client_total_without_success($user_id=0)
	{
		if(empty($user_id))
		{
			return false;
		}
		$total = 0;
		$wheres = array();
		$wheres[] = "user_id = ".$user_id;//所属人
		//等级为终结 的 客户阶段
		$this->load->model('client_type_model');
		$stages = $this->client_type_model->get_stage_by_cle_type(ZJKH);
		if($stages)
		{
			if(count($stages)==1)
			{
				$wheres[] = "cle_stage != '".$stages[0]."'";
			}
			else
			{
				$wheres[] = "cle_stage NOT IN('".implode("','",$stages)."')";
			}
		}
		if(!empty($wheres))
		{
			$where = implode(" AND ",$wheres);
			$this->db_read->where($where);
		}
		$this->db_read->select('count(*) as total',false);
		$this->db_read->from("est_client");
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		return $total;
	}

}