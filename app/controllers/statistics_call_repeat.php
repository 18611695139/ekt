<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  通话重复数统计
 *
 */
class Statistics_call_repeat extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	/**
     *统计页面
     */
	public function index()
	{
		admin_priv();
		global $CDR_CALL_TYPE;//通话记录-通话类型
		$this->smarty->assign('today_start', date("Y-m-d 00:00:00"));
		$this->smarty->assign('today_end', date("Y-m-d 23:59:59"));
		$this->smarty->assign('cdr_call_type',$CDR_CALL_TYPE);
		$this->smarty->display("statistics_call_repeat.htm");
	}


	/**
     * 获取统计信息
     *
     */
	public function get_call_repeat_info()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model("statistics_call_repeat_model");
		$responce = $this->statistics_call_repeat_model->get_call_repeat_info($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}
	
	/**
	 * 导出
	 */
	public function export_repeat()
	{
		admin_priv();
		$condition = $this->input->get();
		$this->load->model('statistics_call_repeat_model');
		$this->statistics_call_repeat_model->export_repeat($condition);
	}
}
