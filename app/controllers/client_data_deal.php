<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 数据处理
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : client_data_deal.php
 * $Author: yhx
 * $time  : Mon Dec 24 16:16:19 CST 2012
*/
class Client_data_deal extends CI_Controller {

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
		//得到数据处理列表显示字段
		$this->load->model("datagrid_confirm_model");
		$cle_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_CONFIRM_CLIENT_DEAL);
		$this->smarty->assign("cle_display_field",$cle_display_field);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$this->smarty->display("client_data_deal.htm");
	}

	/**
	 * 查询结果处理
	 *
	 */
	public function deal_result()
	{
		//不需要处理的字段
		$not_used = array('cle_pingyin','user_id','dployment_num','dept_id','cle_executor_time','cle_creat_time','cle_creat_user_id','cle_update_time','cle_update_user_id','impt_id','cle_dial_number');

		//客户字段
		$this->load->model("field_confirm_model");
		$temp_field = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CLIENT);

		$field_info = array();
		if (!empty($temp_field))
		{
			foreach ($temp_field AS $value)
			{
				if (!empty($value["fields"])&&!in_array($value["fields"],$not_used))
				{
					$field_info[] = $value;
				}
			}
		}

		$this->smarty->assign("field_info",$field_info);

		$this->smarty->display("client_data_result.htm");
	}

	/**
	 * 客户管理 - 获取列表数据
	 *
	 */
	public function list_client_query()
	{
		$condition = $this->input->post();
		$condition['gl_all_data'] = true;
		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($condition);
		//检索条件
		$where = implode(" AND ",$wheres->wheres);

		//查重
		$repeat_condition = empty($condition["repeat_condition"]) ? 0 : $condition["repeat_condition"];

		$this->load->model("client_data_deal_model");
		list($page, $limit, $sort, $order) = get_list_param();
		if($where)
		{
			$responce = $this->client_data_deal_model->get_client_list($page, $limit, $sort, $order,$where,$repeat_condition);
		}
		else
		{
			$responce = new stdClass();
			$responce -> total = 0;
			$responce -> rows = array();
		}
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 一次性删重：将保持重复的第一条记录，重复的第二条记录后将被删除
	 *
	 */
	public function delete_all_repeat()
	{
		$inarray = $this->input->post();
		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($inarray);
		//检索条件
		$where = implode(" AND ",$wheres->wheres);

		//查重
		$repeat_condition = empty($inarray["repeat_condition"]) ? 0 : $inarray["repeat_condition"];
		if ( empty($repeat_condition) )
		{
			make_json_error("缺少查重条件！");
		}

		$this->load->model("client_data_deal_model");
		$result = $this->client_data_deal_model->delete_all_repeat($repeat_condition,$where);
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
	 * 查找替换
	 *
	 */
	public function find_replace()
	{
		$inarray = $this->input->post();
		//需要替换内容的字段
		$replace_field = empty($inarray["replace_field"]) ? "" : $inarray["replace_field"];
		//原字串
		$org_string    = empty($inarray["org_string"]) ? "" : $inarray["org_string"];
		//新字串
		$new_string    = empty($inarray["new_string"]) ? "" : $inarray["new_string"];
		if (  empty($replace_field) || ( empty($org_string) && empty($new_string))  )
		{
			make_json_error("缺少参数！");
		}

		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($inarray);
		//检索条件
		$where = implode(" AND ",$wheres->wheres);

		$this->load->model("client_data_deal_model");
		$result = $this->client_data_deal_model->find_replace($replace_field,$org_string,$new_string,$where);
		make_json_result($result);
	}

	/**
	 * 字段合并
	 *
	 */
	public function merger_confirm()
	{
		$inarray = $this->input->post("queryParams");

		$merger_result = $this->input->post("merger_result");
		$merger_one    = $this->input->post("merger_one");
		$merger_two    = $this->input->post("merger_two");
		$merger_three  = $this->input->post("merger_three");
		if ( empty($merger_result) || ( empty($merger_one)&&empty($merger_two)&&empty($merger_three) ) )
		{
			make_json_error("缺少参数！");
		}

		//检索条件
		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($inarray);
		$where = implode(" AND ",$wheres->wheres);

		//间隔字符
		$merger_interval = $this->input->post("merger_interval");
		if (!empty($merger_interval))
		{
			$merger_interval = $merger_interval;
		}

		$this->load->model("client_data_deal_model");
		$result = $this->client_data_deal_model->merger_confirm($merger_result,$merger_one,$merger_two,$merger_three,$merger_interval,$where);
		make_json_result($result);
	}

	/**
	 * 清空字段
	 *
	 */
	public function empty_field()
	{
		$inarray = $this->input->post("queryParams");

		$empty_filed = $this->input->post("empty_filed");
		if (empty($empty_filed))
		{
			make_json_error("请指定需要清空的字段！");
		}

		//检索条件
		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($inarray);
		$where = implode(" AND ",$wheres->wheres);

		$this->load->model("client_data_deal_model");
		$result = $this->client_data_deal_model->empty_field($empty_filed,$where);
		make_json_result($result);
	}

	/**
	 * 批量删除 列表数据
	 */
	public function delete_all_data()
	{
		$inarray = $this->input->post("queryParams");

		//检索条件
		$this->load->model("client_model");
		$wheres = $this->client_model->get_client_condition($inarray);
		$where = implode(" AND ",$wheres->wheres);

		$this->load->model("client_data_deal_model");
		$result = $this->client_data_deal_model->delete_all_data($where);
		make_json_result($result);
	}
}