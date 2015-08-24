<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class White extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		admin_priv();
		$this->smarty->display("white_list.htm");
	}

	/**
     * 添加黑名单页面
     * */
	public function white_add()
	{
		admin_priv();
		$vcc_id = $this->session->userdata('vcc_id') ? $this->session->userdata('vcc_id') : '';
		$url = '/api/relaynumber/getphone/' . $vcc_id;
		
		//调用接口，返回信息
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,'','GET');
		
		$code = isset($response['code']) ? $response['code'] : 0;
		$phones = array();
		if ($code == '200') {
			$data = isset($response['data']) ? $response['data'] : array();
			foreach ($data as $row) {
				$phones[$row['phone']] = $row['phone'] . '[' . $row['phone400'] .']';
			}
		}
		$this->smarty->assign('phones', $phones);
		$this->smarty->display("whitelist_info.htm");
	}

	public function get_list()
	{
		admin_priv();
		$vcc_id = $this->session->userdata('vcc_id') ? $this->session->userdata('vcc_id') : '';
		$url = '/api/whitelist/list/' . $vcc_id;
		
		//调用接口，返回信息
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,'','GET');
		
		$result = new stdClass();
		if ($response['code'] == 200) {
			$result->rows = $response['data'];
			echo json_encode($result);

		}
	}

	/**
     * 添加白名单号码验证 提交
     */
	public function set_whitelist()
	{
		admin_priv();
		$inarray = $this->input->post() ? $this->input->post() : '';
		$vcc_id = $this->session->userdata('vcc_id');
		$phones = empty($inarray['phone']) ? array() : $inarray['phone'];
		$phone_type = empty($inarray['phone_type']) ? 1 : $inarray['phone_type'];
		$trunk_num = empty($inarray['trunk_num']) ? '' : $inarray['trunk_num'];

		if ($phone_type == 1) {
			//单个号码添加
			$phones = implode(',', $phones);
		} else {
			//批量添加
			$phones = implode(',', $phones);
			$phones = str_replace(PHP_EOL, ',', $phones);
		}
		$param = array(
		'vcc_id' => $vcc_id,
		'phones' => $phones,
		'trunk_num' => $trunk_num
		);
		$url = '/api/whitelist/add';

		//调用接口，返回信息
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,$param,'POST');
		
		$code = isset($response['code']) ? $response['code'] : '';
		$message = isset($response['message']) ? $response['message'] : '消息返回为空';
		switch ($code) {
			case '200':
				$links[0]['text'] = '白名单列表';
				$links[0]['href'] = 'index.php?c=white';
				sys_msg('添加白名单成功', 0, $links);
				break;
			default:
				$links[0]['text'] = '添加白名单';
				$links[0]['href'] = 'index.php?c=white&m=white_add';
				sys_msg($message, 0, $links, true);
				break;
		}
	}

	/**
     * 删除白名单
     */
	public function white_del()
	{
		admin_priv();
		$ids = $this->input->post('ids');
		$vcc_id = $this->session->userdata('vcc_id');
		$param = array(
		'vcc_id' => $vcc_id,
		'phone_ids' => $ids
		);
		$url = '/api/whitelist/delete';
		//调用接口，返回信息
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,$param,'POST');
		
		$code = isset($response['code']) ? $response['code'] : '';
		$message = isset($response['message']) ? $response['message'] : '消息返回为空';
		switch ($code) {
			case '200':
				make_json_result($code);
				break;
			default:
				make_json_error($message);
				break;
		}
	}
}

