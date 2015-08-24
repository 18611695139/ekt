<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 客户管理
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : client.php 
 * $Author: 
 * $time  :
 * @property mixed client_model
 * @property Contact_model contact_model
 */

use Guzzle\Http as Httprequest;

class Client extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Client::index()
	 * 客户管理 - 客户列表
	 * @return void 
	 */
	public function index()
	{
		admin_priv('khglxygl');

		//权限：客户数据（添加）
		$power_client_add = check_authz("power_add");
		$this->smarty->assign("power_client_add",$power_client_add?$power_client_add:0);
		//权限：客户数据（删除）
		$power_client_delete = check_authz("power_delete");
		$this->smarty->assign("power_client_delete",$power_client_delete?$power_client_delete:0);
		//权限：客户数据（放弃）
		$power_client_release = check_authz("power_release");
		$this->smarty->assign("power_client_release",$power_client_release?$power_client_release:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：客户数据（导出）
		$power_client_export = check_authz("power_export");
		$this->smarty->assign("power_client_export",$power_client_export?$power_client_export:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		
		//统计的
		$statistics_date = $this->input->get('statistics_date');
		if(!empty($statistics_date))
		{
			$statistics_date = explode('~',$statistics_date);
			$this->smarty->assign('cle_stage_change_time_start',$statistics_date[0]);
			$this->smarty->assign('cle_stage_change_time_end',$statistics_date[1]);
		}
		$statistics_type = $this->input->get('statistics_type');
		if(!empty($statistics_type))
		$this->smarty->assign('statistics_type',$statistics_type);
		$cle_stage = $this->input->get('cle_stage');
		if(!empty($cle_stage))
		$this->smarty->assign('cle_stage',$cle_stage);
		$user_id = $this->input->get('user_id');
		if(!empty($user_id))
		$this->smarty->assign('user_id',$user_id);
		$dept_id = $this->input->get('dept_id');
		if(!empty($dept_id))
		$this->smarty->assign('dept_id',$dept_id);
		//今天回访客户
		$visit_type = $this->input->get('visit_type');
		$this->smarty->assign("visit_type",$visit_type);
		
		//得到客户管理列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT);
		$this->smarty->assign("cle_display_field",$cle_display_field);

		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action) || in_array('kffw',$action))
		{
			//获取系统配置参数
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			//删除客户时，相应数据处理 1不作处理 2一同删除
			$delete_client_relative = empty($config_info["delete_client_relative"]) ? 0 : $config_info["delete_client_relative"];
			$this->smarty->assign("delete_client_relative",$delete_client_relative);
		}
		$this->smarty->display("client_list.htm");
	}
	
	/**
	 * 我的客户
	 *
	 */
	public function my_client_list()
	{
		admin_priv();
		
		//权限：客户数据（添加）
		$power_client_add = check_authz("power_add");
		$this->smarty->assign("power_client_add",$power_client_add?$power_client_add:0);
		//权限：客户数据（删除）
		$power_client_delete = check_authz("power_delete");
		$this->smarty->assign("power_client_delete",$power_client_delete?$power_client_delete:0);
		//权限：客户数据（放弃）
		$power_client_release = check_authz("power_release");
		$this->smarty->assign("power_client_release",$power_client_release?$power_client_release:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：客户数据（导出）
		$power_client_export = check_authz("power_export");
		$this->smarty->assign("power_client_export",$power_client_export?$power_client_export:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		
		//得到客户管理列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT);
		$this->smarty->assign("cle_display_field",$cle_display_field);
		
		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('ddgl',$action) || in_array('kffw',$action))
		{
			//获取系统配置参数
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			//删除客户时，相应数据处理 1不作处理 2一同删除
			$delete_client_relative = empty($config_info["delete_client_relative"]) ? 0 : $config_info["delete_client_relative"];
			$this->smarty->assign("delete_client_relative",$delete_client_relative);
		}
		
		$user_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_id',$user_id);
		$this->smarty->display("client_own_list.htm");
	}

	/**
	 * 翻译快捷搜索的 visit_type
	 *
	 * @param int $visit_type
     * @return array
     */
	private function _translation_visit_type($visit_type)
	{
		$today = date("Y-m-d");
		$condition = array();
		switch ($visit_type) {
			case 1://最近一次联系时间 = 今天 ，  状态 = '呼通'
			$condition['cle_last_connecttime_start'] = $today;
			$condition['cle_last_connecttime_end'] = $today;
			$condition['cle_stat'] = '呼通';
			break;
			case 2://下次联系时间 = 今天
			$condition['con_rec_next_time_start'] =  $today;
			$condition['con_rec_next_time_end'] = $today;
			break;
			case 3:  // X天内要回访
			$three_days_later    = date("Y-m-d",strtotime("+3 day",strtotime($today)));
			$condition['con_rec_next_time_start'] = $today;
			$condition['con_rec_next_time_end'] = $three_days_later;
			break;
			case 4://过期未回访
			$one_month_before    = date("Y-m-d",strtotime("-1 month",strtotime($today)));
			$condition['con_rec_next_time_start'] = $one_month_before;
			$condition['con_rec_next_time_end'] = date("Y-m-d",strtotime("-1 day",strtotime($today)));
			break;
			default: break;
		}
		return $condition;
	}

	/**
	 * 翻译统计跳转客户列表的 statistics_type
	 *
	 * @param int $statistics_type 统计类型 2新添客户 3回访客户 4退化量 5客户阶段
	 * @param string $cle_stage 客户阶段
     * @return array
     * @author zgx
     */
	private function _translation_statistics_type($statistics_type=0,$cle_stage='')
	{
		$condition = array();
		if(empty($statistics_type))
		{
			return $condition;
		}
		switch($statistics_type)
		{
			case 1:
				$condition['cle_recede'] = 0;
				break;
			case 2: //2新添客户
			$condition['cle_recede'] = 0;
			$condition['cle_if_increment'] = 1;
			break;
			case 3: //3回访客户
			$condition['cle_recede'] = 0;
			$condition['cle_if_increment'] = 0;
			break;
			case 4: //4退化量
			$condition['cle_recede'] = 1;
			break;
			case 5: //5客户阶段
			$condition['cle_recede'] = 0;
			$condition['cle_stage'] = $cle_stage;
			break;
			default:
				break;
		}
		return $condition;
	}

	/**
	 * 清理条件
	 *
	 */
	private function _clean_condition($condition)
	{
		//快捷检索
		if(!empty($condition['visit_type']))
		{
			$condition_visit_type = $this->_translation_visit_type($condition['visit_type']);
			$condition = array_merge($condition,$condition_visit_type);
			unset($condition['visit_type']);
		}
		//统计
		if(!empty($condition['statistics_type']))
		{
			$condition_statistics_type = $this->_translation_statistics_type($condition['statistics_type'],$condition['cle_stage']);
			$condition = array_merge($condition,$condition_statistics_type);
			unset($condition['statistics_type']);
		}
		return $condition;
	}

	/**
	 * 客户管理 - 获取列表数据
	 *
	 */
	public function list_client_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$condition = $this->_clean_condition($condition);
		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_CONFIRM_CLIENT);
		//数据
		$this->load->model("client_model");
		$responce = $this->client_model->get_client_list($condition,$select);
		$this->load->library('json');
		echo $this->json->encode($responce);

	}

	/**
     * client::accept()
     *
     * @return bool
     */
	public function accept()
	{
		admin_priv();
		$cle_id    = $this->input->get('cle_id');
		if (empty($cle_id))
		{
			sys_msg("该客户不存在");
			return  false;
		}
		$this->smarty->assign('now_time',date('Y-m-d H:i:s'));
		$this->smarty->assign('now_date',date('Y-m-d'));

		$user_id = $this->session->userdata("user_id");
		$system_pagination = $this->input->get('system_pagination');
		$system_autocall = $this->input->get('system_autocall');
		$system_autocall_number = $this->input->get('system_autocall_number');

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：客户数据（修改）
		$power_client_update = check_authz("power_update");
		$this->smarty->assign("power_client_update",$power_client_update?$power_client_update:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：客户电话修改权限
		$power_update_c_phone = check_authz("power_update_c_phone");
		$this->smarty->assign("power_update_c_phone",$power_update_c_phone?$power_update_c_phone:0);

		$role_action = $this->session->userdata('role_action');
		$action = explode(',',$role_action);
		if(in_array('kffw',$action))
		{
			//客服服务(查看)
			$power_service_view = check_authz("kffwserview");
			$this->smarty->assign("power_service_view",$power_service_view?$power_service_view:0);
		}
		if(in_array('ddgl',$action))
		{
			//订单信息(查看)
			$power_client_order = check_authz("power_client_order");
			$this->smarty->assign("power_client_order",$power_client_order?$power_client_order:0);
		}
		if(in_array('gdgl',$action))
		{
			//工单权限
			$power_work_flow = check_authz("work_flow_client");
			$this->smarty->assign("power_work_flow",$power_work_flow?$power_work_flow:0);
		}

		//得到客户信息
		$this->load->model("client_model");
		$client_info = $this->client_model->get_client_info($cle_id);
		$cle_user_id = empty($client_info["user_id"]) ? 0 : $client_info["user_id"];
		$cle_dept_id = empty($client_info["dept_id"]) ? 0 : $client_info["dept_id"];
		//权限：客户数据（放弃） 坐席只能释放属于自己的数据
		$power_client_release = check_authz("power_release");
		if($power_client_release)
		{
			if ($user_id != $cle_user_id )
			{
				$power_client_release = 0;
			}
		}
		$this->smarty->assign("power_client_release",$power_client_release?$power_client_release:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();

		//判断处理权限
		$client_permission = $this->client_model->check_client_permission($cle_id,$cle_user_id,$cle_dept_id);
		if (!$client_permission)
		{
			// 来电并且配置了可处理其他人数据的权限则可绕过权限限制
			$callin = $this->input->get("callin");
			$deal_other_client = empty($config_info["deal_other_client"]) ? 1 : $config_info["deal_other_client"];	// 处理非本人数据的来电  1是  2否
			if(!$callin || $deal_other_client != 1)
			{
				$this->smarty->assign("client_info",$client_info);
				$this->smarty->display("client_callin_brief.htm");
				return false;
			}
			//来电处理时非当前坐席打开页面时自动给原坐席发消息
			if($callin && $deal_other_client==1 && $cle_user_id != $user_id)
			{
				$this->load->model('user_model');
				$user_info = $this->user_model->get_user_info($user_id);
				$this->load->model('notice_model');
				$this->notice_model->write_notice('system',$cle_user_id,'',$client_info["cle_name"].'&nbsp;['.$client_info["cle_phone"].']来电已被'.empty($user_info['user_name'])?'':$user_info['user_name'].'处理');
			}
		}
		//过滤电话号码重复： 0不过滤，允许重复；1不允许重复，过滤号码
		$phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
		$this->smarty->assign("phone_ifrepeat",$phone_ifrepeat);
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		if($power_use_contact!=1)
		{
			//得到联系人信息
			$this->load->model("contact_model");
			$cle_contact_info = $this->contact_model->get_contact_by_cle_id($cle_id,20);
			$this->smarty->assign("cle_contact_info",$cle_contact_info ? $cle_contact_info : "");
		}
		//显示 : 上一条/下一条  ， 保存自动取下一条
		$system_pagination  = empty($system_pagination) ? 0 : $system_pagination;
		$this->smarty->assign("system_pagination",$system_pagination);
		//自动外呼
		$system_autocall = empty($system_autocall) ? 0 : $system_autocall;
		$config_call_type = isset($config_info['call_type'])?$config_info['call_type']:0;
		if($config_call_type==1)//系统呼入类型 0呼入呼出 1呼入 2呼出
		{
			$system_autocall = 0;
		}
		$this->smarty->assign("config_call_type",empty($config_info['call_type'])?0:$config_info['call_type']);
		$system_autocall_number = empty($system_autocall_number) ? $client_info['cle_phone'] : $system_autocall_number ;
		$this->smarty->assign("system_autocall",$system_autocall);
		$this->smarty->assign("system_autocall_number",$system_autocall_number);
		//客户管理页面跳转业务受理的数据，取得cookie中的客户ID
		$row_limit   = 0 ;
		$row_index    = 0 ;//数据位置
		if ($system_pagination)
		{
			$last_next_cleID = $this->input->cookie("last_next_cleID");
			$last_next_cleID = explode(",",$last_next_cleID);
			$row_limit       = count($last_next_cleID);

			//查找当前ID所处位置
			$row_index = array_search($cle_id,$last_next_cleID);
			if ($row_index >= 0)
			{
				$row_index++;
			}
		}
		//数据位置
		$this->smarty->assign("row_limit",$row_limit);
		$this->smarty->assign("row_index",$row_index);
		//---------------------------------------------------------------------------------------
		//得到客户可编辑的字段（基本字段 + 自定义字段）
		$can_update_fields = $this->client_model->get_update_available_fields();
		$client_base = array(); //基本字段名
		$client_confirm = array();//自定义字段信息
		$parent_id = array();//下拉选项自定义field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义字段fields
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($can_update_fields as $field_info)
		{
			if($field_info['if_custom']==1)
			{
				if($field_info['data_type']==DATA_TYPE_SELECT)
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL )
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
					$field_info["jl_field_type"] = $this->json->decode($field_info["jl_field_type"],1);
					$jl_field_type[$field_info["id"]] = $field_info["jl_field_type"];
				}
				else if($field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$checkbox_id[]=$field_info['id'];
					if(!empty($client_info[$field_info["fields"].'_1']))
					{
						$check_1 = explode(',',$client_info[$field_info["fields"].'_1']);
						foreach($check_1 as $value)
						{
							$client_info[$field_info["id"].'_1'][$value] = $value;
						}
					}
					if(!empty($client_info[$field_info["fields"].'_2']))
					{
						$check_2 = explode(',',$client_info[$field_info["fields"].'_2']);
						foreach($check_2 as $value)
						{
							$client_info[$field_info["id"].'_2'][$value] = $value;
						}
					}
				}
				$client_confirm[] = $field_info;
			}
			else
			{
				$client_base[$field_info['fields']] = $field_info['fields'];
			}
		}

		$this->smarty->assign("client_base",$client_base);
		$this->smarty->assign("client_confirm",$client_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));


		//========自定义字段============
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
			}
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义选项
		}
		$this->smarty->assign("jl_options",$jl_options);

		$checkbox_options = array();//级联自定义选
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);//级联自定义选项
		}
		$this->smarty->assign("checkbox_options",$checkbox_options);

		//=======基本字段=============
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

		$this->smarty->assign("client_info",$client_info);
		$this->smarty->display("client_accept.htm");
	}

	/**
	 * 业务受理 - 其他信息
	 *
	 */
	public function client_other_message()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		if (!$cle_id)
		{
			die("缺少参数！");
		}

		//得到客户信息
		$this->load->model("client_model");
		$client_info = $this->client_model->get_client_info($cle_id);
		$this->smarty->assign("client_info",$client_info);

		//得到相关日志
		$this->load->model("log_model");
		$result_log = $this->log_model->get_cle_log($cle_id);
		$this->load->library("json");
		$result_log = $this->json->encode($result_log);
		$this->smarty->assign("result_log",$result_log);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		$this->smarty->display("client_other_message.htm");
	}

	/**
	 * 外呼弹屏/来电弹屏/业务受理 - 搜索，得到符合条件的客户ID
	 */
	public function search_client()
	{
		admin_priv();
		$cle_ids = '';

		$name = $this->input->get('name');
		$phone = $this->input->get('phone');


		//$callin 队列来电
		$callin = $this->input->get("callin");

		if (  !empty($name) ||  !empty($phone) )
		{
			if(!empty($phone))
			{
				//去除手机号前面的0 - 处理号码
				$this->load->model('phone_location_model');
				$phone = $this->phone_location_model->remove_prefix_zero($phone);
			}
			$this->load->model("client_model");
			$cle_ids = $this->client_model->search_client_by_name_phone($name,$phone,true);
		}


		$count = count($cle_ids);
		if(!empty($cle_ids)&&$count == 1)
		{
			$cle_id = $cle_ids[0];
			est_header("Location:index.php?c=client&m=accept&callin=$callin&cle_id=".$cle_id);
		}
		elseif ($count > 1)
		{
			est_header("Location:index.php?c=client&m=client_select&callin=$callin&name=".$name."&phone=".$phone);
		}
		else
		{
			$this->load->model("system_config_model");
			$config_info = $this->system_config_model->get_system_config();
			$config_call_type = isset($config_info['call_type'])?$config_info['call_type']:0;
			if($config_call_type==1)
			{
				$this->auto_insert_client($phone,$callin);
			}
			else
			{
				//添加新客户
				est_header("Location:index.php?c=client&m=new_client&cle_phone=".$phone);
			}
		}
	}

	/**
	 * 添加客户信息
	 *
	 */
	public function new_client()
	{
		admin_priv('power_add');
		$this->smarty->assign('now_time',date('Y-m-d H:i:s'));
		$this->smarty->assign('now_date',date('Y-m-d'));

		//外呼弹屏
		$cle_phone = $this->input->get("cle_phone");
		$this->smarty->assign("cle_phone",$cle_phone);

		//队列来电 - 检索客户信息 - 客户信息不存在 - 添加新客户
		$incoming_service = $this->input->get("incoming_service");
		$this->smarty->assign("incoming_service",$incoming_service);

		//权限：自定义字段
		$power_fieldsconfirm       = check_authz("xtglzdpz");
		$this->smarty->assign("power_fieldsconfirm",$power_fieldsconfirm?$power_fieldsconfirm:0);
		//权限：数字字典
		$power_dictionary       = check_authz("xtglszzd");
		$this->smarty->assign("power_dictionary",$power_dictionary?$power_dictionary:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		// 过滤电话号码重复： 0不过滤，允许重复；1不允许重复，过滤号码
		$phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
		$this->smarty->assign("phone_ifrepeat",$phone_ifrepeat);
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		//------------------------------------------------------
		//得到可用的字段（基本字段 + 自定义字段）
		$this->load->model("field_confirm_model");
		$this->load->model("dictionary_model");
		$field_type = $power_use_contact?FIELD_TYPE_CLIENT:FIELD_TYPE_CLIENT_CONTACT;
		$fields = $this->field_confirm_model->get_available_fields($field_type);
		$client_base = array();//客户 基本字段
		$contact_base = array();//联系人 基本字段
		$client_confirm = array();//客户 自定义字段
		$contact_confirm = array();//联系人 自定义字段
		$parent_id = array();//下拉选项field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach($fields as $field_info)
		{
			if($field_info['if_custom']==0)
			{
				if($field_info['field_type']==0)
				$client_base[$field_info['fields']] = $field_info['fields'];
				else
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

				if($field_info['field_type']==0)
				$client_confirm[] = $field_info;
				else
				$contact_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("client_base",$client_base);
		$this->smarty->assign("contact_base",$contact_base);
		$this->smarty->assign("client_confirm",$client_confirm);
		$this->smarty->assign("contact_confirm",$contact_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));

		//自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		$jl_options = array();//自定义字段 级联
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);
		}
		$this->smarty->assign("jl_options",$jl_options);
		$checkbox_options = array();//关联多选框所有选项信息
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);
		}
		$this->smarty->assign("checkbox_options",$checkbox_options);

		//信息来源
		if(in_array('cle_info_source',$client_base))
		{
			$cle_info_source = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_INFO);
			$this->smarty->assign("cle_info_source",$cle_info_source);
		}
		//获取所有省信息
		if(in_array('cle_province_name',$client_base))
		{
			$this->load->model('regions_model');
			$regions_info = $this->regions_model->get_regions(REGION_PROVINCE,1);//深度、父id
			$this->smarty->assign("regions_info",$regions_info);
		}
		//号码状态
		if(in_array('cle_stat',$client_base))
		{
			$client_state_all =$this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STATE);
			$this->smarty->assign("client_state",$client_state_all);
		}
		//客户阶段
		if(in_array('cle_stage',$client_base))
		{
			$cle_stage =  $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
			$this->smarty->assign("cle_stage",$cle_stage);
		}
		$this->smarty->display("client_info.htm");
	}

	/**
	 * 保存一条新客户信息
	 *
	 */
	public function insert_client()
	{
		admin_priv('power_add',false);

		$this->load->model("client_model");
		$user_id = $this->session->userdata('user_id');
		//判断是否超过客户限制
		$check_amount_result = $this->client_model->check_client_amount($user_id);
		if($check_amount_result)
		{
			$inarray = $this->input->post();
			foreach($inarray as $key=>$value)
			{
				//号码重复 判断
				if(($key=='cle_phone' || $key=='cle_phone2' || $key=='cle_phone3') && !empty($inarray[$key]))
				{
					//去除手机号前面的0 - 处理号码
					$this->load->model('phone_location_model');
					$inarray['cle_phone'] = $this->phone_location_model->remove_prefix_zero($inarray[$key]);
					//获取系统配置参数
					$this->load->model("system_config_model");
					$config_info = $this->system_config_model->get_system_config();
					//过滤电话号码重复： 0不过滤，允许重复；1不允许重复，过滤号码
					$phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
					if ($phone_ifrepeat == 1 && $inarray[$key] )
					{
						$cle_ids = $this->client_model->search_client_by_name_phone("",$inarray[$key],true);
						if (!empty($cle_ids))
						{
							make_json_error("添加失败！系统已有该电话号码");
							return;
						}
					}
				}
			}
			$result = $this->client_model->insert_client($inarray);
			if ($result)
			{
				make_json_result($result);
			}
			else
			{
				make_json_error("添加失败！");
			}
		}
		else
		{
			make_json_error("您当前客户数量已达或超过客户限制数量，添加失败！");
		}
	}

	/**
	 * 更新客户信息
	 *
	 */
	public function update_client()
	{
		admin_priv('power_update');

		$inarray = $this->input->post();
		$cle_id  = empty($inarray["cle_id"]) ? 0 : $inarray["cle_id"];
		if (!$cle_id)
		{
			make_json_error("缺少客户参数！");
		}

		//更新客户信息
		$this->load->model("client_model");
		$result = $this->client_model->update_client($cle_id,$inarray);
		if ($result == 1)
		{
			make_json_result(1);
		}
		elseif ($result == 2 )
		{
			make_json_error("客户电话重复！");
		}
		else
		{
			make_json_error("数据更新失败");
		}
	}

	/**
	 * 删除客户数据
	 *
	 */
	public function delete_client()
	{
		admin_priv('power_delete');
		$cle_id_str = $this->input->post("cle_id");
		if (empty($cle_id_str))
		{
			make_json_error("缺少参数");
		}
		$cle_ids = explode(',',$cle_id_str);


		//删除
		$this->load->model("client_model");
		$result = $this->client_model->delete_client($cle_ids);
		if($result)
		{
			make_json_result('删除成功');
		}
		else
		{
			make_json_error();
		}
	}

	/**
	 * 放弃客户数据 - 通过客户ID(cle_id)
	 *
	 */
	public function release_client_by_id()
	{
		admin_priv('power_release');

		$cle_id = $this->input->post("cle_id");
		if ($cle_id == ""  )
		{
			make_json_result(0);
		}

		//放弃数据
		$this->load->model("client_model");
		$result = $this->client_model->release_client($cle_id);
		make_simple_response($result);
	}

	/**
	 * 基本搜索
	 */
	public function base_search()
	{
		admin_priv();
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		//客户阶段
		//		$this->load->model('dictionary_model');
		//		$cle_stage =  $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		//		$this->smarty->assign("cle_stage",$cle_stage);
		//判断号码状态字段是否启用
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$power_cle_stat = 0;
		if(!empty($client_base['cle_stat']))
		{

			$power_cle_stat = 1;
		}
		$this->smarty->assign('power_cle_stat',$power_cle_stat);
		
		// flag 模块标记：manage客户管理（默认），my_client我的客户
		$flag      = $this->input->get("flag");
		$flag      = empty($flag) ? "manage" : $flag;
		$this->smarty->assign("flag",$flag);
		
		$this->smarty->assign('user_session_id',$this->session->userdata('user_id'));

		$this->smarty->display('client_search_base.htm');
	}
	/**
	 * 高级搜索
	 */
	public function advance_search()
	{
		admin_priv();

		// flag 模块标记：manage客户管理（默认），my_client我的客户,data_deal数据处理，history历史客户，public公共客户，resource客户调配
		$flag      = $this->input->get("flag");
		$flag      = empty($flag) ? "manage" : $flag;
		$this->smarty->assign("control_flag","$flag");
		//角色类型
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		$user_session_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_session_id',$user_session_id);
		$dept_session_id = $this->session->userdata('dept_id');
		$this->smarty->assign('dept_session_id',$dept_session_id);
		//高级搜索 逻辑条件
		$logical_condition = array('='=>'等于','!='=>'不等于','like'=>'包含','>'=>'大于','<'=>'小于','>='=>'大于等于','<='=>'小于等于');
		$this->smarty->assign('condition',$logical_condition);
		//权限：公共（全部）
		$power_public_all = check_authz("khglggkeqb");
		$this->smarty->assign("power_public_all",$power_public_all?$power_public_all:0);

		//得到客户可编辑的字段（基本字段 + 自定义字段）
		$this->load->model("client_model");
		$can_update_fields = $this->client_model->get_update_available_fields();
		$client_base = array(); //基本字段名
		$field_confirm = array();//自定义
		foreach($can_update_fields as $field_info)
		{
			if($field_info['if_custom']==1)//自定义
			{
				$field_confirm[$field_info['id']] = $field_info['name'];
			}
			else//基本
			{
				$client_base[$field_info['fields']] = $field_info['fields'];
			}
		}
		$this->smarty->assign("client_base",$client_base);

		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		if($power_use_contact!=1)//联系人自定义
		{
			if (  $flag != "data_deal" )
			{
				$contact_confirm_fields_info = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_CONTACT);
				foreach($contact_confirm_fields_info as $contact)
				{
					$field_confirm[$contact['id']] = $contact['name'];
				}
			}
		}
		$this->smarty->assign("field_confirm",$field_confirm);
		$this->smarty->assign("field_confirm_selected",0);
		//客户阶段
		$this->load->model("dictionary_model");
		$client_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
		$_stage = array();
		for($i=0;$i<count($client_stage);$i++)
		{
			$_stage[] = $client_stage[$i]['name'];
		}
		$this->smarty->assign("_stage",$_stage);
		//信息来源
		if(in_array('cle_info_source',$client_base))
		{
			$cle_info_source = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_INFO);
			$this->smarty->assign("cle_info_source",$cle_info_source);
		}
		//获取所有省信息
		if(in_array('cle_province_name',$client_base))
		{
			$this->load->model('regions_model');
			$regions_info = $this->regions_model->get_regions(REGION_PROVINCE,1);//深度、父id
			$this->smarty->assign("regions_info",$regions_info);
		}
		//号码状态
		if(in_array('cle_stat',$client_base))
		{
			$client_state_all = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STATE);
			$_state = array();
			for($i=0;$i<count($client_state_all);$i++)
			{
				$_state[] = $client_state_all[$i]['name'];
			}
			$this->smarty->assign("client_state",$_state);
		}
		//是否有无所属人检索 1检索 0不检索
		$no_user_search = 1;
		if($flag=='my_client'||$flag=='public')
		$no_user_search = 0;
		$this->smarty->assign('no_user_search',$no_user_search);
		
		global $CLIENT_PUBLIC_TYPE;
		$this->smarty->assign('client_public_type',$CLIENT_PUBLIC_TYPE);
		$this->smarty->display('client_search_advan.htm');
	}

	/**
	 * 导出
	 */
	public function data_output()
	{
		admin_priv('power_export');

		set_time_limit(0);
		@ini_set('memory_limit', '1024M');
		//导出信息
		$data_info = array();

		//转换检索条件
		$condition = $this->input->get();
		$condition = $this->_clean_condition($condition);

		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_CONFIRM_CLIENT);
		//数据
		$total     = empty($condition["total"]) ? 0 : $condition["total"];
		$sortName  = empty($condition["sortName"]) ? "cle_id" : $condition["sortName"];
		$sortOrder = empty($condition["sortOrder"]) ? "DESC" : $condition["sortOrder"];
		$this->load->model("client_model");
		$responce = $this->client_model->get_client_list($condition,$select,1,$total,$sortName,$sortOrder);
		$client_info = $responce->rows;

		$this->load->model('datagrid_confirm_model');
		$field_info = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT);
		$field = array();
        $fields = array();
		foreach($field_info AS $value)
		{
            $fields[$value['fields']] = $value['name'];
			$field[] = $value['fields'];
		}

		for($i=0,$k=0;$i<count($client_info);$i++,$k++)
		{
			for($j=0;$j<count($field);$j++)
			{
				$data_info[$i][$field[$j]] = str_replace("\r", ' ', $client_info[$k][$field[$j]]);
				$data_info[$i][$field[$j]] = str_replace("\n", ' ', $data_info[$i][$field[$j]]);
			}
		}

        $export_type = $this->input->get('export_type');
        switch ($export_type) {
            case 'csv':
                array_unshift($data_info,$fields);
		        $this->load->library("csv");
		        $this->csv->creatcsv('客户数据' . date("YmdHis"),$data_info);
                break;
            case 'excel':
                $this->load->model('excel_export_model');
                $this->excel_export_model->export($data_info, $fields, '客户数据' . date("YmdHis"));
                break;
            default:
                sys_msg("操作失败");
                break;
        }
	}

	/**
	 * 添加/编辑客户信息时，检测客户姓名/电话是否重复
	 *
	 */
	public function check_repeat_data()
	{
		admin_priv();

		$name  = $this->input->post("name");
		$phone = $this->input->post("phone");
		$cle_id    = $this->input->post("cle_id");
		$cle_id    = empty($cle_id) ? 0 : $cle_id;
		if(strlen($phone)<6)//电话长度小于6位不用查重
		{
			$phone = '';
		}
		if (empty($name) && empty($phone))
		{
			make_json_error("数据为空，不需要检查！");
		}
		if(!empty($phone))
		{
			//去除手机号前面的0 - 处理号码
			$this->load->model('phone_location_model');
			$phone = $this->phone_location_model->remove_prefix_zero($phone);
		}

		//检查
		$this->load->model("client_model");
		$cle_ids = $this->client_model->search_client_by_name_phone($name,$phone,true);
		$cle_more = array_diff($cle_ids,array($cle_id));
		if(empty($cle_more))
		{
			make_json_error();
		}
		else
		{
			if(!empty($phone))
			{
				//判断是否启用历史信息
				$this->load->model('client_history_model');
				$move_to_history_cle_id = $this->client_history_model->move_client_to_history($cle_more);
				if(!empty($move_to_history_cle_id))
				{
					$cle_more = array_diff($cle_more,$move_to_history_cle_id);
					if(empty($cle_more))
					{
						make_json_error();
					}
					else
					{
						make_json_result(implode(',',$cle_more));
					}
				}
				else
				{
					make_json_result(implode(',',$cle_more));
				}
			}
			else
			{
				make_json_result(implode(',',$cle_more));
			}
		}
	}

	/**
	 * 编辑客户数据时，验证重复
	 *
	 */
	public function display_repeat_data()
	{
		admin_priv();

		$name = $this->input->get("name");
		$phone = $this->input->get("phone");
		$cle_id    = $this->input->get("cle_id");
		$cle_id    = empty($cle_id) ? 0 : $cle_id;

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		$this->smarty->assign("power_use_contact",empty($config_info["use_contact"]) ? 0 : $config_info["use_contact"]);//是否使用联系人模块 0用 1不用
		//客户基本可用字段信息
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$this->smarty->assign("client_base",$client_base);

		if(strlen($phone)<6)//电话长度小于6位不用查重
		{
			$phone = '';
		}
		if (empty($name) && empty($phone))
		{
			make_json_error("数据为空，不需要检查！");
		}

		$this->smarty->assign("name",$name);
		$this->smarty->assign("phone",$phone);
		$this->smarty->assign("cle_id",$cle_id);
		$this->smarty->display("client_repeat_data.htm");
	}

	/**
	 * 添加/编辑客户信息，过滤重复数据
	 *
	 */
	public function list_repeat_client_query()
	{
		admin_priv();

		$name = $this->input->post("name");
		$phone = $this->input->post("phone");
		if(!empty($phone))
		{
			//去除手机号前面的0 - 处理号码
			$this->load->model('phone_location_model');
			$phone = $this->phone_location_model->remove_prefix_zero($phone);
		}

		$cle_id    = $this->input->post("cle_id");
		$cle_id    = empty($cle_id) ? 0 : $cle_id;

		$condition = array('name'=>$name,'phone'=>$phone,'remove_cle_id'=>$cle_id,'gl_all_data'=>true);
		$select  =array("est_client.cle_id","cle_name","cle_phone","cle_phone2","cle_phone3","cle_stage","cle_last_connecttime","dept_id","user_id");
		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];//是否使用联系人模块
		if($power_use_contact!=1)
		{
			$select[] = 'con_name';
			$select[] = 'con_mobile';
		}
		//获取客户信息
		$this->load->model('client_model');
		$responce = $this->client_model->get_client_list($condition,$select,1,10,'cle_id','desc');
		$this->load->library("json");
		echo $this->json->encode($responce);
	}

	/**
	 * 来电/业务受理 - 根据条件，得到多个客户信息能与条件匹配，请选择正确的数据进行处理
	 *
	 */
	public function client_select()
	{
		admin_priv();

		$name = $this->input->get("name");
		$phone = $this->input->get("phone");

		//队列来电弹屏
		$callin = $this->input->get("callin");
		$this->smarty->assign("callin",$callin);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		$this->smarty->assign("name",$name);
		$this->smarty->assign("phone",$phone);

		$this->smarty->display("client_select.htm");
	}

	/**
	 * 翻页 返回下（上）一页的客户ids 
	 * 返回 客户id 用逗号分隔的字符串
	 */
	public function trun_page()
	{
		$pre_or_next = $this->input->post("pre_or_next");
		if (empty($pre_or_next))
		{
			return false;
		}
		$client_list_param = $this->input->cookie("client_list_param");
		if(empty($client_list_param))
		{
			make_json_error("数据不存在！");
		}

		//客户列表参数
		$client_list_param = explode(",",$client_list_param);
		$page = $client_list_param[0];
		$limit = $client_list_param[1];
		$sort = $client_list_param[2];
		$order = $client_list_param[3];
		if ($pre_or_next == 'pre')//上一页
		{
			$page = $page -1;
		}
		else if($pre_or_next == 'next')//下一页
		{
			$page = $page +1;
		}

		//检索条件
		$where = $this->input->cookie('accept_condition');
		$where = unescape($where);
		$this->load->library('json');
		$condition = $this->json->decode($where,1);
		$condition = $this->_clean_condition($condition);

		//获取数据
		$this->load->model("client_model");
		$responce = $this->client_model->get_client_list($condition,array('cle_id'),$page,$limit,$sort,$order);

		$cle_ids = array();
		foreach ($responce->rows AS $value)
		{
			if (!empty($value["cle_id"]) )
			{
				$cle_ids[] = $value["cle_id"];
			}
		}

		if (!empty($cle_ids) )
		{
			$params = array("last_next_cleID"=>implode(",",$cle_ids),"client_list_param"=>$page.",".$limit.",".$sort.",".$order);
			make_json_result('','',$params);
		}
		else
		{
			make_json_error("数据不存在！");
		}
	}

	/**
	 *  获取 选择客户 页面
	 */
	public function select_clients()
	{
		admin_priv();

		$phone = $this->input->get("phone");
		$this->smarty->assign("phone",$phone);

		$user_id = $this->session->userdata('user_id');
		$this->smarty->assign("user_id",$user_id);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		
		$type = $this->input->get('type');
		$this->smarty->assign('type',$type?'_'.$type:'');

		$this->smarty->display("client_select_list.htm");
	}

	/*判断客户是否存在*/
	public function client_exist()
	{
		admin_priv();

		$cle_id = $this->input->post('cle_id');
		if(!$cle_id)
		{
			make_json_error('缺少参数');
		}
		$this->load->model("client_model");
		$client_info = $this->client_model->get_client_info($cle_id);
		if($client_info)
		make_json_result(1);
		else
		make_json_error('该客户已被删除');
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
		$this->load->model("client_model");
		$client_info = $this->client_model->get_client_info($cle_id);
		//---------------------------------------------------------------------------------------
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

	/**
	 *  呼入类型客户- 搜索，得到符合条件的客户ID (若来电电话不存在于系统，系统自动添加一条信息后返回受理页面)
	 */
	public function auto_insert_client($phone,$callin)
	{
		admin_priv();

		//系统自动添加客户
		if(!empty($phone))
		{
			$insert = array('cle_phone'=>$phone);
			//获取客户电话归属地
			if(strlen($phone) == 7 || strlen($phone) == 8)
			{
				$this->load->config('myconfig');
				$local_code = $this->config->item('local_code');
				$phone = $local_code.$phone;
			}

            $this->config->load('myconfig');
            $api = $this->config->item('api_wintelapi');
            $client = new Httprequest\Client();

            $request = $client->get($api.'/api/common/getnumberloc/'.$phone);
            $response = $request->send()->json();
            if (json_last_error() !== JSON_ERROR_NONE) {
                return array(
                    'signin' => false,
                    'msg' => '解析结果出错，错误为【'.json_last_error().'】'
                );
            }
            $code    = isset($response['code']) ? $response['code'] : 0;
            $message = isset($response['message']) ? $response['message'] : '';
            $data    = isset($response['data']) ? $response['data'] : array();

            if ($code == 200) {
                if(isset($data['city']))
                {
                    $province_city = explode(' ',$data['city']);
                    $this->load->model('regions_model');
                    //省
                    $cle_province = $this->regions_model->get_region_id_by_name($province_city[0],1);
                    $insert['cle_province_id'] = $cle_province[$province_city[0]];
                    $insert['cle_province_name'] = $province_city[0];
                    //市
                    $cle_city = $this->regions_model->get_region_id_by_name($province_city[1],2);
                    $insert['cle_city_id'] = $cle_city[$province_city[1]];
                    $insert['cle_city_name'] = $province_city[1];
                }
            }

			$this->load->model('client_model');
			$cle_id = $this->client_model->insert_client($insert);
			if(!empty($cle_id))
			{
				est_header("Location:index.php?c=client&m=accept&callin=".$callin."&cle_id=".$cle_id);
			}
			else
			{
				$link[0]["text"] = "返回添加客户";
				$link[0]["href"] = "index.php?c=client&m=new_client&cle_phone=".$phone;
				sys_msg("系统自动添加客户失败，请手动添加客户", 0, $link);
			}
		}
		else
		{
			$link[0]["text"] = "返回添加客户";
			$link[0]["href"] = "index.php?c=client&m=new_client";
			sys_msg("号码为空，请手动添加客户", 0, $link);
		}
	}
}