EKT安装手册
================

## 1. 服务器上首先需要安装git

### 1) yum安装

    yum install git

### 2) 手动编译安装

    yum install -y curl curl-devel zlib-devel openssl-devel perl perl-devel cpio expat-devel gettext-devel autoconf
    wget https://www.kernel.org/pub/software/scm/git/git-1.9.2.tar.gz(版本自己选择)
    tar zxvf git-1.9.2.tar.gz
    cd git-1.9.2
    autoconf
    ./configure
    make
    make install
    git --version

### 3) 安装过程中可能遇到的问题

    # 未安装gcc出现的错误
    configure: Setting lib to 'lib' (the default)
    configure: Will try -pthread then -lpthread to enable POSIX Threads.
    configure: CHECKS for site configuration
    configure: CHECKS for programs
    checking for cc... no
    checking for gcc... no
    configure: error: in `/tmp/git-1.9.2':
    configure: error: no acceptable C compiler found in $PATH
    See `config.log' for more details.

    # 需要安装gcc
    yum install gcc


## 2. 安装消息队列

### 1) 安装jdk

    yum  install java

### 2) 安装php的stomp扩展

    wget http://pecl.php.net/get/stomp-1.0.5.tgz
    tar -zxvf stomp-1.0.5.tgz
    cd stomp-1.0.5
    phpize
    ./configure
    make
    make install

    安装完成后往 /etc/php.d 中加入 extension = stomp.so

### 3) 安装 activemq

    cd /usr/local
    wget http://mirror.bjtu.edu.cn/apache/activemq/apache-activemq/5.8.0/apache-activemq-5.8.0-bin.tar.gz
    tar -zxvf  apache-activemq-5.8.0-bin.tar.gz

    # 修改配置文件./activemq/conf/activemq.xml
    在<transportConnectors></transportConnectors>中插入 <transportConnector name="stomp" uri="stomp://0.0.0.0:61612?transport.closeAsync=false"/>

    # 启动activemq
    ./activemq/bin/activemq start

    # 配置开机自启动activemq
    往/etc/rc.d/init.d 中加入 /usr/local/activemq/bin/activemq start >> /usr/local/activemq/activemq.log
	

## 3. 安装EKT程序

### 1) 安装系统文件

    cd /var/www/html
    git clone http://git.wintels.cn/louxin/ekt.git
    cd ekt
    cp app/config/database.php.dist app/config/database.php
    cp app/config/myconfig.php.dist app/config/myconfig.php

### 2) 修改配置文件

#### 2.1) 修改数据库配置文件database.php中的信息
    $db['default']['hostname'] = '192.168.1.35';#主数据库地址
    $db['default']['username'] = 'root';#主数据库用户名
    $db['default']['password'] = 'passw0rd';#主数据库密码
    $db['default']['database'] = 'ekt_session';#主数据库名

#### 2.2) 修改自定义配置文件myconfig.php中的信息
    //中间件服务器IP
    $config['cti_server'] = "192.168.1.35";

    //默认通讯服务器IP
    $config['default_tel_server'] = "192.168.1.35";

    //通讯服务器IP端口
    $config['tel_server_port'] = "5060";

    //默认常连接模式
    $config['default_ol_model'] = "0";

    //软电话使用号码前缀
    $config['sip_prefix'] = true;

    //外地手机外呼前缀 为空则不判断
    $config['mobile_prefix'] = '0';

    //本地区号 只有在需要判断外地手机前缀时才加
    $config['local_code'] = '010';

    //企业编号（设置了企业代码表示为单机版，未设置则表示saas版）
    $config['vcc_code']    = '';

    //rpc接口地址
    $config["rpc_interface"] = "http://127.0.0.1/sync_service/sync_service/sservice/rpcserv.php";

    //录音下载地址
    $config["record_interface"] = 'http://'.$_SERVER['HTTP_HOST'].'/sync_service/sservice/record_download.php';

    //短信发送方式 rpc
    $config["sms_send_method"] = 'rpc';

    //wintel-api 接口文件
    $config["api_wintelapi"] = 'http://192.168.1.35/wintelapi/web/';
	
	//wintel-api 认证密码(后台登录密码)
	$config["wintelapi_secret"] = "e10adc3949ba59abbe56e057f20f883e";

#### 2.3) 修改app/config/activemq.php中地址
    'hostname' => '127.0.0.1' //消息队列所装服务器地址

### 3) 修改目录权限

    chmod -R 755 /var/www/html/ekt
    chown -R apache /var/www/html/ekt

