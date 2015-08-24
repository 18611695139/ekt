<?php /* Smarty version 2.6.19, created on 2015-07-21 09:39:57
         compiled from phone_control_callinner.htm */ ?>
<table cellpadding="0" cellspacing="0" border="0" style="width:95%">
<tr >
<td style="font-weight:700;" align="right">坐席工号：</td>
<td><input id="pc_user_num" type="text" style='width:60px;'></td>
<td style="font-weight:700;"align="right" >主叫号码：</td>
<td >
<span id='pc_caller_span'></span>
</td>
<td align="center" colspan="2"><a class="easyui-linkbutton"  href="javascript:void(0)" iconCls="icon-phone"  id="pc_callinner_btn" >呼叫</a> </td>
</tr>

</table>
<div id='free_user_table'></div>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	//主叫号码
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
		$('#pc_caller_span').html('<select id="pc_caller"  onchange="set_caller_cookie()" style="width:80px;"></select>');
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
			$("#pc_caller").append('<option value="'+_callers[i]+'"  '+_checked+' >'+_callers[i]+'</option>');
		}
	}

	//空闲坐席列表
	$('#free_user_table').datagrid({
		title:'空闲坐席列表',
		height:290,
		nowrap: true,
		striped: true,
		fitColumns:true,
		rownumbers:true,
		singleSelect:true,
		sortName:'user_id',
		sortOrder:'desc',
		idField:'user_id',
		url:'index.php?c=user&m=get_free_users',
		columns:[[
		{title:'user_id',field:'user_id',hidden:true},
		{title:'工号',field:'user_num',width:60,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return value+"&nbsp;&nbsp;<a href='javascript:;' onclick=_callinner_by_user_id('"+rowData.user_id+"'); title='呼叫'><img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a>";
		}},
		{title:'姓名',field:'user_name',width:60},
		{title:'分机号',field:'pho_num',width:60},
		{title:'部门',field:'dept_name',width:60}
		]]
	});

});

$('#pc_callinner_btn').click(function(){
	var user_num = $('#pc_user_num').val();
	if(user_num.length!=0)
	{
		_callinner_by_user_num(user_num);
	}
	else
	{
		$.messager.alert('错误','<br>请填写坐席工号','error');
	}

});

function _callinner_by_user_num(user_num)
{
	$.ajax({
		type:'POST',
		url: 'index.php?c=user&m=get_user_id_by_num',
		data: {'user_num':user_num},
		dataType: 'json',
		success: function(responce){
			if(responce['error'] === 0)
			{
				var user_id = responce['content'];
				_callinner_by_user_id(user_id);
			}
			else
			{
				$.messager.alert('错误','找不到对应的坐席','error');
			}
		}
	});
}

function _callinner_by_user_id(user_id)
{
	var _caller = $('#pc_caller').val();
	wincall.fn_dialinner(user_id,_caller);
}
</script>