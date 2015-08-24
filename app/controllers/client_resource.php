<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 客户调配
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : client_resource.php
 * $Author: yhx
 * $time  : Thu Nov 08 16:53:56 CST 2012
*/
class Client_resource extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Client_resource::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv('khglzytp');

		//得到 资源调配 列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT_RESOURCE);
		$this->smarty->assign("cle_display_field",$cle_display_field);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：客户调配（批量调配）
		$power_batch_deploy = check_authz("power_batch_deploy");
		$this->smarty->assign("power_batch_deploy",$power_batch_deploy?$power_batch_deploy:0);

		$this->smarty->display("client_resource.htm");
	}

	/**
	 * 客户调配 - 基础检索
	 *
	 */
	public function base_search()
	{
		$this->smarty->display("client_resource_search_base.htm");
	}

	private function translation_all_type($all_type = 0)
	{
		$condition = array();
		switch ($all_type) {
			case 2://未分配数据
			$condition["user_id"] = 0;
			break;
			case 3://未分配的新数据
			$condition["user_id"] = 0;
			$condition["cle_first_connecttime"] = "0000-00-00";
			$condition["dployment_num"] = "0";
			$condition['cle_public_type'] = 3;
			break;
			case 4://待再次分配的数据(放弃 或 收回了)
			$condition["assigned"] = true;
			$condition['user_id'] = 0;
			break;
			default:
				break;
		}

		return $condition;
	}
	/**
	 * 获取 客户调配 数据
	 *
	 */
	public function get_client_resource()
	{
		admin_priv('khglzytp');

		$condition = $this->input->post();
		//快捷检索
		if(!empty($condition['all_type']))
		{
			$condition_all_type = $this->translation_all_type($condition['all_type']);
			$condition = array_merge($condition,$condition_all_type);
			unset($condition['all_type']);
		}

		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_CONFIRM_CLIENT_RESOURCE);
		//数据
		$this->load->model("client_model");
		$responce = $this->client_model->get_client_list($condition,$select);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 客户分配 - 通过客户ID分配所属人
	 *
	 */
	public function deployment_client()
	{
		admin_priv();
		
		$dept_id = $this->session->userdata('dept_id');
		$this->load->model('department_model');
		$department_children_ids = $this->department_model->get_department_children_ids($dept_id);
		if(count($department_children_ids)>1)
		    $this->smarty->assign('deployment_dept',1);

		$this->smarty->display("client_select_deployment.htm");
	}

	/**
	 * 通过客户ID 调配客户
	 *
	 */
	public function deployment_by_id()
	{
		admin_priv();

        $cle_id   = $this->input->post("cle_id");
        $session_id = $this->session->userdata("user_id");

        if (empty($cle_id))
        {
            make_json_error("执行失败！");
        }
        $dept_id = $this->input->post("dept_id");
        $user_id = $this->input->post("user_id");

        $cle_id  = explode(",",$cle_id);
        $this->load->model("client_resource_model");
        $result = $this->client_resource_model->deployment_by_id($cle_id,$dept_id,$user_id);
        make_simple_response($result);
	}

	/**
	 * 批量分配 - 客户数据
	 *
	 */
	public function deployment_batch()
	{
		admin_priv('power_batch_deploy');

		$condition = $this->input->get();

		//数据权限 （获取部门id）
		$dept_session_id = $this->session->userdata('dept_id');
		$this->smarty->assign('dept_session_id',$dept_session_id);

		//搜索到的总数据
		$this->smarty->assign("total",$condition['total']);

		//处理快捷检索条件
		if(!empty($condition['all_type']))
		{
			$condition_all_type = $this->translation_all_type($condition['all_type']);
			$condition = array_merge($condition,$condition_all_type);
		}

		$this->load->model('client_resource_model');
		$batch_total = $this->client_resource_model->get_batch_total($condition);
		$this->smarty->assign('batch_total',$batch_total);

		//搜索条件
		$this->load->library("json");
		if(!empty($condition['field_confirm_values']))
		{
			$condition['field_confirm_values'] = $this->json->decode($condition['field_confirm_values'],1);
		}
		$search_condition = $this->json->encode($condition);
		$this->smarty->assign("search_condition",$search_condition);
		
		//判断号码状态字段是否启用
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$power_cle_stat = 0;
		if(!empty($client_base['cle_stat']))
		{
			$power_cle_stat = 1;
		}
		$this->smarty->assign('power_cle_stat',$power_cle_stat);

		$this->smarty->display("client_resource_batch.htm");
	}

	//批量分配 树
	public function deployment_batch_list()
	{
		admin_priv('power_batch_deploy',false);

		$this->load->model('client_resource_model');
		$dept_id = $this->input->post("id");
		$responce= $this->client_resource_model->deployment_batch($dept_id);

		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 批量分配  - 实际分配
	 *
	 */
	public function batch_deployment()
	{
		admin_priv('power_batch_deploy',false);

		$inarray = $this->input->post();
		//检索条件
		$search_condition = empty($inarray["search_condition"]) ? "" : $inarray["search_condition"];
		$this->load->library("json");
		$search_condition = $this->json->decode($search_condition,1);
		//分配总数
		$total_limit      = empty($inarray["total_limit"]) ? 0 : $inarray["total_limit"];
		//坐席 与 分配数量
		$assign_str       = empty($inarray["assign_str"]) ? "" : $inarray["assign_str"];
		//数据不分配给曾经占有过的坐席
		$special_deploy   = empty($inarray["special_deploy"])? 0 : $inarray["special_deploy"];

		//分配
		$this->load->model("client_resource_model");
		$result = $this->client_resource_model->batch_deployment($search_condition,$assign_str,$total_limit,$special_deploy);
		if ($result)
		{
			if($result == '-1')
			{
				$result = 0;
			}
			make_json_result($result);
		}
		else
		{
			make_json_error("执行失败!");
		}
	}

	/**
	 * 收回
	 */
	public function take_back_client()
	{
		admin_priv();

		$cle_id = $this->input->post("cle_id");
		if (empty($cle_id))
		{
			make_json_error("执行失败！");
		}
		$this->load->model("client_resource_model");
		$result = $this->client_resource_model->take_back_client($cle_id);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行失败！");
		}
	}

	/**
	 * 批量回收
	 */
	public function take_more_client_back()
	{
		admin_priv();

		$condition = $this->input->post();
		//处理快捷检索条件
		if(!empty($condition['all_type']))
		{
			$condition_all_type = $this->translation_all_type($condition['all_type']);
			$condition = array_merge($condition,$condition_all_type);
		}

		$this->load->model("client_resource_model");
		$result = $this->client_resource_model->take_more_client_back($condition);
		if ($result)
		{
			make_json_result($result);
		}
		else
		{
			make_json_error("执行失败！");
		}
	}
	
	/**
	 * 获取用户已有数据条数、还可分配条数
	 */
	public function get_this_user_batch()
	{
		$user_id = $this->input->post('user_id');
		if(empty($user_id))
		{
			make_json_error();
		}
		
		$this->load->model("client_resource_model");
		$result = $this->client_resource_model->get_user_batch_by_client_amount($user_id);
		make_simple_response($result);
	}
	
	/**
	 * 平均分配页面
	 */
	public function get_batch_chart_page()
	{
		$dept_id = $this->session->userdata('dept_id');
		$this->smarty->assign("dept_id",$dept_id);
		$this->smarty->display("client_resource_batch_chart.htm");
	}
}