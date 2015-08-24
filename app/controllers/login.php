<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 判断登陆数据中是否有值 有直接进入首页
	 *
	 */
	private function check_login()
	{
		$db_main  = $this->session->userdata('db_main');
		if(empty($db_main) || $db_main == 'localhost')
		{
			return;
		}
		if($this->session->userdata('user_id')>0 && $this->session->userdata('vcc_id') >0)
		{
			est_header('location:index.php?c=main');
			return ;
		}
	}
	/**
	 * Login::index()
	 * 
	 * @return void
	 */
	public function index()
	{
		$this->check_login();
		$this->config->load('myconfig');
		$cfg = $this->config->config;
		$this->smarty->assign('title', $cfg["system_name"].$cfg['version']);
		if(!empty($cfg['vcc_code']))
		{
			$vcc_code = $cfg['vcc_code'];
			$this->smarty->assign('saas_version', false);
		}
		else
		{
			$this->smarty->assign('saas_version', true);
			$vcc_code = $this->input->cookie('vcc_code');
		}
		$this->smarty->assign('vcc_code', $vcc_code);
		$pho_num = $this->input->cookie('pho_num');
		$this->smarty->assign('pho_num', $pho_num);
		$user_num = $this->input->cookie('user_num');
		$this->smarty->assign('user_num', $user_num);
		$this->smarty->assign('copyright', $cfg["copyright"]);
		$this->smarty->display('login.htm');
	}

	/**
     * Login::get_captcha()
     * 
     * @todo 获取验证码
     * @return void
     */
	public function get_captcha()
	{
		log_message('debug', 'get_captcha'.time());
        $this->config->load('myconfig');
        $cfg = $this->config->config;
		$this->load->helper('captcha');
		$cap_path = isset($cfg['captcha_path']) ? $cfg['captcha_path'] : './public/captcha/';
		if (!file_exists($cap_path))
		{
			@mkdir($cap_path, 0777);
			@chmod($cap_path, 0777);
		}
        $vals = array(
            'img_path' => $cap_path,
            'img_url' => $cap_path,
            'img_width' =>60,
            'img_height' => 24,
            'expiration' => 600,
            'word_length' => 4
        );
		$cap = create_captcha($vals);
		$this->session->set_flashdata(array('captcha'=>$cap['word'],'captcha_time'=>$cap['time']));
		make_json_result($cap['image']);
	}

	/**
     * Login::signin()
     * 
     * @return void
     */
	public function signin()
	{
		/* 验证验证码是否正确 */
		$captcha   = $this->input->post('captcha');
		if(strtolower($captcha) == strtolower($this->session->flashdata('captcha')) and time() - $this->session->flashdata('captcha_time') <= 600)
		{

		}
		else
		{
			make_json_error('验证码错误');
		}

		$this->load->model('login_model');
		$vcc_code = $this->input->post('vcc_code');
		$user_num = $this->input->post('user_num');
		$password  = $this->input->post('password');
		$pho_num = $this->input->post('pho_num');
		$this->input->set_cookie('pho_num',$pho_num,86500);
		$this->input->set_cookie('user_num',$user_num,86500);
		$this->input->set_cookie('vcc_code',$vcc_code,86500);
		$sign_res = $this->login_model->check_login($vcc_code,$user_num,$password,$pho_num);

		if($sign_res['signin'])
		{
			log_action(1,"登录系统");
			make_json_result();
		}
		else
		{
			$err_msg = '';
			switch ($sign_res['msg'])
			{
				case 'Wrong Agent Password': $err_msg = '用户名或密码错误';break;
				case 'No Role':$err_msg = '账号未分配权限';break;
				default:$err_msg = $sign_res['msg'];
			}
			$this->session->sess_destroy();//清除session
			make_json_error($err_msg);
		}
	}

	/**
	 * Login::signout()
	 * 
	 * @return void
	 */
	public function signout()
	{
		log_action(2,"退出系统");
		$this->session->sess_destroy();
		header("Location:index.php?c=login");
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */