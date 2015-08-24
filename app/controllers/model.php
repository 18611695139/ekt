<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 模板字段 - 模板
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : field.php
 * $Author: yhx
 * $time  : Mon Nov 12 11:42:06 CST 2012
*/
class Model extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Enter description here...
	 *
	 */
	public function index()
	{
		admin_priv();

		$impt_type = $this->input->get('impt_type');
		$this->smarty->assign('impt_type',$impt_type);
		$this->smarty->display("model_list.htm");
	}

	/**
	 * Classname::index()
	 * 
	 * @return void 
	 */
	public function select_model()
	{
		admin_priv();

		$model_id = $this->input->get("model_id");
		$model_id = empty($model_id) ? 0 : $model_id;
		$impt_type = $this->input->get('impt_type');

		//查询模板信息
		$this->load->model("model_model");
		$field_type = '';
		$model_info = array();
		switch ($impt_type)
		{
			case IMPT_CLIENT ://客户导入
			$field_type = FIELD_TYPE_CLIENT_CONTACT;
			$model_info = $this->model_model->get_model_info(0,MODEL_CLIENT_IMPT);
			break;
			case IMPT_PRODUCT ://产品导入
			$field_type = FIELD_TYPE_PRODUCT;
			$model_info = $this->model_model->get_model_info(0,MODEL_PRODUCT_IMPT);
			break;
		}
		$this->smarty->assign("model_info",$model_info);

		//设置默认选中项
		$model_name   = "";
		$model_detail = "";
		if(!empty($model_info))
		{
			if ($model_id == 0)
			{
				$model_id     = empty($model_info[0]["model_id"]) ? 0 : $model_info[0]["model_id"];
				$model_name   = empty($model_info[0]["model_name"]) ? "" : $model_info[0]["model_name"];
				$model_detail = empty($model_info[0]["model_detail"]) ? "" : $model_info[0]["model_detail"];
			}
			else
			{
				foreach ($model_info AS $value)
				{
					if ($value["model_id"] == $model_id)
					{
						$model_name   = empty($value["model_name"]) ? "" : $value["model_name"];
						$model_detail = empty($value["model_detail"]) ? "" : $value["model_detail"];
					}
				}
			}
		}
		//得到模板选项信息
		$model_fields = "";
		if ($model_detail)
		{
			$model_fields = $this->model_model->get_model_detail_info($model_detail,$field_type,true);
		}
		$this->smarty->assign("model_fields",$model_fields);

		$this->smarty->assign("model_id",$model_id);
		$this->smarty->assign("model_name",$model_name);
		$this->smarty->display("model_select.htm");
	}

	/**
	 * 添加模板 impt_type 1 客户 2产品
	 *
	 */
	public function add_model()
	{
		admin_priv();

		$impt_type = $this->input->get('impt_type');

		if($impt_type==IMPT_CLIENT)
		{
			$field_type = FIELD_TYPE_CLIENT_CONTACT;
			$model_name = "客户模板";//模板名称
			$model_type = MODEL_CLIENT_IMPT;
		}
		else if($impt_type==IMPT_PRODUCT)
		{
			$field_type = FIELD_TYPE_PRODUCT;
			$model_name = "产品模板";//模板名称
			$model_type = MODEL_PRODUCT_IMPT;
		}
		$this->load->model("field_confirm_model");
		$available_fields = $this->field_confirm_model->get_available_fields($field_type);
		$model_field  = array();
		foreach ($available_fields AS $value)
		{
			//去除 不必要的字段 和 自定义级联字段、关联级联字段
			$this->load->model("model_model");
			$cannot_impt_fields = $this->model_model->cannot_impt_fields($impt_type);
			if (!in_array($value["fields"],$cannot_impt_fields ) && $value['data_type']!=4 && $value['data_type']!=7)
			{
				$model_field[] = $value;
			}
		}
		$this->smarty->assign("model_field",$model_field);
		$this->smarty->assign("model_name",$model_name);
		$this->smarty->assign("model_type",$model_type);
		$this->smarty->assign("model_action","index.php?c=model&m=insert_model");
		$this->smarty->display("model_info.htm");
	}

	/**
	 * 保存新模板
	 *
	 */
	public function insert_model()
	{
		admin_priv();

		$model_name = $this->input->post('model_name');
		$model_type = $this->input->post('model_type');
		$field_ids = $this->input->post('field_id');

		//保存
		$this->load->model("model_model");
		$result = $this->model_model->insert_model($model_name,$model_type,$field_ids);
		make_simple_response($result);
	}

	/**
	 * 编辑模板
	 *
	 */
	public function edit_model()
	{
		admin_priv();

		$model_id = $this->input->get("model_id");
		$model_id = empty($model_id) ? 0 : $model_id;
		$impt_type = $this->input->get('impt_type');

		//查询模板信息
		$this->load->model("model_model");
		if($impt_type==IMPT_CLIENT)
		{
			$model_type = MODEL_CLIENT_IMPT;
			$field_type = FIELD_TYPE_CLIENT_CONTACT;
			$model_info = $this->model_model->get_model_info($model_id,MODEL_CLIENT_IMPT);
		}
		else if($impt_type==IMPT_PRODUCT)
		{
			$model_type = MODEL_PRODUCT_IMPT;
			$field_type = FIELD_TYPE_PRODUCT;
			$model_info = $this->model_model->get_model_info($model_id,MODEL_PRODUCT_IMPT);
		}
		$this->smarty->assign("model_info",$model_info);

		//得到模板选项信息
		$model_detail = "";
		if ( !empty($model_info["model_detail"]) )
		{
			$model_result = $this->model_model->get_model_detail_info($model_info["model_detail"],$field_type,false);
			foreach ($model_result AS $value)
			{
				//去除不必要的字段 和 自定义级联字段
				$cannot_impt_fields = $this->model_model->cannot_impt_fields($impt_type);
				if (  !in_array($value["fields"],$cannot_impt_fields ) && $value['data_type']!=4 && $value['data_type']!=7)
				{
					$model_detail[] = $value;
				}
			}
		}
		$this->smarty->assign("model_field",$model_detail);
		$this->smarty->assign("model_id",$model_id);
		$this->smarty->assign("model_name",empty($model_info["model_name"]) ? "" : $model_info["model_name"]);
		$this->smarty->assign("model_type",$model_type);
		$this->smarty->assign("model_action","index.php?c=model&m=update_model");
		$this->smarty->display("model_info.htm");
	}

	/**
	 * 保存编辑后的模板信息
	 *
	 */
	public function update_model()
	{
		admin_priv();

		$model_id = $this->input->post('model_id');
		$model_name = $this->input->post('model_name');
		$field_ids = $this->input->post('field_id');

		//保存
		$this->load->model("model_model");
		$result = $this->model_model->update_model($model_id,$model_name,$field_ids);
		make_simple_response($result);
	}

	/**
	 * 删除模板信息
	 */
	public function delete_model()
	{
		admin_priv();

		$model_id = $this->input->post("model_id");
		$this->load->model("model_model");
		$result = $this->model_model->delete_model($model_id);
		make_simple_response($result);
	}

	/**
	 * 导出 模板信息(客户导入模板)
	 *
	 */
	public function export_model()
	{
		admin_priv();

		$model_id = $this->input->get("model_id");
		if (empty($model_id))
		{
			die("缺少模板参数！");
		}

		//类型
		$impt_type = $this->input->get('impt_type');
		$model_type = '';
		$field_type = '';
		switch ($impt_type)
		{
			case IMPT_CLIENT ://客户导入
			$model_type =MODEL_CLIENT_IMPT;//1
			$field_type = FIELD_TYPE_CLIENT_CONTACT;
			break;
			case IMPT_PRODUCT ://产品导入
			$model_type =MODEL_PRODUCT_IMPT;//3
			$field_type = FIELD_TYPE_PRODUCT;
			break;
		}
		//查询模板信息
		$this->load->model("model_model");
		$model_info = $this->model_model->get_model_info($model_id,$model_type);
		$model_name = empty($model_info["model_name"]) ? "数据模板" : $model_info["model_name"];

		//得到模板选项信息
		$model_detail = array();
		if (!empty($model_info["model_detail"]))
		{
			$model_detail = $this->model_model->get_model_detail_info($model_info["model_detail"],$field_type,true);
		}

		$header = array();
		foreach ($model_detail AS $value)
		{
			if ($value["fields"])
			{
				$header[] = $value["name"];
			}
		}
		$this->load->library("csv");
		$this->csv->creatcsv($model_name,$header);
	}
}