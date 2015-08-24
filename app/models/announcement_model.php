<?php 
class Announcement_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 公告查看权限，查看本部门及上级部门的公告 及 自己发的公告
	 * 
	 * @return string
	 */
	public function get_check_view_authz()
	{
		$condition = "";
		$user_id = $this->session->userdata('user_id');
		$condition .= "create_user_id = '".$user_id."' ";
		//系统公告权限
		$dept_id = $this->session->userdata('dept_id');
		//得到当前部门逆推至根节点部门的ID（包含自身）
		$this->load->model("department_model");
		$dept_parent_ids = $this->department_model->get_department_parent_ids($dept_id);
		if ($dept_parent_ids)
		{
			$dept_parent_ids = implode(",",$dept_parent_ids);
			$condition .= " OR dept_id IN ($dept_parent_ids) ";
		}
		return "(".$condition.")";
	}

	/**
	 * 获取检索条件
	 * 
	 * @param array $condition 检索内容
	 * @return array 返回检索条件以为数组
	 * @author zgx
	 */
	public function get_announcement_condition($condition = array())
	{
		$wheres = array();
		if(!empty($condition['anns_title']))
		{
			$wheres[] = "anns_title LIKE '%".$condition['anns_title']."%'";
		}

		//判断权限,查看本部门及上级部门的公告及自己发的公告
		$condition_check= $this->get_check_view_authz();
		if(!empty($condition_check))
		{
			$wheres[] = $condition_check;
		}
		return $wheres;
	}

	/**
	 * 获取公告列表数据
     * 
     * @param array $condition 检索字段信息
	 * @param string $page 第几页
	 * @param string $limit 每页显示几条
	 * @param string $sort 根据哪个字段排序
	 * @param string $order 排序方式
	 * @return object responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [anns_id]=>10
 	 *        [anns_title] => 公告标题
	 *       	… 
	 *    )
	 *  ...
	 * )
	 * </code>
	 * 
	 * @author zgx
     */
	public function get_announcement_list($condition=array(),$page='', $limit='', $sort=NULL, $order=NULL)
	{
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		$wheres = $this->get_announcement_condition($condition);
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_anns');
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
		$anns_data = $this->db_read->get('est_anns');
		$this->db_read->flush_cache();
		$data = $anns_data->result_array();
		if($data)
		{
			//员工信息
			$this->load->model("user_model");
			$user = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user AS $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
			//部门信息
			$this->load->model("department_model");
			$dept_result = $this->department_model->get_all_department();
			$dept_info   = array();
			foreach ($dept_result AS $value)
			{
				$dept_info[$value["dept_id"]] = $value["dept_name"];
			}
			foreach($data AS $i=>$anns)
			{
				$anns['create_user_name'] = empty($user_info[$anns['create_user_id']])?'不存在':$user_info[$anns['create_user_id']];
				$anns['dept_name'] = empty($dept_info[$anns['dept_id']])?'不存在':$dept_info[$anns['dept_id']];
				$responce -> rows[$i] = $anns;
			}
		}
		return $responce;

	}

	/**
	 * 根据id获取某公告全部信息
     * 
     * @param int $anns_id 公告id
     * @return boolen
     */
	public function get_announcementinfo($anns_id=0)
	{
		$result = array();
		if(empty($anns_id))
		{
			return $result;
		}
		$this->db_read->limit(1);
		$this->db_read->where('anns_id',$anns_id);
		$query = $this->db_read->get('est_anns');
		$result = $query->row_array();

		//获取员工信息
		if(!empty($result['create_user_id']))
		{
			$this->load->model("user_model");
			$user_info = $this->user_model->get_user_info($result['create_user_id']);
			$result['create_user_name'] = empty($user_info['user_name'])?'不存在':$user_info['user_name'];
			if($result['dept_id'] == $user_info['dept_id'])
			{
				$result['dept_name'] = empty($user_info['dept_name'])?'':$user_info['dept_name'];
			}
			else
			{
				$result['dept_name'] = '不存在';
			}
		}
		return $result;
	}

	/**
	 * 添加公告
     * 
     * @param string $title 公告标题
     * @param string $content 公告内容
     * @param int $dept_res_id 所属部门id
     * @return int 公告id
     * @author zgx
     */
	public function insert_announcement($title='',$content='',$dept_res_id=0)
	{
		$datetime = date("Y-m-d H:i:s");
		$data['anns_title']        = $title;
		$data['anns_content']      = $content;
		$data["creat_time"]        = $datetime;
		$data["create_user_id"]    = $this->session->userdata('user_id');
		$data["create_user_name"]  = $this->session->userdata('user_name');

		$dept_id = empty($dept_res_id) ? $this->session->userdata('dept_id'): $dept_res_id;
		$this->load->model('department_model');
		$dept_info = $this->department_model->get_department_info($dept_id);
		$data["dept_id"]       = $dept_info["dept_id"];
		$data["dept_name"]     = $dept_info["dept_name"];
		$this->db_write->insert('est_anns', $data); //保存新公告信息
		$anns_id = $this->db_write->insert_id();
		//弹框信息写入
		//得到指定部门及其下级部门的所有坐席信息
		$this->load->model("user_model");
		$user_info = $this->user_model->get_dept_children_users($dept_res_id,false);
		if( ! empty($user_info))
		{
			$this->load->model("notice_model");
			foreach($user_info AS $_user)
			{
				$this->notice_model->write_notice('announce',$_user['user_id'],'',$data["anns_content"],0,0,$anns_id);
			}
		}

		return  $anns_id;
	}


	/**
	 * 删除公告
     * 
     * @param string $anns_ids 公告id
     * @return bool
     */
	public function delete_announcement($anns_ids='')
	{
		if(empty($anns_ids))
		{
			return false;
		}

		//删除
		$result = $this->db_write->query("DELETE FROM est_anns WHERE anns_id IN($anns_ids)");

		return $result;
	}



	/**
	 * 修改公告
     * 
     * @param int $anns_id 公告id
     * @param string $title 公告标题
     * @param string $ancontent 公告内容
     * @param int $department 所属部门id
     * @return bool
     */
	public function update_annsinfo($anns_id=0,$title='',$ancontent='',$department=0)
	{
		$insert_value["anns_title"]       = replace_illegal_string($title); //标题
		$contents = !empty($ancontent)?str_replace("\n","",$ancontent):"";
		$contents= str_replace('width=\"auto\" height=\"auto\"','',$contents);
		$insert_value["anns_content"]     = $contents;  //内容

		$dept_id = empty($department) ? $this->session->userdata('dept_id'): $department;
		$this->load->model('department_model');
		$dept_info = $this->department_model->get_department_info($dept_id);
		$insert_value["dept_id"]       = $dept_info["dept_id"];
		$insert_value["dept_name"]     = $dept_info["dept_name"];

		$this->db_write->where('anns_id', $anns_id);
		$result = $this->db_write->update('est_anns', $insert_value);

		return $result;
	}

	/**
	 * 工作桌面 - 公告
	 *
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 				[anns_id] => 公告id
	 * 				[anns_title] => 公告标题
	 * 				[anns_content] => 公告内容
	 * 				[creat_time] => 公告创建时间
	 * 				[dept_id] => 所属部门id
	 * 				[dept_name] => 所属部门名称
	 * 				[create_user_name] => 创建人名称
	 * 			)
	 * 			...
	 * )
	 * </code>
	 */
	public function anns_workbench()
	{
		//判断权限
		$condition  = $this->get_check_view_authz();

		if( ! empty($condition))
		{
			$this->db_read->start_cache();
			$this->db_read->where($condition);
			$this->db_read->stop_cache();
		}
		$this->db_read->order_by("creat_time", "desc");
		$query = $this->db_read->get('est_anns',10);
		$this->db_read->flush_cache();
		$anns_info  = $query->result_array();

		return $anns_info;
	}
}