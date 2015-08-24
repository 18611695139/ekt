<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:35
         compiled from dictionary_page.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="main-div"  style='padding-top:30px;width:99%;padding-left:20px;'>
<div  id='_action_dictionary' >
	<?php if ($this->_tpl_vars['client_base']['cle_stage']): ?>
	<div title="客户阶段" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=2'></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['client_base']['cle_stat']): ?>
	<div title="号码状态" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=6'></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['client_base']['cle_info_source']): ?>
	<div title="信息来源" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=1'></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['power_order']): ?>
	<div title="订单状态" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=3'></div>
	<div title="配送方式" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=5'></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['power_service']): ?>
	<div title="服务类型" href='index.php?c=dictionary&m=get_dictionary_detail&p_id=4'></div>
	<?php endif; ?>

</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/json2.js" type="text/javascript"></script>
<script src="./jssrc/viewjs/dictionary_info.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
var type = "";
var client_type_option_str = '<?php $_from = $this->_tpl_vars['client_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cykey'] => $this->_tpl_vars['cle_type']):
?><option value="<?php echo $this->_tpl_vars['cykey']; ?>
" ><?php echo $this->_tpl_vars['cle_type']; ?>
</option><?php endforeach; endif; unset($_from); ?>';
var _width = get_width();
if(_width>600)
{
	_width = 600;
}
$(function(){
	$('#_action_dictionary').tabs({
		width:_width,
		border:false,
		plain:true,
		tabPosition:'left'
	});
});
</script>