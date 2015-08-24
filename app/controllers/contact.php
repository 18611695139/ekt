<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI contact
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : contact.php
 * $Author: yhx
 * $time  : Sun Jan 06 16:06:45 CST 2013
*/
class Contact extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 业务受理  -  联系人
	 *
	 */
	public function index()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		if (!$cle_id)
		{
			die("缺少客户参数！");
		}
		$this->smarty->assign("contact_cle_id",$cle_id);

		//得到联系人列表显示字段
		$this->load->model("datagrid_confirm_model");
		$con_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CONTACT);
		$this->smarty->assign("con_display_field",$con_display_field);

		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$this->smarty->display("contact_list.htm");
	}

	/**
	 * 联系人列表数据
	 *
	 */
	public function get_contact_list()
	{
		admin_priv();

		$cle_id = $this->input->post("cle_id");
		$condition  = array();
		if (!empty($cle_id))
		{
			$condition["cle_id"] = $cle_id;
		}

		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_CONFIRM_CONTACT);
		$select[] = 'con_if_main';
		//检索数据
		$this->load->model("contact_model");
		$responce = $this->contact_model->get_contact_list($condition,$select);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 设置联系人类型
	 *
	 */
	public function set_master_contact()
	{
		admin_priv();

		$inarray     = $this->input->post();
		$con_if_main = empty($inarray["con_if_main"]) ? 0 : $inarray["con_if_main"];
		$con_id      = empty($inarray["con_id"]) ? 0 : $inarray["con_id"];
		$cle_id      = empty($inarray["cle_id"]) ? 0 : $inarray["cle_id"];
		if (!$con_id || !$cle_id)
		{
			make_json_error("缺少必要参数，设置失败！");
		}
		//设置
		$this->load->model("contact_model");
		$result = $this->contact_model->set_master_contact($con_id,$cle_id,$con_if_main);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("设置失败，请重新设置！");
		}
	}

	/**
	 * 删除联系人
	 *
	 */
	public function delete_contact()
	{
		admin_priv();

		$con_id = $this->input->post("con_id");
		if (!$con_id)
		{
			make_json_error("缺少联系人参数，执行失败！");
		}

		//删除
		$this->load->model("contact_model");
		$result = $this->contact_model->delete_contact($con_id);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行失败");
		}
	}

	/**
	 * 添加新联系人
	 *
	 */
	public function new_contact()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		if (!$cle_id)
		{
			die("缺少参数！");
		}
		$this->smarty->assign("cle_id",$cle_id);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//得到联系人使用字段
		$this->load->model("field_confirm_model");
		$contact_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CONTACT);
		$contact_base = array(); //联系人基本字段
		$contact_confirm = array();//联系人自定义字段
		$parent_id = array();//自定义字段 下拉选项 field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($contact_fields as $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$contact_base[$field_info['fields']] = $field_info['fields'];
			}
			else
			{
				if($field_info['data_type']==DATA_TYPE_SELECT)//下拉框field_id
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL)//级联field_id
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
					$jl_field_type[$field_info["id"]] = $this->json->decode($field_info["jl_field_type"],1);
				}
				else if($field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$checkbox_id[]=$field_info['id'];
				}
				$contact_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("contact_base",$contact_base);
		$this->smarty->assign("contact_confirm",$contact_confirm);
		$this->smarty->assign("jl_field_type_contact",$this->json->encode($jl_field_type));

		//联系人自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		//自定义字段 级联
		$jl_options = array();
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义
		}
		$this->smarty->assign("jl_options_contact",$jl_options);

		$checkbox_options = array();//关联多选框所有选项信息
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);
		}
		$this->smarty->assign("checkbox_options_contact",$checkbox_options);

		$this->smarty->assign("form_act","insert_contact");
        $this->smarty->assign('now_time',date('Y-m-d H:i:s'));
        $this->smarty->assign('now_date',date('Y-m-d'));
		$this->smarty->display("contact_info.htm");
	}

	/**
	 * 保存新联系人信息
	 *
	 */
	public function insert_contact()
	{
		admin_priv();

		$inarray = $this->input->post();
		//保存
		$this->load->model("contact_model");
		$result = $this->contact_model->insert_contact($inarray);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("数据保存失败");
		}
	}

	/**
	 * 编辑联系人
	 *
	 */
	public function edit_contact()
	{
		admin_priv();

		$con_id = $this->input->get("con_id");
		if (!$con_id)
		{
			die("缺少联系人参数！");
		}
		$this->smarty->assign("con_id",$con_id);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//得到联系人信息
		$this->load->model("contact_model");
		$contact_info = $this->contact_model->get_contact_info($con_id);
		//得到联系人使用字段
		$this->load->model("field_confirm_model");
		$contact_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CONTACT);
		$contact_base = array(); //联系人基本字段
		$contact_confirm = array();//联系人自定义字段
		$parent_id = array();//自定义字段 下拉选项 field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($contact_fields as $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$contact_base[$field_info['fields']] = $field_info['fields'];
			}
			else
			{
				if($field_info['data_type']==DATA_TYPE_SELECT)//下拉框field_id
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL)//级联field_id
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
					$field_info["jl_field_type"] = $this->json->decode($field_info["jl_field_type"],1);
					$jl_field_type[$field_info["id"]] = $field_info["jl_field_type"];
				}
				else if($field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$checkbox_id[]=$field_info['id'];
					if(!empty($contact_info[$field_info["fields"].'_1']))
					{
						$check_1 = explode(',',$contact_info[$field_info["fields"].'_1']);
						foreach($check_1 as $value)
						{
							$contact_info[$field_info["id"].'_1'][$value] = $value;
						}
					}
					if(!empty($contact_info[$field_info["fields"].'_2']))
					{
						$check_2 = explode(',',$contact_info[$field_info["fields"].'_2']);
						foreach($check_2 as $value)
						{
							$contact_info[$field_info["id"].'_2'][$value] = $value;
						}
					}
				}
				$contact_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("contact_base",$contact_base);
		$this->smarty->assign("contact_confirm",$contact_confirm);
		$this->smarty->assign("jl_field_type_contact",$this->json->encode($jl_field_type));
		//联系人自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		//自定义字段 级联
		$jl_options = array();//级联自定义选项
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			foreach($jl_field as $k=>$v)
			{
				if(!empty($contact_info[$v.'_1']))
				{
					$jl_p_id[] = $contact_info[$v.'_1'];
				}
				if(!empty($contact_info[$v.'_2']))
				{
					$jl_p_id[] = $contact_info[$v.'_2'];
				}
			}
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义选项
		}
		$this->smarty->assign("jl_options_contact",$jl_options);
		
		$checkbox_options = array();//级联自定义选
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);//级联自定义选项
		}
		$this->smarty->assign("checkbox_options_contact",$checkbox_options);

		$this->smarty->assign("contact_info",$contact_info);
		$this->smarty->assign("form_act","update_contact");
		$this->smarty->display("contact_info.htm");
	}

	/**
	 * 更新联系人信息
	 *
	 */
	public function update_contact()
	{
		admin_priv();

		$inarray = $this->input->post();
		//更新
		$this->load->model("contact_model");
		$result = $this->contact_model->update_contact($inarray);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("数据更新失败");
		}
	}

	/**
	 * 得到一个联系人电话
	 *
	 */
	public function get_contact_phone()
	{
		admin_priv();

		$cle_id = $this->input->post("cle_id");
		$contact_phone = "";
		if (!empty($cle_id))
		{
			//获取数据
			$this->load->model("contact_model");
			$contact_phone = $this->contact_model->get_contact_phone($cle_id);
		}
		make_json_result($contact_phone);
	}
}