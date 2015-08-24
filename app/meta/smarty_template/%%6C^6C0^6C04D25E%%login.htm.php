<?php /* Smarty version 2.6.19, created on 2015-07-10 10:32:24
         compiled from login.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="themes/default/login.css" rel="stylesheet" type="text/css" />
<link href="themes/default/easyui.css" rel="stylesheet" type="text/css" />
<script src="./jssrc/jquery.js" type="text/javascript"></script>  
<script src="./jssrc/jquery.easyui.min.js" type="text/javascript"></script>
<script src="./jssrc/easyui-lang.js" type="text/javascript"></script>
<script src="./jssrc/common.js" type="text/javascript"></script>
<script src="./jssrc/md5min.js" type="text/javascript"></script>  
<script language="JavaScript" type="text/javascript">
var clear_prompt;

$(document).ready(function(){
	//添加事件
	$("#btn_signin").click(function(){
		signin();
	});

	$('#captcha').keydown(function(event){
		if(event.keyCode == 13){
			signin();
		}
	});

	get_captcha();

	//设置焦点
	if(!$('#vcc_code').val()){
		$('#vcc_code').focus();
	}
	else{
		$('#password').focus();
	}
});

//获取验证码
function get_captcha(){
	$.ajax({
		url:  'index.php?c=login&m=get_captcha',
		dataType:"json",
		cache:false,
		success: function(responce){
			if(responce['error']=='0'){
				$('#captcha_img').html(responce['content']);
			}
			else{
				$('#captcha_img').html("<font color='red'>获取验证码失败</font>");
			}
		}
	});
}

//保存
var _signd = false;
function signin(){
	if(!$('#vcc_code').validatebox('isValid'))
	return false;
	if(!$('#user_num').validatebox('isValid'))
	return false;
	if(!$('#password').validatebox('isValid'))
	return false;

	var _data = {};
	_data.vcc_code = $('#vcc_code').val();
	_data.user_num = $('#user_num').val();
	_data.password = hex_md5($('#password').val());
	_data.pho_num = $('#pho_num').val();
	_data.captcha = $('#captcha').val();

	$('#btn_signin').css('disabled',true);
	$.ajax({
		url:  'index.php?c=login&m=signin',
		data:_data,
		type:'post',
		dataType:"json",
		cache:false,
		success: function(responce){
			if(responce['error']=='0')
			{
				_signd = true;
				$('#msg').html('');
				$('#msg').next().html("<font color='blue'>登陆中，请稍后。。。<img src='./image/loading.gif' align='absmiddle' /></font>");
				est_header("location:index.php?c=main");
			}
			else
			{
				//处理异步返回提示慢的问题
				if(_signd)
				{
					return false;
				}
				$('#btn_signin').css('disabled',false);
				$('#msg').html("<font color='red'>"+responce['message']+"</font>");
				clearTimeout(clear_prompt);
				clear_prompt = setTimeout(function(){$('#msg').html('');},3000);
				get_captcha();
			}
		}
	});
}
</script>
</head>
<body>

<div id="panel" align="center">
<div id="log_table">
	<table border="0px" style="width:100%;">
		<tr style="height:242px">
			<td colspan="2"> </td>
		</tr>
		<tr>
			<th><label for="vcc_code"><?php if ($this->_tpl_vars['saas_version']): ?>企业账号：<?php endif; ?></label></th>
			<td align="left"><input type="<?php if ($this->_tpl_vars['saas_version']): ?>text<?php else: ?>hidden<?php endif; ?>" id="vcc_code" name="vcc_code" maxlength="50"  class="easyui-validatebox" required="true" value="<?php echo $this->_tpl_vars['vcc_code']; ?>
"  /></td>
		</tr>
		<tr>
			<th><label for="user_num">坐席工号：</label></th>
			<td align="left"><input type="text" id="user_num" name="user_num" maxlength="50" " class="easyui-validatebox" required="true" value="<?php echo $this->_tpl_vars['user_num']; ?>
"  /></td>
		</tr>
		<tr>
			<th><label for="pho_num">坐席电话：</label></th>
			<td align="left"><input type="text" id="pho_num" name="pho_num" maxlength="50" value="<?php echo $this->_tpl_vars['pho_num']; ?>
" /></td>
		</tr>
		<tr>
			<th><label for="password_txt">坐席密码：</label></th>
			<td align="left" style="padding:3px 0px 0px 1px;"><input type="password" id="password" name="password" maxlength="50"  class="easyui-validatebox" required="true" value=""  /></td>
		</tr>
   		 <tr>
    		<th>验证码：</th>
    		<td align="left" style="vertical-align: middle;padding:4px 0px 0px 1px;">
    			<input type="text" style="width: 85px;" id="captcha" name="captcha" maxlength="50" class="easyui-validatebox" />
    			<span id="captcha_img" title="看不清楚？点击更换另一个验证码" style="cursor: pointer;padding-left:9px;" onclick="get_captcha()"></span>
    		</td>
    	</tr>
		<tr>
			<td align="right" id="msg" style="line-height:20px;padding-top:12px;" ></td>
			<td style="color: #444444;line-height:20px;padding-top:12px;" align="left">
				<a href="###" id="btn_signin"><div id="login_button"></div></a>
<!--				<input type="button" id="btn_signin" />-->
			</td>
		</tr>
	</table>
</div>
</div>
<!--<hr style="margin:20px;"/>-->
<div id="copy_right"><?php echo $this->_tpl_vars['copyright']; ?>
</div>
</body>
</html>