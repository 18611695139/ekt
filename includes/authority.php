<?php

$menu['wincall']['name']    = '话务功能';
$menu['khgl']['name']	    = '客户管理';
$menu['wdzs']['name']       = '我的助手';
$menu['dxgl']['name']       = '短信管理';
$menu['kffw']['name']	    = '客服服务';
$menu['cpgl']['name']       = '产品管理';
$menu['ddgl']['name']       = '订单管理';
$menu['gdgl']['name']       = '工单管理';
$menu['lcgl']['name']       = '流程管理';
$menu['tjfx']['name']       = '统计分析';
$menu['monitor']['name']    = '系统监控';
$menu['xtgl']['name']       = '系统管理';
$menu['zsk']['name']    	= '知识库';

//话务管理
$menu['wincall']['children']['consult']		      = array('咨询坐席、咨询外线、咨询接回');
$menu['wincall']['children']['transfer']	      = array('转接');
$menu['wincall']['children']['threeway']	      = array('三方、三方接回');
$menu['wincall']['children']['evaluation']	      = array('转评价');

//客户管理
$menu['khgl']['children']['khglwdkh']		      = array('我的客户','index.php?c=client&m=my_client_list','menu_icon_client');
$menu['khgl']['children']['khglxygl']		      = array('客户管理','index.php?c=client','menu_icon_client');
$menu['khgl']['children']['khgldataimport']       = array('客户导入','index.php?c=data_import&impt_type=1','menu_icon_input');
$menu['khgl']['children']['khglzytp']		      = array('客户调配','index.php?c=client_resource','menu_icon_zytp');
$menu['khgl']['children']['power_batch_deploy']   = array('调配（批量分配）');
$menu['khgl']['children']['khglggkh']		      = array('公共客户','index.php?c=client_public','menu_icon_ggkh');
$menu['khgl']['children']['khglggkeqb']		      = array('公共客户（全部）');
$menu['khgl']['children']['khgldatadeal']         = array('数据处理','index.php?c=client_data_deal','menu_icon_datadeal');
$menu['khgl']['children']['khglhistory']          = array('历史客户','index.php?c=client_history','menu_icon');
$menu['khgl']['children']['power_add']            = array('客户（添加）');
$menu['khgl']['children']['power_update']         = array('客户（修改）');
$menu['khgl']['children']['power_delete']         = array('客户（删除）');
$menu['khgl']['children']['power_release']        = array('客户（放弃）');
$menu['khgl']['children']['power_export']         = array('客户（导出）');
$menu['khgl']['children']['power_phone']          = array('客户（号码显示）');
$menu['khgl']['children']['power_update_c_phone'] = array('客户（电话修改）');

//我的助手
$menu['wdzs']['children']['wdzswdxx']		      = array('我的消息','index.php?c=message','menu_icon_wdxx');
$menu['wdzs']['children']['wdzswdxx_sendxx']	  = array('发消息功能');
$menu['wdzs']['children']['wdzssendxxall']		  = array('发消息（全部）');
$menu['wdzs']['children']['wdzswdtu']		      = array('我的提醒','index.php?c=remind&m=list_remind','menu_icon_wdtu');
$menu['wdzs']['children']['wdzthjl']		      = array('通话记录','index.php?c=callrecords','menu_icon_thjl');
$menu['wdzs']['children']['power_callrecord']     = array('通话记录（全部）');
$menu['wdzs']['children']['wdzswjldgl']           = array('未接来电','index.php?c=missed_calls','menu_icon_wjldgl');
$menu['wdzs']['children']['power_wjld_department']= array('未接来电（分配）');
$menu['wdzs']['children']['wdzsgggl']             = array('公告管理','index.php?c=announcement','menu_icon_announcement');
$menu['wdzs']['children']['wdzsggbj']    		  = array('公告（编辑）');
$menu['wdzs']['children']['wdzstxl']              = array('通讯录','index.php?c=address_book&m=list_address_book','menu_icon_addressbook');
$menu['wdzs']['children']['wdzsgstxl']	  		  = array('通讯录(公司)');
$menu['wdzs']['children']['wdzsgrsz']             = array('个人设置','index.php?c=user&m=self_set_user','menu_icon_userset');
$menu['wdzs']['children']['wdzsbzxd']             = array('帮助向导','index.php?c=setting&m=user_setting','menu_icon_setting');

//短信管理
$menu['dxgl']['children']['smsrecords']           = array('短信管理','index.php?c=sms','menu_icon_smsrecords');
$menu['dxgl']['children']['massmessage']          = array('群发短信','index.php?c=sms&m=send_sms&group_sms=1','menu_icon_massmessage');
$menu['dxgl']['children']['smsmodelmanage']       = array('短信模板','index.php?c=sms&m=list_smsmodel','menu_icon_smsmodelmanage');
$menu['dxgl']['children']['sendsms']              = array('发短信功能');

//客服服务
$menu['kffw']['children']['khglservice']          = array('客服管理','index.php?c=service','menu_icon_service');
$menu['kffw']['children']['kffwserinsert']        = array('新建服务','index.php?c=service&m=add_service','menu_icon');
$menu['kffw']['children']['kffwseralldata']       = array('客服管理（全部）');
$menu['kffw']['children']['power_service_edit']   = array('客服（编辑）');
$menu['kffw']['children']['kffwserdelete']        = array('客服（删除）');
$menu['kffw']['children']['kffwserexport']        = array('客服（导出）');
$menu['kffw']['children']['kffwserview']          = array('客服信息（业务受理）');

