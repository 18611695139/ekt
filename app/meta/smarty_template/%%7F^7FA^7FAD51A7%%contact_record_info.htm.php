<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:36
         compiled from contact_record_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_time', 'contact_record_info.htm', 23, false),array('modifier', 'default', 'contact_record_info.htm', 57, false),)), $this); ?>
<form style="padding:10px;" class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="con_rec_content">联系内容</label>
		<div class="controls">
			<textarea id="con_rec_content" name="con_rec_content" cols="20" rows="3" style="width:60%;"></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="con_rec_next_time">下次联系时间</label>
		<div class="controls">
			<div class="input-append">
            	<input type="text" name="con_rec_next_time" id="con_rec_next_time" value="" style="width:150px;" readonly>
            	<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'con_rec_next_time'})">
                	<span class="glyphicon glyphicon-calendar"></span>
            	</button>
        	</div>
        	<a href="###" id="remind_button" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-remind'" title="提醒" onclick="create_remind();" >创建提醒</a>
		</div>
	</div>
	<div class="control-group" id="tr_rem_time" style="display:none;">
		<label class="control-label" for="con_rec_content">提醒时间(时/分)</label>
		<div class="controls">
			<?php echo smarty_function_html_select_time(array('use_24_hours' => true,'display_seconds' => false,'minute_interval' => 10,'prefix' => 'rmd_','hour_extra' => "title='时'",'minute_extra' => "title='分'"), $this);?>
<span style="color: red;">提示：下次联系时间为提醒日期</span>
		</div>
	</div>
	<div class="control-group" id="tr_rmd_sendsms" style="display:none;">
		<label class="control-label" for="rmd_sendsms">短信提示</label>
		<div class="controls">
			<input type="checkbox" name='rmd_sendsms' id='rmd_sendsms' value="1" <?php if ($this->_tpl_vars['user_sms_phone'] == ''): ?> disabled <?php else: ?> CHECKED <?php endif; ?> /> <?php echo $this->_tpl_vars['user_sms_phone']; ?>
 <span style="color: red;">提示：<?php if ($this->_tpl_vars['user_sms_phone']): ?>短信将发送至该号码<?php else: ?>短信号码可在坐席个人设置中修改<?php endif; ?></span>
		</div>
	</div>
	<div class="control-group" id="tr_rmd_remark" style="display:none;">
		<label class="control-label" for="rmd_remark">提醒内容</label>
		<div class="controls">
			<textarea id="rmd_remark" name="rmd_remark" cols="20" rows="3" style="width:60%;"></textarea>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php if ($this->_tpl_vars['power_client_update'] && ! $this->_tpl_vars['order_id']): ?>
			<input type="checkbox" id="save_client_contact_data" name="save_client_contact_data" title="同步保存客户数据" /> 同步保存客户信息
			<?php endif; ?>
			<button type="button" class="btn btn-small btn-primary" id="submit_contact" onclick="insert_contact_record();">
            	<span class="glyphicon glyphicon-saved"></span> 保存联系记录
        	</button>
        	<span style='color:red;' id='_remind_msg'>&nbsp;</span>
		</div>
	</div>
</form>

