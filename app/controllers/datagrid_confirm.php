<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 显示列表设置
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : datagrid_confirm.php 
 * $Author: 
 * $time  : 
*/
class Datagrid_confirm extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * datagrid_confirm::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv();
		
		//列表显示设置（该类型与est_fields表字段类型无关）    0客户管理   1联系人   2''   3资源调配  4数据处理 5产品  6订单  7客服服务
		$display_type = $this->input->get("display_type");
		$display_type = $display_type ? $display_type : 0;
		$this->smarty->assign("display_type",$display_type);

		//得到需要配置列表显示的字段
		$this->load->model("datagrid_confirm_model");
		$display_info = $this->datagrid_confirm_model->get_datagrid_confirm_fields($display_type);
		$this->smarty->assign("display_info",$display_info);

		$this->smarty->display("datagrid_confirm_info.htm");
	}

	/**
	 * 保存显示列表配置
	 *
	 */
	public function save_datagrid_info()
	{
		admin_priv();
		
		//更新列表配置信息
		$id               = $this->input->post("id");
		$id               = explode('###',rtrim($id,'###'));

		$if_display_value = $this->input->post("if_display_value");
		$if_display_value = explode('###',rtrim($if_display_value,'###'));

		$display_type     = $this->input->post("display_type");
		$display_type     = intval($display_type);

		$show_id = array();
		for($i=0;$i<count($if_display_value);$i++)
		{
			if($if_display_value[$i] == 1)
			{
				$show_id[] = $id[$i];
			}
		}
		if(empty($show_id))
		{
			make_json_error('至少要显示一个字段');
		}
		else
		{
			$show_fields_ids = implode(',',$show_id);
			$user_id = $this->session->userdata("user_id");
			$this->load->model("user_model");
			$result = $this->user_model->update_list_from_user($show_fields_ids,$user_id,$display_type);
			if ($result)
			{
				make_json_result(1);
			}
			else
			{
				make_json_error("操作成功");
			}
		}
	}
}