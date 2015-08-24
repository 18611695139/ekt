<?php
/**
 * 导入/导出 模板配置类
 *
 */
class Model_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 权限控制数据导入的字段
	 * 
	 * @param int $impt_type 导入类型 IMPT_CLIENT 1 客户  IMPT_PRODUCT 2 产品
	 * @return array (一维数组)不允许导入的字段名
	 * 
	 * @author zgx
	 */
	public function cannot_impt_fields($impt_type)
	{
		switch ($impt_type)
		{
			case IMPT_CLIENT:
				$fields  = array('cle_pingyin','cle_stat','cle_first_connecttime','cle_last_connecttime','con_rec_next_time','dployment_num','dept_id','dept_name','cle_executor_time','cle_creat_time','cle_creat_user_id','cle_creat_user_name','cle_update_time','cle_update_user_id','cle_update_user_name','impt_id','con_if_main','user_name','cle_dial_number','cle_info_source');
				$role_type   = $this->session->userdata('role_type');
				$temp_fields = array();
				if ($role_type != DATA_DEPARTMENT)
				{
					$temp_fields   = array('user_id','cle_stage','cle_stat','cle_info_source');
				}
				$fields = array_merge($fields,$temp_fields);
				break;
			case IMPT_PRODUCT:
				$fields = array('product_thum_pic','product_class_id','product_class_name','product_state');
				break;
			default:$fields = array();
		}
		return $fields;
	}

	/**
	 * 得到模仿Excel的表头
	 *
	 * @param int $number
	 * @return array
	 * 
	 * @author zgx
	 */
	public function a_z_table_head($number)
	{
		$number = $number <= 52 ? 52 : $number;
		$letters   = range('A','Z');
		$prefixArr = array();
		$i = 0;
		if ($number <= 0)
		{
			return $prefixArr;
		}

		foreach($letters as $prefixA)
		{
			$i++;
			$prefixArr[] = $prefixA;
		}

		foreach($letters as $prefixA)
		{
			foreach($letters as $prefixB)
			{
				$i ++;
				$prefixArr[] = $prefixA.$prefixB;

				if ($number == $i)
				{
					return $prefixArr;
				}
			}
		}
		return true;
	}

	/**
	 * 得到模板信息
	 *
	 * @param int $model_id   模板ID
	 * @param int $model_type 模板类型：1客户导入，2客户导出，3产品导入，4产品导出
	 * @return array  
	 * 			<pre>
	 * 				第一种情况 一维数组 得到一个模板信息
	 * 				第二种情况 二维数组 得到某类型模板多个模板信息
	 * 			</pre>
	 * 
	 * @author zgx
	 */
	public function get_model_info($model_id = 0,$model_type = 0)
	{
		if(empty($model_id) && empty($model_type))
		{
			return false;
		}
		if (!empty($model_id))
		{
			$this->db_read->where("model_id",$model_id);
		}
		if (!empty($model_type))
		{
			$this->db_read->where("model_type",$model_type);
		}

		$user_id = $this->session->userdata("user_id");
		$this->db_read->where("model_creat_user_id",$user_id);

		$this->db_read->select("model_id,model_name,model_type,model_detail");
		$model_info = $this->db_read->get("est_model");
		if (empty($model_id))
		{
			return $model_info->result_array();
		}
		else
		{
			return $model_info->row_array();
		}
	}

	/**
	 * 根据条件得到模板可用字段信息 (顺序：1.已用的字段排前)
	 *
	 * @param string $fields_id   字段ID，逗号分隔
	 * @param string $fields_type   字段类型 0客户字段  1联系人字段 2 产品字段 -1客户+联系人
	 * @param bool $if_get_used_info
	 * 				<pre> 
	 * 					false	得到某类型模板所有可用字段
	 * 					true 	得到模板使用中的字段信息
	 * 				</pre>
	 * @return array 二维数组
	 * 
	 * @author zgx
	 */
	public function get_model_detail_info($fields_id = "",$fields_type ="",$if_get_used_info = false)
	{
		$result = array();
		$where = '';
		if($if_get_used_info === false)
		{
			if($fields_type!='' && $fields_type != FIELD_TYPE_CLIENT_CONTACT)
			{
				$where = " AND field_type=".$fields_type;
			}
		}
		if (!empty($fields_id))
		{
			if($fields_type == FIELD_TYPE_CLIENT_CONTACT)
			{
				//获取系统配置参数
				$this->load->model("system_config_model");
				$config_info = $this->system_config_model->get_system_config();
				$use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];//是否使用联系人模块 0是1否
				if($use_contact!=1)
				{
					$where = " AND field_type IN(0,1)";
				}
				else
				{
					$where = " AND field_type=0";
				}
			}
			if($if_get_used_info === true)
			{
				$where .= " AND id IN (".$fields_id.")";
			}
			$query  = $this->db_read->query("SELECT id,name,fields,(CASE WHEN id IN(".$fields_id.") THEN 1 ELSE 0 END) AS if_used,field_type,data_type FROM est_fields WHERE state=1 ".$where." ORDER BY if_used desc,FIELD(id,".$fields_id.")");
			$result = $query->result_array();
		}
		return $result;
	}

	/**
	 * 保存新模板信息
	 *
	 * @param string $model_name 模板名称
	 * @param int $model_type 模板类型 0默认  1客户导入，2客户导出，3产品导入，4产品导出
     * @param array $field_ids
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function insert_model($model_name='',$model_type=0,$field_ids=array())
	{
		$user_id = $this->session->userdata("user_id");
		$time    = date("Y-m-d H:i:s");
		$model_detail = implode(",",$field_ids);
		$data = array(
		"model_name"   => $model_name,
		"model_type"   => $model_type,
		"model_detail" => $model_detail,
		"model_creat_user_id" => $user_id,
		"model_creat_time"    => $time
		);
		return  $this->db_write->insert("est_model",$data);
	}

	/**
	 * 保存编辑后的模板信息
	 *
	 * @param int $model_id 模板id
	 * @param string $model_name 模板名称
	 * @param array $field_ids 模板选中的字段id
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function update_model($model_id=0,$model_name='',$field_ids=array())
	{
		if(empty($model_id))
		{
			return false;
		}
		//更新模板
		$model_detail = implode(",",$field_ids);
		$data = array(
		"model_name" => $model_name,
		"model_detail" => $model_detail
		);
		return  $this->db_write->update("est_model",$data,array("model_id"=>$model_id));
	}

	/**
	 * 删除模板
	 *
	 * @param int $model_id  模板ID
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function delete_model($model_id = 0)
	{
		if(empty($model_id))
		{
			return false;
		}
		//删除模板信息
		$result = $this->db_read->delete("est_model",array("model_id"=>$model_id));
		return $result;
	}
}