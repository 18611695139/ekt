<?php
/**
 * 批量数据导入类
 *
 */
class Data_import_model extends CI_Model
{
	//进度临时文件
	private $_progress_file = '';

	function __construct()
	{
		parent::__construct();
	}

	/**
     * 设置导入进度
     * @param string $value
     * @param string $key 缓存的键
     */
	public function _set_progress($value, $key = '')
	{
		$key = empty($key) ? $this->_progress_file : $key;
		$this->cache->save($key, $value);
		//file_put_contents($this->_progress_file,$process_str);
	}

	/**
	 * 导入客户数据
	 *
	 *  @param int    $model_id         模板ID
	 *  @param string $source_file      原始数据文件路径
	 *  @param string $temp_file        临时文件路径
	 *  @param string $cle_info_source  默认信息来源
	 *  @param bool    $shuffle       是否打乱数据顺序
	 *  @param bool    $filter_cle_name  是否过滤重复姓名
	 *  @param bool    $filter_cle_phone 是否过滤客户电话号码
	 *  @param int    $data_owner       数据所属人: DATA_DEPARTMENT 部门  DATA_PERSON 个人
	 * <pre>
	 * DATA_DEPARTMENT 部门
	 * DATA_PERSON 个人
	 * </pre>
	 *  @return bool
	 * 
	 *  @author zgx
	 */
	public function import_client($model_id=0,$source_file="",$temp_file="",$cle_info_source="",$shuffle=false,$filter_cle_name=false,$filter_cle_phone=false,$data_owner=DATA_DEPARTMENT)
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		session_write_close();

		$cle_stat           = "未拨打";//状态
		$total              = 0;//数据总数
		$step               = 100;//以100为单位导入数据  最大为100 不能再大
		$impt_count	        = 1;//单次导入的计数
		$impt_total	        = 0;//总共需要导入的数据
		$impt_success	    = 0;//最终成功数
		$impt_fail	        = 0;//导入失败数
		$impt_id	        = 0;//导入批次号
		$data_error         = array();//被过滤的数据
		$client_field       = array();//客户字段
		$contact_field      = array();//联系人字段
		$model_mark         = array();//模板字段名称
		$require_field      = array();//客户必填项
		$user_info          = array();//全部员工信息  [user_name] => cle_id,dept_id
		$field_poz_name     = -1;//客户姓名的位置
		$field_poz_phone    = -1;//客户电话位置
		$field_poz_info_source = -1;//信息来源位置
		$field_poz_user_id  = -1;//客户数据所属人
		$field_poz_province  = -1;//所属省
		$field_poz_city  = -1;//所属市
		$now_int            = time();
		$now_date           = date("Y-m-d");
		$now_datetime       = date("Y-m-d H:i:s");
		$user_id    		= $this->session->userdata('user_id');
		$user_name 			= $this->session->userdata('user_name');
		$dept_id     		= $this->session->userdata('dept_id');

		$this->_progress_file = $temp_file;

		$this->_set_progress("开始读取数据......");
		$this->load->library('Csv');
		$data = $this->csv->readcsv($source_file);
		$this->_set_progress("读取数据结束");

		$this->load->model("phone_location_model");
		//得到模板字段
		$this->load->model("model_model");
		$model_info = $this->model_model->get_model_info($model_id,MODEL_CLIENT_IMPT);
		/**详细字段整理**/
		$model_detail = "";
		if (!empty($model_info["model_detail"]))
		{
			$model_detail = $this->model_model->get_model_detail_info($model_info["model_detail"],FIELD_TYPE_CLIENT_CONTACT,true);
		}

		foreach ($model_detail AS $key => $detail)
		{
			//模板字段名称
			$model_mark[]  = $detail["name"];
			if ( $detail["field_type"] == FIELD_TYPE_CONTACT )
			{
				//联系人字段
				$contact_field[] = array("field"=>$detail["fields"],"stand"=>$key);
			}
			else
			{
				//客户字段
				$client_field[]  = array("field"=>$detail["fields"],"stand"=>$key);
			}
		}

		/***导入文件的表头（字段名）**/
		$source_header = $data[0];
		//将字符串转换成小写
		$model_mark = $this->get_strtolower($model_mark);
		$source_header = $this->get_strtolower($source_header);
		if ( array_diff($model_mark,$source_header) || array_diff($source_header,$model_mark) )
		{
			$this->_set_progress("导入异常#字段与模板不匹配");
			return FALSE;
		}

		//去掉第一行数据（字段名）
		unset($data[0]);

		//得到数组的长度开始导入数据
		$total = count($data);
		if($total == 0)
		{
			$this->_set_progress("导入异常#没有数据");
			return FALSE;
		}
		
		//如果是随机导入，打乱顺序
		if($shuffle)
		{
			shuffle($data);
		}

