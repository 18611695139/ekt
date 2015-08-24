<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department extends CI_Controller {

	private $max_tree_deep = 6;

	/**
	 * 返回所属部门和下级部门的部门树
	 *
	 */
	public function get_department_tree()
	{
		admin_priv();

		$gl_all_dept = $this->input->get('gl_all_dept');
		if($gl_all_dept)
		{
			$dept_id = 1;//全部
		}
		else 
		{
			$dept_id   = $this->session->userdata('dept_id');
		}
		$this->load->model('department_model');
		$department = $this->department_model->get_somenode_department_tree($dept_id);
		$this->load->library('json');
		echo $this->json->encode($department);
	}

	/**
	 * 编辑部门页面
	 *
	 */
	public function edit_department()
	{
		admin_priv('xtglbmgl');

		$session_dept_id   = $this->session->userdata("dept_id");
		$this->smarty->assign("session_dept_id",$session_dept_id);

		$this->smarty->assign("max_tree_deep",$this->max_tree_deep);
		$this->smarty->display('department_info.htm');
	}
	/**
	 * 编辑部门
	 *
	 */
	public function update_department()
	{
		admin_priv('xtglbmgl');

		$dept_id = $this->input->post('dept_id');
		$dept_name = $this->input->post('dept_name');
		$this->load->model('department_model');
		$result = $this->department_model->update_department($dept_id,$dept_name);
		make_simple_response($result);
	}

	/**
	 * 添加部门
	 *
	 */
	public function insert_department()
	{
		admin_priv('xtglbmgl');

		$dept_deep = $this->input->post('dept_deep');
		if($dept_deep+1 > $this->max_tree_deep)
		{
			make_json_error('当前部门不能添加下一级部门');
			return;
		}
		$dept_p_id = $this->input->post('dept_p_id');
		$dept_name = $this->input->post('dept_name');
		$this->load->model('department_model');
		$dept_id = $this->department_model->insert_department($dept_p_id,$dept_name);
		if( ! $dept_id)
		{
			make_json_error();
		}
		else
		{
			make_json_result($dept_id);
		}
	}

	/**
	 * 删除部门
	 *
	 */
	public function delete_department()
	{
		admin_priv('xtglbmgl');

		$dept_deep = $this->input->post('dept_deep');
		if($dept_deep != 1)
		{
			$dept_id = $this->input->post('dept_id');

			//得到指定部门及其下级部门的所有坐席信息ID
			$this->load->model("user_model");
			$user_total = $this->user_model->get_dept_children_users($dept_id,true);
			if(empty($user_total))
			{
				$this->load->model('department_model');
				$result = $this->department_model->delete_department($dept_id);
				make_simple_response($result);
			}
			else
			{
				make_json_error('当前部门还有员工，删除失败');
			}
		}
		else
		{
			make_json_error('不能删除顶级部门');
		}
	}
}


/* End of file  department.php*/
/* Location: ./app/controllers/department.php */