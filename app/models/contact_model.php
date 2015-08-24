<?php
class Contact_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 得到联系人检索条件
	 *
	 * @param array $condition 检索条件数组 
	 * <pre>
	 * array(
	 *    [cle_id] => 客户ID
	 * )
	 * </pre>
     * @return array
	 */
	public function get_contact_condition( $condition = array() )
	{
		$wheres = array();
		if (!empty($condition["cle_id"]))
		{
			$wheres[] = "cle_id = '".$condition["cle_id"]."'";
		}

		return $wheres;
	}

	/**
	 * 得到联系人管理列表显示数据
	 *
	 * @param array $condition
	 * <pre>
	 * 传递搜索条件的数组
	 * cle_id 客户ID
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
	 *        [con_id]=>10
 	 *        [con_name] =>  联系人姓名
	 *        [con_phone] => 联系人电话
	 *       … 
	 *          )
	 *     )
	 * </code>
	 * @author yan
	 * @package contact_model
	 */
	public function get_contact_list($condition=array(),$select=array(),$page=0, $limit=10, $sort=null, $order=null)
	{
		//检索条件
		$wheres = $this->get_contact_condition($condition);
		if ($wheres)
		{
			$where = implode(" AND ",$wheres);
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		//处理检索字段
		$this->load->model('datagrid_confirm_model');
		if(empty($select))
		{
			$select = $this->datagrid_confirm_model->get_available_select_fields(LIST_CONFIRM_CONTACT);
		}
		//级联
		$jl_field_names = array();
		$this->load->model('field_confirm_model');
		$field_names = $this->field_confirm_model->get_jl_field_name(FIELD_TYPE_CONTACT);
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
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_contact');
		$total = $total_query->row()->total;
		$responce = new stdClass();
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
		$data = $this->db_read->get('est_contact');
		//级联
		if(count($data->result_array())>0)
		{
			$jl_info = array();
			if($if_get_jl_info == true)
			{
				$jl_info = $this->field_confirm_model->get_all_jl_info(FIELD_TYPE_CONTACT);
			}
		}
		$responce -> rows = array();
		foreach($data->result_array() AS $i=>$contact)
		{
			//级联
			if(!empty($jl_field_names))
			{
				foreach($jl_field_names as $jl_field)
				{
                    if($jl_field[1]==DATA_TYPE_JL)
                    {
                        $contact[$jl_field[0]] = '';
                        $jl_name = array();
                        if(!empty($jl_info[$contact[$jl_field[0].'_1']]))
                        {
                            $contact[$jl_field[0]] = $jl_info[$contact[$jl_field[0].'_1']];
                            if(!empty($jl_info[$contact[$jl_field[0].'_2']]))
                            {
                                $contact[$jl_field[0]] .= '-'.$jl_info[$contact[$jl_field[0].'_2']];
                                if(!empty($jl_info[$contact[$jl_field[0].'_3']]))
                                {
                                    $contact[$jl_field[0]] .= '-'.$jl_info[$contact[$jl_field[0].'_3']];
                                }
                            }
                        }
                    }
                    else
                    {
                        $contact[$jl_field[0]] = '';
                        if(!empty($contact[$jl_field[0].'_2']))
                        {
                            foreach(explode(',',$contact[$jl_field[0].'_2']) as $box)
                            {
                                if(!empty($jl_info[$box]))
                                {
                                    $contact[$jl_field[0]] .= $jl_info[$box].'，';
                                }
                            }
                        }
                    }
				}
			}
			$responce -> rows[$i] = $contact;
		}
		$this->db_read->flush_cache();
		return $responce;
	}

	/**
	 * 添加联系人
	 * 
	 * @param array 添加内容
	 * @return boolen
	 */
	public function insert_contact($contact_info = NULL)
	{
		$cle_id = empty($contact_info["cle_id"]) ? 0 : $contact_info["cle_id"];
		if (empty($cle_id) || empty($contact_info))
		{
			return FALSE;
		}
		$con_name = empty($contact_info["con_name"]) ? "" : $contact_info["con_name"];
		//联系人电话
		$con_mobile   = empty($contact_info["con_mobile"]) ? "" : $contact_info["con_mobile"];
		if ($con_mobile)
		{
			$this->load->model('phone_location_model');
			$con_mobile   = $this->phone_location_model->remove_prefix_zero($con_mobile);
		}

		$con_if_main = 0;
		$query_main = $this->db_read->query("SELECT count(*) as total FROM est_contact WHERE cle_id=".$cle_id);
		$if_had_contact = $query_main->row()->total;
		if(empty($if_had_contact))
		{
			$con_if_main = 1;
		}
		$data = array(
		"cle_id"      => $cle_id,
		"con_if_main" => $con_if_main,
		"con_name"    => $con_name,
		"con_mobile"  => $con_mobile,
		"con_mail"    => empty($contact_info["con_mail"]) ? "" : $contact_info["con_mail"],

		"con_remark" => empty($contact_info["con_remark"]) ? "" : $contact_info["con_remark"]
		);
		//得到 联系人自定义字段
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_CONTACT);
		foreach ($confirm_field AS $item)
		{
			if($item["data_type"]==DATA_TYPE_JL || $item["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if(!empty($contact_info[$item["fields"]."_1"]))
				$data[$item["fields"].'_1'] = $contact_info[$item["fields"]."_1"];
				if(!empty($contact_info[$item["fields"]."_2"]))
				$data[$item["fields"].'_2'] = $contact_info[$item["fields"]."_2"];
				if(!empty($contact_info[$item["fields"]."_3"]))
				$data[$item["fields"].'_3'] = $contact_info[$item["fields"]."_3"];
			}
			else
			{
				$data[$item["fields"]] = empty($contact_info[$item["fields"]]) ? "" : $contact_info[$item["fields"]];
			}
		}
		$result = $this->db_write->insert("est_contact",$data);
		return $result;
	}

	/**
     * 修改联系人
     * 
     * @param null $contact_info
     * @return bool
     */
	public function update_contact($contact_info = NULL)
	{
		$con_id = empty($contact_info["con_id"]) ? 0 : $contact_info["con_id"];
		if (empty($con_id) || empty($contact_info))
		{
			return FALSE;
		}
		//得到联系人使用字段
		$this->load->model("field_confirm_model");
		$contact_filed = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CONTACT);

		$update_data = array();
		foreach ($contact_filed AS $field_info)
		{
			if($field_info["data_type"]==DATA_TYPE_JL || $field_info["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if(isset($contact_info[$field_info['fields']."_1"]))
				{
					$update_data[$field_info['fields'].'_1'] = $contact_info[$field_info['fields']."_1"];
					if(empty($contact_info[$field_info['fields']."_1"]))
					{
						$update_data[$field_info['fields'].'_2'] = '';
						$update_data[$field_info['fields'].'_3'] = '';
					}
				}
				if(isset($contact_info[$field_info['fields']."_2"]))
				{
					$update_data[$field_info['fields'].'_2'] = $contact_info[$field_info['fields']."_2"];
					if(empty($contact_info[$field_info['fields']."_2"]))
					{
						$update_data[$field_info['fields'].'_3'] = '';
					}
				}
				if(isset($contact_info[$field_info['fields']."_3"]))
				{
					$update_data[$field_info['fields'].'_3'] = $contact_info[$field_info['fields']."_3"];
				}
			}
			elseif(isset($contact_info[$field_info["fields"]]))
			{
				//联系人姓名
				if ( $field_info["fields"] == "con_name" )
				{
					$update_data["con_name"] = empty($contact_info["con_name"]) ? "" : $contact_info["con_name"];
				}
				//联系人电话
				elseif ($field_info["fields"] == "con_mobile")
				{
					$con_mobile   = empty($contact_info["con_mobile"]) ? "" : $contact_info["con_mobile"];
					if ($con_mobile)
					{
						$this->load->model('phone_location_model');
						$con_mobile   = $this->phone_location_model->remove_prefix_zero($con_mobile);
					}
					$update_data["con_mobile"] = $con_mobile;
				}
				else
				{
					$update_data[$field_info["fields"]] = $contact_info[$field_info["fields"]];
				}
			}
		}
		$result = true;
		if ($update_data)
		{
			$this->db_write->where("con_id",$con_id);
			$result = $this->db_write->update("est_contact",$update_data);
		}

		return $result;
	}

	/**
	 * 删除联系人
	 * 
	 * @param int $con_id
	 * @return bool
	 */
	public function delete_contact($con_id=0)
	{
		if(empty($con_id))
		{
			return false;
		}
		$this->db_write->where("con_id",$con_id);
		$result = $this->db_write->delete('est_contact');
		return $result;
	}

	/**
	 * 通过 客户ID、联系人电话检测是否存在，存在则更新，不存在则插入新信息
	 *
	 * @param int    $cle_id      客户ID
	 * @param string $con_mobile  联系人电话
	 * @param string $con_name    联系人姓名
	 * @return bool
	 */
	public function insert_update_contact($cle_id = 0,$con_mobile = "",$con_name = "")
	{
		if ( empty($cle_id) || empty($con_mobile) )
		{
			return FALSE;
		}

		$this->db_read->where("cle_id",$cle_id);
		$this->db_read->where("con_mobile",$con_mobile);
		$this->db_read->select("con_id,con_name");
		$this->db_read->limit(1);
		$contact_info = $this->db_read->get("est_contact");
		$contact_info = $contact_info->row_array();

		if ($contact_info)
		{
			if ( $con_name && $con_name != $contact_info["con_name"] && $contact_info["con_id"] )
			{
				$this->db_write->where("con_id",$contact_info["con_id"]);
				$this->db_write->update("est_contact",array("con_name"=>$con_name));
			}
		}
		else
		{
			//新数据
			$data = array(
			"cle_id" => $cle_id,
			"con_name" => $con_name,
			"con_mobile" => $con_mobile
			);

			$this->db_write->insert("est_contact",$data);
		}

		return TRUE;
	}

	/**
	 * 设置主联系人类型  
	 * 
	 * @param int $con_id 联系人id
	 * @param int $cle_id 客户id
	 * @param int $con_if_main 是否主联系人 0否  1是
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function set_master_contact($con_id=0,$cle_id=0,$con_if_main=0)
	{
		if (!$con_id || !$cle_id)
		{
			return FALSE;
		}
		//设置为主联系人
		if ($con_if_main == 1)
		{
			//设置所有联系人为普通联系人
			$this->db_write->update("est_contact",array("con_if_main"=>0),"cle_id = $cle_id AND con_if_main = 1");
			//设置指定联系人为主联系人
			$result = $this->db_write->update("est_contact",array("con_if_main"=>1),"con_id = $con_id");
			return $result;
		}
		else
		{
			//设置为普通联系人
			$result = $this->db_write->update("est_contact",array("con_if_main"=>0),"con_id = $con_id");
			return $result;
		}
	}

	/**
	 * 获取某联系人信息
	 *
	 * @param int $con_id 联系人id
	 * @return array
	 * <code>
	 * array(
	 * 	[con_id] => 联系人id
	 *  [cle_id] => 客户id
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_contact_info($con_id = 0 )
	{
		$result = "";
		if ($con_id == 0 )
		{
			return $result;
		}

		$this->db_read->where("con_id",$con_id);
		$query = $this->db_read->get("est_contact");
		return $query->row_array();
	}

	/**
	 * 根据客户id 获取一个联系人电话
	 *
	 * @param int $cle_id  客户ID
	 * @return string 电话号
	 */
	public function get_contact_phone($cle_id = 0)
	{
		if ( empty($cle_id) )
		{
			return "";
		}
		$this->db_read->where("cle_id",$cle_id);
		$this->db_read->where("con_mobile !=","");
		$this->db_read->where("con_if_main", 1);
		$this->db_read->select("con_mobile");
		$this->db_read->limit(1);
		$query = $this->db_read->get("est_contact");
		$result = $query->row_array();
		$result = empty($result["con_mobile"]) ? "" : $result["con_mobile"];

		return $result;
	}




	/**
	 * 根据客户id获取联系人
	 * 
	 * @param int $cle_id 客户id
	 * @param int $limit 获取几条数据
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 			[con_id] => 联系人id
	 * 			...
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_contact_by_cle_id($cle_id=0,$limit=0)
	{
		if(empty($cle_id))
		{
			return false;
		}
		$this->db_read->select('con_mobile,con_name');
		$this->db_read->where(array('cle_id'=>$cle_id));
		$this->db_read->order_by('con_if_main','desc');
		if(!empty($limit))
		$this->db_read->limit($limit);
		$query = $this->db_read->get("est_contact");
		$result = $query->result_array();

		return $result;
	}
}