		/***生成导入日志  得到导入批次号$impt_id**/
		$impt_log_data = array(
		'impt_total' => $total,
		'impt_state' => 0,
		'impt_type' => 1,
		'impt_time' => $now_int,
		'user_id' => $user_id,//操作人id
		'dept_id' => $dept_id,
		'user_name' => $user_name
		);
		$this->db_write->insert('est_import_log',$impt_log_data);
		$impt_id = $this->db_write->insert_id();
		$this->_set_progress("开始导入......#0#$total");

		/* 取得客户字段中关键字段所在位置 */
		foreach($client_field as $field_poz=>$field)
		{
			switch ($field["field"]) {
				case "cle_name":{
					$field_poz_name = $field_poz;
					break;
				}
				case "cle_phone":{
					$field_poz_phone = $field_poz;
					break;
				}
				case "cle_info_source":{
					$field_poz_info_source = $field_poz;
					break;
				}
				case "user_id":{
					$field_poz_user_id = $field_poz;//数据所属人
					break;
				}
				case "cle_province_name":{
					$field_poz_province = $field_poz;//所属省
					break;
				}
				case "cle_city_name":{
					$field_poz_city = $field_poz;//所属市
					break;
				}
				default:
					break;
			}
		}

		//判断必填字段是否已经导入:下次联系时间
		$require_field = array('cle_stat'=>$cle_stat,'cle_creat_time'=>$now_date,'cle_creat_user_id'=>$user_id,'impt_id'=>$impt_id);
		if($field_poz_info_source == -1)
		{
			$require_field["cle_info_source"] = $cle_info_source;//信息来源
		}

		//是否导入数据所属人
		if ($field_poz_user_id == -1)
		{
			switch($data_owner){
				case DATA_DEPARTMENT:{//部门公共数据
					$require_field['dept_id'] = $dept_id;
					$require_field['user_id'] = 0;
					break;
				}
				case DATA_PERSON:{//个人数据
					$require_field['user_id'] = $user_id;
					$require_field['dept_id'] = $dept_id;
					$require_field['cle_executor_time'] = $now_datetime;//分配或占有时间
					break;
				}
				default :
					break;
			}
		}
		else
		{
			//导入数据所属人
			$this->load->model("user_model");
			$user_result = $this->user_model->get_all_users();
			foreach ($user_result AS $key => $value)
			{
				if ($value["user_name"])
				{
					$user_info[$value["user_name"]] = array("user_id" =>$value["user_id"],"dept_id"=>$value["dept_id"] );
				}
			}
		}

		//1.过滤导入数据中 姓名 或 电话 重复 2.导入省跟市时获取相应id
		if($filter_cle_phone||$filter_cle_name||$field_poz_province||$field_poz_city)
		{
			$repeat_cle_name_data = array();//存储不重复的姓名
			$repeat_cle_phone_data = array();//存储不重复的电话
			$repeat_key = array();//数据中（电话或姓名）重复的键位

			$province_info		= array();//导入省时用于存储相应id
			$city_info			= array();//导入市时用于存储相应id
			$province_names		= array();
			$city_names		= array();

			foreach($data as $k => $input_data)
			{
				//客户名称
				if (!empty($input_data[$field_poz_name]) && $filter_cle_name && $field_poz_name >= 0 )
				{
					if(in_array($input_data[$field_poz_name],$repeat_cle_name_data))
					{
						if(!in_array($k,$repeat_key))
						{
							$repeat_key[] = $k;
						}
					}
					else
					{
						$repeat_cle_name_data[] = $input_data[$field_poz_name];
					}
				}
				//客户电话
				if (!empty($input_data[$field_poz_phone])&&$filter_cle_phone && $field_poz_phone >= 0 )
				{
					if(in_array($input_data[$field_poz_phone],$repeat_cle_phone_data))
					{
						if(!in_array($k,$repeat_key))
						{
							$repeat_key[] = $k;
						}
					}
					else
					{
						$repeat_cle_phone_data[] = $input_data[$field_poz_phone];
					}
				}

				//省
				if(!empty($input_data[$field_poz_province]) && $field_poz_province >= 0 )
				{
					$province_names[] = $input_data[$field_poz_province];
				}
				//市
				if(!empty($input_data[$field_poz_city]) && $field_poz_city >= 0 )
				{
					$city_names[] = $input_data[$field_poz_city];
				}
			}

			//所有省id
			if(!empty($province_names))
			{
				$this->load->model('regions_model');
				$province_info = $this->regions_model->get_region_id_by_name($province_names,REGION_PROVINCE);
			}
			//所有市id
			if(!empty($city_names))
			{
				$this->load->model('regions_model');
				$city_info = $this->regions_model->get_region_id_by_name($city_names,REGION_CITY);
			}
		}

