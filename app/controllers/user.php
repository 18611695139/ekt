<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Guzzle\Http\Client;

class User extends CI_Controller {
	
	/**
	 * 员工列表页面
	 */
	public function list_user()
	{
		admin_priv('xtglyhgl');
		$this->smarty->assign('user_session_id',$this->session->userdata('user_id'));
		$this->smarty->assign('role_session_id',$this->session->userdata('role_id'));
		$this->smarty->display("user_list.htm");
	}

	/**
	 * 获取员工列表数据
	 *
	 */
	public function list_user_query()
	{
		admin_priv('xtglyhgl',false);

		$condition = $this->input->post();
		$this->load->model('user_model');
		$responce = $this->user_model->get_user_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 编辑员工界面
	 *
	 */
	public function edit_user()
	{
		admin_priv();
		$user_id = $this->input->get('user_id');
		if(empty($user_id))
		{
			sys_msg('该员工不存在');
		}
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$this->smarty->assign('user_info',$user_info);
		//角色显示
		$this->load->model('role_model');
		$role_tmp = $this->role_model->get_role_list();
		$roles = array();
		foreach ($role_tmp as $role)
		{
			$role_id = $role['role_id'];
			$role_name = $role['role_name'];
			$roles[$role_id] = $role_name;
		}
		$role_session_id = $this->session->userdata('role_id');
		if($role_session_id!=1 && $user_info['role_id']!=1)
		{
			unset($roles[1]);
		}
		$this->smarty->assign('roles',$roles);
		$this->smarty->assign('role_session_id',$role_session_id);
		$this->smarty->display("user_info.htm");
	}

	/**
	 * 执行修改员工信息
	 *
	 */
	public function update_user()
	{
		admin_priv();

		$user_id = $this->input->post('user_id');
		$password = $this->input->post('password');
		$data = $this->input->post();
		$this->load->model('user_model');
		if(!empty($password))
		{
			$user_info = $this->user_model->get_user_info($user_id);
			$res = $this->user_model->reset_password($user_id,$user_info['user_password'],md5($password));
			if ($res!=1)
			{
				make_json_error($res);
			}
		}
		$where = array('user_id'=>$user_id);
        //$this->save_permissions($this->input->post('user_phone_permissions'));//呼叫权限
		$result = $this->user_model->update_user($data,$where);
		make_simple_response($result);
	}

	/**
	 * 获取指定员工姓名
	 *
	 */
	public function get_user_name()
	{
		admin_priv();

		$user_id = $this->input->get('user_id');
		$this->load->model("user_model");
		$user_info = $this->user_model->get_user_info($user_id);
		$user_name = $user_info['user_name'];
		if($user_name)
		{
			make_json_result($user_name);
		}
		else
		{
			make_json_error();
		}
	}

	/**
	 * 通过工号取得员工id
	 *
	 */
	public function get_user_id_by_num()
	{
		admin_priv();

		$user_num = $this->input->post('user_num');
		$this->load->model("user_model");
		$user_id = $this->user_model->get_user_id_by_num($user_num);
		$user_session_id = $this->session->userdata('user_id');
		if($user_id)
		{
			if($user_session_id != $user_id)
			{
				make_json_result($user_id);
			}
			else
			{
				make_json_error('不能自己呼自己');
			}
		}
		else
		{
			make_json_error();
		}
	}

	/**
	 * 得到空闲的坐席
	 *
	 */
	public function get_free_users()
	{
		admin_priv();

		$this->load->model('user_model');
		$responce = $this->user_model->get_free_users();
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 个人设置
	 *
	 */
	public function self_set_user()
	{
		admin_priv();

		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$this->smarty->assign('user_info',$user_info);
		$this->smarty->display('user_set.htm');
	}

	/**
	 * 个人设置保存
	 *
	 */
	public function self_update_user()
	{
		admin_priv();

		$user_id = $this->session->userdata('user_id');
		$old_password = $this->input->post('old_password');
		$password = $this->input->post('password');
		
		//密码修改
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);

		if (! empty($old_password) && !empty($password))
		{
			if(md5($old_password) != $user_info['user_password'])
			{
				make_json_error(2);
			}

			$res = $this->user_model->reset_password($user_id,md5($old_password),md5($password));
			if ($res!=1)
			{
				make_json_error($res);
			}
		}

        //$this->save_permissions($this->input->post('user_phone_permissions'));//呼叫权限
        
        //个人设置字段（除user_id）
		$data = array(
            'user_id'=>$user_id,
            'user_sms_phone'=>$this->input->post('user_sms_phone'),
            "user_if_tip"=>$this->input->post("user_if_tip"),
            'user_ol_model'=>$this->input->post('user_ol_model'),
            'user_display_dialpanel'=>$this->input->post('user_display_dialpanel'),
            'user_name'=>$this->input->post('user_name'),
            'user_login_state'=>$this->input->post('user_login_state'),
            'user_outcall_popup'=>$this->input->post('user_outcall_popup'),
            'user_phone_type'=>$this->input->post('user_phone_type'),
            'user_to_selfphone'=>$this->input->post('user_to_selfphone')
        );

        $user_id = $this->session->userdata('user_id');
        $this->load->model('user_model');
        $user_info = $this->user_model->get_user_info($user_id);

        if ($user_info['user_name'] == $this->input->post('user_name')) unset($data['user_name']);

		$where = array('user_id'=>$user_id);
		$res = $this->user_model->update_user($data,$where);
		if($res===true)
		{
			make_json_result($res);
		}
		else
		{
			make_json_error($res);
		}
	}

	/**
	 * 坐席  combbox
	 *
	 */
	public function get_user_box()
	{
		admin_priv();

		$key_value = $this->input->post("q");
		$dept_id = $this->input->get('dept_id_search');
		$condition = array();
		if(!empty($dept_id))
		{
			$condition['dept_id'] = $dept_id;
		}
		if ($key_value)
		{
			$condition['key_word'] = $key_value;
		}
		$gl_all_data = $this->input->get('gl_all_data');
		if($gl_all_data==1)
		{
			//添加数据权限
			$condition['gl_all_data'] = TRUE;
		}
		$if_have_no_user = $this->input->get('if_have_no_user');
		$this->load->model("user_model");
		$result = $this->user_model->get_user_box($condition,$if_have_no_user);
		$this->load->library("json");
		echo $this->json->encode($result);
	}

	/**
	 * 添加用户
	 */
	public function add_user()
	{
		admin_priv();

		$this->load->model('phone_control_model');
		$que_info = $this->phone_control_model->get_all_ques();
		if( ! $que_info)
		{
			sys_msg("当前没用可用的队列，不能添加员工");
		}
		else
		{
			$this->smarty->assign('que_info',$que_info);
			$this->load->model('role_model');
			//角色显示
			$role_tmp = $this->role_model->get_role_list();
			$roles = array();
			foreach ($role_tmp as $role)
			{
				$role_id = $role['role_id'];
				$role_name = $role['role_name'];
				$roles[$role_id] = $role_name;
			}
			//当前坐席角色id
			$session_rode_id = $this->session->userdata("role_id");
			if($session_rode_id!=1)
			{
				unset($roles[1]);
			}
			$this->smarty->assign('roles',$roles);
			//当前坐席所属部门
			$session_user_id = $this->session->userdata("dept_id");
			$this->smarty->assign("session_user_id",$session_user_id);

			$this->smarty->display('user_add.htm');
		}

	}

	/**
	 * 添加员工
	 *
	 */
	public function insert_user()
	{
		admin_priv();
		
		$insert = $this->input->post();
        //$this->save_permissions($this->input->post('user_phone_permissions'));//呼叫权限
        $this->load->model('user_model');
		$result = $this->user_model->insert_user($insert);
		if($result === true)
		{
            make_json_result();
		}
		else
		{
			make_json_error($result);
		}
	}


    /**
     *员工呼叫权限
     */
    public function save_permissions($str='')
    {
        $this->config->load('myconfig');
        $cfg = $this->config->config;
        $vcc_id = $this->session->userdata('vcc_id');
        $ag_id = $this->session->userdata('user_id');
        if (!$vcc_id)
        {
            die("企业ID缺失！");
        }
        if (!$ag_id)
        {
            die("坐席ID缺失！");
        }
        $client = new Client();
        $param = array(
            'vcc_id' => $vcc_id,
            'call_status' => $str,
            'ag_id' => $ag_id
        );
        $request = $client->post($cfg["api_wintelapi"].'/api/callstatus', array(), $param);
        $array = $request->send()->json();
        switch ($array['code']) {
            case '200':{
                return array('error'=>0);
            }
            case '401':{
                return array('error'=>1,'msg'=>'企业ID为空');
            }
            case '402':{
                return array('error'=>1,'msg'=>'坐席ID为空');

            }
            case '403':{
                return array('error'=>1,'msg'=>'呼叫状态不正确');
            }
            case '404':{
                return array('error'=>1,'msg'=>'更新失败');
            }
        }

        return true;
    }

    /**
	 * 删除员工
	 */
	public function delete_user()
	{
		admin_priv();

		$user_id = $this->input->post('user_id');
		if (empty($user_id))
		{
			make_json_error("缺少参数");
			return;
		}
		$user_session_id = $this->session->userdata("user_id");
		if($user_session_id==$user_id)
		{
			make_json_error("操作失败，不能删除自己");
			return;
		}
		$this->load->model('user_model');
		$result = $this->user_model->delete_user($user_id);
		if ($result==1)
		{
			make_simple_response($result);
		}
		else
		{
			make_json_error("删除失败");
		}
	}

	/*判断员工工号是否已存在*/
	public function if_have_user_num()
	{
		admin_priv();

		$user_num = $this->input->post('user_num');
		$this->load->model('user_model');
		$result = $this->user_model->get_user_id_by_num($user_num);
		if($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error(0);
		}
	}

	/**
	 * 导出员工
	 */
	public function output_user()
	{
		admin_priv();

		@ini_set('memory_limit', '1024M');

		$menu = array('姓名','工号','坐席电话','常接通模式','职位','部门','登录服务器IP','短信提醒号码','是否弹出提示窗口','外呼主叫号码类型','指定的外呼主叫','是否显示外呼窗口','是否外呼弹屏','最后登录IP','最后登录时间','备注');
		$data_info = array();

		$this->load->model('user_model');
		$user_info = $this->user_model->output_user();

        $export_type = $this->input->get('export_type');
        switch ($export_type) {
            case 'csv':
                array_unshift($user_info,$menu);
                $this->load->library("csv");
                $this->csv->creatcsv('员工数据' . date("YmdHis"),$user_info);
                break;
            case 'excel':
                foreach ($user_info as $i=>$user) {
                    foreach ($user as $v) {
                        $data_info[$i][] = $v;
                    }
                }
                $this->load->model('excel_export_model');
                $this->excel_export_model->export($data_info, $menu, '员工数据' . date("YmdHis"));
                break;
            default:
                sys_msg("操作失败");
                break;
        }
	}

	/**
	 * 获取部门及人树
	 *
	 */
	public function get_dept_user_tree()
	{
		admin_priv();
		$this->load->model("user_model");
		$dept_id = $this->input->post('id');
		$responce = $this->user_model->get_dept_user_tree($dept_id);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

}

/* End of file  user.php*/
/* Location: ./app/controllers/user.php */