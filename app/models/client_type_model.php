<?php
class Client_type_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 保存 客户阶段 相应 等级信息
	 *
	 * @param array $cle_stage
	 * @param array $cle_type
	 * @return bool
	 * @author zgx
	 */
	public function save_cle_type_by_stage($cle_stage=array(),$cle_type=array())
	{
		if(count($cle_stage)!=count($cle_type))
		{
			return false;
		}
		foreach ($cle_stage as $key=>$val)
		{
			$cle_stage[$key] = trim($val);
			if(empty($cle_stage[$key]))
			{
				unset($cle_stage[$key]);
			}
		}
		$cle_stage = array_unique($cle_stage);//删除重复数据
		foreach($cle_stage as $skey=>$stage)
		{
			$insert_data[] = array('cle_stage'=>$stage,'cle_type'=>$cle_type[$skey]);
		}
		if(!empty($insert_data))
		{
			$this->db_write->empty_table('est_client_type');//清空表
			$result = $this->db_write->insert_batch('est_client_type',$insert_data);//批量插入
			return $reslut;
		}
		return true;
	}

	/**
	 * 获取某阶段的等级
	 *
	 * @param string $cle_stage
	 * @return bool/int
	 * @author zgx
	 */
	public function get_cle_type_by_stage($cle_stage='')
	{
		if(empty($cle_stage))
		{
			return false;
		}
		$this->db_read->where(array('cle_stage'=>$cle_stage));
		$this->db_read->select('cle_stage,cle_type');
		$query = $this->db_read->get("est_client_type");
		if($query)
		return $query->row()->cle_type;
		else
		return false;
	}

	/**
	 * 根据等级获取相应客户阶段名称
	 *
	 * @param int $cle_type
	 * @return array
	 * @author zgx
	 */
	public function get_stage_by_cle_type($cle_type=0)
	{
		if(empty($cle_type))
		{
			return false;
		}
		$this->db_read->where(array('cle_type'=>$cle_type));
		$this->db_read->select('cle_stage,cle_type');
		$result = $this->db_read->get("est_client_type");
		if($result)
		{
			$cle_stages = array();
			foreach($result->result_array() as $stage)
			{
				$cle_stages[] = $stage['cle_stage'];
			}
			return $cle_stages;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取所有阶段的等级信息
	 * @return array
	 * @author zgx
	 */
	public function get_all_cle_type_info()
	{
		$all_types = array();
		$this->db_read->select('cle_stage,cle_type');
		$result = $this->db_read->get("est_client_type");
		$all_types = $result->result_array();
		$cle_types = array();
		foreach($all_types as $value)
		{
			$cle_types[$value['cle_stage']] = $value['cle_type'];
		}
		return $cle_types;
	}

}