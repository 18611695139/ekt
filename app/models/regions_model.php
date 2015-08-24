<?php
class Regions_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
     * 获得省、市
     *
     * @access      public 类型
     * @param int
     * @param int     父id
     * @return array
     */
	public function get_regions($region_type = 0, $parent_id = 0)
	{
		$this->db_read->where("region_type",$region_type);
		$this->db_read->where("parent_id",$parent_id);
		$this->db_read->select("region_id, region_name");
		$query = $this->db_read->get("est_region");
		//数据
		$result = $query->result_array();
		$regions_info = array();
		foreach($result AS $key => $value)
		{
			$regions_info[$value["region_id"]] = $value["region_name"];
		}
		return $regions_info;
	}

	/**
	 * 通过名称获取id
	 *
	 * @param string $name
	 * @param string $region_type
	 * @return bool/array
	 */
	public function get_region_id_by_name($name='',$region_type='')
	{
		if(empty($name)&&empty($region_type))
		{
			return false;
		}

		$names = array();
		if(is_array($name))
		{
			foreach ($name as $name_item)
			{
				if(!in_array($name_item,$names))
				{
					$names[] = $name_item;
				}
			}
		}
		else
		{
			$names = array($name);
		}

		$this->db_read->where_in('region_name',$names);
		$this->db_read->where('region_type',$region_type);
		$this->db_read->select("region_id, region_name");
		$query = $this->db_read->get("est_region");
		//数据
		$result = $query->result_array();
		$regions_info = array();
		foreach($result AS $region)
		{
			$regions_info[$region['region_name']] = $region['region_id'];
		}
		return $regions_info;
	}

}