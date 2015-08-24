<?php /* Smarty version 2.6.19, created on 2015-08-17 11:04:19
         compiled from index.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'index.htm', 19, false),)), $this); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title><?php echo $this->_tpl_vars['sys_title']; ?>
</title>
<link href='themes/default/easyui.css' rel='stylesheet' type='text/css' />
<link href='themes/default/icon.css' rel='stylesheet' type='text/css' />
<link href='themes/default/index.css' rel='stylesheet' type='text/css' />
<script src='./jssrc/jquery.js' type='text/javascript'></script>  
<script src='./jssrc/jquery.easyui.min.js' type='text/javascript'></script>
<script src='./jssrc/json2.js' type='text/javascript'></script>
<script src='./jssrc/common.js?1.1' type='text/javascript'></script>
<script src='./jssrc/jquery.wincall.js?20150817' type='text/javascript'></script>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language='JavaScript' type='text/javascript'>
var power_phone_view = '<?php echo $this->_tpl_vars['power_phone_view']; ?>
'; //是否显示号码
var user_if_tip = '<?php echo $this->_tpl_vars['user_info']['user_if_tip']; ?>
';//来电/客户挂机，右下角弹出提示窗口
var user_display_dialpanel = '<?php echo $this->_tpl_vars['user_info']['user_display_dialpanel']; ?>
';//是否显示外呼窗口
var user_outcaller_type = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_outcaller_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//外呼主叫号码类型
var user_outcaller_num = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_outcaller_num'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//指定的外呼主叫
var user_cti_server = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_cti_server'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//中间件服务器
var vcc_code = '<?php echo $this->_tpl_vars['user_info']['vcc_code']; ?>
';
var user_num = '<?php echo $this->_tpl_vars['user_info']['user_num']; ?>
';//员工工号
var user_password = '<?php echo $this->_tpl_vars['user_info']['user_password']; ?>
';
var user_phone = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var user_login_state = '<?php echo $this->_tpl_vars['user_info']['user_login_state']; ?>
';//坐席登录状态
var user_ol_model = '<?php echo $this->_tpl_vars['user_info']['user_ol_model']; ?>
';//常接通模式
var user_phone_type = "<?php echo $this->_tpl_vars['user_info']['user_phone_type']; ?>
";//话机类型
var user_tel_server = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_tel_server'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//通讯服务器IP
var user_outcall_popup = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_outcall_popup'])) ? $this->_run_mod_handler('default', true, $_tmp, '2') : smarty_modifier_default($_tmp, '2')); ?>
";//外呼弹屏
var tel_server_port = '<?php echo $this->_tpl_vars['tel_server_port']; ?>
';//通讯服务器端口
var sip_prefix = '<?php echo $this->_tpl_vars['sip_prefix']; ?>
';//软电话号码前缀
var system_connect_type = '<?php echo $this->_tpl_vars['system_connect_type']; ?>
'; //通道连接方式 swf：flash  amq：消息队列（网络条件不好的时候用）
var admin_login_system = '<?php echo $this->_tpl_vars['admin_login_system']; ?>
';//设置向导 - 弹屏
var user_channel = '<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_channel'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
';//外呼通道
var pop_address = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['pop_address'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//弹屏地址
//技能组(队列)信息
var que_list	= []; 
<?php $_from = $this->_tpl_vars['que_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['que_item']):
?>
que_list[<?php echo $this->_tpl_vars['que_item']['que_id']; ?>
] = '<?php echo $this->_tpl_vars['que_item']['que_name']; ?>
';
<?php endforeach; endif; unset($_from); ?>
//置忙原因
var busy = [];
<?php $_from = $this->_tpl_vars['busy']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info']):
?>
busy[<?php echo $this->_tpl_vars['dkey']; ?>
] = '<?php echo $this->_tpl_vars['info']['id']; ?>
';
<?php endforeach; endif; unset($_from); ?>
</script>
<script src="./jssrc/viewjs/index.js?1.4" type="text/javascript"></script>
</head>
<body class='easyui-layout'>
<?php if ($this->_tpl_vars['system_connect_type'] == 'swf'): ?>
<object  width='0' height='0' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0' id='wincall' align='middle'>
<param name='allowScriptAccess' value ='always' />
<param name='allowFullScreen' value='false' />
<param name='movie' value='./charts/wincall400.swf' />
<param name='quality' value='high' />
<param name='bgcolor' value='#ffffff' />	
<embed src='./charts/wincall400.swf' width='0' height='0' id='wincall_emded'  quality='high' name='wincall' align='middle' allowScriptAccess='sameDomain' allowFullScreen='false' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />
</object>
<?php endif; ?>
<object style="height:0px;width:0px;overflow:hidden;" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="0" height="0" id="asound" align="absmiddle">
	<param name="allowScriptAccess" value="./charts/sameDomain" />
	<param name="movie" value="./charts/asound.swf" />
	<param name="quality" value="high" />
	<embed src="./charts/asound.swf" quality="high" width="0" height="0" name="asound" align="absmiddle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

<div region='north' align='left' title="<div id='state-div' ><ul><li style='BORDER-LEFT-STYLE: none;'>坐席号:<?php echo $this->_tpl_vars['user_info']['user_num']; ?>
</li><li>分机号:<?php echo $this->_tpl_vars['user_info']['user_phone']; ?>
</li><li>坐席状态:<span id='seat_state'>未签入</span></li><li>队列状态:<span id='queue_state'>未签入</span></li><li id='caller_loc' style='color:black;'></li><li id='autocall_stat' style='display:none'>自动外呼</li><li><span id='phone_error' style='color:red;display:none'>&nbsp;</span></li></ul></div><div id='submenu-div'><ul><li><a href='index.php?c=login&m=signout'>退出</a></li><li><a id='refresh' href='javascript:void(0)'>刷新</a></li><?php if ($this->_tpl_vars['power_zsk_view']): ?><li ><a id='zsk'  href='#'>知识库</a></li><?php endif; ?><?php if ($this->_tpl_vars['power_sendxx']): ?><li><a id='btn_message' href='#'>发信息</a></li><?php endif; ?><li><a id='add_remind' href='#'>提醒</a></li></li><li id='msg' style='BORDER-LEFT-STYLE: none;color:red;'></li></ul></div>"  style='height:74px;padding:0px;overflow:hidden;'>
 <div id='toolbar' style='text-align:right;'>
		<div><input id='login' type='button'  value='签入' disabled /></div>
		<div><input id='unbusy' type='button' value='空闲' disabled /></div>
		<?php if ($this->_tpl_vars['busy']): ?>
		<?php $_from = $this->_tpl_vars['busy']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info']):
?>
			<div style='width:65px;'><input id='busy<?php echo $this->_tpl_vars['info']['id']; ?>
' class='busy' type='button'  value='<?php echo $this->_tpl_vars['info']['stat_reason']; ?>
' disabled /></div>
		<?php endforeach; endif; unset($_from); ?>
		<?php else: ?>
		<div style='width:65px;'><input id='busy' type='button'  value='忙碌' disabled /></div>
		<?php endif; ?>
		<div><input id='callinner' type='button'  value='内呼' disabled /></div>
		<div><input id='callouter' type='button'  value='外呼' disabled /></div>
        <div style='width:65px;'><input id='hold' type='button' value='保持' disabled /></div>
		<?php if ($this->_tpl_vars['consult']): ?>
		<div><input id='consultinner' type='button'  value='咨询坐席' disabled /></div>
		<div><input id='consultouter' type='button'  value='咨询外线' disabled /></div>
		<div><input id='consultback' type='button'  value='咨询接回' disabled /></div>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['transfer']): ?>
		<div><input id='transfer' type='button'  value='转接' disabled /></div>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['threeway']): ?>
		<div><input id='threeway' type='button'  value='三方' disabled /></div>
		<div style='width:65px;'><input id='threewayback' type='button'  value='三方接回' disabled /></div>
		<?php endif; ?>
        <?php if ($this->_tpl_vars['evaluation']): ?>
        <div style='width:65px;'><input id='evaluation' type='button'  value='满意度' disabled /></div>
        <?php endif; ?>
        <div style='width:65px;'><input id='hangup' type='button'  value='挂断' disabled /></div>
        <?php if ($this->_tpl_vars['use_transque']): ?>
        <div><input id='transque' type='button' value='转技能组' disabled /></div>
        <?php endif; ?>
 </div>
</div>
<div region='west' style='width:200px;overflow:hidden;'>
	<div class='easyui-accordion'  id='mymenu' fit='true' border='false'>
		<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['parent']):
?>
			<div id='mymenu_div' title="<span  class='menu_text'><?php echo $this->_tpl_vars['parent']['name']; ?>
</span>" icon='icon_menu'  style='overflow:auto;'>
				<?php $_from = $this->_tpl_vars['parent']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
				<a href='#' class='menu_item' url="<?php echo $this->_tpl_vars['item'][1]; ?>
"><span class="<?php echo $this->_tpl_vars['item'][2]; ?>
" >&nbsp;</span><?php echo $this->_tpl_vars['item'][0]; ?>
</a>
				<?php endforeach; endif; unset($_from); ?>
			</div>
		<?php endforeach; endif; unset($_from); ?>
	</div>
</div>	
<div region='center'  style='overflow:hidden;'>
	<div id='tabs' class='easyui-tabs' fit='true'  border='false'>
		<div title='工作桌面'>
		<iframe name='mainFrame' frameborder='0' src='index.php?c=workbench' scrolling='auto' style='width:100%;height:99%;'></iframe>
		</div>
	</div>
</div>
<div id='dail_panel'></div>
<div id='transfer_panel'></div>
<div id='message_panel'></div>
<div id='view_remind'></div>
<div id="set_remind"></div>
<div id='keypad_panel'></div>
<div id='busy_reason'></div>
<div id='transque_panel'></div>
<div id="monitor_agent_chanspy"></div><!--监听弹窗-->
<div id='ring_panel' style='text-align:center;vertical-align:middle;padding-top:15px;color:red;font-size:25px;letter-spacing:2px;'><span id='callin_number'></span><br><span id='calling_loc'></span></div>
<div id='index_loading' style="position:absolute;z-index:1000;top:0px;left:0px;width:100%;height:100%;background:#DDEEF2;text-align:center;padding-top: 20%;"><h1><img style="vertical-align:top;" src='./image/load.gif' /><font color="#15428B" style="padding-left:30px;">Loading···</font></h1></div>
<div id='setting_background'><div id='setting_panel'><div></div><!--设置向导-->
</body>
<script type="text/javascript">
    var left_day = '<?php echo ((is_array($_tmp=@$this->_tpl_vars['left_day'])) ? $this->_run_mod_handler('default', true, $_tmp, false) : smarty_modifier_default($_tmp, false)); ?>
';
    if (left_day) {
        $.messager.show({
            title:'时间提醒',
            msg:'您使用的系统还有<span style="color: red">'+left_day+'</span>天即将到期',
            showType:'slide',
            timeout:0
        });
    }
</script>
</html>