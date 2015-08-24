<?php
/**
* 功能: 配置系统相关信息
* 创建日期:
* 最后更新:
* 作者:
*/ 
//类型 1为saas版  2为单机版
$cfg['type'] = 1;

//调试模式
$cfg["debug"] = true;

$cfg["timezone"] = "Asia/Shanghai";

//=============== saas版 =====================

//ccod数据库地址
$cfg['saas_host'] = '127.0.0.1';

//ccod数据库用户名
$cfg['saas_user'] = 'root';

//ccod数据库密码  
$cfg['saas_password'] = 'My23pd89fUd9sD80D';

//ccod数据库名
$cfg['saas_database'] = 'ccod';

//==================== 单机版 =========================

//本地数据库地址
$cfg['my_host'] = '192.168.1.35';

//本地数据库用户名
$cfg['my_user'] = 'root';

//本地数据库密码   passw0rd
$cfg['my_password'] = 'passw0rd';

//本地ekt数据库名
$cfg['my_database'] = 'ekt_ci';

//公司编号
$cfg['vcc_id'] = 100;


