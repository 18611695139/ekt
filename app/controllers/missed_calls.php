<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Missed_calls extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 未接来电
	 */
	public function index()
	{
		admin_priv('wdzswjldgl');

		//权限：未接来电(分配)
		$power_wjld_department = check_authz("power_wjld_department");
		$this->smarty->assign("power_wjld_department",$power_wjld_department?$power_wjld_department:0);

		//七天前
		$start_day = date("Y-m-d",strtotime("-7 day"));
		$end_day   = date("Y-m-d",strtotime("+7 day"));
		$this->smarty->assign('start_day',$start_day);
		$this->smarty->assign('end_day',$end_day);
		//
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：录音下载
		$power_download_record = check_authz("xtgllyxz");
		$this->smarty->assign("power_download_record",$power_download_record?$power_download_record:0);

		$this->smarty->display('missed_calls_list.htm');
	}

	public function missed_calls_query()
	{
		admin_priv('wdzswjldgl',false);

		$condition = $this->input->post();
		$this->load->model("missed_calls_model");
		$responce = $this->missed_calls_model->get_missed_calls_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/*分配未接来电*/
	public function deal_missed_calls()
	{
		admin_priv('power_wjld_department');

		$ids = $this->input->get('ids');
		$this->smarty->assign('ids',$ids);
		$this->smarty->display('missed_calls_deal.htm');
	}

	public function missed_calls_action()
	{
		admin_priv('power_wjld_department',false);

		$user_id = $this->input->post('user_id');
		$ids = $this->input->post('ids');

		$this->load->model('missed_calls_model');
		$result = $this->missed_calls_model->missed_calls_distribution($user_id,$ids);
		if ($result==1)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error($result);
		}
	}
	/*处理未接来电*/
	public function change_missed_calls_state()
	{
		admin_priv();

		$miss_id = $this->input->post('id');
		$this->load->model('missed_calls_model');
		$result = $this->missed_calls_model->change_missed_calls_state($miss_id);
		make_simple_response($result);
	}

	/**
	 * 批量分配未接来电
	 *
	 */
	public function missed_deployment_batch()
	{
		admin_priv('power_wjld_department');

		$inarray = $this->input->get();
		//搜索到的总数据
		$this->smarty->assign("batch_total",$inarray['total']);

		//搜索条件
		$this->load->library("json");
		$search_condition = $this->json->encode($inarray);
		$this->smarty->assign("search_condition",$search_condition);

		$this->smarty->display("missed_calls_batch.htm");
	}

	/**
	 * 未接来电 - 批量分配书数据 - 部门|员工
	 *
	 */
	public function deployment_batch_list()
	{
		admin_priv('power_wjld_department',false);

		$dept_id = $this->input->post('id');
		$this->load->model('missed_calls_model');
		$responce= $this->missed_calls_model->deployment_batch_query($dept_id);

		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 未接来电 - 分配数据
	 *
	 */
	public function missed_calls_batch_deployment()
	{
		admin_priv('power_wjld_department');

		$inarray = $this->input->post();
		//检索条件
		$search_condition = empty($inarray["search_condition"]) ? "" : $inarray["search_condition"];
		$this->load->library("json");
		$search_condition = $this->json->decode($search_condition,1);
		//分配总数
		$total_limit      = empty($inarray["total_limit"]) ? 0 : $inarray["total_limit"];
		//坐席 与 分配数量
		$assign_str       = empty($inarray["assign_str"]) ? "" : $inarray["assign_str"];

		//分配
		$this->load->model("missed_calls_model");
		$result = $this->missed_calls_model->missed_calls_batch_deployment($search_condition,$assign_str,$total_limit);
		if ($result)
		{
			if($result == '-1')
			{
				$result = 0;
			}
			make_json_result($result);
		}
		else
		{
			make_json_error("执行失败!");
		}
	}

    /**
     * 通过callid放回留言地址
     */
    public function get_voice_url()
    {
        $callid = $this->input->get('callid');

        $this->config->load('myconfig');
        $api = $this->config->item('api_recordapi');

        $vcc_id = $this->session->userdata("vcc_id");
        est_header('location:'.$api.'/api/voice/voice/'.$vcc_id.'/'.$callid );

    }
}