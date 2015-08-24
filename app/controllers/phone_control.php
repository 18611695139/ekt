<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Guzzle\Http\Client;

class Phone_control extends CI_Controller {
	/**
	 * 拨号盘
	 *
	 */
	public function keypad_phone_control()
	{
		$this->smarty->display('phone_control_keypad.htm');
	}

	/**
	 * 呼叫坐席
	 *
	 */
	public function callinner_phone_control()
	{
		admin_priv();

		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$outcaller_type = $user_info['user_outcaller_type'];
		$outcaller_num = $user_info['user_outcaller_num'];
		$this->smarty ->assign("outcaller_type",$outcaller_type);
		$this->smarty ->assign("outcaller_num",$outcaller_num);
		$this->smarty->display('phone_control_callinner.htm');
	}

	/**
	 * 外呼
	 *
	 */
	public function callouter_phone_control()
	{
		admin_priv();

		//得到外呼号码
		$phone_num	= $this->input->get('phone_num');
		//外呼通道
		$outChan	= $this->input->get('outChan');
		if(empty($outChan))
		{
			$outChan = 0;
		}
		//外呼主叫
		$outCaller	= $this->input->get('outCaller');
		if(empty($outCaller))
		{
			$outCaller = '';
		}

		if(empty($outCaller))
		{
			$user_id = $this->session->userdata("user_id");
			$this->load->model('user_model');
			$user_info = $this->user_model->get_user_info($user_id);
			$outcaller_type = $user_info['user_outcaller_type'];
			$outcaller_num = $user_info['user_outcaller_num'];
		}
		else
		{
			$outcaller_type = 1;
			$outcaller_num = $outCaller;
		}
		$this->smarty->assign("phone_num",$phone_num);
		$this->smarty->assign("outcaller_type",$outcaller_type);
		$this->smarty->assign("outcaller_num",$outcaller_num);
		$this->smarty->assign("outChan",$outChan);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		$this->smarty->display('phone_control_callouter.htm');
	}

	/**
	 * 咨询坐席
	 *
	 */
	public function consultinner_phone_control()
	{
		admin_priv();

		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$outcaller_type = $user_info['user_outcaller_type'];
		$outcaller_num = $user_info['user_outcaller_num'];
		$this->smarty ->assign("outcaller_type",$outcaller_type);
		$this->smarty ->assign("outcaller_num",$outcaller_num);
		$this->smarty->display('phone_control_consultinner.htm');
	}

	/**
	 * 咨询外线
	 *
	 */
	public function consultouter_phone_control()
	{
		admin_priv();

		//通话记录
		$user_id = $this->session->userdata('user_id');
		$this->load->model('phone_control_model');
		$call_records = $this->phone_control_model->get_user_call_list($user_id);//通话记录
		$this->smarty->assign("call_records",$call_records);

		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$outcaller_type = $user_info['user_outcaller_type'];
		$outcaller_num = $user_info['user_outcaller_num'];
		$this->smarty->assign("outcaller_type",$outcaller_type);
		$this->smarty->assign("outcaller_num",$outcaller_num);
		$this->smarty->display('phone_control_consultouter.htm');
	}

	/* 获取电话归属地*/
	public function phone_area_info()
	{
		admin_priv();

		$phone_num = $this->input->post('number');
		//如果号码等于7位或者8位 则加上本地区号的前缀。
		if(strlen($phone_num) == 7 || strlen($phone_num) == 8)
		{
			$this->load->config('myconfig');
			$local_code = $this->config->item('local_code');
			$phone_num = $local_code.$phone_num;
		}

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();

        $request = $client->get($api.'/api/common/getnumberloc/'.$phone_num);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            make_json_error('解析结果出错，错误为【'.json_last_error().'】');
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data    = isset($response['data']) ? $response['data'] : array();

		if($code == 200)
		{
			make_json_result('','',$data);
		}
		else
		{
			make_json_error($message);
		}
	}

	/**
	 * 处理号码 去除本地号码前缀  加上外地手机前缀
	 */
	public function dealnumber()
	{
		admin_priv();

		$phone_num = $this->input->post('number');
		$this->load->model('phone_location_model');
		$phone_num = $this->phone_location_model->remove_prefix_zero($phone_num);

		$this->load->config('myconfig');
		$local_code = $this->config->item('local_code');
		if(strlen($phone_num) == 8 || strlen($phone_num) == 7)
		{
			$phone_num = $local_code.$phone_num;
		}

        $this->config->load('myconfig');
        $api = $this->config->item('api_wintelapi');
        $client = new Client();

        $request = $client->get($api.'/api/common/getnumberloc/'.$phone_num);
        $response = $request->send()->json();
        if (json_last_error() !== JSON_ERROR_NONE) {
            make_json_error('解析结果出错，错误为【'.json_last_error().'】');
        }

        $code    = isset($response['code']) ? $response['code'] : 0;
        $message = isset($response['message']) ? $response['message'] : '';
        $data    = isset($response['data']) ? $response['data'] : array();

        if($code == 200)
        {
            $code = $data['code'];
            $type = $data['type'];
            $mobile_prefix = $this->config->item('mobile_prefix');
            if($type == 'MOBILE' && $code != $local_code)
            {
                $dealed_number = $mobile_prefix.$phone_num;
            }
            else if($type == 'TEL' && $code == $local_code)
            {
                //去除开始处的区号
                if(stripos($phone_num, $local_code) === 0)
                {
                    $dealed_number = substr($phone_num,strlen($local_code));
                }
            }
            else
            {
                $dealed_number = $phone_num;
            }
            make_json_result($dealed_number);
        }
        else
        {
            make_json_error($message);
        }
	}

    /**
     * 返回一个当前坐席不在的技能组
     */
    public function get_one_other_que()
    {
        /*$ques = $this->input->get('que');
        if(empty($ques))
        {
            die('当前坐席没在任何队列中');
        }
        $ques = json_decode($ques,true);*/
        $this->load->model('phone_control_model');
        $que_list = $this->phone_control_model->get_que_list();//当前企业所有技能组
        $other_ques = array();
        foreach($que_list as $k=>$v)
        {
            //if(isset($v['que_id']) && !in_array($v['que_id'],$ques))
            if(isset($v['que_id']))
            {
                $other_ques[] = $v;
            }
        }
        if(empty($other_ques))
        {
            //die('除去当前坐席所在技能组外，没有其他技能组');
            die('没有技能组');
        }
        $this->smarty->assign('other_ques',$other_ques);
        $this->smarty->display('phone_control_transque.htm');
    }
}

/* End of file  phone_control.php*/
/* Location: ./app/controllers/phone_control.php */