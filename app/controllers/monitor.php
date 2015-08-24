<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 系统监控
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : monitor.php
 * $Author: yhx
 * $time  : Mon Jan 07 13:58:05 CST 2013
*/
class Monitor extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Classname::queue()  队列监控
	 * 
	 * @return void 
	 */
	public function queue()
	{
		//得到所有队列信息
		/*$this->load->model("phone_control_model");
		$result_que = $this->phone_control_model->get_que_list();*/
		$ques       = array();
		/*foreach ($result_que AS $value)
		{
			if (!empty($value["que_id"]))
			{
				$ques[] = $value["que_id"].",".$value["que_name"];
			}
		}*/
		if ($ques)
		{
			$ques = implode("@",$ques);
		}
		else
		{
			$ques = "";
		}
		$this->smarty->assign("ques",$ques);

		$this->smarty->display("monitor_queue.htm");
	}

	/**
	 * 获取队列监控数据
	 *
	 */
	public function queue_list()
	{
		$vcc_id = $this->session->userdata("vcc_id");

        $this->load->model('phone_control_model');
        $result = $this->phone_control_model->get_queue_list_array();
        $ques       = array();
        foreach ($result->rows as $value)
        {
            if (!empty($value["que_id"]))
            {
                $ques[] = $value["que_id"].",".$value["que_name"];
            }
        }
        if ($ques)
        {
            $ques = implode("@",$ques);
        }
        else
        {
            $ques = "";
        }

        $this->load->model("monitor_model");
        $responce = $this->monitor_model->queue_list($vcc_id,$ques);
        $responce->total = $result->total;
        $this->load->library("json");
        echo $this->json->encode($responce);
	}

	/**
	 * 获得队列监控异步数据
	 *
	 */
	public function get_queue_monitor_data()
	{
		//企业ID
		$vcc_id = $this->session->userdata("vcc_id");
		//得到所有队列信息
		$ques = $this->input->get("ques");
		//队列数据
		$this->load->model("monitor_model");
		$responce = $this->monitor_model->queue_list($vcc_id,$ques);
		$que_info = array();
		if(!empty($responce->rows))
		{
			$responce = object_to_array($responce->rows);
			foreach($responce AS $que)
			{
				$que_info[$que['que_id']] = $que;
			}
		}
		make_json_result(1,"",$que_info);
	}

    /**
     * 技能组监控点击查看所在技能组坐席
     */
    public function queue_agent()
    {
        //技能组
        $que_id = $this->input->get("que_id");
        $this->smarty->assign("que_id",$que_id);

        $this->smarty->display("monitor_queue_agent.htm");
    }

    /**
     *
     */
    public function queue_agent_list()
    {
        $vcc_id = $this->session->userdata("vcc_id");
        $que_id = $this->input->get("que_id");
        if (empty($que_id)) {
           return false;
        }
        //获取该技能组坐席监控数据
        $this->load->model("monitor_model");
        $monitor_result = $this->monitor_model->get_agent_monitor_data($vcc_id,$que_id,'');

        $responce = new stdClass();
        $responce -> rows = array();
        $key = 0;
        foreach ($monitor_result as $i=>$agent) {
            $responce -> rows[$key] = $agent;
            $key++;
        }
        $this->load->library("json");
        echo $this->json->encode($responce);
    }

	/**
	 * 坐席监控
	 *
	 */
	public function agent()
	{
		//技能组
		$que_id = $this->input->get("que_id");
		$this->smarty->assign("que_id",$que_id);

		$this->smarty->display("monitor_agent.htm");
	}

	/**
	 * 坐席监控 - 列表数据（仅员工数据）
	 *
	 */
	public function agent_list_query()
	{
		//检索条件
		$condition = $this->input->post();
		//检索字段
		$select = array("user_id","user_name","user_num","dept_name");
		//分页
		$page	= $this->input->post("page");
		$page	= empty($page) ? 1 : $page;
		$limit	= $this->input->post("rows");
		$limit	= empty($limit) ? 10 : $limit;
		//员工数据
		$this->load->model('user_model');
		$responce = $this->user_model->get_user_list($condition,$select,$page,$limit,"user_last_login","DESC");
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 获得坐席监控的数据
	 *
	 */
	public function get_agent_monitor_data()
	{
		//企业ID
		$vcc_id = $this->session->userdata("vcc_id");
		//技能组
		$que_id = $this->input->post("que_id");
		//员工ID(多个ID用逗号分隔)
		$list_user_id = $this->input->post("list_user_id");

		//数据
		$this->load->model("monitor_model");
		$result = $this->monitor_model->get_agent_monitor_data($vcc_id,$que_id,$list_user_id);
		make_json_result(1,"",$result);
	}

	/**
	 * 座席监控 - 监听通话
	 *
	 */
	public function monitor_agent_chanspy()
	{
		$this->smarty->display("monitor_agent_chanspy.htm");
	}

	/**
	 * 排队监控
	 *
	 */
	public function calls()
	{
		$que_id = $this->input->get("que_id");
		$this->smarty->assign("que_id",$que_id);

        //客户基本可用字段信息
        $this->load->model('field_confirm_model');
        $client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
        if(isset($client_base['cle_stage']))
        {
            $this->smarty->assign("cle_stage",1);
        }
        else
        {
            $this->smarty->assign("cle_stage",0);
        }

		$this->smarty->display("monitor_calls.htm");
	}

	/**
	 * 获取排队电话监控数据
	 *
	 */
	public function calls_list()
	{
		$vcc_id = $this->session->userdata("vcc_id");

		//技能组
		$que_id = $this->input->get("que_id");

		//获取数据
		$this->load->model("monitor_model");
		$responce = $this->monitor_model->calls_list($vcc_id,$que_id);
		$this->load->library("json");
		echo $this->json->encode($responce);
	}

	/**
	 * 获得排队监控异步数据
	 *
	 */
	public function get_calls_monitor_data()
	{
		//企业ID
		$vcc_id = $this->session->userdata("vcc_id");
		//技能组
		$que_id = $this->input->get("que_id");

		$list_call_ids = $this->input->post("list_call_ids");
		$list_call_ids = explode(',',$list_call_ids);

		//获取数据
		$this->load->model("monitor_model");
		$responce = $this->monitor_model->calls_list($vcc_id,$que_id);
		$add_data = array();
		$update_data = array();
		$updata_que_ids = array();
		if(!empty($responce->rows))
		{
			$responce = object_to_array($responce->rows);
			foreach($responce AS $calls)
			{
				if(in_array($calls['que_id'],$list_call_ids))
				{
					$updata_que_ids[] = $calls['que_id'];
					$update_data[$calls['que_id']] = $calls;
				}
				else
				{
					$add_data[] = $calls;
				}
			}
		}
		$remove_que_ids = array_diff($list_call_ids,$updata_que_ids);
		foreach($remove_que_ids AS $remove)
		{
			$remove_que_ids[$remove] = $remove;
		}

		make_json_result(1,"",array('update_data'=>$update_data,'add_data'=>$add_data,'remove_data'=>$remove_que_ids));
	}

	
	/**
	 * 获取技能组监控图表数据信息
	 *
	 */
	public function get_monitor_system()
	{
		
		$this->load->model("monitor_model");
		$system_info = $this->monitor_model->get_monitor_system();
		make_json_result($system_info);
	}
}