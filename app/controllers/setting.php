<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 使用帮助
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : setting.php 
 * $Author: 
 * $time  : 
*/
class Setting extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 设置向导 - 管理员设置向导
	 *
	 */
	public function index()
	{
		admin_priv('ztglsybz');
		
		$action = $this->session->userdata('role_action');
		$action = explode(',',$action);
		//客服
		if(in_array('kffw',$action))
		$this->smarty->assign('power_service',1);
		//产品
		if(in_array('cpgl',$action))
		$this->smarty->assign('power_product',1);
		//订单
		if(in_array('ddgl',$action))
		$this->smarty->assign('power_order',1);

		$this->smarty->assign('this_setting',1);//管理员向导

		$this->smarty->display('setting.htm');
	}

	/**
	 * 设置向导 - 部门
	 */
	public function get_setting_department()
	{
		$this->smarty->assign('setting_department',1);
		$this->smarty->display('department_info.htm');
	}
	
	/**
	 * 设置向导 - 产品分类
	 */
	public function get_setting_product_class()
	{
		$this->smarty->assign('setting_product_class',1);
		$this->smarty->display('product_class_info.htm');
	}

	/**
	 * 设置向导 - 帮助向导
	 */
	public function admin_setting()
	{
		$this->smarty->assign('this_setting',201);//管理员向导
		$this->smarty->display('setting.htm');
	}

	/**
	 * 设置向导 - 坐席帮助向导
	 */
	public function user_setting()
	{
		admin_priv();
		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		//话机类型 softphone 软电话  telephone 话机 1自动 2软电话 3话机
		if($user_info['user_phone_type'] == 1)
		{
			if(strlen($user_info['user_phone'])<5)
			{
				$user_phone_type = 'softphone';
				$this_setting = 101;
			}
			else
			{
				$user_phone_type = 'telephone';
				$this_setting = 103;

			}
		}
		else if($user_info['user_phone_type'] == 2)
		{
			$user_phone_type = 'softphone';
			$this_setting = 101;
		}
		else
		{
			$user_phone_type = 'telephone';
			$this_setting = 103;
		}

		$this->smarty->assign('user_phone_type',$user_phone_type);

		$this->smarty->assign('this_setting',$this_setting);//管理员向导

		$this->smarty->display('setting.htm');
	}

	/**
	 * 设置向导 - 向导内容
	 *
	 */
	public function get_setting_content()
	{
		$setting = $this->input->get('this_setting');
		$this->smarty->assign('setting',$setting);
		$this->smarty->display('setting_content.htm');
	}

	public function setting_bomb_box()
	{
		$type = $this->input->get('type');
		$this->smarty->assign('type',$type);
		$this->smarty->display('setting_index.htm');
	}

}