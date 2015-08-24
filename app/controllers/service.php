<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 客服服务
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : service.php
 * $Author: yhx
 * $time  : Fri Feb 22 17:44:12 CST 2013
*/
class Service extends CI_Controller {

	private  $SERVICE_STATE = array("无需处理"=>"无需处理","未处理"=>"未处理","处理中"=>"处理中","处理完成"=>"处理完成");

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Classname::index()  客服服务-列表
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv('khglservice');

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);

		//得到 客服服务 列表显示字段
		$this->load->model("datagrid_confirm_model");
		$serv_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_SERVICE);
		$this->smarty->assign("serv_display_field",$serv_display_field);

		//客服服务(添加)
		$power_service_add = check_authz("kffwserinsert");
		$this->smarty->assign("power_service_add",$power_service_add?$power_service_add:0);
		//客服服务(删除)
		$power_service_delete = check_authz("kffwserdelete");
		$this->smarty->assign("power_service_delete",$power_service_delete?$power_service_delete:0);
		//客服服务(导出)
		$power_service_export = check_authz("kffwserexport");
		$this->smarty->assign("power_service_export",$power_service_export?$power_service_export:0);

		$this->smarty->display("service.htm");
	}

	/**
	 * 客服服务-列表-获取列表数据
	 *
	 */
	public function get_service_list()
	{
		admin_priv('khglservice',false);

		$condition = $this->input->post();
		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_COMFIRM_SERVICE);
		//数据
		$this->load->model("service_model");
		$responce = $this->service_model->get_service_list($condition,$select);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 添加新的  客服服务 信息
	 *
	 */
	public function add_service()
	{
		admin_priv('kffwserinsert');

		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//客服服务(自定义字段)
		$power_service_confirm = check_authz("xtglzdpz");
		$this->smarty->assign("power_service_confirm",$power_service_confirm?$power_service_confirm:0);
		//客服服务(数据字典)
		$power_service_dictionary = check_authz("xtglszzd");
		$this->smarty->assign("power_service_dictionary",$power_service_dictionary?$power_service_dictionary:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		//得到客户信息
		$cle_id = $this->input->get('cle_id');
		if(!empty($cle_id))
		{
			$this->load->model("client_model");
			$client_info = $this->client_model->get_client_info($cle_id);
		}
		$user_session_id = $this->session->userdata("user_id");
		$client_info["serv_state"] = "未处理";
		$client_info["user_id"]    = $user_session_id;
		$this->smarty->assign("service_info",$client_info);
		$this->smarty->assign("user_session_id",$user_session_id);
		//服务类型
		$this->load->model("dictionary_model");
		$service_type = $this->dictionary_model->get_dictionary_detail(DICTIONARY_SERVICE_TYPE);
		$this->smarty->assign("service_type",$service_type);
		//服务状态
		$this->smarty->assign("service_state",$this->SERVICE_STATE);

		//得到使用字段
		$this->load->model("field_confirm_model");
		$service_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_SERVICE);

		$service_base = array(); //基本字段
		$service_confirm = array();//自定义字段
		$parent_id = array();//自定义字段 下拉选项 field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($service_fields as $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$service_base[$field_info['fields']] = $field_info['fields'];
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
				$service_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("service_base",$service_base);
		$this->smarty->assign("service_confirm",$service_confirm);
		$this->smarty->assign("jl_field_type_service",$this->json->encode($jl_field_type));

		//自定义字段 下拉选项
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
		$this->smarty->assign("jl_options_service",$jl_options);

		$checkbox_options = array();//关联多选框所有选项信息
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);
		}
		$this->smarty->assign("checkbox_options_service",$checkbox_options);

        $this->smarty->assign('now_time',date('Y-m-d H:i:s'));
		$this->smarty->assign('now_date',date('Y-m-d'));
		$this->smarty->assign("form_act_service","insert_service");
		$this->smarty->display("service_info.htm");
	}

	/**
	 * 添加新 客户服务 信息
	 *
	 */
	public function insert_service()
	{
		admin_priv('kffwserinsert',false);

		$inarray = $this->input->post();
		$this->load->model("service_model");
		$result = $this->service_model->insert_service($inarray);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("操作失败！");
		}
	}

	/**
	 * 编辑  客服服务 信息
	 *
	 */
	public function edit_service()
	{
		admin_priv('power_service_edit');

		$serv_id = $this->input->get('serv_id');
		if(empty($serv_id))
		{
			die("缺少参数！");
		}
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//客服服务(自定义字段)
		$power_service_confirm = check_authz("xtglzdpz");
		$this->smarty->assign("power_service_confirm",$power_service_confirm?$power_service_confirm:0);
		//客服服务(数据字典)
		$power_service_dictionary = check_authz("xtglszzd");
		$this->smarty->assign("power_service_dictionary",$power_service_dictionary?$power_service_dictionary:0);
		//客服服务(修改)
		$power_service_edit = check_authz("power_service_edit");
		$this->smarty->assign("power_service_edit",$power_service_edit?$power_service_edit:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		//服务类型
		$this->load->model("dictionary_model");
		$service_type = $this->dictionary_model->get_dictionary_detail(DICTIONARY_SERVICE_TYPE);
		$this->smarty->assign("service_type",$service_type);
		//服务状态
		$this->smarty->assign("service_state",$this->SERVICE_STATE);
		//得到  客户服务信息
		$this->load->model("service_model");
		$service_info = $this->service_model->get_a_service_info($serv_id);
		//得到使用字段
		$this->load->model("field_confirm_model");
		$service_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_SERVICE);
		$service_base = array(); //基本字段
		$service_confirm = array();//自定义字段
		$parent_id = array();//自定义字段 下拉选项 field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($service_fields as $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$service_base[$field_info['fields']] = $field_info['fields'];
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
					if(!empty($service_info[$field_info["fields"].'_1']))
					{
						$check_1 = explode(',',$service_info[$field_info["fields"].'_1']);
						foreach($check_1 as $value)
						{
							$service_info[$field_info["id"].'_1'][$value] = $value;
						}
					}
					if(!empty($service_info[$field_info["fields"].'_2']))
					{
						$check_2 = explode(',',$service_info[$field_info["fields"].'_2']);
						foreach($check_2 as $value)
						{
							$service_info[$field_info["id"].'_2'][$value] = $value;
						}
					}
				}
				$service_confirm[] = $field_info;
			}
		}

		$this->smarty->assign("service_base",$service_base);
		$this->smarty->assign("service_confirm",$service_confirm);
		$this->smarty->assign("jl_field_type_service",$this->json->encode($jl_field_type));
		//自定义 下拉选项
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
				if(!empty($service_info[$v.'_1']))
				{
					$jl_p_id[] = $service_info[$v.'_1'];
				}
				if(!empty($service_info[$v.'_2']))
				{
					$jl_p_id[] = $service_info[$v.'_2'];
				}
			}
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义选项
		}
		$this->smarty->assign("jl_options_service",$jl_options);

		$checkbox_options = array();//级联自定义选
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);//级联自定义选项
		}
		$this->smarty->assign("checkbox_options_service",$checkbox_options);
		
		$this->smarty->assign("service_info",$service_info);
		
		$user_session_id = $this->session->userdata("user_id");
		$this->smarty->assign("user_session_id",$user_session_id);
		
		$this->smarty->assign("form_act_service","update_service");
        $this->smarty->assign('now_time',date('Y-m-d H:i:s'));
        $this->smarty->assign('now_date',date('Y-m-d'));
		$this->smarty->display("service_info.htm");
	}

	/**
	 * 编辑 客户服务 信息 -  更新
	 *
	 */
	public function update_service()
	{
		admin_priv('power_service_edit',false);

		$inarray = $this->input->post();
		$this->load->model("service_model");
		$result = $this->service_model->update_service($inarray);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("操作失败！");
		}
	}

	/**
	 * 删除 客服服务  信息
	 *
	 */
	public function delete_service()
	{
		admin_priv('kffwserdelete');

		$serv_id = $this->input->post("serv_id");
		if (empty($serv_id))
		{
			make_json_error("缺少参数！");
		}

		$this->load->model("service_model");
		$result = $this->service_model->delete_service($serv_id);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("操作失败！");
		}
	}

	/**
	 * 客服信息列表  - 导出 客服服务 信息
	 *
	 */
	public function service_output()
	{
		admin_priv('kffwserexport');

		set_time_limit(0);
		@ini_set('memory_limit', '1024M');

        //导出信息
		$data_info = array();

        //转换检索条件
		$condition = $this->input->get();
		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_COMFIRM_SERVICE);

        //数据
        $total     = empty($condition["total"]) ? 0 : $condition["total"];
        $sortName  = empty($condition["sortName"]) ? "serv_accept_time" : $condition["sortName"];
        $sortOrder = empty($condition["sortOrder"]) ? "DESC" : $condition["sortOrder"];

		//得到 客服服务 信息
		$this->load->model('service_model');
        $responce = $this->service_model->get_service_list($condition,$select,1,$total,$sortName,$sortOrder);
        $service_info = $responce->rows;

		$this->load->model('datagrid_confirm_model');
		$field_info = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_SERVICE );
		$field = array();
		$fields = array();
		foreach($field_info AS $value)
		{
			$fields[$value['fields']] = $value['name'];
			$field[] = $value['fields'];
		}

		for($i=0,$k=0;$i<count($service_info);$i++,$k++)
		{
			for($j=0;$j<count($field);$j++)
			{
                //$data_info[$i][] = $service_info[$k][$field[$j]];
                $data_info[$i][$field[$j]] = @str_replace("\r","",str_replace("\n","",$service_info[$k][$field[$j]]));
			}
		}

        $export_type = $this->input->get('export_type');
        switch ($export_type) {
            case 'csv':
                array_unshift($data_info,$fields);
                $this->load->library("csv");
                $this->csv->creatcsv('客服服务数据' . date("YmdHis"),$data_info);
                break;
            case 'excel':
                $this->load->model('excel_export_model');
                $this->excel_export_model->export($data_info, $fields, '客服服务数据' . date("YmdHis"));
                break;
            default:
                sys_msg("操作失败");
                break;
        }
	}

	/**
	 * 客服服务 - 列表 - 基本搜索
	 */
	public function base_search()
	{
		admin_priv();

		//服务类型
		$this->load->model("dictionary_model");
		$service_type = $this->dictionary_model->get_dictionary_detail(DICTIONARY_SERVICE_TYPE);
		$this->smarty->assign("service_type",$service_type);
		$this->smarty->assign("service_type_count",empty($service_type) ? 0 : count($service_type)-1);
		//服务状态
		$this->smarty->assign("service_state",$this->SERVICE_STATE);

		//客服服务(全部数据)
		$power_service_alldata = check_authz("kffwseralldata");
		$this->smarty->assign("power_service_alldata",$power_service_alldata?$power_service_alldata:0);

		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);

		$this->smarty->display('service_search_base.htm');
	}

	/**
	 * 客服服务 - 列表 - 高级搜索
	 *
	 */
	public function advance_search()
	{
		admin_priv();

		//角色类型
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		$user_session_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_session_id',$user_session_id);

		//服务类型
		$this->load->model("dictionary_model");
		$service_type = $this->dictionary_model->get_dictionary_detail(DICTIONARY_SERVICE_TYPE);
		$this->smarty->assign("service_type",$service_type);
		$this->smarty->assign("service_type_count",empty($service_type) ? 0 : count($service_type)-1);
		//服务状态
		$this->smarty->assign("service_state",$this->SERVICE_STATE);

		// 客服服务 - 自定义字段 选项信息
		$this->load->model("field_confirm_model");
		$_confirm_fields_info = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_SERVICE);
		$field_confirm = array();
		for($i=0;$i<count($_confirm_fields_info);$i++)
		{
			//可用的自定义字段
			$field_confirm[$_confirm_fields_info[$i]['id']] = $_confirm_fields_info[$i]['name'];
		}
		$this->smarty->assign("field_confirm",$field_confirm);
		$this->smarty->assign("field_confirm_selected",0);

		//高级搜索 逻辑条件
		$logical_condition = array('='=>'等于','!='=>'不等于','like'=>'包含','>'=>'大于','<'=>'小于','>='=>'大于等于','<='=>'小于等于');
		$this->smarty->assign('condition',$logical_condition);

		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);

		$this->smarty->display('service_search_advan.htm');
	}

	/**
	 * 业务受理 - 客服服务
	 *
	 */
	public function client_service()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		if (empty($cle_id))
		{
			die("缺少必要参数！");
		}
		$this->smarty->assign("service_cle_id",$cle_id);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);

		//客服服务(删除)
		$power_service_delete = check_authz("kffwserdelete");
		$this->smarty->assign("power_service_delete",$power_service_delete?$power_service_delete:0);

		//得到 客服服务 列表显示字段
		$this->load->model("datagrid_confirm_model");
		$serv_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_SERVICE);

		$this->smarty->assign("serv_display_field",$serv_display_field);

		$this->smarty->display("client_service.htm");
	}
}