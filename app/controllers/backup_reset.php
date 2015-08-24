<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 备份与还原
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : backup_reset.php
*/
class Backup_reset extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}


	/**
	 * 备份列表
	 */
	public function index()
	{
		//权限：还原
		$power_reset       = check_authz("power_reset");
		$this->smarty->assign("power_reset",$power_reset?$power_reset:0);
	
		$this->smarty->display("backup_reset.htm");
	}

	public function backup_list_query()
	{
		$this->load->model("backup_reset_model");
		$responce = $this->backup_reset_model->get_backup_list();
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 列表 - 还原
	 */
	public function reset_database()
	{
		$backup_id = $this->input->post('backup_id');
		$this->load->model('backup_reset_model');
		$result = $this->backup_reset_model->data_reset($backup_id);
		make_simple_response($result);
	}

	/**
	 *列表 - 删除
	 */
	public function backup_delete()
	{
		$backup_id = $this->input->post('backup_id');
		$this->load->model('backup_reset_model');
		$result = $this->backup_reset_model->backup_delete($backup_id);
		make_simple_response($result);
	}

	/**
	 * 数据备份
	 */
	public function data_backup()
	{
		$file_name = $this->input->post('backup_name');

		$this->load->model("backup_reset_model");
		$result = $this->backup_reset_model->data_backup($file_name);
		make_simple_response($result);
	}
}