<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Busy extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		admin_priv();
		$reason_info = array();
		$this->load->model("busy_model");
		$reason_info = $this->busy_model->get_busy_reason();
		$this->smarty->assign('reason_info',$reason_info);
		$stat = $this->input->get('stat');
		$this->smarty->assign('stat',$stat);
		$this->smarty->display('busy_reason.htm');
	}

	/**
	 * 添加置忙原因
	 */
	public function save_busy_reason()
	{
		admin_priv();
		$option = $this->input->post('option');
		$old_option = $this->input->post('old_option');
		$option = !is_array($option) ? array() : $option;
		$old_option = !is_array($old_option) ? array() : $old_option;
		foreach($option as $k=>$op)
		{
			if(empty($op))
			{
				unset($option[$k]);
			}
		}
		$delete = $old_option;
		$insert = $option;
		if(!empty($option)&&!empty($old_option))
		{
			$delete = array_diff($old_option,$option);
			$insert = array_diff($option,$old_option);
		}

		if(empty($delete)&&empty($insert))
		{
			$links[0]['text'] = '返回置忙原因设置页面';
			$links[0]['href'] = 'index.php?c=busy';
			sys_msg('没作任何修改', 0, $links);
			return;
		}

		$this->load->model("busy_model");
		//删除
		if(!empty($delete))
		{
			$id_array = array_keys($delete);
			$ids = implode(',',$id_array);
			$result = $this->busy_model->delete_busy_reason($ids);
			if((!$result)||($result['code']!='200'))
			{
				$links[0]['text'] = '返回置忙原因设置页面';
				$links[0]['href'] = 'index.php?c=busy';
				sys_msg('保存设置失败,'.$result['message'], 0, $links);
				return;
			}
		}
		//添加
		if(!empty($insert))
		{
			$result = $this->busy_model->add_busy_reason(1,$insert);
			if((!$result) || ($result['code']!='200'))
			{
				$links[0]['text'] = '返回置忙原因设置页面';
				$links[0]['href'] = 'index.php?c=busy';
				sys_msg('保存设置失败,'.$result['message'], 0, $links);
				return;
			}
		}
		$links[0]['text'] = '返回置忙原因设置页面';
		$links[0]['href'] = 'index.php?c=busy';
		sys_msg('保存设置成功,退出重登系统生效', 0, $links);
	}
}