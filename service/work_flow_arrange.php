<?php
/**
 * 自动整理工单列表
 * $Author:
 * $time  :
 */

set_time_limit(0);

define("IN_EST", true);
/* 取得当前所在的根目录 */
define('ROOT_PATH', str_replace('work_flow_arrange.php', '', str_replace('\\', '/', __FILE__)));


include(ROOT_PATH.'config.inc.php');
date_default_timezone_set($cfg["timezone"]);

if ($cfg['debug'])
{
	define('DEBUG_MODE', 1);
}
/*数据库*/
require(ROOT_PATH . 'includes/cls_mysql.php');

$vcc_infos = array();
//单机版
if($cfg['type'] == 2)
{
	$vcc_infos[$cfg['vcc_id']] = array('db_main_ip'=>$cfg['my_host'],'db_name'=>$cfg['my_database'],'db_user'=>$cfg['my_user'],'db_password'=>$cfg['my_password']);
}
//saas版
else
{
	//得到所有使用中的企业
	$db = new cls_mysql($cfg['saas_host'], $cfg['saas_user'], $cfg['saas_password'], $cfg['saas_database']);
	$vcc_infos = $db->getAll("SELECT vcc_id,db_main_ip,db_name,db_user,db_password,db_system FROM cc_ccods WHERE status=1 AND db_system = 'ekt'",'vcc_id');
	$db->close();
}


//开始
foreach ($vcc_infos as $vcc_id=>$vcc_item)
{
	$vcc_info = $vcc_infos[$vcc_id];
	$db_ip = $vcc_info['db_main_ip'];
	$db_name = $vcc_info['db_name'];
	$db_user = $vcc_info['db_user'];
	$db_password = $vcc_info['db_password'];
	if(empty($db_ip))
	{
		continue;
	}

	$db = new cls_mysql($db_ip,$db_user,$db_password,$db_name);

	//获取现有流程id
	$flow_info = $db->getAll("SELECT flow_id FROM est_flow");
	foreach($flow_info as $value)
	{
		$flow_id = $value['flow_id'];
		$tablename = 'est_flow_info'.$flow_id;
		$work_flow = $db->getAll("SELECT * FROM ".$tablename." WHERE flow_fag=0 limit 5000");
		if(count($work_flow)>0)
		{
			//节点信息
			$nodes = $db->getAll("SELECT node_id,flow_id,node_name,node_use_time,form_id,participant_type,node_participant,action FROM est_flow_nodes WHERE flow_id=".$flow_id,'node_id');
			$forms = array(); //节点相应表单id
			$cascade = array();//级联信息
			$fields = array();//表单字段信息
			foreach($nodes as $node_id=>$node)
			{
				$form_id = empty($node['form_id']) ? 0 : $node['form_id'];
				$forms[$node_id] = $form_id;
				//表单字段信息
				$form_fields = $db->getAll("SELECT form_id,fields,field_name FROM est_form_fields WHERE form_id=".$form_id);
				foreach($form_fields as $field)
				{
					$fields[$node_id][] = $field['fields'].'_'.$node_id;
				}
				$fields[$node_id][] = 'create_user_id_'.$node_id;
				$cascade_info = $db->getAll("SELECT id,parent_id,name,deep,field FROM est_form_cascade WHERE form_id=".$form_id);
				foreach($cascade_info AS $cas)
				{
					$cascade[$form_id][$cas["field"].'_'.$cas["deep"]][$cas["id"]] = $cas["name"];
				}
			}

			//整理数据
			foreach($work_flow as $data)
			{
				$result = array();
				$cle_info = $db->getRow("SELECT * FROM est_client WHERE cle_id=".$data['cle_id']);
				$result['cle_name'] = isset($cle_info['cle_name']) ? $cle_info['cle_name'] : '不存在';
				$result['cle_phone'] = isset($cle_info['cle_phone']) ? $cle_info['cle_phone'] : '';
				$result['cle_province_name'] = isset($cle_info['cle_province_name']) ? $cle_info['cle_province_name'] : '';
				$result['cle_city_name'] = isset($cle_info['cle_city_name']) ? $cle_info['cle_city_name'] : '';

				//查询表单详细信息
				$form_data = array();
				foreach($forms as $nid=>$formid)
				{
					$table = 'est_form_info'.$formid;
					$if_table_exit = $db->getRow("SELECT table_name FROM information_schema.TABLES WHERE table_name ='".$table."'");
					if($if_table_exit && $db->getOne("SHOW TABLES LIKE '$table'"))
					{
						$form_value = $db->getRow("SELECT * FROM ".$table." WHERE flow_id=".$flow_id." AND flow_info_id=".$data['flow_info_id']." AND node_id=".$nid);
						$this_node_form_data = array();
						if($form_value)
						{
							foreach($form_value as $field=>$fval)
							{
								$key_value = explode('_',$field);
								if(count($key_value)==2 && !empty($fval))
								{
									$cascade_key = $key_value[0].'_'.$key_value[1];
									if(isset($cascade[$formid][$cascade_key]) && !empty($cascade[$formid][$cascade_key][$fval]))
									{
										$this_node_form_data[$field."_".$nid] = $cascade[$formid][$cascade_key][$fval];
									}
								}
								else
								{
									$this_node_form_data[$field."_".$nid] = empty($fval) ? "" : replace_illegal_string($fval);
								}
							}
						}
					}
					foreach($fields[$nid] as $v)
					{
						$result[$v] = isset($this_node_form_data[$v])?$this_node_form_data[$v]:'';
					}
					//节点追加说明信息
					$result['node'.$nid.'_description'] = isset($data['node'.$nid.'_description']) ? replace_illegal_string($data['node'.$nid.'_description']) : "";
				}
				$result = addslashes(json_encode($result));

				$db -> query("insert into est_work_flow(flow_id,flow_info_id,flow_number,flow_create_time,flow_status,flow_json,create_time) values(".$flow_id.",".$data['flow_info_id'].",'".$data['flow_number']."','".$data['flow_start_time']."',".$data['flow_status'].",'".$result."','".date('Y-m-d H:i:s')."')ON DUPLICATE KEY UPDATE flow_status=".$data['flow_status'].",flow_json='".$result."'");
				//修改流程记录表标志位
				if($data['flow_status']==2 || $data['flow_status']==3)
				{
				$db->query("update ".$tablename." set flow_fag=1 where flow_fag=0 and flow_info_id=".$data['flow_info_id']);
				}
			}
		}
	}
}

/**
 * replace_illegal_string()
 * 处理输入中的特殊字符
 * @param mixed $string
 * @return bool|string
 */
function replace_illegal_string($string)
{
	if (!$string) {
		return;
	}

	//字符串
	if (is_string($string)) {
		$string = str_replace("&nbsp;", " ", $string);
		$string = str_replace(",", "，", $string);
		$string = str_replace("\r\n", " ", $string);
		$string = str_replace("\r", " ", $string);
		$string = str_replace("\n", " ", $string);
		$string = trim(addslashes($string));
	} //一维数组
	elseif (is_array($string)) {
		foreach ($string AS $k => $v) {
			$v = str_replace("&nbsp;", " ", $v);
			$v = str_replace(",", "，", $v);
			$v = str_replace("\r\n", " ", $v);
			$v = str_replace("\r", " ", $v);
			$v = str_replace("\n", " ", $v);
			$string[$k] = trim(addslashes($v));
		}
	}
	return $string;
}