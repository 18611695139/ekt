<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 系统参数设置
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : system_config.php
 * $Author: yhx
 * $time  : Tue Mar 26 10:20:18 CST 2013
*/
class System_config extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Classname::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv('xtglparamset');

		//获取系统配置参数
		$this->load->model("system_config_model");
		$result = $this->system_config_model->get_system_config();
		$this->smarty->assign("config_info",$result);
		//判断是否有订单模块、客服模块
		$power_order = 0;
		$power_service = 0;
		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action))
		{
			$power_order = 1;
		}
		$this->smarty->assign('power_order',$power_order);
		if(in_array('kffw',$action))
		{
			$power_service = 1;
		}
		$this->smarty->assign('power_service',$power_service);

		$this->smarty->display("system_config_info.htm");
	}

	/**
	 * 更新系统配置信息
	 *
	 */
	public function update_config()
	{
		admin_priv();

		$inarray = $this->input->post();

		//update
		$this->load->model("system_config_model");
		$result = $this->system_config_model->update_system_config($inarray);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("操作失败！");
		}
	}

	//已弹框修改
	public function system_had_login()
	{
		admin_priv();

		$this->load->model("system_config_model");
		$result = $this->system_config_model->update_system_config(array('login_wizard'=>1));
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("操作失败！");
		}
	}
}