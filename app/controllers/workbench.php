<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 工作桌面
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : workbench.php 
 * $Author: yhx
 * $time  : Mon Jul 30 12:04:34 CST 2012
*/
class Workbench extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Workbench::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv();

		$role_type = $this->session->userdata("role_type");
		$this->smarty->assign('role_type',$role_type);
		$dept_id = $this->session->userdata("dept_id");
		$user_id = $this->session->userdata("user_id");
		$this->smarty->assign('user_session_id',$user_id);
		$this->smarty->assign('dept_session_id',$dept_id);
		//提醒
		$this->load->model("remind_model");
		$remind = $this->remind_model->remind_workbench();
		$this->smarty -> assign("action",$remind);

		//消息
		$this->load->model("message_model");
		$messages = $this->message_model->msg_workbench();
		$this->smarty -> assign("message",$messages);

		//公告
		$this->load->model("announcement_model");
		$anns = $this->announcement_model->anns_workbench();
		$this->smarty->assign("anns_info",$anns);
		//权限：公告（编辑）
		$power_announcement_change       = check_authz("wdzsggbj");
		$this->smarty->assign("power_announcement_change",$power_announcement_change?$power_announcement_change:0);
		//权限：公告管理
		$power_announcement_more      = check_authz("wdzsgggl");
		$this->smarty->assign("power_announcement_more",$power_announcement_more?$power_announcement_more:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		//统计
		$this->load->model('statistics_model');
		if($role_type == DATA_PERSON)
		{
			//坐席
			$statistics = $this->statistics_model->statistics_workbench();
			$this->smarty->assign('stat_title','今天我的通话情况');
			$this->smarty->assign('statistics',$statistics);
		}
		else
		{
			$statistics = $this->statistics_model->statistics_dept_workbench();
			$this->smarty->assign('stat_title','今天坐席通话情况');
			$this->smarty->assign('statistics',$statistics);
		}

		//今天要回访客户信息
		$today = date("Y-m-d");
		$revisit_condition = array('con_rec_next_time_start'=>$today,'con_rec_next_time_end'=>$today,'user_id'=>$user_id);
		$revisit_select = array('cle_id','cle_name','cle_stat','cle_stage','cle_last_connecttime');
		$this->load->model("client_model");
		$responce = $this->client_model->get_client_list($revisit_condition,$revisit_select,1,10,'con_rec_next_time','desc');
		$revisit_client_info = $responce->rows;
		$this->smarty->assign('revisit_client_info',$revisit_client_info);

		//我的未接来电
		$this->load->model('missed_calls_model');
		$my_misscall_info = $this->missed_calls_model->my_miss_calls_workbench();
		$this->smarty->assign('my_misscall_info',$my_misscall_info);

		//工作桌面DIV布局 从员工表中得到DIV布局
		$this->load->model("user_model");
		$user_info = $this->user_model->get_user_info($user_id);
		$user_workbench_layout = empty($user_info["user_workbench_layout"]) ? "" : $user_info["user_workbench_layout"];
		$this->smarty->assign("user_workbench_layout",$user_workbench_layout);
		
		$this->smarty->display("workbench.htm");
	}

	/*
	*记录工作桌面DIV的布局
	*/
	public function update_workbench_layout()
	{
		admin_priv();

		$layout_div = $this->input->post("layout_div");
		if ($layout_div)
		{
			//更新
			$user_id = $this->session->userdata("user_id");
			$this->load->model("user_model");
			$this->user_model->update_workbench_layout($user_id,$layout_div);
		}

		make_json_result(1);
	}
}