//产品管理
$menu['cpgl']['children']['cpflgl']			      = array('产品分类','index.php?c=product&m=product_class','menu_icon_cpflgl');
$menu['cpgl']['children']['cpgllb']			      = array('产品管理','index.php?c=product&m=product_list','menu_icon_cpgllb');
$menu['cpgl']['children']['pltjcp']			      = array('产品导入','index.php?c=data_import&impt_type=2','menu_icon_input');
$menu['cpgl']['children']['power_view_product']   = array('产品（查看）');
$menu['cpgl']['children']['power_view_insert']    = array('产品（添加）');
$menu['cpgl']['children']['power_view_update']    = array('产品（编辑）');
$menu['cpgl']['children']['power_view_delete']    = array('产品（删除）');

//订单管理
$menu['ddgl']['children']['ddgllb']       	      = array('订单管理','index.php?c=order&m=order_list','menu_icon_ddgl');
$menu['ddgl']['children']['power_insert_order']   = array('添加订单','index.php?c=order&m=add_order','menu_icon_insert_order');
$menu['ddgl']['children']['ddtj']   			  = array('坐席订单统计','index.php?c=order&m=get_order_statistics_page','menu_icon_zxddtj');
$menu['ddgl']['children']['power_view_order']     = array('订单（查看）');
$menu['ddgl']['children']['power_update_order']   = array('订单（修改）');
$menu['ddgl']['children']['power_delete_order']   = array('订单（删除）');
$menu['ddgl']['children']['power_output_order']   = array('订单（导出）');
$menu['ddgl']['children']['power_client_order']   = array('订单信息（业务受理）');

//工单管理
$menu['gdgl']['children']['work_flow_list'] 	  = array('工单列表','index.php?c=work_flow&m=list_all','menu_icon_client');
$menu['gdgl']['children']['work_flow_deal'] 	  = array('待处理工单','index.php?c=work_flow&m=deal_list','menu_icon_client');
$menu['gdgl']['children']['work_flow_complete']   = array('已处理工单','index.php?c=work_flow&m=complete_list','menu_icon_client');
$menu['gdgl']['children']['work_flow_client']     = array('工单信息（业务受理）');

//流程管理
$menu['lcgl']['children']['form'] 	              = array('表单管理','index.php?c=form','menu_icon_client');
$menu['lcgl']['children']['flow'] 	              = array('流程管理','index.php?c=flow','menu_icon_client');

//统计分析
$menu['tjfx']['children']['tjfxtjs']	          =array('工作量统计','index.php?c=statistics&m=statistics_tree','menu_icon_bmtj');
$menu['tjfx']['children']['tjfxqbqx']	          =array('工作量统计(全部)');
$menu['tjfx']['children']['tywjld']	          	  =array('未接来电统计','index.php?c=statistics&m=statistics_missed_calls','menu_icon');
$menu['tjfx']['children']['tjcfbdsb']	          =array('通话重复数统计','index.php?c=statistics_call_repeat','menu_icon');

//系统监控
$menu['monitor']['children']['mqueue']            = array('技能组监控','index.php?c=monitor&m=queue','menu_icon_mqueue');
$menu['monitor']['children']['magent']            = array('坐席监控','index.php?c=monitor&m=agent','menu_icon_magent');
$menu['monitor']['children']['mcalls']            = array('排队监控','index.php?c=monitor&m=calls','menu_icon_mcalls');

//系统管理
$menu['xtgl']['children']['xtgljsgl']             = array('角色管理','index.php?c=role&m=edit_role','menu_icon_role');
$menu['xtgl']['children']['xtglbmgl']             = array('部门管理','index.php?c=department&m=edit_department','menu_icon_department');
$menu['xtgl']['children']['xtglyhgl']             = array('员工管理','index.php?c=user&m=list_user','menu_icon_user');
$menu['xtgl']['children']['xtglzdpz']             = array('字段配置','index.php?c=field_confirm&m=get_field_confirm_system_page','menu_icon');
$menu['xtgl']['children']['xtglszzd']             = array('数字字典','index.php?c=dictionary&m=get_dictionary_page','menu_icon');
$menu['xtgl']['children']['xtglparamset']         = array('参数设置','index.php?c=system_config','menu_icon_sysconfig');
$menu['xtgl']['children']['ztglsybz']             = array('设置向导','index.php?c=setting','menu_icon_setting');
$menu['xtgl']['children']['blacklist']            = array('黑名单设置','index.php?c=black','menu_icon');
$menu['xtgl']['children']['whitelist']            = array('白名单设置','index.php?c=white','menu_icon');
$menu['xtgl']['children']['busyset']              = array('置忙原因设置','index.php?c=busy','menu_icon');
$menu['xtgl']['children']['xtgllyxz']             = array('录音下载');

//$menu['xtgl']['children']['ztglbfhy']            = array('数据备份','index.php?c=backup_reset','menu_icon_sjkbf');
//$menu['xtgl']['children']['power_reset']         = array('还原');

$menu['zsk']['children']['power_zsk_view'] 	       = array('知识库（查看）');
$menu['zsk']['children']['power_zsk_insert'] 	   = array('知识库（添加）');
$menu['zsk']['children']['power_zsk_update'] 	   = array('知识库（编辑）');
$menu['zsk']['children']['power_zsk_delete'] 	   = array('知识库（删除）');
$menu['zsk']['children']['power_zsk_class'] 	   = array('知识库（栏目管理）');
