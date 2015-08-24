<?php
class System_config_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除缓存
	 *
	 * @return bool
	 */
	private function _clear_config_cache()
	{
		$this->cache->delete('system_config');
		return true;
	}

	/**
	 * 获取系统的配置参数
	 *
	 * @return array(
	 * 					[sms_signature] => 短信后缀
	 * 					[phone_ifrepeat] => 电话号码是否允许重复：0允许重复， 1不允许重复
	 * 					[login_wizard] => 首次登陆弹出向导：0未弹出，1已弹出
	 * 					[order_product_amount] => 下订单时产品数量 1一个 2多个
	 * 					[deal_other_client] => 处理非本人数据的来电  1是  2否
	 * 					[use_custom_menu] => 启用自定义菜单  1是  0,2否
	 * )
	 * @author zgx
	 */
	public function get_system_config()
	{
		$result = array();
		if ( !$this->cache->get('system_config') )
		{
			$this->db_read->limit(1);
			$query    = $this->db_read->get("est_config");
			$result   = $query->row_array();

			$this->cache->save('system_config', $result, 600);
		}
		else
		{
			$result = $this->cache->get('system_config');
		}

		return $result;
	}

	/**
	 * 更新系统设置
	 *
	 * @param array $cfg_info = array(
	 *            sms_signature  =>  短信后缀
	 *            phone_ifrepeat =>  是否过滤重复号码：0否，1过滤重复号码
	 * )
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function update_system_config($cfg_info = NULL)
	{
		if (empty($cfg_info))
		{
			return TRUE;
		}

		/*可以更新的字段*/
		$config_field = array("sms_signature","client_amount","login_wizard","phone_ifrepeat","order_product_amount","deal_other_client","change_dept_dealData","connect_type","use_contact","delete_client_relative","call_type","auto_back_client","client_has_create_day","no_contact_day1","no_contact_day2","use_history","created_day","auto_back_place","use_transque");

		$data = array();
		foreach ($config_field AS $field)
		{
			if (isset($cfg_info[$field]))
			{
				$data[$field] = empty($cfg_info[$field]) ? "" : $cfg_info[$field];
			}
		}

		$this->db_write->from("est_config");
		if ( $this->db_write->count_all_results())
		{
			$result = $this->db_write->update("est_config",$data);
		}
		else
		{
			$result = $this->db_write->insert("est_config",$data);
		}

		if ($result)
		{
			//清空缓存
			$this->_clear_config_cache();
		}

		return $result;
	}
}