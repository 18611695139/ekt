<?php

use Guzzle\Http\Client;

class Sms_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 短信管理列表数据
	 *
	 * @param int $page
	 * @param int $limit
	 * @param int $sort
	 * @param int $order
	 * @param array $where 传递搜索条件的数组
	 * @return object responce
	 */
	public function get_sms_list($page=1, $limit=10, $sort=NULL, $order=NULL,$where=NULL)
	{
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_sms');
		$total = $total_query->row()->total;
		$total_pages = ceil($total/$limit);
		$responce = new stdClass();
		$responce -> total = $total;

		$page = $page > $total_pages ? $total_pages : $page;
		$start = $limit*$page - $limit;
		$start = $start > 0 ? $start : 0;
		if( ! empty($sort))
		$this->db_read->order_by($sort,$order);
		$_data = $this->db_read->get('est_sms',$limit,$start);
		$this->db_read->flush_cache();
		$responce -> rows = array();
		foreach($_data->result_array() AS $i=>$task)
		{

			if ($task["sms_send_time"])
			{
				$task["sms_send_time"] = date("Y-m-d H:i:s",$task["sms_send_time"]);
			}

			$responce -> rows[$i] = $task;
		}

		return $responce;
	}

	/**
	 * 获取短信信息
	 *
	 * @param int $sms_id 短信id
	 * @return array 一维数组
	 */
	public function get_sms_info($sms_id=0)
	{
		if (empty($sms_id))
		{
			return false;
		}
		$query = $this->db_read->get_where("est_sms",array('sms_id'=>$sms_id));
		return $query->row_array();
	}

	/**
	 * 短信模板 - 列表 - 获取列表数据
	 *
	 * @param int $page
	 * @param int $limit
	 * @param int $sort
	 * @param int $order
	 * @param array $where 传递搜索条件的数组
	 * @return object responce
	 */
	public function get_smsmodel_list($page=1, $limit=10, $sort=NULL, $order=NULL,$where=NULL)
	{
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_smsmodel');
		$total = $total_query->row()->total;
		$total_pages = ceil($total/$limit);
		$responce = new stdClass();
		$responce -> total = $total;

		$page = $page > $total_pages ? $total_pages : $page;
		$start = $limit*$page - $limit;
		$start = $start > 0 ? $start : 0;
		if( ! empty($sort))
		$this->db_read->order_by($sort,$order);
		$_data = $this->db_read->get('est_smsmodel',$limit,$start);
		$responce -> rows = array();
		foreach($_data->result_array() AS $i=>$task)
		{
			$responce -> rows[$i] = $task;
		}
		$this->db_read->flush_cache();
		return $responce;
	}

	/**
	 * 短信模板 - 保存新模板
	 *
	 * @param string $theme  模板名称
	 * @param string $content  模板内容
	 * @return bool
	 */
	public function insert_smsmodel($theme='',$content='')
	{
		if(empty($content) || empty($theme))
		{
			return false;
		}
		$data = array(
		"theme"=> $theme,
		"content"=> $content,
		"create_time"=> date("Y-m-d H:i:s"),
		"create_id" => $this->session->userdata("user_id"),
		"create_name"=> $this->session->userdata("user_name")
		);
		$result = $this->db_write->insert("est_smsmodel",$data);
		return  $result;
	}
	/**
	 * 短信模板 - 编辑模板
	 *
	 * @param int $mod_id 模板id
	 * @param  string $theme 标题
	 * @param string $content 模板内容
	 * @return bool
	 */
	public function update_smsmodel($mod_id=0,$theme='',$content='')
	{
		if(empty($content) || empty($theme) || empty($mod_id))
		{
			return false;
		}
		$data = array(
		"theme"=> $theme,
		"content"=> $content,
		"update_time"=> date("Y-m-d H:i:s"),
		"update_id" => $this->session->userdata("user_id"),
		"update_name"=> $this->session->userdata("user_name")
		);
		$this->db_write->where("mod_id",$mod_id);
		return $this->db_write->update("est_smsmodel",$data);
	}
	/**
	 * 短信模板 - 删除模板
	 *
	 * @param array $mod_id_array  需要删除的模板ID
	 * @return bool
	 */
	public function delete_smsmodel($mod_id_array=array())
	{
		if(empty($mod_id_array))
		{
			return false;
		}
		$this->db_write->where_in('mod_id',$mod_id_array);
		return $this->db_write->delete('est_smsmodel');
	}

	/**
	 * 短信模板  - 获取模板信息
	 *
	 * @param int $mod_id  检索条件
	 * @return array  短信模板信息
	 */
	public function get_smsmodel_info($mod_id)
	{
		if (empty($mod_id))
		{
			return false;
		}

		$query = $this->db_read->get_where("est_smsmodel",array("mod_id"=>$mod_id));
		return $query->row_array();
	}

	/**
	 * 得到所有短信模板
	 *	
	 * @param string $theme 标题(为空获取所以模板)
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 			[theme]=> 标题
	 * 			...
	 * 		)
	 * 		...
	 * )
	 * </code>
	 */
	public function get_all_smsmodel($theme='')
	{
		if (!empty($theme))
		{
			$where = "theme LIKE '%$theme%'";
			$this->db_read->where($where);
		}
		$query = $this->db_read->get("est_smsmodel");
		return $query->result_array();
	}

	/**
	 * 发短信
	 * @param string $receiver_phone  接收号码
	 * @param string $sms_contents     短信内容
	 * @param int    $sms_send_time       定时发送
	 * @param int     $add_suffix     添加后缀： 1是,2否
	 * @return bool
	 */
	public function send_sms($receiver_phone='',$sms_contents='',$sms_send_time=0,$add_suffix = 1)
	{
        $vcc_id = $this->session->userdata('vcc_id');
        $vcc_code = $this->session->userdata('vcc_code');
        $raw_contents = $sms_contents;
		if(empty($receiver_phone) || empty($sms_contents))
		{
			return false;
		}
		if($sms_send_time == 0)
		{
			$sms_send_time = time();
		}

        $this->load->model("system_config_model");
        $system_config  = $this->system_config_model->get_system_config();
        $sms_signature  = empty($system_config["sms_signature"]) ? "" : $system_config["sms_signature"];
		if ( $add_suffix == 1 )
		{
			//短信内容添加后缀
			$sms_contents  .= "【".$sms_signature."】";
		}

		$user_id = $this->session->userdata('user_id');
		$user_name = $this->session->userdata('user_name');
		$dept_id = $this->session->userdata('dept_id');
        $data = array(
            'user_id'=>$user_id,
            'dept_id' => $dept_id,
            'user_name'=>$user_name,
            'receiver_phone'=>$receiver_phone,
            'sms_send_time'=>$sms_send_time,
            'sms_contents'=>$sms_contents,
            'sms_insert_time'=>time()
        );
		//保存短信
		$this->db_write->insert('est_sms',$data);
		$sms_id = $this->db_write->insert_id();
		return $this->_send_sms($vcc_id, $vcc_code, $user_id, $receiver_phone, $raw_contents, $sms_send_time, $sms_id, $sms_signature);
	}

	/**
	 * 短信管理 - 重发
	 *
	 * @param int    $sms_id          短信ID
	 * @return bool
	 */
	public function resend_sms($sms_id = 0)
	{
		if (empty($sms_id))
		{
			return false;
		}

		//得到需要重发的短信
        $vcc_id = $this->session->userdata('vcc_id');
        $vcc_code = $this->session->userdata('vcc_code');
		$sms_info = $this->get_sms_info($sms_id);
		$receiver_phone = empty($sms_info['receiver_phone']) ? "" : $sms_info['receiver_phone'];
		$sms_contents   = empty($sms_info['sms_contents']) ? "" : $sms_info['sms_contents'];
		$user_id        = empty($sms_info["user_id"]) ? 0 : $sms_info["user_id"];

        $this->load->model("system_config_model");
        $system_config  = $this->system_config_model->get_system_config();
        $sms_signature  = empty($system_config["sms_signature"]) ? "" : $system_config["sms_signature"];
		if(empty($receiver_phone) || empty($sms_contents) ||  empty($user_id))
		{
			return false;
		}

		//发送时间
		$sms_send_time = time();

		//更新短信
		$update_sms = array(
		"sms_send_time" => $sms_send_time,
		"sms_result" => 0,
		"sms_fail_reason" => ""
		);
		$this->db_write->where("sms_id",$sms_id);
		$this->db_write->update("est_sms",$update_sms);
		return $this->_send_sms($vcc_id, $vcc_code, $user_id, $receiver_phone, $sms_contents, $sms_send_time, $sms_id, $sms_signature);
	}

    /**
     * 发消息
     * @param $vcc_id
     * @param string $vcc_code
     * @param $ag_id
     * @param $receiver_phone
     * @param $sms_contents
     * @param string $send_time
     * @param $client_sms_id
     * @param $signature
     * @return array|\Guzzle\Http\EntityBodyInterface|string
     */
	private function _send_sms($vcc_id,$vcc_code='',$ag_id,$receiver_phone,$sms_contents,$send_time='',$client_sms_id,$signature)
	{
        if (!empty($receiver_phone)) {
            $phones = explode(',',$receiver_phone);
            foreach ($phones as $key=>$phone) {
                if (!preg_match('/^(01|1)(3[0-9]|4[5|7]|5[0|1|2|3|5|6|7|8|9]|8[0-9])\d{8}$/',$phone)) {

                    return array('code'=>406, 'message'=>'接收人的号码格式填写错误');
                }
            }
        }

        //判断内容中是否存在签名
        if (strstr($sms_contents, $signature)) {
            $sms_contents = str_replace('【'.$signature.'】', '', $sms_contents);
        }

        $this->load->config('myconfig');
        $api      = $this->config->item('sms');
        $sms_flag = $this->config->item('sms_flag');
        $token = $this->config->item('sms_token');
        $data = array(
            'vcc_id' => $vcc_id,
            'vcc_code' => $vcc_code,
            'ag_id' => $ag_id,
            'receiver_phone' => $receiver_phone,
            'sms_contents' => $sms_contents,
            'send_time' => $send_time,
            'client_sms_id' => $client_sms_id,
            'signature' => $signature,
            'flag'=>$sms_flag,
            'token'=>$token
        );


        if (empty($api)) {
            return array('code'=>410, 'message'=>'短信接口地址未设置');
        }

        $client = new Client();
        $request = $client->post($api.'/sms/send', null, $data);
        //$response = $request->send();
        //$result = $response->getBody();

        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array('code'=>411, 'message'=>'解析结果出错，错误为【'.json_last_error().'】');
        }

        return $response;
	}

	/**
	 * 短信群发
	 *
	 * @param array $phones
	 * @param string $sms_contents
     * @param int $sms_send_time
	 */
	public function send_sms_batch($phones= array(),$sms_contents='',$sms_send_time=0)
	{
		foreach ($phones as $phone)
		{
			$this->send_sms($phone,$sms_contents,$sms_send_time);
		}
	}

    /**
     * 更新已发送短信的处理结果
     *
     * @return array
     */
    public function get_sentsms_result()
    {
        $vcc_id = $this->session->userdata('vcc_id');
        //查询500条未更新的短信
        $this->db_read->where('if_updated', 0);
        $this->db_read->select('sms_id');
        $this->db_read->limit(500);
        $res = $this->db_read->get('est_sms');
        $sms_ids = array();
        if ($res->num_rows()) {
            foreach($res->result_array() as $key=>$val)
            {
                $sms_ids[] = $val['sms_id'];
            }
        }

        $data = array(
            'vcc_id' => $vcc_id,
            'ids' => $sms_ids
        );
        $this->load->config('myconfig');
        $api = $this->config->item('sms');
        if (empty($api)) {

            log_message('error', '短信接口地址未设置');
        }
        //通过接口获取数据
        $client = new Client();
        $request = $client->post($api.'/sms/result', null, $data);
        $response = $request->send();
        $result = $response->getBody();

        //判断是否获取成功
        if ($result['code'] == 200) {
            if (empty($result['data'])) {

                log_message('error', '没有更新的数据');
            }
            foreach ($result['data'] as $k => $v) {
                if ($v['is_sended'] == 1) {
                    $updata = array(
                        'sms_result' => $v['result'],
                        'if_updated' => 1
                    );
                    $this->db_read->where('sms_id', $v['client_sms_id']);
                    if ($this->db_read->update('est_sms', $updata)) {
                        log_message('error', "UPDATE est_sms SET if_updated = 1 AND sms_result = '{$v['result']}' WHERE sms_id = '{$v['client_sms_id']}'");
                    }
                }
            }
        } else {

            log_message('error', $result['message']);
        }

        return true;
    }
}