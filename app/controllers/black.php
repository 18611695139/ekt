<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI black
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : black.php
 * $Author: cx
 * $time  : Sun Jan 06 16:06:45 CST 2013
 */

use Guzzle\Http\Client;

class Black extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		admin_priv();
		$this->smarty->display("black_list.htm");
	}

	public function get_list()
	{
		admin_priv();

        $this->config->load('myconfig');
        $api    = $this->config->item('api_wintelapi');
        $client = new Client();

        $vcc_code = $this->session->userdata('vcc_code') ? $this->session->userdata('vcc_code'): '';
        list($page, $limit, $sort, $order) = get_list_param();
        $params = array(
            'vcc_code' => $vcc_code,
            'sort'=>array(
                'field'=>$sort,
                'order'=>$order
            )
        );

        $request  = $client->get($api.'/api/blacklist/list/'.urlencode(json_encode($params)));
        $response = $request->send()->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }
        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data    = isset($response['data']) ? $response['data'] : array();

        $result = new stdClass();
        $result -> rows  = array();
        $result -> total = 0;

        if ($code == 200 ) {
            $result -> rows = $data;
            $result -> total = $response['total'];
        }

        echo json_encode($result);
	}

	/**
     * 添加黑名单页面
     * */
	public function black_add()
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

		$this->smarty->display("blacklist_info.htm");
	}

	/**
     * 添加黑名单号码验证 提交
     */
	public function set_blacklist()
	{
		admin_priv();
		$vcc_id = $this->session->userdata('vcc_id');
		$url = '/api/blacklist/add';
		$inarray = $this->input->post();
		$phones = empty($inarray['phone']) ? array() : $inarray['phone'];
		$param = array(
		'vcc_id' => $vcc_id,
		'phones' => implode(',', $phones),
		'phone_type' => empty($inarray['phone_type']) ? 1 : $inarray['phone_type'],
        'trunk_num' => empty($inarray['trunk_num']) ? '' : $inarray['trunk_num']
		);

		//调用接口，返回信息
		$this->load->model('wintelapi_model');
		$response = $this->wintelapi_model->wintelapi_send($url,$param,'POST');

		$code = isset($response['code']) ? $response['code'] : '';
		$message = isset($response['message']) ? $response['message'] : '消息返回为空';
		switch ($code) {
			case '200':
				$links[0]['text'] = '黑名单列表';
				$links[0]['href'] = 'index.php?c=black';
				sys_msg('添加黑名单成功', 0, $links);
				break;
			default:
				$links[0]['text'] = '添加黑名单';
				$links[0]['href'] = 'index.php?c=black&m=black_add';
				sys_msg($message, 0, $links, true);
				break;
		}
	}

	/**
     * 删除黑名单
     */
	public function black_delete()
	{
		admin_priv();
		$url = '/api/blacklist/delete';
		$ids = $this->input->post('ids');
		$vcc_id = $this->session->userdata('vcc_id');
		$param = array(
		'vcc_id' => $vcc_id,
		'phone_ids' => $ids
		);
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

