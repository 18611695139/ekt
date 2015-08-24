<?php
class Dictionary_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 得到一条数据字典信息
	 *
	 * @param int $id 数据字典ID
	 * @return array
	 * <code>
	 * array(
	 * 	[id]=> 数据字典id
	 * 	[name]=> 名称
	 * 	[type]=> 类型（1客户相关  2订单相关  3客服服务）
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_dictionary_info($id = 0)
	{
		if(empty($id))
		{
			return false;
		}
		$result = array();
		if(!$this->cache->get('dictionary_info'.$id))
		{
			$this->db_read->where("id",$id);
			$query  = $this->db_read->get("est_dictionary");
			$result =  $query->row_array();

			$this->cache->save('dictionary_info'.$id, $result, 600);
		}
		else
		{
			$result = $this->cache->get('dictionary_info'.$id);
		}

		return $result;
	}

	/**
	 * 获取数据字典选项信息
	 *
	 * @param int $parent_id 数据字典ID
	 * @return array
	 * <code>
	 * array(
	 * 	 [0]=>array(
	 * 			[id]=> 选项id
	 * 			[parent_id]=> 数据字典id
	 * 			[name]=> 选项名称
	 * 			[sorts]=> 选项顺序
	 * 		)
	 * 	 ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_dictionary_detail($parent_id = 0)
	{
		if(empty($parent_id))
		{
			return false;
		}
		$result = array();
		if(!$this->cache->get('dictionary_detail'.$parent_id))
		{
			$this->db_read->where("parent_id",$parent_id);
			$this->db_read->order_by("sorts","ASC");
			$query = $this->db_read->get("est_dictionary_detail");
			$result =  $query->result_array();
			$this->cache->save('dictionary_detail'.$parent_id, $result, 600);
		}
		else
		{
			$result = $this->cache->get('dictionary_detail'.$parent_id);
		}
		if(($parent_id==6) && empty($result))
		{
			$this->db_write->query("INSERT INTO `est_dictionary_detail` (`name`, `parent_id`, `sorts`) VALUES ('未拨打','6',0), ('呼通','6',1), ('未呼通','6',2), ('空号错号','6',3), ('停机','6',4)");
		}
		return $result;
	}

	/**
	 * 保存数据字典选项信息
	 *
	 * @param int   $dict_id  数据字典类型ID
	 * @param string $options 数据字典选项
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function save_dictionary_detail($dict_id = 0,$options='')
	{
		if(empty($dict_id))
		{
			return false;
		}
		$this->cache->delete('dictionary_detail'.$dict_id);
		foreach ($options as $key=>$val)
		{
			$options[$key] = trim($val);
			if(empty($options[$key]))
			{
				unset($options[$key]);
			}
		}
		$options = array_unique($options);
		$options = array_values($options);
		//得到该字典选项原先的数据
		$options_old = array();
		$query = $this->db_read->get_where('est_dictionary_detail',array("parent_id"=>$dict_id));
		foreach ($query->result() as $row)
		{
			$options_old[$row->id] = $row->name;
		}

		$options_del = array_diff($options_old,$options);
		$options_add = array_diff($options,$options_old);
		$options_update = array_intersect($options_old,$options);

		//更新
		$_update = array();
		foreach ($options_update as $val)
		{
			$id = array_search($val,$options_old);
			$sorts = array_search($val,$options);
			$_update[] = array('id'=>$id,'sorts'=>$sorts);
		}
		if(!empty($_update))
		$this->db_write->update_batch('est_dictionary_detail', $_update,'id');
		//新建
		$_add = array();
		foreach ($options_add as $val)
		{
			$sorts = array_search($val,$options);
			$_add[] = array('parent_id'=>$dict_id,'name'=>$val,'sorts'=>$sorts);
		}
		if(!empty($_add))
		$this->db_write->insert_batch('est_dictionary_detail', $_add);
		//删除
		$_del =array();
		foreach ($options_del as $val)
		{
			$id = array_search($val,$options_old);
			$_del[] = $id;
		}
		if(!empty($_del))
		{
			$this->db_write->where_in('id', $_del);
			$this->db_write->delete('est_dictionary_detail');
		}
		//如果是阶段调整
		if($dict_id == 2)
		{
			$id_add = array();
			if(!empty($_add))
			{
				$name = array();
				foreach ($_add as $tmp)
				{
					$name[] = $tmp['name'];
				}
				$this->db_write->where_in('name',$name);
				$query = $this->db_write->get('est_dictionary_detail');

				foreach ($query->result() as $row)
				{
					$id_add[] = $row->id;
				}
			}
			$this->_stage_table_adjust($id_add,$_del);
		}

		//操作日志
		$dica = $this->get_dictionary_info($dict_id);
		$content = "数据字典|配置 ".$dica['name']." 数据类型" ;
		$this->load->model("log_model");
		$this->log_model->write_client_log($content);

		return TRUE;
	}

	/**
	 * 通过 客户阶段选项名称 获取 客户阶段选项id
	 *
	 * @param string $cle_stage  客户阶段选项名称
	 * @return int|bool
	 * @author zgx
	 */
	public function get_dictionary_stage_by_name($cle_stage = '')
	{
		$stage_array = $this->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		foreach ($stage_array as $item)
		{
			if($item['name'] == $cle_stage)
			return $item['id'];
		}
		return false;
	}

	/**
	 * 阶段统计表调整
	 *
	 * @param array $id_add
	 * @param array $id_del
	 * @return bool
	 */
	private function _stage_table_adjust($id_add=array(),$id_del=array())
	{
		if(empty($id_add) && empty($id_del))
		{
			return false;
		}

		$sql = array();
		foreach ($id_add as $id)
		{
			$sql[] = "ADD COLUMN cle_stage_".$id."  int NULL DEFAULT 0";
		}
		foreach ($id_del as $id)
		{
			$sql[] = "DROP COLUMN cle_stage_".$id;
		}
		$this->db_write->query("ALTER TABLE est_statistics_stage ".implode(',',$sql));
		return true;
	}
}