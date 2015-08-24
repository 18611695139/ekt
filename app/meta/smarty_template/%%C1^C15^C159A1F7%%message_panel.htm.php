<?php /* Smarty version 2.6.19, created on 2015-08-07 10:17:38
         compiled from message_panel.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'message_panel.htm', 61, false),)), $this); ?>
<div id="msg_send" style="width:460px;height:340px;">
<table width="100%" >
<tr style="height:200px">
<td width="50%" style="width:230px" valign="top"><div class="easyui-panel" style="overflow:auto;height:200px"><ul id="user_tree"></ul></div></td>
<td valign="top" style="width:230px">
<b>收信人</b>： 
<div id="user_selected" style="overflow:auto;height:180px;width:220px;"></div>
</td>
</tr>
<tr style="height:80px">
<td colspan="2" valign="top">
<textarea rows="6" id="_message_content" style="width:440px"></textarea><br/>
<div align="right" style="padding-right:20px">
<span id="_msg_msg" style="color:red;padding-right:15px;"></span>
<a class="easyui-linkbutton" href="javascript:void(0)" onclick="_send_message()">发送</a>
</div>
</td>
</td>
</tr>
</table>
</div>

<script language="JavaScript" type="text/javascript">
//电话控制代码
$(document).ready(function (){
	//发消息
	$('#user_tree').tree({
		url:'index.php?c=message&m=get_dept_user_tree&selected_id=<?php echo $this->_tpl_vars['selected_id']; ?>
',
		panelWidth:210,
		checkbox:true,
		animate:true,
		lines:true,
		onCheck:function()
		{
			getChecked();
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#user_tree').tree('options').url = "index.php?c=message&m=get_dept_user_tree&selected_id=<?php echo $this->_tpl_vars['selected_id']; ?>
";
			}
		}

	});
});


//发送消息
function _send_message()
{
	var s_message = $('#_message_content').val();

	if(s_message == '')
	{
		_show_error('消息内容为空');
		return;
	}
	
	var length = $("#_message_content").val().length;
	var max_length = <?php echo ((is_array($_tmp=@$this->_tpl_vars['MSG_MAX_LENGTH'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
;
	if( length > max_length )
	{
		_show_error('超出信息最大输入上限');
	}

	var user_ids = '';
	var nodes = $('#user_tree').tree('getChecked');
	for(var i=0; i<nodes.length; i++){
		if($('#user_tree').tree('isLeaf',nodes[i].target))
		{
			if(!nodes[i].attributes)  //坐席没有attributes属性，只能选择坐席
			{
				if(user_ids != '')
				user_ids +=',';
				user_ids += nodes[i].id;
			}
		}
	}
	if(!user_ids)
	{
		_show_error('收信人为空');
		return;
	}

	$.ajax({
		url:      "index.php?c=message&m=send_message",
		type:     "post",
		data:	  {"user_ids": user_ids,"content":s_message},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$('#message_panel').window('close');
			}
			else
			{
				$.messager.alert(responce[2]["error"],responce[0]["message"]);
			}
		}
	});
}
//得到当前选中的员工
function getChecked()
{
	$('#user_selected').html('');
	var nodes = $('#user_tree').tree('getChecked');

	for(var i=0; i<nodes.length; i++){
		if($('#user_tree').tree('isLeaf',nodes[i].target))
		{
			if(!nodes[i].attributes)  //坐席没有attributes属性，只能选择坐席
			{
				var s = $("<a href='###' class='message_div'>"+nodes[i].text+"</a>");
				s.bind('click',{obj:nodes[i].target},function (event){//取消选中的客户
					$('.tree-checkbox',$(event.data['obj'])).click();
				});
				s.appendTo($('#user_selected'));
			}
		}
	}
}


//显示错误信息
var clear_prompt = '';
function _show_error(msg)
{
	$('#_msg_msg').text(msg);
	clearTimeout(clear_prompt);
	clear_prompt = setTimeout(function(){$('#_msg_msg').html('');},3000);
}
</script>