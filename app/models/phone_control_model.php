<?php 
class Phone_control_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 得到所有队列的信息
	 * @return array
	 */
	public function get_que_list()
	{
		if(  !$this->cache->get('all_queue_list'))
		{
			$this->db_read->select("que_id,que_name,que_type");
			$que_query = $this->db_read->get('est_queue');
			$que_list = $que_query->result_array();
			
			$this->cache->save('all_queue_list',$que_list,600);
		}
		else
		{
			$que_list = $this->cache->get('all_queue_list');
		}

		return $que_list;
	}

	/**
	 * 得到某个坐席的通话记录
	 *
	 * @param int $user_id 坐席id
	 * @return array
	 */
	public function get_user_call_list($user_id=0)
	{
		if(empty($user_id))
		{
			return '';
		}
		$this->load->model("callrecords_model");
		$result = $this->callrecords_model->get_user_callrecords_15_info($user_id); //获取某坐席最近的15条通话记录
		$callrecords = array();
		foreach ($result AS $item)
		{
			if (!empty($item["cus_phone"]))
			{
				$callrecords[$item["cus_phone"]] = $item["cus_phone"];
			}
		}
		return $callrecords;
	}

	/**
	 * 返回随机一个队列用于自动外呼
	 * 
	 * @return array|bool
	 *
	 */
	public function get_random_que()
	{
		$que_query = $this->db_read->get('est_queue',1);
		if ($que_query->num_rows() > 0)
		{
			$row = $que_query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 返回所有队列信息
	 *
	 * @return array|bool
	 */
	public function get_all_ques()
	{
		$query = $this->db_read->get('est_queue');
		if ($query->num_rows() > 0)
		{
			$row = $query->result_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

    public function get_queue_list_array($page=0, $limit=0, $sort=NULL, $order=NULL)
    {
        $this->db_read->select('count(*) as total',FALSE);
        $total_query = $this->db_read->get('est_queue');
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
        if ( !empty($select))
        {
            $this->db_read->select($select);
        }
        $start = get_list_start($total,$page,$limit);
        $this->db_read->limit($limit,$start);
        $rows = $this->db_read->get('est_queue');
        $responce -> rows = array();
        foreach($rows->result_array() AS $i=>$queue)
        {
            $responce -> rows[$i] = $queue;
        }
        return $responce;
    }
}