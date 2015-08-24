<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Knowledge extends CI_Controller {
	/**
	 * 获取知识库页面
	 */
	public function index()
	{
		admin_priv('power_zsk_view');
		
		//权限：知识库(添加)
		$power_zsk_insert = check_authz("power_zsk_insert");
		$this->smarty->assign("power_zsk_insert",$power_zsk_insert?$power_zsk_insert:0);
		//权限：知识库(编辑)
		$power_zsk_update = check_authz("power_zsk_update");
		$this->smarty->assign("power_zsk_update",$power_zsk_update?$power_zsk_update:0);
		//权限：知识库(删除)
		$power_zsk_delete = check_authz("power_zsk_delete");
		$this->smarty->assign("power_zsk_delete",$power_zsk_delete?$power_zsk_delete:0);
		//权限：知识库(栏目管理)
		$power_zsk_class = check_authz("power_zsk_class");
		$this->smarty->assign("power_zsk_class",$power_zsk_class?$power_zsk_class:0);

		$this->smarty->display('knowledge.htm');
	}

	/**
	 * 获取热点文章
	 */
	public function get_hot_art()
	{
		admin_priv();
		
		$art_hot = $this->get_hot_info();
		$this->smarty->assign('art_hot',$art_hot);

		$this->smarty->display('knowledge_hot.htm');
	}

	/**
	 * 获取主要内容
	 */
	public function get_main_content()
	{
		admin_priv();
		
		/*获取所有文章信息*/
		$this->load->model('knowledge_model');
		$main_message = $this->knowledge_model->get_knowledge_info();

		$this->smarty->assign('art_content',$main_message['article_info_10']);
		$this->smarty->assign('class_name',$main_message['class_info']);

		$this->smarty->display('knowledge_content.htm');
	}

	/**
	 * 获取栏目管理页面
	 */
	public function get_class_page()
	{
		admin_priv('power_zsk_class');
		
		$this->smarty->display('knowledge_class.htm');
	}

	/**
	 * 返回栏目树
	 *
	 */
	public function get_class_tree()
	{
		admin_priv();
		
		$this->load->model('knowledge_model');
		$k_class = $this->knowledge_model->get_class_tree();
		$this->load->library('json');
		echo $this->json->encode($k_class);
	}

	/**
	 *添加栏目 
	 */
	public function insert_class()
	{
		admin_priv('power_zsk_class');
		
		$k_pid = $this->input->post('k_pid');
		$k_class_name = $this->input->post('k_class_name');
		$this->load->model('knowledge_model');
		$k_class_id = $this->knowledge_model->insert_class($k_pid,$k_class_name);
		if( ! $k_class_id)
		{
			make_json_error();
		}
		else
		{
			make_json_result($k_class_id);
		}
	}

	/**
	 *编辑栏目
	 */
	public function update_class()
	{
		admin_priv('power_zsk_class');
		
		$k_class_id = $this->input->post('k_class_id');
		$k_class_name = $this->input->post('k_class_name');
		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->update_class($k_class_id,$k_class_name);
		make_simple_response($result);
	}
	
	/**
	 * 节点拖拽结束,更新节点信息
	 *
	 */
	public function drag_node_update()
	{
		admin_priv('power_zsk_class');
		
		//移动结束后的父节点
		$patent_id  = $this->input->post("patent_id");
		//当前节点
		$current_id = $this->input->post("current_id");
		//移动结束后，与移动节点所有平级的节点(包括被移动的节点)
		$child_ids  = $this->input->post("child_ids");
		
		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->drag_node_update($patent_id,$current_id,$child_ids);
		if ($result) 
		{
			make_json_result($result);
		}
		else 
		{
			make_json_error("操作失败");
		}
	}

	/**
	 *删除栏目
	 */
	public function delete_class()
	{
		admin_priv('power_zsk_class');
		
		$k_class_pid = $this->input->post('k_class_pid');
		if($k_class_pid!=0)
		{
			$k_class_id = $this->input->post('k_class_id');
			$this->load->model('knowledge_model');
			$result = $this->knowledge_model->delete_class($k_class_id);
			make_simple_response($result);
		}
		else
		{
			make_json_error('操作失败，栏目顶级不能删除');
		}
	}

	/**
	 * 获取更多、搜索页面
	 */
	public function get_more_article_page()
	{
		admin_priv();
		
		$k_class_id = $this->input->get('k_class_id');
		if(!empty($k_class_id))
		{
			$this->smarty->assign('_class_id',$k_class_id);
			$this->load->model('knowledge_model');
			$class_info = $this->knowledge_model->get_k_class_info($k_class_id);
			$class_name_one = $class_info['k_class_name'];
		}
		else
		{
			//搜索
			$class_name_one = '搜索结果';
			$search_key = $this->input->get('search_key');
			if(!empty($search_key))
			$this->smarty->assign('search_key',$search_key);

			$k_art_title_advan = $this->input->get('k_art_title_advan');
			if(!empty($k_art_title_advan))
			$this->smarty->assign('k_art_title_advan',$k_art_title_advan);

			$k_class_id_advan = $this->input->get('k_class_id_advan');
			if(!empty($k_class_id_advan))
			$this->smarty->assign('k_class_id_advan',$k_class_id_advan);

			$k_art_hot_advan = $this->input->get('k_art_hot_advan');
			if( $k_art_hot_advan == 1 || $k_art_hot_advan == 0  )
			$this->smarty->assign('k_art_hot_advan',$k_art_hot_advan);

			$k_content_advan = $this->input->get('k_content_advan');
			if(!empty($k_content_advan))
			$this->smarty->assign('k_content_advan',$k_content_advan);
		}
		$this->smarty->assign('class_name_one',$class_name_one);

		//权限：知识库(编辑)
		$power_zsk_update = check_authz("power_zsk_update");
		$this->smarty->assign("power_zsk_update",$power_zsk_update?$power_zsk_update:0);
		//权限：知识库(删除)
		$power_zsk_delete = check_authz("power_zsk_delete");
		$this->smarty->assign("power_zsk_delete",$power_zsk_delete?$power_zsk_delete:0);

		$this->smarty->display('knowledge_more.htm');
	}

	/*获取更多、搜索页面列表*/
	function get_more_art_query()
	{
		admin_priv();
		
		$inarray = $this->input->post();
		$this->load->model('knowledge_model');
		$wheres = $this->knowledge_model->get_knowledge_condition($inarray);

		$where = implode(" AND ",$wheres);

		list($page, $limit, $sort, $order) = get_list_param();
		$responce = $this->knowledge_model->get_more_art_query($page, $limit, $sort, $order,$where);
		$this->load->library('json');
		echo $this->json->encode($responce);
	}

	/**
	 * 获取添加文章页面
	 */
	public function add_article()
	{
		admin_priv('power_zsk_insert');
		
		$this->smarty->display('knowledge_art_edit.htm');
	}

	/**
	 * 添加文章
	 */
	public function insert_article()
	{
		admin_priv('power_zsk_insert',false);
		
		$k_art_title   = $this->input->post("art_title");
		$k_art_content = $this->input->post("art_content");
		$k_class_id    = $this->input->post("art_class");
		$k_art_hot     = $this->input->post("art_hot");

		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->insert_article($k_art_title,$k_art_content,$k_class_id,$k_art_hot);
		make_simple_response($result);
	}

	/*获取修改文章页面*/
	public function edit_article()
	{
		admin_priv('power_zsk_update');
		
		$k_art_id = $this->input->get("k_art_id");
		$this->load->model('knowledge_model');
		$k_art_info = $this->knowledge_model->get_article_info($k_art_id);
		$this->smarty->assign('article_info',$k_art_info);
		$this->smarty->display('knowledge_art_edit.htm');
	}

	/*修改文章*/
	public function update_article()
	{
		admin_priv('power_zsk_update',false);
		
		$k_art_id      = $this->input->post("art_id");
		$k_art_title   = $this->input->post("art_title");
		$k_art_content = $this->input->post("art_content");
		$k_class_id    = $this->input->post("art_class");
		$k_art_hot     = $this->input->post("art_hot");

		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->update_article($k_art_id,$k_art_title,$k_art_content,$k_class_id,$k_art_hot);
		make_simple_response($result);
	}

	/*查看文章*/
	public function show_article_detail()
	{
		admin_priv();
		
		$k_art_id = $this->input->get('k_art_id');
		$this->load->model('knowledge_model');
		$k_art_info = $this->knowledge_model->get_article_info($k_art_id);
		$this->smarty->assign('k_art_info',$k_art_info);
		//权限：知识库(编辑)
		$power_zsk_update = check_authz("power_zsk_update");
		$this->smarty->assign("power_zsk_update",$power_zsk_update?$power_zsk_update:0);
		//权限：知识库(删除)
		$power_zsk_delete = check_authz("power_zsk_delete");
		$this->smarty->assign("power_zsk_delete",$power_zsk_delete?$power_zsk_delete:0);

		$this->smarty->display('knowledge_article.htm');
	}

	/*获取热点文章*/
	function get_hot_info()
	{
		admin_priv();
		
		$this->load->model('knowledge_model');
		$hot_info = $this->knowledge_model->get_hot_info();

		return $hot_info;
	}

	/*删除文章*/
	function remove_article()
	{
		admin_priv('power_zsk_delete');
		
		$k_art_ids = $this->input->post('k_art_ids');
		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->delete_article($k_art_ids);
		make_simple_response($result);
	}

	/**
	 * 获取栏目本身及父类名称
	 */
	public function get_class_name()
	{
		admin_priv();
		
		$k_class_name = '';
		$k_class_id = $this->input->post('k_class_id');
		$this->load->model('knowledge_model');
		$result = $this->knowledge_model->get_class_name($k_class_id);
		if($result)
		{
			foreach($result AS $class)
			{
				$k_class_name .= "<a href='###' onclick=art_more(".$class['k_class_id'].",'".$class['k_class_name']."')>".$class['k_class_name']."</a> > ";
			}
		}
		make_simple_response($k_class_name);
	}

    /**
     * ckediter图片上传
     */
    public function upload()
    {
        $this->config->load('myconfig');
        $knowledge_path  = $this->config->item('knowledge_path');
        $filepath = $knowledge_path . $this->session->userdata('vcc_id')."/";

        if(!is_dir($filepath))
        {
            @mkdir($filepath);
            @chmod($filepath,0777);
        }

        $config['upload_path'] = $filepath;
        $config['allowed_types'] = 'gif|jpg|png';

        $config['remove_spaces'] = true;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        /* 判断是否存在上传文件 */
        if($_FILES['upload']['name'])
        {
            $this->upload->do_upload('upload');
            $udata = $this->upload->data();
            if($this->upload->display_errors())
            {
                echo $this->upload->display_errors();
            }
            else#上传成功
            {
                $callback = $_REQUEST["CKEditorFuncNum"];
                echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$filepath . $udata['file_name']."','');</script>";
            }
        }
    }
}
