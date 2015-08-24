<?php
class Field_confirm_model extends CI_Model {
	private $_unchange_state_fields = array('cle_name','cle_phone','cle_first_connecttime','cle_last_connecttime','con_rec_next_time','user_id','dept_id','dployment_num','cle_executor_time','cle_creat_time','cle_creat_user_id','cle_update_time','cle_update_user_id','impt_id','con_if_main','con_name','con_mobile','product_num','product_name','product_state','product_price','product_thum_pic','product_class_id','order_num','order_price','order_state','order_accept_time','serv_type','serv_state','serv_accept_time','serv_deal_time','create_user_id','cle_dial_number');

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除字段缓存
	 *
	 * @param int $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return bool
	 */
	private function _clear_fields_cache($field_type)
	{
		$this->cache->delete('confirm_fields'.$field_type);
		$this->cache->delete('available_confirm_fields'.$field_type);
		$this->cache->delete('available_fields'.$field_type);
		$this->cache->delete('jl_field_info_all'.$field_type);
		$this->cache->delete('jl_available_fields_name'.$field_type);
		$this->cache->delete('base_fields'.$field_type);
		$this->cache->delete('available_base_fields'.$field_type);
		if($field_type == FIELD_TYPE_CLIENT || $field_type == FIELD_TYPE_CONTACT)
		{
			$this->cache->delete('confirm_fields'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('available_confirm_fields'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('available_fields'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('jl_field_info_all'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('jl_available_fields_name'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('base_fields'.FIELD_TYPE_CLIENT_CONTACT);
			$this->cache->delete('available_base_fields'.FIELD_TYPE_CLIENT_CONTACT);
		}
		return true;
	}

	/**
	 * 清除选项缓存
	 *
	 * @param int $parent_id 字段id
	 */
	private function _clear_field_detail_cache($parent_id=0)
	{
		if(!empty($parent_id))
		{
			if(is_array($parent_id))
			{
				foreach($parent_id AS $id)
				{
					$this->cache->delete('field_detail'.$id);
				}
			}
			else
			{
				$this->cache->delete('field_detail'.$parent_id);
			}
		}
	}

	/**
	 * 根据选项 获取指定类型自定义字段信息
	 *
	 * @param  int  $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @param bool $only_used	true 只搜可用的自定义字段		false搜该类型的所有自定义字段
	 * @return  array   $fields  返回字段信息
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[id]=> id,
	 * 		[name]=> 字段名称,
	 * 		[fields]=> 字段,
	 * 		[state]=> 字段状态,
	 * 		[field_type]=> 字段类型
	 *  )
	 * 	...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */	
	private function _confirm_fields($field_type,$only_used = TRUE)
	{
		$cache_string = '';
		if($only_used === TRUE)
		$cache_string = 'confirm_fields';
		else
		$cache_string = 'available_confirm_fields';

		if( !$this->cache->get($cache_string.$field_type))
		{
			switch ($field_type)
			{
				case FIELD_TYPE_CLIENT_CONTACT :
					$this->db_read->where_in('field_type',array(0,1));
					break;
				case FIELD_TYPE_CLIENT  :
					$this->db_read->where('field_type',0);
					break;
				case FIELD_TYPE_CONTACT :
					$this->db_read->where('field_type',1);
					break;
				case FIELD_TYPE_PRODUCT :
					$this->db_read->where('field_type',2);
					break;
				case FIELD_TYPE_ORDER :
					$this->db_read->where('field_type',3);
					break;
				case FIELD_TYPE_SERVICE :
					$this->db_read->where('field_type',4);
					break;
				default:return false;
			}
			if($only_used === TRUE)
			{
				$this->db_read->where('state',1);
			}

			$this->db_read->where("if_custom","1");//自定义字段
			$this->db_read->select("id,name,fields,state,field_type,data_type,if_require,default,jl_series,jl_field_type,datefmt");
			$this->db_read->order_by("custom_poz","ASC");
			$query  = $this->db_read->get("est_fields");
			$result = $query->result_array();
			$this->cache->save($cache_string.$field_type,$result,600);
		}
		else
		{
			$result = $this->cache->get($cache_string.$field_type);
		}
		return $result;
	}

	/**
	 * 获取某类型全部自定义字段
	 *
	 * @param  int  $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return  array   $fields  返回字段信息
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[id]=> id,
	 * 		[name]=> 字段名称,
	 * 		[fields]=> 字段,
	 * 		[state]=> 字段状态,
	 * 		[field_type]=> 字段类型
	 *  )
	 * 	...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_confirm_fields($field_type)
	{
		$confirm_fields_info = $this->_confirm_fields($field_type,FALSE);
		return $confirm_fields_info;
	}

	/**
	 * 获取某类型可用的自定义字段
	 *
	 * @param  int  $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return  array   $fields  返回字段信息
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[id]=> id,
	 * 		[name]=> 字段名称,
	 * 		[fields]=> 字段,
	 * 		[state]=> 字段状态,
	 * 		[field_type]=> 字段类型
	 *  )
	 * 	...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_available_confirm_fields($field_type)
	{
		$available_confirm_fields_info = $this->_confirm_fields($field_type,TRUE);
		return $available_confirm_fields_info;
	}

	/**
	 * 获取某自定义字段下拉选项详情
	 *
	 * @param int $parent_id 字段id
	 * @return array 一维数组
	 */
	private function _field_details($parent_id = 0)
	{
		$field_detail = array();
		if(empty($parent_id))
		{
			return $field_detail;
		}
		if(!is_array($this->cache->get('field_detail'.$parent_id)))
		{
			$this->db_read->where('parent_id',$parent_id);
			$this->db_read->order_by("sorts","ASC");
			$query = $this->db_read->get("est_fields_detail");
			$field_detail = $query->result_array();

			$this->cache->save('field_detail'.$parent_id, $field_detail, 600);
		}
		else
		{
			$field_detail = $this->cache->get('field_detail'.$parent_id);
		}
		return $field_detail;
	}

	/**
	 * 获取一个或多个自定义字段 下拉选项信息（详细内容）
	 *
	 * @param string/array $parent_id 用逗号隔开  $parent_id 字段id
	 * @return array   选项数据
	 * <code>
	 * array(
	 * 	[parent_id]=>array(
	 * 					[id]=> 选项id,
	 * 					[parent_id]=> 字段id,
	 * 					[name]=> 选项名称,
	 * 					[sorts]=> 选项顺序
	 *		 		)
	 * 				...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_field_details($parent_id = '')
	{
		$field_detail_info = array();
		if(empty($parent_id))
		{
			return $field_detail_info;
		}
		if(!is_array($parent_id))
		{
			$parent_id = explode(',',$parent_id);
		}

		foreach($parent_id AS $id)
		{
			$detail = $this->_field_details($id);
			if(!empty($detail))
			{
				$field_detail_info[$id] = $detail;
			}
		}

		return $field_detail_info;
	}

	/**
	 *获取一个或多个自定义字段 下拉选项名称
	 *
	 * @param string/array  $parent_id 用逗号隔开 字段id
	 * @return array   选项数据
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[0]=> 选项名称
	 * 		[1]=> 选项名称
	 * 		[2]=> 选项名称
	 * 		...
	 * 	)
	 * ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_field_details_name($parent_id = '')
	{
		$field_detail = array();
		$result = $this->get_field_details($parent_id);
		foreach($result AS $detail)
		{
			foreach($detail AS $value)
			{
				$field_detail[$value["parent_id"]][] = $value["name"];
			}
		}
		return $field_detail;
	}

	/**
	 * 保存自定义下拉字段选项
	 *
	 * @param int   $parent_id    自定义字段ID
	 * @param array $options      选项信息
	 * @return boolen 
	 * @author zgx
	 */
	public function save_fields_detail($parent_id = 0,$options = array())
	{
		if (!$parent_id)
		{
			return FALSE;
		}
		else
		{
			//删除该类型下的数据
			$this->db_write->delete("est_fields_detail",array("parent_id"=>$parent_id));

			$data    = array();
			$i = 0;
			foreach ($options AS $name)
			{
				if ($name)
				{
					$data[] = array("parent_id"=>$parent_id,"name"=>$name,"sorts"=>$i);
					$i ++;
				}
			}
			if(!empty($data))
			{
				$this->db_write->insert_batch('est_fields_detail', $data);
			}
			$this->_clear_field_detail_cache(array("parent_id"=>$parent_id)); //清除选项缓存
			return TRUE;
		}
	}

	/**
	 * 添加两个空白自定义字段
	 * 
	 * @param int $field_type 类型 
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return array
	 *  <code>
	 * array(
	 * 		[id1]=>id1,
	 * 		[name1]=>name1,
	 * 		[custom_poz1]=>custom_poz1,
	 * 		[fields1]=>fields1,
	 * 		[id2]=>id2,
	 * 		[name2]=>name2,
	 * 		[custom_poz2]=>custom_poz2,
	 * 		[fields2]=>fields2
	 *  )
	 * </code>
	 * @author zgx
	 */
	public function add_two_empty_fields($field_type)
	{
		//字段表 est_fields 添加新信息
		//得到最大poz
		$this->db_read->select_max('custom_poz');
		$this->db_read->where('field_type',$field_type);
		$query = $this->db_read->get('est_fields');
		$max_poz = $query->row()->custom_poz;
		$max_poz = empty($max_poz) ? -1 : $max_poz;

		$new_poz1 = $max_poz+1;
		$new_poz2 = $max_poz+2;

		switch($field_type)
		{
			case FIELD_TYPE_CLIENT:
				$fields1 = 'cle_'.($new_poz1+1);
				$fields2 = 'cle_'.($new_poz2+1);
				$table_name = 'est_client';
				break;
			case FIELD_TYPE_CONTACT:
				$fields1 = 'con_'.($new_poz1+1);
				$fields2 = 'con_'.($new_poz2+1);
				$table_name = 'est_contact';
				break;
			case FIELD_TYPE_PRODUCT:
				$fields1 = 'product_'.($new_poz1+1);
				$fields2 = 'product_'.($new_poz2+1);
				$table_name = 'est_product';
				break;
			case FIELD_TYPE_ORDER:
				$fields1 = 'order_'.($new_poz1+1);
				$fields2 = 'order_'.($new_poz2+1);
				$table_name = 'est_order';
				break;
			case FIELD_TYPE_SERVICE:
				$fields1 = 'serv_'.($new_poz1+1);
				$fields2 = 'serv_'.($new_poz2+1);
				$table_name = 'est_service';
				break;
			default: return false;
		}

		$name =  "自定义";
		$data1 = array(
		"name"       => $name,
		"fields"     => $fields1,
		"if_custom"  => 1,
		"custom_poz" => $new_poz1,
		"state"      => 0,
		"field_type" => $field_type
		);
		$data2 = array(
		"name"       => $name,
		"fields"     => $fields2,
		"if_custom"  => 1,
		"custom_poz" => $new_poz2,
		"state"      => 0,
		"field_type" => $field_type
		);
		$this->db_write->insert("est_fields",$data1);
		$id1 = $this->db_write->insert_id();
		$this->db_write->insert("est_fields",$data2);
		$id2 = $this->db_write->insert_id();
		$this->operation_database_column(array($fields1,$fields2),$table_name,'add');
		$this->_clear_fields_cache($field_type);//清除缓存
		return array('id1'=>$id1,'name1'=>$name,'custom_poz1'=>$new_poz1,'fields1'=>$fields1,'id2'=>$id2,'name2'=>$name,'custom_poz2'=>$new_poz2,'fields2'=>$fields2);
	}

	/**
	 * 操作数据库字段（自定义字段增删改，相应表字段改动）
	 *
	 * @param array $fields      字段
	 * @param int $table_name    表名
	 * @param int $opt_type      add添加   del删除 update修改（文本域时需要text数据类型）
	 * @return bool
	 * @author zgx
	 */
	private function operation_database_column($fields,$table_name,$opt_type)
	{
		$data_field = array();
		//添加字段
		switch($opt_type)
		{
			case 'add':
				foreach ($fields AS $field)
				{
					$data_field[] = "ADD $field VARCHAR(50) DEFAULT '' NOT NULL";
				}
				$data_field = implode(",",$data_field);
				break;
			case 'del':
				foreach ($fields AS $field)
				{
					$data_field[] = "DROP $field";
				}
				$data_field = implode(",",$data_field);
				break;
			case 'update_to_text':
				foreach ($fields AS $field)
				{
					$data_field[] = "MODIFY COLUMN $field text";
				}
				$data_field = implode(",",$data_field);
				break;
			case 'update_to_jl':
				foreach($fields AS $field)
				{
					$data_field[] = "ADD ".$field."_1 text";
					$data_field[] = "ADD ".$field."_2 text";
					$data_field[] = "ADD ".$field."_3 text";
				}
				$data_field = implode(",",$data_field);
				break;
			default:
				break;
		}
		$str_sql = "ALTER TABLE ".$table_name." ".$data_field;

		//导入客户数据
		if ($table_name == 'est_client')
		{
			$this->db_write->query("ALTER TABLE est_import_error_data ".$data_field);//错误数据表
			if($this->db_read->table_exists('est_client_history'))
			{
				$this->db_write->query("ALTER TABLE est_client_history ".$data_field);//客户历史数据表
			}
		}
		return $this->db_write->query($str_sql);
	}

	/**
	 * 删除两个自定义字段
 	 * @param int $field_type 字段类型
 	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @param string $first_unit 第一个字段名
	 * @param string $second_unit 第二个字段名
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function del_two_fields($field_type,$first_unit='',$second_unit='')
	{
		if(!isset($field_type)||empty($first_unit)||empty($second_unit))
		{
			return false;
		}
		switch($field_type)
		{
			case FIELD_TYPE_CLIENT:
				$control_flag     = 12;
				$table_name = 'est_client';
				break;
			case FIELD_TYPE_CONTACT:
				$control_flag = 10;
				$table_name = 'est_contact';
				break;
			case FIELD_TYPE_PRODUCT:
				$control_flag = 12;
				$table_name = 'est_product';
				break;
			case FIELD_TYPE_ORDER:
				$control_flag = 0;
				$table_name = 'est_order';
				break;
			case FIELD_TYPE_SERVICE:
				$control_flag = 0;
				$table_name = 'est_service';
				break;
			default: return false;
		}

		//判断字段是否符合要求
		$this->db_read->where_in('fields',array($first_unit,$second_unit));
		$this->db_read->where(array('if_custom'=>1,'field_type'=>$field_type,'custom_poz >'=>($control_flag-1)));
		$query = $this->db_read->get('est_fields');
		if($query->num_rows() !=2 )
		{
			return false;
		}
		$fields = $query->result_array();
		$field_info1 = $fields[0];
		$field_info2 = $fields[1];

		//删除字段选项表信息
		$this->db_write->where_in("parent_id",array($field_info1['id'],$field_info2['id']));
		$this->db_write->delete("est_fields_detail");
		$this->_clear_field_detail_cache(array($field_info1['id'],$field_info2['id'])); //清除选项缓存


		//删除字段表信息
		$this->db_write->where_in("id",array($field_info1['id'],$field_info2['id']));
		$this->db_write->delete("est_fields");
		$this->_clear_fields_cache($field_type);//清除字段缓存

		//删除数据表中对应的字段
		return  $this->operation_database_column(array($field_info1['fields'],$field_info2['fields']),$table_name,"del");
	}

	/**
	 * 更新自定义字段信息
	 *
	 * @param array $fields  字段
	 * @param array $state   字段状态
	 * @param array $field_value    字段名称
	 * @param int $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function update_fields_confirm($fields,$state,$field_value,$field_type)
	{
		if(empty($fields) || !is_array($fields) ||!is_array($state) ||!is_array($field_value) || count($fields) != count($state) || count($state) != count($field_value))
		{
			return false;
		}
		//更新
		$update    = array(); // est_field 表，更新自定义字段信息
		$unselect_field_arr = array();
		foreach ($fields AS $key => $field)
		{
			if($state[$key] != 1)
			{
				$field_value[$key] = '自定义';
				//删除没有选中字段的选项
				array_push($unselect_field_arr,"'".$field."'");
			}
			//更新自定义字段信息
			$update[$key] = array(
			"fields" => $field,
			"name"   => $field_value[$key],
			"state"  => $state[$key]
			);
			//没选字段文本类型回复默认
			if($state[$key] != 1)
			{
				$update[$key]['data_type'] = DATA_TYPE_INPUT;
			}
		}

		$this->db_write->update_batch("est_fields",$update,"fields");
		if (!empty($unselect_field_arr))
		{
			$unselect_fields_str = implode(',',$unselect_field_arr);
			$query_id = $this->db_read->query("SELECT id FROM est_fields WHERE est_fields.fields in (".$unselect_fields_str.")");
			$parent_id_arr = array();
			foreach($query_id->result_array() AS $id_value)
			{
				$parent_id_arr[] = $id_value['id'];
			}
			if(!empty($parent_id_arr))
			{
				$this->db_write->query("delete FROM est_fields_detail WHERE parent_id in (".implode(',',$parent_id_arr).")");
				$this->_clear_field_detail_cache($parent_id_arr); //清除选项缓存
			}
		}
		return $this->_clear_fields_cache($field_type);//清除缓存
	}

	/**
	 * 自定义字段搜索条件组合
	 * @param string $field_confirm_values 自定义字段搜索内容
	 * 	<pre> 
	 * 		json结构的检索条件 	
	 * 		{"1":{"field_id":"2","make":"=","field_content":"需本人两天后回访"}}
	 * 	</pre>
	 * @param $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return array 
	 * 第一种情况 $type == FIELD_TYPE_CLIENT_CONTACT 判断有没联系人检索
	 * <code>
	 * array(
	 * 		[wheres]=>$wheres,
	 * 		[condition_contact]=>condition_contact
	 * )
	 * </code>
	 * 其他情况
	 * <code>
	 * array(
	 * 		[0]=> where条件,
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_field_confirm_condition($field_confirm_values,$field_type)
	{
		$wheres = array();
		if(empty($field_confirm_values))
		{
			return $wheres;
		}
		//转换格式
		$this->load->library("json");
		$field_confirm_values = $this->json->decode($field_confirm_values,1);
		$confirm_fields = $this->_get_confirm_fields_only_fields($field_type);//获取字段name

		if($field_type == FIELD_TYPE_CLIENT_CONTACT)
		{
			$condition_contact = false;
			$contact_confirm_fields = $this->_get_confirm_fields_only_fields(FIELD_TYPE_CONTACT);//联系人自定义字段列
		}

		foreach($field_confirm_values AS $value)
		{
			if($field_type == FIELD_TYPE_CLIENT_CONTACT)//标记是否检索联系人相关信息
			{
				if (in_array($confirm_fields[$value['field_id']],$contact_confirm_fields))
				{
					$condition_contact = true;
				}
			}
			$fields = $confirm_fields[$value['field_id']];
			if($value['type']=='jl')
			{
				$fields = $fields.'_'.$value['jb'];
			}
			switch($value['make'])
			{
				case 'like':
					$wheres[] = $fields." LIKE '%".$value['field_content']."%'";
					break;
				case '>':
					$wheres[] = $fields." > '".$value['field_content']."'";
					break;
				case '<':
					$wheres[] = $fields." < '".$value['field_content']."'";
					break;
				case '=':
					$wheres[] = $fields." = '".$value['field_content']."'";
					break;
				case '!=':
					$wheres[] = $fields." != '".$value['field_content']."'";
					break;
				case '>=':
					$wheres[] = $fields." >= '".$value['field_content']."'";
					break;
				case '<=':
					$wheres[] = $fields." <= '".$value['field_content']."'";
					break;
				default:
					break;
			}
		}
		if($field_type == FIELD_TYPE_CLIENT_CONTACT)
		{
			return array("wheres"=>$wheres,"condition_contact"=>$condition_contact);
		}
		else
		{
			return $wheres;
		}
	}
	/**
	 * 获取某类型可用是的自定义字段名称
	 * 
	 * @param  int $field_type  字段类型 
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @param bool $if_only_used	true 只搜可用的自定义字段		false搜该类型的所有自定义字段
	 * @return  array  
	 * 
	 * @author zgx
	 */
	private function _get_confirm_fields_only_fields($field_type,$if_only_used = TRUE)
	{
		$result = $this->_confirm_fields($field_type,$if_only_used);
		$column = array();
		foreach ($result AS $value)
		{
			if ($value["fields"] )
			{
				$column[$value["id"]] = $value["fields"];
			}
		}
		return $column;
	}

	/**
	 * 获取某类型可用的字段 (基本字段+自定义字段)
	 *
	 * @param  $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @return array 以id为键值 的field（id，name，fields,field_list_width） 数组
	 * @author zt
	 */
	public function get_available_fields($field_type)
	{
		$use_contact = 0;
		if($field_type==FIELD_TYPE_CLIENT_CONTACT || $field_type==FIELD_TYPE_ORDER ||$field_type==FIELD_TYPE_SERVICE)
		{
			//获取系统配置参数
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			//是否使用联系人模块 0用 1不用
			$use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
			if($use_contact==1 && $field_type==FIELD_TYPE_CLIENT_CONTACT)
			{
				$field_type = FIELD_TYPE_CLIENT;
			}
		}
		if(!$this->cache->get('available_fields'.$field_type))
		{
			switch ($field_type)
			{
				case FIELD_TYPE_CLIENT_CONTACT :
					$this->db_read->where_in('field_type',array(0,1));
					break;
				case FIELD_TYPE_CLIENT  :
					$this->db_read->where('field_type',0);
					break;
				case FIELD_TYPE_CONTACT :
					$this->db_read->where('field_type',1);
					break;
				case FIELD_TYPE_PRODUCT :
					$this->db_read->where('field_type',2);
					break;
				case FIELD_TYPE_ORDER :
					$this->db_read->where('field_type',3);
					break;
				case FIELD_TYPE_SERVICE :
					$this->db_read->where('field_type',4);
					break;
				default:return false;
			}
			$this->db_read->order_by("id","ASC");
			$this->db_read->where('state',1);
			$this->db_read->select('id,name,fields,field_type,if_custom,field_list_width,data_type,if_require,default,jl_series,jl_field_type,datefmt');
			$fields_query = $this->db_read->get('est_fields');
			$fields_info = $fields_query->result_array();
			$fields = array();
			foreach ($fields_info as $field)
			{
				$fields[$field['id']] = $field;
			}
			$this->cache->save('available_fields'.$field_type,$fields,600);
		}
		else
		{
			$fields = $this->cache->get('available_fields'.$field_type);
		}
		//不使用联系人模块
		if($use_contact==1 && ($field_type==FIELD_TYPE_ORDER ||$field_type==FIELD_TYPE_SERVICE))
		{
			$without_contact_field = array();
			foreach($fields AS $key => $value)
			{
				if($value['fields']!='con_name' && $value['fields']!='con_mobile')
				{
					$without_contact_field[$key] = $value;
				}
			}
			$fields = $without_contact_field;
		}
		return $fields;
	}

	/**
	 * 获取某类型 基本字段（不包括可用不可用的）
	 *  @param  $field_type 字段类型
	 * <ul>
	 * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
	 * <li>FIELD_TYPE_CLIENT 客户</li>
	 * <li>FIELD_TYPE_CONTACT 联系人</li>
	 * <li>FIELD_TYPE_PRODUCT 产品</li>
	 * <li>FIELD_TYPE_ORDER 订单</li>
	 * <li>FIELD_TYPE_SERVICE 客服服务</li>
	 * </ul>
	 * @param bool $only_used	true 只搜可用的基本字段		false搜该类型的所有基本字段
	 * @return array
	 */
	private function _base_fields($field_type=FIELD_TYPE_CLIENT,$only_used=false)
	{
		$cache_string = '';
		$use_contact = 0;
		if($field_type==FIELD_TYPE_CLIENT_CONTACT || $field_type==FIELD_TYPE_ORDER ||$field_type==FIELD_TYPE_SERVICE)
		{
			//获取系统配置参数
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			//是否使用联系人模块 0用 1不用
			$use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
			if($use_contact==1 && $field_type==FIELD_TYPE_CLIENT_CONTACT)
			{
				$field_type = FIELD_TYPE_CLIENT;
			}
		}
		if($only_used === TRUE)
		$cache_string = 'base_fields';
		else
		$cache_string = 'available_base_fields';
		if(!$this->cache->get($cache_string.$field_type))
		{
			switch ($field_type)
			{
				case FIELD_TYPE_CLIENT_CONTACT :
					$this->db_read->where_in('field_type',array(0,1));
					break;
				case FIELD_TYPE_CLIENT  :
					$this->db_read->where('field_type',0);
					break;
				case FIELD_TYPE_CONTACT :
					$this->db_read->where('field_type',1);
					break;
				case FIELD_TYPE_PRODUCT :
					$this->db_read->where('field_type',2);
					break;
				case FIELD_TYPE_ORDER :
					$this->db_read->where('field_type',3);
					break;
				case FIELD_TYPE_SERVICE :
					$this->db_read->where('field_type',4);
					break;
				default:return false;
			}
			if($only_used === TRUE)
			{
				$this->db_read->where('state',1);
			}
			$this->db_read->order_by("id","ASC");
			$this->db_read->where('if_custom',0);
			$this->db_read->select('id,name,fields,state');
			$fields_query = $this->db_read->get('est_fields');
			$fields_info = $fields_query->result_array();
			$this->cache->save($cache_string.$field_type,$fields_info,600);
		}
		else
		{
			$fields_info = $this->cache->get($cache_string.$field_type);
		}
		//不使用联系人模块
		if($use_contact==1 && ($field_type==FIELD_TYPE_ORDER ||$field_type==FIELD_TYPE_SERVICE))
		{
			$without_contact_field = array();
			foreach($fields_info AS $key => $value)
			{
				if($value['fields']!='con_name' && $value['fields']!='con_mobile')
				{
					$without_contact_field[$key] = $value;
				}
			}
			$fields_info = $without_contact_field;
		}
		return $fields_info;
	}

	/**
     * 获取所有的基本字段
     * @param int $field_type
     * @return array
     */
	public function get_base_fields($field_type=FIELD_TYPE_CLIENT)
	{
		$fields = array();
		$fields_info = $this->_base_fields($field_type,false);
		$unchange_state_fields = $this->_unchange_state_fields;//state不可修改字段
		foreach ($fields_info as $field)
		{
			if(in_array($field['fields'],$unchange_state_fields))
			{
				$field['is_disabed'] = 1;
			}
			else
			{
				$field['is_disabed'] = 0;
			}
			$fields[$field['id']] = $field;
		}
		return $fields;
	}

	/**
     * 获取可用的基本字段
     * @param int $field_type
     * @return array
     */
	public function get_base_available_fields($field_type=FIELD_TYPE_CLIENT)
	{
		$fields = array();
		$fields_info = $this->_base_fields($field_type,true);
		foreach($fields_info as $field)
		{
			$fields[$field['fields']] = $field;
		}
		return $fields;
	}


	/**
	 * 保存基本信息的配置
	 *
	 * @param int $field_type
	 * @param array $is_use
	 * @return array
	 * @author zgx
	 */
	public function save_base_field_state($field_type,$is_use=array())
	{
		if(empty($is_use))
		{
			return false;
		}
		$update = array();
		$unchange_state_fields = $this->_unchange_state_fields;//state不可修改字段
		foreach($is_use as $field=>$state)
		{
			if(!in_array($field,$unchange_state_fields))
			{
				$update[] = array('fields'=>$field,'state'=>$state);
			}
		}
		if(!empty($update))
		{
			$this->db_write->update_batch("est_fields",$update,"fields");
			$this->_clear_fields_cache($field_type);
		}
		return true;
	}

	/**
	 * 获取某一字段的信息
	 * @param int $field_id 字段id
	 * @return array|bool
	 */
	public function get_one_field_info($field_id=0)
	{
		if(empty($field_id))
		{
			return false;
		}
		$this->db_read->where('id',$field_id);
		$this->db_read->select("name,fields,state,data_type,field_type,if_require,default,jl_series,jl_field_type,datefmt");
		$result = $this->db_read->get('est_fields');
		return $result->row_array();
	}

	/**
	 * 更改某一字段的信息
	 * @param int $field_id 字段id
	 * @param array $update
	 * @return bool
	 */
	public function update_one_field_info($field_id=0,$update=array())
	{
		if(empty($field_id)||empty($update))
		{
			return false;
		}
		$can_change_fields = array('name','state','field_list_width','data_type','if_require','default','jl_series','jl_field_type','datefmt');
		$update_array = array();
		foreach($update AS $key=>$value)
		{
			if(in_array($key,$can_change_fields))
			{
				$update_array[$key] = $value;
			}
		}
		if(!empty($update_array))
		{
			$field_info = $this->get_one_field_info($field_id);
			$this->db_write->where("id",$field_id);
			$result = $this->db_write->update("est_fields",$update_array);
			if($result)
			{
				if(isset($update_array['data_type'])&&($update_array['data_type']==DATA_TYPE_TEXTAREA || $update_array['data_type']==DATA_TYPE_JL||$update_array['data_type']==DATA_TYPE_CHECKBOXJL))
				{
					if($field_info)
					{
						switch($field_info['field_type'])
						{
							case FIELD_TYPE_CLIENT:
								$table_name = 'est_client';
								break;
							case FIELD_TYPE_CONTACT:
								$table_name = 'est_contact';
								break;
							case FIELD_TYPE_PRODUCT:
								$table_name = 'est_product';
								break;
							case FIELD_TYPE_ORDER:
								$table_name = 'est_order';
								break;
							case FIELD_TYPE_SERVICE:
								$table_name = 'est_service';
								break;
							default: return false;
						}
						if($update_array['data_type']==DATA_TYPE_TEXTAREA && $field_info['data_type']!=DATA_TYPE_TEXTAREA)
						{
							$this->operation_database_column(array($field_info['fields']),$table_name,'update_to_text');
						}
						else if($update_array['data_type']==DATA_TYPE_JL||$update_array['data_type']==DATA_TYPE_CHECKBOXJL)
						{
							if(!$this->db_read->field_exists($field_info['fields']."_1",$table_name))
							{
								$this->operation_database_column(array($field_info['fields']),$table_name,'update_to_jl');
							}
						}
					}
				}
				$this->_clear_fields_cache($field_info['field_type']);//清除缓存
			}
			return $result;
		}
		return false;
	}


	//==========级联=========================================================================================
    /**
     * 保存级联选项信息
     *
     * @param int $field_id
     * @param int $p_id
     * @param int $type
     * @param array $options
     * @param int $field_type
     *
     * @return bool
     */

    public function save_confirm_jl($field_id=0,$p_id=0,$type=0,$options=array(),$field_type=FIELD_TYPE_CLIENT)
	{
		if (empty($field_id)||empty($options)||empty($type))
		{
			return FALSE;
		}
		else
		{
			$insert_data    = array();
			$del_id = array();
			foreach ($options AS $key=>$op)
			{
				if($op['id']!=0)
				{
					if(empty($op['name']))
					{
						$del_id[] = $op['id'];
					}
					else
					{
						$this->db_write->query("UPDATE est_fields_jl SET name='".$op['name']."' WHERE id=".$op['id']." AND name!='".$op['name']."'");
					}
				}
				elseif (!empty($op['name']))
				{
					$insert_data[] = array("fields_id"=>$field_id,"parent_id"=>$p_id,"name"=>$op['name'],"type"=>$type);
				}
			}
			if(!empty($insert_data))
			{
				$this->db_write->insert_batch('est_fields_jl', $insert_data);
			}
			if(!empty($del_id))
			{
				//1、获取第二级id
				if($type==1)
				{
					$this->db_read->where_in('parent_id',$del_id);
					$this->db_read->select('id');
					$query = $this->db_read->get('est_fields_jl');
					foreach($query->result_array() as $value)
					{
						$del_id[] = $value['id'];
					}
				}
				$this->db_write->where_in('id',$del_id);
				if($type==1||$type==2)
				$this->db_write->or_where_in('parent_id',$del_id);
				$this->db_write->delete("est_fields_jl");
			}
            $this->_clear_fields_cache($field_type);//清除缓存

			return TRUE;
		}
	}

	/**
	 * 获取某类型某级的选项
	 *
	 * @param int $field_id
	 * @param int $p_id
	 * @param int $type
	 * @return array|bool
	 */
	public function get_jl_options($field_id = 0, $p_id = 0,$type=0)
	{
		if(empty($field_id))
		{
			return false;
		}
		$this->db_read->where("type",$type);
		$this->db_read->where("parent_id",$p_id);
		$this->db_read->where("fields_id",$field_id);
		$this->db_read->select("id, name");
		$query = $this->db_read->get("est_fields_jl");
		//数据
		$result = $query->result_array();
		$jl_info = array();
		foreach($result AS $key => $value)
		{
			$jl_info[$value["id"]] = $value["name"];
		}
		return $jl_info;
	}

	/**
	 * 级联判断是有第三级
	 *
	 * @param int $field_id
	 * @return array|bool
	 */
	public function check_jl_if_have_three($field_id=0)
	{
		if(empty($field_id))
		{
			return false;
		}
		if(!is_array($field_id))
		{
			$field_id = explode(',',$field_id);
		}
		$this->db_read->where("type",3);
		$this->db_read->where_in("fields_id",$field_id);
		$this->db_read->group_by("fields_id");
		$this->db_read->select("fields_id");
		$query_three = $this->db_read->get("est_fields_jl");
		$have_three = array();
		foreach($query_three->result_array() as $value)
		{
			$have_three[$value['fields_id']] = 1;
		}
		return $have_three;
	}

	/**
	 * 获取某类型所有级联第一级信息 及 编辑页面上已存值的下一级信息
	 * @param int|array $field_id 字段id
	 * @param int|array $p_id 父id
	 * @param array (field_id=>field_name) 整理数据上用到
	 * @return array (field_name_1=>array(级联id=>级联名))
	 */
	public function get_jl_first_options($field_id=0,$p_id=0,$jl_field=array())
	{
		if(empty($field_id))
		{
			return false;
		}
		if(!is_array($field_id))
		{
			$field_id = explode(',',$field_id);
		}
		if(!is_array($p_id))
		{
			$p_id = explode(',',$p_id);
		}
		$this->db_read->where_in("parent_id",$p_id);
		$this->db_read->where_in("fields_id",$field_id);
		$this->db_read->select("id, name,fields_id,type");
		$query = $this->db_read->get("est_fields_jl");
		//数据
		$result = $query->result_array();
		$jl_info = array();
		foreach($result AS $key => $value)
		{
			if(!empty($jl_field))
			{
				$jl_info[$jl_field[$value['fields_id']].'_'.$value["type"]][$value["id"]] = $value["name"];
			}
			else
			{
				$jl_info[$value['fields_id'].'_'.$value["type"]][$value["id"]] = $value["name"];
			}
		}
		return $jl_info;
	}

    /**
     * 获取某类型可用的级联字段名
     * @param int $field_type
     * @return array
     */
	public function get_jl_field_name($field_type=FIELD_TYPE_CLIENT)
	{
		if(!$this->cache->get('jl_available_fields_name'.$field_type))
		{
			$fields_name = array();
			$fields = $this->_confirm_fields($field_type,TRUE);//获取某类型可用自定义字段信息
			foreach($fields as $key=>$field_info)
			{
				if($field_info['data_type']==DATA_TYPE_JL || $field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$fields_name[] = array($field_info['fields'],$field_info['data_type']);;
				}
			}
			$this->cache->save('jl_available_fields_name'.$field_type,$fields_name,600);
		}
		else
		{
			$fields_name = $this->cache->get('jl_available_fields_name'.$field_type);
		}
		return $fields_name;
	}

    /**
     * 获取某类型所有级联字段信息
     * @param int $field_type 字段类型
     * <ul>
     * <li>FIELD_TYPE_CLIENT_CONTACT 客户+联系人</li>
     * <li>FIELD_TYPE_CLIENT 客户</li>
     * <li>FIELD_TYPE_CONTACT 联系人</li>
     * <li>FIELD_TYPE_PRODUCT 产品</li>
     * <li>FIELD_TYPE_ORDER 订单</li>
     * <li>FIELD_TYPE_SERVICE 客服服务</li>
     * </ul>
     * @return array (级联id=>级联名)
     */
	public function get_all_jl_info($field_type=FIELD_TYPE_CLIENT)
	{
		if(!$this->cache->get('jl_field_info_all'.$field_type))
		{
			$fields_id = array();
			$fields = $this->get_available_confirm_fields($field_type);//获取某类型可用自定义字段信息
			foreach($fields as $key=>$field_info)
			{
				if($field_info['data_type']==DATA_TYPE_JL || $field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$fields_id[] = $field_info['id'];
				}
			}
			$jl_info = array();
			if(!empty($fields_id))
			{
				$this->db_read->where_in("fields_id",$fields_id);
				$this->db_read->select("id, name");
				$query = $this->db_read->get("est_fields_jl");
				$result = $query->result_array();//数据
				foreach($result AS $k => $jl)
				{
					$jl_info[$jl['id']] = $jl["name"];
				}
			}
			$this->cache->save('jl_field_info_all'.$field_type,$jl_info,600);
		}
		else
		{
			$jl_info = $this->cache->get('jl_field_info_all'.$field_type);
		}
		return $jl_info;
	}

	/**
	 * 获取关联多选项所有选项信息
	 *
	 * @param int $field_id
	 * @param int $check_p_id
	 * @param array $jl_field
	 * @return array
	 */
	public function get_checkbox_options($field_id=0)
	{
		if(empty($field_id))
		{
			return false;
		}
		if(!is_array($field_id))
		{
			$field_id = explode(',',$field_id);
		}
		$this->db_read->where_in("fields_id",$field_id);
		$this->db_read->select("id,parent_id,name,fields_id,type");
		$query = $this->db_read->get("est_fields_jl");
		$result = $query->result_array();
		$jl_info = array();
		foreach($result AS $key => $value)
		{
			$jl_info[$value['fields_id'].'_'.$value["type"]][$value["id"]] = array('name'=>$value["name"],'p_id'=>$value["parent_id"]);
		}
		return $jl_info;
	}
}