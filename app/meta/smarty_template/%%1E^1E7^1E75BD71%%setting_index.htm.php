<?php /* Smarty version 2.6.19, created on 2015-07-14 14:30:08
         compiled from setting_index.htm */ ?>
<!--设置向导-->
	<div style='padding:30px 10px 20px 0px;'>
	<table><tbody>
	<tr>
		<td ><img src='./image/settp.jpg' /></td>
		<?php if ($this->_tpl_vars['type'] == 1): ?>
		<td>系统首次登录，强烈建议进入设置向导，设置系统基础信息。</td>
		<?php else: ?>
		<td>您是首次登录系统，建议进入帮助向导走一遍，有助于熟悉系统。</td>
		<?php endif; ?>
	</tr>
	<tr>
		<td colspan='5' align='center'><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)" onclick="cancel_window(0)"><?php if ($this->_tpl_vars['type'] == 1): ?>设置向导<?php else: ?>帮助向导<?php endif; ?></a>&nbsp;&nbsp;<a class="easyui-linkbutton" iconCls="icon-undo" href="javascript:void(0)" onclick="cancel_window(1)">跳过</a></td>
		
	</tr>
	</tbody></table>
	</div>
<!--设置向导 end-->

<script language='JavaScript' type='text/javascript'>
// 设置向导 - 关闭窗口
function cancel_window(id)
{
	$("#setting_background").fadeOut("normal",function(){
		$(this).remove();
	});
	$('#setting_panel').window('close');
	if(id==0)
	{
		if(<?php echo $this->_tpl_vars['type']; ?>
==1)
		{
			window.parent.addTab('设置向导','index.php?c=setting','menu_icon_setting');
		}
		else
		{
			window.parent.addTab('帮助向导','index.php?c=setting&m=user_setting','menu_icon_setting');
		}
	}
}
</script>