<?php
class File_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 检索条件
	 *
	 * @param array $condition 检索内容
	 * @return array
	 * 
	 * @author zgx
	 */
	private function get_file_condition($condition=array())
	{
		$wheres = array();
		if(!empty($condition['cle_id']))
		{
			$wheres[] = "cle_id = '".$condition['cle_id']."'";
		}
		return $wheres;
	}

	/**
	 * 获取文件列表数据
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
	 *        [file_id]=>10
 	 *        [file_old_name] => 文件原名
	 *       	… 
	 *    )
	 * ...
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_file_list($condition = array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_file_condition($condition);
		$where = implode(" AND ",$wheres);

		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_file');
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
		$data = $this->db_read->get('est_file');
		$data = $data->result_array();

		$responce -> rows = array();
		$this->db_read->flush_cache();//清除缓存细信息

		if (!empty($data))
		{
			//坐席信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result AS $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}
		}

		$i = 0;
		foreach($data AS $i=>$file_info)
		{
			$file_ext = explode('.',$file_info['file_new_name']);
			$file_info["file_ext"] = $file_ext[count($file_ext)-1];
			$file_info["file_size"] = $file_info["file_size"];
			//创建人
			$file_info["user_name"]   = empty($user_info[$file_info["file_upload_user_id"]]) ? "" : $user_info[$file_info["file_upload_user_id"]];
			$responce -> rows[$i]           = $file_info;
			$i ++;
		}
		return $responce;
	}


	/**
	 * 添加文件
	 * 
	 * @param int $cle_id 客户id
     * @param string $file_old_name 文件原名
     * @param string $file_new_name 文件新名
     * @param string $file_size 文件大小
	 * @return bool
	 * @author zgx
	 */
	public function insert_file($cle_id=0,$file_old_name='',$file_new_name='',$file_size='')
	{
		if(empty($cle_id)||empty($file_old_name)||empty($file_new_name)||empty($file_size))
		{
			return false;
		}
		$user_id = $this->session->userdata('user_id');
		$dept_id = $this->session->userdata('dept_id');
		$data = array(
		'file_old_name'=>$file_old_name,
		'file_new_name'=>$file_new_name,
		'file_upload_time'=>date('Y-m-d H:i:s'),
		'file_upload_user_id'=>$user_id,
		'file_upload_dept_id'=>$dept_id,
		'file_size'=>$file_size,
		'cle_id'=>$cle_id
		);
		$result = $this->db_write->insert('est_file',$data);
		return $result;
	}

	/**
	 * 删除文件
	 * 
	 * @param int|string $file_ids 文件id
	 * @return bool
	 * @author zgx
	 */
	public function delete_file($file_ids='')
	{
		if(empty($file_ids))
		{
			return '缺少参数';
		}
		$where = 'file_id IN('.$file_ids.')' ;
		$this->db_read->select('file_new_name,file_id');
		$this->db_read->where($where);
		$query = $this->db_read->get('est_file');
		if($query)
		{
			$vcc_code = $this->session->userdata('vcc_code');
			foreach($query->result_array() AS $value)
			{
				@unlink(FILE.$vcc_code.'/'.$value['file_new_name']);
			}
		}
		$result = $this->db_write->delete('est_file',$where);
		if($result)
		{
			return 1;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 下载文件
	 * @param int $file_id 文件id
	 * @param string $file_new_name 文件新名
	 * @param string $file_old_name 文件原名
	 */
	public function download_file($file_id=0,$file_new_name='',$file_old_name='')
	{
		if (!empty($file_new_name) && $file_id!=0)
		{
			$vcc_code = $this->session->userdata('vcc_code');
			$path = FILE.$vcc_code.'/'.$file_new_name;
			if(file_exists($path))
			{
				$this->load->helper('download');
				$data = file_get_contents($path); // 读文件内容
				$name = empty($file_old_name) ? "客户文件" : $file_old_name;
                $name = mb_convert_encoding($name, 'GBK', 'UTF-8');

				force_download($name,$data);
			}
			else
			{
				sys_msg('该文件已被非法删除');
			}
		}
		else
		{
			sys_msg('下载异常');
		}

	}

	/**
	 * 调整文件大小格式
	 * @param string $file_size 文件大小 (kb)
	 * @return string
	 * @author zgx
	 */
	public function get_file_size($file_size='')
	{
		if(empty($file_size) && $file_size >= 0)
		{
			return false;
		}

		if($file_size >= 1024)
		{
			if(($file_size/1024) >=1024)
			{
				return round(($file_size/(1024*1024)),2).'GB';
			}
			else
			{
				return round(($file_size/1024),2).'MB';
			}
		}
		else
		{
			return $file_size.'KB';
		}
	}
}