<script language="JavaScript" type="text/javascript">
$('select[name="rmd_Hour"]').css('width','80px');
$('select[name="rmd_Minute"]').css('width','80px');
//新建联系记录
function insert_contact_record()
{
	if( "<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
" == "0" && "<?php echo ((is_array($_tmp=@$this->_tpl_vars['order_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
" == "0")
	{
		$.messager.alert("提示","<br>缺少参数！","info");
		return false;
	}

	var contact_data = {};
	<?php if ($this->_tpl_vars['cle_id']): ?>
	contact_data.cle_id     = '<?php echo $this->_tpl_vars['cle_id']; ?>
';
	<?php endif; ?>
	<?php if ($this->_tpl_vars['order_id']): ?>
	contact_data.order_id     = '<?php echo $this->_tpl_vars['order_id']; ?>
';
	<?php endif; ?>
	contact_data.rec_callid = rec_callid;
	contact_data.con_rec_content      = $("#con_rec_content").val();//联系内容
	contact_data.con_rec_next_time    = $("#con_rec_next_time").val();//下次联系时间
	if(contact_data.con_rec_content=='' && contact_data.con_rec_next_time=='')
	{
		return false;
	}

	//创建提醒
	var button_text = $("#remind_button").linkbutton("options").text;
	if( button_text == '关闭提醒' )
	{
		contact_data.create_reamind = 1;

		//类型  0我的提醒  1客户提醒  2订单提醒
		contact_data.rmd_type       = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['rmd_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
";
		<?php if ($this->_tpl_vars['rmd_type'] == 1): ?>
		contact_data.rmd_param_int  = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
";
		contact_data.rmd_param_char = "客户："+$("#cle_name").val();
		<?php else: ?>
		contact_data.rmd_param_int  = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['order_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
";
		contact_data.rmd_param_char = "订单："+$("#order_num").val();
		<?php endif; ?>

		if( contact_data.con_rec_next_time == "" )
		{
			contact_data.con_rec_next_time = "<?php echo $this->_tpl_vars['date']; ?>
";
		}
		contact_data.rmd_time	     = contact_data.con_rec_next_time+" "+$('select[name="rmd_Hour"]').val()+":"+$('select[name="rmd_Minute"]').val();//提醒时间
		contact_data.rmd_sendsms     = $("#rmd_sendsms").attr('checked')=='checked'?1:0;//是否短信提醒
		contact_data.user_sms_phone  = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_sms_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";//短信提醒号码
		contact_data.rmd_remark      = $("#rmd_remark").val();//提醒内容
	}

	$.ajax({
		type:'POST',
		url: "index.php?c=contact_record&m=insert_contact_record",
		data: contact_data,
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$('#contact_record').datagrid('reload');
				$('#con_rec_content').val('');
				$("#rmd_remark").val('');
				if( button_text == '关闭提醒' )
				{
					_show_contact_msg("<img src='./themes/default/icons/ok.png' />&nbsp;联系记录、提醒创建成功");
					create_remind();
				}
				else
				_show_contact_msg("<img src='./themes/default/icons/ok.png' />&nbsp;联系记录创建成功");
				<?php if ($this->_tpl_vars['power_client_update'] && ! $this->_tpl_vars['order_id']): ?>
				if($("#save_client_contact_data").attr("checked") == "checked")
				{
					//数据比较，得到修改过的数据
					var current_data = get_current_value();
					var changed_data = data_comparison(original_data,current_data);
					changed_data     = json2url(changed_data);
					if( changed_data != "" )
					{
						save_client();
					}
				}
				<?php endif; ?>
			}
			else
			{
				$.messager.alert('错误','执行错误','error');
			}
		}
	});
}

//创建提醒
function create_remind()
{
	var button_text = $("#remind_button").linkbutton("options").text;
	var remind_date = $("#con_rec_next_time").val();
	//创建提醒
	if( button_text == "创建提醒" )
	{
		//提醒时间
		$("#tr_rem_time").css("display",'');
		//短信提醒
		$("#tr_rmd_sendsms").css("display",'');
		//提醒内容
		$("#tr_rmd_remark").css("display",'');

		if( remind_date == "")
		{
			$("#con_rec_next_time").val("<?php echo $this->_tpl_vars['date']; ?>
");
		}
		$("#remind_button").linkbutton({text:"关闭提醒"});
	}
	else
	{
		//提醒时间
		$("#tr_rem_time").css("display",'none');
		//短信提醒
		$("#tr_rmd_sendsms").css("display",'none');
		//提醒内容
		$("#tr_rmd_remark").css("display",'none');

		if( remind_date == "<?php echo ((is_array($_tmp=@$this->_tpl_vars['date'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
")
		{
			$("#con_rec_next_time").val("");
		}
		$("#remind_button").linkbutton({text:"创建提醒"});
	}
}
//显示消息
function _show_contact_msg(msg)
{
	$("#_remind_msg").html(msg);
	setTimeout(function(){
		$("#_remind_msg").html("");
	},3000);
}
</script>