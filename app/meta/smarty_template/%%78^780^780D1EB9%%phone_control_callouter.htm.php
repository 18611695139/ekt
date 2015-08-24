<?php /* Smarty version 2.6.19, created on 2015-07-20 22:54:06
         compiled from phone_control_callouter.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'phone_control_callouter.htm', 178, false),)), $this); ?>
<table cellpadding="0" cellspacing="0" border="0" style="width:260px;padding:5px;" id="tb_callouter">
<tr >
<td style="font-weight:700;padding:10px 5px 5px 5px;" align="right"> 外呼号码：</td>
<td>
<select id="pc_phone_num" name="pc_phone_num" style="width:130px;" ></select>
<a href="javascript:void(0)"  id="keypad_btn" onclick='get_keypad_panel_content()' title='拨号盘' ><img src='./image/keypad.jpg' border='0' height='16' width='16' align='absmiddle'/></a>
<input id='_keypad_type' name='_keypad_type' value='0' type='hidden'>
</td>
</tr>
<tr>
<td style="font-weight:700;padding:10px 5px 5px 5px"align="right" > 主叫号码：</td>
<td>
<span id='pc_caller_span'></span>
</td>
</tr>
<tr>
<td align="center" colspan="2" style="width:180px;padding:5px 0px 0px 0px">
<div id='_keypad_content' style="display:none;">
	<table id="kaypad_table" cellpadding="0" cellspacing="0" border="0" style="width:180px;padding:10px 5px 10px 15px">
	<tr>
		<td><a href="#" class="easyui-linkbutton" ><b>1</b></a></td>
		<td><a href="#" class="easyui-linkbutton" ><b>2</b></a></td>
		<td><a href="#" class="easyui-linkbutton" ><b>3</b></a></td>
	</tr>
	<tr >
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>4</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>5</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>6</b></a></td>
	</tr>
	<tr>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>7</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>8</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>9</b></a></td>
	</tr>
	<tr>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>*</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>0</b></a></td>
		<td style="padding-top:5px"><a href="#" class="easyui-linkbutton" ><b>#</b></a></td>
	</tr>
	</table>
</div>
<a class="easyui-linkbutton"  href="javascript:void(0)" iconCls='icon-phone' id="pc_callouter_btn" title='呼叫'>呼叫</a>
</td>
</tr>
</table>
<script language="JavaScript" type="text/javascript">
var _pc_skill_group = '';
$(document).ready(function(){
	if(wincall.isCalling())
	{
		get_keypad_panel_content();
	}
	$('#tb_callouter').bind('keyup',function(event) {
		if(event.keyCode==13){
			$('#pc_callouter_btn').click();
		}
	});

	/*主叫号码*/
	var _callers= new Array();
	try
	{
		_callers =  wincall.fn_get_caller();
	}
	catch(e){}
	if(_callers.length == 0)
	{
		<?php if ($this->_tpl_vars['outcaller_type'] == 1): ?>
		$('#pc_caller_span').html('<?php echo $this->_tpl_vars['outcaller_num']; ?>
<input id="pc_caller" name="pc_caller" type="hidden" value="<?php echo $this->_tpl_vars['outcaller_num']; ?>
" />');
		<?php else: ?>
		$('#pc_caller_span').html('默认主叫<input id="pc_caller" name="pc_caller" type="hidden" value="" />');
		<?php endif; ?>
	}
	else
	{
		$('#pc_caller_span').html('<select id="pc_caller"  onchange="set_caller_cookie()" style="width:130px;"></select>');
		var cookie_caller = get_cookie('est_caller');
		for(var i=0;i<_callers.length;i++)//添加外呼主叫号码
		{
			var _checked = '';
			<?php if ($this->_tpl_vars['outcaller_type'] == 1): ?>
			if(_callers[i] == '<?php echo $this->_tpl_vars['outcaller_num']; ?>
')
			{
				_checked="selected";
			}
			<?php else: ?>
			if( cookie_caller && cookie_caller == _callers[i] )
			{
				_checked="selected";
			}
			<?php endif; ?>
			$("#pc_caller").append('<option value="'+_callers[i]+'" '+_checked+' >'+_callers[i]+'</option>');
		}
	}

	/*技能组*/
	var _skill_groups =  wincall.fn_get_que();//得到当前坐席所属队列  index.htm中定义队列信息数组queue_nfo
	for(var i=0;i<_skill_groups.length;i++)
	{
		if( que_list[_skill_groups[i]] != 'undefined')
		{
			_pc_skill_group = _skill_groups[i];
			continue;
		}
	}

	//通话记录 - array
	var call_record = wincall.fn_get_callrecord();
	if( call_record )
	{
		$("#pc_phone_num").append('<option value=""></option>');
		for(var i = 0 ; i < call_record.length ; i++)
		{
			var temp_recork = call_record[i];
			<?php if (! $this->_tpl_vars['power_phone_view']): ?>
			temp_recork     = hidden_part_number(temp_recork);
			<?php endif; ?>
			$("#pc_phone_num").append('<option value="'+call_record[i]+'">'+temp_recork+'</option>');
		}
	}

	//客户号码
	<?php if ($this->_tpl_vars['phone_num']): ?>
	var temp_phone_num = "<?php echo $this->_tpl_vars['phone_num']; ?>
";
	<?php if (! $this->_tpl_vars['power_phone_view']): ?>
	temp_phone_num     = hidden_part_number(temp_phone_num);
	<?php endif; ?>
	$('#pc_phone_num').append('<option value="<?php echo $this->_tpl_vars['phone_num']; ?>
">'+temp_phone_num+'</option>');
	$('#pc_phone_num').val('<?php echo $this->_tpl_vars['phone_num']; ?>
');
	<?php endif; ?>

	//外呼号码
	$('#pc_phone_num').combobox({
		hasDownArrow:false,
		filter: function(q, row){
			var opts = $(this).combobox('options');
			return row[opts.textField].indexOf(q) == 0;
		},
		onChange:function(new_value,old_value){
			if(new_value.length>8)
			{
				$('#pc_phone_num').combobox('hidePanel');
			}
		}
	});

	//拨号盘
	$('#kaypad_table a').click(function(){
		if(wincall.isCalling())
		{
			wincall.fn_dtmf($(this).text());
		}
		var _pc_phone_num = $('#pc_phone_num').combobox("getText");
		_pc_phone_num += $(this).text();
		$('#pc_phone_num').combobox('setValue',_pc_phone_num);
	});
});

$('#pc_callouter_btn').click(function(){
	var phone_num = $('#pc_phone_num').combobox("getText");

	<?php if (! $this->_tpl_vars['power_phone_view']): ?>
	if( phone_num && exist_star(phone_num) )
	{
		phone_num = $('#pc_phone_num').combobox("getValue");
	}
	<?php endif; ?>

	if(phone_num.length==0)
	{
		return;
	}

	var _caller	= $('#pc_caller').val();
	var _skill_group	= Number(_pc_skill_group);

	//呼叫
	wincall.fn_dialouter(phone_num,_caller,_skill_group,<?php echo ((is_array($_tmp=@$this->_tpl_vars['outChan'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
);
	$('#pc_phone_num').combobox('hidePanel');
	$('#dail_panel').window('close');
});

//拨号盘
function get_keypad_panel_content()
{
	var _keypad_type = $('#_keypad_type').val();
	if(_keypad_type==0)
	{
		//显示拨号盘
		$("#_keypad_content").css("display",'');
		$('#_keypad_type').val(1);
	}
	else
	{
		//显示拨号盘
		$("#_keypad_content").css("display",'none');
		$('#_keypad_type').val(0);
	}
}
</script>