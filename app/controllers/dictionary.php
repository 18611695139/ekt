<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 数据字典
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : dictionary.php 
 * $Author: 
 * $time  : 
*/
class Dictionary extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Dictionary::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv();

		$type = $this->input->get('type');
		$this->smarty->assign('type',$type);

		//客户阶段 等级 （客户阶段 用的）
		$this->smarty->assign('client_type',array(CSKH=>'初始',GJKH=>'跟进',ZJKH=>'终结'));
		
		//判断号码状态字段是否启用
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$this->smarty->assign("client_base",$client_base);

		$this->smarty->display("dictionary_info.htm");
	}

	public function get_dictionary_detail()
	{
		admin_priv();

		$p_id = $this->input->get("p_id");
		$this->smarty->assign("p_id",$p_id);
		if ($p_id)
		{
			$this->load->model("dictionary_model");
			//查询数据字典详细信息
			$dinfo = $this->dictionary_model->get_dictionary_detail($p_id);
			$this->smarty->assign("dinfo",$dinfo);
			//类型名称
			$dic_name = $this->dictionary_model->get_dictionary_info($p_id);
			$this->smarty->assign("dictionary_type",empty($dic_name["name"])?"":$dic_name["name"]);

			//客户阶段 等级 （客户阶段 p_id = 2 时用的）
			if($p_id == 2)
			{
				$this->smarty->assign('client_type',array(CSKH=>'初始',GJKH=>'跟进',ZJKH=>'终结'));
				$this->load->model('client_type_model');
				$all_cle_types = $this->client_type_model->get_all_cle_type_info();
				$this->smarty->assign('all_cle_types',$all_cle_types);
			}
		}
		$if_refesh = $this->input->get('if_refesh');
		$this->smarty->assign("if_refesh",$if_refesh);

		$this->smarty->display("dictionary_content.htm");
	}

	/**
	 * 保存数据字典信息
	 *
	 */
	public function save_dictionary_detail()
	{
		admin_priv();

		$option_json = $this->input->post('option');
		$this->load->library('Json');
		$options = $this->json->decode($option_json);
		$dict_id = $this->input->post('dict');
		//保存信息
		$this->load->model("dictionary_model");
		$result = $this->dictionary_model->save_dictionary_detail($dict_id,$options);
		make_simple_response($result);
	}
	
	/**
	 * 数字字典 - 统一页面
	 */
	public function get_dictionary_page()
	{
		$this->smarty->assign('client_type',array(CSKH=>'初始',GJKH=>'跟进',ZJKH=>'终结'));
		
		//判断号码状态字段是否启用
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$this->smarty->assign("client_base",$client_base);
		
		$action = $this->session->userdata('role_action');
		$action = explode(',',$action);
		//客服
		if(in_array('kffw',$action))
		$this->smarty->assign('power_service',1);
		//订单
		if(in_array('ddgl',$action))
		$this->smarty->assign('power_order',1);
		
		$this->smarty->display("dictionary_page.htm");
	}
}