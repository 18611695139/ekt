<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 通话记录
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : callrecords.php 
 * $Author: yhx
 * $time  : Fri Jul 13 14:33:39 CST 2012
*/
class Callrecords extends CI_Controller {

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
		//权限：通话记录（全部数据）
		$power_callrecord = check_authz("power_callrecord");
		$this->smarty->assign("power_callrecord",$power_callrecord?$power_callrecord:0);
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign("role_type",$role_type);
		//权限：发短信功能
		$sendsms = check_authz("sendsms");
		$this->smarty->assign("message_authority",$sendsms?$sendsms:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：录音下载
		$power_download_record = check_authz("xtgllyxz");
		$this->smarty->assign("power_download_record",$power_download_record?$power_download_record:0);

		$this->smarty->assign('today_start', date("Y-m-d 00:00:00"));
		$this->smarty->assign('today_end', date("Y-m-d 23:59:59"));

		$this->smarty->display("callrecord_list.htm");
	}

	/**
	 *得到通话记录
	 * 
	 * @return void 
	 */
	public function callrecords_query()
	{
		$condition = $this->input->post();
		//翻译快捷查询  1全部数据  2我的数据
		if(!empty($condition['date_type']) && $condition['date_type'] ==2)
		{
			$condition['user_id'] = $this->session->userdata("user_id");
		}

		$this->load->model("callrecords_model");
		$responce = $this->callrecords_model->get_callrecords_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 下载录音 并改变录音名
	 */
	public function download_record()
	{
		$callid = $this->input->get('callid');
		$type   = $this->input->get('type');
        $ag_id   = $this->input->get('ag_id');
		/*$this->load->model('callrecords_model');
		$record_i = $this->callrecords_model->get_pro_record_role($callid);
		$this->load->config('myconfig');
		$record_url = $this->config->item('record_interface');//坐席录音下载地址*/

        $this->config->load('myconfig');
        $api = $this->config->item('api_recordapi');

        $vcc_id = $this->session->userdata("vcc_id");
        if (empty($ag_id)) {
            est_header('location:'.$api.'/api/record/playrecord/'.$vcc_id.'/'.$callid );
        } else {
            est_header('location:'.$api.'/api/record/playrecord/'.$vcc_id.'/'.$callid.'/'.$ag_id );
        }
	}

	/**
	 * 通过callid放回录音地址
	 */
	public function get_record_url()
	{
		$callid = $this->input->get('callid');
        $ag_id   = $this->input->get('ag_id');
        /*$this->load->config('myconfig');
        $record_url = $this->config->item('record_interface');//坐席录音下载地址
        est_header('location:'.$record_url.'?callid='.$callid.'&type='.$type);*/
        $this->config->load('myconfig');
        $api = $this->config->item('api_recordapi');

        $vcc_id = $this->session->userdata("vcc_id");
        if (empty($ag_id)) {
            est_header('location:'.$api.'/api/record/playrecord/'.$vcc_id.'/'.$callid );
        } else {
            est_header('location:'.$api.'/api/record/playrecord/'.$vcc_id.'/'.$callid.'/'.$ag_id );
        }
	}


	/**
	 * 录音播放器 （业务受理 - 跟踪记录）
	 *
	 */
	public function record_player()
	{
		$callid = $this->input->get("callid");
		if (empty($callid))
		{
			die("缺少录音参数");
		}
		$this->smarty->assign("callid",$callid);

		$this->smarty->display("player.htm");
	}
}