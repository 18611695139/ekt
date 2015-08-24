<?php
class Backup_reset_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 数据备份
	 * 
	 * @param string $file_name 文件名
	 * @return bool
	 * @author zgx
	 */
	public function data_backup($file_name='')
	{
		if(!$file_name)
		{
			return false;
		}
		$file_name .= '-'.date('Y-m-d-H-i-s');
		$backup_time = date('Y-m-d H:i:s');
		require_once(APPPATH.'config/database.php');

		$MY_HOST = $db['default']['hostname'];
		$MY_USER = $db['default']['username'];
		$MY_PASSWORD = $db['default']['password'];
		$MY_DATABASE = $this->session->userdata('db_name');
		if(!is_dir('./public/backup'))
		{
			@mkdir('./public/backup');
			@chmod('./public/backup',0777);
		}
		$vcc_code = $this->session->userdata('vcc_code');
		if(!is_dir('./public/backup/'.$vcc_code))
		{
			@mkdir('./public/backup/'.$vcc_code);
			@chmod('./public/backup/'.$vcc_code,0777);
		}
		exec("mysqldump --opt $MY_DATABASE -h".$MY_HOST." -u".$MY_USER." -p".$MY_PASSWORD." -R -B >./public/backup/".$vcc_code.'/'.$file_name.".sql",$out,$result);

		if($result == 0)
		{
			$data_backup = array(
			'backup_datetime'=>$backup_time,
			'backup_file_name'=>$file_name,
			'backup_create_user_id'=>$this->session->userdata('user_id')
			);
			$this->db_write->insert('est_backup',$data_backup);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 备份列表
	 * 
	 * @param int $page 第几页
	 * @param int $limit 每页显示几个
	 * @param int $sort 根据哪个字段排序
	 * @param int $order 排序方式
	 * @return object
 	 * 根据select 中的字段返回结果
 	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [backup_id]=>10
	 *       … 
	 *     )
	 *  )
	 * </code>
	 * @author zgx
	 */
	public function get_backup_list($page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_backup');
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
		$_data = $this->db_read->get('est_backup');
		$this->db_read->flush_cache();

		$responce -> rows = array();
		if($_data)
		{
			//坐席信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result AS $key => $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}

			foreach($_data->result_array() AS $i=>$backup)
			{
				$backup['backup_create_user_name'] = empty($user_info[$backup["backup_create_user_id"]]) ? '' : $user_info[$backup["backup_create_user_id"]];
				$responce -> rows[$i] = $backup;
			}
		}
		return $responce;
	}

	/**
	 * 删除备份
	 * 
	 * @param int $backup_id 备份id
	 * @return bool
	 * @author zgx
	 */
	public function backup_delete($backup_id=0)
	{
		if(!$backup_id)
		{
			return false;
		}
		$query = $this->db_read->get_where('est_backup',array('backup_id'=>$backup_id));
		$backup_info = $query->row_array();

		$vcc_code = $this->session->userdata('vcc_code');
		@unlink('./public/backup/'.$vcc_code.'/'.$backup_info['backup_file_name'].'.sql');
		$where = 'backup_id IN('.$backup_id.')' ;
		$result = $this->db_write->delete('est_backup',$where);
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
	 * 数据还原
	 * 
	 * @param int $backup_id
	 * @return bool
	 */
	public function data_reset($backup_id=0)
	{
		if(empty($backup_id))
		{
			return false;
		}
		$query = $this->db_read->get_where('est_backup',array('backup_id'=>$backup_id));
		$backup_info = $query->row_array();

		require_once(APPPATH.'config/database.php');
		$MY_HOST = $db['default']['hostname'];
		$MY_USER = $db['default']['username'];
		$MY_PASSWORD = $db['default']['password'];
		$MY_DATABASE = $this->session->userdata('db_name');
		$vcc_code = $this->session->userdata('vcc_code');

		exec('mysql -h'.$MY_HOST.' -u'.$MY_USER.' -p'.$MY_PASSWORD.' < ./public/backup/'.$vcc_code.'/'.$backup_info['backup_file_name'].'.sql',$in,$result);

		if($result == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}