<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取客户相关文件列表
	 *
	 */
	public function index()
	{
		admin_priv();
		
		$cle_id = $this->input->get('cle_id');
		$this->smarty->assign('cle_id',$cle_id);

		$this->smarty->display('file_list.htm');
	}

	/**
	 * 获取列表数据
	 *
	 */
	public function list_file_query()
	{
		admin_priv();

		$condition = $this->input->post();
		$this->load->model("file_model");

		$responce = $this->file_model->get_file_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 上传
	 */
	public function upload_file()
	{
		admin_priv();
		
		$cle_id = $this->input->post('cle_id');
		$error = '';
		$msg = '';

		//上传附件
		$allowed_types = 'avi|swf|asf|psd|bmp|gif|txt|csv|xls|xlsx|jpg|jpeg|mp3|wav|pdf|tif|png|zip|doc|docx|chm|ppt|pptx|rar';

		$vcc_code = $this->session->userdata('vcc_code');
		$filepath = FILE.$vcc_code.'/';
		if(!is_dir($filepath))
		{
			@mkdir($filepath);
			@chmod($filepath,0777);
		}

		$config['upload_path'] = $filepath; //上传路径
		$config['allowed_types'] = $allowed_types;//允许类型
		$config['encrypt_name']  = TRUE;  //重命名
		$this->load->library('upload',$config);
		$this->upload->initialize($config);

		if($_FILES['fileToUpload']['name'])
		{
			if(!$this->upload->do_upload('fileToUpload'))
			{
				$error = $this->upload->display_errors();
			}
			else
			{
				//上传成功
				$udata = $this->upload->data();

				$this->load->model('file_model');
				$file_size = $this->file_model->get_file_size($udata['file_size']);
				$result = $this->file_model->insert_file($cle_id,$udata['orig_name'],$udata['file_name'],$file_size);
				if($result)
				{
					$msg .= '上传成功';
				}
				else
				{
					@unlink($filepath.$udata['file_name']);
					$error = '上传失败';
				}
			}
		}
		echo "{'error': '". $error . "','msg': '" . $msg . "'}";
	}

	/**
	 * 删除
	 */
	public function delete_file()
	{
		admin_priv();

		$file_ids = $this->input->post('file_ids');
		$this->load->model('file_model');
		$result = $this->file_model->delete_file($file_ids);
		make_simple_response($result);
	}

	/**
	 * 下载
	 */
	public function download_file()
	{
		admin_priv();

		$file_id = $this->input->get('file_id');
		$file_new_name = $this->input->get('file_new_name');
		$file_old_name = $this->input->get('file_old_name');

		$this->load->model('file_model');
		$this->file_model->download_file($file_id,$file_new_name,$file_old_name);
		return;
	}

}