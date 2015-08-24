<?php

use Guzzle\Http\Client;

class Login_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
     * Login_model::check_login()
     * @param string $vcc_code
     * @param int $user_num
     * @param int $password
     * @param string $pho_num
     * @return array -1 用户名密码错误 1 验证通过
     */
	public function check_login($vcc_code,$user_num,$password,$pho_num='')
	{
        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_code' => $vcc_code,
            'ag_num' => $user_num,
            'password' => $password
        );
        $request = $client->post($api.'/api/agent/signin', array(), $params);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }
        $code = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data = isset($response['data']) ? $response['data'] : array();
		//登陆成功
		if($code == 200)
		{
			if(empty($data['db_main_ip']))
			{
				return array('signin' => false,'msg'=>'未配置数据库的连接信息');
			}
            $user_id = isset($data['ag_id']) ? $data['ag_id'] : 0;
            $role_action = isset($data['role_action']) ? $data['role_action'] : '';
            $vcc_data = array(
                'user_id'=> $user_id,
                'vcc_code'=>$vcc_code,
                'vcc_id'=>isset($data['vcc_id']) ? $data['vcc_id'] : 0,
                'db_main'=>isset($data['db_main_ip']) ? $data['db_main_ip'] : 0,
                'db_slave'=>isset($data['db_slave_ip']) ? $data['db_slave_ip'] : 0,
                'db_name'=>isset($data['db_name']) ? $data['db_name'] : 0,
                'db_user'=>isset($data['db_user']) ? $data['db_user'] : 0,
                'db_pwd'=>isset($data['db_password']) ? $data['db_password'] : 0,
                'role_action' => isset($data['role_action']) ? $data['role_action'] : '',
            );
            $this->config->load('myconfig');
            $cfg = $this->config->config;
            $vcc_code = $cfg['vcc_code'];

            //有效期判断
            if ($cfg['if_enable_expired_not_alowed_to_use']) {
                $this->db = $this->load->database('wintels', true);
                $this->db->select("open_date , due_date");
                $this->db->from("cc_ccods");
                $this->db->where(array("vcc_code" => $vcc_code));
                $query = $this->db->get();
                if ($query) {
                    $query_result = $query->row_array();
                    if (!empty($query_result)) {
                        $open_date = $query_result['open_date'];
                        $due_date = $query_result['due_date'];
                        if (!empty($due_date) && !empty($open_date)) {
                            if (date('Y-m-d') < $open_date) {
                                return array('signin' => false,'msg'=>'还未到开始日期，请联系客服');
                            }
                            if(date('Y-m-d') > $due_date) {
                                return array('signin' => false,'msg'=>'您使用的系统已经到期，请联系客服');
                            }
                        }
                    } else {
                        return array('signin' => false,'msg'=>'未设置有效期,请联系客服');
                    }
                } else {
                    return array('signin' => false,'msg'=>'未设置有效期,请联系客服');
                }
            }
			$this->session->set_userdata($vcc_data);
		}
		else
		{
			return array('signin' => false,'msg' => $message);
		}
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
        //print_r($user_info);
		$this->load->model('role_model');
		if(empty($user_info['role_id']))
		{
			//统计已分配角色的坐席数量
			$role_users_count = $this->user_model->get_role_users_count();
			if ($role_users_count > 0)
			{
				//已经存在分配到角色坐席
				return array('signin' => false,'msg'=>'用户未配置角色信息');
			}
			else
			{
				//所有坐席都未分配角色，该用户为系统首个用户  - 为该用户添加角色：1,部门为1（根部门）
				$this->user_model->update_user(array("role_id"=>1,"dept_id"=>1),array("user_id"=>$user_id));
				$user_info['role_id'] = 1;
				$user_info['dept_id'] = 1;
			}
		}

		$role_info = $this->role_model->get_role_info($user_info['role_id']);
		$role_action_list = $this->role_model->filter_role_action_list($role_info['role_action_list'],$role_action);
		// set session
        //print_r($user_info);
        $user_data = array(
            'user_name'=>$user_info['user_name'],
            'user_num'=>$user_info['user_num'],
            'dept_id' => $user_info['dept_id'],
            'role_id' => $role_info['role_id'],
            'role_type' => $role_info['role_type'],
            'role_action_list'=>$role_action_list
        );

		$this->config->load('myconfig');
		$binding_phone = $this->config->item('binding_phone');
		if($binding_phone)
		{
			$user_data['user_phone'] = $user_info['user_phone'];
		}
		else
		{
			$user_data['user_phone'] = $pho_num;
		}
		$this->session->set_userdata($user_data);
		if(empty($user_info['user_last_ip']))
		{
			$this->input->set_cookie('est_login_system',1,60);
		}
		$this->user_model->set_agent_login();

		return array('signin'=>true);
	}
}
