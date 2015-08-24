<?php
class Contact_record_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取检索条件
	 * 
	 * @param array $condition 检索内容
	 * @return array $wheres
	 * @author zgx
	 */
	private function get_contact_record_condition($condition = array())
	{
		$wheres = array();
		if(!empty($condition['cle_id']))
		{
			$wheres[] = "cle_id = ".$condition['cle_id'];
		}
		if(!empty($condition['order_id']))
		{
			$wheres[] = "order_id = ".$condition['order_id'];
		}
		return $wheres;
	}

	/**
	 *业务受理 - 过往联系记录
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
	 *        [con_rec_id]=>10
 	 *        [con_rec_content] => 联系内容
	 *       	… 
	 *    )
	 * ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public  function get_contact_record_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		global $CDR_CALL_TYPE;//通话记录-通话类型

		$wheres = $this->get_contact_record_condition($condition);
		$where = implode(" AND ",$wheres);

		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_contact_record');
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
		$query = $this->db_read->get('est_contact_record');
		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息
		$_record_data = $query->result_array();

		if(!empty($_record_data))
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result as $user_msg)
			{
				$user_info[$user_msg["user_id"]] = $user_msg["user_name"];       //坐席名称
			}
			foreach($_record_data AS $i=>$value)
			{
				$value["call_type"]  = empty($CDR_CALL_TYPE[$value["call_type"]]) ? "" : $CDR_CALL_TYPE[$value["call_type"]]; //通话类型
				$value["conn_secs"]  = timeFormate($value["conn_secs"]);//通话时长

				$value["user_name"] = empty($user_info[$value["user_id"]]) ? '' : $user_info[$value["user_id"]];
				$responce -> rows[$i] = $value;
			}
		}

		return $responce;
	}

	/**
	 * 得到一条联系记录
	 *
	 * @param int $con_rec_id 联系记录ID
	 * @return array
	 * <code>
	 * array(
	 * 			[cle_id]=> 客户ID
	 * 			[order_id]=> 订单ID
	 * 			[callid]=> 录音id
	 * 			[con_rec_id]=> 联系记录id
	 * 			[con_rec_content]=> 联系内容
	 * 			[user_id]=> 创建人id
	 * 			[con_rec_next_time]=> 下次联系时间
	 * 			[conn_secs]=> 通话时长
	 * 			[call_type]=> 通话类型
	 * 			[con_rec_time]=> 联系时间
	 * 		)
	 * </code>
	 * @author zgx
	 */
	public function get_contact_record_info($con_rec_id = 0)
	{
		$contact_record = array();
		if ($con_rec_id)
		{
			$this->db_read->where("con_rec_id",$con_rec_id);
			$query  = $this->db_read->get("est_contact_record");
			$contact_record = $query->row_array();
		}
		return $contact_record;
	}

	/**
	 * 获取过往联系记录详情（客户）
	 * 
	 * @param int $cle_id 客户id
	 * @return bool/array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 			[callid]=> 录音id
	 * 			[con_rec_id]=> 联系记录id
	 * 			[con_rec_content]=> 联系内容
	 * 			[user_id]=> 创建人id
	 * 			[con_rec_next_time]=> 下次联系时间
	 * 			[conn_secs]=> 通话时长
	 * 			[call_type]=> 通话类型
	 * 			[con_rec_time]=> 联系时间
	 * 		)
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function contact_record_detail($cle_id=0)
	{
		if(empty($cle_id))
		{
			return false;
		}
		global $CDR_CALL_TYPE;//通话记录-通话类型
		$query = $this->db_read->query("SELECT callid,con_rec_id,con_rec_content,user_id,con_rec_next_time,conn_secs,call_type,con_rec_time FROM est_contact_record WHERE cle_id = ".$cle_id."  ORDER BY con_rec_time desc");
		$contact_detail = array();
		if($query)
		{
			$contact_info = $query->result_array();
			foreach($contact_info AS $value)
			{
				$value["call_type_name"]  = empty($CDR_CALL_TYPE[$value["call_type"]]) ? "" : $CDR_CALL_TYPE[$value["call_type"]]; //通话类型
				$value["conn_secs"]  = timeFormate($value["conn_secs"]);//通话时长
				if(!empty($value["con_rec_content"]))
				{
					$contact_detail[] = $value;
				}
			}
		}
		return $contact_detail;
	}

	/**
	* 插入联系记录
	*
	* @param int $callid 录音ID
	* @param string $con_rec_content 联系内容
	* @param string $con_rec_next_time 下次联系时间
	* @param int $cle_id 客户ID
	* @param int $order_id 订单ID
	* @return bool
	* @author zgx
	*/
	public function insert_contact_record($callid=0,$con_rec_content='',$con_rec_next_time='',$cle_id=0,$order_id=0)
	{
		if(empty($cle_id) && empty($order_id))
		{
			return false;
		}

		$user_id = $this->session->userdata("user_id");

		$data = array(
		"con_rec_content" => $con_rec_content,
		"con_rec_next_time" => $con_rec_next_time,
		"callid" => $callid,
		"user_id" => $user_id,
		"con_rec_time" => date("Y-m-d H:i:s")
		);
		if(!empty($cle_id))
		{
			//客户信息
			$data['cle_id'] = $cle_id;
		}
		if(!empty($order_id))
		{
			$data['order_id'] = $order_id;
		}
		if($callid > 0)
		{
			$query = $this->db_read->get_where('est_contact_record',array('callid'=>$callid));
			if ($query->num_rows() > 0)
			{
				$result = $this->db_write->update("est_contact_record",$data,array('callid'=>$callid));
				return $result;
			}
		}

		$result = $this->db_write->insert("est_contact_record",$data);
		return $result;
	}
}
