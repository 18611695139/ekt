<?php
/**
 * This file is part of ekt_ci.
 * Author: louxin
 * Date: 14-1-20
 * Time: 下午4:24
 * File: form.php
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flow extends CI_Controller
{
	public function index()
	{
		$this->smarty->display('flow/flow_list.htm');
	}

	/**
	 * 获取流程列表数据
	 */
	public function get_flow_list_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('flow_model');
		$responce = $this->flow_model->get_flow_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 流程添加页面
	 *
	 */
	public function add_flow()
	{
		//查询所有坐席
		$this->load->model('user_model');
		$result = $this->user_model->get_users();
		$users = array();
		foreach ($result as $user) {
			$users[$user['user_id']] = $user['user_name'];
		}
		$this->smarty->assign('users', json_encode($users));

		//查询所有部门
		$this->load->model('department_model');
		$result = $this->department_model->get_all_department();
		$departments = array();
		foreach ($result as $department) {
			$departments[$department['dept_id']] = $department['dept_name'];
		}
		$this->smarty->assign('departments', json_encode($departments));

		//查询所有角色
		$this->load->model('role_model');
		$result = $this->role_model->get_all_roles();
		$roles = array();
		foreach ($result as $role) {
			$roles[$role['role_id']] = $role['role_name'];
		}
		$this->smarty->assign('roles', json_encode($roles));

		//获取表单
		$this->load->model('form_model');
		$form_info = $this->form_model->get_all_form_info();
		$this->smarty->assign('form_info', json_encode($form_info));
        $this->smarty->assign('form_info_del', json_encode(''));
		$this->smarty->assign('opter_txt','index.php?c=flow&m=insert_flow');
		$this->smarty->display('flow/flow.htm');
	}

	/**
	 * 执行添加流程
	 *
	 */
	public function insert_flow()
	{
		$insert_data = $this->input->post();
		if(empty($insert_data))
		{
			make_json_error();
			return;
		}
		$back[0]["text"] = "返回流程添加页面";
		$back[0]["href"] = "index.php?c=flow&m=add_flow";
		if(empty($insert_data['flow_name']))
		{
			sys_msg("添加失败，流程名称不能为空", 0, $back);
			return;
		}
		$this->load->library("json");
		$node_info = $this->json->decode($insert_data['node_info'],1);
		if(empty($node_info))
		{
			sys_msg("添加失败，流程下不能没有节点", 0, $back);
			return;
		}
		else
		{
			foreach($node_info as $key=>$node)
			{
				if(!empty($node))
				{
					if(!isset($node['node_name'])||empty($node['node_name']))
					{
						sys_msg("添加失败，"."节点名称不能为空", 0, $back);
						return;
					}
					elseif(empty($node['form_id']))
					{
						sys_msg("添加失败，".$node['node_name']."节点不能没有表单", 0, $back);
						return;
					}
					elseif(empty($node['action']))
					{
						sys_msg("添加失败，".$node['node_name']."节点不能没有动作", 0, $back);
						return;
					}
					else
					{
						foreach($node['action'] as $action)
						{
							if(empty($action['jump']))
							{
								sys_msg("添加失败，".$node['node_name'].' - '.$action['name']."动作跳转不能为空", 0, $back);
								return;
							}
						}
					}
				}
				else
				{
					unset($node_info[$key]);
				}
			}
		}
		$insert_data['node_info'] = $node_info;
		$this->load->model('flow_model');
		$result = $this->flow_model->insert_flow($insert_data);
		$link[0]["text"] = "返回流程列表";
		$link[0]["href"] = "index.php?c=flow";
		if($result)
		{
			sys_msg("添加成功", 0, $link);
		}
		else
		{
			sys_msg("添加失败", 0, $link);
		}
	}

	/**
	 * 流程编辑页面
	 *
	 * @return unknown
	 */
	public function edit_flow()
	{
		$flow_id = $this->input->get('flow_id');
		if (empty($flow_id))
		{
			sys_msg("流程不存在");
			return  false;
		}
		$this->smarty->assign('opter_txt','index.php?c=flow&m=update_flow');
		//流程、节点信息
		$this->load->model('flow_model');
		$flow_info = $this->flow_model->get_flow_info($flow_id);
		$this->smarty->assign('flow_info',$flow_info);
		//查询所有坐席
		$this->load->model('user_model');
		$result = $this->user_model->get_users();
		$users = array();
		foreach ($result as $user) {
			$users[$user['user_id']] = $user['user_name'];
		}
		$this->smarty->assign('users', json_encode($users));
		//查询所有部门
		$this->load->model('department_model');
		$result = $this->department_model->get_all_department();
		$departments = array();
		foreach ($result as $department) {
			$departments[$department['dept_id']] = $department['dept_name'];
		}
		$this->smarty->assign('departments', json_encode($departments));
		//查询所有角色
		$this->load->model('role_model');
		$result = $this->role_model->get_all_roles();
		$roles = array();
		foreach ($result as $role) {
			$roles[$role['role_id']] = $role['role_name'];
		}
		$this->smarty->assign('roles', json_encode($roles));
		//获取表单
		$this->load->model('form_model');
		$form_info = $this->form_model->get_all_form_info();

        $node_info = json_decode($flow_info['node_info'],true);

        $form_info_del = array();
        foreach ($node_info as $v){
            $form = $this->form_model->get_form_info($v['form_id']);
            if (empty($v['form_id']) || empty($form) || is_array($v['form_id'])) {
                continue;
            }
            if ($form['is_del'] == 1) $form_info_del[] = $v['form_id'];
            $form_info[$v['form_id']] = $form['form_name'];
        }

		$this->smarty->assign('form_info', json_encode($form_info));
        $this->smarty->assign('form_info_del', json_encode($form_info_del));

		$this->smarty->display('flow/flow.htm');
	}

	/**
	 * 执行修改流程
	 *
	 */
	public function update_flow()
	{
		$update = $this->input->post();
		$flow_id = $this->input->post('flow_id');
		if(empty($update)||empty($flow_id))
		{
			make_json_error();
			return;
		}

		$back[0]["text"] = "返回流程编辑页面";
		$back[0]["href"] = "index.php?c=flow&m=edit_flow&flow_id=".$flow_id;
		if(empty($update['flow_name']))
		{
			sys_msg("编辑失败，流程名称不能为空", 0, $back);
			return;
		}
		$this->load->library("json");
		$node_info = $this->json->decode($update['node_info'],1);
		if(empty($node_info))
		{
			sys_msg("编辑失败，流程下不能没有节点", 0, $back);
			return;
		}
		else
		{
			foreach($node_info as $node)
			{
				if(!empty($node))
				{
					if(!isset($node['node_name'])||empty($node['node_name']))
					{
						sys_msg("编辑失败，"."节点名称不能为空", 0, $back);
						return;
					}
					if(empty($node['form_id']))
					{
						sys_msg("编辑失败，".$node['node_name']."不能没有表单", 0, $back);
						return;
					}
					elseif(empty($node['action']))
					{
						sys_msg("编辑失败，".$node['node_name']."不能没有动作", 0, $back);
						return;
					}
					else
					{
						foreach($node['action'] as $action)
						{
							if(empty($action['jump']))
							{
								sys_msg("编辑失败，".$node['node_name'].' - '.$action['name']."动作跳转不能为空", 0, $back);
								return;
							}
						}
					}
				}
			}
		}

		$update['node_info'] = $node_info;
		$this->load->model('flow_model');
		$result = $this->flow_model->update_flow($flow_id,$update);
		$link[0]["text"] = "返回流程列表";
		$link[0]["href"] = "index.php?c=flow";
		if($result)
		{
			sys_msg("编辑成功", 0, $link);
		}
		else
		{
			sys_msg("编辑失败", 0, $link);
		}
	}

	/**
     * 删除流程
     */
	public function delete_flow()
	{
		admin_priv();
		$flow_id = $this->input->post('flow_id');
		$flow_id = explode(',',$flow_id);
		$this->load->model('flow_model');
		$result = $this->flow_model->delete_flow($flow_id);
		make_simple_response($result);
	}

}