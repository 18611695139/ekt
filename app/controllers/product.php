<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {

	//===================== 产品分类  ===============================
	/**
	 * 产品分类列表
	 */
	public function product_class()
	{
		$this->smarty->display('product_class_info.htm');
	}

	/**
	 * 获取分类数的所有信息
	 */
	public function get_product_class_tree()
	{
		admin_priv();

		$this->load->model('product_model');
		$p_c_info = $this->product_model->get_somenode_product_class_tree();
		$this->load->library('json');
		echo $this->json->encode($p_c_info);
	}

	/**
	 * 插入分类
	 */
	public function insert_product_class()
	{
		admin_priv();

		$p_c_pid = $this->input->post('p_c_p_id');
		$p_c_name = $this->input->post('p_c_name');
		$this->load->model('product_model');
		$p_c_id = $this->product_model->insert_product_class($p_c_pid,$p_c_name);
		if( ! $p_c_id)
		{
			make_json_error();
		}
		else
		{
			make_json_result($p_c_id);
		}
	}

	/**
	 * 修改分类
	 */
	public function update_product_class()
	{
		admin_priv();

		$p_c_id = $this->input->post('p_c_id');
		$p_c_name = $this->input->post('p_c_name');
		$this->load->model('product_model');
		$result = $this->product_model->update_product_class($p_c_id,$p_c_name);
		make_simple_response($result);
	}

	/**
	 * 删除分类
	 */
	public function delete_product_class()
	{
		admin_priv();

		$p_c_pid = $this->input->post('p_c_pid');
		if($p_c_pid != 0)
		{
			$p_c_id = $this->input->post('p_c_id');
			$this->load->model('product_model');
			$result = $this->product_model->delete_product_class($p_c_id);
			make_simple_response($result);
		}
		else
		{
			make_json_error('不能删除顶级产品分类');
		}
	}


	//============ 产品  ===============================================

	/**
	 * 产品列表
	 */
	public function product_list()
	{
		admin_priv('cpgllb');

		//得到列表显示字段
		$this->load->model("datagrid_confirm_model");
		$product_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_PRODUCT);
		$this->smarty->assign("product_display_field",$product_display_field);

		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);
		//权限：产品(删除)
		$power_view_delete       = check_authz("power_view_delete");
		$this->smarty->assign("power_view_delete",$power_view_delete?$power_view_delete:0);
		//权限：产品(添加)
		$power_view_insert       = check_authz("power_view_insert");
		$this->smarty->assign("power_view_insert",$power_view_insert?$power_view_insert:0);
		//权限：产品(编辑)
		$power_view_update       = check_authz("power_view_update");
		$this->smarty->assign("power_view_update",$power_view_update?$power_view_update:0);

		$this->smarty->display("product_list.htm");
	}

	/**
	 * 获取产品列表数据
	 */
	public function get_product_query()
	{
		admin_priv('cpgllb',false);

		//检索条件
		$condition = $this->input->post();
		//检索字段
		$this->load->model('datagrid_confirm_model');
		//$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_COMFIRM_PRODUCT);
		//数据
		$this->load->model('product_model');
		$responce = $this->product_model->get_product_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 产品 - 基本搜索
	 */
	public function product_base_search()
	{
		$this->smarty->display('product_search_base.htm');
	}

	/**
	 * 产品 - 高级搜索
	 */
	public function product_advance_search()
	{
		admin_priv();

		//高级搜索 逻辑条件
		$logical_condition = array('='=>'等于','!='=>'不等于','like'=>'包含','>'=>'大于','<'=>'小于','>='=>'大于等于','<='=>'小于等于');
		$this->smarty->assign('condition',$logical_condition);

		//得到产品自定义字段信息
		$this->load->model("field_confirm_model");
		$product_confirm_fields_info = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);
		$field_confirm = array();
		for($i=0;$i<count($product_confirm_fields_info);$i++)
		{
			$field_confirm[$product_confirm_fields_info[$i]['id']] = $product_confirm_fields_info[$i]['name'];
		}
		$this->smarty->assign("field_confirm",$field_confirm);
		$this->smarty->assign("field_confirm_selected",0);

		$this->smarty->display('product_search_advan.htm');
	}

	/**
	 * 获取添加页面
	 */
	public function add_product()
	{
		admin_priv('power_view_insert');

		//权限：自定义字段
		$power_fieldsconfirm_product       = check_authz("xtglzdpz");
		$this->smarty->assign("power_fieldsconfirm_product",$power_fieldsconfirm_product?$power_fieldsconfirm_product:0);

		$this->load->model('product_model');
		$product_num = $this->product_model->new_product_number();
		$this->smarty->assign("product_num",$product_num);

		$this->load->model("field_confirm_model");
		$product_confirm = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);//得到 自定义信息
		$parent_id = array(); //下拉选项field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach ($product_confirm AS $item)
		{
			if($item['data_type']==DATA_TYPE_SELECT)
			{
				$parent_id[] = $item["id"];
			}
			else if($item['data_type']==DATA_TYPE_JL)//级联field_id
			{
				$jl_id[] = $item["id"];
				$jl_field[$item["id"]] = $item["fields"];
				$jl_field_type[$item["id"]] = $this->json->decode($item["jl_field_type"],1);
			}
			else if($item['data_type']==DATA_TYPE_CHECKBOXJL)
			{
				$checkbox_id[]=$item['id'];
			}
		}
		$this->smarty->assign("product_confirm",$product_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));

		//自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		//自定义字段 级联
		$jl_options = array();
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义
		}
		$this->smarty->assign("jl_options",$jl_options);

		$checkbox_options = array();//关联多选框所有选项信息
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);
		}
		$this->smarty->assign("checkbox_options",$checkbox_options);

		$this->smarty->assign('other_pic_count',8);
        $this->smarty->assign('now_time',date('Y-m-d H:i:s'));
        $this->smarty->assign('now_date',date('Y-m-d'));
		$this->smarty->display('product_info.htm');
	}

	/**
	 * 添加产品
	 */
	public function insert_product()
	{
		admin_priv('power_view_insert',false);

		$inarray = $this->input->post();

		$this->load->model('product_model');
		//上传图片
		$pic = $this->product_model->upload_pic('product_pic');
		if(!empty($pic))
		{
			$inarray['product_pic'] = $pic['pic'];
			$inarray['product_thum_pic'] = $pic['pic_thumb'];
		}
		for($i=1;$i<=8;$i++)
		{
			$pic_other = $this->product_model->upload_pic('product_other_pic'.$i);

			if(!empty($pic_other))
			{
				$inarray['product_other_pic'.$i] = $pic_other['pic'];
				$inarray['product_other_thum_pic'.$i] = $pic_other['pic_thumb'];
			}
		}

		$result = $this->product_model->insert_product($inarray);
		if($result != 0)
		{
			$link[0]['text'] = '添加新产品';
			$link[0]['href'] = 'index.php?c=product&m=add_product';
			$link[1]['text'] = '返回产品列表';
			$link[1]['href'] = 'index.php?c=product&m=product_list';
			sys_msg('添加成功',0,$link);
		}
		else
		{
			$link[0]['text'] = '返回';
			$link[0]['href'] = 'index.php?c=product&m=add_product';
			sys_msg('添加失败',0,$link);
		}
	}

	/**
	 * 获取编辑页面
	 */
	public function edit_product()
	{
		admin_priv('power_view_update');

		//权限：自定义字段
		$power_fieldsconfirm_product       = check_authz("xtglzdpz");
		$this->smarty->assign("power_fieldsconfirm_product",$power_fieldsconfirm_product?$power_fieldsconfirm_product:0);

		$product_id = $this->input->get('product_id');
		if(empty($product_id))
		{
			die("缺少参数！");
		}
		$this->load->model('product_model');
		$product_info = $this->product_model->get_product_info($product_id);

		$this->load->model("field_confirm_model");
		$product_confirm = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);//得到自定义字段
		$_confirm = array();
		$parent_id = array();//自定义字段 下拉选项
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach ($product_confirm AS $item)
		{
			if($item['data_type']==DATA_TYPE_SELECT)
			{
				$parent_id[] = $item["id"];
			}
			else if($item['data_type']==DATA_TYPE_JL)//级联field_id
			{
				$jl_id[] = $item["id"];
				$jl_field[$item["id"]] = $item["fields"];
				$item["jl_field_type"] = $this->json->decode($item["jl_field_type"],1);
				$jl_field_type[$item["id"]] = $item["jl_field_type"];
			}
			else if($item['data_type']==DATA_TYPE_CHECKBOXJL)
			{
				$checkbox_id[]=$item['id'];
				if(!empty($product_info[$item["fields"].'_1']))
				{
					$check_1 = explode(',',$product_info[$item["fields"].'_1']);
					foreach($check_1 as $value)
					{
						$product_info[$item["id"].'_1'][$value] = $value;
					}
				}
				if(!empty($product_info[$item["fields"].'_2']))
				{
					$check_2 = explode(',',$product_info[$item["fields"].'_2']);
					foreach($check_2 as $value)
					{
						$product_info[$item["id"].'_2'][$value] = $value;
					}
				}
			}
			$_confirm[] = $item;
		}
		$this->smarty->assign("product_confirm",$_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));

		//自定义字段 下拉选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);
		//自定义字段 级联
		$jl_options = array();//级联自定义选项
		$jl_p_id = array(0);//级联自定义字段父id
		if(!empty($jl_id))
		{
			foreach($jl_field as $k=>$v)
			{
				if(!empty($product_info[$v.'_1']))
				{
					$jl_p_id[] = $product_info[$v.'_1'];
				}
				if(!empty($product_info[$v.'_2']))
				{
					$jl_p_id[] = $product_info[$v.'_2'];
				}
			}
			$jl_options = $this->field_confirm_model->get_jl_first_options($jl_id,$jl_p_id,$jl_field);//级联自定义选项
		}
		$this->smarty->assign("jl_options",$jl_options);
		
		$checkbox_options = array();//级联自定义选
		if(!empty($checkbox_id))
		{
			$checkbox_options = $this->field_confirm_model->get_checkbox_options($checkbox_id);//级联自定义选项
		}
		$this->smarty->assign("checkbox_options",$checkbox_options);

		$this->smarty->assign('product_info',$product_info);
		$this->smarty->display('product_info.htm');
	}

	/**
	 * 编辑产品
	 */
	public function update_product()
	{
		admin_priv('power_view_update',false);

		$inarray = $this->input->post();
		$product_id = $inarray['product_id'];

		$this->load->model('product_model');
		//上传图片
		$pic = $this->product_model->upload_pic('product_pic');
		if(!empty($pic))
		{
			$inarray['product_pic'] = $pic['pic'];
			$inarray['product_thum_pic'] = $pic['pic_thumb'];
		}

		for($i=1;$i<=8;$i++)
		{
			$pic_other = $this->product_model->upload_pic('product_other_pic'.$i);
			if(!empty($pic_other))
			{
				$inarray['product_other_pic'.$i] = $pic_other['pic'];
				$inarray['product_other_thum_pic'.$i] = $pic_other['pic_thumb'];
			}
		}

		$result = $this->product_model->update_product($product_id,$inarray);
		if($result != 0)
		{
			$link[0]['text'] = '返回编辑产品';
			$link[0]['href'] = 'index.php?c=product&m=edit_product&product_id='.$product_id;
			sys_msg('修改成功',0,$link);
		}
		else
		{
			$link[0]['text'] = '返回';
			$link[0]['href'] = 'javascript:history(-1)';
			sys_msg('修改失败',0,$link);
		}
	}

	/**
	 * 删除产品
	 */
	public function delete_product()
	{
		admin_priv('power_view_delete');

		$product_id = $this->input->post('product_ids');
		$this->load->model('product_model');
		$result = $this->product_model->delete_product($product_id);
		make_simple_response($result);
	}

	/**
	 * 改变产品状态
	 */
	public function set_product_state()
	{
		$product_id = $this->input->post('product_id');
		$product_state = $this->input->post('product_state');
		$this->load->model('product_model');
		$result = $this->product_model->set_product_state($product_id,$product_state);
		make_simple_response($result);
	}

	/**
	 * 展示商品原图
	 *
	 */
	public function show_pic()
	{
		admin_priv();

		$product_id = $this->input->get('product_id');
		if(empty($product_id))
		{
			sys_msg('缺少参数');
		}
		$this->load->model('product_model');
		$product_info = $this->product_model->get_product_info($product_id);
		$product_pic = $product_info['product_pic'];
		$this->smarty->assign("product_pic",$product_pic);
		$this->smarty->display('product_pic.htm');
	}

	/**
	 * 展示产品 （只看不能编辑） 
	 *
	 */
	public function view_product()
	{
		admin_priv();

		$product_id = $this->input->get('product_id');
		$this->load->model('product_model');
		$product_info = $this->product_model->get_product_info($product_id);
		$this->smarty->assign('product_info',$product_info);

		$this->load->model("field_confirm_model");
		//自定义字段 选项信息
		$parent_id = array();
		//得到 自定义字段
		$product_confirm = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);
		$this->smarty->assign("product_confirm",$product_confirm);
		$this->smarty->display('product_view.htm');
	}

	/**
	 * 订单产品 combbox
	 */
	public function get_product_box()
	{
		admin_priv();

		$key_value = $this->input->post("q");

		$this->load->model("product_model");
		$result = $this->product_model->get_product_box($key_value);
		$this->load->library("json");
		echo $this->json->encode($result);
	}

	/**
	 * 删除产品图片
	 */
	public function delete_product_pic()
	{
		admin_priv();

		$product_id = $this->input->post('product_id');
		$product_only_name = $this->input->post('product_only_name');
		$pic = $this->input->post('pic');
		$pic_thumb = $this->input->post('thumb_pic');

		$this->load->model('product_model');
		$result = $this->product_model->delete_product_pic($product_id,$product_only_name,$pic,$pic_thumb);
		make_simple_response($result);
	}
}