<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends CI_Controller {

	/**
	 * 编辑角色
	 *
	 */
	public function edit_role()
	{
		admin_priv('xtgljsgl');
		
		$this->load->model('role_model');
		$roles	= $this->role_model->get_role_list();
		$this->smarty->assign('roles',$roles);
		$role_types	= $this->role_model->get_role_type_list();
		$this->smarty->assign('role_types',$role_types);
		$authority	= $this->role_model->get_authority_list();
		$this->smarty->assign('authority',$authority);
		$this->smarty->display('role_info.htm');
	}

	/**
	 * 返回角色信息
	 *
	 */
	public function get_role_info()
	{
		admin_priv();
		
		$role_id = $this->input->post('role_id');
		$this->load->model('role_model');
		$role_info	= $this->role_model->get_role_info($role_id);
		make_json_result('','',$role_info);
	}

	public function insert_role()
	{
		admin_priv();
		
		$role_name = $this->input->post('role_name');
		$role_type = $this->input->post('role_type');
		$this->load->model('role_model');
		$role_id = $this->role_model->insert_role($role_name,$role_type);
		if($role_id)
		{
			make_json_result($role_id);
		}
		else 
		{
			make_json_error();
		}
	}

	public function update_role()
	{
		admin_priv();
		
		$role_id	= $this->input->post('role_id');
		$role_name	= $this->input->post('role_name');
		$role_type	= $this->input->post('role_type');
		$this->load->model('role_model');
		$result = $this->role_model->update_role($role_id,$role_name,$role_type);
		make_simple_response($result);
	}

	/**
	 * 设置角色内容
	 *
	 */
	public function set_role_action_list()
	{
		admin_priv();
		
		$role_id	= $this->input->post('role_id');
		$role_action_list	= $this->input->post('role_action_list');
		$this->load->model('role_model');
		$result = $this->role_model->set_role_action_list($role_id,$role_action_list);
		make_simple_response($result);
	}
	
	public function delete_role()
	{
		admin_priv();
		
		$role_id = $this->input->post('role_id');
		if($role_id==1)
		{
			make_json_error('');
			return;
		}
		$this->load->model('role_model');
		$result = $this->role_model->delete_role($role_id);
		make_simple_response($result);
	}
}

/* End of file user.php */
/* Location: D:\wamp\www\easycall_ci\app\controllers\user.php */