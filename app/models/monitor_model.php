 <?php

 use Guzzle\Http\Client;

 class Monitor_model extends CI_Model {

 	function __construct()
 	{
 		parent::__construct();
 	}

 	/**
 	 * 获取队列监控数据
 	 *
 	 * @param int    $vcc_id
 	 * @param string $ques      que_id，que_name@que_id，que_name
     * @return object
 	 */
 	public function queue_list($vcc_id = 0 , $ques = '' )
 	{
 		$responce = new stdClass();
 		$responce -> rows = array();

 		if ($vcc_id == 0 || $ques == '')
 		{
 			return $responce;
 		}

 		//系统队列  que_id，que_name
 		$ques       = explode("@",$ques);
 		$que_ids    = array();  // [0] => que_id
 		$queue_info = array(); //  [que_id] => que_name
 		foreach ($ques AS $key => $value)
 		{
 			if ($value)
 			{
 				$value = explode(",",$value);
 				$que_ids[] = $value[0];
 				$queue_info[$value[0]] = $value[1];
 			}
 		}

 		if (empty($que_ids))
 		{
 			return $responce;
 		}
 		$que_ids = implode(",",$que_ids);  //  que_id,que_id,que_id

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'que_ids' => $que_ids
        );
        $request = $client->post($api.'/api/monitor/queue', array(), $params);
        $response = $request->send()->json();


        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }
        $code    = isset($response['code']) ? $response['code'] : 0;
        $data    = isset($response['data']) ? $response['data'] : array();

        $key = 0;
        if ($code == 200) {
            $data_que_id = array();
            if (!empty($data)) {
                foreach ($data as  $info) {
                    $data_que_id[] = $info['que_id'];
                    $que_name = empty($queue_info[$info['que_id']]) ? "未知队列" : $queue_info[$info['que_id']];
                    $responce -> rows[$key] = array(
                        'que_id' =>$info['que_id'],
                        'que_name' =>$que_name,
                        'online' =>$info['online'],
                        'queue' =>$info['queue'],
                        'ring' =>$info['ring'],
                        'call' =>$info['call'],
                        'rest' =>$info['rest'],
                        'ready' =>$info['ready'],
                        'busy' =>$info['busy'],
                    );
                    $key++;
                }
            }
            foreach ($ques as $que) {
                if ($que) {
                    $que = explode(",",$que);
                    if (!in_array($que[0],$data_que_id)) {
                        $responce -> rows[$key] = array(
                            'que_id' =>$que[0],
                            'que_name' =>$que[1],
                            'online' =>0,
                            'queue' =>0,
                            'ring' =>0,
                            'call' =>0,
                            'rest' =>0,
                            'ready' =>0,
                            'busy' =>0,
                        );
                        $key++;
                    }
                }
            }
        }
 		return $responce;
 	}

 	/**
 	 * 获得坐席监控的数据
 	 *
 	 * @param int $vcc_id  企业ID
 	 * @param int $que_id  技能组ID
 	 * @param string $list_user_id  员工ID(多个ID用逗号分隔)
 	 * 
 	 * @return array
 	 */
 	public function get_agent_monitor_data($vcc_id = 0,$que_id = 0,$list_user_id = "" )
 	{
 		$monitor_result = array();

 		if ( empty($vcc_id) )
 		{
 			return $monitor_result;
 		}

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'que_id' => $que_id,
            'user_ids' => $list_user_id
        );
        $request = $client->post($api.'/api/monitor/agent', array(), $params);
        $response = $request->send()->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $data    = isset($response['data']) ? $response['data'] : array();

        if ($code == 200) {
            /*
             |--------------------------------------------------------------------------
             | 坐席状态
             |--------------------------------------------------------------------------
             */
            $AGSTATUS[0] = "未登录";
            $AGSTATUS[1] = "就绪";
            $AGSTATUS[2] = "置忙";
            $AGSTATUS[4] = "占用";
            $AGSTATUS[5] = "事后处理";
            $AGSTATUS[7] = "常接通离线";
            $AGSTATUS[8] = "振铃";
            $AGSTATUS[9] = "通话";

            //置忙原因
            $busy_reason = array();
            $this->load->model("busy_model");
            $busy_reason_info = $this->busy_model->get_busy_reason();
            foreach ($busy_reason_info as $busy)
            {
                $busy_reason[$busy['id']] = $busy['stat_reason'];
            }

            foreach ($data as $monitor_data)
            {
                //员工对应监控数据
                $monitor_result[$monitor_data['ag_id']]["user_id"]         = $monitor_data['ag_id'];
                $monitor_result[$monitor_data['ag_id']]["user_num"]         = $monitor_data['ag_num'];
                $monitor_result[$monitor_data['ag_id']]["user_name"]         = $monitor_data['ag_name'];

                /* 坐席分机号码 'pho_num'*/
                $monitor_result[$monitor_data['ag_id']]["pho_num"]         = empty($monitor_data['pho_num']) ? "" : $monitor_data['pho_num'];
                /* 状态 'status'*/
//                $monitor_result[$monitor_data['ag_id']]["status"] = empty($AGSTATUS[$monitor_data['ag_sta']]) ? "" : $AGSTATUS[$monitor_data['ag_sta']];
                if($monitor_data['ag_sta'] == 4)
                {
                    if($monitor_data['pho_sta'] == 1) {
                        $monitor_result[$monitor_data['ag_id']]["status"] = "振铃";
                    }
                    elseif($monitor_data['pho_sta'] == 2) {
                        $monitor_result[$monitor_data['ag_id']]["status"] = "通话";
                    }
                }
                else if($monitor_data['ag_sta'] == 2 && isset($busy_reason[$monitor_data['ag_sta_reason']]))
                {
                     $monitor_result[$monitor_data['ag_id']]["status"] = "置忙【".$busy_reason[$monitor_data['ag_sta_reason']]."】";
                }
                else
                {
                    $monitor_result[$monitor_data['ag_id']]["status"] = empty($AGSTATUS[$monitor_data['ag_sta']]) ? "" : $AGSTATUS[$monitor_data['ag_sta']];
                }
                /* 状态持续时长 'status_secs'*/
                $monitor_result[$monitor_data['ag_id']]["status_secs_int"] = empty($monitor_data['status_secs']) ? 0 : $monitor_data['status_secs'];
                $monitor_result[$monitor_data['ag_id']]["status_secs"]     = empty($monitor_data['status_secs']) ? 0 : $monitor_data['status_secs'];
            }
        }

 		return $monitor_result;
 	}

 	/**
 	 * 获取排队电话监控数据
 	 *
 	 * @param int  $vcc_id
 	 * @param int $que_id  队列ID
     * @return object
 	 */
 	public function calls_list($vcc_id = 0,$que_id = 0)
 	{
 		$responce = new stdClass();
 		$responce -> rows = array();

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();
        $params = array(
            'vcc_id' => $vcc_id,
            'que_id' => $que_id
        );
        $request = $client->post($api.'/api/monitor/calls', array(), $params);
        $response = $request->send()->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $data    = isset($response['data']) ? $response['data'] : array();

        if ($code == 200 && !empty($data)) {
            //得到所有队列信息
            $this->load->model("phone_control_model");
            $result_que = $this->phone_control_model->get_que_list();
            $queue_info = array();
            foreach ($result_que AS $key => $value)
            {
                if (!empty($value["que_id"]))
                {
                    $queue_info[$value["que_id"]] = $value["que_name"];
                }
            }

            $cle_phones  = array();
            $client_info = array();
            foreach ($data AS $key => $value)
            {
                if(!in_array($value['queuer_num'],$cle_phones))
                {
                    $cle_phones[] = $value['queuer_num'];
                }
            }

            $this->db_read->where_in('cle_phone', $cle_phones);
            $this->db_read->select('cle_phone,cle_name,cle_id,cle_stage,user_id');
            $query = $this->db_read->get('est_client');
            $cle_info = $query->result_array();

            if($cle_info)
            {
                $this->load->model("user_model");
                $user_result = $this->user_model->get_all_users();
                $user_info   = array();
                foreach ($user_result AS $user)
                {
                    $user_info[$user["user_id"]] = $user["user_name"];
                }

                foreach($cle_info AS $cle_key=>$client)
                {
                    $client['user_name'] = empty($user_info[$client['user_id']])?'':$user_info[$client['user_id']];
                    $client_info[$client['cle_phone']] = $client;
                }
            }

            foreach ($data AS $key => $value)
            {
                $cle_name = '';
                $cle_stage = '';
                $user_name = '';
                $responce -> rows[$key]["que_id"]     = $value['que_id'];
                /* 队列 */
                $responce -> rows[$key]["que_name"]   = empty($queue_info[$value['que_id']]) ? "" : $queue_info[$value['que_id']];
                /* 主叫号码 queuer_num*/
                $responce -> rows[$key]["queuer_num"] = empty($value['queuer_num']) ? "" : $value['queuer_num'];
                if(!empty($value['queuer_num']) && !empty($client_info[$value['queuer_num']]))
                {
                    $cle_name  = $client_info[$value["queuer_num"]]['cle_name'];
                    $cle_stage = $client_info[$value["queuer_num"]]['cle_stage'];
                    $user_name = $client_info[$value["queuer_num"]]['user_name'];
                }
                /*客户姓名*/
                $responce -> rows[$key]["cle_name"] = $cle_name;
                $responce -> rows[$key]["cle_stage"] = $cle_stage;
                $responce -> rows[$key]["user_name"] = $user_name;
                /* 进入队列时间 in_time*/
                $responce -> rows[$key]["in_time"]    = empty($value['in_time']) ? "" : date("Y-m-d H:i:s",$value['in_time']);
                /* 排队状态 queuer_sta*/
                $responce -> rows[$key]["queuer_sta"] = $value['queuer_sta'] == 1 ? "分配中" : "等待分配";
                /* 排队时长 in_secs*/
                $responce -> rows[$key]["in_secs"]    = empty($value['in_secs']) ? 0 : timeFormate($value['in_secs']);
            }

        }
 		return $responce;
 	}

 	/**
 	 * 获取技能组监控图表数据信息
 	 *
 	 * @return array
 	 */
 	public function get_monitor_system()
 	{
        $system_info = array();
        $vcc_code    = $this->session->userdata("vcc_code");

        $system_info['queue'] = 0;
        $system_info['ring']  = 0;
        $system_info['call']  = 0;
        $system_info['rest']  = 0;
        $system_info['ready'] = 0;
        $system_info['busy']  = 0;
        $system_info['max']   = 0;

        $this->config->load('myconfig');
        $api    = $this->config->item('api_wintelapi');
        $client = new Client();

        $request  = $client->get($api.'/api/monitor/system/'.$vcc_code);
        $response = $request->send()->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'signin' => false,
                'msg' => '解析结果出错，错误为【'.json_last_error().'】'
            );
        }
        $code    = isset($response['code']) ? $response['code'] : 0;
        $data    = isset($response['data']) ? $response['data'] : array();

        if ($code == 200) {
            if (!empty($data)) {
                $system_info['queue'] = $data['queue_nums'];
                $system_info['ring']  = $data['ring_nums'];
                $system_info['call']  = $data['call_nums'];
                $system_info['rest']  = $data['wait_nums'];
                $system_info['ready'] = $data['ready_nums'];
                $system_info['busy']  = $data['busy_nums'];
                $system_info['max']   = $data['queue_nums'];
            }
        }

        return $system_info;
 	}
 }