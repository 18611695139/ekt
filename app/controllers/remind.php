<?php
/**
 * EasyTone 提醒
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : remind.php 
 * $Author: yhx
 * $time  : Fri Jun 29 16:43:45 CST 2012
*/
class Remind extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**  提醒列表
	 * Classname::index()
	 * 
	 * @return void 
	 */
	public function list_remind()
	{
		admin_priv('wdzswdtu');

		$this->smarty->display("remind_list.htm");
	}

	/**
	 * 翻译快捷搜索的 sql_type
	 *
	 * @param int $sql_type
     * @return array
     */
	private function translation_sql_type($sql_type)
	{
		$condition = array();
		switch ($sql_type)
		{
			case 2:{//今日提醒
				$today_start = date("Y-m-d 00:00:00");
				$today_end   = date("Y-m-d 23:59:59");
				$condition['rmd_time_start'] = $today_start;
				$condition['rmd_time_end'] = $today_end;
				break;
			}
			case 3:{//过期未处理提醒
				$yestoday =  date("Y-m-d 23:59:59",strtotime("-1 day"));
				$condition['rmd_time_start']='0000-00-00 00:00:00';
				$condition['rmd_time_end'] =  $yestoday;
				$condition['rmd_deal'] = 0;
				break;
			}
			case 4:{//7日内提醒
				$today_start = date("Y-m-d 00:00:00");
				$seven_day   = date("Y-m-d",strtotime("+7 days"))." 23:59:59";//7天后的时间
				$condition['rmd_time_start'] = $today_start;
				$condition['rmd_time_end'] =  $seven_day;
				break;
			}
			case 5:{//未处理
				$condition['rmd_deal'] = 0;
				break;
			}
			case 6:{//已处理
				$condition['rmd_deal'] = 1;
				break;
			}
			default:
				break;
		}
		return $condition;
	}

	/**   获取提醒列表数据
	 * Classname::remind_list_query()
	 * 
	 * @return void 
	 */
	public function remind_list_query()
	{
		admin_priv('wdzswdtu',false);

		$condition = $this->input->post();
		if(!empty($condition['sql_type']))
		{
			$condition_sql_type = $this->translation_sql_type($condition['sql_type']);
			$condition = array_merge($condition,$condition_sql_type);
		}
		$this->load->model('remind_model');
		$responce = $this->remind_model->get_remind_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 新建提醒
	 *
	 */
	public function new_remind()
	{
		admin_priv();

		//参数
		$rmd_param_in   = $this->input->get('rmd_param_in');
		$rmd_param_char = $this->input->get("rmd_param_char");
		//提醒时间
		$rmd_time       = $this->input->get('rmd_time');
		if ( empty($rmd_time) )
		{
			$rmd_time   = date("Y-m-d");
		}
		//提醒类型  0默认，自建提醒   1客户相关提醒
		$rmd_type       = $this->input->get("rmd_type");
		$rmd_type       = $rmd_type ? $rmd_type : 0;

		//短信提醒
		$user_id = $this->session->userdata('user_id');
		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($user_id);
		$user_sms_phone = $user_info['user_sms_phone'];

		$remind_info = array("rmd_param_in"=>$rmd_param_in,"rmd_param_char"=>$rmd_param_char,"rmd_time"=>$rmd_time,"rmd_type"=>$rmd_type,"user_sms_phone"=>$user_sms_phone);
		$this->smarty->assign("remind_info",$remind_info);

		$this->smarty->display('remind_info.htm');
	}

	/**
	 * 保存提醒信息
	 *
	 */
	public function insert_remind()
	{
		admin_priv();

		//参数
		$rmd_param_in   = $this->input->post('rmd_param_in');
		$rmd_param_char = $this->input->post('rmd_param_char');
		//提醒时间
		$rmd_time       = $this->input->post('rmd_time');
		//短信提醒、提醒号码
		$rmd_sendsms    = $this->input->post('rmd_sendsms');
		$user_sms_phone = $this->input->post("user_sms_phone");
		//提醒内容
		$rmd_remark     = $this->input->post('rmd_remark');
		//提醒类型  0默认，自建提醒   1客户相关提醒  2订单相关提醒
		$rmd_type       = $this->input->post("rmd_type");

		if($rmd_sendsms == 1)
		{
			$rmd_sendsms = true;
		}
		else
		{
			$rmd_sendsms = false;
		}

		$this->load->model('remind_model');
		$result = $this->remind_model->insert_remind($rmd_param_in,$rmd_param_char,$rmd_time,$rmd_remark,$rmd_type,$rmd_sendsms,$user_sms_phone);
		make_json_result($result);
	}

	/**
	 * 删除提醒
	 *
	 */
	public function delete_remind()
	{
		admin_priv();

		$rmd_ids = $this->input->post("rmd_ids");
		if ($rmd_ids )
		{
			$this->load->model("remind_model");
			$result = $this->remind_model->delete_remind($rmd_ids);
			if ($result)
			{
				make_json_result(1);
			}
			else
			{
				make_json_error('操作失败');
			}
		}
		else
		{
			make_json_error("缺少参数");
		}

	}

	/**
	 * 点击列表中的提醒内容，查看提醒信息
	 *
	 */
	public function view_remind_data()
	{
		admin_priv();
		
		$rmd_id = $this->input->get("rmd_id");
		if($rmd_id > 0)
		{
			$this->load->model("remind_model");
			$remind_info = $this->remind_model->get_remind_info($rmd_id);
			if ($remind_info["rmd_time"] == "0000-00-00 00:00:00")
			{
				$remind_info["rmd_time"] = "";
			}

			$this->smarty->assign("result",$remind_info);
			$this->smarty->display("remind_view_panel.htm");
		}
		else
		{
			$links[0]['text'] = "我的提醒";
			$links[0]['href'] = 'index.php?c=remind&m=list_remind';
			sys_msg('我的提醒，缺少参数',1,$links,FALSE);
		}
	}

	/**
	 * 标记提醒为已处理
	 *
	 */
	public function mak_remind_deal()
	{
		admin_priv();
		
		$rmd_id = $this->input->post("rmd_id");
		if(!$rmd_id)
		{
			make_json_error('缺少参数');
		}
		$this->load->model("remind_model");
		$result = $this->remind_model->mak_remind_deal($rmd_id);
		if ($result)
		{
			$action_type = $this->input->post('action_type');
			if(!$action_type || $action_type != 'dealRigntNow')
			{
				make_json_result(1);
			}
			else{
				//判断客户、订单是否已不存
				$this->load->model("remind_model");
				$remind_info = $this->remind_model->get_remind_info($rmd_id);
				if($remind_info['rmd_type']==1)
				{
					$this->load->model("client_model");
					$client_info = $this->client_model->get_client_info($remind_info['rmd_param_int']);
					if($client_info)
					make_json_result(1);
					else
					make_json_error('该客户已被删除');
				}
				else if($remind_info['rmd_type']==2)
				{
					$this->load->model('order_model');
					$order_info = $this->order_model->get_order_info($remind_info['rmd_param_int']);
					if($order_info)
					make_json_result(1);
					else
					make_json_error('该订单已被删除');
				}
				else
				make_json_result(1);
			}
		}
		else
		{
			make_json_error('处理失败');
		}
	}

}

/* End of file  remind.php*/
/* Location: ./app/controllers/remind.php */