<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:12
         compiled from user_set.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="main-div">
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0"  style="width:100%;border-width:0px">
 <tbody >
    <tr>
		<td class="narrow-label" ><label for="name">姓名：</label></td>
		<td style="width: 30%"><input type='text' id='user_name' name='user_name' value='<?php echo $this->_tpl_vars['user_info']['user_name']; ?>
' /></td>
	</tr>
	<tr>
		<td class="narrow-label" ><label for="old_password">旧密码：</label></td>
		<td style="width: 30%"><input id="old_password" name="old_password" required="true" class="easyui-validatebox" type="password" value="" /></td>
	</tr>
	<tr>
		<td class="narrow-label" ><label for="password">新密码：</label></td>
		<td style="width: 30%"><input id="password" name="password" required="true" type="password" value=""  class="easyui-validatebox" validType="minLength[3]" /></td>
	</tr>
	<tr>
		<td class="narrow-label" ><label for="password_cp">重复新密码：</label></td>
		<td style="width: 30%"><input id="password_cp" name="password_cp" required="true" type="password" value="" class="easyui-validatebox"  validType="password_cp"/></td>
	</tr>
	<tr>
		<td class="narrow-label" ><label for="user_sms_phone">手机号码：</label></td>  
		<td style="width: 30%">
			<input id="user_sms_phone" name="user_sms_phone" type="text" value="<?php echo $this->_tpl_vars['user_info']['user_sms_phone']; ?>
" />
			<span class="help-inline">
				手机号码作用：1、用于创建提醒时添加短信提示；2、用于开启转手机功能。
			</span>
		</td> 
	</tr>
	</tbody>
</table>


	<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" id='user_advan_set_info' style="width:100%;border-width:0px;display:none;">
