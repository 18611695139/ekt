<?php
/**
 * 自动收回符合要求的客户数据
 * $Author:
 * $time  :
 */

set_time_limit(0);

//exec("ps -fe | grep /usr/local/auto_zfyj/auto_zfyj.php|grep -v grep|grep -v auto_zfyj.log",$pid);


define("IN_EST", true);
/* 取得当前所在的根目录 */
define('ROOT_PATH', str_replace('auto_back.php', '', str_replace('\\', '/', __FILE__)));


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
		return false;
	}

	$db = new cls_mysql($db_ip,$db_user,$db_password,$db_name);

	//获取系统参数关于自动收回的配置信息
	$config_info = $db->getRow("SELECT auto_back_client,client_has_create_day,no_contact_day1,no_contact_day2,auto_back_place FROM est_config");

	$stage_sql = '';
	$make_stage= '';
	if($config_info['auto_back_client']!=0)
	{
		//获取标志终结 的客户阶段名称
		$stages = $db->getALL("SELECT cle_stage FROM est_client_type WHERE cle_type=3");
		$cle_stages = array();
		foreach($stages as $stage)
		{
			$cle_stages[] = $stage['cle_stage'];
		}
		$stage_sql = '';
		if($cle_stages)
		{
			if(count($cle_stages)==1)
			{
				$make_stage = $cle_stages[0];
				$stage_sql =  " AND cle_stage!='".$cle_stages[0]."'";
			}
			else
			{
				$make_stage = implode(",",$cle_stages);
				$stage_sql = " AND cle_stage NOT IN('".implode("','",$cle_stages)."')";
			}
		}
	}

	$cle_ids = array();
	$today = date("Y-m-d");
	switch($config_info['auto_back_client'])
	{
		case 1: //方案一：没有临界点的
		$X_days_later = $config_info['no_contact_day1'];
		if($X_days_later>0)
		{
			$X_days_later  = date("Y-m-d",strtotime("-".$X_days_later." day",strtotime($today)));
			$cle_info = $db->getALL("SELECT cle_id FROM est_client WHERE user_id!=0 AND cle_last_connecttime<='$X_days_later' AND cle_update_time<='$X_days_later' AND cle_creat_time<='$X_days_later'".$stage_sql);
			foreach($cle_info as $value)
			{
				$cle_ids[] = $value['cle_id'];
			}
		}
		break;
		case 2: //方案二：有临界点的
		$has_create_day = $config_info['client_has_create_day'];//临界点
		$no_contact_in = $config_info['no_contact_day1']; //临界点内没联系的天数
		$no_contact_out = $config_info['no_contact_day2']; //临界点外没联系的天数
		if(!empty($has_create_day))
		{
			//临界点内的(如30内)
			if(!empty($no_contact_in))
			{
				$has_create_data = date("Y-m-d",strtotime("-".$has_create_day." day",strtotime($today)));
				$X_days_later = date("Y-m-d",strtotime("-".$no_contact_in." day",strtotime($today)));
				$cle_info = $db->getALL("SELECT cle_id FROM est_client WHERE user_id!=0 AND cle_last_connecttime<='$X_days_later' AND cle_update_time<='$X_days_later' AND cle_creat_time>='$has_create_data'".$stage_sql );
				foreach($cle_info as $value)
				{
					$cle_ids[] = $value['cle_id'];
				}
			}
			//临界点外的(如30外)
			if(!empty($no_contact_in))
			{
				$X_days_later = date("Y-m-d",strtotime("-".$no_contact_out." day",strtotime($today)));
				$cle_info = $db->getALL("SELECT cle_id FROM est_client WHERE user_id!=0 AND cle_last_connecttime<='$X_days_later' AND cle_update_time<='$X_days_later' AND cle_creat_time<'$has_create_data'".$stage_sql );
				foreach($cle_info as $value)
				{
					if(!in_array($value['cle_id'],$cle_ids))
					{
						$cle_ids[] = $value['cle_id'];
					}
				}
			}
		}
		break;
		default: //不启用
		break;
	}

	$total = 0;//收回总数
	if(!empty($cle_ids))
	{
		if(isset($config_info['auto_back_place'])&& ($config_info['auto_back_place']==0))
		{
			$update_sql = "UPDATE est_client SET cle_if_release=2,last_user_id=CONCAT(IFNULL(last_user_id,''),',',user_id),cle_update_time=CURDATE(),cle_update_user_id=0,user_id=0,dept_id=1 WHERE cle_id IN(".implode(",",$cle_ids).")";
		}
		else
		{
			$update_sql = "UPDATE est_client SET cle_if_release=2,last_user_id=CONCAT(IFNULL(last_user_id,''),',',user_id),cle_update_time=CURDATE(),cle_update_user_id=0,user_id=0 WHERE cle_id IN(".implode(",",$cle_ids).")";
		}
		$db->query($update_sql);
		$total = $db->affected_rows();
	}

	$db->close();

	if($config_info['auto_back_client']!=0)
	{
		$logfilename = ROOT_PATH.'log/success_'. date('Y_m_d') . '.log';
		$str = 'date: '.date('Y_m_d H:i:s'). "\t企业代码：".$vcc_id."\n终结客户阶段名称：".$make_stage. "\n自动收回条数：".$total."\n客户id:".implode(',',$cle_ids)."\n\n";
		file_put_contents($logfilename, $str, FILE_APPEND);
	}
}