		//开始逐条导入数据整理、导入
		foreach($data as $key_item=>$data_item)
		{
			//空则不处理
			if(empty($data_item))
			{
				continue;
			}
			//过滤特殊字符，回车、换行
			if($impt_count < $step)
			{
				$impt_count++;
			}
			else
			{
				$impt_total += $impt_count;
				$impt_count = 1;
				$this->_set_progress("导入数据......#$impt_total#$total");
			}

			//客户数据
			$impt_client_data    = $require_field; //$require_field客户必填字段
			//联系人数据
			$import_contact_data = array();

			//客户数据
			foreach ($client_field AS $key => $info)
			{
				if (empty($data_item[$info["stand"]]))
				{
					$impt_client_data[$info["field"]] = '';
				}
				else
				{
					//导入数据所属人
					if ($field_poz_user_id >= 0 && $info["field"] == 'user_id')
					{
						$user_name = $data_item[$info["stand"]];
						//个人数据根据员工姓名获取user_id
						$impt_client_data['user_id'] = empty($user_info[$data_item[$info["stand"]]]["user_id"]) ?
						0 : $user_info[$data_item[$info["stand"]]]["user_id"];
						//相应部门id,为空数据部门为当前操作部门
						$impt_client_data['dept_id'] = empty($user_info[$data_item[$info["stand"]]]["dept_id"]) ?
						$dept_id : $user_info[$data_item[$info["stand"]]]["dept_id"];
						//占用或分配时间
						$impt_client_data['cle_executor_time'] = $now_datetime;
					}
					//导入省
					elseif($field_poz_province >= 0 && $info["field"] == 'cle_province_name')
					{
						//省名称
						$impt_client_data[$info["field"]] = empty($province_info[$data_item[$info["stand"]]]) ?
						'' : $data_item[$info["stand"]];
						$impt_client_data['cle_province_id'] = empty($impt_client_data[$info["field"]]) ?
						0 : $province_info[$data_item[$info["stand"]]];
					}
					//导入市
					elseif($field_poz_city >= 0 && $info["field"] == 'cle_city_name')
					{
						//市名称
						$impt_client_data[$info["field"]] = empty($city_info[$data_item[$info["stand"]]]) ?
						'' : $data_item[$info["stand"]];
						$impt_client_data['cle_city_id'] = empty($impt_client_data[$info["field"]]) ?
						0 : $city_info[$data_item[$info["stand"]]];
					}
					//其他客户导入数据
					else
					{
						$impt_client_data[$info["field"]] = $data_item[$info["stand"]];
					}
				}
			}
			//联系人数据
			foreach ($contact_field AS $key => $info)
			{
				if (empty($data_item[$info["stand"]]))
				{
					$import_contact_data[$info["field"]] = '';
				}
				else
				{
					$import_contact_data[$info["field"]] = $data_item[$info["stand"]];
				}
			}
			//如果有 联系人电话 处理
			if (isset($import_contact_data["con_mobile"]) && !empty($import_contact_data["con_mobile"]))
			{
				//号码处理  有0开头的手机号，去0
				$import_contact_data["con_mobile"] = $this->phone_location_model->remove_prefix_zero($import_contact_data["con_mobile"]);
			}
			//处理客户电话号码
			if ($field_poz_phone >= 0 )
			{
				$temp_phone = trim($impt_client_data['cle_phone']);

				if ($temp_phone)
				{
					//号码处理  有0开头的手机号，去0
					$impt_client_data['cle_phone'] = $this->phone_location_model->remove_prefix_zero($temp_phone);
				}
			}
			//检查客户姓名拼音
			if (!empty($impt_client_data["cle_name"]))
			{
				$impt_client_data["cle_name"] = replace_illegal_string($impt_client_data["cle_name"]);
				$impt_client_data["cle_pingyin"] = ctype_alnum($impt_client_data["cle_name"]) ?
				$impt_client_data["cle_name"] : pinyin($impt_client_data["cle_name"],TRUE);
			}
			else
			{
				$impt_client_data["cle_pingyin"] = "";
			}

			//判断是否超过客户限制
			if(!empty($impt_client_data['user_id']))
			{
				if (($field_poz_user_id == -1 && $data_owner == DATA_PERSON) || ($field_poz_user_id >= 0))
				{
					$this->load->model('client_model');
					$check_amount_result = $this->client_model->check_client_amount($impt_client_data['user_id']);
					if(!$check_amount_result)
					{
						$impt_client_data['impt_error_remark'] = $user_name."的客户数量已达客户限制数";
						$data_error[] = $impt_client_data;
						continue;
					}
				}
			}

			//过滤姓名、电话（导入数据重复、跟数据库数据重复）
			if($filter_cle_phone||$filter_cle_name)
			{
				if(!empty($repeat_key))
				{
					if(in_array($key_item,$repeat_key))
					{
						$impt_client_data['impt_error_remark'] = "[客户电话]或[客户名称]与本次导入数据重复";
						$data_error[] = $impt_client_data;
						continue;
					}
				}
				
				/*$result_str = array();
				$wheres = array();
				//客户名称
				if (!empty($impt_client_data['cle_name'])&&$filter_cle_name && $field_poz_name >= 0 )
				{
					$result_str[] = "[客户姓名]";
					$wheres[] = "cle_name = '".$impt_client_data['cle_name']."'";
				}
				//客户电话
				if (!empty($impt_client_data['cle_phone'])&&$filter_cle_phone && $field_poz_phone >= 0 )
				{
					$result_str[] = "[客户电话]";
					$wheres[] = "cle_phone = '".$impt_client_data['cle_phone']."'";
				}
				$where = implode(' OR ',$wheres);
				if (!empty($where))
				{
					$total_error = 0;
					$this->db_read->where($where);
					$this->db_read->select('count(*) as total',FALSE);
					$total_error_query = $this->db_read->get('est_client');
					$total_error = $total_error_query->row()->total;
					if($total_error>0)
					{
						$result_string = implode('或',$result_str);
						$impt_client_data['impt_error_remark'] = $result_string."与系统中已有数据重复";
						$data_error[] = $impt_client_data;
						continue;
					}
				}*/
				
				$filter_name_phone = array();
				//客户名称
				if (!empty($impt_client_data['cle_name'])&&$filter_cle_name && $field_poz_name >= 0 )
				{
					$filter_name_phone['cle_name'] = $impt_client_data['cle_name'];
				}
				//客户电话
				if (!empty($impt_client_data['cle_phone'])&&$filter_cle_phone && $field_poz_phone >= 0 )
				{
					$filter_name_phone['cle_phone'] = $impt_client_data['cle_phone'];
				}
				if(!empty($filter_name_phone))
				{
					$impt_error_remark = $this->filter_client_data($filter_name_phone);
					if(!empty($impt_error_remark))
					{
						$impt_client_data['impt_error_remark'] = $impt_error_remark;
						$data_error[] = $impt_client_data;
						continue;
					}
				}
				
			}

			//数据插入数据库
			if($impt_client_data['user_id']==0)
			{
				$impt_client_data['cle_public_type'] = 3;
			}
			$this->db_write->insert("est_client",$impt_client_data);
			$insert_cle_id = $this->db_write->insert_id();
			if ( $insert_cle_id )
			{
				//联系人信息
				$contact_content = implode('',$import_contact_data);//判断联系人中的内容是否为空
				if (!empty($contact_content))
				{
					$import_contact_data["cle_id"]      = $insert_cle_id;
					$import_contact_data["con_if_main"] = 1;
					$this->db_write->insert("est_contact",$import_contact_data);
				}
			}
			$impt_success ++;
		}

