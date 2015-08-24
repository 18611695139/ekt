<?php
/**
 * This file is part of ekt_ci.
 * Author: zgx
 * Date: 14-2-20
 * Time: 下午5:32
 * File: work_flow.php
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Work_flow extends CI_Controller
{
	public function index()
	{
		admin_priv();
		$cle_id = $this->input->get('cle_id');
		$this->smarty->assign('cle_id',$cle_id);
		$this->smarty->display('flow/work_flow_list_add.htm');
	}

	/**
     * 业务受理页面快捷创建工单
     */
	public function client_accept_add_workflow()
	{
		admin_priv();
		$cle_id = $this->input->get('cle_id');
		//查询所有的工单
		$this->load->model('flow_model');
		$flows = $this->flow_model->get_all_flow();
		$this->smarty->assign('flows', $flows);
		$this->smarty->assign('cle_id', $cle_id);
		$this->smarty->display('flow/client_accept_add_workflow.htm');
	}

	/**
	 * 创建工单
	 *
	 * @return void
	 */
	public function add_work_flow()
	{
		admin_priv();
		$now_time = time();//当前时间
		$flow_id = $this->input->get('flow_id');
		$cle_id = $this->input->get('cle_id');
		if (empty($flow_id)|| !is_numeric($flow_id))
		{
			sys_msg("流程不存在");
			return;
		}
		$this->smarty->assign('flow_id',$flow_id);
		$this->smarty->assign('cle_id',$cle_id);

		//流程信息
		$this->load->model('flow_model');
		$flow_info = $this->flow_model->get_flow_info($flow_id);
		if($flow_info)
		{
			//流程有效时间判断
			if($flow_info['valid_from']!='0000-00-00 00:00:00')
			{
				if($now_time<strtotime($flow_info['valid_from']))
				{
					sys_msg("该工单还没到有效时间,不能创建");
					return;
				}
			}
			if($flow_info['valid_to']!='0000-00-00 00:00:00')
			{
				if($now_time>=strtotime($flow_info['valid_to']))
				{
					sys_msg("该工单已超过有效时间,不能创建");
					return;
				}
			}
			$this->smarty->assign('flow_name',$flow_info['flow_name']);
			//获取第一个节点信息
			$this->load->library("json");
			$node_info = $this->json->decode($flow_info['node_info'],1);
			$first_node = array_shift($node_info);//第一个节点
			$this->smarty->assign('node_info',array($first_node['node_id']=>$first_node));
			$this->smarty->assign('this_node',$first_node['node_id']);
			//判断当前用户是否有权限
			$this->load->model('work_flow_model');
			$node_limit = $this->work_flow_model->check_node_limit($first_node['participant_type'],$first_node['node_participant']);
			if($node_limit)
			{
				//表单信息
				$this->load->model('form_model');
				$form_info[$first_node['node_id']] = $this->form_model->get_form_info($first_node['form_id']);
				$this->smarty->assign('form_info',$form_info);
				//表单字段信息
				$form_fields = $this->form_model->get_form_fields_info($first_node['form_id']);
				$this->smarty->assign('form_fields',array($first_node['node_id']=>$form_fields));
			}
			else
			{
				sys_msg("你没有权限操作该流程节点");
				return;
			}
			$this->smarty->assign('opter_txt','insert');
			$this->smarty->display('flow/work_flow.htm');
		}
		else
		{
			sys_msg("流程已不存在");
			return;
		}
	}

	/**
	 * 处理工单
	 *
	 * @return void
	 */
	public function edit_work_flow()
	{
		admin_priv();

		global $FLOWNODE_STATUS;
		$user_id = $this->session->userdata('user_id');//当前坐席id
		$flow_detail_id = $this->input->get('id');//当前流程详细记录id
		if (empty($flow_detail_id))
		{
			sys_msg("该工单不存在");
			return;
		}
		$this->smarty->assign('flow_detail_id',$flow_detail_id);

		$link[0]["text"] = "返回待处理工单列表";
		$link[0]["href"] = "index.php?c=work_flow&m=deal_list";

		//流程节点详细记录信息
		$this->load->model('work_flow_model');
		$detail = $this->work_flow_model->get_flow_detail_info($flow_detail_id);
		if(!$detail)
		{
			sys_msg("获取该流程详细记录失败",0,$link);
			return;
		}
		$this_node_status = $detail['node_status'];//当前节点状态
		$this->smarty->assign('this_node_status',$this_node_status);

		$flow_id = $detail['flow_id'];//当前流程id
		$this->smarty->assign('flow_id',$flow_id);

		$this->smarty->assign('cle_id',$detail['cle_id']);

		$this_node_id = $detail['node_id'];//当前节点id
		$this->smarty->assign('this_node',$this_node_id);

		$flow_info_id = $detail['flow_info_id'];//当前流程记录id
		$this->smarty->assign('flow_info_id',$flow_info_id);

		$this->smarty->assign('back_reason',$detail['back_reason']);//返回原因

		//流程信息
		$this->load->model('flow_model');
		$flow_info = $this->flow_model->get_flow_info($flow_id);
		if(!$flow_info || !isset($flow_info['node_info']))
		{
			sys_msg("获取该流程信息失败",0,$link);
			return;
		}
		$this->smarty->assign('flow_name',isset($flow_info['flow_name'])?$flow_info['flow_name']:'');//当前流程名称
		//流程下所有节点信息
		$this->load->library("json");
		$node = $this->json->decode($flow_info['node_info'],1);
		//判断当前用户是否有权限
		$node_limit = $this->work_flow_model->check_node_limit($node[$this_node_id]['participant_type'],$node[$this_node_id]['node_participant']);
		if(!$node_limit)
		{
			sys_msg("你没有权限操作该流程节点",0,$link);
			return;
		}
		//判断该节点是否已有人占用,若有返回提示，若无占用该数据
		if(!empty($detail['deal_user_id']))
		{
			if($detail['deal_user_id']!=$user_id)
			{
				if(isset($detail['user_name']) && isset($detail['user_num']))
				{
					sys_msg("该工单 ".$detail['user_name']."【".$detail['user_num']."】 已在处理", 0, $link);
					return;
				}
				else
				{
					sys_msg("该工单已有人在处理", 0, $link);
					return;
				}
			}
		}
		else
		{
			$this->load->model('work_flow_model');
			$this->work_flow_model->update_node_statues_user($flow_detail_id,$detail['flow_info_id'],$detail['node_id']);
		}

		//员工信息
		$this->load->model("user_model");
		$user_result = $this->user_model->get_all_users_without_dept();
		$user_info   = array();
		foreach ($user_result as $user)
		{
			$user_info[$user["user_id"]] = $user["user_name"];
		}
		//流程记录信息
		$flow_each_info = $this->work_flow_model->get_work_flow_info($flow_id,$flow_info_id);
		//节点记录信息整理
		$node_info = array();
		$form_ids = array();
		$node_form_ids = array();
		$flow_each_node = array();
		foreach($node as $nid =>$value)
		{
			if(($nid==$this_node_id)||($nid<$this_node_id && ($flow_each_info['node'.$nid.'_status']==2||$flow_each_info['node'.$nid.'_status']==4)))
			{
				$node_info[$nid] = $value;
				if(!in_array($value['form_id'],$form_ids))
				{
					$form_ids[] = $value['form_id'];
				}
				$node_form_ids[] = array('node_id'=>$nid,'form_id'=>$value['form_id']);
				$flow_each_node[$nid]['node_start_time'] = empty($flow_each_info['node'.$nid.'_start_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_start_time']);
				$flow_each_node[$nid]['node_end_time'] = empty($flow_each_info['node'.$nid.'_end_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_end_time']);
				$flow_each_node[$nid]['node_over_time'] = (empty($flow_each_info['node'.$nid.'_over_time'])||($flow_each_info['node'.$nid.'_start_time']==$flow_each_info['node'.$nid.'_end_time']))?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_over_time']);
				if(!empty($flow_each_info['node'.$nid.'_over_time']))
				{
					if(($nid==$this_node_id) &&(time()>$flow_each_info['node'.$nid.'_over_time']))
					{
						$flow_each_node[$nid]['is_over_time'] = 1;
					}
				}
				$flow_each_node[$nid]['node_user_name'] = empty($user_info[$flow_each_info['node'.$nid.'_user_id']])?'':$user_info[$flow_each_info['node'.$nid.'_user_id']];
				$flow_each_node[$nid]['node_status'] = !isset($FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']])?'':$FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']];
				$flow_each_node[$nid]['node_remark'] = $flow_each_info['node'.$nid.'_remark'];
				$flow_each_node[$nid]['node_description'] = $flow_each_info['node'.$nid.'_description'];
			}
		}
		$this->smarty->assign('flow_each_node',$flow_each_node);
		$this->smarty->assign('node_info',$node_info);//当前及以前处理过的节点信息
		//表单信息
		$form_info = array();
		$this->load->model('form_model');
		$form = $this->form_model->get_form_info($form_ids);
		//表单字段信息
		$form_fields_info = array();
		$form_fields = $this->form_model->get_more_form_fields($form_ids);
		//之前节点填写数据
		$original_form_data = array();
		foreach($node_form_ids as $nfid)
		{
			$form_info[$nfid['node_id']] = empty($form[$nfid['form_id']]) ? 0 : $form[$nfid['form_id']];//表单信息
			if($nfid['node_id']!=$this_node_id)
			{
				//之前节点表单字段加上节点id，以免到统一页面id重复
				$result = $this->work_flow_model->change_html_id_name($form_info[$nfid['node_id']]['form_html'],$nfid['node_id']);
				$new_html   = isset($result['html']) ? $result['html'] : '';
				if(!empty($new_html))
				{
					$form_info[$nfid['node_id']]['form_html'] = $new_html;
				}
			}
			//表单字段
			$form_fields_info[$nfid['node_id']] = $form_fields[$nfid['form_id']];
			//获取之前节点信息
			$original_form_data[$nfid['node_id']] = $this->work_flow_model->get_form_value_info($nfid['form_id'],$flow_id,$flow_info_id,$nfid['node_id']);
		}

		$this->smarty->assign('form_info',$form_info);//表单信息
		$this->smarty->assign('form_fields',$form_fields_info);
		$this->smarty->assign('original_form_data',$original_form_data);

		$this->smarty->assign('opter_txt','update');
		$this->smarty->display('flow/work_flow.htm');
	}

	/**
	 * 工单数据保存
	 *
	 */
	public function save_work_info()
	{
		admin_priv();
		$data = $this->input->post();
		$opter_txt = $this->input->post('opter_txt');
		$form_id = $this->input->post('form_id');
		$flow_id = $this->input->post('flow_id');
		$this_node_id = $this->input->post('this_node');
		$next_node_id = $this->input->post('next_node');
		$this->load->model('work_flow_model');
		if($opter_txt=='insert')
		{
			$result = $this->work_flow_model->insert_work_model($flow_id,$form_id,$this_node_id,$next_node_id,$data);
			if($result)
			{
				$link[0]["text"] = "返回查看工单";
				$link[0]["href"] = "index.php?c=work_flow&m=view_work_flow&id=".$result."&flow_id=".$flow_id."&node_id=".$this_node_id."&client=no";
				sys_msg("操作成功", 0, $link);
			}
			else
			{
				$link[0]["text"] = "返回添加页面";
				$link[0]["href"] = 'index.php?c=work_flow&m=add_work_flow&cle_id='.$data['cle_id'].'&flow_id='.$flow_id;
				sys_msg("操作失败", 0, $link);
			}
			return;
		}
		else
		{
			$flow_detail_id = $this->input->post('flow_detail_id');
			$flow_info_id = $this->input->post('flow_info_id');
			$this_node_status = $this->input->post('this_node_status');
			if($this_node_status!=3)
			{
				$result = $this->work_flow_model->update_work_model($flow_detail_id,$flow_info_id,$flow_id,$form_id,$this_node_id,$next_node_id,$data);
			}
			else
			{
				$result = $this->work_flow_model->update_back_work_flow($flow_detail_id,$flow_info_id,$flow_id,$form_id,$this_node_id,$next_node_id,$data);
			}
			$link[0]["text"] = "返回待处理列表";
			$link[0]["href"] = "index.php?c=work_flow&m=deal_list";
		}

		if($result)
		{
			sys_msg("操作成功", 0, $link);
		}
		else
		{
			sys_msg("操作失败", 0, $link);
		}
	}

	/**
	 * 查看工单
     *
     * @return void
	 */
	public function view_work_flow()
	{
		admin_priv();
		global $FLOWNODE_STATUS;
		$user_id = $this->session->userdata('user_id');
		$flow_detail_id = $this->input->get('id');
		if (empty($flow_detail_id))
		{
			sys_msg("该工单不存在");
			return;
		}
		$this->smarty->assign('flow_detail_id',$flow_detail_id);
		$this->load->model('work_flow_model');
		$detail = $this->work_flow_model->get_flow_detail_info($flow_detail_id);
		if(!$detail)
		{
			sys_msg("获取该流程详细记录失败");
			return;
		}
		$this->smarty->assign('cle_id',$detail['cle_id']);
		$flow_id = $detail['flow_id'];
		$flow_info_id = $detail['flow_info_id'];
		//流程记录信息
		$this->load->model("work_flow_model");
		$flow_each_info = $this->work_flow_model->get_work_flow_info($flow_id,$flow_info_id);
		//员工信息
		$this->load->model("user_model");
		$user_result = $this->user_model->get_all_users_without_dept();
		$user_info   = array();
		foreach ($user_result as $user)
		{
			$user_info[$user["user_id"]] = $user["user_name"];
		}
		//流程信息
		$this->load->model('flow_model');
		$flow_info = $this->flow_model->get_flow_info($flow_id);
		$this->smarty->assign('flow_name',$flow_info['flow_name']);
		if($flow_info)
		{
			//节点信息
			$this->load->library("json");
			$node = $this->json->decode($flow_info['node_info'],1);

			$this->load->model('form_model');

			$node_info = array();
			$form_ids = array();
			$node_form_ids = array();
			$flow_each_node = array();
			foreach($node as $nid =>$value)
			{
				if(($flow_each_info['node'.$nid.'_status']==2)||($flow_each_info['node'.$nid.'_status']==4))
				{
					$node_info[$nid] = $value;
					if(!in_array($value['form_id'],$form_ids))
					{
						$form_ids[] = $value['form_id'];
					}
					$node_form_ids[] = array('node_id'=>$nid,'form_id'=>$value['form_id']);//表单及节点id
					$flow_each_node[$nid]['node_start_time'] = empty($flow_each_info['node'.$nid.'_start_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_start_time']);
					$flow_each_node[$nid]['node_end_time'] = empty($flow_each_info['node'.$nid.'_end_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_end_time']);
					$flow_each_node[$nid]['node_over_time'] = (empty($flow_each_info['node'.$nid.'_over_time'])||($flow_each_info['node'.$nid.'_start_time']==$flow_each_info['node'.$nid.'_end_time']))?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_over_time']);
					$flow_each_node[$nid]['node_user_name'] = empty($user_info[$flow_each_info['node'.$nid.'_user_id']])?'':$user_info[$flow_each_info['node'.$nid.'_user_id']];
					$flow_each_node[$nid]['node_status'] = !isset($FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']])?'':$FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']];
					$flow_each_node[$nid]['node_remark'] = $flow_each_info['node'.$nid.'_remark'];
					$flow_each_node[$nid]['node_description'] = $flow_each_info['node'.$nid.'_description'];
				}
			}
			$this->smarty->assign('flow_each_node',$flow_each_node);
			//节点信息
			$this->smarty->assign('node_info',$node_info);
			//表单信息
			$form_info = array();
			$form = $this->form_model->get_form_info($form_ids);
			//表单字段
			$form_fields_info = array();
			$form_fields = $this->form_model->get_more_form_fields($form_ids);
			$original_form_data = array();//之前节点信息
			foreach($node_form_ids as $nfid)
			{
				$form_info[$nfid['node_id']] = empty($form[$nfid['form_id']]) ? 0 : $form[$nfid['form_id']];//表单信息
				$result = $this->work_flow_model->change_html_id_name($form_info[$nfid['node_id']]['form_html'],$nfid['node_id']);
				$new_html   = isset($result['html']) ? $result['html'] : '';
				if(!empty($new_html))
				{
					$form_info[$nfid['node_id']]['form_html'] = $new_html;
				}
				//获取之前节点信息
				$original_form_data[$nfid['node_id']] = $this->work_flow_model->get_form_value_info($nfid['form_id'],$flow_id,$flow_info_id,$nfid['node_id']);
				//表单字段
				$form_fields_info[$nfid['node_id']] = $form_fields[$nfid['form_id']];
			}
			$this->smarty->assign('form_info',$form_info);//表单信息
			$this->smarty->assign('form_fields',$form_fields_info);
			$this->smarty->assign('original_form_data',$original_form_data);
		}

		$client = $this->input->get('client');
		$this->smarty->assign('if_client',empty($client)?'':$client);

		$this->smarty->assign('opter_txt','view');
		$this->smarty->display('flow/work_flow_view.htm');
	}

	/**
     * 追加说明
     *
     * @return void
     */
	public function addinsert_work_flow()
	{
		admin_priv();
		global $FLOWNODE_STATUS;
		$flow_detail_id = $this->input->get('id');
		if (empty($flow_detail_id))
		{
			sys_msg("该工单不存在");
			return;
		}
		$user_id = $this->session->userdata('user_id');
		$this->smarty->assign('flow_detail_id',$flow_detail_id);
		//判断该节点是否已有人占用
		$this->load->model('work_flow_model');
		$detail = $this->work_flow_model->get_flow_detail_info($flow_detail_id);
		if(!$detail)
		{
			sys_msg("获取该流程详细记录失败");
			return;
		}

		$flow_id = $detail['flow_id'];
		$this->smarty->assign('flow_id',$flow_id);

		$this_node_status = $detail['node_status'];
		$this->smarty->assign('this_node_status',$this_node_status);
		$this->smarty->assign('back_reason',$detail['back_reason']);

		$this_node_id = $detail['node_id'];
		$this->smarty->assign('this_node',$this_node_id);

		$flow_info_id = $detail['flow_info_id'];
		$this->smarty->assign('flow_info_id',$flow_info_id);
		//流程记录信息
		$flow_each_info = $this->work_flow_model->get_work_flow_info($flow_id,$flow_info_id);
		//员工信息
		$this->load->model("user_model");
		$user_result = $this->user_model->get_all_users_without_dept();
		$user_info   = array();
		foreach ($user_result as $user)
		{
			$user_info[$user["user_id"]] = $user["user_name"];
		}

		//流程信息
		$this->load->model('flow_model');
		$flow_info = $this->flow_model->get_flow_info($flow_id);
		$this->smarty->assign('flow_name',$flow_info['flow_name']);
		if($flow_info)
		{
			//节点信息
			$this->load->library("json");
			$node = $this->json->decode($flow_info['node_info'],1);

			$this->load->model('form_model');

			$node_info = array();
			$form_ids = array();
			$node_form_ids = array();
			$flow_each_node = array();
			foreach($node as $nid =>$value)
			{
				if(($nid==$this_node_id)||($nid<$this_node_id && ($flow_each_info['node'.$nid.'_status']==2||$flow_each_info['node'.$nid.'_status']==3)))
				{
					$node_info[$nid] = $value;
					if(!in_array($value['form_id'],$form_ids))
					{
						$form_ids[] = $value['form_id'];
					}
					$node_form_ids[] = array('node_id'=>$nid,'form_id'=>$value['form_id']);
					$flow_each_node[$nid]['node_start_time'] = empty($flow_each_info['node'.$nid.'_start_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_start_time']);
					$flow_each_node[$nid]['node_end_time'] = empty($flow_each_info['node'.$nid.'_end_time'])?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_end_time']);
					$flow_each_node[$nid]['node_over_time'] = (empty($flow_each_info['node'.$nid.'_over_time'])||($flow_each_info['node'.$nid.'_start_time']==$flow_each_info['node'.$nid.'_end_time']))?'':date('Y-m-d H:i:s',$flow_each_info['node'.$nid.'_over_time']);
					$flow_each_node[$nid]['node_user_name'] = empty($user_info[$flow_each_info['node'.$nid.'_user_id']])?'':$user_info[$flow_each_info['node'.$nid.'_user_id']];
					$flow_each_node[$nid]['node_status'] = !isset($FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']])?'':$FLOWNODE_STATUS[$flow_each_info['node'.$nid.'_status']];
					$flow_each_node[$nid]['node_remark'] = $flow_each_info['node'.$nid.'_remark'];
					$flow_each_node[$nid]['node_description'] = $flow_each_info['node'.$nid.'_description'];
				}
			}
			$this->smarty->assign('flow_each_node',$flow_each_node);
			//节点信息
			$this->smarty->assign('node_info',$node_info);
			//表单信息
			$form_info = array();
			$form = $this->form_model->get_form_info($form_ids);
			//表单字段
			$form_fields_info = array();
			$form_fields = $this->form_model->get_more_form_fields($form_ids);
			$original_form_data = array();//之前节点信息
			foreach($node_form_ids as $nfid)
			{
				$form_info[$nfid['node_id']] = $form[$nfid['form_id']];//表单信息
				$result = $this->work_flow_model->change_html_id_name($form_info[$nfid['node_id']]['form_html'],$nfid['node_id']);
				$new_html   = isset($result['html']) ? $result['html'] : '';
				if(!empty($new_html))
				{
					$form_info[$nfid['node_id']]['form_html'] = $new_html;
				}
				//获取之前节点信息
				$original_form_data[$nfid['node_id']] = $this->work_flow_model->get_form_value_info($nfid['form_id'],$flow_id,$flow_info_id,$nfid['node_id']);
				//表单字段
				$form_fields_info[$nfid['node_id']] = $form_fields[$nfid['form_id']];
			}
			$this->smarty->assign('form_info',$form_info);//表单信息
			$this->smarty->assign('form_fields',$form_fields_info);
			$this->smarty->assign('original_form_data',$original_form_data);
		}
		$this->smarty->assign('opter_txt','view');
		$this->smarty->display('flow/add_work_flow_view.htm');
	}

	/**
     * 将追加的内容修改到数据库中
     */
	public function insert_work_flow()
	{
		admin_priv();
		$description = $this->input->post('description');
		$description = empty($description) ? '' : $description;
		$flow_info_id = $this->input->post('flow_info_id');
		$flow_id = $this->input->post('flow_id');
		$node_id = $this->input->post('node_id');
		if(empty($flow_info_id))
		{
			$link[0]["text"] = "追加说明";
			$link[0]["href"] = "index.php?c=work_flow&m=complete_list";
			sys_msg('追加说明内容失败，工单信息ID不存在',0,$link);
		}
		else
		{
			$data = array(
			'node'.$node_id.'_description' => $description
			);
			$table = 'est_flow_info'.$flow_id;
			$this->load->model('form_model');
			$res = $this->form_model->insert_form_description($flow_info_id,$table,$data);
			if($res)
			{
				$link[0]["text"] = "追加说明";
				$link[0]["href"] = "index.php?c=work_flow&m=complete_list";
				sys_msg('追加说明内容成功',0,$link);
			}
			else
			{
				$link[0]["text"] = "追加说明";
				$link[0]["href"] = "index.php?c=work_flow&m=addinsert_work_flow";
				sys_msg('追加说明内容失败',0,$link);
			}
		}

	}

	/**
     * 退回页面
     */
	public function back()
	{
		$this->smarty->display('flow/work_flow_back_div.htm');
	}

	/**
	 * 显示待处理工单列表
	 *
	 */
	public function deal_list()
	{
		admin_priv();
		//查询所有的流程
		$this->load->model('flow_model');
		$flows = $this->flow_model->get_all_flow_name();
		$this->smarty->assign('flows', $flows);
		$this->smarty->display('flow/work_flow_list_nodeal.htm');
	}

	/**
	 * 获取待处理列表数据
	 */
	public function get_work_flow_list_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('work_flow_model');
		$responce = $this->work_flow_model->get_work_flow_nodeal_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
     * 显示已处理工单列表
     */
	public function complete_list()
	{
		admin_priv();
		//查询所有的流程
		$this->load->model('flow_model');
		$flows = $this->flow_model->get_all_flow_name();
		$this->smarty->assign('flows', $flows);
		$role_type = $this->session->userdata("role_type");
		$this->smarty->assign('role_type',$role_type);
//		if($role_type == DATA_PERSON)
//		{
			//查询所有坐席
			$this->load->model('user_model');
			$result = $this->user_model->get_users();
			$users = array();
			foreach ($result as $user) {
				$users[$user['user_id']] = $user['user_name'];
			}
			$this->smarty->assign('users', $users);
//		}

		$this->smarty->display('flow/work_flow_list_complete.htm');
	}

	/**
	 * 获取已处理工单列表数据
	 *
	 */
	public function get_work_flow_complete_list_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('work_flow_model');
		$responce = $this->work_flow_model->get_work_flow_complete_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
     * 【业务受理】工单列表页面
     */
	public function work_client_list()
	{
		admin_priv();
		$cle_id = $this->input->get('cle_id');
		$this->smarty->assign('cle_id',$cle_id);
		$this->smarty->display('flow/work_flow_list_client.htm');
	}

	/**
	 * 获取【业务受理】工单列表数据
	 */
	public function get_work_flow_client_query()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('work_flow_model');
		$responce = $this->work_flow_model->get_work_flow_client($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}


	/**
     * 显示工单列表
     */
	public function list_all()
	{
		admin_priv();
		$flow_id = $this->input->get('flow_id');
		$this->smarty->assign('flow_id',$flow_id);

		//查询所有的流程
		$this->load->model('flow_model');
		$flows = $this->flow_model->get_all_flow_name();
		$this->smarty->assign('flows', $flows);

		//若未传递flow_id，则从所有流程中获取第一个
		if(empty($flow_id)&&!empty($flows))
		{
			$array_keys = array_keys($flows);
			if(is_array($array_keys))
			{
				$flow_id = $array_keys[0];
			}
		}

		//查询所有的流程节点
		$items = array();
		if(!empty($flow_id))
		{
			$nodes = $this->flow_model->get_all_nodes_by_flow_id($flow_id);
			$this->load->model('form_model');
			foreach($nodes as $node_id =>$node)
			{
				$form_id = empty($node['form_id']) ? 0 : $node['form_id'];
				$form_fields = $this->form_model->get_form_fields_info($form_id);
				foreach($form_fields as $field)
				{
					$items[] = array('field'=>$field['fields'].'_'.$node_id,'label'=>$field['field_name']);
				}
				$items[] = array('field'=>'user_name'.$form_id.'_'.$node_id,'label'=>'操作人');
				$items[] = array('field'=>'node'.$node_id.'_description','label'=>'追加说明');
			}
		}
		$this->smarty->assign('items', $items);

		$this->smarty->assign('start_time', date("Y-m-d 00:00:00"));
		$this->smarty->assign('end_time', date("Y-m-d 23:59:59"));

		$this->smarty->display('flow/work_flow_list.htm');
	}

	/**
     * 获取工单列表数据
     *
     */
	public function get_all_work_flow_list()
	{
		admin_priv();
		$condition = $this->input->post();
		$this->load->model('work_flow_model');
		$responce = $this->work_flow_model->get_all_work_flow_list($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
     * 导出所有工单列表
     */
	public function export_all_work_flow_list()
	{
		admin_priv();
		$flow_id = $this->input->get('flow_id');
		$start_time = $this->input->get('start_time');
		$end_time = $this->input->get('end_time');
		$this->load->model('work_flow_model');
		$this->work_flow_model->export_over_work_flow($flow_id,$start_time,$end_time);
	}
}