### 4) 修改后台数据库(wintels|ccod) 数据

    # 在cc_ccods表找到对应企业那条数据修改以下字段内容
    db_main_ip：主数据库ip
    db_slave_ip：从数据库ip。没有没有从数据库就填主数据库的ip
    db_name：ekt数据库的表名。
    db_user：ekt数据库登陆用户名（一般是root）
    db_password：ekt数据库登陆密码 （一般是passw0rd）
    db_system:  此处选择ekt (单机版可以不填)
    role_action：ekt系统权限控制 （功能模块间用逗号隔开，如：wincall,khgl,wdzs,dxgl,kffw,cpgl,ddgl,tjfx,monitor,xtgl,zsk,）

    # 目前系统现有功能模块
    wincall     话务功能
    khgl        客户管理
    wdzs        我的助手
    dxgl        短信管理
    kffw        客服服务
    cpgl        产品管理
    ddgl        订单管理
    gdgl        工单管理
    lcgl        流程管理
    tjfx        统计分析
    monitor     系统监控
    xtgl        系统管理
    zsk         知识库

### 5) 注意事项

    # 若客户需要启用自动回收客户服务功能，往 /etc/crontab 加上一行：
    30 2 * * * root php /var/www/html/ekt/service/auto_back.php >> /var/www/html/ekt/app/logs/auto_back.log

    # 若客户启用工单模块，往 /etc/crontab 加上一行：
    */10 * * * * root php /var/www/html/ekt/service/work_flow_arrange.php >> /var/www/html/ekt/app/logs/workflow.log
	
	# 启用以上任何一项都需要修改 ekt/service/config.inc.php 配置文件 （区分单机版 、 saas版）
		//类型 1为saas版  2为单机版
		$cfg['type'] = 2;

		//=============== saas版 =====================

		//ccod数据库地址
		$cfg['saas_host'] = '192.168.1.35';

		//ccod数据库用户名
		$cfg['saas_user'] = 'root';

		//ccod数据库密码  
		$cfg['saas_password'] = 'passw0rd';

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

		//企业ID
		$cfg['vcc_id'] = 100;
	
## 4. 安装EKT数据库

    mysql -h数据库服务器IP地址 -uroot -ppassw0rd < /var/www/html/ekt/install/ekt_session.sql
    mysql -h数据库服务器IP地址 -uroot -ppassw0rd < /var/www/html/ekt/install/ekt.sql
	

## 5. 安装同步服务

### 1) 安装同步服务文件

    cd /var/www/html
    git clone http://git.wintels.cn/louxin/sync_service.git

### 2) 修改配置文件

#### 2.1) 修改sync_service/sservice中配置文件config.inc.php
    //后台数据库地址
    $cfg["my_host"] = "192.168.1.35";

    //后台数据库用户名
    $cfg["my_user"] = "root";

    //后台数据库密码   passw0rd
    $cfg["my_password"] = "passw0rd";

    //后台数据库名
    $cfg["my_database"] = "ccod";

    //录音服务器地址
    $cfg["record_download_addr"] = "http://192.168.1.35/ccwintels/cls_download.php";

#### 2.2) 修改服务端sync_service/sync_service中配置文件config.inc.php
    //后台数据库地址
    $cfg["my_host"] = "192.168.1.35";

    //后台数据库用户名
    $cfg["my_user"] = "root";

    //后台数据库密码
    $cfg["my_password"] = "passw0rd";

    //后台数据库名
    $cfg["my_database"] = "ccod";

#### 2.3) 修改客户端sync_service/sync_client中配置文件config.inc.php (区分saas版、单机版)
    //同步rpc接口地址
    $cfg['rpc_serv_addr'] = 'http://127.0.0.1/sync_service/sservice/rpcserv.php';

    //类型 1为saas版 同步 2为单机版同步
    $cfg['type'] = 1;

    /*******************************saas版参数****************************************/
    //后台数据库地址
    $cfg['saas_host'] = '192.168.1.35';

    //后台数据库用户名
    $cfg['saas_user'] = 'root';

    //后台数据库密码
    $cfg['saas_password'] = 'passw0rd';

    //后台数据库名
    $cfg['saas_database'] = 'ccod';

    /*******************************单机版参数****************************************/
    //ekt数据库地址
    $cfg['my_host'] = '192.168.1.35';

    //ekt数据库用户名
    $cfg['my_user'] = 'root';

    //ekt数据库密码
    $cfg['my_password'] = 'passw0rd';

    //ekt数据库名
    $cfg['my_database'] = 'ekt';

    //企业id
    $cfg['vcc_id'] = 1;

    //同步的系统
    $cfg['system'] = 'ekt';

### 3) 修改目录权限

    chmod -R 755 /var/www/html/sync_service

### 4) 往 /etc/crontab 中加入以下几行

    * * * * * root php /var/www/html/sync_service/sync_service/sync_service.php >> /var/www/html/sync_service/sync_service/sync_service.log
    10 1 * * * root php /var/www/html/sync_service/sync_service/sync_service_del_file.php >> /var/www/html/sync_service/sync_service/sync_service_del.log
    * * * * * root php /var/www/html/sync_service/sync_client/sync_client.php >> /var/www/html/sync_service/sync_client/sync_client.log
