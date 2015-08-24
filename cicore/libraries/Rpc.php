<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * EasyTone rpc向后台请求接口
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : cls_inferface.php 
 * $Author: ZT
 * $time  : Wed Aug 11 11:08:58 CST 2010
*/
class Rpc
{
	private $client   = null;
	private $rpc_interface = '';
	private $CI;

    /**
     * 构造函数
     *  $interface 短信rpc使用 默认是 interface接口地址为 main_interface.php	 传参数1 则接口地址为短信接口地址 sms_interface.php
     * @param array $params
     */
    public function __construct($params = array())
	{
		log_message('debug', "Rpc Class Initialized");
		$this->CI = &get_instance();

		$this->CI->config->load('myconfig');
		$cfg = $this->CI->config->config;
		foreach (array('rpc_interface') as $key)
		{
			$this->$key = (isset($params[$key])) ? $params[$key] : $cfg[$key];
		}

        define('KEEP_PHPRPC_COOKIE_IN_SESSION',true);
		include_once(BASEPATH."libraries/rpc_client/phprpc_client.php");
		$this->client = new PHPRPC_Client();
		$this->client->setProxy(NULL);
		$this->client->useService($this->rpc_interface);
		$this->client->setKeyLength(2);
		$this->client->setEncryptMode(2);
		$this->client->setCharset('UTF-8');
		$this->client->setTimeout(10);
	}

