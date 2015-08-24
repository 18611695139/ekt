<?php
class Role_model extends CI_Model
{
	/**
	 * 角色类型
	 *
	 * @var array
	 */
	private $_role_type = array(1 => "部门",	2 => "个人");


	function __construct()
	{
		parent::__construct();

	}

	/**
	 * 清楚缓存
	 *
	 * @param int $role_id
	 * @return boolean
	 */
	private function _clear_role_cache($role_id=0)
	{
		$this->cache->delete('role_info'.$role_id);
		return true;
	}
	/**
	 * 返回所有角色
	 *
	 * @param string $where 检索条件
	 * @return array
	 */
	public function get_role_list($where = NULL)
	{
		if( ! empty($where))
		{
			$this ->db_read->where($where);
		}
		$this ->db_read->select('role_id,role_name,role_type');
		$role_query = $this->db_read->get('est_role');
		$roles	= $role_query->result_array();
		$this->load->model('role_model');
		$role_types	= $this->role_model->get_role_type_list();
		foreach ($roles as $key => $role)
		{
			$roles[$key]['role_type_name']	= empty($role_types[$role['role_type']]) ? "" : $role_types[$role['role_type']];
		}

		return $roles;
	}

	/**
	 * 返回所有角色类型
	 * 
	 * @return array
	 */
	public function get_role_type_list()
	{
		return $this->_role_type;
	}

	/**
	 * 返回角色详细信息
	 *
	 * @param int $role_id
	 * @return array
	 */
	public function get_role_info($role_id=0)
	{
		if( ! $this->cache->get('role_info'.$role_id))
		{
			$role_query	= $this->db_read->get_where('est_role',array('role_id'=>$role_id));
			$role_info	= $role_query->row_array();
			if(! empty($role_info))
			{
				$role_types	= $this->get_role_type_list();
				$role_info['role_type_name']	= $role_types[$role_info['role_type']];
			}
			$this->cache->save('role_info'.$role_id,$role_info,600);
		}
		else
		{
			$role_info = $this->cache->get('role_info'.$role_id);
		}
		return $role_info;
	}

    /**
     * 获取所有角色
     *
     * @return array
     */
    function get_all_roles()
    {
        $this->db->select('role_id,role_name,role_action_list,role_type,role_grade');
        $query = $this->db_read->get('est_role');
        if ($query->num_rows())
        {
            return $query->result_array();
        }
        else
        {
            return array();
        }
    }

	/**
	 * 通过后台设置的大权限过来过滤功能权限
	 *
	 * @param string $role_action_list
	 * @param string $role_action 大权限类型控制
	 * @return string
	 */
	public function filter_role_action_list($role_action_list='',$role_action='')
	{
		require_once(FCPATH.'includes/authority.php');
		$role_action_arr = explode(',',$role_action);
		$role_short = array_diff(array_keys($menu),$role_action_arr);
		if(empty($role_short))
		{
			return $role_action_list;
		}
		else
		{
			foreach ($role_short as $action)
			{
				foreach ($menu[$action]['children'] as $role_name =>$role)
				{
					$role_action_list = str_replace($role_name.',','',$role_action_list);
				}
			}
			return $role_action_list;
		}
	}

