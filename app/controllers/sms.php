<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CI 短信管理
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : sms.php 
 * $Author: yhx
 * $time  : Wed Jul 18 15:48:38 CST 2012
*/
class Sms extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 得到列表的快捷搜索
	 *
	 * @param int $stand
	 */
	private function get_condition($stand)
	{
		$sql_sms = "";
		switch ($stand) {
			case 1:{//全部数据
				$sql_sms = "sms_id > 0";
				break;
			}
			case 2:{//今月
				$sql_sms = "sms_send_time >= '".strtotime(date("Y-m-01 00:00:00"))."' AND sms_send_time <= '".strtotime(date("Y-m-t 23:59:59"))."'";
				break;
			}
			case 3:{//今周
				$week = date("w");   //周几
				$sql_sms = "sms_send_time >= '".strtotime("-$week days")."' AND sms_send_time <= '".strtotime(date("Y-m-d 23:59:59"))."'";
				break;
			}
			case 4:{//今日
				$sql_sms = "sms_send_time >= '".strtotime(date("Y-m-d 00:00:00"))."' AND sms_send_time <= '".strtotime(date("Y-m-d 23:59:59"))."'";
				break;
			}
			case 5:{//成功
				$sql_sms = "sms_result = 1";
				break;
			}
			case 6:{//失败
				$sql_sms = "sms_result = 2";
				break;
			}
			default:
				break;
		}
		return $sql_sms;
	}

	/**  短信管理-列表
	 * sms::index()
	 * 
	 * @return void 
	 */
	public function index()
	{
		admin_priv('smsrecords');

		$this->smarty->assign("MAX_smsLength",MAXSMSLENGTH);//最大短信字符数
		$this->smarty->assign("EACH_smsLength",EACHSMSLENGTH);//每条短信的字符数

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$this->smarty->display("sms_list.htm");
	}

	/**
	 * 短信管理 - 列表 - 获取列表数据
	 *
	 */
	public function list_sms_query()
	{
		admin_priv('smsrecords',false);

		$inarray = $this->input->post();
		$wheres = array();
		//数据权限
		$role_condition = data_permission();
		if ($role_condition)
		{
			$wheres[] = $role_condition;
		}
		if (!empty($inarray["receiver_phone"]))
		{//短信接收号码
			$wheres[] = "receiver_phone LIKE '%".$inarray["receiver_phone"]."%'";
		}
		if (!empty($inarray["send_start_time"]))
		{//发送时间-开始
			$wheres[] = "sms_send_time >= '".strtotime($inarray["send_start_time"])."'";
		}
		if (!empty($inarray["send_end_time"]))
		{//发送时间-结束
			$wheres[] = "sms_send_time <= '".strtotime($inarray["send_end_time"])."'";
		}
		if (!empty($inarray["sql_type_st"]))
		{//数据范围
			$wheres[] = $this->get_condition($inarray["sql_type_st"]);
		}
		if (!empty($inarray["sql_type_ss"]))
		{//5成功  6失败
			$wheres[] = $this->get_condition($inarray["sql_type_ss"]);
		}
		$wheres = array_unique($wheres);
		$where = implode(" AND ",$wheres);

		list($page, $limit, $sort, $order) = get_list_param();
		$this->load->model("sms_model");
		$responce = $this->sms_model->get_sms_list($page, $limit, $sort, $order,$where);
		$this->load->library("json");
		echo $this->json->encode($responce);
	}

	/**
	 * 短信管理 - 列表 - 查看短信内容
	 *
	 */
	public function view_smscontent()
	{
		admin_priv();

		$sms_id = $this->input->post("sms_id");
		if ($sms_id > 0)
		{
			$this->load->model("sms_model");
			$sms_info = $this->sms_model->get_sms_info($sms_id);
			make_json_result($sms_info["sms_contents"]);
		}
		else
		{
			make_json_error("缺少短信相关参数！");
		}
	}

	/**
	 * 短信模板 - 列表
	 *
	 */
	public function list_smsmodel()
	{
		admin_priv('smsmodelmanage');

		$this->smarty->display("smsmodel_list.htm");
	}

	/**
	 * 短信模板 - 列表 - 获取列表数据
	 *
	 */
	public function list_smsmodel_query()
	{
		admin_priv('smsmodelmanage',false);

		$search_theme = $this->input->post('search_theme');
		$wheres = array();
		if (!empty($search_theme))
		{
			$wheres[] = "theme LIKE '%".$search_theme."%'";
		}
		$where = implode(",",$wheres);

		list($page, $limit, $sort, $order) = get_list_param();
		$this->load->model("sms_model");
		$responce = $this->sms_model->get_smsmodel_list($page, $limit, $sort, $order,$where);
		$this->load->library("json");
		echo $this->json->encode($responce);
	}

	/**
	 * 短信模板  - 添加
	 *
	 */
	public function add_smsmodel()
	{
		admin_priv();

		$this->smarty->assign("MAX_smsLength",MAXSMSLENGTH);//最大短信字符数
		$this->smarty->assign("EACH_smsLength",EACHSMSLENGTH);//每条短信的字符数
		$this->smarty->assign("MAX_tiaoshu",ceil(MAXSMSLENGTH/EACHSMSLENGTH));//最大短信条数
		$form_act = "insert_smsmodel";//添加模板
		$this->smarty->assign("form_act",$form_act);
		$this->smarty->display("smsmodel_info.htm");
	}

	/**
	 * 短信模板  - 编辑
	 *
	 */
	public function edit_smsmodel()
	{
		admin_priv();
		//获取模板信息
		$mod_id   = $this->input->get("mod_id");
		$this->load->model("sms_model");
		$smsmodel_info = $this->sms_model->get_smsmodel_info($mod_id);
		if($smsmodel_info)
		{
			$this->smarty->assign("smsmodel_info",$smsmodel_info);
		}

		$this->smarty->assign("MAX_smsLength",MAXSMSLENGTH);//最大短信字符数
		$this->smarty->assign("EACH_smsLength",EACHSMSLENGTH);//每条短信的字符数
		$this->smarty->assign("MAX_tiaoshu",ceil(MAXSMSLENGTH/EACHSMSLENGTH));//最大短信条数

		$form_act = "update_smsmodel";//编辑模板
		$this->smarty->assign("form_act",$form_act);
		$this->smarty->display("smsmodel_info.htm");
	}

	/**
	 * 短信模板 - 添加新短信模板
	 *
	 */
	public function insert_smsmodel()
	{
		admin_priv();

		$theme = $this->input->post('theme');
		$content = $this->input->post('content');
		$this->load->model("sms_model");
		$result = $this->sms_model->insert_smsmodel($theme,$content);
		make_simple_response($result);
	}

	/**
	 * 短信模板 - 编辑模板信息
	 *
	 */
	public function update_smsmodel()
	{
		admin_priv();

		$mod_id = $this->input->post('mod_id');
		$theme = $this->input->post('theme');
		$content = $this->input->post('content');
		$this->load->model("sms_model");
		$result = $this->sms_model->update_smsmodel($mod_id,$theme,$content);
		make_simple_response($result);
	}

	/**
	 * 短信模板 - 删除系统中的短信模板
	 *
	 */
	public function remove_smsmodel()
	{
		admin_priv();

		$mod_id = $this->input->post("mod_id");
		$mod_id_array = explode(',',$mod_id);
		$this->load->model("sms_model");
		$result = $this->sms_model->delete_smsmodel($mod_id_array);
		make_simple_response($result);
	}

	/**
	 * 发短信 - 发短信页面
	 *
	 */
	public function send_sms()
	{
		admin_priv();

		$power_sendsms = check_authz("sendsms");
		if(!$power_sendsms)
		{
			$links[0]['text'] = '';
			$links[0]['href'] = '#';
			sys_msg('您没有发短息的权限。',0,$links,false);
		}

		$this->smarty->assign("MAX_smsLength",MAXSMSLENGTH);//最大短信字符数
		$this->smarty->assign("EACH_smsLength",EACHSMSLENGTH);//每条短信的字符数
		$this->smarty->assign("MAX_tiaoshu",ceil(MAXSMSLENGTH/EACHSMSLENGTH));//最大短信条数

		//权限：客户电话（显示）
		$power_phone_view = check_authz("power_phone");
		$this->smarty->assign("power_phone_view",$power_phone_view?$power_phone_view:0);

		$group_sms = $this->input->get('group_sms');
		if(check_authz("massmessage") && $group_sms)
		{
			$group_sms = 1;
		}
		else
		{
			$group_sms = 0;
		}
		$this->smarty->assign("group_sms",$group_sms);
		$receiver_phone = $this->input->get('receiver_phone');
		$this->smarty->assign("receiver_phone",$receiver_phone);
		$this->smarty->display("sms_info.htm");
	}

	/**
	 * 群发短信 - 模板
	 *
	 */
	public function get_smsmodel()
	{
		admin_priv();

		$theme = $this->input->post("q");
		$this->load->model("sms_model");
		$sms_model = $this->sms_model->get_all_smsmodel($theme);
		$this->load->library("json");
		echo $this->json->encode($sms_model);
	}

	/**
	 * 群发短信 - 逗号分隔号码
	 */
	public function send_sms_by_comma()
	{
		admin_priv();

		if (!check_authz("massmessage"))
		{
			make_json_error('没有发送短信的权限');
		}

		$this->load->model("sms_model");
		$receiver_phone = $this->input->post('_group_phone');
		$sms_contents = $this->input->post('_sms_content');
		$receiver_phone = str_replace("，",",",str_replace("/","",str_replace(".","",str_replace(" ","",trim($receiver_phone)))));
		$receiver_phone = explode(",",$receiver_phone);
		$this->sms_model->send_sms_batch($receiver_phone,$sms_contents);
		make_json_result('发送完成');
	}

	/**
	 * 群发短信  - 文件上传
	 */
	public function send_sms_by_file()
	{
		admin_priv();

		$error = '';
		$msg = '';

		//上传附件
		$allowed_types = 'csv|txt';

        $this->load->config('myconfig');
		$filepath = $this->config->item('upload_path').'sms_phone/';
		if(!is_dir($filepath))
		{
			@mkdir($filepath);
			@chmod($filepath,0777);
		}

		$config['upload_path']   = $filepath;   //上传路径
		$config['allowed_types'] = $allowed_types;  //允许类型
		$config['encrypt_name']  = TRUE;  //重命名
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if($_FILES['_file_address']['name'])
		{
			if(!$this->upload->do_upload('_file_address'))
			{
				$error = $this->upload->display_errors();
			}
			else
			{
				//上传成功
				$udata = $this->upload->data();

				$data_file = $filepath.$udata['file_name'];
                $receiver_phone = '';
				if ($data_file)
				{
					$receiver_phone = file_get_contents($data_file);
					$receiver_phone = str_replace("\xa3\xac",',',$receiver_phone);
					$receiver_phone = str_replace("，",',',$receiver_phone);
					$receiver_phone = str_replace("\r\n",',',$receiver_phone);
				}

				$receiver_phone = explode(",",$receiver_phone);
				if (count($receiver_phone) == 0)
				{
					return FALSE;
				}

				$this->load->model("sms_model");
				$sms_contents = $this->input->post('sms_content');
				//发信息
				$this->sms_model->send_sms_batch($receiver_phone,$sms_contents);
				$msg .= '发送完成';
			}
		}
		echo "{'error': '". $error . "','msg': '" . $msg . "'}";
	}

	/**
	 * 发短信  -  单发
	 *
	 */
	public function send_sms_single()
	{
		admin_priv();

		$sms_contents = $this->input->post('_sms_content');
		$receiver_phone = $this->input->post('_single_phone');

		$this->load->model("sms_model");
		$result = $this->sms_model->send_sms($receiver_phone,$sms_contents);
        if(isset($result['code']) && $result['code'] == 200 )
        {
            make_json_result();
        }
        else
        {
            make_json_error($result['message']);
        }
	}

	/**
	 * 短信重发
	 *
	 */
	public function resend_sms()
	{
		admin_priv();

		$sms_id = $this->input->post('sms_id');
		//得到需要重发的短信
		$this->load->model("sms_model");
		$sms_info = $this->sms_model->get_sms_info($sms_id);
		$receiver_phone = $sms_info['receiver_phone'];
		$sms_content = $sms_info['sms_contents'];

		$result = $this->sms_model->send_sms($receiver_phone,$sms_content,0,2);
		if(isset($result['code']) && $result['code'] == 200 )
		{
			make_json_result();
		}
		else
		{
            make_json_error($result['message']);
		}
	}

	/**
     * 接收短信结果，进行实时更新
     */
	public function get_sendsms_result()
	{
		$this->load->model('sms_model');
		$this->sms_model->get_sentsms_result();
	}
}