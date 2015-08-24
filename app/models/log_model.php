<?php
class Log_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 记录 客户 est_client_log 日志
	 *
	 * @param string|array $content  日志内容
	 * @param int|array    $cle_id   客户ID
	 * @return bool
	 * @author zt
	 */
	public function write_client_log($content = "",$cle_id = 0)
	{
		if(empty($content))
		{
			return false;
		}
		$user_name = $this->session->userdata('user_name');
		$date_time  = date('Y-m-d H:i:s');
		$real_ip   = real_ip();

		if(!is_array($cle_id))
		{
			$cle_id = array($cle_id);
		}

		$data = array();
		foreach ($cle_id AS $key => $value)
		{
			$tmp_content = '';
			if(is_array($content) && !empty($content[$key]))
			{
				$tmp_content = $content[$key];
			}
			else
			{
				$tmp_content = $content;
			}
			$data[] = array(
			"log_time" => $date_time,
			"user_name" => $user_name,
			"user_ip" => $real_ip,
			"cle_id" => $value,
			"contents" => $tmp_content
			);
		}
		$this->db_write->insert_batch('est_client_log', $data);
		return true;
	}

	/**
	 * 获取客户相关est_client_log的日志
	 *
	 * @param $cle_id  客户ID
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=> array(
	 * 		[id]=> id
	 * 		[log_time]=> 插入时间戳
	 * 		[user_name]=> 员工姓名
	 * 		[user_ip]=> 员工ip
	 * 		[cle_id]=> 客户id
	 * 		[contents]=> 操作内容
	 * 	)
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_cle_log($cle_id = 0)
	{
		if(empty($cle_id))
		{
			return false;
		}
		$result = array();
		$this->db_read->where("cle_id",$cle_id);
		$this->db_read->select("id,log_time,user_name,user_ip,cle_id,contents");
		$this->db_read->order_by('id','desc');
		$query = $this->db_read->get("est_client_log");
		$result = $query->result_array();
		return $result;
	}

	/**
	 * 订单操作日志 
	 *
	 * @param string|array $content 日志内容
	 * @param int|array$order_id     订单id
	 * @return bool
	 * @author zt
	 */
	public function write_order_log($content='',$order_id=0)
	{
		if(empty($content) || empty($order_id))
		{
			return false;
		}
		$user_name = $this->session->userdata('user_name');

		$datetime      = date('Y-m-d H:i:s');
		$real_ip   = real_ip();

		if(!is_array($order_id))
		{
			$order_id = array($order_id);
		}

		$data = array();
		foreach ($order_id AS $key => $value)
		{
			$tmp_content = '';
			if(is_array($content) && !empty($content[$key]))
			{
				$tmp_content = $content[$key];
			}
			else
			{
				$tmp_content = $content;
			}
			$data[] = array(
			"log_time" => $datetime,
			"user_name" => $user_name,
			"user_ip" => $real_ip,
			"order_id" => $value,
			"contents" => $tmp_content
			);
		}
		
		$this->db_write->insert_batch('est_order_log', $data);
		return TRUE;
	}

	/**
	 * 获取订单相关的日志
	 *
	 * @param $order_id  订单ID
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=>array(
	 * 		[id]=> 日志id
	 * 		[log_time]=> 插入时间
	 * 		[user_name]=> 员工姓名
	 * 		[user_ip]=> 员工ip
	 * 		[cle_id]=> 客户id
	 * 		[cle_name]=> 客户名称
	 * 		[log_contents]=> 操作内容
	 * 	)
	 * 	...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_order_log($order_id = 0)
	{
		$result = array();
		if (!empty($order_id))
		{
			$this->db_read->where("order_id",$order_id);
			$this->db_read->select("id,log_time,user_ip,user_name,contents");
			$this->db_read->order_by('id','desc');
			$query = $this->db_read->get("est_order_log");
			$result = $query->result_array();
		}
		return $result;
	}
}