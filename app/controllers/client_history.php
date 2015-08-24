<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Client_history extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		admin_priv();
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//得到客户管理列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT);
		$this->smarty->assign("cle_display_field",$cle_display_field);

		$this->smarty->display("client_history.htm");
	}

	/**
	 * 获取历史信息列表数据
	 *
	 */
	public function get_client_history_list()
	{
		admin_priv();
		$condition = $this->input->post();
		$condition['gl_all_data'] = true;
		//数据
		$this->load->model("client_history_model");
		$responce = $this->client_history_model->get_client_history_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 基本检索
	 */
	public function base_search_history()
	{
		admin_priv();
		$this->smarty->display('client_history_search_base.htm');
	}

	/**
	 * 获取客户基本信息页面
	 */

	public function get_client_base_page()
	{
		admin_priv();
		$cle_id    = $this->input->get('cle_id');
		if (empty($cle_id))
		{
			echo "该客户不存在";
			return  false;
		}
		$only_page = $this->input->get('only_page');
		if($only_page)
		{
			$this->smarty->assign('only_page',$only_page);
		}
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);

		//得到客户信息
		$this->load->model("client_history_model");
		$client_info = $this->client_history_model->get_client_info($cle_id);
		//---------------------------------------------------------------------------------------
		$this->load->model("client_model");
		//得到客户可编辑的字段（基本字段 + 自定义字段）
		$can_update_fields = $this->client_model->get_update_available_fields();
		$client_base = array(); //基本字段名
		$client_confirm = array();//自定义字段信息
		$parent_id = array();//下拉选项自定义field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义字段fields
		foreach($can_update_fields as $field_info)
		{
			if($field_info['if_custom']==1)
			{
				$client_confirm[] = $field_info;
				if($field_info['data_type']==DATA_TYPE_SELECT)
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL)
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
				}
			}
			else
			{
				$client_base[$field_info['fields']] = $field_info['fields'];
			}
		}
		$this->smarty->assign("client_base",$client_base);
		$this->smarty->assign("client_confirm",$client_confirm);

		$this->load->model("dictionary_model");
		//信息来源
		if(in_array('cle_info_source',$client_base))
		{
			$cle_info_source = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_INFO);
			$this->smarty->assign("cle_info_source",$cle_info_source);
		}
		//获取所有省信息、市
		if(in_array('cle_province_name',$client_base))
		{
			$this->load->model('regions_model');
			$regions_province_info = $this->regions_model->get_regions(REGION_PROVINCE,1);//省 深度、父id
			$this->smarty->assign("regions_province_info",$regions_province_info);
			if(!empty($client_info['cle_province_id']))
			{
				$regions_city_info = $this->regions_model->get_regions(REGION_CITY,$client_info['cle_province_id']);//市 深度、父id
				$this->smarty->assign("regions_city_info",$regions_city_info);
			}
		}
		//号码状态 DICTIONARY_CLIENT_STATE
		if(in_array('cle_stat',$client_base))
		{
			$client_state_all = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STATE);
			$client_state = array();
			for($i=count($client_state_all)-1;$i>=0;$i--)
			{
				$client_state[] = $client_state_all[$i];
			}
			$this->smarty->assign("client_state",$client_state_all);
		}
		//客户阶段
		if(in_array('cle_stage',$client_base))
		{
			$cle_stage =  $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
			$this->smarty->assign("cle_stage",$cle_stage);
		}

		//自定义字段
		$this->load->model("field_confirm_model");
		//自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		//自定义字段 级联
		$jl_options = array();//级联自定义选项
		$jl_have_three = array();//判断各级联字段是两级/三级
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			foreach($jl_field as $k=>$v)
			{
				if(!empty($client_info[$v.'_1']))
				{
					$jl_p_id[] = $client_info[$v.'_1'];
				}
				if(!empty($client_info[$v.'_2']))
				{
					$jl_p_id[] = $client_info[$v.'_2'];
				}
				if(!empty($client_info[$v.'_3']))
				{
					$jl_p_id[] = $client_info[$v.'_3'];
				}
			}
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义选项
			$jl_have_three = $this->field_confirm_model->check_jl_if_have_three($jl_id);//判断各级联字段是两级/三级
		}
		$this->smarty->assign("jl_options",$jl_options);
		$this->smarty->assign("jl_have_three",$jl_have_three);

		$this->smarty->assign("client_info",$client_info);
		$this->smarty->display('client_base_info.htm');
	}
}