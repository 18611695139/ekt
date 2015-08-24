<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 易客通 api接口
 * ============================================================================
 * 版权所有 2008-2010 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : Api.php
 * $Author: ztsu
 * $time  : Fri Jan 04 16:37:33 CST 2013
*/

class Api
{
	private $api_interface   = '';
	private $CI;

	public function __construct($params = array())
	{
		log_message('debug', "Apc Class Initialized");
		$this->CI = &get_instance();

		$this->CI->config->load('myconfig');
		$cfg = $this->CI->config->config;
		foreach (array('api_interface') as $key)
		{
			$this->$key = (isset($params[$key])) ? $params[$key] : $cfg[$key];
		}
	}

	/**
	 * 取得号码归属地
	 *
	 * @param string $number
	 * @return array
	 */
	public function phone_area_info($number)
	{
		$post_data = array (
		"act" => "numberloc",
		"number" => $number
		);
		return $this->_send_data($post_data);
	}

	/**
	 * 发短信
	 *
	 * @param string $receiver_phone
	 * @param string $sms_contents
	 * @param int $sms_send_time
	 * @param int $sms_id
	 */
	public function send_sms($receiver_phone,$sms_contents,$sms_send_time,$sms_id)
	{
		$post_data = array(
		'act' => 'send_sms',
		'phone' => $receiver_phone,
		'content' => $sms_contents,
		'send_time' => $sms_send_time,
		'sms_id' => $sms_id
		);
		return $this->_send_data($post_data);
	}


	/**
	 * 发送数据
	 * @param array $post_data 要发送的数据
	 */
	private function _send_data($post_data)
	{
		$post_data['vcc_code'] = $this->_get_vcc_code();
		$post_data['auth_str'] = $this->_get_auth_str();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_interface);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		if ($output === FALSE) {
			$curl_error = curl_error($ch);
			curl_close($ch);
			$this->error_log("phone_area_info   ".$curl_error);
			return array('iRet'=>1,'iMsg'=>$curl_error);
		}
		curl_close($ch);
		$this->CI->load->library('json');
		return $this->CI->json->decode($output,1);
	}

	/**
	 * 取得密文 
	 * 密文 = 公司编码#当前时间戳 用密匙加密
	 *
	 */
	private function _get_auth_str()
	{
		$this->CI->config->load('myconfig');
		$api_vcc_code = $this->_get_vcc_code();
		$api_auth_key = $this->CI->config->item('api_auth_key');
		$api_auth_str = $this->xor_enc($api_vcc_code.'#'.time(),$api_auth_key);
		return $api_auth_str;
	}

	/**
	 * 取得vcc_code
	 *
	 */
	private function _get_vcc_code()
	{
		$this->CI->config->load('myconfig');
		$api_vcc_code = $this->CI->config->item('api_vcc_code');
		if(empty($api_vcc_code))
		{
			$api_vcc_code = $this->CI->session->user_data('vcc_code');
		}
		return $api_vcc_code;
	}
	
		/**
	 * 异或加密解密算法
	 *
	 * @param string $str
	 * @param string $key
	 * @return string
	 */

	private function xor_enc($str,$key)
	{
		$txt = '';
		$keylen = strlen($key);
		for($i=0;$i<strlen($str);$i++)
		{
			$k = $i%$keylen;
			$txt .= $str[$i] ^ $key[$k];
		}
		return $txt;
	}

	private function error_log($message = '')
	{
		$logfilename = FCPATH.APPPATH.'logs/error_api_'. date('Y_m_d') . '.log';
		$str = 'date: '.date('Y_m_d H:i:s')  . "\n"
		.'error: '.$message . "\n\n";
		file_put_contents($logfilename, $str,FILE_APPEND);
	}
}