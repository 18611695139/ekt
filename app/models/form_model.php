<?php
class Form_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 清除缓存
	 *
	 * @param unknown_type $form_id
	 */
	private function _clear_form_cache($form_id=0)
	{
		$this->cache->delete('all_form_name');
		if(!empty($form_id))
		{
			$this->cache->delete('form_fields'.$form_id);
			$this->cache->delete('cascade_names_'.$form_id);
			$query = $this->db_read->query("SELECT field,parent_id FROM `est_form_cascade` where form_id=".$form_id." group by parent_id");
			foreach($query->result_array() as $info)
			{
				$this->cache->delete('cascade_'.$info['field'].'_'.$form_id.'_'.$info['parent_id']);
			}
		}
	}

	/**
     * 清除级联缓存
     * @param $form_id
     * @param $field
     * @param int $parent_id
     * @return bool
     */
	private function _clear_cascade_cache($form_id,$field,$parent_id=0)
	{
		$this->cache->delete('cascade_names_'.$form_id);
		$this->cache->delete('cascade_'.$field.'_'.$form_id.'_'.$parent_id);
		return true;
	}

	/**
	 * 添加表单
	 * @param string $form_name 表单名称
	 * @param string $form_html
	 * @param string $cascadeOption 级联选项信息
	 * @return bool|int
	 */
	public function insert_form($form_name='',$form_html='',$cascadeOption='')
	{
		if(empty($form_name) || empty($form_html))
		{
			return false;
		}
		$result = $this->translate_html_to_fields($form_html);
		$gobal_html   = isset($result['gobal_html']) ? $result['gobal_html'] : '';
		$new_html   = isset($result['html']) ? $result['html'] : '';
		$fields = isset($result['fields']) ? $result['fields'] : '';

		$this->db_write->trans_start();

		$data = array(
		'form_name' => $form_name,
		'form_html'=>stripcslashes($new_html),
		'form_gobal_html' =>stripcslashes($gobal_html),
		'create_user_id' => $this->session->userdata('user_id'),
		'create_time' => time()
		);
		$this->db_write->insert('est_form',$data);
		$form_id = $this->db_write->insert_id();

		//新建字段
		foreach($fields AS $k => $field)
		{
			$fields[$k]['form_id'] = $form_id;
			switch($field['field_type'])
			{
				case 1:
					$default_value = explode(PHP_EOL, $fields[$k]['default_value']);
					$fields[$k]['default_value'] = implode(",",$default_value);
					break;
			}
		}
		$this->db_write->insert_batch('est_form_fields', $fields);

		$fields_info   = array();//存储表单表字段信息
		foreach($fields AS $k => $field)
		{
			$fields[$k]['form_id'] = $form_id;
			switch($field['field_type'])
			{
				case 1:
					$fields_info[] = " `".trim($field['fields'])."` text NOT NULL COMMENT '$field[field_name]'";//文本框
					break;
				default:
					$fields_info[] = " `".trim($field['fields'])."` VARCHAR( 100 ) NOT NULL COMMENT '$field[field_name]'";
					break;
			}
		}
		//表单表
		$sql  = "CREATE TABLE IF NOT EXISTS `est_form_info".$form_id."` (`id` int(11) NOT NULL auto_increment PRIMARY KEY,";
		$sql .= " `flow_id` int(11) NOT NULL COMMENT '流程id',";
		$sql .= " `flow_info_id` int(11) NOT NULL COMMENT '流程详细信息id',";
		$sql .= " `node_id` int(11) NOT NULL COMMENT '节点ID',";
		$sql .= implode(",", $fields_info).',';
		$sql .= " `create_user_id` int(11) NOT NULL COMMENT '创建人id',";
		$sql .= " `create_time` int(11) NOT NULL COMMENT '创建时间',";
		$sql .= " KEY `flow_id` (`flow_id`),";
		$sql .= " KEY `flow_info_id` (`flow_info_id`)";
		$sql .= ") ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='表单信息".$form_id."' AUTO_INCREMENT=1 ;";
		$this->db_write->query($sql);

		//级联自定义选项
		if(!empty($cascadeOption))
		{
			$this->load->library("json");
			$cascadeOption = $this->json->decode($cascadeOption,1);
			foreach($cascadeOption as $key=>$value)
			{
				$series = count($value);
				if($series>0)
				{
					//第一级选项
					$real_id1 = array();
					if(!empty($value[1][0]))
					{
						foreach($value[1][0] as $option1)
						{
							$data = array('parent_id'=>0,'name'=>$option1['name'],'field'=>$option1['field'],'deep'=>1,'form_id'=>$form_id);
							$result = $this->db_write->insert('est_form_cascade',$data);
							$real_id1[$option1['id']] = $this->db_write->insert_id();
						}
					}
					//第2级选项
					if($series==2)
					{
						$insert2 = array();
						foreach($real_id1 as $old_id=>$read_id)
						{
							if(!empty($value[2][$old_id]))
							{
								foreach($value[2][$old_id] as $option2)
								{
									$insert2[] = array('parent_id'=>$read_id,'name'=>$option2['name'],'field'=>$option2['field'],'deep'=>2,'form_id'=>$form_id);
								}
							}
						}
						if(!empty($insert2))
						{
							$this->db_write->insert_batch('est_form_cascade', $insert2);
						}
					}
					else
					{
						$real_id2 = array();
						foreach($real_id1 as $old_id=>$read_id)
						{
							if(!empty($value[2][$old_id]))
							{
								foreach($value[2][$old_id] as $option2)//第2级选项
								{
									$data = array('parent_id'=>$read_id,'name'=>$option2['name'],'field'=>$option2['field'],'deep'=>2,'form_id'=>$form_id);
									$result = $this->db_write->insert('est_form_cascade',$data);
									$real_id2[$option2['id']] = $this->db_write->insert_id();
								}
							}
						}
						//第三级选项
						$insert3 = array();
						foreach($real_id2 as $old_id=>$read_id)
						{
							if(!empty($value[3][$old_id]))
							{
								foreach($value[3][$old_id] as $option3)//第2级选项
								{
									$insert3[] = array('parent_id'=>$read_id,'name'=>$option3['name'],'field'=>$option3['field'],'deep'=>3,'form_id'=>$form_id);
								}
							}
						}
						if(!empty($insert3))
						{
							$this->db_write->insert_batch('est_form_cascade', $insert3);
						}
					}
				}
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			$this->_clear_form_cache();//清除缓存
			return $form_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 编辑表单
	 *
	 * @param int $form_id
	 * @param string $form_name
	 * @param string $form_html
	 * @return bool
	 */
	public function update_form($form_id=0,$form_name='',$form_html='')
	{
		if(empty($form_id)||empty($form_name)||empty($form_html))
		{
			return false;
		}

		$update_user_id = $this->session->userdata('user_id');

		$result = $this->translate_html_to_fields($form_html);
		$gobal_html   = isset($result['gobal_html']) ? $result['gobal_html'] : '';
		$new_html   = isset($result['html']) ? $result['html'] : '';
		$fields = isset($result['fields']) ? $result['fields'] : '';

		$this->db_write->trans_start();

		$data = array(
		'form_name'=>$form_name,
		'form_html' => stripcslashes($new_html),
		'form_gobal_html' =>stripcslashes($gobal_html),
		'update_user_id'=>$update_user_id,
		'update_time'=>time()
		);
		$this->db_write->where("form_id",$form_id);
		$this->db_write->update("est_form",$data);
		//表单表表名
		$form_table = 'est_form_info'.$form_id;
		//获取原本字段信息
		$old_fields = array();
		$form_fields_info = $this->get_form_fields_info($form_id);
		foreach($form_fields_info AS $v)
		{
			$old_fields[] = $v['fields'];
		}
		$new_fields = array();
		foreach($fields AS $k=>$v)
		{
			$new_fields[$k] = $v['fields'];
		}

		//新增加的字段
		$fields_to_add = array_diff($new_fields, $old_fields);
		foreach($fields_to_add AS $k => $v)
		{
			$fields[$k]['form_id'] = $form_id;
			$this->db_write->insert('est_form_fields', $fields[$k]);//字段表添加新字段
			//更新表单信息表的字段
			$field_name = $fields[$k]['field_name'];
			if($fields[$k]['field_type']==1)
			{
				$this->db_write->query("ALTER TABLE $form_table ADD `$v` text NOT NULL COMMENT '$field_name'");
			}
			else
			{
				$this->db_write->query("ALTER TABLE $form_table ADD `$v` VARCHAR( 100 ) NOT NULL COMMENT '$field_name'");
			}
		}

		//删除的字段
		$fields_to_delete = array_diff($old_fields, $new_fields);
		foreach($fields_to_delete AS $k => $v)
		{
			$this->db_write->where('form_id', $form_id);
			$this->db_write->where('fields', $v);
			$this->db_write->delete('est_form_fields');
		}

		//更新字段信息
		foreach($fields AS $k => $v)
		{
			if(!in_array($v['fields'],$fields_to_add)&&!in_array($v['fields'],$fields_to_delete))
			{
				$fields_to_update[$k] = $v;
				$this->db_write->update('est_form_fields',$v,array('form_id'=>$form_id,'fields'=>$v['fields']));
				if($v['field_type'] == 1)
				{
					$this->db_write->query("ALTER TABLE $form_table MODIFY COLUMN ".$v['fields']." text NOT NULL COMMENT '".$v['field_name']."'");
				}
			}
		}

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			$this->_clear_form_cache($form_id);//清除缓存
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除表单
	 * @param int $form_id
	 * @return bool
	 */
	public function delete_form($form_id=0)
	{
		if (empty($form_id))
		{
			return FALSE;
		}
		$form_ids = array();
		if(is_array($form_id))
		{
			foreach ($form_id as $item)
			{
				$form_ids[] = (int)$item;
			}
		}
		else
		{
			$form_ids = array($form_id);
		}
		$this->db_write->trans_start();
		/*$this->db_write->where_in('form_id',$form_ids);
		$this->db_write->delete(array('est_form','est_form_cascade','est_form_fields'));
		foreach($form_ids AS $id)
		{
			if(!empty($id))
			{
				$table = 'est_form_info'.$id;
				if($this->db_read->table_exists($table))
				{
					$this->db_write->query("DROP TABLE $table");
				}
				$this->_clear_form_cache($id);//清除缓存
			}
		}*/
        $this->db_write->where_in('form_id',$form_ids);
        $this->db_write->update('est_form',array('is_del'=>1));
        $this->_clear_form_cache();//清除缓存

		$this->db_write->trans_complete();
		if($this->db_write->trans_status() === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取表单列表数据
	 *
	 * @param array $condition
	 * @param int $page
	 * @param int $limit
	 * @param string $sort
	 * @param string $order
	 * @return object
	 */
	public function get_form_list($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = array('is_del = 0');
		$responce = new stdClass();
		$responce -> total = 0;
		$responce -> rows = array();

		if(isset($condition['form_name']))
		{
			$wheres[] = "form_name LIKE '%".$condition['form_name']."%'";
		}
		$where = implode(" AND ",$wheres);
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_form');
		$total = $total_query->row()->total;
		$responce -> total = $total;
		if(empty($page))
		{
			list($page, $limit, $sort, $order) = get_list_param();
		}
		if( ! empty($sort))
		{
			$this->db_read->order_by($sort,$order);
		}
		$start = get_list_start($total,$page,$limit);
		$this->db_read->limit($limit,$start);
		$this->db_read->select('form_id,form_name,create_user_id,create_time,update_user_id,update_time');
		$data = $this->db_read->get('est_form');
		$this->db_read->flush_cache();//清除缓存
		if($data)
		{
			//员工信息
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users_without_dept();
			$user_info   = array();
			foreach ($user_result as $user)
			{
				$user_info[$user["user_id"]] = $user["user_name"];
			}

			foreach($data->result_array() AS $i=>$value)
			{
				$value["create_time"] = $value["create_time"] ? date("Y-m-d H:i:s",$value["create_time"]) : ""; //创建时间
				$value["create_user_name"]   = empty($user_info[$value["create_user_id"]]) ? "" : $user_info[$value["create_user_id"]];
				$value["update_time"] = $value["update_time"] ? date("Y-m-d H:i:s",$value["update_time"]) : ""; //修改时间
				$value["update_user_name"]   = empty($user_info[$value["update_user_id"]]) ? "" : $user_info[$value["update_user_id"]];

				$responce -> rows[$i] = $value;
			}
		}
		return $responce;
	}
	
	/**
	 * 获取表单信息
	 *
	 * @param int|array $form_id
	 * @return array
	 */
	public function get_form_info($form_id=0)
	{
		if(empty($form_id))
		{
			return false;
		}
		$form_ids = array();
		$form_info = array();
		if(is_array($form_id))
		{
			foreach ($form_id as $item)
			{
				$form_ids[] = (int)$item;
			}
			$this->db_read->where_in('form_id',$form_ids);
			$this->db_read->select("form_id,form_name,form_html,form_gobal_html,is_del");
			$query = $this->db_read->get("est_form");
			foreach($query->result_array() as $value)
			{
				$form_info[$value['form_id']] = $value;
			}
		}
		else
		{
			$this->db_read->where("form_id",$form_id);
			$this->db_read->select("form_id,form_name,form_html,form_gobal_html,is_del");
			$query = $this->db_read->get("est_form");
			$form_info = $query->row_array();
		}
		return $form_info;
	}

	/**
	 * 获取多个表单字段信息
	 *
	 * @param string $form_ids
	 * @return array
	 */
	public function get_more_form_fields($form_ids=array())
	{
		if(empty($form_ids)||!is_array($form_ids))
		{
			return false;
		}
		$more_form_fields = array();
		foreach ($form_ids as $form_id)
		{
			$more_form_fields[$form_id] = array();
			$fields = $this->get_form_fields_info($form_id);
			if(!empty($fields))
			{
				$more_form_fields[$form_id] = $fields;
			}
		}
		return $more_form_fields;
	}

	/**
	 * 获取表单字段信息
	 *
	 * @param int|array $form_id
	 * @return mixed
	 */
	public function get_form_fields_info($form_id=0)
	{
		if(empty($form_id))
		{
			return false;
		}
		if( ! $this->cache->get('form_fields'.$form_id))
		{
			$form_fields = array();
			$this->db_read->where('form_id',$form_id);
			$result = $this->db_read->get('est_form_fields');
			foreach($result->result_array() as $key=>$val)
			{
				if($val['field_type'] == 6)
				{
					$val['default_value'] = date('Y-m-d H:i:s',time());
				}
				if($val['field_type'] == 1)
				{
					if(empty($val['default_value']))
					{
						$val['default_value'] = '';
					}
					else
					{
						$val['default_value'] = explode(",",$val['default_value']);
						$val['default_value'] = implode(PHP_EOL,$val['default_value']);
					}
				}
				$form_fields[$key] = $val;
			}
			$this->cache->save('form_fields'.$form_id,$form_fields,600);
		}
		else
		{
			$form_fields = $this->cache->get('form_fields'.$form_id);
		}
		return $form_fields;
	}

	/**
	 * 获取所有表单名和id
	 *
	 * @return array
	 */
	public function get_all_form_info()
	{
		if( ! $this->cache->get('all_form_name'))
		{
			$this->db_read->select("form_id,form_name");
            $this->db_read->where("is_del",0);
			$query = $this->db_read->get("est_form");
			$form_info = array();
			foreach($query->result_array() as $form)
			{
				$form_info[$form['form_id']] = $form['form_name'];
			}
			$this->cache->save('all_form_name',$form_info,600);
		}
		else
		{
			$form_info = $this->cache->get('all_form_name');
		}
		return $form_info;
	}
	
	/**
	 * 获取某表单某字段级联的选项
	 *
	 * @param int $form_id
	 * @param string $field
	 * @param int $parent_id
	 * @return array
	 */
	public function get_cascade_info($form_id=0,$field='',$parent_id=0)
	{
		if(empty($form_id) || empty($field))
		{
			return false;
		}
		if( ! $this->cache->get('cascade_'.$field.'_'.$form_id.'_'.$parent_id))
		{
			$this->db_read->where("parent_id",$parent_id);
			$this->db_read->where("form_id",$form_id);
			$this->db_read->where("field",$field);
			$this->db_read->select("id,parent_id,name,deep");
			$query = $this->db_read->get("est_form_cascade");
			//数据
			$result = $query->result_array();
			$cascade_info = array();
			foreach($result AS $key => $value)
			{
				$cascade_info[$value["id"]] = $value["name"];
			}
			$this->cache->save('cascade_'.$field.'_'.$form_id.'_'.$parent_id,$cascade_info,600);
		}
		else
		{
			$cascade_info = $this->cache->get('cascade_'.$field.'_'.$form_id.'_'.$parent_id);
		}
		return $cascade_info;
	}

	/**
     * 获取某表单级联字段所有选项名称
     * @param int $form_id
     * @return array|bool
     */
	public function get_echo_form_cascade_name($form_id=0)
	{
		if(empty($form_id))
		{
			return false;
		}

		if( ! $this->cache->get('cascade_names_'.$form_id))
		{
			$this->db_read->where("form_id",$form_id);
			$this->db_read->select("id,parent_id,name,deep,field");
			$query = $this->db_read->get("est_form_cascade");
			//数据
			$result = $query->result_array();
			$cascade_info = array();
			foreach($result AS $value)
			{
				$cascade_info[$value["field"].'_'.$value["deep"]][$value["id"]] = $value["name"];
			}
			$this->cache->save('cascade_names_'.$form_id,$cascade_info,600);
		}
		else
		{
			$cascade_info = $this->cache->get('cascade_names_'.$form_id);
		}
		return $cascade_info;
	}

	/**
     * 级联框添加选项
     * @param int $form_id
     * @param int $parent_id
     * @param string $name
     * @param int $deep
     * @param string $field
     * @return bool
     */
	public function insert_cascade($form_id=0,$parent_id=0,$name='',$deep=1,$field='')
	{
		if(empty($form_id)||empty($field))
		{
			return false;
		}
		$data = array('parent_id'=>$parent_id,'name'=>$name,'field'=>$field,'deep'=>$deep,'form_id'=>$form_id);
		$result = $this->db_write->insert('est_form_cascade',$data);
		if($result)
		{
			$this->_clear_cascade_cache($form_id,$field,$parent_id);//清楚缓存
			return $this->db_write->insert_id();
		}
		else
		return false;
	}

	/**
     * 级联框选项名修改
     * @param int $id
     * @param string $name
     * @return bool
     */
	public function update_cascade($id=0,$name='')
	{
		if(empty($id))
		{
			return false;
		}
		$data = array('name'=>$name);
		$this->db_write->where("id",$id);
		$result = $this->db_write->update("est_form_cascade",$data);
		if($result)
		{
			$this->db_read->where("id",$id);
			$this->db_read->select("form_id,field,parent_id");
			$query = $this->db_read->get("est_form_cascade");
			$info = $query->row_array();
			$this->_clear_cascade_cache($info['form_id'],$info['field'],$info['parent_id']);//清楚缓存
		}
		return $result;
	}

	/**
     * 级联框选项删除
     * @param int $id
     * @param int $deep
     * @param int $series
     * @return bool
     */
	public function delete_cascade($id=0,$deep=0,$series=3)
	{
		if(empty($id)||empty($deep))
		{
			return false;
		}
		$del_id = array($id);

		if($series==3 && $deep==1)
		{
			$this->db_read->where_in('parent_id',$id);
			$this->db_read->select('id');
			$query = $this->db_read->get('est_form_cascade');
			foreach($query->result_array() as $value)
			{
				$del_id[] = $value['id'];
			}
		}

		$del_id = implode(',',$del_id);
		$select = $this->db_read->query('SELECT id,parent_id,form_id,field FROM est_form_cascade WHERE id IN('.$del_id.') OR parent_id IN('.$del_id.')');
		$result = $this->db_write->query('DELETE FROM est_form_cascade WHERE id IN('.$del_id.') OR parent_id IN('.$del_id.')');
		if($result)
		{
			foreach($select->result_array() as $value)
			{
				$this->_clear_cascade_cache($value['form_id'],$value['field'],$value['parent_id']);//清楚缓存
			}
		}
		return $result;
	}

	/**
	 * 从html中抠出字段信息
	 *
	 * @param string $form_html
	 * @return array
	 */
	public function translate_html_to_fields($form_html='')
	{
		if(empty($form_html))
		{
			return false;
		}
		require('./vendor/simple_html_dom/simple_html_dom.php');
		$html   = str_get_html($form_html);
		$gobal_html = $html->save();
		$fields = array();
		$control_group = $html->find('div[class=control-group]');
		foreach($control_group AS $div)
		{
			$widgetId = $div->title;
			$fields[$widgetId] = array('field_if_must'=>0,'default_value'=>'','option_type'=>'','table'=>'','option_value'=>'','option_text'=>'','parent_value'=>'','parent_table'=>'','parent_table_value'=>'','series'=>'','usessq'=>0,'datefmt'=>'');
			$fields[$widgetId]['fields'] = $widgetId;//字段
			$fields[$widgetId]['field_name'] = $div->label;//字段名称
			$div->removeAttribute('label');
			$div->removeAttribute('title');
			//是否必填
			if($div->hasAttribute('required'))
			{
				if($div->required=='required')
				{
					$fields[$widgetId]['field_if_must'] = 1;
				}
				$div->removeAttribute('required');
			}
			//默认值
			if($div->hasAttribute('defaultval'))
			{
				$fields[$widgetId]['default_value'] = $div->defaultval;
				$div->removeAttribute('defaultval');
			}
			if($div->hasAttribute('table'))//数据库表
			{
				$fields[$widgetId]['table'] = $div->table;
				$div->removeAttribute('table');
			}
			if($div->hasAttribute('fieldvalue'))//值字段名
			{
				$fields[$widgetId]['option_value'] = $div->fieldvalue;
				$div->removeAttribute('fieldvalue');
			}
			if($div->hasAttribute('fieldtext'))//文本字段名
			{
				$fields[$widgetId]['option_text'] = $div->fieldtext;
				$div->removeAttribute('fieldtext');
			}
			if($div->hasAttribute('arrange'))//排列方式
			{
				$div->removeAttribute('arrange');
			}
			if($div->hasAttribute('options'))//选项
			{
				$div->removeAttribute('options');
			}

			switch($div->type)
			{
				case 'input':
					$fields[$widgetId]['field_type'] = 0;//输入框
					if($div->hasAttribute('validate'))
					{
						$inputType = $div->validate;
						$div->removeAttribute('validate');
						switch($inputType)
						{
							case 'number':
								break;
							case 'email':
								break;
							default:
								break;
						}
					}
					break;
				case 'textarea':
					$fields[$widgetId]['field_type'] = 1;//文本框
					if($div->hasAttribute('rows'))//行数
					{
						$div->removeAttribute('rows');
					}
					break;
				case 'checkbox':
					$fields[$widgetId]['field_type'] = 2;//多选框
					break;
				case 'radio':
					$fields[$widgetId]['field_type'] = 3;//单选框
					break;
				case 'select':
					$fields[$widgetId]['field_type'] = 4;//下拉框
					if($div->hasAttribute('multiple'))//是否多选
					{
						$div->removeAttribute('multiple');
					}
					$fields[$widgetId]['option_type'] = 'custom';
					if($div->hasAttribute('optiontype'))//选项设置方式
					{
						$fields[$widgetId]['option_type'] = $div->optiontype;
						$div->removeAttribute('options');
					}
					break;
				case 'cascade':
					$fields[$widgetId]['field_type'] = 5;//级联框
					$fields[$widgetId]['option_type'] = 'custom';
					if($div->hasAttribute('optiontype'))//选项设置方式
					{
						$fields[$widgetId]['option_type'] = $div->optiontype;
						$div->removeAttribute('options');
					}
					if($div->hasAttribute('parentvalue'))
					{
						$fields[$widgetId]['parent_value'] = $div->parentvalue;
						$div->removeAttribute('parentvalue');
					}
					if($div->hasAttribute('parenttable'))
					{
						$fields[$widgetId]['parent_table'] = $div->parenttable;
						$div->removeAttribute('parenttable');
					}
					if($div->hasAttribute('parenttablevalue'))
					{
						$fields[$widgetId]['parent_table_value'] = $div->parenttablevalue;
						$div->removeAttribute('parenttablevalue');
					}
					$fields[$widgetId]['series'] = $div->series;//级数
					$fields[$widgetId.'_2'] = $fields[$widgetId.'_1'] = $fields[$widgetId];
					$fields[$widgetId.'_1']['fields'] = $widgetId.'_1';
					$fields[$widgetId.'_1']['field_name'] = $fields[$widgetId]['field_name'].'1';
					$fields[$widgetId.'_2']['fields'] = $widgetId.'_2';
					$fields[$widgetId.'_2']['field_name'] = $fields[$widgetId]['field_name'].'2';
					if($fields[$widgetId]['series']==3)
					{
						$fields[$widgetId.'_3'] = $fields[$widgetId];
						$fields[$widgetId.'_3']['fields'] = $widgetId.'_3';
						$fields[$widgetId.'_3']['field_name'] =$fields[$widgetId]['field_name'].'3';
					}
					unset($fields[$widgetId]);
					break;
				case 'date':
					$fields[$widgetId]['field_type'] = 6;//日期框
					$fields[$widgetId]['datefmt'] = 'yyyy-MM-dd HH:mm:ss';
					if($div->hasAttribute('datefmt'))
					{
						if(!empty($div->datefmt))
						{
							$fields[$widgetId]['datefmt'] = $div->datefmt;
						}
						$div->removeAttribute('datefmt');
					}
					break;
				case 'attachment':
					$fields[$widgetId]['field_type'] = 7;//附件框
					break;
				case 'name':
					$fields[$widgetId]['field_type'] = 8;//姓名
					break;
				case 'mobile':
					$fields[$widgetId]['field_type'] = 9;//手机
					break;
				case 'phone':
					$fields[$widgetId]['field_type'] = 10;//电话
					break;
				case 'gender':
					$fields[$widgetId]['field_type'] = 11;//性别
					break;
				case 'address':
					$fields[$widgetId]['field_type'] = 12;//地址
					if($div->hasAttribute('usessq'))
					{
						$fields[$widgetId]['usessq'] = $div->usessq;
					}
					if($fields[$widgetId]['usessq']==1)
					{
						$fields[$widgetId.'_town'] = $fields[$widgetId.'_city'] = $fields[$widgetId.'_province'] = $fields[$widgetId];
						$fields[$widgetId.'_province']['fields'] = $widgetId.'_province';
						$fields[$widgetId.'_province']['field_name'] = '省';
						$fields[$widgetId.'_city']['fields'] = $widgetId.'_city';
						$fields[$widgetId.'_city']['field_name'] = '市';
						$fields[$widgetId.'_town']['fields'] = $widgetId.'_town';
						$fields[$widgetId.'_town']['field_name'] = '区';
					}
					break;
			}
		}
		$new_html = $html->save();
		$html->clear();

		return array('gobal_html'=>$gobal_html,'html' => $new_html, 'fields' => $fields);
	}

	/**
     * 追加说明信息
     * @param $flow_info_id
     * @param $table
     * @param $data
     * @return bool
     */
	public function insert_form_description($flow_info_id,$table,$data)
	{
		$this->db_read->where('flow_info_id',$flow_info_id);
		if($this->db_read->update($table,$data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}