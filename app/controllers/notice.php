<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notice extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 读消息|监听消息
	 *
	 */
	public function monitor_notice()
	{
		admin_priv();
		
		$this->load->model("notice_model");
		$notice = $this->notice_model->monitor_notice();
		if($notice && is_array($notice))
		{
			make_json_result('','',$notice);
		}
	}
}