<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:50
         compiled from user_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'user_info.htm', 29, false),array('modifier', 'default', 'user_info.htm', 35, false),)), $this); ?>
<div class="main-div" style='background:#ffffff;'>
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" align="center"  style="width:98%">
 <tbody>
	 <tr>
         <td class="narrow-label"  align="right"><label for="user_num">工号：</label></td>		           
         <td style="width: 30%">&nbsp;&nbsp;<?php echo $this->_tpl_vars['user_info']['user_num']; ?>
</td>          
      </tr>
      <tr>
         <td class="narrow-label"  align="right"><label for="user_name">姓名：</label></td>		           
         <td style="width: 30%"><input type='text' id='user_name' name='user_name' value='<?php echo $this->_tpl_vars['user_info']['user_name']; ?>
' class="easyui-validatebox" missingMessage="此项不能为空" required="true" /></td>         
      </tr>
     
	<tr>
	<td class="narrow-label" ><label for="password">新密码：</label></td>
	<td style="width: 30%"><input id="password" name="password"  type="password" size = "25" value=""  class="easyui-validatebox" validType="minLength[3]" /></td>
	</tr>
	
      <tr>
          <td class="narrow-label" align="right"><label for="role">角色：</label></td>
          <td style="width: 30%">
           <select name="role" id="role">
 	 		<?php $_from = $this->_tpl_vars['roles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['role_id'] => $this->_tpl_vars['roles_info']):
?>
 	 		<option value="<?php echo $this->_tpl_vars['role_id']; ?>
"  <?php if ($this->_tpl_vars['user_info']['role_id'] == $this->_tpl_vars['role_id']): ?> SELECTED <?php endif; ?>   ><?php echo $this->_tpl_vars['roles_info']; ?>
</option>
 	 		<?php endforeach; endif; unset($_from); ?>
 		   </select>
          
          
         <!--  <select name="role" id="role">
           <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['roles'],'selected' => $this->_tpl_vars['user_info']['role_id']), $this);?>
</select>-->
         <span class="require-field">*</span>
          </td>
     </tr>
     <tr>
         <td  class="narrow-label"  align="right">部门：</td> 
         <td style="width: 30%"><input id="dept_id" name="dept_id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['dept_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
" class="easyui-validatebox" missingMessage="此项不能为空" required="true" style="width:182px;"  ><span class="require-field">*</span>  </td>
     </tr>
        </tbody>
</table>

     <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" id='user_advan_set_info' align="center"  style="width:98%;display:none;">
 <tbody>
      <tr>
       <td  class="narrow-label"  align="right">坐席登录状态：</td>
       <td style="width: 30%">
        <input type="radio"  name="user_login_state" value="1"  <?php if ($this->_tpl_vars['user_info']['user_login_state'] == 1): ?>checked<?php endif; ?> />空闲
		<input  type="radio"  name="user_login_state" value="2" <?php if ($this->_tpl_vars['user_info']['user_login_state'] == 2): ?>checked<?php endif; ?> />忙碌
       </td>
     </tr>
      <tr>
         <td  class="narrow-label"  align="right">外呼主叫号码：</td> 
         <td style="width: 30%"> 
         <input type="radio"  name="user_outcaller_type" value="0" onclick="change_oucaller_type(0)" <?php if ($this->_tpl_vars['user_info']['user_outcaller_type'] == 0): ?>checked<?php endif; ?> />全部
		<input  type="radio"  name="user_outcaller_type" value="1"  onclick="change_oucaller_type(1)" <?php if ($this->_tpl_vars['user_info']['user_outcaller_type'] == 1): ?>checked<?php endif; ?> />指定
		<input  <?php if ($this->_tpl_vars['user_info']['user_outcaller_type'] == 0): ?> disabled <?php endif; ?>  id="user_outcaller_num" name="outcaller" value="<?php echo $this->_tpl_vars['user_info']['user_outcaller_num']; ?>
