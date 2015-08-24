<?php 
class Address_book_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 通信录 - 列表检索条件
	 * @param array 检索内容
	 * @return array
	 * @author zgx
	 */
	private function get_address_book_condition($condition = array())
	{
		$wheres = array();
		if(!empty($condition['search_key']))
		{
			$wheres[] = "(tx_name LIKE '%".$condition['search_key']."%' OR q_pingyin LIKE '%".$condition['search_key']."%')";
		}

		$user_id = $this->session->userdata('user_id');
		if(!empty($condition['sql_type']))
		{
			switch($condition['sql_type'])
			{
				case 11://全部数据
				$wheres[] = "(tx_executor = '-1' OR tx_executor = ".$user_id.")";
				break;
				case 1://公司数据
				$wheres[] = "tx_executor = '-1'";
				break;
				case 2://我的数据
				$wheres[] = "tx_executor = ".$user_id;
				default:
					break;
			}
		}
		return $wheres;
	}


	/**
	 * 获取通讯录列表信息
	 * 
	 * @param array $condition 检索字段信息
	 * @param int $page 第几页
	 * @param int $limit 每页显示几条
	 * @param string $sort 根据哪个字段排序
	 * @param string $order 排序方式
	 * @return object responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [tx_id]=>10
 	 *        [tx_name] => 名称
	 *       	… 
	 *    )
	 *  ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_address_book_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_address_book_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_address_book');
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
		$_data = $this->db_read->get('est_address_book');
		$this->db_read->flush_cache();
		$responce -> rows = array();
		foreach($_data->result_array() AS $i=>$task)
		{
			$responce -> rows[$i] = $task;
		}
		return $responce;
	}

	/**
	 * 添加通讯录
	 * @param string $tx_name 名称
	 * @param string $tx_phone1 电话1
	 * @param string $tx_phone2 电话2
	 * @param string $tx_remark 备注
	 * @param int    $tx_type  类型：  1公司  2个人
	 * @return bool
	 * @author zgx
	 */
	public function insert_address_book($tx_name='',$tx_phone1='',$tx_phone2='',$tx_remark='',$tx_type = 0 )
	{
		if(empty($tx_type))
		{
			return false;
		}

		if($tx_type == 1)
		{
			$tx_executor = -1;//公司数据
		}
		else
		{
			$tx_executor = $this->session->userdata("user_id");
		}

		if(!empty($tx_name))
		{
			$b_pingyin = ctype_alnum($tx_name) ? $tx_name : pinyin($tx_name,TRUE);
			$q_pingyin = ctype_alnum($tx_name) ? $tx_name : pinyin($tx_name);
		}
		else
		{
			$b_pingyin = '';
			$q_pingyin = '';
		}

		$data = array(
		'tx_name'=>$tx_name,
		'tx_phone1'=>$tx_phone1,
		'tx_phone2'=>$tx_phone2,
		'tx_remark'=>$tx_remark,
		'tx_executor'=>$tx_executor,
		'b_pingyin'=>$b_pingyin,
		'q_pingyin'=>$q_pingyin
		);

		$result = $this->db_write->insert("est_address_book",$data);
		if(!$result)
		{
			return false;
		}
		else
		{
			return $this->db_write->insert_id();
		}
	}

	/**
	 * 获取一条通讯录的全部信息
	 * @param int $tx_id 通讯录id
	 * @return array
	 * <code>
	 * array(
	 * 		[tx_id] => 通讯录id
	 * 		[tx_name] => 名称
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_address_book_info($tx_id=0)
	{
		if($tx_id==0)
		{
			return '缺少参数';
		}
		$where = array('tx_id'=>$tx_id);
		$query = $this->db_read->get_where('est_address_book',$where,1);
		return $query->row_array();
	}

	/**
	 * 修改通讯录
	 * @param int $tx_id 通讯录id
	 * @param string $tx_name 名称
	 * @param string $tx_phone1 电话1
	 * @param string $tx_phone2 电话2
	 * @param string $tx_remark 备注
	 * @return bool
	 * @author zgx
	 */
	public function update_address_book($tx_id=0,$tx_name='',$tx_phone1='',$tx_phone2='',$tx_remark='')
	{
		if($tx_id==0)
		{
			return false;
		}
		if(!empty($tx_name))
		{
			$b_pingyin = ctype_alnum($tx_name) ? $tx_name : pinyin($tx_name,TRUE);
			$q_pingyin = ctype_alnum($tx_name) ? $tx_name : pinyin($tx_name);
		}
		else
		{
			$b_pingyin = '';
			$q_pingyin = '';
		}

		$data = array(
		'tx_name'=>$tx_name,
		'tx_phone1'=>$tx_phone1,
		'tx_phone2'=>$tx_phone2,
		'tx_remark'=>$tx_remark,
		'b_pingyin'=>$b_pingyin,
		'q_pingyin'=>$q_pingyin
		);
		$where = array('tx_id'=>$tx_id);

		$result = $this->db_write->update("est_address_book",$data,$where);
		if(!$result)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * 删除一通信录
	 * @param string $tx_id 通讯录id
	 * @return bool
	 * @author zgx
	 */
	public function delete_address_book($tx_id='')
	{
		if(empty($tx_id))
		{
			return '缺少参数';
		}
		$where = array('tx_id'=>$tx_id);
		$result = $this->db_write->delete('est_address_book',$where);
		if($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}
