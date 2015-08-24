<?php /* Smarty version 2.6.19, created on 2015-08-07 09:19:24
         compiled from user_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'user_add.htm', 19, false),array('modifier', 'default', 'user_add.htm', 25, false),)), $this); ?>
<div class="main-div" style='background:#ffffff;'>
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" align="center"  style="width:98%">
 <tbody>
	 <tr>
         <td class="narrow-label"  align="right"><label for="user_num">工号：</label></td>		           
         <td style="width: 50%"><input type='text' id='user_num' name='user_num' value='' onblur='check_num()' onclick='on_num()'/> <span class="require-field" id='num_message'>*</span></td>          
      </tr>
      <tr>
         <td class="narrow-label" style="width:30%" align="right"><label for="user_name">姓名：</label></td>		           
         <td style="width: 50%"><input type='text' id='user_name' name='user_name' value='' class="easyui-validatebox" missingMessage="此项不能为空" required="true" /> <span class="require-field">*</span></td>          
      </tr>
      <tr>
      	<td class="narrow-label" style="width:30%"  align="right"><label for="user_password">密码：</label></td>
      	<td style="width: 50%"><input type='password' id='user_password' name='user_password' value='123456' class="easyui-validatebox" validType="minLength[3]"/> <span class="require-field">密码默认是123456</span></td>
      </tr>
      <tr>
          <td class="narrow-label" align="right"><label for="role">角色：</label></td>
          <td style="width: 50%">
           <select name="role" id="role"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['roles'],'selected' => $this->_tpl_vars['user_info']['role_id']), $this);?>
</select>
         <span class="require-field">*</span>
          </td>
     </tr>
     <tr>
         <td  class="narrow-label"  align="right">部门：</td> 
         <td style="width: 50%"><input id="dept_id" name="dept_id" class="easyui-combotree" url="index.php?c=department&m=get_department_tree"  required="true" style="width:182px;" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['session_user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
" ><span class="require-field">*</span> <input type='hidden' id='dept_name' name='dept_name' value='' /></td>
     </tr>
      </tbody>
</table>

     <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" id='user_advan_set_info' align="center"  style="width:98%;display:none;">
 <tbody>
      <tr>
         <td  class="narrow-label"  align="right">队列：</td> 
         <td style="width: 30%">
         	<select id="user_que" name="user_que" >
				<?php $_from = $this->_tpl_vars['que_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['que']):
?>
					<option value='<?php echo $this->_tpl_vars['que']['que_id']; ?>
'><?php echo $this->_tpl_vars['que']['que_name']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
         </td>
     </tr>
 	
     <tr>
       <td  class="narrow-label"  align="right">坐席登录状态：</td>
       <td style="width: 30%">
        <input type="radio"  name="user_login_state" value="1"  checked/>空闲
		<input  type="radio"  name="user_login_state" value="2"   />忙碌
       </td>
     </tr>
    
      <tr>
         <td  class="narrow-label"  align="right">外呼主叫号码：</td> 
         <td style="width: 30%"> 
         <input type="radio"  name="user_outcaller_type" value="0" onclick="change_oucaller_type_add(0)" checked/>全部
		<input  type="radio"  name="user_outcaller_type" value="1"  onclick="change_oucaller_type_add(1)" />指定
		<input   id="user_outcaller_num" name="outcaller" value="" disabled/>
         </td>
     </tr>
      <tr>
         <td  class="narrow-label"  align="right"><label for="user_tel_server" class='easyui-tooltip' title='通讯服务器只有在使用软电话时才有用' data-options="position:'left'">通讯服务器：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" name="user_tel_server" id="user_tel_server" value="">
         </td>
     </tr>
     <tr>
         <td  class="narrow-label"  align="right"><label for="user_cti_server">中间件服务器：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" name="user_cti_server" id="user_cti_server" value="">
         </td>
     </tr>
     <tr>
         <td  class="narrow-label"  align="right"><label for="user_channel">坐席外呼通道：</label></td> 
         <td style="width: 30%"> 
      	  <input type="text" name="user_channel" id="user_channel" value="0">
         </td>
     </tr>
   <tr>
		<td class="narrow-label" ><label for="user_phone_type">话机类型：</label></td>  
		<td style="width: 30%">
            <input type="radio" name="user_phone_type" value="3" checked />话机
			<span class='easyui-tooltip' title='坐席电话长度少于5为软电话，大于等于5为话机'><input type="radio" name="user_phone_type" value="1" />自动识别</span>
			<input type="radio" name="user_phone_type" value="2" />软电话

		</td> 
	</tr>
     <tr  style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">常接通：</label></td> 
	 <td style="width: 30%"> 
		<a href='###' ><input type="radio" onclick='is_ol_phone()' name="ofen_phone" value="1"/></a>是
		<a href='###' ><input type="radio" onclick='no_ol_phone()' name="ofen_phone"  value="2" checked/></a>否
      </td>
	</tr>
	<tr  style='display:none;'>
	 <td  class="narrow-label"  align="right"><label for="">自动接听：</label></td> 
	 <td style="width: 30%"> 
	 <span id='free_phone_content'>
		<input type="radio" name="free_phone"  value="1" />是
		<input type="radio" name="free_phone"  value="2" checked />否
		</span>
      </td>
	</tr>
 </tbody>
</table>
<div style="text-align:right;padding-right:20px">
	<a class="easyui-linkbutton" iconCls="icon-seting" href="javascript:void(0)" onclick="advan_setting()" id='advan_set_user__but'>高级设置</a>
	<a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)" onclick="save_user_add()"  id="submit_user_btn">添加</a>
</div>
</div>
<script src="./jssrc/viewjs/user_add.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
function is_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1'checked/>是 <input type='radio' name='free_phone'  value='2' />否");
}

function no_ol_phone()
{
	$('#free_phone_content').html("<input type='radio' name='free_phone'  value='1' disabled />是 <input type='radio' name='free_phone'  value='2' checked disabled />否");
}

</script>