<tbody >
	<tr>
	<td colspan="2" align="center" height="50px" >-------------------以下功能修改后需 【<b>刷新系统</b>】 才能生效-------------------</td>
	</tr>
	<tr>
	<td class="narrow-label" ><label for="user_to_selfphone">转手机功能：</label></td>  
	<td style="width: 30%">
	<span title="注：转手机功能启用步骤： 步骤一：管理员后台启用; 步骤二：坐席本人前台个人设置填入手机号码，转手机功能选中开启。" class='easyui-tooltip' data-options="position:'right'">
	<input type="radio" name="user_to_selfphone" value="2" <?php if ($this->_tpl_vars['user_info']['user_to_selfphone'] == 2): ?>checked<?php endif; ?> /> 开启
	<input type="radio" name="user_to_selfphone" value="1" <?php if ($this->_tpl_vars['user_info']['user_to_selfphone'] == 1): ?>checked<?php endif; ?> /> 关闭
	</span>
	<span>&nbsp;&nbsp;(老客户来电，而所属坐席没登陆系统时，可转到坐席手机)</span>
	</td> 
	</tr>
	<tr>
	<td class="narrow-label" ><label for="user_phone_type">话机类型：</label></td>  
	<td style="width: 30%">
	<input type="radio" name="user_phone_type" value="1" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 1): ?>checked<?php endif; ?> /> 自动识别 
	<input type="radio" name="user_phone_type" value="2" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 2): ?>checked<?php endif; ?> /> 软电话
	<input type="radio" name="user_phone_type" value="3" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 3): ?>checked<?php endif; ?>  /> 话机
	<span>&nbsp;&nbsp;(自动识别：坐席电话长度少于5为软电话，大于等于5为话机)</span>
	</td> 
	</tr>
	  <tr>
       <td  class="narrow-label"  align="right">坐席登录状态：</td>
       <td>
        <input type="radio"  name="user_login_state" value="1" <?php if ($this->_tpl_vars['user_info']['user_login_state'] == 1): ?>checked <?php endif; ?> /> 空闲
		<input  type="radio"  name="user_login_state" value="2" <?php if ($this->_tpl_vars['user_info']['user_login_state'] == 2): ?>checked<?php endif; ?>  /> 忙碌
       </td>
     </tr>
    <tr>
	<td class="narrow-label" ><label for="user_if_tip">来电/挂机提示：</label></td>  
	<td style="width: 30%">
	<input type="radio" name="user_if_tip" value="1" <?php if ($this->_tpl_vars['user_info']['user_if_tip'] == 1 || $this->_tpl_vars['user_info']['if_tip'] == 0): ?>checked<?php endif; ?> /> 是
	<input type="radio" name="user_if_tip" value="2" <?php if ($this->_tpl_vars['user_info']['user_if_tip'] != 1): ?>checked<?php endif; ?>  /> 否
	</td> 
	</tr>
	<tr style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">常接通：</label></td> 
	 <td>
		<a href='###' ><input type="radio" onclick='is_ol_phone()' name="ofen_phone" value="1"   <?php if ($this->_tpl_vars['user_info']['user_ol_model'] != 0): ?>checked<?php endif; ?>/></a> 是
		<a href='###' ><input type="radio" onclick='no_ol_phone()' name="ofen_phone"  value="2" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>checked<?php endif; ?>/></a> 否
		<span>&nbsp;&nbsp;(系统会保持和坐席电话的长时间接通状态。例如：挂机时只是挂断客户电话，坐席仍和系统保持接通状态。)</span>
      </td>
	</tr>
	<tr style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">自动接听：</label></td> 
	 <td> 
	 	<span id='free_phone_content'>
		<input type="radio" name="free_phone"  value="1" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>disabled<?php elseif ($this->_tpl_vars['user_info']['user_ol_model'] == 2): ?>checked<?php endif; ?>/> 是
		<input type="radio" name="free_phone"  value="2" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>checked disabled<?php elseif ($this->_tpl_vars['user_info']['user_ol_model'] != 2): ?>checked<?php endif; ?>/> 否
		</span>
      </td>
	</tr>
     <tr>
         <td  class="narrow-label"  align="right"><label for="user_display_dialpanel" >外呼弹窗确认：</label></td> 
         <td> 
		 <input type="radio" name="user_display_dialpanel" value="1" <?php if ($this->_tpl_vars['user_info']['user_display_dialpanel'] == 1): ?>checked<?php endif; ?>/> 是
		 <input type="radio" name="user_display_dialpanel" value="2" <?php if ($this->_tpl_vars['user_info']['user_display_dialpanel'] != 1): ?>checked<?php endif; ?>/> 否
		 <span>&nbsp;&nbsp;(点击外呼图标外呼时弹出外呼详情窗口)</span>
         </td>
     </tr>
       <tr>
         <td  class="narrow-label"  align="right"><label for="user_outcall_popup" >外呼弹屏：</label></td> 
         <td>
		 <input type="radio" name="user_outcall_popup" value="1" <?php if ($this->_tpl_vars['user_info']['user_outcall_popup'] == 1): ?>checked<?php endif; ?>/> 是
		 <input type="radio" name="user_outcall_popup" value="2" <?php if ($this->_tpl_vars['user_info']['user_outcall_popup'] != 1): ?>checked<?php endif; ?>/> 否
		 <span>&nbsp;&nbsp;(外呼时根据外呼号码弹出业务受理界面)</span>
         </td>
     </tr>
	 </tbody>
</table>

     <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:100%;border-width:0px;padding:10px;">
	<tr>
	 <td align="center" colSpan="2">
	 <button type="button" class="btn btn-primary"  onclick="submit_theform();">
    	<span class="glyphicon glyphicon-saved"></span> 保存设置
	 </button>
	 <img id="loading" src="./image/loading.gif" style="display:none;">
	 <a href="###" id="advan_set_user_but" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-seting'" title="高级设置" onclick="advan_setting();" >高级设置</a>
	 </td>
	</tr>
 </tbody>
</table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/viewjs/user_set.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var dept_id = <?php echo $this->_tpl_vars['user_info']['dept_id']; ?>
;

function is_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1' <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 2): ?>checked<?php endif; ?>/>是 <input type='radio' name='free_phone'  value='2' <?php if ($this->_tpl_vars['user_info']['user_ol_model'] != 2): ?>checked<?php endif; ?>/>否");
}

function no_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1' disabled />是 <input type='radio' name='free_phone'  value='2' checked disabled />否");
}
</script>