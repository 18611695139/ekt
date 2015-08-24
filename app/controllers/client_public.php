<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 公共客户
 * ============================================================================
 * 版权所有 2009-2013 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : client_public.php 
 * $Author: 
 * $time  :
 */
class Client_public extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 公共客户列表
	 */
	public function index()
	{
		admin_priv('khglggkh');
		//得到客户管理列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT);
		$this->smarty->assign("cle_display_field",$cle_display_field);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$this->smarty->display('client_public_list.htm');
	}

	/**
	 * 获取无所属人客户列表数据
	 */
	public function list_client_public_query()
	{
		admin_priv('khglggkh',false);
		$condition = $this->input->post();
		$condition['user_id'] = 0;
		//公告客户(全部)
		$power_all_public_client = check_authz("khglggkeqb");
		if($power_all_public_client)
		{
			$condition['gl_all_data'] = true;
		}
		else
		{
			$condition['gl_all_data'] = false;
		}
		//数据类型
		if(!empty($condition['all_type']))
		{
			$condition['cle_public_type'] = $condition['all_type'];
		}
		
		//数据
		$this->load->model("client_model");
		if($power_all_public_client)
		{
			$responce = $this->client_model->get_client_list($condition,array(),0, 10, null, null,true);
		}
		else
		{
			$responce = $this->client_model->get_client_list($condition);
		}
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 占用一条无所属人的数据
	 */
	public function take_up_client()
	{
		admin_priv();
		$cle_id = $this->input->post("cle_id");
		if (empty($cle_id))
		{
			make_json_error('操作失败');
			return;
		}

		$this->load->model("client_model");
		$client_info = $this->client_model->get_client_info($cle_id);
		if($client_info['cle_public_type']==0 && $client_info['user_id']!=0)
		{
			make_json_error('操作失败，该客户刚被【'.$client_info["user_name"].'】占用了');
			return;
		}
		else
		{
			//判断是否超过客户限制
			$user_id = $this->session->userdata('user_id');
			$check_amount_result = $this->client_model->check_client_amount($user_id);
			if($check_amount_result)
			{
				//占用
				$this->load->model("client_public_model");
				$result = $this->client_public_model->take_up_client($cle_id);
				make_simple_response($result);
			}
			else
			{
				make_json_error("操作失败，您当前客户数量已达或超过客户限制数量！");
			}
		}
	}
	
	/**
	 * 基本搜索
	 */
	public function base_search_public()
	{
		admin_priv();
		$this->smarty->display('client_public_search_base.htm');
	}
}