<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 自定义字段设置
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : field_confirm.php 
 * $Author: 
 * $time  : 
*/
class Field_confirm extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取自定义字段信息
	 */
	public function get_field_detail()
	{
		admin_priv();

		$parent_id = $this->input->post('parent_id');
		if(empty($parent_id))
		{
			make_json_result('');
		}
		$this->load->model("field_confirm_model");
		$info = $this->field_confirm_model->get_one_field_info($parent_id);
		if(!empty($info))
		{
			if($info['data_type']==DATA_TYPE_SELECT)
			{
				$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
				if(isset($field_detail[$parent_id]))
				$field_detail = $field_detail[$parent_id];
				make_json_result(array('data_type'=>DATA_TYPE_SELECT,'info'=>$field_detail));
			}
			elseif($info['data_type']==DATA_TYPE_JL||$info['data_type']==DATA_TYPE_CHECKBOXJL)
			{
				$jl_first_info = $this->field_confirm_model->get_jl_options($parent_id,0,1);
				make_json_result(array('data_type'=>$info['data_type'],'jl_series'=>empty($info['jl_series'])?0:$info['jl_series'],'info'=>$jl_first_info));
			}
		}
		make_json_result('');
	}


	/**
	 * 客户(client)、联系人(contact) - 自定义字段
	 *
	 */
	public function field_setting()
	{
		admin_priv();

		//字段类型 ： 客户(0)、联系人(1)、产品(2)、订单(3)、客服服务(4)
		$field_type = $this->input->get("field_type");
		$this->load->model("field_confirm_model");
		//需要编辑的自定义字段
		$edit_field_confirm = $this->field_confirm_model->get_confirm_fields($field_type);

		$if_refesh = $this->input->get('if_refesh');
		$this->smarty->assign("if_refesh",$if_refesh);

		$this->smarty->assign("field_type",$field_type);
		$this->smarty->assign("edit_field_confirm",$edit_field_confirm);
		$this->smarty->display("field_confirm.htm");
	}

	//编辑选项
	public function field_option_setting()
	{
		admin_priv();

		$parent_id = $this->input->get("field_id");
		if (!$parent_id)
		{
			sys_msg("缺少参数");
		}
		$this->smarty->assign("parent_id",$parent_id);

		$this->load->model("field_confirm_model");
		$field_info = $this->field_confirm_model->get_one_field_info($parent_id);
		if($field_info)
		{
			$this->smarty->assign("field_info",$field_info);
		}
		//得到自定义字段 选项信息
		$dinfo = $this->field_confirm_model->get_field_details($parent_id);
		if($dinfo)
		$this->smarty->assign("dinfo",$dinfo[$parent_id]);
		$this->smarty->display("field_confirm_options.htm");
	}

	/**
	 * 保存自定义字段选项
	 *
	 */
	public function save_fields()
	{
		admin_priv();

		//保存信息
		$parent_id = $this->input->post("parent_id");//字段id
		$data_type   = $this->input->post("data_type");//字段类型
		$if_require   = $this->input->post("if_require");//是否必选
		$default   = $this->input->post("default");//默认值
		$datefmt   = $this->input->post("datefmt");//日期格式
		$update = array(
		'data_type'=>$data_type,
		'if_require'=>$if_require,
		'default'=>empty($default)?'':$default,
		'datefmt'=>$datefmt
		);
		if($data_type==DATA_TYPE_JL)
		{
			$update['jl_series'] = $this->input->post("jl_series");//级数
			$update['jl_field_type'] = $this->input->post("jl_field_type");//最后一级类型
		}
		$this->load->model("field_confirm_model");
		$result = $this->field_confirm_model->update_one_field_info($parent_id,$update);
		if($result&&$data_type==DATA_TYPE_SELECT)
		{
			$options   = $this->input->post("options");
			$result = $this->field_confirm_model->save_fields_detail($parent_id,$options);
		}
		make_simple_response($result);
	}

	/**
	 * 添加两个空白字段   
	 *@return array
	 */
	public function add_two_empty_fields()
	{
		admin_priv();

		$field_type = $this->input->post('field_type');
		$this->load->model("field_confirm_model");
		$unit_info = $this->field_confirm_model->add_two_empty_fields($field_type);
		make_json_result('','',$unit_info);
	}

	/**
	 * 删除客户添加的自定义字段
	 *
	 */
	public function delete_create_field()
	{
		admin_priv();

		$field_type = $this->input->post('field_type');
		$first_unit = $this->input->post('first_unit');
		$second_unit = $this->input->post('second_unit');

		$this->load->model("field_confirm_model");
		$result = $this->field_confirm_model->del_two_fields($field_type,$first_unit,$second_unit);
		make_simple_response($result);
	}

	/**
	 * 更新自定义字段 信息
	 *
	 */
	public function update_fields_confirm()
	{
		admin_priv();

		$_data  = $this->input->post("_data");
		$fields = array();
		$state = array();
		$field_value = array();
		foreach ($_data as $key=>$value)
		{
			if ($key != 'undefined')
			{
				$fields[] = $key;
				$tmp = explode('|',$value,2);
				if(count($tmp) < 2)
				{
					continue;
				}
				$state[] = ($tmp[0] == 'checked') ? 1 : 0;
				$field_value[] = $tmp[1];
			}
		}
		$field_type = $this->input->post('field_type');

		//更新 自定义字段 设置
		$this->load->model("field_confirm_model");
		$result = $this->field_confirm_model->update_fields_confirm($fields,$state,$field_value,$field_type);
		make_simple_response($result);
	}

	/**
	 * 字段配置公共页面
	 */
	public function get_field_confirm_system_page()
	{
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);

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

		$this->smarty->display('field_system_page.htm');
	}

	/**
	 * 基本信息页面
	 */
	public function get_field_base()
	{
		$field_type = $this->input->get('field_type');
		//客户的基本字段
		$this->load->model('field_confirm_model');
		$field_info = $this->field_confirm_model-> get_base_fields($field_type);
		$this->smarty->assign('field_info',$field_info);
		$this->smarty->assign('field_type',$field_type);
		$this->smarty->display('field_system_base.htm');
	}

	/**
	 * 保存基本字段是否可用信息
	 */
	public function save_base_field_state()
	{
		$field_type = $this->input->post('field_type');
		$is_use = $this->input->post('_if_use');
		$update_data = array();
		foreach ($is_use as $key=>$value)
		{
			if ($key != 'undefined'&&in_array($value,array(0,1)))
			{
				$update_data[$key] = $value;
			}
		}
		$this->load->model("field_confirm_model");
		$result = $this->field_confirm_model->save_base_field_state($field_type,$update_data);
		make_simple_response($result);
	}

	/**
	 * 保存级联选项
	 */
	public function save_jl()
	{
		$field_id = $this->input->post('field_id');
		$p_id = $this->input->post('p_id');
		$options = $this->input->post('options');
		$type = $this->input->post('type');
		$field_type = $this->input->post('field_type');
		$this->load->model("field_confirm_model");
		$result = $this->field_confirm_model->save_confirm_jl($field_id,$p_id,$type,$options,$field_type);
		make_simple_response($result);
	}

	/**
	 * 获取级联选项信息
	 *
	 */
	public function get_jl_options()
	{
		$field_id = $this->input->post('field_id');
		$p_id = $this->input->post("parent_id");
		$type = $this->input->post('type');

		//级联选项信息
		$this->load->model('field_confirm_model');
		$jl_info = $this->field_confirm_model->get_jl_options($field_id, $p_id,$type);

		make_json_result($jl_info);
	}
}