		$impt_fail = $total - $impt_success;
		$this->db_write->update('est_import_log',array('impt_success'=>$impt_success,'impt_fail'=>$impt_fail,'impt_state'=>1),array('impt_id'=>$impt_id));
		//将错误数据进入错误日志表
		if( ! empty($data_error))
		{
			$this->db_write->insert_batch('est_import_error_data',$data_error);
		}
		usleep(200);
		$this->_set_progress("导入结束#$impt_success#$total#$impt_fail#$impt_id");
		return TRUE;
	}

	/**
     * 过滤客户数据
     *
     * @param boolean $filter_cle_phone 是否过滤客户电话号码
     * @param boolean $filter_cle_name 是否过滤客户姓名
     * @param int $field_poz_name 姓名字段在模板中的位置，表明是否需要过滤
     * @param int $field_poz_phone 号码字段在模板中的位置，表明是否需要过滤
     * @param string $phone 号码
     */
	public function filter_client_data($filter_name_phone = array())
	{
		//$this->load->library('elasticsearch');
		$impt_error = '';
		$impt_error_remark = array();
		//客户电话
		if (!empty($filter_name_phone['cle_phone']))
		{
			$query = $this->db_read->query("SELECT cle_id FROM est_client WHERE cle_phone='".$filter_name_phone['cle_phone']."'");
			$cle_ids = array();
			foreach($query->result_array() as $value)
			{
				$cle_ids[] = $value['cle_id'];
			}
			if(count($cle_ids)>0)
			{
				//判断是否启用历史信息
				$this->load->model('client_history_model');
				$move_to_history_cle_id = $this->client_history_model->move_client_to_history($cle_ids);
				if(!empty($move_to_history_cle_id))
				{
					$cle_ids = array_diff($cle_ids,$move_to_history_cle_id);
				}
				if(!empty($cle_ids))
				{
					$impt_error_remark[] = "[客户电话]与系统中已有数据重复";
				}
			}
		}
		//客户名称
		if (!empty($filter_name_phone['cle_name']))
		{
			$this->db_read->where("cle_name = '".$filter_name_phone['cle_name']."'");
			$this->db_read->select('count(*) as total',FALSE);
			$total_error_query = $this->db_read->get('est_client');
			$total_error = $total_error_query->row()->total;
			if($total_error>0)
			{
				$impt_error_remark[] = "[客户姓名]与系统中已有数据重复";
			}
		}
		
		if(!empty($impt_error_remark))
		{
			$impt_error = implode(',',$impt_error_remark);
		}
		return $impt_error;
	}

	/**
	 * 导入产品
	 *
	 * @param int $model_id	模板id
	 * @param string $source_file	数据源路径
	 * @param string $temp_file	临时文件路劲
	 * @param bool $shuffle 是否打乱数据顺序
	 * @param bool $filter_product_name 是否过滤重复产品名称
	 * @param int $product_state	产品状态
	 * @param int $product_class_id 产品类别id
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function import_product($model_id=0,$source_file='',$temp_file='',$shuffle=false,$filter_product_name=false,$product_state=0,$product_class_id=0)
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		session_write_close();

		$total              = 0;//数据总数
		$step               = 100;//以100为单位导入数据  最大为100 不能再大
		$impt_count	        = 1;//单次导入的计数
		$impt_total	        = 0;//总共需要导入的数据
		$impt_success	    = 0;//最终成功数
		$impt_fail	        = 0;//导入失败数
		$impt_id	        = 0;//导入批次号
		$data_error         = array();//被过滤的数据
		$product_field       = array();//产品字段
		$model_mark         = array();//模板字段名称
		$database_product_name  = array();
		$field_poz_name     = -1;//产品名称的位置
		$field_poz_number   = -1;//产品编号位置
		$now_int            = time();
		$import_user_id     = $this->session->userdata('user_id');
		$import_user_name   = $this->session->userdata('user_name');
		$import_dept_id     = $this->session->userdata('dept_id');
		$impt_product_data_array = array();//存储要可以插入数据库的产品信息

		$this->_progress_file = $temp_file;
		$this->_set_progress("开始读取数据......");
		$this->load->library('Csv');
		$data = $this->csv->readcsv($source_file);
		$this->_set_progress("读取数据结束");

		//得到模板字段
		$this->load->model("model_model");
		$model_info = $this->model_model->get_model_info($model_id,MODEL_PRODUCT_IMPT);//模板类型 3 产品导入
		//详细字段
		$model_detail = "";
		if ( !empty($model_info["model_detail"]) )
		{
			$model_detail = $this->model_model->get_model_detail_info($model_info["model_detail"],FIELD_TYPE_PRODUCT,true);
		}
		foreach ($model_detail AS $key => $detail)
		{
			$model_mark[]  = $detail["name"];//字段名称
			$product_field[] = array("field"=>$detail["fields"],"stand"=>$key);
		}

		$source_header = $data[0]; //导入文件的表头（字段名）
		//将字符串转换成小写
		$model_mark = $this->get_strtolower($model_mark);
		$source_header = $this->get_strtolower($source_header);
		if ( array_diff($model_mark,$source_header) || array_diff($source_header,$model_mark) )
		{
			$this->_set_progress("导入异常#字段与模板不匹配");
			return FALSE;
		}
		//去掉第一行数据（字段名）
		unset($data[0]);
		//得到数组的长度开始导入数据
		$total = count($data);
		if($total == 0)
		{
			$this->_set_progress("导入异常#没有数据");
			return FALSE;
		}
		
		//如果是随机导入，打乱顺序
		if($shuffle)
		{
			shuffle($data);
		}
		
		//生成导入日志
		$impt_log_data = array(
		'impt_total'     => $total,
		'impt_state'     => 0,
		'impt_type'     => 2,
		'impt_time'      => $now_int,
		'user_id'   => $import_user_id,
		'dept_id'   => $import_dept_id,
		'user_name' => $import_user_name
		);
		$this->db_write->insert('est_import_log',$impt_log_data);
		$impt_id = $this->db_write->insert_id();
		$this->_set_progress("开始导入......#0#$total");

		/* 三、取得产品字段中关键字段所在位置 */
		foreach($product_field as $field_poz=>$field)
		{
			/* 取得字段所在位置 */
			switch ($field["field"]) {
				case "product_name":{
					$field_poz_name = $field_poz;
					break;
				}
				case "product_num":{
					$field_poz_number = $field_poz;
					break;
				}
				default:
					break;
			}
		}
		//5.过滤导入数据中产品名称重复
		if( $filter_product_name && $field_poz_name >= 0 )
		{
			$repeat_product_name_data = array();//存储不重复的产品名称
			$repeat_key = array();//数据中（产品名称）重复的键位

			foreach($data as $k=>$input_data)
			{
				//产品名称
				if (!empty($input_data[$field_poz_name]) )
				{
					if(in_array($input_data[$field_poz_name],$repeat_product_name_data))
					{
						if(!in_array($k,$repeat_key))
						{
							$repeat_key[] = $k;
						}
					}
					else
					{
						$repeat_product_name_data[] = $input_data[$field_poz_name];
					}
				}
			}
		}
		//6.开始导入数据整理、导入***********************************************
		foreach($data as $key_item=>$data_item)
		{
			//空则不处理
			if(empty($data_item))
			{
				continue;
			}
			//过滤特殊字符，回车、换行
			if($impt_count < $step)
			{
				$impt_count ++;
			}
			else
			{
				$impt_total += $impt_count;
				$impt_count    = 1;
				$this->_set_progress("导入数据......#$impt_total#$total");
			}
			//插入的产品数据
			$impt_product_data = array();
			foreach ($product_field AS $key => $info)
			{
				if (empty($data_item[$info["stand"]]))
				{
					$impt_product_data[$info["field"]] = '';
				}
				else
				{
					$impt_product_data[$info["field"]] = $data_item[$info["stand"]];
				}
			}
			//过滤产品名称（导入数据重复、跟数据库数据重复）
			if($filter_product_name)
			{
				if(!empty($repeat_key))
				{
					if(in_array($key_item,$repeat_key))
					{
						$impt_product_data['impt_error_remark'] = "[产品名称]与本次导入数据重复";
						$data_error[] = $impt_product_data;
						continue;
					}
				}
				$result_str = array();
				$wheres = array();
				//产品名称
				if (!empty($impt_product_data['product_name'])&&$filter_product_name && $field_poz_name >= 0 )
				{
					$result_str[] = "[产品名称]";
					$wheres[] = "product_name = '".$impt_product_data['product_name']."'";
				}
				$where = implode(' OR ',$wheres);
				if (!empty($where))
				{
					$total_error = 0;
					$this->db_read->where($where);
					$this->db_read->select('count(*) as total',FALSE);
					$total_error_query = $this->db_read->get('est_product');
					$total_error = $total_error_query->row()->total;
					if($total_error>0)
					{
						$result_string = implode('或',$result_str);
						$impt_product_data['impt_error_remark'] = $result_string."与系统中已有数据重复";
						$data_error[] = $impt_product_data;
						continue;
					}
				}
			}
			//必填字段处理 检查产品编号
			if ( $field_poz_number == -1 || empty($impt_product_data["product_num"]) )
			{
				$this->load->model('product_model');
				$new_product_num = $this->product_model->new_product_number();
				$impt_product_data["product_num"] = $new_product_num;
			}
			$impt_product_data["product_class_id"] = $product_class_id;
			$impt_product_data["product_state"] = $product_state;
			$impt_product_data["impt_id"] = $impt_id;

			//存储可插入数据库的数据
			$impt_product_data_array[] = $impt_product_data;
			$impt_success ++;
		}
		//8.插入产品数据
		if(!empty($impt_product_data_array))
		{
			$this->db_write->insert_batch('est_product',$impt_product_data_array);
		}
		//9.修改日志
		$impt_fail = $total - $impt_success;
		$this->db_write->update('est_import_log',array('impt_success'=>$impt_success,'impt_fail'=>$impt_fail,'impt_state'=>1),array('impt_id'=>$impt_id));

		usleep(200);
		$this->_set_progress("导入结束#$impt_success#$total#$impt_fail#$impt_id");
		return TRUE;
	}

	/**
	 * 获取导入日志检索条件
	 * 
	 * @param array $condition  检索条件
	 * @return array
	 * 
	 * @author zgx
	 */
	private function get_import_log_condition($condition = array())
	{
		$wheres = array();
		//导入类型
		if(!empty($condition['impt_type']))
		{
			switch ($condition['impt_type'])
			{
				case IMPT_CLIENT ://客户
				$wheres[] = "impt_type = 1";
				break;
				case IMPT_PRODUCT ://产品
				$wheres[] = "impt_type = 2";
				break;
			}
		}
		//导入批次号
		if(!empty($condition['impt_id']))
		{
			$wheres[] = "impt_id = ".$condition['impt_id'];
		}

		//数据权限
		$wheres[] = data_permission();

		return $wheres;
	}

	/**
	 * 数据导入日志-数据列表
	 *
	 * @param array $condition 传递搜索条件的数组
	 * @param int $page
	 * @param int $limit
	 * @param int $sort
	 * @param int $order
	 * @return object responce
	 * <code>
	 * $responce->total = 10
	 * $responce->rows = array(
	 *  [0] => array(
	 *        [impt_id]=> 批次号
 	 *        [impt_time] => 导入时间
	 *        [impt_success] =>  导入成功数
	 *       … 
	 *          )
	 *     )
	 * </code>
	 * @author zgx
	 */
	public function get_import_log($condition=array(),$page=0, $limit=0, $sort=NULL, $order=NULL)
	{
		$wheres = $this->get_import_log_condition($condition);
		$where = implode(" AND ",$wheres);

		if( ! empty($where))
		{
			$this->db_read->start_cache();
			$this->db_read->where($where);
			$this->db_read->stop_cache();
		}

		$this->db_read->select('count(*) as total',FALSE);
		$total_query = $this->db_read->get('est_import_log');
		$total = $total_query->row()->total;
		$responce = new stdClass();
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
		$_data = $this->db_read->get('est_import_log');
		$this->db_read->flush_cache();//清除缓存细信息

		$responce -> rows = array();
		foreach($_data->result_array() AS $i=>$item)
		{
			$item["impt_time"] = empty($item["impt_time"]) ? "" : date("Y-m-d H:i:s",$item["impt_time"]);
			$responce -> rows[$i] = $item;
		}
		return $responce;
	}

	/**
	 *  删除导入失败的数据
	 * 
	 * @param $impt_id 批次号
	 * @param $impt_type 导入类型 IMPT_CLIENT客户  IMPT_PRODUCT产品
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function delete_failure_data($impt_id = 0 ,$impt_type = '')
	{
		if (empty($impt_id)||empty($impt_type))
		{
			return FALSE;
		}

		$log_contact = array();
		$log_table  = array();
		$user_name   = $this->session->userdata('user_name');
		$user_num    = $this->session->userdata('user_num');

		//1、若是 客户导入 删除 导入时 记录不合法数据表 相应的数据
		if($impt_type == IMPT_CLIENT)
		{
			$query = $this->db_write->delete("est_import_error_data",array("impt_id"=>$impt_id));
			if ($query)
			{
				$log_contact[] = "删除导入批次号为 $impt_id 失败数据(导入日志)|操作人：".$user_name."[".$user_num."]";
				$log_table[] = 'est_import_error_data';
			}
			else
			return FALSE;
		}

		//2、删除导入日志相应数据
		$old_import_info = $this->get_import_log_info($impt_id);
		if ($old_import_info)
		{
			//成功数为0 删除
			if ( $old_import_info["impt_success"] == 0 )
			{
				$query_log = $this->db_write->delete("est_import_log",array("impt_id"=>$impt_id,"impt_success"=>0));
				if($query_log)
				{
					$log_contact[] = "删除导入成功数为 0 批次号为 $impt_id 的到入日志(导入日志)|操作人：".$user_name."[".$user_num."]";
					$log_table[] = 'est_import_log';
				}
			}
			else
			{
				//成功数不为0 失败数清零
				$this->db_write->where("impt_id",$impt_id);
				$this->db_write->update("est_import_log",array("impt_total"=> $old_import_info["impt_total"]-$old_import_info["impt_fail"],"impt_fail"=>0));
			}
		}

		return TRUE;
	}

	/**
	 * 删除导入成功的数据 （把相应表相应批次号的数据一并删除）
	 *
	 * @param int $impt_id 批次号
	 * @param string $impt_type 类型
	 * @return bool
	 * 
	 * @author zgx
	 */
	public function delete_success_data($impt_id = 0,$impt_type = '')
	{
		if (empty($impt_id)||empty($impt_type))
		{
			return FALSE;
		}

		$log_client_contact = array();
		$log_cle_id  = array();
		$log_contact = array();
		$log_table  = array();

		$user_name   = $this->session->userdata('user_name');
		$user_num    = $this->session->userdata('user_num');

		//1、删除相应表相应批次号的数据
		switch($impt_type)
		{
			case IMPT_CLIENT :
				$query_impt = $this->db_read->query("SELECT cle_id FROM est_client WHERE impt_id=$impt_id");
				$query_impt = $query_impt->result_array();
				if (!empty($query_impt))
				{
					//删除客户表中指定批次号的数据
					$this->db_write->delete("est_client",array("impt_id"=>$impt_id));
					//生成日志
					$log_contact[] = "删除导入批次号为 $impt_id 的客户数据(导入日志)|操作人：".$user_name."[".$user_num."]";
					$log_table[] = 'est_client';
					foreach($query_impt AS $v)
					{
						$log_client_contact[] = "删除导入批次号为 $impt_id 的客户数据(导入日志)|操作人：".$user_name."[".$user_num."]";
						$log_cle_id[] = $v['cle_id'];
					}
				}
				break;
			case IMPT_PRODUCT :
				//删除订单表中指定批次号的数据
				$this->db_write->delete("est_product",array("impt_id"=>$impt_id));
				//生成日志
				$log_contact[] = "删除导入批次号为 $impt_id 的订单数据(导入日志)|操作人：".$user_name."[".$user_num."]";
				$log_table[] = 'est_product';
				break;
		}

		//2、删除导入日志相应数据
		$old_import_info = $this->get_import_log_info($impt_id);
		if ($old_import_info)
		{
			//失败数为0 删除
			if ($old_import_info["impt_fail"] == 0)
			{
				$query_log = $this->db_write->delete("est_import_log",array("impt_id"=>$impt_id,"impt_fail"=>0));
				if($query_log)
				{
					$log_contact[] = "删除导入失败数为 0 批次号为 $impt_id 的到入日志(导入日志)|操作人：".$user_name."[".$user_num."]";
					$log_table[] = 'est_import_log';
				}
			}
			else
			{
				//失败数不为0，成功数清零
				$this->db_write->where("impt_id",$impt_id);
				$this->db_write->update("est_import_log",array("impt_total"=> $old_import_info["impt_total"]-$old_import_info["impt_success"],"impt_success"=>0));
			}
		}

		//3、记入日志
		$this->load->model("log_model");
		if (!empty($log_client_contact))
		{
			$this->log_model->write_client_log($log_client_contact,$log_cle_id);
		}
		return TRUE;
	}

	/**
	 * 导出该批次中，导入失败的数据（客户）
	 *
	 * @param int $impt_id  批次号
     * @return bool
	 *
	 * @author zgx
	 */
	public function export_failure_data($impt_id=0)
	{
		if (empty($impt_id))
		{
			return FALSE;
		}

		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		//得到需要导出的数据详细信息
		$export_data = $this->get_import_error_data($impt_id);
		if(count($export_data)>0)
		{
			//得到坐席信息
			$this->load->model("user_model");
			$user_info = $this->user_model->get_user_info($export_data[0]['cle_creat_user_id']);

			foreach ($export_data AS $key => $item)
			{
				if (!empty($item["cle_creat_user_id"]))
				{
					$export_data[$key]["cle_creat_user_id"] = empty($user_info['user_name']) ? "" : $user_info['user_name'];
				}
			}
		}

		//导出字段
		$this->load->model("field_confirm_model");
		$field = $this->field_confirm_model->get_available_fields(FIELD_TYPE_CLIENT);
		$extra = array("user_id","dept_id","cle_first_connecttime","cle_last_connecttime","con_rec_next_time","dployment_num","cle_executor_time","cle_update_user_id","cle_update_time");

		//表头
		$title          = array();
		$title["name"]  = array();
		$title["field"] = array();
		foreach ($field AS $key => $value)
		{
			if (!in_array($value["fields"],$extra))
			{
				array_push($title["name"],$value["name"]);
				array_push($title["field"],$value["fields"]);
			}
		}
		array_push($title["name"],"失败原因");
		array_push($title["field"],"impt_error_remark");

		//构造数据
		$_data = $this->construct_export_data($title,$export_data);

		//导出csv文件
		$filename = $impt_id."失败数据";
		$this->load->library('Csv');
		$this->csv->creatcsv($filename,$_data);
		return true;
	}

	/**
	 * 得到该批次，导入失败的数据（客户）
	 * @param int $impt_id	批次号
	 * @return array
	 * 
	 * @author zgx
	 */
	private function get_import_error_data($impt_id = 0)
	{
		if (empty($impt_id))
		{
			return array();
		}

		$query = $this->db_read->get_where("est_import_error_data",array("impt_id"=>$impt_id));

		return $query->result_array();
	}

	/**
	 * 构造导出数据
	 *
	 * @param array $title 表头
	 * @param array $data 需要导出的数据
	 * @return array
	 * 
	 * @author zgx
	 */
	private function construct_export_data($title=array(),$data=array())
	{
		$export_data    = array();
		$export_data[0] = $title["name"];//表头
		$i = 1;
		foreach ($data as $task)
		{
			foreach ($title["field"] as $field)
			{
				if (empty($task[$field]))
				{
					$export_data[$i][] = "";
				}
				else
				{
					$export_data[$i][] = $task[$field];
				}
			}
			$i ++;
		}
		return  $export_data;
	}

	/**
	 * 得到某导入日志信息
	 *
	 * @param int $impt_id
	 * @return array 
	 * <code>
	 * array(
	 *  [impt_id]=> 批次号,
	 *  [impt_total]=> 导入总数,
	 *  [impt_success]=> 导入成功数,
	 *  [impt_fail]=> 导入失败数
	 * )
	 * </code>
	 * 
	 * @author zgx
	 */
	public function get_import_log_info( $impt_id = 0 )
	{
		if(!$impt_id)
		{
			return array();
		}

		$this->db_read->select("impt_id,impt_total,impt_success,impt_fail");
		$this->db_read->where("impt_id",$impt_id);
		$query = $this->db_read->get("est_import_log");
		$import_info = $query->row_array();

		return $import_info;
	}

	/**
	 * 将字符串转换成小写
	 *
	 * @param string/array(一维数组) $str_content
	 * @return string/array
	 * 
	 * @author zgx
	 */
	private function get_strtolower( $str_content = NULL)
	{
		if ($str_content)
		{
			if (is_array($str_content))
			{
				//一维数组
				foreach ($str_content AS $k_i => $value_i)
				{
					$str_content[$k_i] = strtolower($value_i);
				}
			}
			else
			{
				//字符串
				$str_content = strtolower($str_content);
			}
		}
		return $str_content;
	}
}