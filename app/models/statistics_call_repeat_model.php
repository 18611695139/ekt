<?php

class Statistics_call_repeat_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 检索条件
	 *
	 */
	private function get_repeat_codition($condition=array())
	{
		$wheres = array();
		//时间
		if (!empty($condition["start_time"]))
		{
			$wheres[] = "start_time >= '".strtotime($condition["start_time"])."'";
		}
		if (!empty($condition["end_time"]))
		{
			$wheres[] = "start_time <= '".strtotime($condition["end_time"])."'";
		}
		//通话类型
		if(!empty($condition["call_type"]) && is_numeric($condition["call_type"]))
		{
			$wheres[] = "call_type = ".$condition["call_type"];
		}
		/*电话*/
		$wheres[] = "cus_phone !=0";
		return $wheres;
	}

	/**
	 * 获通话重复统计列表数据
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param string $sort
	 * @param string $order
	 * @return object
	 */
	public function get_call_repeat_info($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $CDR_CALL_TYPE;//通话记录-通话类型
		
		$wheres = $this->get_repeat_codition($condition);
		$where = implode(" AND ",$wheres);
		
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		
		$responce = new stdClass();
		$responce->total = 0;
		$responce->rows = array();
		
		//获取重复电话的数量
		$query = $this->db_read->query("SELECT count(*),cus_phone FROM est_cdr WHERE $where GROUP BY $sort HAVING count( * ) >1");
		$total = $query->num_rows();
		$responce->total = $total;
		$total_pages = ceil($total / $limit);
		$page = $page > $total_pages ? $total_pages : $page;
		$start = $limit * $page - $limit;
		$start = $start > 0 ? $start : 0;
		//获取数据
		$rows = $this->db_read->query("SELECT count( * ) as num ,cus_phone,call_type,sum(if(result=0,1,0)) as through FROM est_cdr WHERE $where GROUP BY $sort $order HAVING count( * ) >1 LIMIT $start,$limit");
		foreach ($rows->result_array() AS $i => $v) 
		{
			$v['ratio'] = round($v['through'] / $v['num'] * 100, 2) . "%";
			$v['call_type'] = empty($CDR_CALL_TYPE[$v["call_type"]]) ? "" : $CDR_CALL_TYPE[$v["call_type"]]; //通话类型
			
			$responce->rows[$i] = $v;
		}
		return $responce;
	}
	
	/**
	 * 导出
	 */
	public function export_repeat($condition=array())
	{
		set_time_limit(0);
		@ini_set('memory_limit', '1024M');
		
		$data = array();
		$data[0] = array('时间','号码','通话类型','拨打次数','接通数','比例');
		$start_time = isset($condition['start_time'])?$condition['start_time']:'';
		$end_time = isset($condition['end_time'])?$condition['end_time']:'';
		//数据
		$total     = empty($condition["total"]) ? 0 : $condition["total"];
		$sortName  = empty($condition["sortName"]) ? "cus_phone" : $condition["sortName"];
		$sortOrder = empty($condition["sortOrder"]) ? "DESC" : $condition["sortOrder"];
		$responce = $this->get_call_repeat_info($condition,1,$total,$sortName,$sortOrder);
		$repeat_info = $responce->rows;
		foreach($repeat_info as $key=>$v)
		{
			$data[] = array($start_time.'~'.$end_time,$v['cus_phone'],$v['call_type'],$v['num'],$v['through'],$v['ratio']);
		}
		$this->load->library("csv");
		$this->csv->creatcsv('通话重复号码统计',$data);
	}
}