<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:12
         compiled from field_system_base.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<table cellspacing="0" cellpadding="4" align='center' style="width:100%;border-collapse: collapse; border: 0px solid gray;" >
	<?php $_from = $this->_tpl_vars['field_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ke'] => $this->_tpl_vars['val']):
        $this->_foreach['list_Name']['iteration']++;
?>
	<?php if (($this->_foreach['list_Name']['iteration']-1)%2 == 0): ?>
    <tr align="center">
    <?php endif; ?>
	<?php if ($this->_tpl_vars['val']['fields'] == 'cle_phone' || $this->_tpl_vars['val']['fields'] == 'cle_phone2' || $this->_tpl_vars['val']['fields'] == 'cle_phone3' || $this->_tpl_vars['val']['fields'] == 'con_mobile'): ?>
		<td style="text-align:right;width:15%"><input type="checkbox" id="<?php echo $this->_tpl_vars['val']['fields']; ?>
ck" <?php if ($this->_tpl_vars['val']['state'] == 1): ?>checked<?php endif; ?> <?php if ($this->_tpl_vars['val']['is_disabed'] == 1): ?>disabled<?php endif; ?> /></td>
		<td style="text-align:left;">
            <div class="input-append">
                 <input type="text" id="<?php echo $this->_tpl_vars['val']['fields']; ?>
" value="<?php echo $this->_tpl_vars['val']['name']; ?>
" disabled/>
                 <span class="add-on"><i class="glyphicon glyphicon-earphone"></i></span>
            </div>
		</td>
	<?php else: ?>
		<td style="text-align:right;width:15%"><input type="checkbox" id="<?php echo $this->_tpl_vars['val']['fields']; ?>
ck" <?php if ($this->_tpl_vars['val']['state'] == 1): ?>checked<?php endif; ?> <?php if ($this->_tpl_vars['val']['is_disabed'] == 1): ?>disabled<?php endif; ?> /></td>
		<td style="text-align:left;"><input type="text" id="<?php echo $this->_tpl_vars['val']['fields']; ?>
" maxlength="7" value="<?php echo $this->_tpl_vars['val']['name']; ?>
" disabled /></td>
	<?php endif; ?>
    <?php if (($this->_foreach['list_Name']['iteration']-1)%2 != 0): ?>
    </tr>
    <?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</table>
<div style="text-align:center;padding-top:30px;padding-bottom:30px;">
	<span style='color:red;' id='_base_field_<?php echo $this->_tpl_vars['field_type']; ?>
'></span>
	<button type="button" class="btn btn-primary" onclick="save_base_flied('<?php echo $this->_tpl_vars['field_type']; ?>
')">
    	<span class="glyphicon glyphicon-saved"></span> 保存配置
	</button>
</div>
<script type="text/javascript" language="javascript">
/*保存*/
function save_base_flied(field_type)
{
	var _if_use = {};
	<?php $_from = $this->_tpl_vars['field_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ke'] => $this->_tpl_vars['val']):
?>
	if($("#<?php echo $this->_tpl_vars['val']['fields']; ?>
ck").attr('checked')=='checked')
	{
		_if_use.<?php echo $this->_tpl_vars['val']['fields']; ?>
 = 1;
	}
	else
	{
		_if_use.<?php echo $this->_tpl_vars['val']['fields']; ?>
 = 0;
	}
	<?php endforeach; endif; unset($_from); ?>
	$.ajax({
		type:'POST',
		url: "index.php?c=field_confirm&m=save_base_field_state",
		data:{'_if_use':_if_use,'field_type':field_type},
		dataType:"json",
		success: function(responce){
			if(responce['error']==0){
				_show_base_msg(field_type,"<img src='./themes/default/icons/ok.png' />&nbsp;配置成功");
			}else{
				$.messager.alert("错误","<br>操作失败！",'error');
			}
		}
	});
}

//显示基本字段编辑信息
function _show_base_msg(field_type,msg)
{
	$("#_base_field_"+field_type).html(msg);
	setTimeout(function(){
		$("#_base_field_"+field_type).html("");
	},2000);
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>