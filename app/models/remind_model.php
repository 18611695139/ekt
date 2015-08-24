<?php
/**
 * 提醒类
 *
 */
class Remind_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	/**  提醒检索条件
	 * 
	 * @param  array $condition 检索内容
	 * @return array $wheres
	 * 
	 * @author zgx
	 */
	private function get_remind_condition($condition = array())
	{
		$wheres = array();
		//用户
		$user_id = $this->session->userdata("user_id");
		$wheres[] = "user_id = $user_id";

		//提醒时间
		if(!empty($condition['rmd_time_start']))
		{
			$wheres[] = "rmd_time >= '".$condition['rmd_time_start']."'";
		}
		if(!empty($condition['rmd_time_end']))
		{
			$wheres[] = "rmd_time <= '".$condition['rmd_time_end']."'";
		}
		//提醒状态
		if(isset($condition['rmd_deal']))
		{
			$wheres[] = "rmd_deal = ".$condition['rmd_deal'];
		}
		return array_unique($wheres);
	}

	/**
	 * 获取提醒列表信息
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
	 *        [rmd_id]=>10
 	 *        [rmd_type] => 提醒类型
	 *       	… 
	 *    )
	 *  ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_remind_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_remind_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_remind');
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
		$remind_data = $this->db_read->get('est_remind');
		$this->db_read->flush_cache();//清除缓存
		$responce -> rows = array();
		foreach($remind_data->result_array() AS $i=>$task)
		{
			$responce -> rows[$i] = $task;
		}
		return $responce;
	}

	/**
	 * 返回某提醒信息
	 *
	 * @param int $rmd_id  提醒ID
	 * @return array
	 * <code>
	 * array(
	 * 		[rmd_id]=>1,
	 * 		[rmd_param_int]=> int型参数,
	 * 		...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_remind_info($rmd_id=0)
	{
		$result =array();
		if(empty($rmd_id))
		{
			return $result;
		}
		$this->db_read->where('rmd_id',$rmd_id);
		$query = $this->db_read->get("est_remind");
		return $query->row_array();
	}

	/**
	 * 添加新题型
	 *
	 * @param int      $rmd_param_int    int型参数 我的提醒0、客户id 、订单id
	 * @param string   $rmd_param_char  varchar型参数 提醒类型中文（我的提醒、客户:客户名称、订单:订单编号）
	 * @param string $rmd_time        提醒时间
	 * @param string     $rmd_remark      提醒内容
     * @param int   $rmd_type
	 * @param bool    $rmd_sendsms     是否短信提醒
	 * @param string   $user_sms_phone  短信提醒号码 
	 * @return int 返回添加提醒id
	 * 
	 * @author zgx
	 */
	public function insert_remind($rmd_param_int = 0,$rmd_param_char = '',$rmd_time = '',$rmd_remark = '',$rmd_type = 0,$rmd_sendsms = false,$user_sms_phone = '')
	{
		$user_id = $this->session->userdata('user_id');
		$user_name = $this->session->userdata('user_name');
		$dept_id = $this->session->userdata('dept_id');

		$data = array(
		"rmd_param_int" => empty($rmd_param_int) ? 0 : $rmd_param_int,
		"rmd_param_char" => empty($rmd_param_char) ? "" : $rmd_param_char,
		"rmd_type" => empty($rmd_type) ? 0 : $rmd_type,
		"rmd_time" => $rmd_time,
		"user_id" => $user_id,
		"user_name" => $user_name,
		"rmd_create_time" => date('Y-m-d H:i:s'),
		"rmd_remark" => $rmd_remark,
		"rmd_sendsms" => $rmd_sendsms,
		"dept_id" => $dept_id
		);

		//保存提醒信息
		$this->db_write->insert('est_remind',$data);
		$rmd_id = $this->db_write->insert_id();
		//弹框信息写入
		$this->load->model("notice_model");
		$this->notice_model->write_notice('remind',$user_id,$user_name,$rmd_remark,strtotime($rmd_time),0,$rmd_id);
		//是否短信提醒   rmd_sendsms
		if ( $rmd_sendsms && $user_sms_phone )
		{
			//发短信
			$this->load->model("sms_model");
			$this->sms_model->send_sms($user_sms_phone,$rmd_remark,strtotime($rmd_time));
		}
		return $rmd_id;
	}

	/**
	 * 删除指定提醒
	 *
	 * @param string $rmd_ids  提醒ID
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function delete_remind($rmd_ids='')
	{
		if(empty($rmd_ids))
		{
			return false;
		}

		$user_id      = $this->session->userdata("user_id");
		//删除弹框消息
		$this->load->model("notice_model");
		$remind_ids = explode(',',$rmd_ids);
		foreach($remind_ids AS $id)
		{
			$this->notice_model->remove_notice($user_id,'remind',$id);
		}
		return $this->db_write->query("DELETE FROM est_remind WHERE rmd_id IN(".$rmd_ids.")");
	}

	/**
	 * 标记为已处理
	 *
	 * @param int $rmd_id  提醒ID
	 * @return bool 执行结果
	 * 
	 * @author zgx
	 */
	public function mak_remind_deal($rmd_id=0)
	{
		return $this->db_write->query("UPDATE est_remind SET rmd_deal=1,rmd_deal_time = '".date("Y-m-d H:i:s")."' WHERE rmd_deal = 0 AND rmd_id IN ($rmd_id)");
	}

	/**
	 * 工作桌面 - 我的提醒
	 * 
	 * @return array
	 * <code>
	 * array(
	 * 	[1] => array(
	 * 		[rmd_id]=>1,
	 * 		[rmd_param_int]=> int型参数,
	 * 		[rmd_param_char]=> varchar型参数,
	 * 		[rmd_remark]=> 提醒内容，
	 * 		[rmd_time]=> 提醒时间
	 * 	)
	 * 	[2] => array(
	 * 		[rmd_id]=>1,
	 * 		[rmd_param_int]=> int型参数,
	 * 		[rmd_param_char]=> varchar型参数,
	 * 		[rmd_remark]=> 提醒内容，
	 * 		[rmd_time]=> 提醒时间
	 * 	)
	 * 	[3] => array(
	 * 		[rmd_id]=>1,
	 * 		[rmd_param_int]=> int型参数,
	 * 		[rmd_param_char]=> varchar型参数,
	 * 		[rmd_remark]=> 提醒内容，
	 * 		[rmd_time]=> 提醒时间
	 * 	)
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function remind_workbench()
	{
		$today_start  = date('Y-m-d 00:00:00');
		$today_end    = date('Y-m-d 23:59:59');//今天晚上的时间
		$sevenday     = date("Y-m-d",strtotime("+7 days"))." 23:59:59";//七天后的时间
		$user_id      = $this->session->userdata("user_id");

		$action = array();
		$action[1] ="";//过期
		$action[2] ="";//今天任务
		$action[3] ="";//7天内任务

		//过期
		$query        = $this->db_read->query("SELECT rmd_id,rmd_param_int,rmd_param_char,rmd_remark,rmd_time FROM est_remind WHERE rmd_deal = 0 AND user_id = ".$user_id."  AND  rmd_time < '".$today_start."' limit 10");
		$Expiredtime  = $query->result_array();
		if(!empty($Expiredtime))
		{
			$num_e = count($Expiredtime);
			if($num_e>3)
			{
				$num_e = 3;
			}
			for($i=0;$i<$num_e;$i++)
			{
				$action[1][] = $Expiredtime[$i];
			}
		}
		else
		{
			foreach($Expiredtime as $v)
			$action[1][] = $v;
		}

		//今天任务
		$query      = $this->db_read->query("SELECT rmd_id,rmd_param_int,rmd_param_char,rmd_remark,rmd_time FROM est_remind WHERE rmd_deal = 0 AND user_id = ".$user_id."  AND  '".$today_start."' <= rmd_time AND rmd_time <= '".$today_end."' limit 10");
		$Todaytime  = $query->result_array();
		if(!empty($Todaytime))
		{
			$num_t = count($Todaytime);
			if($num_t>5)
			{
				$num_t = 5;
			}
			for($j=0;$j<$num_t;$j++)
			{
				$action[2][] = $Todaytime[$j];
			}
		}
		else
		{
			foreach($Todaytime as $v)
			$action[2][] = $v;
		}

		//7天内任务
		$query      = $this->db_read->query("SELECT rmd_id,rmd_param_int,rmd_param_char,rmd_remark,rmd_time FROM est_remind WHERE rmd_deal = 0 AND user_id = ".$user_id."  AND  '".$today_end."' < rmd_time AND rmd_time <= '".$sevenday."' limit 10");
		$Seventime  = $query->result_array();
		if(!empty($Seventime))
		{
			$num_s = count($Seventime);
			if($num_s>5)
			{
				$num_s = 5;
			}
			for($k=0;$k<$num_s;$k++)
			{
				$action[3][] = $Seventime[$k];
			}
		}
		else
		{
			foreach($Seventime as $v)
			$action[3][] = $v;
		}
		return $action;
	}
}