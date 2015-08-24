<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 我的消息
 * ============================================================================
 * 版权所有 2009-2012 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : message.php 
 * $Author: yhx
 * $time  : Mon Jul 23 18:08:01 CST 2012
*/
class Message extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 翻译快捷搜索
	 *  @param int $data_type   数据类型
	 * @return array
	 */
	private function translation_data_type($data_type)
	{
		$condition = array();
		$user_id       = $this->session->userdata("user_id");
		switch ($data_type) {
			case '-1':{//收件箱
				$condition['msg_receive_user_id'] = $user_id;
				break;
			}
			case '-2':{//发件箱
				$condition['msg_send_user_id'] = $user_id;
				break;
			}
			case '1':{//全部数据
				break;
			}
			case '2':{//未读
				$condition['msg_if_readed'] = 0;
				break;
			}
			case '3':{//已读
				$condition['msg_if_readed'] = 1;
				break;
			}
			default:
				break;
		}
		return $condition;
	}

	/**  消息管理 - 收件箱  -1
	 * Message::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv('wdzswdxx');
		
		//权限：发消息
		$power_sendxx   = check_authz("wdzswdxx_sendxx");
		$this->smarty->assign("power_sendxx",$power_sendxx?$power_sendxx:0);
		
		$this->smarty->display("message_list.htm");
	}

	/**
	 * 消息管理 - 获取收件箱/发件箱数据
	 *
	 */
	public function get_message_query()
	{
		admin_priv('wdzswdxx',false);
		
		$condition = $this->input->post();
		//数据类型
		if (!empty($condition["data_type"]))
		{
			$condition_data_type = $this->translation_data_type($condition["data_type"]);
			$condition = array_merge($condition,$condition_data_type);
		}
		//-1收件箱    -2发件箱
		if (!empty($condition["msg_type"]))
		{
			$condition_msg_type = $this->translation_data_type($condition["msg_type"]);
			$condition = array_merge($condition,$condition_msg_type);
		}

		$this->load->model("message_model");
		$respance = $this->message_model->get_message_list($condition);
		$this->load->library("json");
		echo $this->json->encode($respance);
	}

	/**
	 * 消息管理 - 消息列表 - 删除消息
	 *
	 */
	public function delete_message()
	{
		admin_priv();
		
		$msg_id = $this->input->post("msg_id");
		$this->load->model("message_model");
		$result = $this->message_model->delete_message($msg_id);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行错误");
		}
	}

	/**
	 * 消息管理 - 收件箱 - 标记为已读
	 *
	 */
	public function update_read()
	{
		admin_priv();
		
		$msg_id = $this->input->post("msg_id");
		$msg_id = $msg_id ? $msg_id : 0;

		$this->load->model("message_model");
		$msg_id = explode(',',$msg_id);
		$result = $this->message_model->set_message_readed($msg_id);
		if ($result)
		{
			make_json_result(1);
		}
		else
		{
			make_json_error("执行错误");
		}
	}

	/**
	 * 查看信息
	 *
	 */
	public function message_view()
	{
		admin_priv();
		
		$msg_id = $this->input->post("msg_id");
		$this->load->model("message_model");
		$result = $this->message_model->get_msg_info($msg_id);
		if ($result)
		{
			$this->message_model->set_message_readed($msg_id);
			make_json_result($result["msg_content"]);
		}
		else
		{
			make_json_error("执行错误");
		}
	}

	/**
	 * 消息管理 - 发件箱  -2
	 *
	 */
	public function send_box()
	{
		$this->smarty->display("message_send_list.htm");
	}

	/**
	 * 发消息窗口
	 *
	 */
	public function message_panel()
	{
		$selected_id = $this->input->get("selected_id");
		$this->smarty->assign("selected_id",$selected_id);

		//坐席发信息，每条信息最大字数
		$this->smarty->assign("MSG_MAX_LENGTH",MSG_MAX_LENGTH);
		
		$this->smarty ->display("message_panel.htm");
	}

	/**
	 * 发消息
	 *
	 */
	public function send_message()
	{
		admin_priv();
		
		$content     = stripcslashes($this->input->post('content'));
		$user_ids    = explode(',',$this->input->post('user_ids'));
		if(empty($content) || empty($user_ids))
		{
			return false;
		}
		$this->load->model("message_model");
		$result = $this->message_model->send_message($content,$user_ids);
		make_simple_response($result);
	}
	
	/*获取树*/
	public function get_dept_user_tree()
	{
		admin_priv();
		
		$selected_id = $this->input->get("selected_id");

		$this->load->model('message_model');
		$dept_users = $this->message_model->get_message_sender_tree($selected_id);
		$this->load->library('json');
		echo $this->json->encode($dept_users);
	}
}