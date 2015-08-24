<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 跟踪记录
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : contact_record.php
 * $Author: yhx
 * $time  : Tue Oct 30 16:12:58 CST 2012
*/
class Contact_record extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 联系记录
	 *
	 */
	public function contact_record_list()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		$order_id = $this->input->get('order_id');
		if (empty($cle_id) && empty($order_id))
		{
			die("缺少参数！");
		}
		if($cle_id)
		{
			$this->smarty->assign("cle_id",$cle_id);
		}
		if($order_id)
		{
			$this->smarty->assign("order_id",$order_id);
		}

		//权限：录音下载
		$power_download_record = check_authz("xtgllyxz");
		$this->smarty->assign("power_download_record",$power_download_record?$power_download_record:0);

		$this->smarty->display("client_contact_record.htm");
	}

	/**
	 * Contact_record::contact_record_query()
	 * 业务受理 - 过往联系记录
	 * @return void 
	 */
	public function contact_record_query()
	{
		admin_priv();

		$condition = $this->input->post();
		//数据
		$this->load->model("contact_record_model");
		$responce = $this->contact_record_model->get_contact_record_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 过往联系记录详情(客户)
	 * return @array()
	 */
	public function contact_record_detail()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		$this->load->model("contact_record_model");
		$contact_record_detail = $this->contact_record_model->contact_record_detail($cle_id);

		$this->smarty->assign('contact_record_detail',$contact_record_detail);
		$this->smarty->display('contact_record_detail.htm');

	}


	/**
	 * 新建联系记录
	 *
	 */
	public function new_contact_record()
	{
		admin_priv();

		$cle_id = $this->input->get("cle_id");
		$order_id = $this->input->get('order_id');
		if (!$cle_id && !$order_id)
		{
			die("缺少客户参数！");
		}
		if ($cle_id)
		{
			//客户
			$this->smarty->assign("cle_id",$cle_id);
			$this->smarty->assign("rmd_type",REMIND_CLIENT);
		}

		if($order_id)
		{
			//订单
			$this->smarty->assign("order_id",$order_id);
			$this->smarty->assign("rmd_type",REMIND_ORDER);
		}
		//默认下次联系时间
		$this->smarty->assign("date",date("Y-m-d"));

		//权限：客户数据（修改）
		$power_client_update = check_authz("power_update");
		$this->smarty->assign("power_client_update",$power_client_update?$power_client_update:0);

		//短信提醒
		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$user_sms_phone = empty($user_info['user_sms_phone']) ? "" : $user_info['user_sms_phone'];
		$this->smarty->assign("user_sms_phone",$user_sms_phone);

		$this->smarty->display("contact_record_info.htm");
	}

	/**
	 * 保存新的联系记录
	 *
	 */
	public function insert_contact_record()
	{
		admin_priv();

		$cle_id = $this->input->post('cle_id');
		$order_id = $this->input->post('order_id');
		$callid = $this->input->post('rec_callid');
		$con_rec_content = $this->input->post('con_rec_content');
		$con_rec_next_time = $this->input->post('con_rec_next_time');

		//提醒类型  0默认，自建提醒   1客户相关提醒  2订单相关提醒
		$rmd_type       = $this->input->post("rmd_type");

		//创建提醒
		$create_reamind = $this->input->post("create_reamind");
		if ( !empty($create_reamind) && $create_reamind == 1)
		{
			$rmd_param_int  = $this->input->post('rmd_param_int');
			$rmd_param_char = $this->input->post('rmd_param_char');
			//提醒内容
			$rmd_remark     = $this->input->post("rmd_remark");
			//提醒时间
			$rmd_time       = $this->input->post('rmd_time');
			//短信提醒、提醒号码
			$rmd_sendsms    = $this->input->post('rmd_sendsms');
			$user_sms_phone = $this->input->post("user_sms_phone");
			if($rmd_sendsms == 1)
			{
				$rmd_sendsms = true;
			}
			else
			{
				$rmd_sendsms = false;
			}
			$this->load->model('remind_model');
			$this->remind_model->insert_remind($rmd_param_int,$rmd_param_char,$rmd_time,$rmd_remark,$rmd_type,$rmd_sendsms,$user_sms_phone);
		}

		//保存新的联系记录
		$this->load->model("contact_record_model");
		$result = $this->contact_record_model->insert_contact_record($callid,$con_rec_content,$con_rec_next_time,$cle_id,$order_id);
		make_simple_response($result);
	}
}