	/**
	 * 插入角色
	 *
	 * @param string $role_name
	 * @param int $role_type
	 * @return bool
	 */
	public function insert_role($role_name='',$role_type=2)
	{
		$data = array(
		'role_name'	=> $role_name,
		'role_type'	=> $role_type
		);

		$result = $this->db_write->insert('est_role',$data);
		if($result)
		{
			return $this->db_write->insert_id();
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新角色信息
	 *
	 * @param int $role_id
	 * @param string $role_name
	 * @param int $role_type
	 * @return bool
	 */
	public function update_role($role_id=0,$role_name='',$role_type=2)
	{
		$data = array(
		'role_name'	=> $role_name,
		'role_type'	=> $role_type
		);
		$where = array('role_id'=>$role_id);
		$this->load->model('user_model');
		$this->user_model->update_user($data,$where);
		$this->db_write->update('est_role',$data,$where);
		return $this->_clear_role_cache($role_id);
	}

	public function set_role_action_list($role_id=0,$role_action_list='')
	{
		$data = array('role_action_list'	=> $role_action_list);
		$where = array('role_id'=>$role_id);
		$this->db_write->update('est_role',$data,$where);
		return $this->_clear_role_cache($role_id);
	}

	/**
	 * 删除角色
	 *
	 * @param int $role_id
	 * @return bool
	 */
	public function delete_role($role_id)
	{
		$where = array('role_id'=>$role_id);
		$this->load->model('user_model');
		$this->user_model->update_user(array('role_id'=>0,'role_name'=>''),$where);
		$this->db_write->delete('est_role',$where);
		return $this->_clear_role_cache($role_id);
	}

	/**
	 * 返回所有权限
	 *
	 * @return array
	 */
	public function get_authority_list()
	{
		require_once(FCPATH.'includes/authority.php');
		$role_action = $this->session->userdata('role_action');
		$role_action_arr = explode(',',$role_action);
		$role_short = array_diff(array_keys($menu),$role_action_arr);
		if(!empty($role_short))
		{
			foreach ($role_short as $action)
			{
				unset($menu[$action]);
			}
		}
		
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		if(isset($config_info['use_history'])&&($config_info['use_history']!=1))
		{
			unset($menu['khgl']['children']['khglhistory']);
		}
		
		return $menu;
	}

	/**
	 * 得到自定义菜单
	 *
	 */
	private function _get_custom_menu()
	{
		$custom_menu = array();
		$custom_menu['custom_menu']['name']='我的菜单';
		$custom_menu['custom_menu']['children']=array();
		$user_num = $this->session->userdata('user_num');

		$menu_query = $this->db_read->get('est_custom_menu');
		$menu = $menu_query->result_array();
		if(!empty($menu))
		{
			foreach ($menu as $menu_item)
			{
				//特殊的name
				switch($menu_item['name'])
				{
					case 'menu_name'://菜单名称 默认为自定义菜单
					if(!empty($menu_item['label']))
					{
						$custom_menu['custom_menu']['name']=$menu_item['label'];
					}
					break;
					case 'pop_address'://弹屏地址
					break;
					default:
						if(!empty($menu_item['label']) && !empty($menu_item['name']) && !empty($menu_item['url']))
						{
							$menu_item['url'] = str_replace('[工号]',$user_num,$menu_item['url']);
							$custom_menu['custom_menu']['children'][$menu_item['name']] = array($menu_item['label'],$menu_item['url'],'menu_icon');
						}
				}
			}
		}
		return $custom_menu;
	}

	/**
	 * 取得弹屏地址
	 * 弹屏地址中的参数 可以是 坐席工号、姓名、主叫号码、被叫号码、按键号码、
	 * http://127.0.0.1/test.php?aa=[工号]&bb=[姓名]&cc=[主叫]&dd=[被叫]&ee=[按键]
	 * @return string 处理后的弹屏地址
	 */
	public function get_pop_address()
	{
		$pop_address_query = $this->db_read->get_where('est_custom_menu',array('name'=>'pop_address'));
		if($pop_address_query->num_rows() > 0)
		{
			$pop_address = $pop_address_query->row()->url;
			$user_num = $this->session->userdata('user_num');
			$user_name = $this->session->userdata('user_name');
			$pop_address = str_replace('[工号]',$user_num,$pop_address);
			$pop_address = str_replace('[姓名]',urlencode($user_name),$pop_address);
//			$pop_address = str_replace('[主叫]','"+_caller+"',$pop_address);
//			$pop_address = str_replace('[被叫]','"+_called+"',$pop_address);
//			$pop_address = str_replace('[按键]','"+_custom+"',$pop_address);
//			$pop_address = str_replace('[服务号码]','"+_servnum+"',$pop_address);
			return $pop_address;
		}
		else 
		{
			return '';
		}
	}



	/**
	 * 返回菜单列表
	 *
	 * @return string
	 */
	public function get_menu_list()
	{
		$role = $this->session->userdata('role_action_list');
		$role_action = $this->session->userdata('role_action');
		if($role != 'all')
		$role = explode(',',$role);
		require_once(FCPATH.'includes/authority.php');

		$role_action_arr = explode(',',$role_action);
		$role_short = array_diff(array_keys($menu),$role_action_arr);
		if(!empty($role_short))
		{
			foreach ($role_short as $action)
			{
				unset($menu[$action]);
			}
		}
		
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		if(isset($config_info['use_history'])&&($config_info['use_history']!=1))
		{
			unset($menu['khgl']['children']['khglhistory']);
		}

		foreach ($menu as $key => $val)
		{
			$is_top = 0;
			foreach ($val['children'] as $k=>$v)
			{
				if(!isset($v[1]))
				{
					unset($menu[$key]['children'][$k]);
				}
				else
				{
					if($role == 'all')
					{
						$is_top = 1;
					}
					else if(in_array($k,$role))
					{
						$is_top = 1;
					}
					else
					{
						unset($menu[$key]['children'][$k]);
					}
				}
			}
			if(!$is_top)
			{
				unset($menu[$key]);
			}
		}
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		if($config_info['use_custom_menu'])
		{
			$custom_menu = $this->_get_custom_menu();
			$menu = array_merge($menu,$custom_menu);
		}
		return $menu;
	}
}
