<?php
class Order_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除缓存
	 *
	 * @param int $order_id
     * @return bool
	 * @author yan
	 */
	private function _clear_order_cache($order_id = 0)
	{
		$this->cache->delete('get_order_info'.$order_id);
		return true;
	}

	/**
	 * 处理检索条件
	 *
	 * @param array $condition  检索条件
	 * @return array  SQL数组
	 * @author yan
	 */
	private function get_order_condition($condition = array())
	{
		$wheres = array();
		$condition_product = false;

		/*订单ID*/
		if (!empty($condition["order_ids"]))
		{
			$wheres[] = "order_id IN(".$condition['order_ids'].")";
		}
		/*订单编号*/
		if(!empty($condition['order_num']))
		{
			$wheres[] = "order_num = '".trim($condition['order_num'])."'";
		}
		/*下单时间*/
		if(!empty($condition['order_accept_time_start']))
		{
			$wheres[] = "order_accept_time >= '".$condition['order_accept_time_start']." 00:00:00'";
		}
		if(!empty($condition['order_accept_time_end']))
		{
			$wheres[] = "order_accept_time <= '".$condition['order_accept_time_end']." 23:59:59'";
		}
		/*配送方式*/
		if(!empty($condition['order_delivery_mode']))
		{
			$wheres[] = "order_delivery_mode = '".$condition['order_delivery_mode']."'";
		}
		/*配送单号*/
		if(!empty($condition['order_delivery_number']))
		{
			$wheres[] = "order_delivery_number = '".$condition['order_delivery_number']."'";
		}
		/* 订单状态*/
		if(!empty($condition['order_state']))
		{
			$wheres[] = "order_state = '".$condition['order_state']."'";
		}
		/* 客户姓名*/
		if(!empty($condition['cle_name']))
		{
			$wheres[] = "cle_name LIKE '%".trim($condition['cle_name'])."%'";
		}
		/* 客户电话*/
		if(!empty($condition['cle_phone']))
		{
			$wheres[] = "cle_phone LIKE '%".trim($condition['cle_phone'])."%'";
		}
		/* 客户地址*/
		if(!empty($condition['cle_address']))
		{
			$wheres[] = "cle_address LIKE '%".trim($condition['cle_address'])."%'";
		}
		/* 所属人*/
		if (isset($condition["user_id"]) && is_numeric($condition["user_id"]))
		{
			$wheres[] = "user_id = ".$condition["user_id"];
		}
		/* 所属部门*/
		if (!empty($condition["dept_id"]))
		{
			$this->load->model("department_model");
			$dept_array = $this->department_model->get_department_children_ids($condition["dept_id"]);
			if(!empty($dept_array))
			{
				$dept_ids = implode(",",$dept_array);
				$wheres[] = "dept_id IN (".$dept_ids.")";
			}
		}
		/* 订单产品*/
		if(!empty($condition['product_id']))
		{
			$condition_product = true;
			$wheres[] = "est_order_product.product_id = ".$condition['product_id'];
		}
		/* 自定义字段*/
		if(!empty($condition['field_confirm_values']))
		{
			$this->load->model('field_confirm_model');
			$wheres_field = $this->field_confirm_model->get_field_confirm_condition($condition['field_confirm_values'],FIELD_TYPE_ORDER);
			$wheres = array_merge($wheres,$wheres_field);
		}
		/*客户ID*/
		if(!empty($condition['cle_id']))
		{
			$wheres[] = "cle_id = ".$condition['cle_id'];
		}
		//数据权限
		$wheres[] = data_permission();
		$responce = new stdClass();
		$responce -> wheres =  $wheres;
		$responce -> condition_product = $condition_product;
		return $responce;
	}

	/**
	 * 通过订单ID得到对应的产品信息
	 *
	 * @param array $order_ids 符合条件的订单ID，多个ID以逗号分隔
	 * @return array $product_info  产品信息
	 * 
	 * @author yan
	 */
	public function get_product_by_orderID($order_ids = array())
	{
		$product_info = array();
		if (!empty($order_ids))
		{
			$this->db_read->where_in("order_id",$order_ids);
			$this->db_read->select("op_id,order_id,product_id,product_name,product_num,product_number,product_discount,product_class,product_price,product_thum_pic");
			$query = $this->db_read->get("est_order_product");
			$query = $query->result_array();
			foreach ($query AS $value)
			{
				if(empty($value["product_thum_pic"]) || !file_exists($value["product_thum_pic"]))
				{
					$value["product_thum_pic"] = NO_PIC;
				}
				$product_info[$value["order_id"]][] = $value;
			}
		}
		return $product_info;
	}

	/**
	 * 得到订单管理列表显示数据
	 *
	 * @param array $condition 传递搜索条件的数组
	 * @param array $select 检索的字段 数组
	 * @param int $page    分页
	 * @param int $limit   每页显示数量
	 * @param int $sort    排序字段
	 * @param int $order   排序
	 * @return object responce total rows
	 * @author yan
	 */
	public function get_order_list($condition = array(), $select = array(), $page = 0, $limit = 10, $sort = NULL, $order = NULL)
	{
		$join_order_product = false; //关联产品表
		$where_responce = $this->get_order_condition($condition);
		$wheres = $where_responce->wheres;
		$condition_product = $where_responce->condition_product;
		if($condition_product)
		$join_order_product = true;

		//检索条件
		$this->db_read->start_cache();
		if (!empty($wheres))
		{
			$where = implode(" AND ",$wheres);
			$this->db_read->where($where);
		}
		$this->db_read->from('est_order');
		if($join_order_product)
		{
			$this->db_read->join('est_order_product', 'est_order.order_id = est_order_product.order_id ','LEFT');
		}
		$this->db_read->stop_cache();

		//处理检索字段
		if(empty($select))
		{
			$this->load->model('datagrid_confirm_model');
			$select = $this->datagrid_confirm_model->get_available_select_fields(LIST_COMFIRM_ORDER);
		}
		//级联
		$jl_field_names = array();
		$this->load->model('field_confirm_model');
		$field_names = $this->field_confirm_model->get_jl_field_name(FIELD_TYPE_ORDER);
		$if_get_jl_info = false;
		foreach($field_names as $field)
		{
            $name = isset($field[0]) ? $field[0] : '';
            if(in_array($name,$select))
            {
                $jl_field_names[] = $field;
                $if_get_jl_info = true;
                $select[] = $name.'_1';
                $select[] = $name.'_2';
                $select[] = $name.'_3';
            }
		}

		$this->db_read->select('count(*) as total,sum(order_price) as total_sum',FALSE);
		$total_query = $this->db_read->get();
		$responce = new stdClass();
		$total = $total_query->row()->total;
		$responce -> total = $total;

		$responce -> rows = array();
		if($total == 0)
		{
			return $responce;
		}

		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if (!empty($sort))
		{
			$this->load->model("datagrid_confirm_model");
			$sort = $this->datagrid_confirm_model->replace_sort_field($sort);
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->select($select);
		$this->db_read->limit($limit,$start);
		$_data = $this->db_read->get();
		$_data = $_data->result_array();

		$show_user_name = $show_dept_name = false;
		if(in_array('user_id',$select))
		{
			$show_user_name = true;
		}
		if(in_array('dept_id',$select))
		{
			$show_dept_name = true;
		}

		if (!empty($_data))
		{
			if ($show_user_name)
			{
				//坐席信息
				$this->load->model("user_model");
				$user_result = $this->user_model->get_all_users();
				$user_info   = array();
				foreach ($user_result AS $key => $user)
				{
					$user_info[$user["user_id"]] = $user["user_name"];
				}
			}

			if ($show_dept_name)
			{
				//部门信息
				$this->load->model("department_model");
				$dept_result = $this->department_model->get_all_department();
				$dept_info   = array();
				foreach ($dept_result AS $value)
				{
					$dept_info[$value["dept_id"]] = $value["dept_name"];
				}
			}
			//级联
			if($if_get_jl_info == true)
			{
				$jl_info = array();
				$jl_info = $this->field_confirm_model->get_all_jl_info(FIELD_TYPE_ORDER);
			}
		}

		$this->db_read->flush_cache();
		$orser_ids = array();
		foreach($_data AS $i=>$task)
		{
			//下单人
			if ($show_user_name)
			{
				$task["user_name"]   = empty($user_info[$task["user_id"]]) ? "" : $user_info[$task["user_id"]];
			}
			if ($show_dept_name)
			{
				$task["dept_name"]   = empty($dept_info[$task["dept_id"]]) ? "" : $dept_info[$task["dept_id"]];
			}

			//列表可修改列备份
			$task["order_state_original"] = empty($task["order_state"]) ? "" : $task["order_state"];
			$task["order_delivery_number_original"] = empty($task["order_delivery_number"]) ? "" : $task["order_delivery_number"];
			//级联
			if(!empty($jl_field_names))
			{
				foreach($jl_field_names as $jl_field)
				{
					if($jl_field[1]==DATA_TYPE_JL)
					{
						$task[$jl_field[0]] = '';
						$jl_name = array();
						if(!empty($jl_info[$task[$jl_field[0].'_1']]))
						{
							$task[$jl_field[0]] = $jl_info[$task[$jl_field[0].'_1']];
							if(!empty($jl_info[$task[$jl_field[0].'_2']]))
							{
								$task[$jl_field[0]] .= '-'.$jl_info[$task[$jl_field[0].'_2']];
								if(!empty($jl_info[$task[$jl_field[0].'_3']]))
								{
									$task[$jl_field[0]] .= '-'.$jl_info[$task[$jl_field[0].'_3']];
								}
							}
						}
					}
					else
					{
						$task[$jl_field[0]] = '';
						if(!empty($task[$jl_field[0].'_2']))
						{
							foreach(explode(',',$task[$jl_field[0].'_2']) as $box)
							{
								if(!empty($jl_info[$box]))
								{
									$task[$jl_field[0]] .= $jl_info[$box].'，';
								}
							}
						}
					}
				}
			}
			$responce -> rows[$i] = $task;
			$orser_ids[] = $task['order_id'];
		}
		//允许订单产品数量 1一个 2多个
		$this->load->model('system_config_model');
		$system_config = $this->system_config_model->get_system_config();
		if($system_config['order_product_amount']==1)
		{
			if (!empty($orser_ids))
			{
				$product_info = $this->get_product_by_orderID($orser_ids);
				foreach ($responce -> rows AS $key => $value)
				{
					if (!empty($product_info[$value["order_id"]]))
					{
						$responce -> rows[$key]['product_name'] = $product_info[$value["order_id"]][0]["product_name"];
						$responce -> rows[$key]['product_number'] = $product_info[$value["order_id"]][0]["product_number"];
						$responce -> rows[$key]['product_id'] = $product_info[$value["order_id"]][0]["product_id"];
						$responce -> rows[$key]['product_price'] = $product_info[$value["order_id"]][0]["product_price"];
						$responce -> rows[$key]['product_thum_pic'] = $product_info[$value["order_id"]][0]["product_thum_pic"];
					}
					else
					{
						$responce -> rows[$key]['product_thum_pic'] = '';
						$responce -> rows[$key]['product_number'] = '';
					}
				}
			}
		}
		$responce->footer = array(array('order_num'=>'总金额','order_price'=>$total_query->row()->total_sum."元"));

		return $responce;
	}

	/**
	 * 获取订单的信息
	 *
	 * @param int $order_id  订单ID
	 * @return array  订单信息
	 * @author yan
	 */
	public function get_order_info($order_id =0)
	{
		if (empty($order_id))
		{
			return false;
		}
		$order_info = array();
		if(!$this->cache->get('get_order_info'.$order_id))
		{
			$this->db_read->where("order_id",$order_id);
			$query = $this->db_read->get('est_order');
			$order_info = $query->row_array();
			$this->cache->save('get_order_info'.$order_id, $order_info, 600);
		}
		else
		{
			$order_info = $this->cache->get('get_order_info'.$order_id);
		}
		return $order_info;
	}

	/**
	 * 添加新订单
	 *
	 * @param array $order_info  新订单
	 * @return int  $order_id  订单ID
	 * @author yan
	 */
	public function insert_order($order_info = array())
	{
		//客户
		$cle_id = empty($order_info['cle_id']) ? '' : $order_info['cle_id'];
		$cle_name = empty($order_info['cle_name']) ? '' : $order_info['cle_name'];
		$cle_address = empty($order_info['cle_address']) ? '' : $order_info['cle_address'];
		$cle_phone = empty($order_info['cle_phone']) ? '' : $order_info['cle_phone'];
		//订单
		$order_num = empty($order_info['order_num']) ? $this->new_order_number() : $order_info['order_num'];
		$order_price = empty($order_info['order_price']) ? '' : $order_info['order_price'];
		$order_remark = empty($order_info['order_remark']) ? '' : $order_info['order_remark'];
		$order_delivery_number = empty($order_info['order_delivery_number']) ? '' : $order_info['order_delivery_number'];
		$order_delivery_mode = empty($order_info['order_delivery_mode']) ? '' : $order_info['order_delivery_mode'];
		$con_name = empty($order_info['con_name']) ? '' : $order_info['con_name'];
		$con_mobile = empty($order_info['con_mobile']) ? '' : $order_info['con_mobile'];
		$order_state = empty($order_info['order_state']) ? "" : $order_info['order_state'];
		if(isset($order_price))
		{
			if(!is_numeric($order_price))
			{
				if(!is_float($order_price))
				{
					$order_price = '';
				}
			}
		}
		$data_order = array(
		'order_num'=>$order_num,
		'cle_id'=>$cle_id,
		'cle_name'=>$cle_name,
		'cle_phone'=>$cle_phone,
		'cle_address'=>$cle_address,
		'order_price'=>$order_price,
		'order_remark'=>$order_remark,
		"user_id" => $this->session->userdata("user_id"),
		"dept_id" => $this->session->userdata("dept_id"),
		"order_accept_time" => date("Y-m-d H:i:s"),
		"order_state" => $order_state,
		"order_delivery_mode" => $order_delivery_mode,
		"order_delivery_number" => $order_delivery_number,
		"con_name" => $con_name,
		"con_mobile" => $con_mobile
		);
		//得到订单自定义字段
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_ORDER);
		foreach ($confirm_field AS $item)
		{

			if($item["data_type"]==DATA_TYPE_JL || $item["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if(!empty($order_info[$item["fields"]."_1"]))
				$data_order[$item["fields"].'_1'] = $order_info[$item["fields"]."_1"];
				if(!empty($order_info[$item["fields"]."_2"]))
				$data_order[$item["fields"].'_2'] = $order_info[$item["fields"]."_2"];
				if(!empty($order_info[$item["fields"]."_3"]))
				$data_order[$item["fields"].'_3'] = $order_info[$item["fields"]."_3"];
			}
			else
			{
				$data_order[$item["fields"]] = empty($order_info[$item["fields"]]) ? "" : $order_info[$item["fields"]];
			}
		}
		$result = $this->db_write->insert("est_order",$data_order);
		if (!$result)
		{
			return FALSE;
		}
		else
		{
			$this->_clear_order_cache();//清除缓存
			$order_id = $this->db_write->insert_id();

			//订单产品
			if(!empty($order_info['goods_info']))
			{
				$this->set_order_product($order_id,$order_info['goods_info'],'','insert');
			}

			//记录日志
			$this->load->model("log_model");
			$this->log_model->write_order_log('添加订单',$order_id);

			return $order_id;
		}
	}

	/**
	 * 修改订单产品
	 *
	 * @param int    $order_id  订单ID
	 * @param array  $goods_info  产品信息
	 * @param int    $order_total_price  订单总价
	 * @param string $type  类型  update：编辑订单    insert：添加新订单
	 * @return bool
	 * 
	 * @author yan
	 */
	public function set_order_product($order_id=0,$goods_info=array(),$order_total_price=0,$type='update')
	{
		if(empty($order_id) || empty($goods_info))
		{
			return false;
		}
		if($type=='update')
		{
			$res_project = $this->db_write->delete("est_order_product",array('order_id'=>$order_id));
			if(!$res_project)
			{
				return false;
			}
		}
		//添加订单产品信息
		$data_product = array();
		foreach($goods_info AS $product)
		{
			$data_product[] = array(
			'order_id'=>$order_id,
			'product_id'=>$product['product_id'],
			'product_num'=>$product['product_num'],
			'product_name'=>$product['product_name'],
			'product_number'=> empty($product['product_number']) ? 1 : $product['product_number'],
			'product_discount'=> empty($product['product_discount']) ? "100.00" : $product['product_discount'],
			'product_thum_pic'=> empty($product['product_thum_pic']) ? "" : $product['product_thum_pic'],
			'product_class'=> empty($product['product_class']) ? "" : $product['product_class'],
			'product_price'=> empty($product['product_price']) ? 0 : $product['product_price']
			);
		}
		//保存产品信息
		$result = $this->db_write->insert_batch("est_order_product",$data_product);
		if(!$result)
		{
			return false;
		}
		if($type=='update')
		{
			$this->db_write->update("est_order",array('order_price'=>$order_total_price),array('order_id'=>$order_id));
			//记录日志
			$this->load->model("log_model");
			$this->log_model->write_order_log('修改订单产品，订单总额变为：'.$order_total_price.'元',$order_id);
		}
		$this->_clear_order_cache($order_id);//清除缓存
		return TRUE;
	}

	/**
	 * 编辑订单
	 *
	 * @param int   $order_id  订单ID
	 * @param array $order_info   订单内容
	 * @return bool
	 * @author yan
	 */
	public function update_order($order_id=0,$order_info = array())
	{
		if(empty($order_id))
		{
			return '缺少参数';
		}
		$user_id = $this->session->userdata('user_id');
		$role_type = $this->session->userdata('role_type');
		if($role_type == DATA_PERSON && $order_info['user_id'] != $user_id)
		{
			return false;
		}

		$where_order = array('order_id'=>$order_id);
		//得到订单使用字段
		$this->load->model("field_confirm_model");
		$order_filed = $this->field_confirm_model->get_available_fields(FIELD_TYPE_ORDER);

		/*判断是否可编辑的字段*/
		$update_array = array();

		$log_content = '修改订单';
		foreach ($order_filed as $field_info)
		{
			if(isset($field_info["data_type"]) && $field_info["data_type"]==DATA_TYPE_JL || $field_info["data_type"]==DATA_TYPE_CHECKBOXJL)//级联自定义字段
			{
				if(isset($order_info[$field_info['fields']."_1"]))
				{
					$update_array[$field_info['fields'].'_1'] = $order_info[$field_info['fields']."_1"];
				}
				if(isset($order_info[$field_info['fields']."_2"]))
				{
					$update_array[$field_info['fields'].'_2'] = $order_info[$field_info['fields']."_2"];
				}
				if(isset($order_info[$field_info['fields']."_3"]))
				{
					$update_array[$field_info['fields'].'_3'] = $order_info[$field_info['fields']."_3"];
				}
			}
			elseif(isset($order_info[$field_info['fields']]))
			{
				if($field_info['fields'] == 'cle_name')
				{
					$log_contents['cle_name'] = $order_info['cle_name'];
				}
				if($field_info['fields'] != 'order_num' && $field_info['fields'] != 'user_id')
				{
					$update_array[$field_info['fields']] = $order_info[$field_info['fields']];
					$log_content .= "|".$field_info["name"].":".$order_info[$field_info["fields"]];
				}
			}
		}
		if(!empty($order_info['cle_id']))
		{
			$update_array['cle_id'] =  $order_info['cle_id']; //修改客户时加上客户id
			$log_contents['cle_id'] =  $order_info['cle_id'];
		}
		if(!empty($update_array))
		{
			if(isset($update_array['order_price']))
			{
				if(!is_numeric($update_array['order_price']))
				{
					if(!is_float($update_array['order_price']))
					{
						unset($update_array['order_price']);
					}
				}
			}
			$result = $this->db_write->update("est_order",$update_array,$where_order);
			if (!$result)
			{
				return FALSE;
			}

			//记录日志
			$this->load->model("log_model");
			$this->log_model->write_order_log($log_content,$order_id);
		}
		$this->_clear_order_cache($order_id);//清除缓存

		return TRUE;
	}


	/**
	 * 删除订单
	 *
	 * @param int $order_id  订单ID
	 * @return bool
	 * @author yan
	 */
	public function delete_order($order_id=0)
	{
		if(empty($order_id))
		{
			return '缺少参数';
		}
		$where = 'order_id IN('.$order_id.')' ;
		//删除符合条件的订单信息
		$result = $this->db_write->delete('est_order',$where);
		if($result)
		{
			$this->db_write->delete('est_order_product',$where);

			$this->_clear_order_cache($order_id);//清除缓存
			//记录日志
			$this->load->model("log_model");
			$this->log_model->write_order_log('删除订单',$order_id);
			return true;
		}
		else
		{
			return false;
		}

	}

	/**
	 * 新订单号
	 * 编号的标准是 年月日0000 比如：201210080001
	 * @author yan
	 */
	public function new_order_number()
	{
		//判断lock文件夹是否存在
        $this->config->load('myconfig');
        $cfg = $this->config->config;
		$lock_dir = isset($cfg['lock_path']) ? $cfg['lock_path'] : './public/lock/';
		if (!file_exists($lock_dir))
		{
			@mkdir($lock_dir, 0777);
			@chmod($lock_dir, 0777);
		}

		$vcc_id = $this->session->userdata('vcc_id');
		$type_name = 'order'.$vcc_id;
		$count_file = $lock_dir.$type_name;//计数文件
		$lock_file     = $lock_dir.$type_name.'.lck';//锁文件
		//判断临时文件有没有生成没有的话创建临时文件
		if ( ! file_exists($count_file))
		{
			file_put_contents($count_file,0);
			@chmod($count_file, 0777);
		}

        if (!file_exists($lock_file)) {
            touch($lock_file);
            @chmod($lock_file, 0777);
        } elseif (!is_writable($lock_file)) {
            @chmod($lock_file, 0777);
        }
		$fp = fopen($lock_file, "w+");
		// 执行文件锁定
		if (flock($fp, LOCK_EX))
		{
			//取得文件修改时间如果不是今天那么从新计数
			if(filemtime($count_file) < strtotime(date('Y-m-d')))
			{
				$count_num = 1;
			}
			else
			{
				//读取文件
				$str = file_get_contents($count_file);
				$count_num = intval($str);
				if(is_int($count_num)&&$count_num>0)
				{
					$count_num += 1;
				}
				else
				{
					$count_num = 1;
				}
			}
			file_put_contents($count_file,$count_num);
			flock($fp, LOCK_UN); // 释放锁定
		}
		fclose($fp);
		$serial_number = date('Ymd').sprintf('%03d',$count_num);
		return $serial_number;
	}

	/**
	 * 批量修改订单状态
	 *
	 * @param array  $order_ids    订单ID
	 * @param string  $order_state  新状态
	 * @return bool
	 * @author yan
	 */
	public function batch_update_order_state($order_ids=array(),$order_state='')
	{
		if(empty($order_ids) || empty($order_state))
		{
			return '缺少参数';
		}

		$this->db_write->where_in('order_id',$order_ids);
		$result = $this->db_write->update("est_order",array('order_state'=>$order_state));
		if($result)
		{
			//记录日志
			$this->load->model("log_model");
			$log_content = '修改订单 订单状态改为'.$order_state;
			$this->log_model->write_order_log($log_content,$order_ids);

			$this->_clear_order_cache();//清除缓存
			return true;
		}
		return false;
	}

	/**
	 * 客户所属人改变时同步更新订单所属人和所属部门信息
	 *
	 * @param int | array $cle_id 可以是一个值 或者 是数组
	 * @param int $user_id 新的user_id
	 * @param int $dept_id 新的部门id 0表示不改变
	 * @return bool
	 */
	public function update_order_user_id_when_client_resource($cle_id,$user_id=0,$dept_id=0)
	{
		if(empty($user_id) && empty($dept_id))
		{
			return false;
		}
		if(!is_array($cle_id))
		{
			$cle_id = array($cle_id);
		}
		if(empty($cle_id))
		{
			return false;
		}
		$update = array();
		if(!empty($user_id))
		{
			$this->load->model('user_model');
			$user_info = $this->user_model->get_user_info($user_id);
			$dept_id = $user_info['dept_id'];
			$update['user_id'] = $user_id;
			$update['dept_id'] = $dept_id;
		}
		else
		{
			$update['user_id'] = 0;
			if(!empty($dept_id))
			{
				$update['dept_id'] = $dept_id;
			}
		}
		$this->db_write->where_in('cle_id',$cle_id);
		$this->db_write->update('est_order',$update);
		//记录日志
		$this->load->model("log_model");
		$this->db_read->where_in('cle_id',$cle_id);
		$this->db_read->select('order_id');
		$order_query = $this->db_read->get('est_order');
		$order_info = $order_query->result_array();
		if (!empty($order_info))
		{
			$order_ids = array();
			foreach ($order_info as $order)
			{
				$order_ids[] = $order['order_id'];
			}
			$this->log_model->write_order_log('因客户所属人改变，同时改变订单所属人',$order_ids);
		}
		return true;
	}

	/**
	 * 坐席部门改变，没有客户的订单相应部门也改变
	 * @param int $user_id 坐席id
	 * @param int $new_dept_id 部门id
	 * @return bool
	 */
	public function change_order_dept_id($user_id=0,$new_dept_id=0)
	{
		if(empty($user_id)||empty($new_dept_id))
		{
			return false;
		}

		$order_query = $this->db_read->query("SELECT order_id FROM est_order WHERE user_id=".$user_id." AND dept_id!=".$new_dept_id);
		$order_info = $order_query->result_array();
		if (!empty($order_info))
		{
			$result = $this->db_read->query("UPDATE est_order SET dept_id=".$new_dept_id." WHERE user_id=".$user_id." AND dept_id!=".$new_dept_id);
			if($result)
			{
				//记录日志
				$this->load->model("log_model");
				$order_ids = array();
				foreach ($order_info as $order)
				{
					$order_ids[] = $order['order_id'];
				}
				$this->log_model->write_order_log('坐席部门改变，同时订单部门改变',$order_ids);
			}
			return $result;
		}
		else
		return false;
	}

	/**
	 * 根据客户id，修改订单客户信息
	 *
	 * @param int $cle_id
	 * @param array $update_data
	 * @return bool
	 * @author zgx
	 */
	public function update_order_client_by_cleid($cle_id=0,$update_data=array())
	{
		if(empty($cle_id)||empty($update_cle_data))
		{
			return false;
		}
		$log_str = '';
		$update_cle_data = array();
		if(isset($update_data['cle_name']))
		{
			$update_cle_data['cle_name'] = $update_data['cle_name'];
			$log_str .= '|客户姓名改变相应订单客户姓名改变为：'.$update_data['cle_name'];
		}
		if(isset($update_data['cle_phone']))
		{
			$update_cle_data['cle_phone'] = $update_data['cle_phone'];
			$log_str .= '|客户电话改变相应订单客户电话改变为：'.$update_data['cle_phone'];
		}
		if(!empty($update_cle_data))
		{
			$this->db_write->where("cle_id",$cle_id);
			$result = $this->db_write->update("est_order",$update_cle_data);
			if($result)
			{
				$order_query = $this->db_read->query("SELECT order_id FROM est_order WHERE cle_id=".$cle_id);
				$order_info = $order_query->result_array();
				//记录日志
				$this->load->model("log_model");
				$order_ids = array();
				foreach ($order_info as $order)
				{
					$order_ids[] = $order['order_id'];
				}
				$this->log_model->write_order_log($log_str,$order_ids);
			}
			return $result;
		}
		return false;
	}

	/**
	 * 订单统计
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param int $sort
	 * @param int $order
	 * @return object
	 * @author zgx
	 */
	public function get_order_statistics($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = array();
		if(!empty($condition['start_date']))
		{
			$wheres[] = "order_accept_time >='".$condition['start_date']."'";
		}
		if(!empty($condition['end_date']))
		{
			$wheres[] = "order_accept_time <='".$condition['end_date']."'";
		}
		if(!empty($condition['user_id']))
		{
			$wheres[] = "user_id = ".$condition['user_id'];
		}
		/* 订单状态*/
		if(!empty($condition['order_state']))
		{
			$wheres[] = "order_state = '".$condition['order_state']."'";
		}
		if (!empty($condition["dept_id"]))
		{
			$this->load->model("department_model");
			$dept_array = $this->department_model->get_department_children_ids($condition["dept_id"]);
			if(!empty($dept_array))
			{
				$dept_ids = implode(",",$dept_array);
				$wheres[] = "dept_id IN (".$dept_ids.")";
			}
		}
		else
		{
			//数据权限
			$wheres[] = data_permission();
		}

		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$responce = new stdClass();
		//获取总金额
		$this->db_read->select('sum(order_price) as total_price',FALSE);
		$price_query = $this->db_read->get('est_order');
		$responce->footer = array(array('order_total'=>'总金额','price'=>empty($price_query->row()->total_price)?0:$price_query->row()->total_price."元"));

		//获取符合条件的下单坐席数
		$this->db_read->select('count(*) as total',FALSE);
		$this->db_read->group_by('user_id');
		$total_query = $this->db_read->get('est_order');
		$total = count($total_query->result_array());
		$responce -> total = $total;

		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);

		//订单状态
		$this->load->model("dictionary_model");
		$order_state = $this->dictionary_model->get_dictionary_detail(DICTIONARY_ORDER_STATE);
		$states = array();
		$sql_state = '';
		foreach($order_state as $value)
		{
			$state_id = 'order_state'.$value['id'];
			$sql_state .= ",SUM(CASE WHEN order_state='".$value['name']."' THEN 1 ELSE 0 END) AS ".$state_id;
		}
		$this->db_read->select('user_id,sum(order_price) as price,dept_id,sum(product_number) AS product_number,count(*) as order_total'.$sql_state);
		$this->db_read->join('est_order_product', 'est_order.order_id = est_order_product.order_id ','LEFT');
		$this->db_read->group_by('user_id');
		$query = $this->db_read->get('est_order');
		$this->db_read->flush_cache();//清除缓存细信息
		$responce -> rows = array();
		if($query)
		{
			//部门信息
			$this->load->model("department_model");
			$dept_result = $this->department_model->get_all_department();
			$dept_info   = array();
			foreach ($dept_result AS $value)
			{
				$dept_info[$value["dept_id"]] = $value["dept_name"];
			}

			//坐席信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			$user_info   = array();
			foreach ($user_result AS $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"]."[".$user['user_num']."]";
			}

			foreach($query->result_array() AS $i=>$order)
			{
				$order['user_name'] = empty($user_info[$order['user_id']])?'':$user_info[$order['user_id']];
				$order['dept_name'] = empty($dept_info[$order['dept_id']])?'':$dept_info[$order['dept_id']];
				$responce -> rows[$i] = $order;
			}
		}
		return $responce;
	}
}