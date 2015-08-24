<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * EasyTone 公告
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : announcement.php 
 * $Author: yhx
 * $time  : Wed Jun 27 17:50:14 CST 2012
*/

class Announcement extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Announcement::index()
	 * 
	 * @return void 系统公告列表
	 */
	public function index()
	{
		//当前坐席
		$user_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_id',$user_id);
		
		//权限：公告（编辑）
		$power_announcement_change       = check_authz("wdzsggbj");
		$this->smarty->assign("power_announcement_change",$power_announcement_change?$power_announcement_change:0);
		
		$this->smarty->display("announcement_list.htm");
	}

	/**
     * 获取公告列表数据
     * 
     * @return void   列表数据
     */
	public function announcement_list()
	{
		$condition = array();
		//公告标题
		$condition['anns_title'] = $this->input->post("anns_title");
		//数据
		$this->load->model('announcement_model');
		$responce = $this->announcement_model->get_announcement_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
     * Announcement::add_announcement()
     * 
     * @return void   添加公告
     */
	public function add_announcement()
	{
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		
		$anns_info["dept_id"] = $this->session->userdata("dept_id");
		$this->smarty->assign("anns_info",$anns_info);
		
		$this->smarty->assign("ins_up","announcement_insert");
		$this->smarty->display("announcement_add.htm");
	}

	/**
     * Announcement::announcement_insert()
     * 
     * @return void   保存公告
     */
	public function announcement_insert()
	{
		$title = $this->input->post('title');
		if(empty($title))
		{
			$links[0]['text'] = "添加公告";
			$links[0]['href'] = 'index.php?c=announcement&m=add_announcement';
			sys_msg("公告标题不能为空",0,$links);
			return false;
		}
		$content = $this->input->post('ancontent');
		$title = replace_illegal_string($title); //标题
		
		$this->load->model('announcement_model');
		$dept_id = $this->input->post('department');
		if(empty($dept_id))
		{
			$dept_id = '';
		}
		$this->announcement_model->insert_announcement($title,$content,$dept_id);

		$links[0]['text'] = "公告列表";
		$links[0]['href'] = 'index.php?c=announcement';
		sys_msg('成功发布公告',0,$links);
	}

	/**
     * Announcement::announcement_delete()
     * 
     * @return void   删除公告
     */
	public function announcement_delete()
	{
		$anns  = $this->input->post("anns_id");
		$this->load->model('announcement_model');
		$result = $this->announcement_model->delete_announcement($anns);
		if($result==1)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error();
		}
	}

	/**
     * Announcement::announcement_edit()
     * 
     * @return void   编辑公告
     */
	public function announcement_edit()
	{		
		$anns_id = $this->input->get("anns_id");
		if ( empty($anns_id) ) 
		{
			$link[0]['text'] = "缺少必要参数";
			$link[0]['href'] = "index.php?c=announcement";
			sys_msg("缺少必要参数", 0, $link);
			return  false;
		}
		
		//公告信息
		$this->load->model('announcement_model');
		$anns_info = $this->announcement_model->get_announcementinfo($anns_id);
		//查看部门是否还在系统中
		$this->load->model('department_model');
		$dept_info = $this->department_model->get_department_info($anns_info['dept_id']);
		if(empty($dept_info))
		{
			$anns_info['dept_id'] = '';
		}
		$this->smarty->assign("anns_info",$anns_info);
		
		$this->smarty->assign("ins_up","announcement_update");
		$this->smarty->display("announcement_add.htm");
	}

	/**
     * Announcement::announcement_update()
     * 
     * @return void   更新公告信息
     */
	public function announcement_update()
	{
		$anns_id    = $this->input->post("anns_id");
		$title      = $this->input->post("title");
		$ancontent  = $this->input->post("ancontent");
		$department = $this->input->post("department");
		
		$this->load->model('announcement_model');
		$result = $this->announcement_model->update_annsinfo($anns_id,$title,$ancontent,$department);
		if($result === true)
		{
			$links[0]['text'] = "系统公告列表";
			$links[0]['href'] = 'index.php?c=announcement';
			sys_msg('系统公告列表',0,$links);
		}
		else
		{
			$links[0]['text'] = "编辑公告";
			$links[0]['href'] = 'index.php?c=announcement&m=announcement_edit&anns_id='.$anns_id;
			sys_msg('编辑公告失败，重新保存',0,$links);
		}
	}

	/**
     * Announcement::announcement_view()
     * 
     * @return void  查看公告信息
     */
	public function announcement_view()
	{
		$anns_id = $this->input->get("anns_id");
		$this->load->model('announcement_model');
		$anns_info = $this->announcement_model->get_announcementinfo($anns_id);
		$this->smarty->assign("anns_info",$anns_info);
		$this->smarty->display("announcement_view.htm");
	}
}


/* End of file  announcement.php*/
/* Location: ./app/controllers/announcement.php */