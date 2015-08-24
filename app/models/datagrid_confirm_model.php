<?php
class Datagrid_confirm_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 特殊字段需要将id转化成名称再使用
	 *
	 * @author zt
	 */
	private $_special_fields = array(
	'user_id' => 'user_name',
	'dept_id' => 'dept_name',
	'cle_creat_user_id' => 'cle_creat_user_name',
	'cle_update_user_id' => 'cle_update_user_name',
	'product_class_id' => 'product_class_name',
	'create_user_id' => 'create_user_name'
	);

	/**
	 * 列表排序字段替换 
	 *<pre>
	 * user_name => user_id
	 * dept_name => dept_id
	 * cle_creat_user_name => cle_creat_user_id
	 * cle_update_user_name => cle_update_user_id
	 * product_class_name => product_class_id
	 * deal_user_name => deal_user_id
	 * </pre>
	 * @param string $org_field
	 * @return string
	 * @author zt
	 */
	public function replace_sort_field($org_field = '')
	{
		if(in_array($org_field,$this->_special_fields))
		{
			$new_field = array_search($org_field, $this->_special_fields);
		}
		else
		{
			$new_field = $org_field;
		}
		return $new_field;
	}

	/**
	 * 特殊字段 显示的时候 id转变为名称
	 *
	 * @param string $field_name 字段名称
	 * @return string 转变后的字段名称
	 * @author zt
	 */
	private function _replace_show_field($field_name)
	{
		if (array_key_exists ($field_name,$this->_special_fields)) {
			return $this->_special_fields[$field_name];
		}
		return $field_name;
	}

	/**
     * list_type 转为 field_type
     *
     * @param $list_type
     * @return bool|int
     */
	private function _list_type_to_field_type($list_type)
	{
		switch ($list_type)
		{
			case LIST_CONFIRM_CLIENT :
			case LIST_CONFIRM_CLIENT_RESOURCE :
				//获取系统配置参数
				$this->load->model("system_config_model");
				$config_info = $this->system_config_model->get_system_config();
				//是否使用联系人模块
				$use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
				if($use_contact!=1)
				{
					return FIELD_TYPE_CLIENT_CONTACT;
				}
			case LIST_CONFIRM_CLIENT_DEAL  :
				return FIELD_TYPE_CLIENT;
			case LIST_CONFIRM_CONTACT :
				return FIELD_TYPE_CONTACT;
			case LIST_COMFIRM_PRODUCT :
				return FIELD_TYPE_PRODUCT;
			case LIST_COMFIRM_ORDER :
				return FIELD_TYPE_ORDER;
			case LIST_COMFIRM_SERVICE :
				return FIELD_TYPE_SERVICE;
			default:return false;
		}
	}

	/**
     * id转为字段名称
     *
     * @param array $ids
     * @param $list_type
     * @return array
     * @author zt
     */
	private function _id_to_fields($ids=array(),$list_type)
	{
		$field_type = $this->_list_type_to_field_type($list_type);
		$this->load->model('field_confirm_model');
		$available_fields = $this->field_confirm_model->get_available_fields($field_type);
		$fields = array();
		foreach ($ids as $id)
		{
			if(!empty($available_fields[$id]))
			{
				$fields[] = $available_fields[$id]['fields'];
			}
		}
		return $fields;
	}

	/**
	 * 数组排序 
	 * 规则为如果在选中的数组中存在的，则按照选中数组的顺序，如果不存在的，则加到选中数组的后面。
	 * @param  $field_available
	 * @param  $field_selected
     * @return array
     */
	private function _sort_datagrid($field_available,$field_selected=array())
	{
		$sort_result = array();
		$end_needle = count($field_selected);
		foreach ($field_available as $field)
		{
			$field_id = $field['id'];
			if(in_array($field_id,$field_selected))
			{
				$poz = array_search($field_id,$field_selected);
				$sort_result[$poz] = $field;
			}
			else
			{
				$sort_result[$end_needle] = $field;
				$end_needle ++;
			}
		}
		ksort($sort_result);
		$sort_result = array_values($sort_result);
		return $sort_result;
	}

	/**
	 * 得到当前坐席指定类型列表的配置字段，包括选中的和没有选中的字段
	 * 
	 * @param  int $list_type  列表类型
	 * <ul>
	 * <li>LIST_CONFIRM_CLIENT 客户管理</li>
	 * <li>LIST_CONFIRM_CONTACT 联系人</li>
	 * <li>LIST_CONFIRM_CLIENT_RESOURCE 资源调配</li>
	 * <li>LIST_CONFIRM_CLIENT_DEAL 数据处理</li>
	 * <li>LIST_COMFIRM_PRODUCT 产品</li>
	 * <li>LIST_COMFIRM_ORDER 订单</li>
	 * <li>LIST_COMFIRM_SERVICE 客服服务</li>
	 * </ul>
	 * @return array $display_info 返回值中将已经选中的列排在最上方。
	 * @author zt
	 */
	public function get_datagrid_confirm_fields($list_type)
	{
		$user_id = $this->session->userdata("user_id");
//		if(!$this->cache->get('datagrid_confirm_field'.$user_id.'_'.$list_type))
//		{
			$this->load->model('user_model');
			$fields_display = $this->user_model->get_list_display_id($user_id,$list_type);
			$fields_display_array = explode(',',$fields_display);
			$field_type = $this->_list_type_to_field_type($list_type);
			$this->load->model('field_confirm_model');
			$all_fields = $this->field_confirm_model->get_available_fields($field_type);

			$fields = array();
			foreach ($all_fields as $field)
			{
				if(in_array($field['id'],$fields_display_array))
				{
					$field['if_display'] = 1;
				}
				else
				{
					$field['if_display'] = 0;
				}
				$fields[] = $field;
			}
			$fields = $this->_sort_datagrid($fields,$fields_display_array);
//			$this->cache->save('datagrid_confirm_field'.$user_id.'_'.$list_type,$fields,600);
//		}
//		else
//		{
//			$fields = $this->cache->get('datagrid_confirm_field'.$user_id.'_'.$list_type);
//		}
		return $fields;
	}

	/**
	 * 得到当前坐席指定类型列表需要显示的字段，对其中的特殊字段会展示的时候展示名称
	 *<pre>
	 * user_name => user_id
	 * dept_name => dept_id
	 * cle_creat_user_name => cle_creat_user_id
	 * cle_update_user_name => cle_update_user_id
	 * product_class_name => product_class_id
	 * deal_user_name => deal_user_id
	 * </pre>
	 * @param  int $list_type  列表类型
	 * <ul>
	 * <li>LIST_CONFIRM_CLIENT 客户管理</li>
	 * <li>LIST_CONFIRM_CONTACT 联系人</li>
	 * <li>LIST_CONFIRM_CLIENT_RESOURCE 资源调配</li>
	 * <li>LIST_CONFIRM_CLIENT_DEAL 数据处理</li>
	 * <li>LIST_COMFIRM_PRODUCT 产品</li>
	 * <li>LIST_COMFIRM_ORDER 订单</li>
	 * <li>LIST_COMFIRM_SERVICE 客服服务</li>
	 * </ul>                  
	 * @return array $display_info
	 */
	public function get_datagrid_show_fields($list_type)
	{
		$user_id = $this->session->userdata("user_id");
//		if(!$this->cache->get('datagrid_show_field'.$user_id.'_'.$list_type))
//		{
			$this->load->model('user_model');
			$fields_display = $this->user_model->get_list_display_id($user_id,$list_type);
			$fields_display_array = explode(',',$fields_display);
			$field_type = $this->_list_type_to_field_type($list_type);
			$this->load->model('field_confirm_model');
			$fields_available = $this->field_confirm_model->get_available_fields($field_type);
			$fields = array();
			foreach ($fields_available as $field)
			{
				if(in_array($field['id'],$fields_display_array))
				{
					$field['fields'] = $this->_replace_show_field($field['fields']);
					$fields[] = $field;
				}
			}
			$fields = $this->_sort_datagrid($fields,$fields_display_array);
//			$this->cache->save('datagrid_show_field'.$user_id.'_'.$list_type,$fields,600);
//		}
//		else
//		{
//			$fields = $this->cache->get('datagrid_show_field'.$user_id.'_'.$list_type);
//		}
		return $fields;
	}

	/**
	 * 得到当前坐席指定类型列表的检索字段 包括id字段
	 *
	 * @param  int $list_type  列表类型
	 * <ul>
	 * <li>LIST_CONFIRM_CLIENT 客户管理</li>
	 * <li>LIST_CONFIRM_CONTACT 联系人</li>
	 * <li>LIST_CONFIRM_CLIENT_RESOURCE 资源调配</li>
	 * <li>LIST_CONFIRM_CLIENT_DEAL 数据处理</li>
	 * <li>LIST_COMFIRM_PRODUCT 产品</li>
	 * <li>LIST_COMFIRM_ORDER 订单</li>
	 * <li>LIST_COMFIRM_SERVICE 客服服务</li>
	 * </ul>    
	 * @return array 字段的数组
	 */
	public function get_datagrid_select_fields($list_type)
	{
		$user_id = $this->session->userdata("user_id");
		//if(!$this->cache->get('datagrid_select_field'.$user_id.'_'.$list_type))
		//{
			$idfields = $this->_get_id_fields($list_type);
			$this->load->model('user_model');
			$fields_display = $this->user_model->get_list_display_id($user_id,$list_type);
			$fields_display_array = explode(',',$fields_display);
			$fields = $this->_id_to_fields($fields_display_array,$list_type);
			$fields = array_merge($fields,$idfields);
			//$this->cache->save('datagrid_select_field'.$user_id.'_'.$list_type,$fields,600);
		//}
		//		else
		//		{
		//			$fields = $this->cache->get('datagrid_select_field'.$user_id.'_'.$list_type);
		//		}
		return $fields;
	}

	/**
	 * 指定类型列表的id字段
	 *
	 * @param string $list_type
	 * @return array
	 */
	private function _get_id_fields($list_type='')
	{
		$idfields = array();
		switch ($list_type)
		{
			case LIST_CONFIRM_CLIENT :
			case LIST_CONFIRM_CLIENT_RESOURCE  :
				$idfields[] = 'est_client.cle_id';
				break;
			case LIST_CONFIRM_CLIENT_DEAL  :
				$idfields[] = 'est_client.cle_id';
				break;
			case LIST_CONFIRM_CONTACT :
				$idfields[] = 'con_id';
				break;
			case LIST_COMFIRM_PRODUCT :
				$idfields[] = 'product_id';
				$idfields[] = 'product_pic';
				break;
			case LIST_COMFIRM_ORDER :
				$idfields[] = 'est_order.order_id';
				$idfields[] = 'cle_id';
				break;
			case LIST_COMFIRM_SERVICE :
				$idfields[] = 'serv_id';
				$idfields[] = 'cle_id';
				break;
			default:return false;
		}
		return $idfields;
	}
	/**
	 * 得到当前坐席指定类型列表所有可检索字段 包括id字段
	 *
	 * @param  int $list_type  列表类型
	 * <ul>
	 * <li>LIST_CONFIRM_CLIENT 客户管理</li>
	 * <li>LIST_CONFIRM_CONTACT 联系人</li>
	 * <li>LIST_CONFIRM_CLIENT_RESOURCE 资源调配</li>
	 * <li>LIST_CONFIRM_CLIENT_DEAL 数据处理</li>
	 * <li>LIST_COMFIRM_PRODUCT 产品</li>
	 * <li>LIST_COMFIRM_ORDER 订单</li>
	 * <li>LIST_COMFIRM_SERVICE 客服服务</li>
	 * </ul>
     * @return array
	 * @author zt
	 */
	public function get_available_select_fields($list_type)
	{
		$field_type = $this->_list_type_to_field_type($list_type);
		$this->load->model('field_confirm_model');
		$available_fields = $this->field_confirm_model->get_available_fields($field_type);
		$fields = array();
		foreach ($available_fields as $field)
		{
			$fields[] = $field['fields'];
		}
		$idfields = $this->_get_id_fields($list_type);
		$fields = array_merge($fields,$idfields);
		return $fields;
	}
}