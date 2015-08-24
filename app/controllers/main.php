<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Main::index()
	 * 
	 * @return void
	 */
	public function index()
	{
		admin_priv();

		//得到坐席登陆信息
		$this->config->load('myconfig');
		$cfg = $this->config->config;
		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$user_info['vcc_code']	= $this->session->userdata('vcc_code');
		$user_info['vcc_id']	= $this->session->userdata('vcc_id');
		$user_info['user_phone'] = $this->session->userdata['user_phone'];
		//话机类型 softphone 软电话  telephone 话机 1自动 2软电话 3话机
		if($user_info['user_phone_type'] == 1)
		{
			if(strlen($user_info['user_phone'])<5)
			{
				$user_info['user_phone_type'] = 'softphone';
			}
			else
			{
				$user_info['user_phone_type'] = 'telephone';

			}
		}
		else if($user_info['user_phone_type'] == 2)
		{
			$user_info['user_phone_type'] = 'softphone';
		}
		else
		{
			$user_info['user_phone_type'] = 'telephone';
		}
		//号码前缀
		if($cfg["sip_prefix"])
		{
			$sip_prefix = 'ss'.$this->session->userdata('vcc_id').'ss';
		}
		else
		{
			$sip_prefix = '';
		}
		$this->smarty->assign('sip_prefix',$sip_prefix);
		$this->smarty->assign('mobile_prefix',$cfg["mobile_prefix"]);
		$this->smarty->assign('user_info',$user_info);
		$this->smarty->assign('sys_title',$cfg["system_name"]);
		$this->smarty->assign('tel_server_port',$cfg["tel_server_port"]);

		//技能组(队列)信息
		$this->load->model('phone_control_model');
		$que_list = $this->phone_control_model->get_que_list();
		$this->smarty->assign("que_list",$que_list);

		//菜单
		$this->load->model('role_model');
		$menu = $this->role_model->get_menu_list();
		$this->smarty->assign('menu',$menu);

		/*	话务功能权限 */
		$this->smarty->assign('consult',check_authz("consult"));//咨询
		$this->smarty->assign('transfer',check_authz("transfer"));//转接
		$this->smarty->assign('threeway',check_authz("threeway"));//三方
		$this->smarty->assign('evaluation',check_authz("evaluation"));//转评价

		//权限：知识库(查看)
		$power_zsk_view = check_authz("power_zsk_view");
		$this->smarty->assign("power_zsk_view",$power_zsk_view?$power_zsk_view:0);
		//权限：发消息
		$power_sendxx   = check_authz("wdzswdxx_sendxx");
		$this->smarty->assign("power_sendxx",$power_sendxx?$power_sendxx:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();

		//通道连接方式 swf：flash  amq：消息队列
		$system_connect_type = 'amq';
		if($config_info && !empty($config_info['connect_type']))
		{
			$system_connect_type = $config_info['connect_type'];
		}
		$this->smarty->assign('system_connect_type',$system_connect_type);

		//设置向导 - 弹屏
		$role_type = $this->session->userdata('role_type');
		$admin_login_system = false;
		if($role_type==DATA_DEPARTMENT)
		{
			if(!$config_info || $config_info['login_wizard']==0)
			$admin_login_system = true;

		}
		$this->smarty->assign('admin_login_system',$admin_login_system);

		if($config_info['use_custom_menu'])
		{
			$pop_address = $this->role_model->get_pop_address();
			$this->smarty->assign('pop_address',$pop_address);
		}

        //是否启用转技能组功能 0否 1是
        $use_transque = 0;
        if($config_info && !empty($config_info['use_transque']))
        {
            $use_transque = $config_info['use_transque'];
        }
        $this->smarty->assign('use_transque',$use_transque);

		
		//获取置忙原因
		$this->load->model("busy_model");
		$busy = $this->busy_model->get_busy_reason();
		$this->smarty->assign('busy',$busy);

		$this->smarty->display('index.htm');
	}

    /**
     * 检查剩余时间,如果小于10天则提示消息
     */
    public function check_date()
    {
        $this->config->load('myconfig');
        $cfg = $this->config->config;
        if ($cfg['if_enable_expired_not_alowed_to_use']) {
            $vcc_code = $cfg['vcc_code'];
            $this->db = $this->load->database("wintels", true);
            $this->db->select("open_date, due_date");
            $this->db->from("cc_ccods");
            $this->db->where(array("vcc_code" => $vcc_code));
            $query = $this->db->get();
            $query_result = $query->row_array();
            if ($query_result) {
                $due_date = $query_result['due_date'];
                if ($due_date) {
                    $due_second = strtotime($due_date);
                    $left_times = $due_second - strtotime('now');
                    if ($left_times <= 10*24*60*60) {
                        $left_day = ceil($left_times/(24*3600));
                        $this->smarty->assign('left_day', $left_day);
                        $this->index();//此处可替换成跳转页面
                    } else if ($left_times <= 0) {
                        sys_msg("您使用的系统已经到期", 0, "index.php?c=login");
                    }else {
                        $this->index();
                    }
                } else {
                    $this->index();
                }
            } else {
                sys_msg("有效期未知", 0, "index.php?c=login");
            }
        } else {
            $this->index();
        }
    }

    /**
     * 设置企业截至日期页面
     */
    public function set_due_date()
    {
        if($this->session->userdata("flag_singin")){
            $this->session->unset_userdata('flag_singin');
            $this->config->load('myconfig');
            $cfg = $this->config->config;
            $vcc_code = $cfg['vcc_code'];
            $this->smarty->assign('vcc_code', $vcc_code);
            $this->smarty->display('set_due_date.html');
        } else{
            $this->check_password();
        }
    }

    /**
     * 更新企业截至日期
     */
    public function update_due_date()
    {
        $vcc_code = $this->input->post("vcc_code");
        $due_date = $this->input->post("due_date");
        if (empty($vcc_code)) {
            echo json_encode("企业代码为空");
            exit;
        }
        if (empty($due_date)) {
            echo json_encode("请设置截止时间");
            exit;
        }
        $wintel_db = $this->load->database("wintels", true);
        $wintel_db->where("vcc_code", $vcc_code);
        $result = $wintel_db->update("cc_ccods", array("due_date" => $due_date));
        if($result) {
            echo json_encode("1");//更新成功
        } else {
            echo json_encode("0");//更新失败
        }
    }

    /**
     *设置日期密码页面
     */
    public function check_password()
    {
        $this->smarty->display('valid.html');
    }

    /**
     * 验证日期设置密码
     */
    public function signin(){
        $password=$this->input->post('password');
        if ($password == 'wintelpassw0rd') {
            $this->session->set_userdata("flag_singin", 1);
            echo json_encode("1");//密码正确
        } else {
            echo json_encode("0");//密码错误
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */