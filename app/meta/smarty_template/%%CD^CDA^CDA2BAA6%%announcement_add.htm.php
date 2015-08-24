<?php /* Smarty version 2.6.19, created on 2015-08-15 16:32:26
         compiled from announcement_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'announcement_add.htm', 10, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div align="center" class="form-div"> 
<form action="./index.php?c=announcement&m=<?php echo $this->_tpl_vars['ins_up']; ?>
" method="POST" name="theForm" id="theForm" enctype="multipart/form-data">
<table align="center" width="95%">
<tr>
<td ><input type="hidden" id="anns_id" name="anns_id" value="<?php echo $this->_tpl_vars['anns_info']['anns_id']; ?>
"/>
标题：<input type="text" style="height:25px" name="title" size="50" id="title_add_ans" value="<?php echo $this->_tpl_vars['anns_info']['anns_title']; ?>
"></td>
</tr>
<tr>
<td  >部门：<input  id="department" name="department" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['anns_info']['dept_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"/></td>
</tr>
<tr>
<td>
<textarea id="ancontent" name="ancontent"><?php echo $this->_tpl_vars['anns_info']['anns_content']; ?>
</textarea>
</td>
</tr>
<tr>
<td align="center">
<a href="###" class="easyui-linkbutton" iconCls="icon-save" onClick="javascript:return checkvalue();" id="add_anns">保存</a>
</td>
</tr>
</table>
</form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="./jssrc/xheditor/xheditor.js?v=1.11"></script>
<script src="./jssrc/viewjs/announcement_add.js" type="text/javascript"></script>