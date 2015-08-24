<?php
/**
 * 知识库类
 */
class Knowledge_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 获取知识库所有栏目树的对象数组
	 *
	 * @param int $class_id  类型ID(如果 $class_id > 0 返回指定节点的子节点，不包含自身)
	 * @return object
	 */
	public function get_class_tree($class_id = 0)
	{
		$arr_tree = array();
		$this->db_read->order_by('k_class_sort');
		$nodes_query = $this->db_read->get("est_knowledge_class");
		foreach ($nodes_query->result_array() as $node)
		{
			$tmp_node = new stdClass();
			$tmp_node -> id = $node['k_class_id'];
			$tmp_node -> pid = $node['k_class_pid'];
			$tmp_node -> text = $node['k_class_name'];
			$tmp_node -> attributes = $node['k_class_deep'];

			if ( isset($arr_tree[$node['k_class_id']]) ) 
			{
				$arr_tree[$node['k_class_id']]-> id = $tmp_node -> id;
				$arr_tree[$node['k_class_id']]-> pid = $tmp_node -> pid;
				$arr_tree[$node['k_class_id']]-> text = $tmp_node -> text;
				$arr_tree[$node['k_class_id']]-> attributes = $tmp_node -> attributes;
			}
			else 
			{
				$arr_tree[$node['k_class_id']] = $tmp_node;
			}			
			$arr_tree[$node['k_class_pid']] -> children[] = $arr_tree[$node['k_class_id']];
			$arr_tree[$node['k_class_pid']] -> state = "closed";
		}
		if(!empty($arr_tree[1]))
		{
			$arr_tree[1] -> state = "open";
		}
		
		if (empty($arr_tree[$class_id]->children)) 
		{
			return array();
		}
		else 
		{
			return object_to_array($arr_tree[$class_id]->children);
		}
	}

	/**
	 * 返回一条栏目信息
	 *
	 * @param int $k_class_id 栏目id
	 * @return array
	 * <code>
	 * array(
	 * 	[k_class_id]=> 栏目id
	 *  [k_class_name]=> 栏目名称
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_k_class_info($k_class_id=0)
	{
		if($k_class_id === 0)
		{
			$k_class_info = array('k_class_id'=>0,'k_class_name'=>'','k_class_name_link'=>'0,0','k_class_deep'=>0);
		}
		else
		{
			$where = array('k_class_id'=>$k_class_id);
			$k_class_query = $this->db_read->get_where('est_knowledge_class',$where,1);
			$k_class_info = $k_class_query->row_array();
		}

		return $k_class_info;
	}

	/**
	 * 添加栏目
	 *
	 * @param int $k_pid
	 * @param string $k_class_name
	 * @return bool|int $k_class_id
	 */
	public function insert_class($k_pid=0,$k_class_name='')
	{
		$k_class_p_info = $this->get_k_class_info($k_pid);
		$k_class_deep   = $k_class_p_info['k_class_deep'] + 1;
		$data = array(
		'k_class_pid'=>$k_pid,
		'k_class_name'=>$k_class_name,
		'k_class_deep'=>$k_class_deep
		);
		$result = $this->db_write->insert('est_knowledge_class',$data);
		if($result)
		{
			$k_class_id = $this->db_write->insert_id();
			$k_class_name_link =  $k_class_p_info['k_class_name_link'].$k_class_id.',';
			$data = array('k_class_name_link'=>$k_class_name_link,"k_class_sort"=>$k_class_id);
			$where = array('k_class_id'=>$k_class_id);
			$result = $this->db_write->update('est_knowledge_class',$data,$where);
			return $k_class_id;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * 更新栏目信息
	 *
	 * @param int $k_class_id
	 * @param string $k_class_name
	 * @return bool
	 */
	public function update_class($k_class_id=0,$k_class_name='')
	{
		$data = array('k_class_name'=>$k_class_name);
		$where = array('k_class_id'=>$k_class_id);
		$result = $this->db_write->update('est_knowledge_class',$data,$where);

		return $result;
	}
	
	/**
	 * 拖拽节点-> 更新节点信息
	 *
	 * @param int    $patent_id   父节点ID
	 * @param int    $current_id  当前拖拽的节点ID
	 * @param string $child_ids   移动结束后，与移动节点所有平级的节点(包括被移动的节点)（多个ID逗号分隔）
     * @return bool
	 */
	public function drag_node_update($patent_id = 0,$current_id = 0,$child_ids = "")
	{
		if ( empty($patent_id) || empty($current_id) || empty($child_ids) ) 
		{
			return TRUE;
		}
		
		//平级的节点(包括被移动的节点)，用于排序
		$child_ids = explode(",",$child_ids);
		//交换数组中的键和值
		$child_ids = array_flip($child_ids);
		
		//数据
		$update_value  = array();
		
		//父节点信息
		$k_class_p_info = $this->get_k_class_info($patent_id);
		
		//当前节点
		$k_current_deep = $k_class_p_info['k_class_deep'] + 1;
		$k_current_link = $k_class_p_info['k_class_name_link'].$current_id.',';
		$k_current_sort = isset($child_ids[$current_id]) ? $child_ids[$current_id] : 0;
		$update_value[] = array("k_class_id"=>$current_id,"k_class_pid"=>$patent_id,"k_class_deep"=>$k_current_deep,"k_class_name_link"=>$k_current_link,"k_class_sort"=>$k_current_sort);
		//其他同级节点排序
		foreach ($child_ids AS $key_class_id => $value_order) 
		{
			if ($key_class_id > 0 && $key_class_id != $current_id ) 
			{
				$update_value[] = array("k_class_id"=>$key_class_id,"k_class_sort" => empty($value_order) ? 0 : $value_order );
			}
		}
		
		//当前移动节点的子节点
		$children_nodes = $this->get_class_tree($current_id);
		if ( !empty($children_nodes) ) 
		{
			$deal_children  = $this->drap_deal_children($children_nodes,$k_current_deep,$k_current_link);
			if (!empty($deal_children)) 
			{
				$update_value = array_merge($update_value,$deal_children);
			}
		}
        
		//更新数据
		if ( !empty($update_value)) 
		{
			 $this->db_write->update_batch("est_knowledge_class",$update_value,"k_class_id");
			 return TRUE;
		}
		
		return TRUE;
	}
	
	/**
	 * 处理移动节点的子节点
	 *
	 * @param string  $children_nodes   子节点对象数组
	 * @param int    $k_current_deep   节点深度
	 * @param string $k_current_link 
	 * @return array
	 */
	public function drap_deal_children($children_nodes = "",$k_current_deep = 0,$k_current_link = "")
	{
		//处理结果
		$deal_result = array();
		
		if ($children_nodes && $k_current_link) 
		{
			foreach ($children_nodes AS $key => $node_info) 
			{
				$deal_result[] = array("k_class_id"=>$node_info["id"],"k_class_deep"=>$k_current_deep+1,"k_class_name_link"=>$k_current_link.$node_info["id"].",");
				if ( !empty($node_info["children"] ) ) 
				{
					$_deal_result = $this->drap_deal_children($node_info["children"],$k_current_deep+1,$k_current_link.$node_info["id"].",");
					$deal_result = array_merge($deal_result,$_deal_result);
				}
			}
		}
		
		return 	$deal_result;
	}

	/**
	 * 删除栏目
	 *
	 * @param int $k_class_id
	 * @return bool | string(id字符串)
	 */
	public function delete_class($k_class_id=1)
	{
		if($k_class_id <= 1)
		{
			return false;
		}
		//获取包括子类的id
		$k_class_ids = $this->get_children_class_id($k_class_id);
		$k_class_ids_str = implode(",",$k_class_ids);
		//删除底下所有文章
		$this->db_write->query("DELETE FROM est_knowledge_article WHERE k_class_id IN($k_class_ids_str)");
		//删除栏目(包括子类)
		$this->db_write->query("DELETE FROM est_knowledge_class WHERE k_class_id IN($k_class_ids_str)");
		return $k_class_ids_str;
	}

	/**
	 * 获取栏目本身及子类id
	 * @param int $k_class_id 栏目id
	 * @return array 
	 */
	public function get_children_class_id($k_class_id=1)
	{
		$k_class_ids = array();
		$query = $this->db_read->query("SELECT k_class_id FROM est_knowledge_class WHERE k_class_name_link LIKE '%,$k_class_id,%'");
		$art_info = $query->result_array();
		foreach($art_info AS $art)
		{
			$k_class_ids[] = $art['k_class_id'];
		}
		return $k_class_ids;
	}

	/**
	 * 获取栏目本身及父类名称
	 * @param int $k_class_id 分类id
	 * @return array
	 */
	public function get_class_name($k_class_id=0)
	{
		if($k_class_id==0)
		{
			return false;
		}
		$k_class_names = array();
		$query = $this->db_read->query("SELECT k_class_id,k_class_name_link FROM est_knowledge_class WHERE k_class_id=".$k_class_id);
		$k_class_info = $query->row_array();
		if($k_class_info)
		{
			$k_class_name_link = rtrim($k_class_info['k_class_name_link'],',');
			$query_class = $this->db_read->query("SELECT k_class_id,k_class_name FROM est_knowledge_class WHERE k_class_id IN(".$k_class_name_link.")");
			$k_class_names = $query_class->result_array();
		}
		return $k_class_names;
	}

	/**
	 * 获取首页内容
	 * 
	 * @return array
	 */
	function get_knowledge_info()
	{
		$class_info = array();
		$article_info = array();
		$query = $this->db_read->query("SELECT k_class_name,k_class_name_link,k_class_id FROM est_knowledge_class WHERE k_class_deep=1 ORDER BY k_class_sort ASC");

		if($query->num_rows() <= 0)
		{
			return null;
		}
		else
		{
			$rows = $query->result_array();
			for($k=0;$k<count($rows);$k++)
			{
				$class_info[] = $rows[$k];
				$art_types = $this->get_children_class_id($rows[$k]["k_class_id"]);
				$k_class_ids_str = implode(",",$art_types);
				$query = $this->db_read->query("SELECT est_knowledge_article.*,est_knowledge_class.* FROM est_knowledge_article  LEFT JOIN est_knowledge_class ON est_knowledge_article.k_class_id = est_knowledge_class.k_class_id WHERE est_knowledge_article.k_class_id IN(".$k_class_ids_str.") ORDER BY est_knowledge_article.k_art_create_date DESC LIMIT 8");

				if($query->num_rows()<=0)
				{
					$article_info[] = array("");
				}
				else
				{
					$article_info[] = $query->result_array();
				}

			}
		}
		$arr_k_info = array('class_info'=>$class_info,'article_info_10'=>$article_info);
		return $arr_k_info;
	}

	/**
	 * 获取搜索条件
	 * 
	 * @param array $condition 检索内容
	 * @return array
	 */
	public function get_knowledge_condition($condition = NULL)
	{
		$wheres = array();
		if (empty($condition))
		{
			return $wheres;
		}

		//栏目id(包括子类id)
		if(!empty($condition['k_class_id']))
		{
			$k_class_ids = $this->knowledge_model->get_children_class_id($condition['k_class_id']);
			$k_class_types = implode(",",$k_class_ids);
			$wheres[] = "k_class_id IN($k_class_types)";
		}
		//关键字(文章标题)
		if(!empty($condition['search_key']))
		{
			$wheres[] = "k_art_title LIKE '%".$condition['search_key']."%'";
		}
		/****高级搜索***/
		//文章标题
		if(!empty($condition['k_art_title_advan']))
		{
			$wheres[] = "k_art_title LIKE '%".$condition['k_art_title_advan']."%'";
		}
		//栏目id
		if(!empty($condition['k_class_id_advan']))
		{
			$wheres[] = "k_class_id =".$condition['k_class_id_advan'];
		}
		//是否热点
		if( isset($condition['k_art_hot_advan']) && $condition['k_art_hot_advan'] != ''  )
		{
			$wheres[] = "k_art_hot = ".$condition['k_art_hot_advan'];
		}
		//文章内容
		if(!empty($condition['k_content_advan']))
		{
			$wheres[] = "k_art_content LIKE'%".$condition['k_content_advan']."%'";
		}

		return $wheres;

	}

	/**
	 * 文章列表
     * @param int $page
     * @param int $limit
     * @param int $sort
     * @param int $order
     * @param int $where
     * @return object
	 */
	function get_more_art_query($page=1, $limit=10, $sort=NULL, $order=NULL,$where=NULL)
	{
		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_knowledge_article');
		$total = $total_query->row()->total;
		$total_pages = ceil($total/$limit);
		$responce = new stdClass();
		$responce -> total = $total;

		$page = $page > $total_pages ? $total_pages : $page;
		$start = $limit*$page - $limit;
		$start = $start > 0 ? $start : 0;
		if( ! empty($sort))
		$this->db_read->order_by($sort,$order);
		$remind_data = $this->db_read->get('est_knowledge_article',$limit,$start);
		$this->db_read->flush_cache();
		$responce -> rows = array();
		$remind_data = $remind_data->result_array();
		if(!empty($remind_data))
		{
			$knowledge_class_info = array();
			$knowledge_class_query = $this->db_read->query("SELECT k_class_name,k_class_id FROM est_knowledge_class");
			foreach($knowledge_class_query->result_array() AS $kl_class)
			{
				$knowledge_class_info[$kl_class['k_class_id']] = $kl_class['k_class_name'];
			}

			foreach($remind_data AS $i=>$task)
			{
				if(empty($task['k_art_update_user'] ))
				{
					$task['k_art_update_user'] = $task['user_name'];
					$task['k_art_update_date'] = $task['k_art_create_date'];
				}
				$task['k_class_name'] = empty($knowledge_class_info[$task['k_class_id']]) ? '' : $knowledge_class_info[$task['k_class_id']];

				$responce -> rows[$i] = $task;
			}
		}

		return $responce;
	}

	/**
	 *获取某文章所有信息
	 * 
	 * @param int $k_art_id 文章id
	 * @return array
	 * <code>
	 * array(
	 * 		[k_art_id] => 文章id
	 * 		[k_art_title] => 文章标题
	 * 		[k_art_content] => 文章内容
	 * )
	 * </code>
	 */
	function get_article_info($k_art_id=0)
	{
		$k_art_info = array();
		if($k_art_id === 0)
		{
			$k_art_info = array('k_art_id'=>0,'k_art_title'=>'','k_art_content'=>'');
		}
		else
		{

			$k_art_query = $this->db_read->query("SELECT * FROM est_knowledge_article WHERE k_art_id=$k_art_id");
			$k_art_info = $k_art_query->row_array();
			$k_art_click_rate = $k_art_info['k_art_click_rate']+1;
			$data = array('k_art_click_rate'=>$k_art_click_rate);
			$where = array('k_art_id'=>$k_art_id);
			$this->db_write->update('est_knowledge_article',$data,$where);
		}
		return $k_art_info;
	}

	/**
	  * 获取热点关注内容
	  * 
	  * @return array
	  * <code>
	  * array(
	  * 	[0] => array(
	  * 		[k_art_title] => 文章标题
	  * 		[k_art_id] => 文章id
	  * 		[k_class_id] => 栏目id
	  * 	)
	  * 	...
	  * )
	  * </code>
	  */
	function get_hot_info()
	{
		$hot_info = array();
		$k_hot_query = $this->db_read->query("SELECT k_art_title,k_art_id,k_class_id FROM est_knowledge_article WHERE k_art_hot=1 ORDER BY k_art_update_date DESC,k_art_create_date DESC,k_art_click_rate DESC LIMIT 12");
		$hot_info = $k_hot_query->result_array();

		return $hot_info;
	}

	/**
	 * 添加一文章
	 * @param string $k_art_title 文章标题
	 * @param string $k_art_content 文章内容
	 * @param int $k_class_id 分类id
	 * @param int $k_art_hot 是否标记为热点文章
	 * @return bool|int
	 */
	public function insert_article($k_art_title='无标题',$k_art_content='',$k_class_id=2,$k_art_hot=0)
	{
		$user_name = $this->session->userdata('user_name');
		$data = array(
		'user_name'=>$user_name,
		'k_art_create_date'=>date("Y-m-d H:i:s",time()),
		'k_art_content'=>$k_art_content,
		'k_art_title'=>$k_art_title,
		'k_class_id'=>$k_class_id,
		'k_art_hot'=>$k_art_hot
		);
		$result = $this->db_write->insert('est_knowledge_article',$data);
		if($result)
		{
			$k_art_id = $this->db_write->insert_id();
			return $k_art_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 修改文章
	 * 
	 * @param int $k_art_id 文章id
	 * @param string $k_art_title 文章标题
	 * @param string $k_art_content 文章内容
	 * @param int $k_class_id 栏目id
	 * @param int $k_art_hot 是否标记为热点
	 * @return bool
	 */
	function update_article($k_art_id=0,$k_art_title='无标题',$k_art_content='',$k_class_id=2,$k_art_hot=0)
	{
		if($k_art_id==0)
		{
			return false;
		}
		$update_user = $this->session->userdata('user_name');
		$data = array(
		'k_art_title'=>$k_art_title,
		'k_art_content'=>$k_art_content,
		'k_class_id'=>$k_class_id,
		'k_art_hot'=>$k_art_hot,
		'k_art_update_user'=>$update_user,
		'k_art_update_date'=>date("Y-m-d H:i:s",time())
		);
		$where = array('k_art_id'=>$k_art_id);
		$result = $this->db_write->update('est_knowledge_article',$data,$where);
		if($result)
		{
			return $k_art_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 删除文章
	 * 
	 * @param string $k_art_ids 文章id字符串
	 * @return bool
	 */
	function delete_article($k_art_ids)
	{
		$where = "k_art_id IN($k_art_ids)";
		$result = $this->db_write->delete('est_knowledge_article',$where);
		return $result;
	}
}
