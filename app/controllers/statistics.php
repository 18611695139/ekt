<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 统计
 *
 */
class Statistics extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	///=========== 工作量统计 ======================================
	/**
	 * 统计树 - 列表树
	 */
	public function statistics_tree()
	{
		admin_priv('tjfxtjs');

		//客户基本可用字段信息
		$this->load->model('field_confirm_model');
		$client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
		$this->smarty->assign("client_base",$client_base);

		//数据字典信息
		$stages = array();
		if(isset($client_base['cle_stage']))
		{
			$this->load->model("dictionary_model");
			$dic_stage = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_STAGE);
			foreach($dic_stage as $value)
			{
				$value['stage_id'] = 'cle_stage_'.$value['id'];
				$stages[] = $value;
			}
		}
		$this->smarty->assign('stages',$stages);

		//默认显示当天的数据
		$this->smarty->assign('today_date',date("Y-m-d"));
		$this->smarty->display("statistics_dept_user_tree.htm");
	}

	/**
	 * 获取统计信息
	 *
	 */
	public function get_statistics_tree_info()
	{
		admin_priv('tjfxtjs',false);

		$deal_date_search_start = $this->input->post('state_time');
		$deal_date_search_end   = $this->input->post('end_time');
		$this->load->model("statistics_model");
		$dept_id = $this->input->post('id');
		$responce = $this->statistics_model->get_statistics_tree_info($dept_id,$deal_date_search_start,$deal_date_search_end);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 导出
	 */
	public function output_statistics_data()
	{
		set_time_limit(0);
		@ini_set('memory_limit', '1024M');

		//时间整理
		$state_time = $this->input->get('state_time');
		$end_time = $this->input->get('end_time');
		$timeFormate = $this->input->get('timeFormate');
		if($state_time == $end_time)
		{
			$date = $state_time;
		}
		else
		{
			$date = $state_time.'~'.$end_time;
		}

		//获取导出信息
		$this->load->model("statistics_model");
		$output_data = $this->statistics_model->get_output_statistics_data($state_time,$end_time,$timeFormate);

		$this->load->library("csv");
		$this->csv->creatcsv($date.'统计数据',$output_data);

       /* $fields = array();
        $data_info = array();
        if(isset($output_data[0]))
        {
            $fields = $output_data[0];
            unset($output_data[0]);
            foreach($output_data as $data)
            {
                $data_info[] = $data;
            }
        }
        $this->load->model('excel_export_model');
        $this->excel_export_model->export($data_info, $fields, '统计数据' . date("YmdHis"));*/
	}

	/**
	 * 获取坐席操作详情
	 */
	public function get_user_sta_detail()
	{
		admin_priv();
		$user_id = $this->input->get('user_id');
		$this->smarty->assign('user_id',$user_id);
		$start_time = $this->input->get('start_time');
		$this->smarty->assign('start_time',$start_time);
		$end_time = $this->input->get('end_time');
		$this->smarty->assign('end_time',$end_time);

		$this->smarty->display('statistics_sta_detail.htm');
	}

	public function get_sta_detail_query()
	{
		admin_priv();
		$user_id = $this->input->post('user_id');
		$start_time = $this->input->post('start_time');
		$end_time = $this->input->post('end_time');

		$condition = array('user_id'=>$user_id,'start_time'=>$start_time,'end_time'=>$end_time);

		$this->load->model('statistics_model');
		$responce = $this->statistics_model->get_sta_detail_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	///=========== 未接来电统计 ======================================
	/**
	 * 未接来电统计
	 */
	public function statistics_missed_calls()
	{
		admin_priv();

		//默认显示当天的数据
		$this->smarty->assign('today_date',date("Y-m-d"));

		$this->smarty->display('statistics_missed_calls.htm');
	}

	/**
	 * 获取未接来电统计信息
	 */
	public function get_statistics_missed_calls_query()
	{
		admin_priv();
		$condition = $this->input->post();

		$this->load->model('statistics_model');
		$responce= $this->statistics_model->get_statistics_missed_calls($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}
}
