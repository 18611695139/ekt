<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Address_book extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取通讯录列表页面
	 */
	public function list_address_book()
	{
		admin_priv('wdzstxl');
		
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：公司通讯录(管理)
		$power_company_address_book       = check_authz("wdzsgstxl");
		$this->smarty->assign("power_company_address_book",$power_company_address_book?$power_company_address_book:0);

		$user_id = $this->session->userdata('user_id');
		$this->smarty->assign("user_id",$user_id);

		$this->smarty->display('address_book.htm');
	}

	/**
	 * 获取通讯录列表数据
	 */
	public function address_book_query()
	{
		admin_priv('wdzstxl',false);
		
		$condition = array();
		$condition['search_key'] = $this->input->post('search_key');
		$condition['sql_type'] = $this->input->post('sql_type');
		$this->load->model("address_book_model");
		$responce = $this->address_book_model->get_address_book_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 获取添加通讯录页面 type -- 1 公司 2个人
	 */
	public function add_address_book()
	{
		admin_priv();
		
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);

		$type = $this->input->get("type");
		$this->smarty->assign('type',$type);
		
		$this->smarty->display('address_book_add.htm');
	}

	/**
	 * 执行添加一通讯录
	 */
	public function insert_address_book()
	{
		admin_priv();
		
		$tx_name   = $this->input->post("tx_name");
		$tx_phone1 = $this->input->post("tx_phone1");
		$tx_phone2 = $this->input->post("tx_phone2");
		$tx_remark = $this->input->post("tx_remark");

		// 1公司   2个人
		$tx_type = $this->input->post("tx_type");

		$this->load->model('address_book_model');
		$result = $this->address_book_model->insert_address_book($tx_name,$tx_phone1,$tx_phone2,$tx_remark,$tx_type);

		make_simple_response($result);
	}

	/**
	 * 获取修改通讯录页面
	 */
	public function edit_address_book()
	{
		admin_priv();
		
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);

		$tx_id = $this->input->get('tx_id');
		$this->load->model('address_book_model');
		$address_book_info = $this->address_book_model->get_address_book_info($tx_id);
		$this->smarty->assign('result',$address_book_info);

		$this->smarty->assign('type',3);
		$this->smarty->display('address_book_add.htm');
	}

	/**
	 * 执行修改通讯录
	 */
	public function update_address_book()
	{
		admin_priv();
		
		$tx_id     = $this->input->post("tx_id");
		$tx_name   = $this->input->post("tx_name");
		$tx_phone1 = $this->input->post("tx_phone1");
		$tx_phone2 = $this->input->post("tx_phone2");
		$tx_remark = $this->input->post("tx_remark");

		$this->load->model('address_book_model');
		$result = $this->address_book_model->update_address_book($tx_id,$tx_name,$tx_phone1,$tx_phone2,$tx_remark);
		make_simple_response($result);
	}

	/**
	 * 删除通讯录
	 */
	public function delete_address_book()
	{
		admin_priv();
		
		$tx_id = $this->input->post('tx_id');
		$this->load->model('address_book_model');
		$result = $this->address_book_model->delete_address_book($tx_id);
		make_simple_response($result);
	}

}