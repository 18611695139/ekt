<?php
/**
 * 部门类
 *
 */
class Department_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 清除缓存
	 *
	 * @param int $dept_id 部门id
	 * @return bool
	 */
	private function _clear_dept_cache($dept_id=0)
	{
		$this->cache->delete('tree_info');
		$this->cache->delete('all_of_department');
		if(!empty($dept_id))
		{
			$this->cache->delete('dept_info'.$dept_id);
			$this->cache->delete('department_id_deep'.$dept_id);
			$this->cache->delete('department_parent_ids'.$dept_id);
			$this->cache->delete('department_children'.$dept_id);
		}
		return true;
	}

	/**
	 * 递归函数 返回树的所有节点的数组
	 *
	 * @param array $dept_tree 部门树
	 * @return array 部门id
	 */
	private function _get_tree_ids($dept_tree)
	{
		$children = array();
		if(is_array($dept_tree))
		{
			foreach ($dept_tree as $dept)
			{
				$children[] = $dept['id'];
				if(isset($dept['children']))
				{
					$_children = $this->_get_tree_ids($dept['children']);
					$children = array_merge($children,$_children);
					$children = array_unique($children);
					sort($children);
				}
			}
		}
		return $children;
	}

	/**
	 * 递归得到上级部门信息（从当前节点逆推至第2级，包含自身）
	 *
	 * @param int     $dept_id    当前部门ID
	 * @param array   $dept_info  部门信息，以dept_id为下标的数组
	 * @return array
	 */
	private function get_reverse_last($dept_id = 0,$dept_info =array())
	{
		$result = array();
		if ( $dept_id )
		{
			//返回部门ID数组
			$result[] = $dept_id;

			if ( !empty($dept_info) && $dept_info[$dept_id]["dept_pid"] > 0 && $dept_info[$dept_id]["dept_deep"] > 0 )
			{
				$_result = $this->get_reverse_last($dept_info[$dept_id]["dept_pid"],$dept_info);
				$result  = array_merge($result,$_result);
			}
		}

		return $result;
	}

	/**
	 * 根据$dept_id 获取深度dept_deep符合条件（小于等于）的部门信息
	 *
	 * @param int  $dept_id
	 * @return array()
	 */
	private function get_department_id_deep( $dept_id = 0)
	{
		$result = array();
		if ($dept_id == 0 )
		{
			return $result;
		}

		if(! $this->cache->get('department_id_deep'.$dept_id))
		{
			$the_dept = $this->get_department_info($dept_id);
			$the_deep = empty($the_dept["dept_deep"]) ? 1 : $the_dept["dept_deep"];

			$this->db_read->where("dept_deep <= '$the_deep'");
			$this->db_read->select("dept_id,dept_pid,dept_deep");
			$query = $this->db_read->get("est_department");
			$result = $query->result_array();

			$this->cache->save('department_id_deep'.$dept_id,$result,600);
		}
		else
		{
			$result = $this->cache->get('department_id_deep'.$dept_id);
		}

		return $result;
	}

	//===========================================================================================

	/**
	 * 获取指定部门所有的子部门的ID(包含自身)
	 *
	 * @param int $dept_id 当前部门id
	 * @return array 
	 * <code>
	 * array(
	 * 	[0]=> 部门id,
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_department_children_ids($dept_id = 0)
	{
		$children = array();
		if (!empty($dept_id))
		{
			if( !$this->cache->get('department_children'.$dept_id))
			{
				$dept_tree	= $this->get_somenode_department_tree($dept_id);
				$children	= $this->_get_tree_ids($dept_tree);
				$this->cache->save('department_children'.$dept_id,$children,600);
			}
			else
			{
				$children = $this->cache->get('department_children'.$dept_id);
			}
		}

		return $children;
	}

	/**
	 * 获取当前部门逆推至根节点部门的ID（包含自身）
	 *
	 * @param int     $dept_id  当前部门ID
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=> 部门id,
	 *  ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_department_parent_ids($dept_id = 0)
	{
		if(empty($dept_id))
		{
			return false;
		}
		if( !$this->cache->get('department_parent_ids'.$dept_id))
		{
			//得到符合条件的部门信息
			$condition_dept = $this->get_department_id_deep($dept_id);
			//以dept_id 为下标的部门信息数组
			$temp_dept      = array();
			foreach ($condition_dept AS $value)
			{
				$temp_dept[$value["dept_id"]] = $value;
			}

			//递归函数得到所有父节点
			$dept_info = $this->get_reverse_last($dept_id,$temp_dept);
			$dept_info = array_unique($dept_info);

			$this->cache->save('department_parent_ids'.$dept_id,$dept_info,600);
		}
		else
		{
			$dept_info = $this->cache->get('department_parent_ids'.$dept_id);
		}

		return $dept_info;
	}

	/**
	 * 获取指定部门树的对象数组（包含部门自身）
	 *
	 * @param int $dept_id 部门id
	 * @return array
	 * <code>
	 * array(1) {
  	 *		[1]=>object(stdClass)#45 (1) {
     * 			["id"]=> 	string(1) "1"
     *			["pid"]=>	string(1) "0"
     * 			["text"]=>	string(12) "华夏成讯"
     *			["attributes"]=>	string(1) "1"
     *			["state"]=>	string(4) "open"
     *			["children"]=>	array(1) {
     *								[0]=>object(stdClass)#31 (4) {
     *									["id"]=>	string(1) "4"
     *									["pid"]=>	string(1) "1"
     * 									["text"]=>	string(9) "研发部"
     * 									["attributes"]=>	string(1) "2"
      }
	 * </code>
	 * @author zgx
	 */
	public function get_somenode_department_tree($dept_id = 1)
	{
		$arr_tree = $this->get_whole_department_tree();
		if(!empty($arr_tree) && !empty($arr_tree[$dept_id]))
		{
			$arr_tree[$dept_id] -> state = "open";
			$result_array = object_to_array($arr_tree[$dept_id]);
			return array($result_array);
		}
		else
		{
			return array();
		}
	}

	/**
	 * 获取整个部门树的对象
	 *
	 * return array-obj
	 * <code>
	 * array(1) {
  	 *		[1]=>object(stdClass)#45 (1) {
     * 			["id"]=> 	string(1) "1"
     *			["pid"]=>	string(1) "0"
     * 			["text"]=>	string(12) "华夏成讯"
     *			["attributes"]=>	string(1) "1"
     *			["state"]=>	string(6) "closed"
     *			["children"]=>	array(1) {
     *								[0]=>object(stdClass)#31 (4) {
     *									["id"]=>	string(1) "4"
     *									["pid"]=>	string(1) "1"
     * 									["text"]=>	string(9) "研发部"
     * 									["attributes"]=>	string(1) "2"
     *	}
	 * </code>
	 * @author zgx
	 */
	public function get_whole_department_tree()
	{
		if(!$this->cache->get('tree_info'))
		{
			$arr_tree = array();
			$this->db_read->select('dept_id,dept_pid,dept_name,dept_deep');
			$this->db_read->order_by('dept_id');
			$nodes_query = $this->db_read->get("est_department");
			foreach ($nodes_query->result_array() as $node)
			{
				$tmp_node = new stdClass();
				$tmp_node -> id = $node['dept_id'];
				$tmp_node -> pid = $node['dept_pid'];
				$tmp_node -> iconCls= 'icon-depart';
				$tmp_node -> text = $node['dept_name'];
				$tmp_node -> attributes = $node['dept_deep'];

				$arr_tree[$node['dept_id']] = $tmp_node;
				$arr_tree[$node['dept_pid']] -> children[] = $tmp_node;
				$arr_tree[$node['dept_pid']] -> state = 'closed';
			}
			$this->cache->save('tree_info',$arr_tree,600);
		}
		else
		{
			$arr_tree = $this->cache->get('tree_info');
		}

		return $arr_tree;
	}

	/**
	 * 返回指定部门信息
	 *
	 * @param int $dept_id 部门id
	 * @return array
	 * <code>
	 * 	array(
	 * 		[dept_id]=> 部门id,
	 * 		[dept_pid]=> 父id，
	 * 		[dept_name]=> 部门名称,
	 * 		[dept_deep]=> 部门深度
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_department_info($dept_id=0)
	{
		if( ! $this->cache->get('dept_info'.$dept_id))
		{
			if($dept_id === 0)
			{
				$dept_info = array('dept_id'=>0,'dept_name'=>'','dept_deep'=>0);
			}
			else
			{
				$where = array('dept_id'=>$dept_id);
				$dept_query = $this->db_read->get_where('est_department',$where,1);
				$dept_info = $dept_query->row_array();
			}
			$this->cache->save('dept_info'.$dept_id,$dept_info,600);
		}
		else
		{
			$dept_info = $this->cache->get('dept_info'.$dept_id);
		}
		return $dept_info;
	}

	/**
     * 获取所有部门的信息
     * 
     * @return array 返回所有部门
     * <code>
     * array(
     *   [0]=>array(
     * 		[dept_id]=> 部门id,
     * 		[dept_name]=> 部门名称
     *  )
     * ...
     * )
     * </code>
     * @author zgx
     */
	public function get_all_department()
	{
		if( ! $this->cache->get('all_of_department'))
		{
			$this->db_read->select('dept_id,dept_name');
			$query = $this->db_read->get('est_department');
			$dept_info =  $query->result_array();

			$this->cache->save('all_of_department',$dept_info,600);
		}
		else
		{
			$dept_info = $this->cache->get('all_of_department');
		}

		return $dept_info;
	}

	/**
	 * 添加一部门信息
	 *
	 * @param int $dept_p_id 父id
	 * @param string $dept_name	部门名称
	 * @return bool | int 新增部门id
	 * @author zgx
	 */
	public function insert_department($dept_p_id=0,$dept_name='')
	{
		if($dept_p_id==0)
		{
			return false;
		}
		$dept_p_info = $this->get_department_info($dept_p_id);
		$dept_deep = $dept_p_info['dept_deep'] + 1;
		$data = array(
		'dept_pid'=>$dept_p_id,
		'dept_name'=>$dept_name,
		'dept_deep'=>$dept_deep
		);
		$result = $this->db_write->insert('est_department',$data);
		if($result)
		{
			$dept_id = $this->db_write->insert_id();
			$this->_clear_dept_cache();
			return $dept_id;
		}
		else
		{
			return $result;
		}

	}

	/**
	 * 更新部门信息
	 *
	 * @param int $dept_id 部门id
	 * @param string $dept_name	部门名称
	 * @return bool
	 * @author zgx
	 */
	public function update_department($dept_id=0,$dept_name='')
	{
		if(empty($dept_id))
		{
			return false;
		}
		$data = array('dept_name'=>$dept_name);
		$where = array('dept_id'=>$dept_id);
		$result = $this->db_write->update('est_department',$data,$where);
		if($result)
		{
			$this->load->model('user_model');
			$this->user_model->update_user($data,$where);
			$this->_clear_dept_cache($dept_id);
		}
		return $result;
	}

	/**
	 * 删除部门（包括所有子部门）
	 *
	 * @param int $dept_id 部门id
	 * @return bool
	 * @author zgx
	 */
	public function delete_department($dept_id=0)
	{
		if($dept_id==0||$dept_id==1)
		{
			return false;
		}
		$dept_children = $this->get_department_children_ids($dept_id);
		$where = 'dept_id in ('.implode(',',$dept_children).')';

		$result = $this->db_write->delete('est_department',$where);
		if($result)
		{
			//收回部门所属数据
			$this->load->model('client_resource_model');
			$this->client_resource_model->take_more_client_back(array('dept_id'=>$dept_id),'删除部门，自动收回该部门下所有数据');
			return $this->_clear_dept_cache($dept_id);
		}
		else
		return false;
	}

	/**
	 * 获取下一级部门的信息（不包括本部门及下下级部门,判断是否有下下一级）
	 * 
	 * @param int $dept_id 部门id
	 * @return array
	 * <code>
	 * array(
	 * 	[0]=> array(
	 * 		["id"]=> 	string(1) "1"
     *		["pid"]=>	string(1) "0"
     * 		["text"]=>	string(12) "华夏成讯"
     *		["attributes"]=>	string(1) "1"
     *		["state"]=>	string(6) "closed"
	 *  )
	 * ...
	 * )
	 * </code>
	 * @author zgx
	 */
	public function get_next_level_depatements($dept_id=0)
	{
		$dept_tree = $this->get_somenode_department_tree($dept_id);
		$dept_next_level = array();
		if(empty($dept_tree[0]['children']))
		{
			return $dept_next_level;
		}
		else
		{
			$dept_next_level = $dept_tree[0]['children'];
			foreach ($dept_next_level as $key=>$dept)
			{
				if(!empty($dept['state']))
				{
					$dept_next_level[$key]['children'] = '';
				}
			}
			return $dept_next_level;
		}
	}
}