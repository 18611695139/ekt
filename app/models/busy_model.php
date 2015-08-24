<?php

class Busy_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取现有置忙原因
	 *
	 */
	public function get_busy_reason()
	{
		$reason_info = array();
		$vcc_id = $this->session->userdata('vcc_id');
		$url = '/api/busyreason/list/' . $vcc_id;
		$reason_info = array();
		try {
			//调用接口，获取数据
			$this->load->model('wintelapi_model');
			$response = $this->wintelapi_model->wintelapi_send($url,'','GET',2);
			if($response)
			{
				$code = isset($response['code']) ? $response['code'] : '';
				$message = isset($response['message']) ? $response['message'] : '消息返回为空';
				switch ($code) {
					case '200':
						$reason_info = isset($response['data']) ? $response['data'] : array();
						break;
					default:
						break;
				}
			}
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
		}
		return  $reason_info;
	}

	/**
	 * 添加置忙原因
	 */
	public function add_busy_reason($stat=1,$reason=array())
	{
		if(empty($reason))
		{
			return false;
		}
		$url = '/api/busyreason/add';
		$vcc_id = $this->session->userdata('vcc_id');
		$param = array(
		'vcc_id' => $vcc_id,
		'stat' => $stat,
		'reason' =>json_encode($reason)
		);
		//调用接口，获取数据
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,$param,'POST');
		return $response;
	}


	/**
	 * 删除置忙原因
	 */
	public function delete_busy_reason($ids='')
	{
		if(empty($ids))
		{
			return false;
		}
		$url = '/api/busyreason/delete';
		$vcc_id = $this->session->userdata('vcc_id');
		$param = array(
		'vcc_id'=>$vcc_id,
		'ids'=>$ids
		);
		//调用接口，获取数据
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,$param,'POST');
		return $response;
	}
}