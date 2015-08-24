<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 数据导入
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : data_import.php
 * $Author: yhx
 * $time  : Fri Nov 09 14:52:32 CST 2012
*/
class Data_import extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Data_import::index()
	 * 
	 * @return void
	 */
	public function index()
	{
		admin_priv();
		//导入类型
		$impt_type = $this->input->get('impt_type');
		switch ($impt_type)
		{
            case IMPT_CLIENT ://客户导入
                //客户基本可用字段信息
                $this->load->model('field_confirm_model');
                $client_base = $this->field_confirm_model->get_base_available_fields(FIELD_TYPE_CLIENT);
                $this->smarty->assign("client_base",$client_base);
                //信息来源
                if(!empty($client_base['cle_info_source']))
                {
                    $this->load->model("dictionary_model");
                    $cle_info_source = $this->dictionary_model->get_dictionary_detail(DICTIONARY_CLIENT_INFO);
                    $this->smarty->assign("cle_info_source",$cle_info_source);
                }
                //角色权限
                $role_type = $this->session->userdata('role_type');
                $this->smarty->assign('role_type',$role_type);
                break;
            case IMPT_PRODUCT ://产品导入
                break;
            default:
                break;
		}

		//获取系统配置参数
		$this->load->model("system_config_model");
		$config_info = $this->system_config_model->get_system_config();
		$phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
		$this->smarty->assign("phone_ifrepeat",$phone_ifrepeat);

		$this->smarty->assign("impt_type",$impt_type);
		$this->smarty->display("data_import.htm");
	}

	/**
	 * 导入模板
	 */
	public function import_model()
	{
		admin_priv();

		$model_id = $this->input->get("model_id");
		$model_id = empty($model_id) ? 0 : $model_id;
		$this->smarty->assign("model_id",$model_id);

		$impt_type = $this->input->get('impt_type');
		$model_type = '';
		$field_type = '';
		switch ($impt_type)
		{
            case IMPT_CLIENT ://客户导入
                $model_type = MODEL_CLIENT_IMPT;//1
                $field_type = FIELD_TYPE_CLIENT_CONTACT;
                break;
            case IMPT_PRODUCT ://产品导入
                $model_type = MODEL_PRODUCT_IMPT;//3
                $field_type = FIELD_TYPE_PRODUCT;
                break;
		}

		//得到模板选项信息
		$this->load->model("model_model");
		$model_info   = $this->model_model->get_model_info($model_id,$model_type);
		$model_detail = array();
		if (!empty($model_info["model_detail"]))
		{
			//得到模板使用中的字段
			$model_detail = $this->model_model->get_model_detail_info($model_info["model_detail"],$field_type,true);
		}
		$this->smarty->assign("model_detail",$model_detail);
		//表头
		$table_head =  $this->model_model->a_z_table_head(10);
		$this->smarty->assign("table_head",$table_head);
		//表格宽度
		$table_width = count($table_head)*80;
		$this->smarty->assign("table_width",$table_width);

		$this->smarty->display("data_import_model.htm");
	}

	/**
	 * 选择数据文件，开始导入
	 */
	public function data_upload()
	{
		admin_priv();

		$impt_type = $this->input->post("impt_type");
		if(empty($impt_type))
		{
			return false;
		}
		$param = array('impt_type'=>$impt_type);//参数
		$model_id = $this->input->post("selected_system_model");//显示模板ID
		if (empty($model_id))
		{
			return false;
		}
		$param['model_id'] = $model_id;//参数
		if(empty($_FILES['file_address']['name']))
		{
			$link[0]["text"] = "上传文件失败";
			$link[0]["href"] = "index.php?c=data_import&impt_type=".$impt_type;
			sys_msg("上传文件出错", 0, $link);
		}

		switch ($impt_type)
		{
            //客户导入
            case IMPT_CLIENT :
                $param['cle_info_source'] = $this->input->post("cle_info_source"); //信息来源
                $param['data_owner'] = $this->input->post("data_owner"); //数据所属人
                if(empty($param['data_owner']))
                {
                    $param['data_owner'] = DATA_PERSON;
                }
                $param['shuffle'] = $this->input->post("shuffle");//数据是否打乱顺序
                $param['filter_cle_name'] = $this->input->post("filter_cle_name");//过滤姓名重复

                //获取系统配置参数
                $this->load->model("system_config_model");
                $config_info = $this->system_config_model->get_system_config();
                $phone_ifrepeat = empty($config_info["phone_ifrepeat"]) ? 0 : $config_info["phone_ifrepeat"];
                if ($phone_ifrepeat)
                {
                    $param['filter_cle_phone'] = 1;//过滤电话重复
                }
                else
                {
                    $param['filter_cle_phone'] = $this->input->post("filter_cle_phone");//过滤电话重复
                }
                break;
            //产品导入
            case IMPT_PRODUCT :
                $param['product_class_id'] = $this->input->post("product_class_id"); //产品分类
                $param['product_state'] = $this->input->post("product_state"); //产品状态
                $param['shuffle'] = $this->input->post("shuffle");//数据是否打乱顺序
                $param['filter_product_name'] = $this->input->post("filter_product_name");//过滤产品名称相同的数据
                break;
		}

		/* 上传数据文件 */
        $this->load->config('myconfig');
		$filepath = $this->config->item('upload_path');
		if(!is_dir($filepath))
		{
			@mkdir($filepath);
			@chmod($filepath,0777);
		}
		$config['upload_path']   = $filepath;   //上传路径
		$config['allowed_types'] = 'txt|csv';  //允许类型
		$config['encrypt_name']  = TRUE;  //重命名
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$upload_result = $this->upload->do_upload("file_address");

		if ($upload_result)
		{
			$udata = $this->upload->data();
			$param['source_file'] = $filepath.$udata['file_name']; //上传至服务器的文件
			$tmp_file = $udata['raw_name'];
			$param['tmp_file'] = $tmp_file; //缓存键值

            $this->load->model('data_import_model');
            $this->data_import_model->_set_progress("上传文件成功", $tmp_file);

			$this->load->library('json');
			$data = $this->json->encode($param);
			$this->import_process($tmp_file,$impt_type,$data);
		}
		else
		{
			$link[0]["text"] = "上传文件失败".$this->upload->display_errors();
			$link[0]["href"] = "index.php?c=data_import&impt_type=".$impt_type;
			sys_msg("上传文件出错", 0, $link);
		}

        return true;
	}

	/**
	 * 导入进度页面
	 */
	private function import_process($tmp_file,$impt_type,$param_data)
	{
		$this->smarty->assign("tmp_file",$tmp_file);
		$this->smarty->assign("impt_type",$impt_type);
		$this->smarty->assign("param_data",$param_data);
		$this->smarty->display("data_import_process.htm");
	}

	/**
	 * 读取进度条
	 */
	public function read_process()
	{
		admin_priv();

        $key = $this->input->post("tmp_file");
        $this->load->driver('cache', array('adapter'=>'memcached','prefix'=>$this->session->userdata('vcc_id').'_'));
        echo $this->cache->get($key);
	}

	/**
	 * 开始处理数据 - 导入
	 */
	public function import_data()
	{
		admin_priv();

		$param = $this->input->post('param');
		$this->load->library('json');
		$param = $this->json->decode($param);

		$impt_type	= $param->impt_type;//显示进度的临时文件
		$model_id	= $param->model_id;//显示模板ID
		$source_file= $param->source_file; //上传的数据文件名称
		$temp_file	= $param->tmp_file;//显示进度的临时文件

		switch ($impt_type)
		{
            case IMPT_CLIENT :
                $cle_info_source	= $param->cle_info_source;//信息来源
                $shuffle			= $param->shuffle;//数据是否打乱顺序
                $filter_cle_name	= $param->filter_cle_name;//姓名重复过滤
                $filter_cle_phone 	= $param->filter_cle_phone;//客户电话号码过滤
                $data_owner 		= $param->data_owner;//数据所属人: DATA_DEPARTMENT 部门  DATA_PERSON 个人
                //导入
                $this->load->model("data_import_model");
                $this->data_import_model->import_client($model_id,$source_file,$temp_file,$cle_info_source,$shuffle,$filter_cle_name,$filter_cle_phone,$data_owner);
                break;
            case IMPT_PRODUCT :
                $product_state 		= $param->product_state;
                $product_class_id    = $param->product_class_id;
                $shuffle 		= $param->shuffle;//数据是否打乱顺序
                $filter_product_name 	= $param->filter_product_name;//姓名重复过滤
                //导入
                $this->load->model("data_import_model");
                $this->data_import_model->import_product($model_id,$source_file,$temp_file,$shuffle,$filter_product_name,$product_state,$product_class_id);
                break;
		}
	}

	/**
	 * 导入日志列表
	 */
	public function display_import_log()
	{
		admin_priv();

		$impt_type = $this->input->get('impt_type');
		if(!empty($impt_type))
		$this->smarty->assign("impt_type",$impt_type);

		$import_id = $this->input->get('import_id');
		if(!empty($import_id))
		$this->smarty->assign("import_id",$import_id);

		$this->smarty->display("data_import_log.htm");
	}

	/**
	 * 获取导入日志列表信息
	 */
	public function import_log_query()
	{
		admin_priv();

		$this->load->model("data_import_model");
		$condition = array();
		$impt_type = $this->input->get('impt_type');
		if($impt_type)
		$condition['impt_type'] = $impt_type;


		$impt_id = $this->input->post("impt_id");
		if($impt_id)
		$condition['impt_id'] = $impt_id;

		$responce = $this->data_import_model->get_import_log($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 删除 某批次号 导入成功的数据 （把相应表(impt_type 1客户表 2产品表)相应数据一并删除）
	 *
	 */
	public function delete_success_data()
	{
		admin_priv();

		$impt_id = $this->input->post("impt_id");
		$impt_type = $this->input->post("impt_type");
		if (empty($impt_id)||empty($impt_type))
		{
			make_json_error("缺少参数，执行失败！");
		}
		//删除
		$this->load->model("data_import_model");
		$result = $this->data_import_model->delete_success_data($impt_id,$impt_type);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行失败！");
		}
	}
	/**
	 * 删除 某批次号 导入错误的数据（有错误日志表的，一并删除相应数据）
	 *
	 */
	public function delete_failure_data()
	{
		admin_priv();

		$impt_id = $this->input->post("impt_id");
		$impt_type = $this->input->post("impt_type");
		if (empty($impt_id)||empty($impt_type))
		{
			make_json_error("缺少参数，执行失败！");
		}
		//删除
		$this->load->model("data_import_model");
		$result = $this->data_import_model->delete_failure_data($impt_id,$impt_type);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行失败！");
		}
	}
	/**
	 * 导出该批次中，导入失败的数据（客户）
	 *
	 */
	public function export_failure_data()
	{
		admin_priv();

		$impt_id = $this->input->get("impt_id");
		if (empty($impt_id))
		{
			make_json_error("缺少参数，执行失败！");
		}
		//删除
		$this->load->model("data_import_model");
		$this->data_import_model->export_failure_data($impt_id);
	}
}