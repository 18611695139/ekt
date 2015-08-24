<?php
class Client_data_deal_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 得到数据处理列表显示数据
	 *
	 * @param int $page
	 * @param int $limit
	 * @param int $sort
	 * @param int $order
	 * @param array $where 传递搜索条件的数组
	 * @param int $repeat_condition  查重  1客户姓名重复  2客户电话重复
	 * @return object responce
	 */
	public  function get_client_list($page=1, $limit=50, $sort=NULL, $order=NULL,$where=NULL,$repeat_condition = 0)
	{
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		
		if ($sort) 
		{
			//列表排序，显示字段->排序字段
			$this->load->model("datagrid_confirm_model");
			$sort = $this->datagrid_confirm_model->replace_sort_field($sort);
		}

		$search_filed = "";
		if ($repeat_condition == 1)
		{
			$search_filed = "cle_name";
		}
		elseif ($repeat_condition == 2)
		{
			$search_filed = "cle_phone";
		}

		if ( $repeat_condition == 1 || $repeat_condition == 2 )
		{
			if (!empty($where))
			{
				$where = " AND ".$where;
			}
			$total_query = $this->db_read->query("SELECT COUNT(*) AS total FROM est_client,(  SELECT $search_filed  FROM est_client WHERE $search_filed != '' $where GROUP BY $search_filed  HAVING COUNT(*) > 1 ) AS table2 WHERE est_client.$search_filed = table2.$search_filed");
		}
		else
		{
			$this->db_read->select('count(*) as total',FALSE);
			$total_query = $this->db_read->get("est_client");
		}

		$total = $total_query->row()->total;
		$total_pages = ceil($total/$limit);
		$responce = new stdClass();
		$responce -> total = $total;

		$page = $page > $total_pages ? $total_pages : $page;
		$start = $limit*$page - $limit;
		$start = $start > 0 ? $start : 0;
		if ( $repeat_condition == 1 || $repeat_condition == 2 )
		{
			$data = $this->db_read->query("SELECT est_client.* FROM est_client,(  SELECT $search_filed  FROM est_client WHERE $search_filed != '' $where GROUP BY $search_filed  HAVING COUNT(*) > 1 ) AS table2 WHERE est_client.$search_filed = table2.$search_filed ORDER BY $sort $order LIMIT $start,$limit");
		}
		else
		{
			if( ! empty($sort))
			{
				$this->db_read->order_by($sort,$order);
			}
			$this->db_read->limit($limit,$start);
			$data = $this->db_read->get("est_client");
		}
		$data = $data->result_array();

		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息

		if (!empty($data))
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
		}

		$i = 0;
		foreach($data AS $i=>$client_info)
		{
			//数据所属人
			$client_info["user_name"]   = empty($user_info[$client_info["user_id"]]) ? "" : $user_info[$client_info["user_id"]];
			//创建人
			$client_info["cle_creat_user_name"]   = empty($user_info[$client_info["cle_creat_user_id"]]) ? "" : $user_info[$client_info["cle_creat_user_id"]];
			//更新人
			$client_info["cle_update_user_name"]   = empty($user_info[$client_info["cle_update_user_id"]]) ? "" : $user_info[$client_info["cle_update_user_id"]];
			//所属部门
			$client_info["dept_name"]   = empty($dept_info[$client_info["dept_id"]]) ? "公司数据" : $dept_info[$client_info["dept_id"]];

			$responce -> rows[$i]           = $client_info;
		}

		return $responce;
	}

	/**
	 * 一次性删重：将保持重复的第一条记录，重复的第二条记录后将被删除
	 *
	 * @param int   $repeat_condition  查重  1客户姓名重复  2客户电话重复
	 * @param array $where  检索条件
	 * @return bool
	 */
	public function delete_all_repeat($repeat_condition = 0,$where = NULL)
	{
		if ( $repeat_condition != 1 && $repeat_condition != 2 )
		{
			return FALSE;
		}

		$search_filed = "";
		if ($repeat_condition == 1)
		{
			$search_filed = "cle_name";
		}
		elseif ($repeat_condition == 2)
		{
			$search_filed = "cle_phone";
		}

		if (!empty($where))
		{
			$where = " AND ".$where;
		}

		$result = $this->db_write->query("DELETE FROM est_client USING est_client,(  SELECT DISTINCT MIN(cle_id) AS cle_id,$search_filed  FROM est_client WHERE $search_filed != '' $where  GROUP BY $search_filed  HAVING COUNT(1) > 1) AS table2 WHERE est_client.$search_filed = table2.$search_filed  AND est_client.cle_id <> table2.cle_id ");

		return $result;
	}

	/**
	 * 查找替换
	 *
	 * @param string  $replace_field  需要进行内容替换的字段
	 * @param string  $org_string     原字段
	 * @param string  $new_string     新字串
	 * @param array   $where          检索条件
	 * @return int 返回修改数
	 */
	public function find_replace($replace_field = '',$org_string = '',$new_string = '',$where = NULL)
	{
		if (  empty($replace_field) || ( empty($org_string) && empty($new_string))  )
		{
			return FALSE;
		}

		if (!empty($where))
		{
			$where = " WHERE ".$where;
		}

		$rows = 0;
		if (empty($org_string))
		{
			//客户姓名
			$name_sql = "";
			if ($replace_field == "cle_name" )
			{
				$cle_pingyin = "";
				if ( !empty($new_string)  )
				{
					$cle_pingyin = ctype_alnum($new_string)?$new_string:pinyin($new_string,TRUE);
				}
				$name_sql = ",cle_pingyin = '$cle_pingyin'";
			}
			$this->db_write->query("UPDATE est_client SET $replace_field = '$new_string' $name_sql $where");
			$rows = $this->db_write->affected_rows();
		}
		else
		{
			$this->db_write->query("UPDATE est_client SET $replace_field = replace($replace_field,'$org_string','$new_string') $where");
			$rows = $this->db_write->affected_rows();
			if ( $replace_field == "cle_name" )
			{
				//更新客户拼音
				$this->update_name_pinying($where);
			}
		}

		return $rows;
	}

	/**
	 * 合并字段
	 *
	 * @param string $merger_result
	 * @param string $merger_one       字段1
	 * @param string $merger_two       字段2
	 * @param string $merger_three     字段3
	 * @param string $merger_interval  间隔字符
	 * @param array  $where   检索条件
	 * @return int 返回影响行数
	 */
	public function merger_confirm($merger_result = '',$merger_one = '',$merger_two = '',$merger_three = '',$merger_interval = '',$where = NULL)
	{
		if (  empty($merger_result) || ( empty($merger_one)&&empty($merger_two)&&empty($merger_three) )   )
		{
			return 0;
		}

		if (!empty($where))
		{
			$where = " WHERE ".$where;
		}

		$merger = array();
		if ($merger_one)
		{
			$merger[] = $merger_one;
		}
		if ($merger_two)
		{
			$merger[] = $merger_two;
		}
		if ($merger_three)
		{
			$merger[] = $merger_three;
		}
		$merger = implode(",",$merger);

		$this->db_write->query("UPDATE est_client SET $merger_result = concat_ws('$merger_interval',$merger) $where");
		$rows = $this->db_write->affected_rows();
		if ( $merger_result == "cle_name" )
		{
			//更新客户拼音
			$this->update_name_pinying($where);
		}

		return $rows;
	}

	/**
	 * 清空字段
	 *
	 * @param string $empty_filed  需要清空的字段
	 * @param array  $where  检索条件
	 * @return int 返回影响行数
	 */
	public function empty_field($empty_filed = '',$where = NULL)
	{
		if (empty($empty_filed))
		{
			return 0;
		}

		if( ! empty($where))
		{
			$this->db_write->start_cache();
			$this->db_write->where($where);
			$this->db_write->stop_cache();
		}

		$data[$empty_filed] = "";
		//客户姓名
		if ($empty_filed == "cle_name")
		{
			$data["cle_pingyin"] = "";  //客户姓名拼音
		}
		if ($empty_filed == "cle_pingyin")
		{
			$data["cle_name"] = "";
		}

		$this->db_write->update("est_client",$data);
		$rows = $this->db_write->affected_rows();

		return $rows;
	}

	/**
	 * 更新客户姓名的拼音
	 *
	 * @param array $where  检索条件
	 * @return bool
	 */
	public function update_name_pinying($where = NULL)
	{
		$temp_value = $this->db_read->query("SELECT cle_id,cle_name FROM est_client $where");
		$temp_value = $temp_value->result_array();

		if (!empty($temp_value))
		{
			$update_value = array();
			$flag = 1;
			$step = 100;
			foreach ($temp_value AS $client)
			{
				$cle_pingyin = "";
				$cle_name    = "";
				if (!empty($client["cle_name"]))
				{
					$cle_name    = replace_illegal_string($client["cle_name"]);
					//拼音处理
					if(ctype_alnum($cle_name))
					{
						$cle_pingyin = $cle_name;
					}
					else
					{
						$cle_pingyin = pinyin($cle_name,TRUE);
					}
				}
				$update_value[] = array("cle_id"=>$client["cle_id"],"cle_name"=>$cle_name,"cle_pingyin"=>$cle_pingyin);

				if ( $flag == $step )
				{
					$this->db_write->update_batch('est_client', $update_value, 'cle_id');

					$update_value = array();
					$flag = 0;
				}

				$flag ++;
			}

			//不足100条的单独处理
			if ( !empty($update_value) )
			{
				$this->db_write->update_batch('est_client', $update_value, 'cle_id');
			}
		}

		return TRUE;
	}

	/**
	 * 批量删除
	 * 
	 * @param array $where  检索条件
	 * @return int 返回删除总条数
	 */
	public function delete_all_data($where=NULL)
	{
		$cle_ids = array();
		$query = $this->db_read->query("SELECT cle_id FROM est_client where ".$where);
		if($query)
		{
			foreach($query->result_array() AS $value)
			{
				$cle_ids[] = $value['cle_id'];
			}
		}

		$cle_id = implode(",",$cle_ids);

		if(!empty($cle_id))
		{
			$this->db_write->query("DELETE FROM est_client where cle_id IN($cle_id)");
			$this->db_write->query("DELETE FROM est_contact where cle_id IN($cle_id)");
		}
		return count($cle_ids);
	}
}