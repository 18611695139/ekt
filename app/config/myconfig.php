<?php

//系统名称
$config["system_name"] = "德汇信呼叫中心"; 

//系统版本
$config['version'] = "";

//版权信息
$config['copyright'] = "<p>Copyright Corporation All Rights 北京德汇信科技有限公司</p>";

//中间件服务器IP
$config['cti_server'] = $_SERVER['HTTP_HOST'];

//默认通讯服务器IP
$config['default_tel_server'] = $_SERVER['HTTP_HOST'];

//通讯服务器IP端口
$config['tel_server_port'] = "5090";

//默认常连接模式
$config['default_ol_model'] = "0";

//软电话使用号码前缀
$config['sip_prefix'] = true;

//外地手机外呼前缀 为空则不判断
$config['mobile_prefix'] = '0';

//本地区号 只有在需要判断外地手机前缀时才加
$config['local_code'] = '';

//企业编号（设置了企业代码表示为单机版，未设置则表示saas版）
$config['vcc_code']    = '';

/**
 * wintel-api 接口文件，不带后缀/
 * 如果安装了manage3.0请使用3.0里面集成的api,
 * 如果3.0访问地址为 http://127.0.0.1/manage3/web;那么api地址为http://127.0.0.1/manage3/web/wintelapi;
 * 注意内部使用不验证http://127.0.0.1/manage3/web/wintelapi/api
 */
$config["api_wintelapi"] = 'http://127.0.0.1/wintelapi/web';

//wintel-api 录音接口文件（公网地址需要在客户端浏览器访问该地址），不带后缀/
$config["api_recordapi"] = 'http://'.$_SERVER['HTTP_HOST'].'/wintelapi/web';

//wintel-api 认证密码
$config["wintelapi_secret"] = "e10adc3949ba59abbe56e057f20f883e";

//elasticsearch服务器配置
$config['elasticsearch'] = array(
    'hosts' => array(
        '127.0.0.1:9200'
    )
);

//短信接口地址
$config['sms'] = "";

//短信发送平台标记  0=>二枢纽,1=>北京SaaS,2=>龙信,3=>西三旗,4=>信合,5=>测试
$config['sms_flag'] = "0";

//短信发送令牌
$config['sms_token'] = "";

//是否开启过期时间提示功能,true开启 false关闭
$config['if_enable_expired_not_alowed_to_use'] = false;

//验证码保存路径，结尾带/
$config['captcha_path'] = './public/captcha/';

//验证码保存路径，结尾带/
$config['lock_path'] = './public/lock/';

//知识库文件存储路径
$config['knowledge_path'] = './public/knowledge/';

//上传临时文件存储路径
$config['upload_path'] = './public/uploadfile/';

/* End of file myconfig.php */
/* Location: ./application/config/myconfig.php */