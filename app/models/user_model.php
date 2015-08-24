<?php

use Guzzle\Http\Client;

class User_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除缓存
	 *
	 * @param array $where
	 * @param int $user_id 员工id
	 * @return bool
	 */
	private function _clear_user_cache($where=null,$user_id=0)
	{
		if ($where != null)
		{
			$this->db_read->where($where);
			$this->db_read->select('user_id');
			$user_query = $this->db_read->get('est_users');
			$user_ids = $user_query->result_array();
			foreach ($user_ids as $tmp_user)
			{
				$tmp_user_id = $tmp_user['user_id'];
				$this->cache->delete('user_info'.$tmp_user_id);
			}
		}

		if($user_id != 0)
		{
			$this->cache->delete('user_info'.$user_id);
		}
		$this->cache->delete('all_users'.$user_id);
		$this->cache->delete('all_users_without_dept');
		return true;
	}

	/**
	 * 返回还未使用过的分机号， 在绑定分机号的时候使用
	 * 
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=> 分机号
	 *  ...
	 * )
	 * </code>
	 */
	public function get_unused_phone()
	{
		$this->config->load('myconfig');
		$max_phone_num = (int)$this->config->item('max_phone_num');
		$min_phone_num = (int)$this->config->item('min_phone_num');

		$all_phone = array();
		for ($i = $min_phone_num;$i<= $max_phone_num;$i++)
		{
			$all_phone[$i] = 1;
		}
		$this->db_read->select('user_phone');
		$phone_query = $this->db_read->get('est_users');

		foreach ($phone_query->result_array() as $i=>$tmp_row)
		{
			$tmp_phone = $tmp_row['user_phone'];
			if( ! empty($tmp_phone))
			{
				unset($all_phone[$tmp_phone]);
			}
		}
		$left_phone = array_keys($all_phone);
		sort($left_phone);
		return $left_phone;
	}

	/**
	 * 通过工号取得user_id
	 *
	 * @param string $user_num 员工工号
	 * @return int user_id
	 * @author zgx
	 */
	public function get_user_id_by_num($user_num='')
	{
		$where = array('user_num'=>$user_num);
		$user_query = $this->db_read->get_where('est_users',$where,1);
		if($user_query->num_rows()>0)
		{
			return $user_query->row()->user_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取员工检索条件
	 * 
	 * @param array $condition 检索内容   
	 * <pre>
	 * data_permission TRUE 添加上数据权限
	 * key_word 关键字 检索姓名或工号匹配的
	 * dept_id 部门(包括子部门)
	 * dept_id_only 不包括子部门
	 * more_user string 检索多个员工 如：1,2,3,4
	 * </pre>
	 * @return array $wheres
	 * @author zgx
	 */
	private function get_user_codition($condition = array())
	{
		$wheres = array();
		//列表关键字
		if(!empty($condition['key_word']))
		{
			$wheres[] = "(user_name like '%".$condition['key_word']."%' OR user_num  like '%".$condition['key_word']."%')";
		}
		//部门（包括子部门）
		if(!empty($condition['dept_id']))
		{
			$this->load->model('department_model');
			$dept_children = $this->department_model->get_department_children_ids($condition['dept_id']);

			$wheres[] = 'dept_id IN ('.implode(',',$dept_children).')';
		}
		//部门（不包括子部门）
		if(!empty($condition['dept_id_only']))
		{
			$wheres[] = "dept_id = ".$condition['dept_id_only'];
		}
		//员工工号
		if(!empty($condition['user_num']))
		{
			$wheres[] = "user_num = ".$condition['user_num'];
		}
		//员工电话
		if(!empty($condition['user_phone']))
		{
			$wheres[] = "user_phone = ".$condition['user_phone'];
		}
		//指定员工
		if(!empty($condition['more_user']))
		{
			$wheres[] = "user_id IN(".$condition['more_user'].")";
		}
		/*全局查询，不设置数据权限*/
		if(empty($condition['gl_all_data']) || $condition['gl_all_data'] !== true)
		{
			if($this->session->userdata('dept_id')!=1)
			{
				$wheres[] = data_permission();//数据权限
			}
		}
		$wheres[] = "user_id > 0";

		return $wheres;
	}

	/**
	 * 员工列表信息
	 *
	 * @param array $condition 传递搜索条件的数组
	 * @param array $select   检索字段，空则检索所有字段
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
	 *        [user_id]=>10
 	 *        [user_name] => 员工名称
	 *        [user_num] =>  员工编号
	 *        … 
	 *     )
	 *  )
	 * </code>
	 * @author zgx
	 */
	public function get_user_list($condition=array(),$select=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_user_codition($condition);
		$where = implode(' AND ',$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_users');
		$total = $total_query->row()->total;
		$responce = new stdClass();
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		if ( !empty($select))
		{
			$this->db_read->select($select);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$user_rows = $this->db_read->get('est_users');
		$responce -> rows = array();
		foreach($user_rows->result_array() AS $i=>$user)
		{
			$responce -> rows[$i] = $user;
		}
		$this->db_read->flush_cache();
		return $responce;
	}

	/**
	 * 返回一个员工信息
	 *
	 * @param int $user_id 员工id
	 * @return array
	 */
	public function get_user_info($user_id=0)
	{
		if(empty($user_id))
		{
			return FALSE;
		}
		if(!$this->cache->get('user_info'.$user_id))
		{
			$this->db_read->select('user_id,user_num,user_password,user_name,user_phone,user_ol_model,role_id,role_name,role_type,dept_id,dept_name,user_tel_server,user_cti_server,user_channel,user_sms_phone,user_if_tip,user_remark,user_last_login,user_phone_type,user_last_ip,user_outcaller_type,user_outcaller_num,user_notebook,user_login_state,user_workbench_layout,user_display_dialpanel,user_outcall_popup,user_to_selfphone');
			$user_query = $this->db_read->get_where('est_users',array('user_id'=>$user_id));
			$user_info = $user_query->row_array();
			$this->config->load('myconfig');
			if(empty($user_info['user_ol_model']))
			{
				$user_info['user_ol_model'] = $this->config->item('default_ol_model');
			}
			//通讯服务器
			if(empty($user_info['user_tel_server']))
			{
				$user_info['user_tel_server'] = $this->config->item('default_tel_server');
			}
			//中间件服务器
			if (empty($user_info["user_cti_server"]))
			{
				$user_info["user_cti_server"] = $this->config->item("cti_server");
			}

			$this->cache->save('user_info'.$user_id, $user_info, 600);
		}
		else
		{
			$user_info = $this->cache->get('user_info'.$user_id);
		}
		return $user_info;
	}

	/**
	 * 得到指定条件的坐席信息
	 *
	 * @param array $condition  检索条件
	 * @param string $sort 排序
	 * <code>
	 * array(
	 * 	[0]=> array(
	 * 		[user_id]=> 员工id
	 * 		[user_name]=> 员工名称
	 * 		...
	 * 	)
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_users($condition = array(),$sort='')
	{
		//获取检索信息
		$condition['gl_all_data'] = true;
		$wheres = $this->user_model->get_user_codition($condition);
		$where = implode(' AND ',$wheres);

		if($where)
		{
			$this->db_read->where($where);
		}
		$this->db_read->select('user_id,user_num,user_name,user_phone,user_ol_model,role_id,role_name,role_type,dept_id,dept_name,user_tel_server,user_cti_server,user_sms_phone,user_if_tip,user_remark,user_last_login,user_phone_type,user_last_ip,user_outcaller_type,user_outcaller_num,user_notebook,user_login_state,user_workbench_layout,user_display_dialpanel,user_outcall_popup,user_to_selfphone');
		if($sort)
		{
			$this->db_read->order_by($sort,"asc");
		}
		$query = $this->db_read->get("est_users");
		return $query->result_array();
	}

	/**
	 * 根据权限返回所有所能看到的坐席
	 *
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=> array(
	 * 		[user_id]=> 员工id
	 * 		[user_name]=> 员工名称
	 * 		...
	 * 	)
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_all_users()
	{
		$user_id = $this->session->userdata("user_id");
		if( ! $this->cache->get('all_users'.$user_id))
		{
			//数据权限
			$role_condition = data_permission();
			if ($role_condition)
			{
				$this->db_read->where($role_condition);
			}
			$this->db_read->select('user_id,user_name,user_num,dept_id,dept_name');
			$result = $this->db_read->get("est_users");
			$all_users = $result->result_array();
			$this->cache->save('all_users'.$user_id,$all_users,600);
		}
		else
		{
			$all_users = $this->cache->get('all_users'.$user_id);
		}
		return $all_users;
	}

	/**
	 * 获取全部员工信息（没部门限制）
	 *
	 * @return array
	 */
	public function get_all_users_without_dept()
	{

		if( ! $this->cache->get('all_users_without_dept'))
		{
			$this->db_read->select('user_id,user_name,user_num,dept_id,dept_name');
			$result = $this->db_read->get("est_users");
			$all_users = $result->result_array();
			$this->cache->save('all_users_without_dept',$all_users,600);
		}
		else
		{
			$all_users = $this->cache->get('all_users_without_dept');
		}
		return $all_users;
	}

	/**
	 * 得到指定部门及其下级部门的所有坐席信息
	 * 
	 * @param int    $dept_id   部门ID
	 * @param bool $return_ids  true:返回员工ID的一维数组；  false:返回员工全部信息
	 * @return array
	 * <code>
	 * 情况一:员工ID的一维数组
	 * 情况二：员工全部信息
	 * array(
	 * 	[0]=> array(
	 * 		[user_id]=> 员工id
	 * 		[user_name]=> 员工名称
	 * 		...
	 *  )
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_dept_children_users($dept_id = 0,$return_ids = true)
	{
		//符合条件的员工信息
		$user_info = array();
		if ($dept_id == 0 )
		{
			return $user_info;
		}
		$condition['dept_id'] = $dept_id;
		//符合条件的员工信息
		$this->load->model("user_model");
		$user_result  = $this->user_model->get_users($condition);
		if ($user_result)
		{
			//返回员工ID一维数组
			if ($return_ids === true)
			{
				foreach ($user_result AS $value)
				{
					if ($value["user_id"])
					{
						$user_info[] = $value["user_id"];
					}
				}
			}
			else
			{
				$user_info = $user_result;
			}
		}
		return $user_info;
	}

	/**
	 * 获取空闲的坐席信息
	 * 
	 * @return object
	 * <code>
	 * $responce->total	总数
	 * $responce->rows[] = array(
	 * 		[user_id] = array(
	 * 			[user_name]=> 员工名称
	 * 			[user_num]=> 员工工号
	 * 			[dept_name]=> 部门名称
	 * 		)
	 * 		...
	 * )
	 * ...
	 * </code>
	 * @author zgx
	 */
	public function get_free_users()
	{
		$vcc_id = $this->session->userdata('vcc_id');
		$user_session_id = $this->session->userdata('user_id');

        $this->config->load('myconfig');
        $api    = $this->config->item('api_wintelapi');
        $client = new Client();

        $request = $client->get($api.'/api/agent/free/'.$vcc_id);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }

        $code = isset($response['code']) ? $response['code'] : 0;
        $data = isset($response['data']) ? $response['data'] : array();

        $responce = new stdClass();

        if ($code != 200 || empty($data)) {
            $responce->total = 0;
            $responce->rows = array();
            return $responce;
        } else if (count($data) == 1 && $data[0]['ag_id'] == $user_session_id) { //判断是否只有当前自己一个坐席
            $responce->total = 0;
            $responce->rows = array();
            return $responce;
        }

        $free_users_id   = array();
        $free_users_info = array();
        foreach ($data as $user)
        {
            $user_id = $user['ag_id'];
            $free_users_id[] = $user_id;
            $free_users_info[$user_id] = array(
                'user_id'=>$user_id,
                'pho_num'=>$user['pho_num']
            );
        }

		$this->db_read->select('user_id,user_name,user_num,dept_name');
		$this->db_read->where_in('user_id',$free_users_id);
		$user_query = $this->db_read->get('est_users');
		$user_info = $user_query->result_array();

		$i =0;
		foreach ($user_info as $user)
		{
			$user_id = $user['user_id'];
			if($user_id != $user_session_id)
			{
				$free_users_info[$user_id]['user_name'] = $user['user_name'];
				$free_users_info[$user_id]['user_num'] = $user['user_num'];
				$free_users_info[$user_id]['dept_name'] = $user['dept_name'];
				$responce->rows[$i] = $free_users_info[$user_id];
				$i++;
			}
		}
		$responce->total = $i;
		return $responce;
	}

	/**
	 * 统计已分配角色的坐席数量
	 *
	 * @return int
	 * @author zgx
	 */
	public function get_role_users_count()
	{
		$this->db_read->select('count(*) as total',false);
		$this->db_read->where("role_id >","0");
		$this->db_read->from("est_users");
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		return $total;
	}

	/**
	 * 得到坐席数
	 *
	 * @param int $dept_id 部门id
	 * @return int
	 * @author zgx
	 */
	public function get_user_totalnum($dept_id=0)
	{
		$total = 0;
		if(!empty($dept_id))
		{
			$this->db_read->where(array('dept_id'=>$dept_id));
		}
		$this->db_read->select('count(*) as total',false);
		$this->db_read->from("est_users");
		$total_query = $this->db_read->get();
		$total = $total_query->row()->total;
		return $total;
	}

	/**
	 * 根据条件获取坐席信息（最多20条信息）
	 *
	 * @param array $condition  检索条件
	 * @param $if_have_no_user 1 返回搜到数据+'无所属人'  2 只返回搜到数据
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[user_id]=> 员工id
	 * 		[user_name_num]=> 员工姓名[员工工号]
	 * 		...
	 * 	)
	 *  ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_user_box($condition = array(),$if_have_no_user = 0)
	{
		$wheres = $this->get_user_codition($condition); //整理检索条件
		$where = implode(" AND ",$wheres);
		if (!empty($where))
		{
			$this->db_read->where($where);
		}
		$this->db_read->limit(20);
		$this->db_read->select('user_id,user_name,user_num');
		$query = $this->db_read->get("est_users");
		$result = $query->result_array();

		$user_box = array();
		if($if_have_no_user == 1)
		{
			$user_box[] = array('user_id'=>'0','user_name_num'=>'无所属人');
		}
		if(!empty($result))
		{
			foreach ($result AS $value)
			{
				$user_box[] = array('user_id'=>$value["user_id"],'user_name_num'=>$value["user_name"]."[".$value["user_num"]."]");
			}
		}
		return $user_box;
	}

	/**
	 * 修改密码
	 *
	 * @param int $user_id 员工id
	 * @param string $old_password	原密码
	 * @param string $password	 新密码
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function reset_password($user_id=0,$old_password='',$password='')
	{
		if(empty($user_id))
		{
			return false;
		}

		$vcc_id = $this->session->userdata("vcc_id");

        $this->config->load('myconfig');
        $api    = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'ag_id' => $user_id,
            'old_password' => $old_password,
            'new_password' => $password,
        );
        $request = $client->post($api.'/api/password/update', array(), $params);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';

		if($code == 200)
		{
			$this->db_write->query("UPDATE est_users SET user_password='".$password."' WHERE user_id=".$user_id);
			$this->_clear_user_cache(null,$user_id);
			return 1;
		}
		else
		{
			return $message;
		}
	}

	/**
	 * 设置坐席最近一次登陆时间
	 *
	 */
	public function set_agent_login()
	{
		$user_real_ip = real_ip();
		$login_data = date('Y-m-d H:i:s');
		$data = array('user_last_login'=>$login_data,'user_last_ip'=>$user_real_ip);
		$user_id = $this->session->userdata('user_id');
		$this->db_write->where('user_id', $user_id);
		$this->db_write->update('est_users',$data);
	}

	/**
	 * 更新 根据user_id更新员工信息
	 *
	 * @param array $data
	 * @param  $where
	 * @return bool
	 */
	public function update_user($data,$where)
	{
		//角色 找出相应信息
		if (! empty($data['role_id']))
		{
			$this->load->model('role_model');
			$role_info = $this->role_model->get_role_info($data['role_id']);
			$data['role_name'] = $role_info['role_name'];
			$data['role_type'] = $role_info['role_type'];
		}

		//判断是否可编辑的字段
		$update_array = array();
		$user_field = array('user_name','dept_id','dept_name','user_remark','user_tel_server','user_cti_server','user_channel','user_sms_phone','user_if_tip','user_ol_model','user_outcaller_type',
		'user_outcaller_num','user_login_state','user_phone_type','role_id','role_name','role_type','user_display_dialpanel','user_outcall_popup','user_to_selfphone');
		foreach ($user_field as $key)
		{
			if(isset($data[$key]))
			{
				$update_array[$key] = $data[$key];
			}
		}

		if(empty($update_array))
		{
			return true;
		}

		//部门改变
		if(isset($update_array["dept_id"]) && !empty($update_array["dept_id"]) && isset($where["user_id"])&& !empty($where['user_id']))
		{
			//处理坐席拥有的数据
			$this->load->model("client_model");
			$total = $this->client_model->get_user_client_total_num($where['user_id']);
			if ( $total > 0 )
			{
				//获取系统配置参数 - 员工部门改变，处理所属数据 : 1、数据所属部门改变为新部门   2 收回所属数据
				$this->load->model("system_config_model");
				$config_info = $this->system_config_model->get_system_config();
				$dept_dealData_type = empty($config_info["change_dept_dealData"]) ? 1 : $config_info["change_dept_dealData"];

				$this->load->model("client_resource_model");
				if ( $dept_dealData_type == 1 )
				{
					//数据所属部门改变为新部门
					$owner_dept_id = $update_array["dept_id"];
					$owner_dept_name = empty($update_array["dept_name"]) ? "" : $update_array["dept_name"];
					$this->client_resource_model->user_dept_change_client($where['user_id'],$owner_dept_id,$owner_dept_name);
				}
				elseif ( $dept_dealData_type == 2 )
				{
					//2收回所属数据
					$this->client_resource_model->take_more_client_back($where);
				}
			}
			//坐席所拥有统计信息相应改变部门
			$this->load->model("statistics_model");
			$this->statistics_model->user_dept_change_statistics($where["user_id"],$update_array["dept_id"]);

			$role_action = $this->session->userdata('role_action');
			$action = explode(',',$role_action);
			if(in_array('ddgl',$action))
			{
				//坐席所订单部门
				$this->load->model("order_model");
				$this->order_model->change_order_dept_id($where["user_id"],$update_array["dept_id"]);
			}
			if(in_array('kffw',$action))
			{
				//改变客服服务信息->处理人位当前坐席的数据->处理部门对应改变
				$this->load->model("service_model");
				$this->service_model->update_service_dept_id($where["user_id"],$update_array["dept_id"]);
			}

			//写入消息
			$user_session_id = $this->session->userdata('user_id');
			if($where['user_id']!=$user_session_id)
			{
				$this->load->model('notice_model');
				$this->notice_model->write_notice('system',$where['user_id'],'',"您的所属部门于".date('Y-m-d H:i:s')."被管理员修改了，当前若登着系统请退出重新登录再操作系统，以免操作数据出错",0,$this->session->userdata('user_id'));
			}
		}
		/****修改相应后台信息  ***/
		$vcc_id = $this->session->userdata("vcc_id");

        $this->config->load('myconfig');
        $api    = $this->config->item('api_wintelapi');
        $client = new Client();

		//1、名称修改
		if(isset($update_array['user_name']) && $where['user_id'])
		{
            $params = array(
                'vcc_id' => $vcc_id,
                'ag_id' => $where['user_id'],
                'ag_name' => $update_array['user_name']
            );
            $request  = $client->post($api.'/api/agent/edit', array(), $params);
            $response = $request->send()->json();
            if (json_last_error() !== JSON_ERROR_NONE) {
                return '解析结果出错，错误为【'.json_last_error().'】';
            }

            $code    = isset($response['code']) ? $response['code'] : 0;
            $message = isset($response['message']) ? $response['message'] : '';

            if( $code!=200 ) return $message;
		}

		//2、后台设置转手机功能
		if(isset($update_array['user_to_selfphone']) && $where['user_id'] )
		{
			if($update_array['user_to_selfphone']==1) {
                $state = 0;
                if (empty($update_array['user_sms_phone'])) {
                    $update_array['user_sms_phone'] = '13800000000';
                }
            } else {
                $state = 1;
            }

            $params = array(
                'vcc_id' => $vcc_id,
                'ag_id' => $where['user_id'],
                'phone' => $update_array['user_sms_phone'],
                'state' => $state
            );
            $request  = $client->post($api.'/api/agent/agextphone', array(), $params);
            $response = $request->send()->json();
            if (json_last_error() !== JSON_ERROR_NONE) {
                return '解析结果出错，错误为【'.json_last_error().'】';
            }

            $code    = isset($response['code']) ? $response['code'] : 0;
            $message = isset($response['message']) ? $response['message'] : '';

            /*410为未更新任何内容*/
            if( $code!=200 && $code!=410) return $message;
		}

        $this->db_write->where($where);
        $this->db_write->update('est_users',$update_array);
        $this->_clear_user_cache($where);

		return true;
	}

	/**
	 * 改变所有坐席的常接通模式
	 *
	 * @param string $ol_model 常接通模式：0不用常接通、1常接通、2常接通并自动接听
	 * @return bool
	 * @author zgx
	 */
	public function change_all_ol_model($ol_model = '2')
	{
		$data = array(
		"user_ol_model"=>$ol_model
		);
		$this->db_write->update("est_users",$data);
		return true;
	}

	/**
	 * 添加员工
	 * 
	 * @param array $insert
	 * @return bool
	 * @author zgx
	 */
	public function insert_user($insert=array())
	{
		if(empty($insert)||!isset($insert['user_que'])||empty($insert['user_que']))
		{
			return false;
		}
		$user_que = $insert['user_que'];
		$must_fields = array('user_name','user_num','role_id','dept_id','dept_name','user_password');//必须字段
		$user_fields = array('user_name','user_num','role_id','dept_id','dept_name','user_remark','user_ol_model','user_outcaller_type','user_outcaller_num','user_password','user_login_state',
		'user_tel_server','user_phone_type','user_cti_server');//添加可能传过来的字段
		$data = array();
		foreach($user_fields AS $field)
		{
			//必须字段判断
			if(in_array($field,$must_fields))
			{
				if(!isset($insert[$field])||empty($insert[$field]))
				{
					return false;
				}
				else
				{
					if($field=='user_password')
					{
						$data[$field] = md5($insert[$field]);
					}
					else
					{
						$data[$field] = $insert[$field];
					}
				}
			}
			else//其他字段
			{
				if(isset($insert[$field]))
				{
					$data[$field] = $insert[$field];
				}
			}
		}

		$vcc_id = $this->session->userdata("vcc_id");
		//后台添加用户，返回user_id

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'que_id' => $user_que,
            'ag_num' => $data['user_num'],
            'ag_name' => $data['user_name'],
            'ag_password' => $data['user_password']
        );
        $request = $client->post($api.'/api/agent/add', array(), $params);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '解析结果出错，错误为【'.json_last_error().'】';
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';

        if ($code == 200) {
            $data['user_id'] = isset($response['lastId']) ? $response['lastId'] : '';
            if ( ! empty($data['role_id']))
            {
                $this->load->model('role_model');
                $role_info = $this->role_model->get_role_info($data['role_id']);
                $data['role_name'] = $role_info['role_name'];
                $data['role_type'] = $role_info['role_type'];
            }
            $result = $this->db_write->insert('est_users',$data);
            if($result)
            {
                $this->_clear_user_cache(null,$this->session->userdata('user_id'));
                return true;
            }
            else
            {
                return false;
            }
        } else {
            return $message;
        }
	}

	/**
	 * 删除员工
	 * @param int $user_id 员工id
	 * @return bool
	 * @author zgx
	 */
	public function delete_user($user_id=0)
	{
		if (empty($user_id))
		{
			return FALSE;
		}
		$vcc_id = $this->session->userdata("vcc_id");

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'ag_id' => $user_id
        );
        $request = $client->post($api.'/api/agent/delete', array(), $params);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '解析结果出错，错误为【'.json_last_error().'】';
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data    = isset($response['data']) ? $response['data'] : array();

        if ($code == 500 && $data[0]['code'] == 200) {
            //删除员工
            $result = $this->db_write->query("DELETE FROM est_users WHERE user_id = $user_id");
            if ($result)
            {
                //释放坐席所属数据
                $this->load->model("client_model");
                $this->client_model->release_client("",$user_id,'删除员工，自动放弃该员工所属数据');

                //删除统计相关数据
                $this->load->model("statistics_model");
                $this->statistics_model->delete_statistics_by_delete_user($user_id);
            }

            return $this->_clear_user_cache(null,$user_id);
        } else {
            return $message;
        }
	}


	/**
	 * 导出
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[user_name]=> 员工姓名
	 * 		[user_num]=> 员工工号
	 * 		[user_phone]=> 坐席电话
	 * 		[user_ol_model]=> 常接通模式
	 * 		[role_name]=> 角色
	 * 		[dept_name]=> 部门
	 * 		[user_tel_server]=> 通讯服务器(SIP网关)
	 * 		[user_sms_phone]=> 短信提醒号码
	 * 		[user_if_tip]=> 来电/客户挂机，右下角弹出提示窗口
	 * 		[user_outcaller_type]=> 外呼主叫号码类型
	 * 		[user_outcaller_num]=> 指定的外呼主叫
	 * 		[user_display_dialpanel]=> 是否显示外呼窗口
	 * 		[user_outcall_popup]=> 外呼弹屏
	 * 		[user_last_ip]=> 最后登录ip
	 * 		[user_last_login]=> 最后登录时间
	 * 	)
	 * ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function output_user()
	{
		$query = $this->db_read->query("SELECT user_name,user_num,user_phone,(CASE user_ol_model WHEN '0' THEN '无常接通' WHEN '1' THEN '常接通' ELSE '常接通并自动外呼' END),role_name,dept_name,user_tel_server,user_sms_phone,(CASE user_if_tip WHEN '1' THEN '是' WHEN '2' THEN '否' ELSE '' END),(CASE user_outcaller_type WHEN '0' THEN '全部' WHEN '1' THEN '指定' ELSE '' END),user_outcaller_num,(CASE user_display_dialpanel WHEN '1' THEN '是' WHEN '2' THEN '否' ELSE '' END),(CASE user_outcall_popup WHEN '1' THEN '是' WHEN '2' THEN '否' ELSE '' END),user_last_ip,user_last_login,user_remark FROM est_users");
		return $query->result_array();
	}

	/**
	 * 记录工作桌面DIV的布局
	 *
	 * @param int    $user_id     员工ID
	 * @param string $layout_div  DIV布局
	 * @return boolen
	 * @author zgx
	 */
	public function update_workbench_layout($user_id = 0,$layout_div = '')
	{
		if (!$user_id)
		{
			return FALSE;
		}

		$this->db_write->where("user_id",$user_id);
		$result = $this->db_write->update("est_users",array("user_workbench_layout"=>$layout_div));
		if($result)
		{
			return $this->_clear_user_cache(null,$user_id);
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户指定模块类型列表显示字段id
	 * 
	 * @param int $user_id
	 * @param int $display_type
	 * <pre>
	 * LIST_CONFIRM_CLIENT 客户 
	 * LIST_CONFIRM_CONTACT 联系人 
	 * LIST_CONFIRM_CLIENT_RESOURCE 资源 
	 * LIST_CONFIRM_CLIENT_DEAL 数据处理  
	 * LIST_COMFIRM_PRODUCT 产品 
	 * LIST_COMFIRM_ORDER 订单  
	 * LIST_COMFIRM_SERVICE 客服服务
	 * </pre>
	 * @return string
	 * 
	 * @author zgx
	 */
	public function get_list_display_id($user_id=0,$display_type=0)
	{
		$display_field = array();
		if ($user_id)
		{
			switch ($display_type)
			{
				case LIST_CONFIRM_CLIENT :
					$this->db_read->select("client_list_display AS list_display");
					break;
				case LIST_CONFIRM_CONTACT :
					$this->db_read->select("contact_list_display AS list_display");
					break;
				case LIST_CONFIRM_CLIENT_RESOURCE :
					$this->db_read->select("resource_list_display AS list_display");
					break;
				case LIST_CONFIRM_CLIENT_DEAL :
					$this->db_read->select("datadeal_list_display AS list_display");
					break;
				case LIST_COMFIRM_PRODUCT :
					$this->db_read->select("product_list_display AS list_display");
					break;
				case LIST_COMFIRM_ORDER  :
					$this->db_read->select("order_list_display AS list_display");
					break;
				case LIST_COMFIRM_SERVICE  :
					$this->db_read->select("service_list_display AS list_display");
					break;
				default:return false;
			}

			$this->db_read->limit(1);
			$this->db_read->where('user_id',$user_id);
			$query = $this->db_read->get("est_users");
			$display_field = $query->row()->list_display;
		}

		return $display_field;
	}

	/**
	 * 更新用户某类型列表显示 
	 * 
	 * @param string $show_fields_ids 显示字段id
	 * @param int $user_id 员工id
	 * @param int $display_type 列表类型
	 * <pre>
	 * LIST_CONFIRM_CLIENT 客户 
	 * LIST_CONFIRM_CONTACT 联系人 
	 * LIST_CONFIRM_CLIENT_RESOURCE 资源 
	 * LIST_CONFIRM_CLIENT_DEAL 数据处理  
	 * LIST_COMFIRM_PRODUCT 产品 
	 * LIST_COMFIRM_ORDER 订单  
	 * LIST_COMFIRM_SERVICE 客服服务
	 * </pre>
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function update_list_from_user($show_fields_ids='',$user_id = 0,$display_type = 0)
	{
		switch ($display_type)
		{
			case LIST_CONFIRM_CLIENT :
				$data = array('client_list_display'=>$show_fields_ids);
				break;
			case LIST_CONFIRM_CONTACT :
				$data = array('contact_list_display'=>$show_fields_ids);
				break;
			case LIST_CONFIRM_CLIENT_RESOURCE :
				$data = array('resource_list_display'=>$show_fields_ids);
				break;
			case LIST_CONFIRM_CLIENT_DEAL :
				$data = array('datadeal_list_display'=>$show_fields_ids);
				break;
			case LIST_COMFIRM_PRODUCT :
				$data = array('product_list_display'=>$show_fields_ids);
				break;
			case LIST_COMFIRM_ORDER  :
				$data = array('order_list_display'=>$show_fields_ids);
				break;
			case LIST_COMFIRM_SERVICE  :
				$data = array('service_list_display'=>$show_fields_ids);
				break;
			default:return false;
		}
		$this->db_write->where("user_id",$user_id);
		$this->db_write->update("est_users",$data);

		$this->cache->delete('datagrid_show_field'.$user_id.'_'.$display_type);
		$this->cache->delete('datagrid_select_field'.$user_id.'_'.$display_type);
		$this->cache->delete('datagrid_confirm_field'.$user_id.'_'.$display_type);
		return true;
	}

	/**
	 * 部门及部门下人 树 (异步获取数据)
	 *@param int $dept_id
	 * @return array
	 */
	public function get_dept_user_tree($dept_id=0)
	{
		$dept_session_id = $this->session->userdata('dept_id');
		$this->load->model('department_model');
		$dept_tree_arr = array();
		//部门id
		if(empty($dept_id)||$dept_id == $dept_session_id)
		{
			$dept_id = $dept_session_id;
			$dept_parent_info = $this->department_model->get_department_info($dept_id);
			$dept_tree_arr[$dept_parent_info['dept_id']] =  new stdClass();
			$dept_tree_arr[$dept_parent_info['dept_id']] -> id = $dept_parent_info['dept_id'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> pid = $dept_parent_info['dept_pid'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> text = $dept_parent_info['dept_name'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> iconCls= 'icon-depart';
			$dept_tree_arr[$dept_parent_info['dept_id']] -> attributes = $dept_parent_info['dept_deep'];
			$dept_tree_arr[$dept_parent_info['dept_id']] -> state = "open";
		}
		//一、下一级 部门
		$dept_info = $this->department_model->get_next_level_depatements($dept_id);
		if($dept_info)
		{
			//判断部门下是否有员工
			foreach($dept_info AS $dept)
			{
				if(empty($dept['state']))
				{
					$if_have_user = $this->user_model->get_user_totalnum($dept['id']);
					if($if_have_user > 0)
					{
						$dept['state'] = "closed";
						$dept['children'] = '';
					}
				}
				$dept_tree_arr[$dept_id] -> children[] = $dept;
			}
		}
		//二、下一级 员工
		$user_info = $this->user_model->get_users(array('dept_id_only'=>$dept_id));
		if($user_info)
		{
			foreach($user_info as $user)
			{
				$tmp_user = new stdClass();
				$tmp_user -> id = 'user'.$user['user_id'];
				$tmp_user -> pid= $user['dept_id'];
				$tmp_user -> text = $user['user_name']."[".$user['user_num']."]";
				$tmp_user -> iconCls= 'icon-user';
				$tmp_user -> attributes = 'last';
				$dept_tree_arr[$user['dept_id']] -> children[] = $tmp_user;
			}
		}
		//返回
		if(!empty($dept_tree_arr))
		{
			if($dept_id == $dept_session_id)
			return array(object_to_array($dept_tree_arr[$dept_id]));
			else
			return object_to_array($dept_tree_arr[$dept_id]->children);
		}
		else
		{
			return $dept_tree_arr;
		}
	}

    /**
     * @param $user_num
     * @return string
     * 通过user_num 获取  password 黑狐
     */
    public function getUserPassByNum($user_num)
    {
        $this->db_read->where("user_num", $user_num);
        $this->db_read->select("user_password");
        $query = $this->db_read->get("est_users");
        if ($query->row()) {
            return $query->row()->user_password;
        }
        return '';
    }

}