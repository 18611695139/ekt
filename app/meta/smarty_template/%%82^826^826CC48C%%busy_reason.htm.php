<?php /* Smarty version 2.6.19, created on 2015-07-21 09:21:15
         compiled from busy_reason.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="main-div">
<form id="busyForm" action="index.php?c=busy&m=save_busy_reason" method="post">
<table id="busyTable" borderColor="#ffffff" cellSpacing="0" cellPadding="0"  style="width:100%;border-width:0px;">
 <tbody >
 <?php if ($this->_tpl_vars['reason_info']): ?>
	<?php $_from = $this->_tpl_vars['reason_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info']):
?>
	<tr>
		<td class="narrow-label" ><?php echo $this->_tpl_vars['dkey']+1; ?>
.</td>
		<td style="width:30%">
			<input type="text" id="option<?php echo $this->_tpl_vars['dkey']+1; ?>
" name="option[]" value="<?php echo $this->_tpl_vars['info']['stat_reason']; ?>
" readonly/>
			<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
               	<span class="glyphicon glyphicon-minus"></span>
            </button>
		</td>
	</tr>
	<input type="hidden" name="old_option[<?php echo $this->_tpl_vars['info']['id']; ?>
]" value="<?php echo $this->_tpl_vars['info']['stat_reason']; ?>
">
	<?php endforeach; endif; unset($_from); ?>
	<?php else: ?>
    <tr>
		<td class="narrow-label" >1.</td>
		<td style="width:30%">
			<input type="text" id="option1" name="option[]" value="" />
			<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
               	<span class="glyphicon glyphicon-minus"></span>
            </button>
		</td>
	</tr>
	<tr>
		<td class="narrow-label" >2.</td>
		<td style="width:30%">
			<input type="text" id="option2" name="option[]" value="" />
			<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
               	<span class="glyphicon glyphicon-minus"></span>
            </button>
		</td>
	</tr>
	<tr>
		<td class="narrow-label" >3.</td>
		<td style="width:30%">
			<input type="text" id="option3" name="option[]" value="" />
			<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
               	<span class="glyphicon glyphicon-minus"></span>
            </button>
		</td>
	</tr>
	<?php endif; ?>
	</tbody>
</table>
</form>
<div style="width:90%;text-align:center;padding-top:10px;padding-bottom:10px;">
	  <button type="button" class="btn"  onclick="add_option()">
    	<span class="glyphicon glyphicon-plus"></span> 添加
	 </button>
	 <button type="button" class="btn btn-primary"  onclick="save_reason()">
    	<span class="glyphicon glyphicon-saved"></span> 保存设置
	 </button>
</div>
</div>


<script language="JavaScript" type="text/javascript">
$('button[name="btn-minus"]').on('click', function() {
	$(this).parent().parent().remove();
});
/*添加*/
function add_option()
{
	var obj = document.getElementById("busyTable");
	var id = obj.rows.length +1+'';
	if(id<=5)
	{
		var tr = $('\
		<tr>\
			<td class="narrow-label">'+id+'.</td>\
			<td style="width:30%">\
				<input type="text" id="option'+id+'" name="option[]" value="" />\
				<button type="button" name="btn-minus" class="btn btn-mini btn-danger">\
               		<span class="glyphicon glyphicon-minus"></span>\
            	</button>\
            </td>\
		</tr>');
		$('#busyTable').append(tr);
		$('button[name="btn-minus"]').on('click', function() {
			$(this).parent().parent().remove();
		});
	}
	else
	{
		$.messager.alert('错误','不能超过5个置忙原因','error');
	}
}



/*保存设置*/
function save_reason()
{
	$('#busyForm').submit();
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>