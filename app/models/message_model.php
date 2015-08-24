<?php 
class Message_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取检索条件
	 *
	 * @param array $condition 检索内容
	 * @return array 返回检索条件的一维数组
	 */
	private function get_message_condition($condition = array())
	{
		$wheres = array();
		//收件箱
		if(!empty($condition['msg_receive_user_id']))
		{
			$wheres[] = "msg_receive_user_id = ".$condition['msg_receive_user_id'];
		}
		//发件箱
		if(!empty($condition['msg_send_user_id']))
		{
			$wheres[] = "msg_send_user_id = ".$condition['msg_send_user_id'];
		}
		//0未读 1已读
		if(isset($condition['msg_if_readed']))
		{
			$wheres[] = "msg_if_readed = ".$condition['msg_if_readed'];
		}

		if(!empty($condition['search_key']))
		{
			$wheres[] = "(msg_content LIKE '%".$condition["search_key"]."%' OR msg_send_user_name LIKE '%".$condition["search_key"]."%')";
		}

		return array_unique($wheres);
	}
	/**
	 * 消息管理 - 获取收件箱/发件箱数据
	 *
	 * @param array $where 检索字段信息
	 * @param int $page 第几页
	 * @param int $limit 每页显示几条
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @return obj responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [msg_id]=>10
 	 *        [msg_send_user_name] => 发送人姓名
	 *        … 
	 *    )
	 * ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_message_list($condition=array(),$page='', $limit='', $sort=NULL, $order=NULL)
	{
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		$wheres = $this->get_message_condition($condition);
		$where = implode(" AND ",$wheres);
		if(!empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_message');
		$total = $total_query->row()->total;
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
		$data_rows = $this->db_read->get('est_message');
		$this->db_read->flush_cache();
		$data = $data_rows->result_array();
		if($data)
		{	
			foreach($data AS $i=>$msg)
			{
				$responce -> rows[$i] = $msg;
			}
		}
		return $responce;
	}

	/**
	 *  消息管理 - 消息列表 - 删除消息
	 *
	 * @param string $msg_id 消息ID
	 * @return boolen
	 * 
	 * @author zgx
	 */
	public function delete_message($msg_id='')
	{
		if(empty($msg_id))
		{
			return false;
		}
		return $this->db_write->query("DELETE FROM est_message WHERE msg_id IN ($msg_id)");
	}

	/**
	 * 获取某消息信息
	 *
	 * @param int $msg_id 消息id
	 * @return array
	 * <code>
	 * array(
	 * 		[msg_id] => 消息id
	 * 		[msg_send_user_id] => 发送者id
	 * 		[msg_send_user_name] => 发送人姓名
	 * 		[msg_receive_user_id] => 接收人ID
	 * 		[msg_receive_user_name] => 接收人姓名
	 * 		[msg_content] => 消息内容
	 * 		[msg_if_readed] => 是否已读 0：未读   1：已读
	 * 		[msg_insert_time] => 发送时间
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_msg_info($msg_id=0)
	{
		if (!empty($msg_id))
		{
			$where  = array("msg_id"=>$msg_id);
			$this->db_read->where($where);
		}

		$query = $this->db_read->get("est_message");
		return $query->row_array();
	}

	/**
	 * 设置消息已读
	 *
	 * @param int|string  $msg_id  id 或者 ids
	 * @return bool 执行结果
	 * 
	 * @author zgx
	 */
	public function set_message_readed($msg_id=0)
	{
		//如果传的是数组
		if(is_array($msg_id))
		{
			$where = "msg_id IN (".implode(',',$msg_id).")";
		}
		else
		{
			$where = "msg_id = $msg_id";
		}
		$this->db_write->where($where);
		$data  = array("msg_if_readed"=>"1");
		return $this->db_write->update("est_message",$data);
	}

	/**
	 * 发送消息
	 *
	 * @param string $content  信息内容
	 * @param string/int $receiver  接收人
	 * @return bool 执行结果 bool
	 * 
	 * @author zgx
	 */
	public function send_message($content='',$receiver='')
	{

		if(!is_array($receiver))
		{
			$receivers = array($receiver);
		}
		else
		{
			$receivers = $receiver;
		}

		$insert_time = date('Y-m-d H:i:s');
		$show_time   = time();

		$user_id     = $this->session->userdata("user_id");
		$user_name   = $this->session->userdata("user_name");
		//得到坐席信息
		$this->load->model("user_model");
		$this->load->model('notice_model');
		foreach ($receivers AS $receive_user_id )
		{
			$receiver_user_info =$this->user_model->get_user_info($receive_user_id);
			$receiver_user_name = $receiver_user_info['user_name'];

			$data_mes = array(
			'msg_send_user_id'=>$user_id,
			'msg_send_user_name'=>$user_name,
			'msg_receive_user_id'=>$receive_user_id,
			'msg_receive_user_name'=>$receiver_user_name,
			'msg_content'=>$content,
			'msg_insert_time'=>$insert_time,
			'msg_show_time'=>$show_time
			);
			$this->db_write->insert('est_message',$data_mes);
			$msg_id = $this->db_write->insert_id();
			$this->notice_model->write_notice('message',$receive_user_id,$user_name,$content,0,$user_id,$msg_id);
		}
		return TRUE;
	}

	/**
	 * 工作桌面 - 得到最新的未读消息
	 *
	 * @return array 二位数组
	 * @author zgx
	 */
	public function msg_workbench()
	{
		$sys_time=time()+30;
		$user_id = $this->session->userdata("user_id");
		$query = $this->db_read->query("SELECT msg_id,msg_send_user_id,msg_receive_user_id,msg_send_user_name,msg_receive_user_name,msg_content,msg_content_text ,msg_insert_time FROM est_message WHERE msg_show_time <= $sys_time AND msg_if_readed = 0 AND msg_receive_user_id = $user_id LIMIT 10");
		$messages = $query->result_array();
		//未读信息总数
		$this->db_read->where("msg_show_time <=",$sys_time);
		$this->db_read->where("msg_if_readed","0");
		$this->db_read->where("msg_receive_user_id",$user_id);
		$this->db_read->from("est_message");
		$messages_total[0] = $this->db_read->count_all_results();
		//实际信息数
		$messages_total[1] =$messages?count($messages):0;
		return $messages;
	}

	/**
	 * 返回用户和部门的树 （发消息时用,不能自己发自己）
	 * @param int $selected_id  默认选中
	 * @return array
	 * <code>
	 * array(
	 *      [0] => Array
	 *          (
	 *              [id] => 1
	 *              [pid] => 0
	 *              [text] => 华夏成讯
	 *              [attributes] => 1
	 *              [children] => Array
	 *                  (
	 *                    [0] => Array
	 *                        (
	 *                            [id] => 4
	 *                            [pid] => 1
	 *                            [text] => 研发部
	 *                           [attributes] => 2
	 *                        )
	 * 						[1] => Array
	 * 							(
	 * 								[id] => 员工id
	 * 								[text] => 员工姓名[员工工号]
	 * 							)
	 * 						...
	 *                 )
	 *           )
	 * 		...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_message_sender_tree($selected_id=0)
	{
		//当前坐席部门id
		$dept_session_id = $this->session->userdata('dept_id');
		$user_session_id = $this->session->userdata('user_id');
		//权限：发消息(全部 能给公司所有人发消息)
		$power_message_sh = check_authz("wdzssendxxall");
		if($power_message_sh)
		{
			$dept_session_id = 1;
		}

		//加载
		$this->load->model('department_model');
		$this->load->model('user_model');

		//默认没有收消息人
		$user_selected = false;

		//	权限：发消息
		$power_sendxx   = check_authz("wdzswdxx_sendxx");
		if($power_sendxx)
		{
			//获取部门树
			$dept_tree_arr = $this->department_model->get_whole_department_tree();
			//员工信息
			$user_info = $this->user_model->get_dept_children_users($dept_session_id,false);
			foreach($user_info as $user)
			{
				if($user['user_id'] == $user_session_id)
				{
					continue;
				}
				$tmp_user =  new stdClass();
				$tmp_user -> text = $user['user_name']."[".$user['user_num']."]";
				$tmp_user -> iconCls= 'icon-user';
				$tmp_user -> id = $user['user_id'];
				if ($selected_id == $user['user_id'])
				{
					$tmp_user -> checked = TRUE;
					$user_selected = true;
				}
				$dept_tree_arr[$user['dept_id']]->children[] = $tmp_user;
			}
			//部门数据信息整理
			$department_children_ids = $this->department_model->get_department_children_ids($dept_session_id);
			foreach($department_children_ids as $dept)
			{
				if($dept == $dept_session_id)
				{
					$dept_tree_arr[$dept] -> state = "open";
				}
				else
				{
					if(!empty($dept_tree_arr[$dept] -> children))
					$dept_tree_arr[$dept] -> state = "closed";
				}
			}

		}
		//没有权限 或 收件人不在权限范围内
		if($selected_id!=0 && !$user_selected)
		{
			$user_info = $this->user_model->get_user_info($selected_id);
			$dept_session_id = $user_info['dept_id'];//部门为接信息人所属部门

			$dept_parent_info = $this->department_model->get_department_info($dept_session_id);
			$dept_tree_arr[$dept_session_id] =  new stdClass();
			$dept_tree_arr[$dept_session_id] -> id = $dept_parent_info['dept_id'];
			$dept_tree_arr[$dept_session_id] -> pid = $dept_parent_info['dept_pid'];
			$dept_tree_arr[$dept_session_id] -> text = $dept_parent_info['dept_name'];
			$dept_tree_arr[$dept_session_id] -> iconCls= 'icon-depart';
			$dept_tree_arr[$dept_session_id] -> attributes = $dept_parent_info['dept_deep'];
			$dept_tree_arr[$dept_session_id] -> state = "open";

			$tmp_user =  new stdClass();
			$tmp_user -> text = $user_info['user_name']."[".$user_info['user_num']."]";
			$tmp_user -> id = $user_info['user_id'];
			$tmp_user -> iconCls= 'icon-user';
			$tmp_user -> checked = TRUE;
			$dept_tree_arr[$dept_session_id]->children[] = $tmp_user;

		}
		return array(object_to_array($dept_tree_arr[$dept_session_id]));
	}
}