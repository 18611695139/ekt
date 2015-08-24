<?php
/**
 * This file is part of ekt_ci.
 * Author: louxin
 * Date: 14-1-20
 * Time: 下午4:24
 * File: form.php
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Form extends CI_Controller
{
	public function index()
	{
		$this->smarty->display('form/form_list.htm');
	}

	/**
     * 获取表单列表数据
     */
	public function get_form_list_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('form_model');
		$responce = $this->form_model->get_form_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
     * 添加保单页面
     */
	public function add_form()
	{
		$this->smarty->assign('opter_txt','index.php?c=form&m=insert_form');
		$this->smarty->display('form/form.htm');
	}

	public function loadAttribute()
	{
		$type = $this->input->get('type');
		$fileName = $type . 'Attribute.htm';
		$this->smarty->display('form/'.$fileName);
	}

	/**
     * 执行添加
     */
	public function insert_form()
	{
		admin_priv();
		$form_name = $this->input->post('form_name');
		$form_html = $this->input->post('html');
		$cascadeOption = $this->input->post('cascadeOption');
		if(empty($form_name)||empty($form_html))
		{
			make_json_error();
			return;
		}
		$this->load->model('form_model');
		$result = $this->form_model->insert_form($form_name,$form_html,$cascadeOption);
		$link[0]["text"] = "返回表单工单列表";
		$link[0]["href"] = "index.php?c=form";
		if($result)
		{
			sys_msg("保存成功", 0, $link);
		}
		else
		{
			sys_msg("保存失败", 0, $link);
		}
	}

	/**
     * 删除保单
     */
	public function delete_form()
	{
		admin_priv();
		$form_id = $this->input->post('form_id');
		$form_id = explode(',',$form_id);
		$this->load->model('form_model');
		$result = $this->form_model->delete_form($form_id);
		make_simple_response($result);
	}

	/**
     * 编辑页面
     */
	public function edit_form()
	{
		$form_id = $this->input->get('form_id');
		if (empty($form_id))
		{
			sys_msg("该表单不存在");
			return  false;
		}
		$this->smarty->assign('opter_txt','index.php?c=form&m=update_form');
		$this->load->model('form_model');
		$form_info = $this->form_model->get_form_info($form_id);
		$this->smarty->assign('form_info',$form_info);
		$this->smarty->display('form/form.htm');
	}

	/**
     * 执行编辑
     */
	public function update_form()
	{
		admin_priv();
		$form_id = $this->input->post('form_id');
		$form_name = $this->input->post('form_name');
		$form_html = $this->input->post('html');
		if(empty($form_id)||empty($form_name)||empty($form_html))
		{
			make_json_error();
			return;
		}
		$this->load->model('form_model');
		$result = $this->form_model->update_form($form_id,$form_name,$form_html);
		$link[0]["text"] = "返回表单工单列表";
		$link[0]["href"] = "index.php?c=form";
		if($result)
		{
			sys_msg("保存成功", 0, $link);
		}
		else
		{
			sys_msg("保存失败", 0, $link);
		}
	}

    /**
     * 获取级联框选项编辑页面
     */
    public function get_casede_option_page()
    {
        $type = $this->input->get('type');
        $this->smarty->assign('casecadeType',$type);
        $parent = $this->input->get('parent');
        $this->smarty->assign('parent',$parent);
        $this->smarty->display('form/cascadeOption.htm');
    }

	/**
     * 获取级联自定义选项信息
     */
	public function get_cascade_info()
	{
		admin_priv();
		$form_id = $this->input->post('form_id');
		$parent_id = $this->input->post('parent_id');
		$field = $this->input->post('field');

		$this->load->model('form_model');
		$cascade_info = $this->form_model->get_cascade_info($form_id,$field,$parent_id);
		make_json_result($cascade_info);
	}

    /**
     * 添加级联选项
     */
    public function insert_cascade()
	{
		admin_priv();
		$form_id = $this->input->post('form_id');
		$parent_id = $this->input->post('parent_id');
		$name = $this->input->post('name');
		$deep = $this->input->post('deep');
		$field = $this->input->post('field');
		$this->load->model('form_model');
		$result = $this->form_model->insert_cascade($form_id,$parent_id,$name,$deep,$field);
		make_simple_response($result);
	}

    /**
     * 编辑级联选项
     */
    public function update_cascade()
	{
		admin_priv();
		$id = $this->input->post('id');
		$name = $this->input->post('name');

		if(empty($id))
		{
			make_json_error();
			return;
		}
		$this->load->model('form_model');
		$result = $this->form_model->update_cascade($id,$name);
		make_simple_response($result);
	}

    /**
     * 删除级联选项
     */
    public function delete_cascade()
	{
		admin_priv();
		$id = $this->input->post('id');
		$deep = $this->input->post('deep');
		$series = $this->input->post('series');

		if(empty($id)||empty($deep))
		{
			make_json_error();
			return;
		}

		$this->load->model('form_model');
		$result = $this->form_model->delete_cascade($id,$deep,$series);
		make_simple_response($result);
	}

}