"/>
         </td>
     </tr>
      <tr>
         <td  class="narrow-label"  align="right"><label for="user_tel_server" class='easyui-tooltip' title='通讯服务器只有在使用软电话时才有用' data-options="position:'left'">通讯服务器：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" id="user_tel_server" value="<?php echo $this->_tpl_vars['user_info']['user_tel_server']; ?>
">
         </td>
     </tr>
     <tr>
         <td  class="narrow-label"  align="right"><label for="user_cti_server">中间件服务器：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" name="user_cti_server" id="user_cti_server" value="<?php echo $this->_tpl_vars['user_info']['user_cti_server']; ?>
">
         </td>
     </tr>
       <tr>
         <td  class="narrow-label"  align="right"><label for="user_channel">坐席外呼通道：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" name="user_channel" id="user_channel" value="<?php echo $this->_tpl_vars['user_info']['user_channel']; ?>
">
         </td>
     </tr>
     <tr>
	<td class="narrow-label" ><label for="user_phone_type">话机类型：</label></td>  
	<td style="width: 30%">
	<span class='easyui-tooltip' title='坐席电话长度少于5为软电话，大于等于5为话机'>
	<input type="radio" name="user_phone_type" value="1" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 1): ?>checked<?php endif; ?> />自动识别</span>
	<input type="radio" name="user_phone_type" value="2" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 2): ?>checked<?php endif; ?> />软电话
	<input type="radio" name="user_phone_type" value="3" <?php if ($this->_tpl_vars['user_info']['user_phone_type'] == 3): ?>checked<?php endif; ?>  />话机
	</td> 
	</tr>
    <tr style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">常接通：</label></td> 
	 <td> 
		<a href='###' ><input type="radio" onclick='is_ol_phone()' name="ofen_phone" value="1"   <?php if ($this->_tpl_vars['user_info']['user_ol_model'] != 0): ?>checked<?php endif; ?>/></a>是
		<a href='###' ><input type="radio" onclick='no_ol_phone()' name="ofen_phone"  value="2" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>checked<?php endif; ?>/></a>否
      </td>
	</tr>
	<tr style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">自动接听：</label></td> 
	 <td> 
	 	<span id='free_phone_content'>
		<input type="radio" name="free_phone"  value="1" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>disabled<?php elseif ($this->_tpl_vars['user_info']['user_ol_model'] == 2): ?>checked<?php endif; ?>/>是
		<input type="radio" name="free_phone"  value="2" <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 0): ?>checked disabled<?php elseif ($this->_tpl_vars['user_info']['user_ol_model'] != 2): ?>checked<?php endif; ?>/>否
		</span>
      </td>
	</tr>
 </tbody>
</table>
</div>
<div style="text-align:right;padding-top:10px;padding-right:20px">
<span id='_user_msg' style='color:red;' ></span>
<a class="easyui-linkbutton" iconCls="icon-seting" href="javascript:void(0)" onclick="advan_setting()" id='advan_set_user__but'>高级设置</a>
	<a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="save_user()"  id="submit_user_btn">保存</a>
<!--	<a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)" onclick="cancel_user()">取消</a>-->
</div>
<div style="padding-top:20px;"> </div>
<script language="JavaScript" type="text/javascript">
var user_session_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var role_session_id = <?php echo $this->_tpl_vars['role_session_id']; ?>
;//当前登陆系统的坐席
var _role_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['role_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;//要编辑的坐席
var _dept_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['dept_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var _dept_name = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_info']['dept_name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
function is_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1' <?php if ($this->_tpl_vars['user_info']['user_ol_model'] == 2): ?>checked<?php endif; ?>/>是 <input type='radio' name='free_phone'  value='2' <?php if ($this->_tpl_vars['user_info']['user_ol_model'] != 2): ?>checked<?php endif; ?>/>否");
}

function no_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1' disabled />是 <input type='radio' name='free_phone'  value='2' checked disabled />否");
}
</script>
<script src="./jssrc/viewjs/user_info.js?1.1" type="text/javascript"></script>