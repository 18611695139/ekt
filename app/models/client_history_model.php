<?php
class Client_history_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
     * 根据客户id把相应客户信息放入历史信息表中
     * @param int $cle_id
     * @return bool
     */
	public function move_client_to_history($cle_id=0)
	{
		if(empty($cle_id))
		{
			return false;
		}

		$fields = array(); //两表都存在的字段
		$cle_ids = array(); //客户id
		$today = date('Y-m-d');

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

		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();

		if(isset($config_info['use_history'])&&($config_info['use_history']==1)&&!empty($config_info['created_day']))
		{
			$X_day = date("Y-m-d",strtotime("-".$config_info['created_day']." day",strtotime($today)));//X内没联系过
			//标志终结的客户阶段
			$this->load->model('client_type_model');
			$cle_stages = $this->client_type_model->get_stage_by_cle_type(3);
			if(!empty($cle_stages))
			{
				$this->db_read->where_in('cle_stage',$cle_stages);
			}
			//查询客户信息
			$this->db_read->where_in('cle_id',$cle_ids);
			$this->db_read->where("cle_last_connecttime <",$X_day);
			$this->db_read->where("cle_update_time <",$X_day);
			$this->db_read->where("cle_creat_time <",$X_day);
			$query = $this->db_read->get('est_client');
			$client_info = $query->result_array();
			if(count($client_info)>0)
			{
				$client_fields = $this->db_read->list_fields('est_client');//客户表字段
				$history_fields = $this->db_read->list_fields('est_client_history');//历史表字段
				foreach($history_fields as $hf)
				{
					if(in_array($hf,$client_fields))
					{
						$fields[] = $hf;
					}
				}

				if(!empty($fields))
				{
					$insert_array = array();
					$move_cle_id = array();
					foreach($client_info as $key=>$value)
					{
						$move_cle_id[] = $value['cle_id'];
						foreach($fields as $field)
						{
							$insert_array[$key][$field] = $value[$field];
						}
					}
					if(!empty($insert_array))
					{
						//批量插入历史数据
						$this->db_write->insert_batch('est_client_history',$insert_array);
						//删除客户表里数据
						$this->db_write->where_in('cle_id',$move_cle_id);
						$this->db_write->delete('est_client');
					}
					return $move_cle_id;
				}
			}
		}
		return false;
	}



	/**
	 * 得到客户历史列表显示数据
	 */
	public function get_client_history_list($condition=array(),$select=array(),$page=0, $limit=10, $sort=null, $order=null,$get_all_users=false)
	{
		$select_contact = true;// 检索联系人字段
		$join_contact = true; //连接联系人表
		$sql_join = 'AND est_contact.con_if_main = 1';
		//处理检索条件
		$this->load->model('client_model');
		$where_responce = $this->client_model->get_client_condition($condition);
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
		foreach($select as $i=>$s)
		{
			if($s == 'est_client.cle_id')
			{
				$select[$i] = 'est_client_history.cle_id';
			}
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
			if(in_array($field,$select))
			{
				$jl_field_names[] = $field;
				$if_get_jl_info = true;
				$select[] = $field.'_1';
				$select[] = $field.'_2';
				$select[] = $field.'_3';
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
		if(!empty($where))
		{
			$this->db_read->where($where);
		}
		$this->db_read->from('est_client_history');
		if($join_contact)
		{
			$this->db_read->join('est_contact', 'est_contact.cle_id = est_client_history.cle_id '.$sql_join,'LEFT');
		}
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if($total == 0)
		{
			return $responce;
		}
		$start = get_list_start($total,$page,$limit);


		if (!empty($sort))
		{
			$this->load->model("datagrid_confirm_model");
			$sort = $this->datagrid_confirm_model->replace_sort_field($sort);
			$this->db_read->order_by($sort,$order);
		}

		if(!empty($where))
		{
			$this->db_read->where($where);
		}
		$this->db_read->from('est_client_history');
		if($join_contact)
		{
			$this->db_read->join('est_contact', 'est_contact.cle_id = est_client_history.cle_id '.$sql_join,'LEFT');
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
			$user_result = $this->user_model->get_all_users_without_dept();
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
					$client_info[$jl_field] = '';
					$jl_name = array();
					if(!empty($jl_info[$client_info[$jl_field.'_1']]))
					{
						$client_info[$jl_field] = $jl_info[$client_info[$jl_field.'_1']];
						if(!empty($jl_info[$client_info[$jl_field.'_2']]))
						{
							$client_info[$jl_field] .= '-'.$jl_info[$client_info[$jl_field.'_2']];
							if(!empty($jl_info[$client_info[$jl_field.'_3']]))
							{
								$client_info[$jl_field] .= '-'.$jl_info[$client_info[$jl_field.'_3']];
							}
						}
					}
				}
			}
			$responce -> rows[$i]           = $client_info;
			$i ++;
		}
		return $responce;
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
		$query = $this->db_read->get("est_client_history");
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
}