    /**
     * 登陆验证
     * @param $vcc_code
     * @param $user_num
     * @param $user_password
     * @return array
     */
    public function signin($vcc_code,$user_num,$user_password)
	{
		$retarr =  $this->client->signin($vcc_code,$user_num,$user_password);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("signin   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 修改个人密码(密码均为编码后的数据)
     * @param $vcc_id
     * @param $ag_id
     * @param $oldpassword
     * @param $newpassword
     * @return array
     */
    public function edit_password($vcc_id,$ag_id,$oldpassword,$newpassword)
	{
		$retarr = $this->client->edit_password($vcc_id,$ag_id,$oldpassword,$newpassword);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("edit_password   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 把客户端的短信发送至服务器
     * @param $vcc_id
     * @param $ag_id
     * @param $receiver_phone 接收号码
     * @param $sms_contents 短信内容
     * @param int $send_time_int 发送时间戳
     * @param int $client_sms_id 客户端短信唯一标示
     * @return array
     */
    public function send_sms($vcc_id,$ag_id,$receiver_phone,$sms_contents,$send_time_int=0,$client_sms_id=0)
	{
		$retarr =  $this->client->send_sms($vcc_id,$ag_id,$receiver_phone,$sms_contents,$send_time_int,$client_sms_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("send_sms   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 新建坐席
     * @param $vcc_id
     * @param $que_id
     * @param $ag_num
     * @param $ag_name
     * @param $ag_password
     * @return array
     */
    public function new_agent($vcc_id,$que_id,$ag_num,$ag_name,$ag_password)
	{
		$retarr = $this->client->new_agent($vcc_id,$que_id,$ag_num,$ag_name,$ag_password);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("new_agent   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 编辑员工
     * @param $vcc_id
     * @param $ag_id
     * @param $ag_name
     * @return array
     */
    public function edit_agent($vcc_id,$ag_id,$ag_name)
	{
		$retarr = $this->client->edit_agent($vcc_id,$ag_id,$ag_name);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("edit_agent   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 删除员工
     * @param $vcc_id
     * @param $ag_id
     * @return array
     */
    public function remove_agent($vcc_id,$ag_id)
	{
		$retarr = $this->client->remove_agent($vcc_id,$ag_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("remove_agent   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 得到空闲的坐席
     * @param $vcc_id
     * @return array
     */
    public function get_free_agents($vcc_id){
		$retarr =  $this->client->get_free_agents($vcc_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("get_free_agents   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 得到通话中的坐席
     * @param $vcc_id
     * @return array
     */
    public function get_talking_agents($vcc_id){
        $retarr =  $this->client->get_talking_agents($vcc_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("get_talking_agents   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 得到队列监控数据
     * @param $vcc_id
     * @param $que_ids
     * @return array
     */
    public function get_monitor_queue($vcc_id,$que_ids)
	{
		$retarr = $this->client->get_monitor_queue($vcc_id,$que_ids);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("get_monitor_queue   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 获取坐席监控数据
     * @param $vcc_id
     * @param $user_ids
     * @param $que_id
     * @return array
     */
    public function get_monitor_agent($vcc_id,$user_ids,$que_id)
	{
		$retarr = $this->client->get_monitor_agent($vcc_id,$user_ids,$que_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("get_monitor_agent   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

    /**
     * 获取排队电话监控数据
     * @param $vcc_id
     * @param $que_id
     * @return array
     */
    public function get_monitor_calls($vcc_id,$que_id)
	{
		$retarr = $this->client->get_monitor_calls($vcc_id,$que_id);
		if (is_array($retarr) && isset($retarr["iRet"]))
		{
			return $retarr;
		}
		else
		{
			$this->error_log("get_monitor_calls   ".$retarr->Message);
			return array('iRet'=>1,'iMsg'=>$retarr->Message);
		}
	}

	/**
	 * 把老客户来电转给坐席手机 (前提：启用了转老客户的功能)
	 * @param int $vcc_id
	 * @param int $ag_id 坐席id
	 * @param string $phone 手机号
	 * @param int $state 状态 0无效 1有效
	 * @return array
	 */
	public function set_agextphone($vcc_id,$ag_id,$phone,$state)
	{
		$agextphonearr = $this->client->set_agextphone($vcc_id,$ag_id,$phone,$state);
		if(is_array($agextphonearr) && isset($agextphonearr["iRet"]))
		{
			return $agextphonearr;
		}
		else
		{
			$this->error_log("set_agextphone   ".$agextphonearr->Message);
			return array('iRet'=>1,'iMsg'=>$agextphonearr->Message);
		}
	}

	/**
	 * 获取时间段内统计中需要的数据(登录时长、就绪时长、置忙时长)
	 *
	 * @param int    $vcc_id    企业ID
	 * @param string $user_ids  员工ID（多个ID以逗号分隔）
	 * @param string $start_date 开始日期
	 * @param string $end_date 结束日期
	 * @return array
	 */
	public function get_statistics_data($vcc_id,$user_ids,$start_date,$end_date)
	{
		$statistics_data = $this->client->get_statistics_data($vcc_id,$user_ids,$start_date,$end_date);
		if(is_array($statistics_data) && isset($statistics_data["iRet"]))
		{
			return $statistics_data;
		}
		else
		{
			$this->error_log("get_statistics_data   ".$statistics_data->Message);
			return array('iRet'=>1,'iMsg'=>$statistics_data->Message);
		}
	}

	/**
     * 取得号码归属地信息
     * @param $number
     * @return array
     */
    public function get_numberloc($number)
	{
		$numberloc = $this->client->numberloc($number);
		if(is_array($numberloc) && isset($numberloc["iRet"]))
		{
			return $numberloc;
		}
		else
		{
			$this->error_log("get_numberloc   ".$numberloc->Message);
			return array('iRet'=>1,'iMsg'=>$numberloc->Message);
		}
	}

    /**
     * 获取坐席操作详情
     * @param $vcc_id
     * @param $user_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function get_sta_detail($vcc_id,$user_id,$start_date,$end_date)
	{
		$sta_detail = $this->client->get_sta_detail($vcc_id,$user_id,$start_date,$end_date);
		if(is_array($sta_detail) && isset($sta_detail["iRet"]))
		{
			return $sta_detail;
		}
		else
		{
			$this->error_log("get_sta_detail   ".$sta_detail->Message);
			return array('iRet'=>1,'iMsg'=>$sta_detail->Message);
		}
	}
	
	public function get_monitor_system($vcc_id)
	{
		$res = $this->client->get_monitor_system($vcc_id);
		if(is_array($res) && isset($res["iRet"]))
		{
			return $res;
		}
		else
		{
			$this->error_log("get_monitor_system   ".$res->Message);
			return array('iRet'=>1,'iMsg'=>$res->Message);
		}
	}

    /**
     * 返回错误信息
     * @param string $message
     */
    private function error_log($message = '')
	{
		$logfilename = FCPATH.APPPATH.'logs/error_rpc_'. date('Y_m_d') . '.log';
		$str = 'date: '.date('Y_m_d H:i:s')  . "\n"
		.'error: '.$message . "\n\n";
		file_put_contents($logfilename, $str,FILE_APPEND);
	}
}