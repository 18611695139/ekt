<?php
class Callrecords_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**  通话记录-通话结果 */
	private $CALL_RESULT = array(0=>'接通',1=>'自动外呼呼损',2=>'未接通');
	/*挂断原因*/
	private $CALL_ENDRESULT = array(0=>'',1=>'振铃放弃',2=>'未接',11=>'用户挂机',12=>'坐席挂断',13=>'转接',14=>'拦截');
	/**
	 * 通话记录-检索条件
	 * 
	 * @param array $condition 检索内容
	 * @return array 返回检索条件数组
	 * @author zgx
	 */
	private function get_callrecords_condition($condition = array())
	{
		$wheres  = array();
		//客户电话
		if (!empty($condition["cus_phone"]))
		{
			$wheres[] = "cus_phone LIKE '%".$condition["cus_phone"]."%'";
		}
		//坐席ID
		if (!empty($condition["user_id"]))
		{
			$wheres[] = "user_id = ".$condition["user_id"];
		}
		//时间
		if (!empty($condition["start_date"]))
		{
			$wheres[] = "start_time >= '".strtotime($condition["start_date"])."'";
		}
		if (!empty($condition["end_date"]))
		{
			$wheres[] = "start_time <= '".strtotime($condition["end_date"])."'";
		}
		//通话时长
		if (!empty($condition["conn_secs"]))
		{
			$wheres[] = "conn_secs > ".$condition["conn_secs"];
		}
		if (!empty($condition["record_cle_id"]) )
		{
			$wheres[] = "cle_id = ".$condition["record_cle_id"];
		}
		//权限：通话记录（全部数据）
		$power_callrecord = check_authz("power_callrecord");
		if($power_callrecord!=1)
		{
			//数据权限
			$wheres[] = data_permission();
		}

		return $wheres;
	}

	/**
	 * 通话记录-数据列表
	 *
	 * @param array $condition 检索字段信息
	 * @param int $page 第几页
	 * @param int $limit 每页显示几条
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @return object responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [id]=>10
 	 *        [con_sec] => 时长
	 *       	… 
	 *    )
	 * ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public  function get_callrecords_list($condition = array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $CDR_CALL_TYPE;//通话记录-通话类型
		$CALL_RESULT = $this->CALL_RESULT;
        $CALL_ENDRESULT = $this->CALL_ENDRESULT;

		$wheres = $this->get_callrecords_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_cdr');
		$total = $total_query->row()->total;
		$responce = new stdClass();
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$cdr_data = $this->db_read->get('est_cdr');
		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息
		if($cdr_data)
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user_result as $user_msg)
			{
				$user_info[$user_msg["user_id"]]["user_name"]  = $user_msg["user_name"];       //坐席名称
			}

			foreach($cdr_data->result_array() AS $i=>$callrecord)
			{
				$callrecord["callid"]     = $callrecord["call_id"];//听、下载录音
				$callrecord["call_type"]  = empty($CDR_CALL_TYPE[$callrecord["call_type"]]) ? "" : $CDR_CALL_TYPE[$callrecord["call_type"]]; //通话类型
				$callrecord["start_time"] = $callrecord["start_time"] ? date("Y-m-d H:i:s",$callrecord["start_time"]) : ""; //开始时间
				$callrecord["conn_secs"]  = timeFormate($callrecord["conn_secs"]);//通话时长
				$callrecord["result"]     = empty($CALL_RESULT[$callrecord["result"]]) ? "" : $CALL_RESULT[$callrecord["result"]];//通话结果
                //挂断原因
                if (isset($callrecord["endresult"])) {
                    $callrecord["endresult"] = empty($CALL_ENDRESULT[$callrecord["endresult"]]) ? "" : $CALL_ENDRESULT[$callrecord["endresult"]];
                } else {
                    $callrecord["endresult"] = "-";
                }
				if ($callrecord["user_id"])
				{
					$callrecord["user_name"]  = empty(   $user_info[$callrecord["user_id"]]["user_name"]   ) ? "" : $user_info[$callrecord["user_id"]]["user_name"];
				}
				$responce -> rows[$i] = $callrecord;
			}
		}
		return $responce;
	}



	/**
	 * 获取录音格式信息 
	 */
	public function get_pro_record_role($callid)
	{
		$pro_record = '';
		return $pro_record;
	}

	/**
	 * 获取30天内已经呼叫过的号码
	 * 
	 * @return array 返回号码的一维数组
	 * @author zgx
	 *
	 */
	public function get_all_called_number_in_30days()
	{
		$nums = array();
		$this->db_read->select('distinct(cus_phone)');
		$time_now = time();
		$time_start = $time_now - 30 * 24 * 60 * 60;
		$this->db_read -> where('start_time >',$time_start);
		$number_query = $this->db_read->get('est_cdr');
		$nums_temp = $number_query->result_array();
		foreach ($nums_temp as $num_item)
		{
			$nums[$num_item['cus_phone']] = 1;
		}
		unset($nums_temp);
		if(!empty($nums))
		{
			ksort($nums);
		}
		return $nums;
	}

	/**
	 * 获取某坐席最近的15条通话记录
	 *
	 * @param int $user_id 员工id
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[call_id]=> 录音id
	 * 		[user_id]=> 坐席id
	 * 		[dept_id]=> 部门id
	 * 		[call_type]=> 通话类型
	 * 		[conn_secs]=> 通话时长
	 * 		[cus_phone]=> 客户电话
	 * 		...
	 * 	)
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_user_callrecords_15_info($user_id=0)
	{
		if (!empty($user_id))
		{
			$this->db_read->where(array('user_id'=>$user_id));
		}
		$this->db_read->order_by("start_time","DESC");
		$query = $this->db_read->get("est_cdr",15);
		return $query->result_array();
	}

	/**
	 * 获取半小时内呼通过的电话号码
	 */
	public function get_phone_on_half_hour()
	{
		$half_hour = time()-(60*30);//半小时前时间戳
		$query = $this->db_read->query("SELECT cus_phone FROM est_cdr WHERE start_time>='".$half_hour."' AND result=0 AND conn_secs>0 AND cus_phone!=''");
		$phones = array();
		foreach($query->result_array() as $phone)
		{
			if(!in_array($phone['cus_phone'],$phones))
			{
				$phones[] = $phone['cus_phone'];
			}
		}
		return $phones;
	}
}