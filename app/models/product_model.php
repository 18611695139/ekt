<?php
class Product_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	//=================  产品分类  =======================================================
	/**
	 * 清除缓存 - 产品分类
	 *
	 * @param int $product_class_id 分类id
	 * @return bool
	 */
	private function _clear_class_cache($product_class_id=0)
	{
		$this->cache->delete('product_tree_info');
		$this->cache->delete('product_all_class_info');
		if($product_class_id!=0)
		{
			$this->cache->delete('product_class_info'.$product_class_id);
		}
		return true;
	}

	/**
	 * 递归函数 返回树的所有节点的数组
	 *
	 * @param array $p_c_tree
	 * @return array
	 */
	private function _get_children($p_c_tree)
	{
		$children = array();
		if(is_array($p_c_tree))
		{
			foreach ($p_c_tree as $p_c_info)
			{
				$children[] = $p_c_info['id'];
				if(isset($p_c_info['children']))
				{
					$_children = $this->_get_children($p_c_info['children']);
					$children = array_merge($children,$_children);
				}
			}
		}
		return $children;
	}

	/**
	 * 得到所有的子分类的ID(包含自身)
	 *
	 * @param int $p_c_id
	 * @return array
	 */
	public function get_product_class_children_ids($p_c_id=0)
	{
		if(empty($p_c_id))
		{
			return false;
		}
		$p_c_tree	= $this->get_somenode_product_class_tree($p_c_id);
		$children	= $this->_get_children($p_c_tree);
		$children[]	= $p_c_id;
		sort($children);
		return array_unique($children);
	}

	/**
	 * 获取整个产品分类树对象
	 * @return array
	 */
	public function get_whole_product_class_tree()
	{
		if(!$this->cache->get('product_tree_info'))
		{
			$arr_tree = array();
			$this->db_read->order_by('p_c_id');
			$nodes_query = $this->db_read->get("est_product_class");
			foreach ($nodes_query->result_array() as $node)
			{
				$tmp_node = new stdClass();
				$tmp_node -> id = $node['p_c_id'];
				$tmp_node -> pid = $node['p_c_pid'];
				$tmp_node -> text = $node['p_c_name'];
				$tmp_node -> attributes = array('deep'=>$node['p_c_deep'],'pname'=>$node['p_c_pname'],'pid'=>$node['p_c_pid']);

				$arr_tree[$node['p_c_id']] = $tmp_node;
				$arr_tree[$node['p_c_pid']] -> children[] = $tmp_node;
				$arr_tree[$node['p_c_pid']] -> state = "closed";
			}
			$this->cache->save('product_tree_info',$arr_tree,600);
		}
		else
		{
			$arr_tree = $this->cache->get('product_tree_info');
		}

		return $arr_tree;
	}

	/**
	 * 获取某个分类树的对象数组（包含分类自身及其子分类）
	 *
	 * @param int $p_c_id 分类id
	 * @return array
	 * @author zgx
	 */
	public function get_somenode_product_class_tree($p_c_id = 1)
	{
		$arr_tree = $this->get_whole_product_class_tree();
		if(!empty($arr_tree) && !empty($arr_tree[$p_c_id]))
		{
			$arr_tree[$p_c_id] -> state = "open";
			$result_array = object_to_array($arr_tree[$p_c_id]);
			return array($result_array);
		}
		else
		{
			return array();
		}
	}

	/**
	 * 获取下一级产品的信息（不包括本分类及下下级分类,判断是否有下下一级）
	 * 
	 * @param int $p_c_id 分类id
	 * @return array
	 * @author zgx
	 */
	public function get_next_level_product_class($p_c_id=0)
	{
		$p_c_tree = $this->get_somenode_product_class_tree($p_c_id);
		$p_c_next_level = array();
		if(empty($p_c_tree[0]['children']))
		{
			return $p_c_next_level;
		}
		else
		{
			$p_c_next_level = $p_c_tree[0]['children'];
			foreach ($p_c_next_level as $key=>$p_c)
			{
				if(!empty($p_c['state']))
				{
					$p_c_next_level[$key]['children'] = '';
				}
			}
			return $p_c_next_level;
		}
	}

	/**
	 * 获取一产品分类信息
	 * @param int $p_c_id
	 * @return array
	 */
	public function get_product_class_info($p_c_id=0)
	{
		if( ! $this->cache->get('product_class_info'.$p_c_id))
		{
			if($p_c_id === 0)
			{
				$p_c_info = array('p_c_id'=>0,'p_c_name'=>'','p_c_deep'=>0);
			}
			else
			{
				$where = array('p_c_id'=>$p_c_id);
				$query = $this->db_read->get_where('est_product_class',$where,1);
				$p_c_info = $query->row_array();
			}
			$this->cache->save('product_class_info'.$p_c_id,$p_c_info,600);
		}
		else
		{
			$p_c_info = $this->cache->get('product_class_info'.$p_c_id);
		}

		return $p_c_info;
	}

	/**
	 * 获取分类所有信息
	 *
	 * @return array 产品分类信息
	 */
	public function get_all_product_class()
	{
		if( ! $this->cache->get('product_all_class_info'))
		{
			$this->db_read->select("p_c_id,	p_c_pid,p_c_name,p_c_pname,	p_c_deep");
			$query = $this->db_read->get("est_product_class");
			$product_all_class_info = $query->result_array();
			$this->cache->save('product_all_class_info',$product_all_class_info,600);
		}
		else
		{
			$product_all_class_info = $this->cache->get('product_all_class_info');
		}
		return $product_all_class_info;
	}

	/**
	 * 插入一条产品分类
	 * @param int $p_c_pid
	 * @param string $p_c_name
	 * @return bool | int $p_c_id
	 */
	public function insert_product_class($p_c_pid=0,$p_c_name='')
	{
		$p_c_p_info = $this->get_product_class_info($p_c_pid);
		$p_c_deep = $p_c_p_info['p_c_deep'] + 1;

		if($p_c_p_info['p_c_pid']==0)
		{
			$p_c_pname = $p_c_name;
		}
		else
		{
			$p_c_pname = $p_c_p_info['p_c_pname'].'-'.$p_c_name;
		}
		$data = array(
		'p_c_pid'=>$p_c_pid,
		'p_c_name'=>$p_c_name,
		'p_c_deep'=>$p_c_deep,
		'p_c_pname'=>$p_c_pname
		);
		$result = $this->db_write->insert('est_product_class',$data);
		if($result)
		{
			$p_c_id = $this->db_write->insert_id();
			$this->_clear_class_cache();
			return $p_c_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新产品分类信息
	 * @param int $p_c_id
	 * @param string $p_c_name
	 * @return bool
	 */
	public function update_product_class($p_c_id=0,$p_c_name='')
	{
		$p_c_p_info = $this->get_product_class_info($p_c_id);
		$p_c_deep = $p_c_p_info['p_c_deep'];
		$p_name = explode('-',$p_c_p_info['p_c_pname']);
		if($p_c_p_info['p_c_pid'] != 0)
		{
			$p_c_old_pname = $p_name[$p_c_deep-2];
			$p_name[$p_c_deep-2] = $p_c_name;
			$p_c_pname = implode('-',$p_name);

			$query = $this->db_read->query("SELECT p_c_id,p_c_name,p_c_pname FROM est_product_class WHERE p_c_pname LIKE '%$p_c_old_pname-%'");
			if($query)
			{
				foreach($query->result_array() AS $children)
				{
					$p_name_children = explode('-',$children['p_c_pname']);
					$p_name_children[$p_c_deep-2] = $p_c_name;
					$p_c_pname_children = implode('-',$p_name_children);
					$this->db_write->update('est_product_class',array('p_c_pname'=>$p_c_pname_children),array('p_c_id'=>$children['p_c_id']));
				}
			}
		}
		else
		{
			$p_c_pname = $p_c_name;
		}

		$data = array('p_c_name'=>$p_c_name,'p_c_pname'=>$p_c_pname);
		$where = array('p_c_id'=>$p_c_id);
		$result = $this->db_write->update('est_product_class',$data,$where);

		if($result)
		{
			$this->_clear_class_cache($p_c_id);
			$p_c_children = $this->get_product_class_children_ids($p_c_id);
			$this->_clear_product_cache(0,'product_class_id IN('.implode(',',$p_c_children).')');
			return 1;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除分类信息(包括子分类 , 相关分类下的产品)
	 * @param int $p_c_id
	 * @return bool
	 */
	public function delete_product_class($p_c_id=0)
	{
		if($p_c_id === 0)
		{
			return '缺少参数';
		}

		$p_c_children = $this->get_product_class_children_ids($p_c_id);
		$where = 'p_c_id in ('.implode(',',$p_c_children).')';
		$result = $this->db_write->delete('est_product_class',$where);

		if($result)
		{
			//分类下的产品的处理
			$this->db_write->query('DELETE FROM est_product WHERE product_class_id IN('.implode(',',$p_c_children).')');
			$this->_clear_class_cache($p_c_id);
			$this->_clear_product_cache(0,'product_class_id IN('.implode(',',$p_c_children).')');
			return true;
		}
		else
		{
			return false;
		}
	}

	//=============== 产品  ============================================================================

	/**
	 * 清除缓存 -产品
	 *
	 * @param int $product_id 产品id
     * @param string
	 * @return bool
	 */
	private function _clear_product_cache($product_id=0,$where = null)
	{
		if($product_id!=0)
		{
			$this->cache->delete('product_info'.$product_id);
		}
		if ($where != null)
		{
			$this->db_read->where($where);
			$this->db_read->select('product_id');
			$query = $this->db_read->get('est_product');
			$product_ids = $query->result_array();
			foreach ($product_ids as $tmp)
			{
				$tmp_id = $tmp['product_id'];
				$this->cache->delete('product_info'.$tmp_id);
			}
		}
		return true;
	}

	/**
	 * 获取产品搜索条件
	 * @param array $condition 检索内容
	 * @return array
	 */
	private function get_product_condition( $condition = array() )
	{
		$wheres = array();
		//产品名称
		if(!empty($condition['product_name']))
		{
			$wheres[] = "product_name LIKE '%".$condition['product_name']."%'";
		}
		//产品状态
		if(!empty($condition['product_state']))
		{
			$wheres[] = "product_state = '".$condition['product_state']."'";
		}
		//产品分类
		if(!empty($condition['product_class_id']))
		{
			$product_class_ids = $this->get_product_class_children_ids($condition["product_class_id"]);
			if(!empty($product_class_ids))
			{
				$class_ids = implode(",",$product_class_ids);
				if(count($product_class_ids)>1)
				{
					$wheres[] = "product_class_id IN (".$class_ids.")";
				}
				else
				{
					$wheres[] = "product_class_id = ".$class_ids;
				}
			}
		}
		//产品编号
		if(!empty($condition['product_num']))
		{
			$wheres[] = "product_num = '".trim($condition['product_num'])."'";
		}
		//产品价格
		if(!empty($condition['price1']))
		{
			$wheres[] = "product_price >= ".trim($condition['price1']);
		}
		if(!empty($condition['price2']))
		{
			$wheres[] = "product_price <= ".trim($condition['price2']);
		}

		/*高级搜索 - 自定义字段*/
		if(!empty($condition['field_confirm_values']))
		{
			$this->load->model('field_confirm_model');
			$wheres_field = $this->field_confirm_model->get_field_confirm_condition($condition['field_confirm_values'],FIELD_TYPE_PRODUCT);
			$wheres = array_merge($wheres,$wheres_field);
		}

		return $wheres;
	}

	/**
	 * 得到产品管理列表显示数据
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
	public function get_product_list($condition = array(),$select=array(),$page=0, $limit=10, $sort=NULL, $order=NULL)
	{
		$vcc_code = $this->session->userdata('vcc_code');
		//处理检索条件
		$wheres = $this->get_product_condition($condition);
		if (!empty($wheres))
		{
			$where  = implode(" AND ",$wheres);
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}
		//处理检索字段
		if(empty($select))
		{
			$this->load->model('datagrid_confirm_model');
			$select = $this->datagrid_confirm_model->get_available_select_fields(LIST_COMFIRM_PRODUCT);
		}
		//级联
		$jl_field_names = array();

		$this->load->model('field_confirm_model');
		$field_names = $this->field_confirm_model->get_jl_field_name(FIELD_TYPE_PRODUCT);
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

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_product');
		$total = $total_query->row()->total;
		$responce = new stdClass();
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
		$p_data = $this->db_read->get('est_product',$limit,$start);
		$this->db_read->flush_cache();
		if($p_data)
		{
			//获取所有产品分类信息
			$product_all_class_info = $this->get_all_product_class();
			$product_class_pname = array();
			foreach($product_all_class_info as $value)
			{
				$product_class_pname[$value['p_c_id']] = $value['p_c_pname'];
			}
			//级联
			if(count($p_data->result_array())>0)
			{
				if($if_get_jl_info == true)
				{
					$jl_info = array();
					$jl_info = $this->field_confirm_model->get_all_jl_info(FIELD_TYPE_PRODUCT);
				}
			}

			foreach($p_data->result_array() AS $i=>$product)
			{
				if(!empty($product['product_class_id']) && !empty($product_class_pname[$product['product_class_id']]))
				{
					$product['product_class_name'] = $product_class_pname[$product['product_class_id']];
				}
				else
				{
					$product['product_class_name'] = '';
				}
				if(!empty($product['product_thum_pic']) && file_exists(PIC_FILE.$vcc_code.'/'.$product['product_thum_pic']))
				{
					$product['product_thum_pic'] = PIC_FILE.$vcc_code.'/'.$product['product_thum_pic'];
					$product['product_pic'] = PIC_FILE.$vcc_code.'/'.$product['product_pic'];
				}
				else
				{
					$product['product_thum_pic'] = '';
				}
				$product['no_picture'] = NO_PIC;
				//级联
				if(!empty($jl_field_names))
				{
					foreach($jl_field_names as $jl_field)
					{
                        if($jl_field[1]==DATA_TYPE_JL)
                        {
                            $product[$jl_field[0]] = '';
                            $jl_name = array();
                            if(!empty($jl_info[$product[$jl_field[0].'_1']]))
                            {
                                $product[$jl_field[0]] = $jl_info[$product[$jl_field[0].'_1']];
                                if(!empty($jl_info[$product[$jl_field[0].'_2']]))
                                {
                                    $product[$jl_field[0]] .= '-'.$jl_info[$product[$jl_field[0].'_2']];
                                    if(!empty($jl_info[$product[$jl_field[0].'_3']]))
                                    {
                                        $product[$jl_field[0]] .= '-'.$jl_info[$product[$jl_field[0].'_3']];
                                    }
                                }
                            }
                        }
                        else
                        {
                            $product[$jl_field[0]] = '';
                            if(!empty($product[$jl_field[0].'_2']))
                            {
                                foreach(explode(',',$product[$jl_field[0].'_2']) as $box)
                                {
                                    if(!empty($jl_info[$box]))
                                    {
                                        $product[$jl_field[0]] .= $jl_info[$box].'，';
                                    }
                                }
                            }
                        }
					}
				}
				$responce -> rows[$i] = $product;
			}
		}
		return $responce;
	}

	/**
	 * 获取产品信息
	 * @param int $product_id
	 * @return array  产品信息
	 */
	public function get_product_info($product_id=0)
	{
		$vcc_code = $this->session->userdata('vcc_code');
		if($product_id==0)
		{
			return FALSE;
		}

		if( ! $this->cache->get('product_info'.$product_id))
		{
			$this->db_read->where("product_id",$product_id);
			$this->db_read->limit(1);
			$product_query = $this->db_read->get('est_product');
			$product_info = $product_query->row_array();

			$product_info['no_picture'] = NO_PIC;
			if(!empty($product_info['product_thum_pic']))
			{
				if(file_exists(PIC_FILE.$vcc_code.'/'.$product_info['product_thum_pic']))
				{
					$only_product_name = explode('.',$product_info['product_pic']);
					$product_info['product_pic_only_name'] = $only_product_name[0];
					$product_info['product_thum_pic'] = PIC_FILE.$vcc_code.'/'.$product_info['product_thum_pic'];
					$product_info['product_pic'] = PIC_FILE.$vcc_code.'/'.$product_info['product_pic'];
				}
				else
				{
					$product_info['product_thum_pic'] = '';
					$product_info['product_pic'] = '';
				}
			}
			for($i=1;$i<=8;$i++)
			{
				if(!empty($product_info['product_other_thum_pic'.$i]))
				{
					if(file_exists(PIC_FILE.$vcc_code.'/'.$product_info['product_other_thum_pic'.$i]))
					{
						$only_product_name = explode('.',$product_info['product_other_pic'.$i]);
						$product_info['product_other_pic_only_name'.$i] = $only_product_name[0];
						$product_info['product_other_thum_pic'.$i] = PIC_FILE.$vcc_code.'/'.$product_info['product_other_thum_pic'.$i];
						$product_info['product_other_pic'.$i] = PIC_FILE.$vcc_code.'/'.$product_info['product_other_pic'.$i];
					}
					else
					{
						$product_info['product_other_thum_pic'.$i] = '';
						$product_info['product_other_pic'.$i] = '';
					}
				}
			}
			if(!empty($product_info['product_class_id']))
			{
				$class_info = $this->get_product_class_info($product_info['product_class_id']);
				if(!empty($class_info))
				{
					$product_info['product_class_pname'] = $class_info['p_c_pname'];
				}
				else
				{
					$product_info['product_class_pname'] = '';
				}
			}

			$this->cache->save('product_info'.$product_id,$product_info,600);
		}
		else
		{
			$product_info = $this->cache->get('product_info'.$product_id);
		}

		return $product_info;
	}

	/**
	 * 添加新产品
	 *
	 * @param array $product_info    新产品信息
	 * @return int  $product_id 新产品ID
	 */
	public function insert_product($product_info = array() )
	{
		if(empty($product_info['product_class_id']))
		{
			return FALSE;
		}

		$product_id = 0;
		$product_info['product_state'] = empty($product_info['product_state']) ? 0 : $product_info['product_state'];
		$product_info['product_num'] = empty($product_info['product_num']) ? $this->new_product_number() : $product_info['product_num'];

		/*判断是否可编辑的字段*/
		$insert_array = array();
		foreach (array('product_name','product_price','product_state','product_class_id','product_pic','product_num','product_thum_pic','product_other_pic1','product_other_thum_pic1','product_other_pic2','product_other_thum_pic2','product_other_pic3','product_other_thum_pic3','product_other_pic4','product_other_thum_pic4','product_other_pic5','product_other_thum_pic5','product_other_pic6','product_other_thum_pic6','product_other_pic7','product_other_thum_pic7','product_other_pic8','product_other_thum_pic8') as $key)
		{
			if(isset($product_info[$key]))
			{
				$insert_array[$key] = $product_info[$key];
			}
		}
		//得到产品自定义字段
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);
		foreach ($confirm_field AS $item)
		{
			if($item["data_type"]==4 || $item["data_type"]==7)//级联自定义字段
			{
				if(!empty($product_info[$item["fields"]."_1"]))
				$insert_array[$item["fields"].'_1'] = is_array($product_info[$item["fields"]."_1"])?implode(',',$product_info[$item["fields"]."_1"]):$product_info[$item["fields"]."_1"];
				if(!empty($product_info[$item["fields"]."_2"]))
				$insert_array[$item["fields"].'_2'] = is_array($product_info[$item["fields"]."_2"])?implode(',',$product_info[$item["fields"]."_2"]):$product_info[$item["fields"]."_2"];
				if(!empty($product_info[$item["fields"]."_3"]))
				$insert_array[$item["fields"].'_3'] = is_array($product_info[$item["fields"]."_3"])?implode(',',$product_info[$item["fields"]."_3"]):$product_info[$item["fields"]."_3"];
			}
			else
			{
				$insert_array[$item["fields"]] = empty($product_info[$item["fields"]]) ? "" : $product_info[$item["fields"]];
			}
		}
		$result = $this->db_write->insert("est_product",$insert_array);
		if (!$result)
		{
			return FALSE;
		}
		$product_id = $this->db_write->insert_id();
		$this->_clear_product_cache($product_id);

		return $product_id;
	}

	/**
	 * 修改产品信息
	 *
	 * @param int   $product_id   产品ID
	 * @param array $product_info      需要编辑的信息
	 * @return bool
	 */
	public function update_product($product_id=0,$product_info=array())
	{
		if($product_id==0 ||empty($product_info['product_class_id']))
		{
			return '缺少参数';
		}

		$product_info['product_state'] = empty($product_info['product_state']) ? 0 : $product_info['product_state'];
		$product_info['product_num'] = empty($product_info['product_num']) ? $this->new_product_number() : $product_info['product_num'];

		/*判断是否可编辑的字段*/
		$update_array = array();
		foreach (array('product_name','product_price','product_state','product_class_id','product_pic','product_num','product_thum_pic','product_other_pic1','product_other_thum_pic1','product_other_pic2','product_other_thum_pic2','product_other_pic3','product_other_thum_pic3','product_other_pic4','product_other_thum_pic4','product_other_pic5','product_other_thum_pic5','product_other_pic6','product_other_thum_pic6','product_other_pic7','product_other_thum_pic7','product_other_pic8','product_other_thum_pic8') as $key)
		{
			if(isset($product_info[$key]))
			{
				$update_array[$key] = $product_info[$key];
			}
		}
		//得到产品自定义字段
		$this->load->model("field_confirm_model");
		$confirm_field = $this->field_confirm_model->get_available_confirm_fields(FIELD_TYPE_PRODUCT);
		foreach ($confirm_field AS $item)
		{
			if(isset($item["data_type"]) && $item["data_type"]==4 || $item["data_type"]==7)//级联自定义字段
			{
				if(isset($product_info[$item['fields']."_1"]))
				{
					$update_array[$item['fields'].'_1'] = is_array($product_info[$item["fields"]."_1"])?implode(',',$product_info[$item["fields"]."_1"]):$product_info[$item["fields"]."_1"];
				}
				if(isset($product_info[$item['fields']."_2"]))
				{
					$update_array[$item['fields'].'_2'] = is_array($product_info[$item["fields"]."_2"])?implode(',',$product_info[$item["fields"]."_2"]):$product_info[$item["fields"]."_2"];
				}
				if(isset($product_info[$item['fields']."_3"]))
				{
					$update_array[$item['fields'].'_3'] = is_array($product_info[$item["fields"]."_3"])?implode(',',$product_info[$item["fields"]."_3"]):$product_info[$item["fields"]."_3"];
				}
			}
			else
			{
				$update_array[$item["fields"]] = empty($product_info[$item["fields"]]) ? "" : $product_info[$item["fields"]];
			}
		}
		$result = $this->db_write->update("est_product",$update_array,array('product_id'=>$product_id));
		$this->_clear_product_cache($product_id);
		return $result;
	}

	/**
	 * 删除产品
	 * @param string $product_id
	 * @return bool
	 */
	public function delete_product($product_id='')
	{
		if(empty($product_id))
		{
			return false;
		}

		$where = 'product_id IN('.$product_id.')' ;
		$result = $this->db_write->delete('est_product',$where);
		if($result)
		{
			$this->_clear_product_cache($product_id);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 *  改变产品状态
	 *
	 * @param int    $product_id  产品ID
	 * @param int $product_state  产品状态
	 * @return bool
	 */
	public function set_product_state($product_id=0,$product_state=0)
	{
		if($product_id==0 ||$product_state==0)
		{
			return false;
		}
		if($product_state==1)
		{
			$product_state=2;
		}
		else
		{
			$product_state=1;
		}
		$result = $this->db_write->query("UPDATE est_product SET product_state = $product_state WHERE product_id = ".$product_id);
		$this->_clear_product_cache($product_id);
		if($result)
		{
			return 1;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 订单产品 combbox
	 * @param string $key_value
	 * @return array
	 */
	public function get_product_box($key_value='')
	{
		$wheres = array();
		if ($key_value)
		{
			$wheres[] = "product_num like '$key_value%' OR product_name LIKE '%$key_value%'";
		}

		$where = implode(" AND ",$wheres);
		if ($where)
		{
			$this->db_read->where($where);
		}

		$this->db_read->select('product_id,product_name,product_num');
		$this->db_read->limit(9);
		$this->db_read->order_by('product_id','desc');
		$query = $this->db_read->get("est_product");
		$result = $query->result_array();
		if(!empty($result))
		{
			foreach ($result AS $key => $value)
			{
				if ($value["product_name"])
				{
					$result[$key]["product"] = $value["product_name"]."[".$value["product_num"]."]";
				}
			}
		}
		return $result;
	}


	/**
     * 新产品编号
     * 编号的标准是以30开头依次累加 比如：3000000001
     * 
     * @param string $type_name 类型名称
     * @return string
     */
	public function new_product_number($type_name='product')
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
		$type_name = $type_name.$vcc_id;
		$count_file = $lock_dir.$type_name;//计数文件
		$lock_file = $lock_dir.$type_name.'.lck';//锁文件
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
			file_put_contents($count_file,$count_num);
			flock($fp, LOCK_UN); // 释放锁定
		}
		fclose($fp);
		$serial_number = '3'.sprintf('%03d',$count_num);
		return $serial_number;
	}

	/**
	  * 上传产品图片（并生成缩略图）
	  * @param string $pic_name
	  * @return array
	  */
	public function upload_pic($pic_name='')
	{
		$uppic = array();
		if(empty($pic_name))
		{
			return $uppic;
		}
		$vcc_code = $this->session->userdata('vcc_code');
		//上传附件
		$allowed_types = 'jpg|png|gif|jpeg|bmp';
		$filepath = PIC_FILE.$vcc_code.'/';
		if(!is_dir(PIC_FILE))
		{
			@mkdir(PIC_FILE);
			@chmod(PIC_FILE,0777);
		}
		if(!is_dir($filepath))
		{
			@mkdir($filepath);
			@chmod($filepath,0777);
		}
		$config['upload_path'] = $filepath; //上传路径
		$config['allowed_types'] = $allowed_types;//允许类型
		$config['overwrite'] = TRUE;

		$picfix_num = $this->new_product_number('productpic');

		$config['file_name'] = date('Ymd',time()).$picfix_num.'.jpg'; //重命名
		$this->load->library('upload',$config);
		$this->upload->initialize($config);
		//接收文件
		$receiver_pic = "";
		$width  = 0;
		$height = 0;
		if($_FILES[$pic_name]['name'])
		{
			if(!$this->upload->do_upload($pic_name))
			{
				sys_msg($this->upload->display_errors());
			}
			else
			{
				$udata = $this->upload->data();
				//上传成功
				$receiver_pic = $udata['file_name'];

				//得到图片 width   heght
				$width  = $udata['image_width'];
				$height = $udata['image_height'];
			}
		}

		if(!empty($receiver_pic))
		{
			//生成缩略图
			$pic_thumb = $this->_get_thumb_pic($receiver_pic,50,50);
			if($pic_thumb)
			{
				// 缩小超过限制的图片  max_width = 800；  max_height = 800
				if ( $width > 800 || $height > 800)
				{
					$width  = $width > 800 ? 800 : $width;
					$height = $height > 800 ? 800 : $height;

					$this->_get_thumb_pic($receiver_pic,800,800,FALSE);
				}

				$uppic['pic']       = $receiver_pic;
				$uppic['pic_thumb'] = $pic_thumb;
				return $uppic;
			}
		}
		return $uppic;
	}

	/**
	 * 生成缩略图
	 * @param string $pic 原图
	 * @param int $width 宽
	 * @param int $height 高
	 * @param bool $create_thumb  TRUE 生成缩略图， FALSE 仅缩放图片，不生成缩略图
	 * @return array
	 */
	private function _get_thumb_pic($pic,$width=50,$height=50,$create_thumb = TRUE)
	{
		$vcc_code = $this->session->userdata('vcc_code');
		$config['image_library'] = 'gd2';
		$config['source_image'] = PIC_FILE.$vcc_code.'/'.$pic;
		$config['create_thumb'] = $create_thumb;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;

		$this->load->library('image_lib');
		$this->image_lib->clear();
		$this->image_lib->initialize($config);

		if ($this->image_lib->resize())
		{
			$thumb = explode('.',$pic);
			$pic_thumb = $thumb[0].'_thumb.'.$thumb[1];
			return $pic_thumb;
		}
		else
		{
			sys_msg($this->image_lib->display_errors());
		}
	}

	/**
	 * 删除产品某图片
	 * 
	 * @param int $product_id 产品id
	 * @param string $product_only_name 图片名称
	 * @param string $pic 图片字段名称
	 * @param string $pic_thumb 缩略图字段名称
	 * @return bool
	 * @author zgx
	 */
	public function delete_product_pic($product_id=0,$product_only_name='',$pic='',$pic_thumb='')
	{
		if(empty($product_id)||empty($product_only_name)||empty($pic)||empty($pic_thumb))
		{
			return false;
		}
		$vcc_code = $this->session->userdata('vcc_code');
		$update_array = array();
		$update_array[$pic] = '';
		$update_array[$pic_thumb] = '';

		$result = $this->db_write->update('est_product',$update_array);
		if($result)
		{
			$this->_clear_product_cache($product_id); //清楚缓存
			@unlink(PIC_FILE.$vcc_code.'/'.$product_only_name.".jpg");
			@unlink(PIC_FILE.$vcc_code.'/'.$product_only_name."_thumb.jpg");
			return $pic;
		}
		else
		return false;
	}
}