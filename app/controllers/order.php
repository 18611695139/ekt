<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller {
	/**
	 * 获取订单列表
	 */
	public function order_list()
	{
		admin_priv('ddgllb');

		//权限：订单(添加)
		$power_insert_order       = check_authz("power_insert_order");
		$this->smarty->assign("power_insert_order",$power_insert_order?$power_insert_order:0);
		//权限：订单(修改)
		$power_update_order       = check_authz("power_update_order");
		$this->smarty->assign("power_update_order",$power_update_order?$power_update_order:0);
		//权限：订单(删除)
		$power_delete_order       = check_authz("power_delete_order");
		$this->smarty->assign("power_delete_order",$power_delete_order?$power_delete_order:0);
		//权限：订单(导出)
		$power_output_order       = check_authz("power_output_order");
		$this->smarty->assign("power_output_order",$power_output_order?$power_output_order:0);
		//权限：订单(查看)
		$power_view_order       = check_authz("power_view_order");
		$this->smarty->assign("power_view_order",$power_view_order?$power_view_order:0);
		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//权限：订单(修改)
		$power_update_order       = check_authz("power_update_order");
		$this->smarty->assign("power_update_order",$power_update_order?$power_update_order:0);

		//允许订单产品数量 1一个 2多个
		$this->load->model('system_config_model');
		$system_config = $this->system_config_model->get_system_config();
		$this->smarty->assign('system_order_product_amount',$system_config['order_product_amount']);
		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$this->smarty->assign("order_state",$order_state);
		//得到列表显示字段
		$this->load->model("datagrid_confirm_model");
		$_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_ORDER);
		$this->smarty->assign("_display_field",$_display_field);

		$this->smarty->display('order_list.htm');
	}

	private function _clean_condition($condition)
	{
		$user_id = $this->session->userdata('user_id');
		/*快捷搜索 - 1全部数据 2我的数据*/
		if(!empty($condition['all_type']) && $condition['all_type'] == 2)
		{
			$condition['user_id']  = $user_id;
			unset($condition['all_type']);
		}
		return $condition;
	}

	/**
	 * 获取订单列表信息
	 */
	public function get_order_query()
	{
		admin_priv('ddgllb',false);

		//检索条件
		$condition = $this->input->post();
		$condition = $this->_clean_condition($condition);

		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_COMFIRM_ORDER);
		//数据
		$this->load->model("order_model");
		$responce = $this->order_model->get_order_list($condition,$select);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 基本搜索
	 */
	public function order_base_search()
	{
		admin_priv();

		//角色类型
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);

		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$this->smarty->assign("order_state",$order_state);
		$this->smarty->display('order_search_base.htm');
	}

	/**
	 * 高级搜索
	 */
	public function order_advance_search()
	{
		admin_priv();

		//角色类型
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		$dept_session_id = $this->session->userdata('dept_id');
		$this->smarty->assign('dept_session_id',$dept_session_id);
		$user_session_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_session_id',$user_session_id);
		//高级搜索 逻辑条件
		$logical_condition = array('='=>'等于','!='=>'不等于','like'=>'包含','>'=>'大于','<'=>'小于','>='=>'大于等于','<='=>'小于等于');
		$this->smarty->assign('condition',$logical_condition);
		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$this->smarty->assign("order_state_info",$order_state);
		//得到订单字段信息（基本可编辑+自定义）
		$this->load->model("field_confirm_model");
		$order_fields_info = $this->field_confirm_model->get_available_fields(FIELD_TYPE_ORDER);
		$order_base = array(); //基本字段名
		$field_confirm = array();//自定义
		foreach($order_fields_info as $field_info)
		{
			if($field_info['if_custom']==1)//自定义
			{
				$field_confirm[$field_info['id']] = $field_info['name'];
			}
			else//基本
			{
				$order_base[$field_info['fields']] = $field_info['fields'];
			}
		}
		$this->smarty->assign("order_base",$order_base);
		$this->smarty->assign("field_confirm",$field_confirm);
		$this->smarty->assign("field_confirm_selected",0);
		//配送方式 数字字典
		if(in_array('order_delivery_mode',$order_base))
		{
			$this->load->model("dictionary_model");
			$delivery_info = $this->dictionary_model->get_dictionary_detail(DICTIONARY_DELIVERY_MODE);
			$this->smarty->assign("delivery_info",$delivery_info);
		}
		$this->smarty->display('order_search_advan.htm');
	}

	/**
	 * 获取列表产品详情页
	 */
	public function get_list_product()
	{
		admin_priv();

		$order_id = $this->input->get('order_id');

		$this->load->model('order_model');
		$order_product_info = $this->order_model->get_product_by_orderID($order_id);
		if(isset($order_product_info[$order_id]))
		{
			$order_num = $this->input->get('order_num');
			$this->smarty->assign('order_num',$order_num);
			$this->smarty->assign('order_product_info',$order_product_info[$order_id]);
			$this->smarty->display('order_list_product.htm');
		}
		else
		{
			echo "该订单没有任何产品信息！";
		}
		return;
	}

	/**
	 * 订单详情 - 添加产品
	 *
	 */
	public function select_products()
	{
		$this->smarty->display("order_product_select_list.htm");
	}

	/**
	 * 添加新订单
	 */
	public function add_order()
	{
		admin_priv('power_insert_order');

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：自定义字段
		$power_fieldsconfirm_order       = check_authz("xtglzdpz");
		$this->smarty->assign("power_fieldsconfirm_order",$power_fieldsconfirm_order?$power_fieldsconfirm_order:0);
		//权限：数字字典
		$power_dictionary_order = check_authz("xtglszzd");
		$this->smarty->assign("power_dictionary_order",$power_dictionary_order?$power_dictionary_order:0);
		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		//允许订单产品数量 1一个 2多个
		$this->smarty->assign('system_order_product_amount',$system_config['order_product_amount']);
		//获取订单号
		$this->load->model('order_model');
		$order_num = $this->order_model->new_order_number();
		$this->smarty->assign("order_num",$order_num);
		//客户信息
		$cle_id = $this->input->get('cle_id');
		if(!empty($cle_id))
		{
			//得到客户信息
			$this->load->model("client_model");
			$client_info = $this->client_model->get_client_info($cle_id);
			//得到一联系人信息
			$this->load->model("contact_model");
			$contact_info = $this->contact_model->get_contact_by_cle_id($cle_id,1);
			if(!empty($contact_info))
			{
				$client_info = array_merge($client_info,$contact_info[0]);
			}

			$this->smarty->assign("client_info",$client_info);
		}
		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$this->smarty->assign("order_state",$order_state);
		//自定义字段
		$this->load->model("field_confirm_model");
		$order_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_ORDER);//获取可编辑的字段信息
		$order_base = array(); //订单可用的基本字段
		$order_confirm = array();//订单可用的自定义字段
		$parent_id = array();//下拉field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach ($order_fields AS $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$order_base[$field_info['fields']] = $field_info['fields'];
			}
			else
			{
				if($field_info['data_type']==DATA_TYPE_SELECT)//下拉框field_id
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL)//级联field_id
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
					$jl_field_type[$field_info["id"]] = $this->json->decode($field_info["jl_field_type"],1);
				}
				else if($field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$checkbox_id[]=$field_info['id'];
				}
				$order_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("order_base",$order_base);
		$this->smarty->assign("order_confirm",$order_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));
		//配送方式 数字字典
		if(in_array('order_delivery_mode',$order_base))
		{
			$this->load->model("dictionary_model");
			$delivery_info = $this->dictionary_model->get_dictionary_detail(DICTIONARY_DELIVERY_MODE);
			$this->smarty->assign("delivery_info",$delivery_info);
		}
		//自定义 下拉选项
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

        $this->smarty->assign('now_time',date('Y-m-d H:i:s'));
        $this->smarty->assign('now_date',date('Y-m-d'));
		$this->smarty->display('order_add.htm');
	}

	/**
	 * 保存订单
	 */
	public function insert_order()
	{
		admin_priv('power_insert_order',false);

		$inarray = $this->input->post();
		//产品信息
		$this->load->library("json");
		$inarray['goods_info'] = $this->json->decode($inarray['goods_info'],1);
		if(count($inarray['goods_info'])>1)
		{
			//允许订单产品数量 1一个 2多个
			$this->load->model('system_config_model');
			$system_config = $this->system_config_model->get_system_config();
			if($system_config['order_product_amount']==1)
			{
				make_json_error('系统只允许添加一个产品');
				return;
			}
		}
		$this->load->model('order_model');
		$result = $this->order_model->insert_order($inarray);
		make_simple_response($result);
	}

	/**
	 * 订单受理 
	 */
	public function order_accept()
	{
		admin_priv();

		//订单基本信息
		$order_id = $this->input->get('order_id');
		if(empty($order_id) || $order_id =='undefined')
		{
			sys_msg("该订单不存在");
			return  false;
		}
		$user_session_id = $this->session->userdata('user_id');
		$this->smarty->assign('user_session_id',$user_session_id);
		$role_type = $this->session->userdata('role_type');
		$this->smarty->assign('role_type',$role_type);
		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);
		//权限：订单(修改)
		$power_update_order       = check_authz("power_update_order");
		$this->smarty->assign("power_update_order",$power_update_order?$power_update_order:0);
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);
		//权限：发短信
		$power_sendsms       = check_authz("sendsms");
		$this->smarty->assign("power_sendsms",$power_sendsms?$power_sendsms:0);
		//客服服务(查看)
		$power_service_view = check_authz("kffwserview");
		$this->smarty->assign("power_service_view",$power_service_view?$power_service_view:0);
		//获取系统配置参数
		$this->load->model("system_config_model");
		$system_config = $this->system_config_model->get_system_config();
		//是否使用联系人模块
		$power_use_contact = empty($system_config["use_contact"]) ? 0 : $system_config["use_contact"];
		$this->smarty->assign("power_use_contact",$power_use_contact);
		//订单信息
		$this->load->model('order_model');
		$order_info = $this->order_model->get_order_info($order_id);
		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$this->smarty->assign("order_state",$order_state);
		//自动外呼
		$system_autocall = $this->input->get('system_autocall');
		$system_autocall_number = $this->input->get('system_autocall_number');
		$system_autocall = empty($system_autocall) ? 0 : $system_autocall;
		$system_autocall_number = empty($system_autocall_number) ? $order_info['cle_phone'] : $system_autocall_number ;
		$this->smarty->assign("system_autocall",$system_autocall);
		$this->smarty->assign("system_autocall_number",$system_autocall_number);
		//是否显示 : 上一条/下一条
		$system_pagination = $this->input->get('system_pagination');
		$system_pagination  = empty($system_pagination) ? 0 : $system_pagination;
		$this->smarty->assign("system_pagination",$system_pagination);
		//订单管理页面跳转订单受理的数据，取得cookie中的订单ID
		$row_limit   = 0 ;
		$row_index    = 0 ;//数据位置
		if ($system_pagination)
		{
			$last_next_orderID = $this->input->cookie("last_next_orderID");
			$last_next_orderID = explode(",",$last_next_orderID);
			$row_limit       = count($last_next_orderID);

			//查找当前ID所处位置
			$row_index = array_search($order_id,$last_next_orderID);
			if ($row_index >= 0)
			{
				$row_index++;
			}
		}
		//数据位置
		$this->smarty->assign("row_limit",$row_limit);
		$this->smarty->assign("row_index",$row_index);
		//自定义字段
		$this->load->model("field_confirm_model");
		$order_fields = $this->field_confirm_model->get_available_fields(FIELD_TYPE_ORDER);//获取可编辑的字段信息
		$order_base = array(); //订单可用的基本字段
		$order_confirm = array();//订单可用的自定义字段
		$parent_id = array();//下拉field_id
		$jl_id = array();//级联自定义field_id
		$jl_field = array();//级联自定义field_name
		$checkbox_id=array();//多选级联id
		$jl_field_type = array();//最后一级级联类型信息
		$this->load->library("json");
		foreach ($order_fields AS $field_info)
		{
			if($field_info['if_custom']==0)
			{
				$order_base[$field_info['fields']] = $field_info['fields'];
			}
			else
			{
				if($field_info['data_type']==DATA_TYPE_SELECT)//下拉框field_id
				{
					$parent_id[] = $field_info["id"];
				}
				else if($field_info['data_type']==DATA_TYPE_JL)//级联field_id
				{
					$jl_id[] = $field_info["id"];
					$jl_field[$field_info["id"]] = $field_info["fields"];
					$field_info["jl_field_type"] = $this->json->decode($field_info["jl_field_type"],1);
					$jl_field_type[$field_info["id"]] = $field_info["jl_field_type"];
				}
				else if($field_info['data_type']==DATA_TYPE_CHECKBOXJL)
				{
					$checkbox_id[]=$field_info['id'];
					if(!empty($order_info[$field_info["fields"].'_1']))
					{
						$check_1 = explode(',',$order_info[$field_info["fields"].'_1']);
						foreach($check_1 as $value)
						{
							$order_info[$field_info["id"].'_1'][$value] = $value;
						}
					}
					if(!empty($order_info[$field_info["fields"].'_2']))
					{
						$check_2 = explode(',',$order_info[$field_info["fields"].'_2']);
						foreach($check_2 as $value)
						{
							$order_info[$field_info["id"].'_2'][$value] = $value;
						}
					}
				}
				$order_confirm[] = $field_info;
			}
		}
		$this->smarty->assign("order_base",$order_base);
		$this->smarty->assign("order_confirm",$order_confirm);
		$this->smarty->assign("jl_field_type",$this->json->encode($jl_field_type));
		//配送方式 数字字典
		if(in_array('order_delivery_mode',$order_base))
		{
			$this->load->model("dictionary_model");
			$delivery_info = $this->dictionary_model->get_dictionary_detail(DICTIONARY_DELIVERY_MODE);
			$this->smarty->assign("delivery_info",$delivery_info);
		}
		//自定义 下拉选项
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
				if(!empty($order_info[$v.'_1']))
				{
					$jl_p_id[] = $order_info[$v.'_1'];
				}
				if(!empty($order_info[$v.'_2']))
				{
					$jl_p_id[] = $order_info[$v.'_2'];
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

		$this->smarty->assign('order_info',$order_info);
		$this->smarty->display('order_accept.htm');
	}

	/**
	 * 修改订单
	 */
	public function update_order()
	{
		admin_priv('power_update_order');

		$inarray = $this->input->post();
		$order_id  = empty($inarray["order_id"]) ? 0 : $inarray["order_id"];
		if (!$order_id)
		{
			make_json_error("缺少参数！");
		}
		$this->load->model('order_model');
		$result = $this->order_model->update_order($order_id,$inarray);

		make_simple_response($result);
	}

	/**
	 * 保存订单产品
	 */
	public function set_order_product()
	{
		admin_priv();

		$order_id = $this->input->post('order_id');
		$order_total_price = $this->input->post('order_total_price');
		$goods_info = $this->input->post('goods_info');
		//产品信息
		$this->load->library("json");
		$goods_info = $this->json->decode($goods_info,1);
		if(count($goods_info)>1)
		{
			//允许订单产品数量 1一个 2多个
			$this->load->model('system_config_model');
			$system_config = $this->system_config_model->get_system_config();
			if($system_config['order_product_amount']==1)
			{
				make_json_error('系统只允许添加一个产品');
				return;
			}
		}
		$this->load->model('order_model');
		$result = $this->order_model->set_order_product($order_id,$goods_info,$order_total_price);
		make_simple_response($result);
	}

	/**
	 * 订单受理 - 订单产品
	 */
	public function get_order_product_info()
	{
		admin_priv();

		$order_id = $this->input->get('order_id');
		$this->load->model('order_model');
		$order_product_info = $this->order_model->get_product_by_orderID($order_id);
		if(!empty($order_product_info))
		{
			$this->smarty->assign('order_product_info',$order_product_info[$order_id]);
		}
		$this->smarty->assign('order_id',$order_id);

		//允许订单产品数量 1一个 2多个
		$this->load->model('system_config_model');
		$system_config = $this->system_config_model->get_system_config();
		$this->smarty->assign('system_order_product_amount',$system_config['order_product_amount']);

		//权限：订单(修改)
		$power_update_order       = check_authz("power_update_order");
		$this->smarty->assign("power_update_order",$power_update_order?$power_update_order:0);
		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);


		$this->smarty->display('order_product_info.htm');
	}

	/**
	 * 删除订单
	 */
	public function delete_order()
	{
		admin_priv('power_delete_order');

		$order_id = $this->input->post('order_ids');
		$this->load->model('order_model');
		//权限：订单(删除)
		$power_delete_order       = check_authz("power_delete_order");
		if($power_delete_order)
		{
			$result = $this->order_model->delete_order($order_id);
			make_simple_response($result);
		}
		else
		{
			make_json_error("你没删除订单的权限，不能删除！");
		}
	}

	/**
	 * 导出订单
	 */
	public function output_order()
	{
		admin_priv('power_output_order');

		set_time_limit(0);
		@ini_set('memory_limit', '1024M');

		//导出信息
		$data_info = array();

		//检索条件
		$condition = $this->input->get();
		$user_id   = $this->session->userdata('user_id');
		/*快捷搜索 - 1全部数据 2我的数据*/
		if(!empty($condition['all_type']) && $condition['all_type'] == 2)
		{
			$condition['user_id']  = $user_id;
		}
		//检索字段
		$this->load->model('datagrid_confirm_model');
		$select = $this->datagrid_confirm_model->get_datagrid_select_fields(LIST_COMFIRM_ORDER);

        //数据
        $total     = empty($condition["total"]) ? 0 : $condition["total"];
        $sortName  = empty($condition["sortName"]) ? "order_id" : $condition["sortName"];
        $sortOrder = empty($condition["sortOrder"]) ? "DESC" : $condition["sortOrder"];

        //得到 信息
        $this->load->model('order_model');
        $responce = $this->order_model->get_order_list($condition,$select,1,$total,$sortName,$sortOrder);
        $order_info = $responce->rows;

		//得到字段
		$this->load->model('datagrid_confirm_model');
		$field_info = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_ORDER );
		$field = array();
        $fields = array();
		foreach($field_info AS $value)
		{
            $fields[$value['fields']] = $value['name'];
			$field[] = $value['fields'];
		}

		for($i=0,$k=0;$i<count($order_info);$i++,$k++)
		{
			for($j=0;$j<count($field);$j++)
			{
				$data_info[$i][$field[$j]] = $order_info[$k][$field[$j]];
			}
		}

        $export_type = $this->input->get('export_type');
        switch ($export_type) {
            case 'csv':
                array_unshift($data_info,$fields);
                $this->load->library("csv");
                $this->csv->creatcsv('订单数据' . date("YmdHis"),$data_info);
                break;
            case 'excel':
                $this->load->model('excel_export_model');
                $this->excel_export_model->export($data_info, $fields, '订单数据' . date("YmdHis"));
                break;
            default:
                sys_msg("操作失败");
                break;
        }
	}

	/**
	 * 订单查看 - 订单日志
	 *
	 */
	public function get_order_log()
	{
		admin_priv();

		$order_id = $this->input->get("order_id");
		if (!empty($order_id))
		{
			$this->load->model('order_model');
			$order_info = $this->order_model->get_order_info($order_id);
			$this->load->model('user_model');
			$user_info = $this->user_model->get_user_info($order_info['user_id']);
			$order_info['user_name'] = empty($user_info['user_name']) ? '' : $user_info['user_name'];
			$order_info['dept_name'] = empty($user_info['dept_name']) ? '' : $user_info['dept_name'];
			$this->smarty->assign('order_info',$order_info);
			//得到相关日志
			$this->load->model("log_model");
			$result_log = $this->log_model->get_order_log($order_id);
			$this->smarty->assign("order_log",$result_log);
		}
		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$this->smarty->display("order_log_message.htm");
	}

	/**
	 * 客户订单
	 */
	public function client_order()
	{
		admin_priv();

		$cle_id = $this->input->get('cle_id');
		$this->smarty->assign('cle_id',$cle_id);

		//得到列表显示字段
		$this->load->model("datagrid_confirm_model");
		$_display_field = $this->datagrid_confirm_model->get_datagrid_show_fields(LIST_COMFIRM_ORDER);
		$this->smarty->assign("_display_field",$_display_field);

		//允许订单产品数量 1一个 2多个
		$this->load->model('system_config_model');
		$system_config = $this->system_config_model->get_system_config();
		$this->smarty->assign('system_order_product_amount',$system_config['order_product_amount']);

		//权限：产品(查看)
		$power_view_product       = check_authz("power_view_product");
		$this->smarty->assign("power_view_product",$power_view_product?$power_view_product:0);
		//权限：订单(查看)
		$power_view_order       = check_authz("power_view_order");
		$this->smarty->assign("power_view_order",$power_view_order?$power_view_order:0);

		//权限：订单(添加)
		$power_insert_order = check_authz("power_insert_order");
		$this->smarty->assign("power_insert_order",$power_insert_order?$power_insert_order:0);

		$this->smarty->display('client_order.htm');
	}

	/**
	 * 订单受理  1上一页  2下一页
	 *
	 */
	public function trun_page_order()
	{
		admin_priv();

		$pre_or_next = $this->input->post("pre_or_next");
		if (empty($pre_or_next))
		{
			return false;
		}

		$order_list_param = $this->input->cookie("order_list_param");
		if(empty($order_list_param))
		{
			make_json_error("数据不存在！");
		}

		//客户列表参数
		$order_list_param = explode(",",$order_list_param);
		$page = $order_list_param[0];
		$limit = $order_list_param[1];
		$sort = $order_list_param[2];
		$order = $order_list_param[3];
		if ($pre_or_next == 'pre')//上一页
		{
			$page = $page -1;
		}
		else if($pre_or_next == 'next')//下一页
		{
			$page = $page +1;
		}
		//检索条件
		$where = $this->input->cookie('accept_order_condition');
		$where = unescape($where);
		$this->load->library('json');
		$condition = $this->json->decode($where,1);
		$condition = $this->_clean_condition($condition);

		//获取数据
		$this->load->model("order_model");
		$responce = $this->order_model->get_order_list($condition,array('order_id'),$page, $limit, $sort, $order);

		$order_ids = array();
		foreach ($responce->rows AS $value)
		{
			if (!empty($value["order_id"]) )
			{
				$order_ids[] = $value["order_id"];
			}
		}

		if (!empty($order_ids) )
		{
			$params = array("last_next_orderID"=>implode(",",$order_ids),"order_list_param"=>$page.",".$limit.",".$sort.",".$order);
			make_json_result('','',$params);
		}
		else
		{
			make_json_error("数据不存在！");
		}
	}

	/**
	 * 批量修改 - 状态
	 */
	public function batch_update_order_state()
	{
		admin_priv();

		$order_ids = $this->input->post('order_ids');
		$order_ids = explode(',',$order_ids);
		$order_state = $this->input->post('order_state');
		if (!$order_ids)
		{
			make_json_error("缺少参数！");
		}
		$this->load->model('order_model');
		$result = $this->order_model->batch_update_order_state($order_ids,$order_state);

		make_simple_response($result);
	}

	/**
	 * 列表直接修改订单数据
	 */
	public function update_order_by_list()
	{
		admin_priv();

		$change_data = $this->input->post('change_data');
		//修改信息
		$this->load->library("json");
		$change_data = $this->json->decode($change_data,1);
		if(!empty($change_data))
		{
			$this->load->model('order_model');
			if(!empty($change_data['order_id']))
			$result = $this->order_model->update_order($change_data['order_id'],$change_data);
			else
			$result = false;
			make_simple_response($result);
		}
		else
		{
			make_json_error('当前列表没修改数据');
		}
	}

	/**
	 * 展现订单详情
	 */
	public function view_order()
	{
		admin_priv();

		$this->load->model("field_confirm_model");
		//自定义字段 选项信息
		$parent_id = array();
		//得到 自定义字段
		$order_confirm = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_ORDER);
		foreach ($order_confirm AS $item)
		{
			if($item['data_type']==DATA_TYPE_SELECT)
			{
				$parent_id[] = $item["id"];
			}
		}
		$this->smarty->assign("order_confirm",$order_confirm);

		//自定义字段 选项
		$field_detail = array();
		if (!empty($parent_id))
		{
			$field_detail = $this->field_confirm_model->get_field_details_name($parent_id);
		}
		$this->smarty->assign("field_detail",$field_detail);

		$order_id = $this->input->get('order_id');
		$this->load->model('order_model');
		$order_info = $this->order_model->get_order_info($order_id);
		$this->smarty->assign('order_info',$order_info);

		$this->smarty->display('order_view.htm');
	}

	/**
     * 订单统计页面
     */
	public function get_order_statistics_page()
	{
		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$states = array();
		foreach($order_state as $value)
		{
			$value['state_id'] = 'order_state'.$value['id'];
			$states[] = $value;
		}
		$this->smarty->assign("order_state",$states);

		$this->smarty->assign('dept_session_id',$this->session->userdata('dept_id'));

		$this->smarty->assign('today_start',date('Y-m-d').' 00:00:00');
		$this->smarty->assign('today_end',date('Y-m-d').' 23:59:59');
		$this->smarty->display('order_statistics.htm');
	}

	/**
	 * 获取统计数据
	 *
	 */
	public function get_order_statistics_info()
	{
		//检索条件
		$condition = $this->input->post();

		//数据
		$this->load->model("order_model");
		$responce = $this->order_model->get_order_statistics($condition);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}
}