<?php /* Smarty version 2.6.19, created on 2015-07-21 11:09:50
         compiled from model_select.htm */ ?>
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div align="center" style="background-color: #F4FAFB;border:1px;width:100%;height:375px;overflow:auto;" class="main-div">
<div align="center" style="float:left;width:50%;">
			<table cellspacing="0" cellpadding="5" style="padding: 2px;">
				<tbody>
				<tr bgcolor="#D0E4F9">
				<td><b>序号</b></td><td><b>模板名称</b></td><td>&nbsp;</td>
				</tr>
  				<?php $_from = $this->_tpl_vars['model_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['mkey'] => $this->_tpl_vars['info']):
?>
  				<tr>
  					<td><?php echo $this->_tpl_vars['mkey']+1; ?>
</td>
  					<td ><a id="<?php echo $this->_tpl_vars['info']['model_id']; ?>
" href="###" onclick="select_model(<?php echo $this->_tpl_vars['info']['model_id']; ?>
);"><?php echo $this->_tpl_vars['info']['model_name']; ?>
</a></td>
  					<td><a  href='###' onclick="parent.delete_model(<?php echo $this->_tpl_vars['info']['model_id']; ?>
)" title="删除模板"><img src='./image/no.gif' align='absmiddle' border='0' height='16' width='16' /></a></td>
 			    </tr>
   				<?php endforeach; endif; unset($_from); ?>
 				</tbody>
				</table>
</div>
	 <div style="float:left;width:45%;border-left:2px solid gray">
			<table cellspacing="0" cellpadding="5" style="padding: 2px;">
				<tbody>
     				<tr>
     					<td colspan="2" bgcolor="#D0E4F9"><b>导入项【<?php echo $this->_tpl_vars['model_name']; ?>
】</b></td>
     					</tr>
     			   <?php if ($this->_tpl_vars['model_fields']): ?>		
        			<?php $_from = $this->_tpl_vars['model_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['info']):
?>
        			<tr>
	          			<td><?php echo $this->_tpl_vars['key']+1; ?>
</td>
	          			<td><?php echo $this->_tpl_vars['info']['name']; ?>
</td>
        			</tr>
        			<?php endforeach; endif; unset($_from); ?>
        			<?php else: ?>
        			<tr>
	          			<td colspan="2">请添加模板选项！</td>
        			</tr>
        			<?php endif; ?>
        			</tbody>
			</table>
</div>

</div>

<input type="hidden" id="model_id" name="model_id" value="<?php echo $this->_tpl_vars['model_id']; ?>
" />
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//标记选中模板
	var model_id=$('#model_id').val();
	if(model_id){
		$('#'+model_id).css("color","red");
	}
});

//选择模板
function select_model(model_id)
{
	//刷新iframe
	parent.refresh_iframe(model_id);
}
</script>