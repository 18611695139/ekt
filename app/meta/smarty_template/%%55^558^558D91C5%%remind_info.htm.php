<?php /* Smarty version 2.6.19, created on 2015-08-06 10:20:59
         compiled from remind_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'remind_info.htm', 2, false),array('function', 'html_select_time', 'remind_info.htm', 9, false),)), $this); ?>
<div style="background:#fff;border:1px solid #ccc;">
<input type='hidden' size='25' id='cle_name' name='cle_name' value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"/>
 	<table borderColor="#ffffff" cellSpacing="0" cellPadding="1"  align="center" border="1">
     <tbody>
        <tr>
            <td class="micro-label" >提醒时间：</td>
            <td>
            <span class="combo datebox" style="width: 115px;"><input type="text" class="combo-text validatebox-text easyui-validatebox" id='rmd_time' name='rmd_time' readonly style="width: 95px;" value="<?php echo $this->_tpl_vars['remind_info']['rmd_time']; ?>
"><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'rmd_time',minDate:'<?php echo $this->_tpl_vars['remind_info']['rmd_time']; ?>
',readOnly:'true' })" ></span></span></span>
            &nbsp; 时间：<?php echo smarty_function_html_select_time(array('use_24_hours' => true,'display_seconds' => false,'minute_interval' => 10,'prefix' => 'rmd_'), $this);?>

            </td>
       </tr>   
       <tr>
	       <td class="micro-label" >短信提示：</td><td> <input type="checkbox" name='rmd_sendsms' id='rmd_sendsms' value="1" <?php if ($this->_tpl_vars['remind_info']['user_sms_phone'] == ''): ?> disabled <?php else: ?> CHECKED <?php endif; ?> /> <?php echo $this->_tpl_vars['remind_info']['user_sms_phone']; ?>
 <span style="color: red;">提示：短信号码可在坐席个人设置中修改</span></td>    
       </tr>
       <tr> 
            <td class="micro-label" ><label for="rmd_remark">提醒内容：</lable></td><td><textarea cols="47" rows="5" name="rmd_remark" id="rmd_remark" class="easyui-validatebox"  required="true" missingMessage="提醒内容不能为空"><?php echo $this->_tpl_vars['result']['remark']; ?>
</textarea></td>
       </tr>
       <tr>
       <td align="center" colspan="2">
       <span id="_remind_msg_info" style='color:red;'></span>
       <a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" id="submit_remind_btn">保存</a>
       </td>
       </tr>
     </tbody>
 </table>
</div>
<script language="JavaScript" type="text/javascript">
//保存提醒
$('#submit_remind_btn').click(function(){
	if( !$('#rmd_remark').validatebox('isValid')  ) //提醒内容
	{
		return false;
	}
	var _data = {};
	_data.rmd_param_in	 = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['remind_info']['rmd_param_in'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
"//int型参数
	_data.rmd_param_char = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['remind_info']['rmd_param_char'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"//varchar型参数
	_data.rmd_time	     = $('#rmd_time').val()+" "+$('select[name="rmd_Hour"]').val()+":"+$('select[name="rmd_Minute"]').val();//提醒时间
	_data.rmd_sendsms    = $("#rmd_sendsms").attr('checked')=='checked'?1:0;//是否短信提醒
	_data.user_sms_phone = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['remind_info']['user_sms_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//短信提醒号码
	_data.rmd_remark     = $('#rmd_remark').val();//提醒内容
	_data.rmd_type       = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['remind_info']['rmd_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
"

	$('#submit_remind_btn').linkbutton('disable');
	if($('#rmd_time').val().length!=0)
	{
		$.ajax({
			type:'POST',
			url: 'index.php?c=remind&m=insert_remind',
			data:_data,
			dataType:"json",
			success: function(responce){
				if(responce['error']===0){
					$('#_remind_msg_info').html("<img src='./themes/default/icons/ok.png' />&nbsp;新建提醒成功！");
					$('#remind_list').datagrid('reload');
					setTimeout(function(){$('#set_remind').window('close')},1000);
				}
				else
				{
					$.messager.alert('错误','保存提醒失败','error');
					$('#submit_remind_btn').linkbutton('enable');
				}
			}
		});
	}
	else
	{
			$.messager.alert('错误','提醒日期不能为空','error');
	}
});
</script>