<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*数据类型   角色类型*/
define('DATA_DEPARTMENT',1); //部门数据
define('DATA_PERSON',2); //个人数据

/*导入模板*/
define('IMPT_CLIENT',1); //客户导入
define('IMPT_PRODUCT',2); //产品导入
define('MODEL_CLIENT_IMPT',1); //模板客户导入
define('MODEL_PRODUCT_IMPT',3); //模板产品导入

/*列表类型*/
define('LIST_CONFIRM_CLIENT',0); //客户管理列表配置
define('LIST_CONFIRM_CONTACT',1); //联系人列表配置
define('LIST_CONFIRM_CLIENT_RESOURCE',3); //资源调配列表配置
define('LIST_CONFIRM_CLIENT_DEAL',4); //数据处理列表配置
define('LIST_COMFIRM_PRODUCT',5); //产品列表配置
define('LIST_COMFIRM_ORDER',6); //订单列表配置
define('LIST_COMFIRM_SERVICE',7); //客服服务 列表配置

/*字段类型*/
define('FIELD_TYPE_CLIENT_CONTACT',-1); //客户+联系人
define('FIELD_TYPE_CLIENT',0); //客户
define('FIELD_TYPE_CONTACT',1); //联系人
define('FIELD_TYPE_PRODUCT',2); //产品
define('FIELD_TYPE_ORDER',3); //订单
define('FIELD_TYPE_SERVICE',4); //客服服务

/*自定义字段文本类型*/
define('DATA_TYPE_INPUT',1); //文本框
define('DATA_TYPE_SELECT',2); //下拉选择
define('DATA_TYPE_TEXTAREA',3); //文本域
define('DATA_TYPE_JL',4); //级联框
define('DATA_TYPE_DATA',5); //日期框
define('DATA_TYPE_CHECKBOXJL',7); //关联级联

/*数据字典*/
define('DICTIONARY_CLIENT_INFO',1); //客户信息- 信息来源
define('DICTIONARY_CLIENT_STAGE',2); //客户信息- 客户阶段
define('DICTIONARY_ORDER_STATE',3); //订单信息- 订单状态
define('DICTIONARY_SERVICE_TYPE',4); //客服服务 - 服务类型
define('DICTIONARY_DELIVERY_MODE',5); //订单信息 - 配送方式
define('DICTIONARY_CLIENT_STATE',6); //客户信息 - 号码状态

/*提醒类型*/
define("REMIND_MINE",0);  //我的提醒
define("REMIND_CLIENT",1);//客户相关提醒
define("REMIND_ORDER",2); //订单相关提醒

/*短信*/
define('MAXSMSLENGTH',300); //短信最大字符数
define('EACHSMSLENGTH',70); //每条短信字符数

/*坐席信息*/
define("MSG_MAX_LENGTH",300);//坐席发信息，每条信息最大字数


define('PIC_FILE','./public/product_pic/'); //照片存储地址
define('FILE','./public/uploadfile/'); //文件存储地址
define('NO_PIC','./image/no_picture.jpg'); //文件存储地址

define('REGION_PROVINCE',1);//省
define('REGION_CITY',2);//市

/*客户类型*/
define('CSKH',1);//初始
define('GJKH',2);//跟进
define('ZJKH',3);//终结

/*客户数据类型*/
$CLIENT_PUBLIC_TYPE[0] = '有所属人';
$CLIENT_PUBLIC_TYPE[1] = '放弃';
$CLIENT_PUBLIC_TYPE[2] = '收回';
$CLIENT_PUBLIC_TYPE[3] = '新导入';

/*流程状态*/
$FLOW_STATUS[0] = '未启用';
$FLOW_STATUS[1] = '启用';
$FLOW_STATUS[2] = '过期';
$FLOW_STATUS[3] = '禁用';

//工单全局状态
$WORKFLOW_STATUS[0] = '未开始';
$WORKFLOW_STATUS[1] = '运行中';
$WORKFLOW_STATUS[2] = '已完成';
$WORKFLOW_STATUS[3] = '已结束';
$WORKFLOW_STATUS[4] = '已删除';

//流程节点状态
$FLOWNODE_STATUS[0] = '未处理';
$FLOWNODE_STATUS[1] = '处理中';
$FLOWNODE_STATUS[2] = '正常完成';
$FLOWNODE_STATUS[3] = '退回';
$FLOWNODE_STATUS[4] = '超时完成';

//通话记录 - 通话类型
$CDR_CALL_TYPE[1] = '呼出';
$CDR_CALL_TYPE[2] = '呼入';
$CDR_CALL_TYPE[3] = '呼出转接';
$CDR_CALL_TYPE[4] = '呼入转接';
$CDR_CALL_TYPE[5] = '呼出拦截';
$CDR_CALL_TYPE[6] = '呼入拦截';
$CDR_CALL_TYPE[7] = '(被)咨询';
$CDR_CALL_TYPE[8] = '(被)三方';
$CDR_CALL_TYPE[9] = '监听';
$CDR_CALL_TYPE[10] = '强插';
$CDR_CALL_TYPE[11] = '转内线';
$CDR_CALL_TYPE[12] = '转外线';
$CDR_CALL_TYPE[13] = '内部呼叫';

/* End of file constants.php */
/* Location: ./application/config/constants.php */