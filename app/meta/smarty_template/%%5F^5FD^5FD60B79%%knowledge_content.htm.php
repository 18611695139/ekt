<?php /* Smarty version 2.6.19, created on 2015-08-07 10:18:02
         compiled from knowledge_content.htm */ ?>
<table style="width:100%;">
<tr>
<?php $_from = $this->_tpl_vars['art_content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['k_content'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['k_content']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['base'] => $this->_tpl_vars['item']):
        $this->_foreach['k_content']['iteration']++;
?>
<?php if ($this->_tpl_vars['base']%2 == 0 && $this->_tpl_vars['base'] != 0): ?>
</tr>
<tr>
<?php endif; ?>
<td style="width:50%">
<div class="easyui-panel" data-options="iconCls:'icon-tip'" tools='#more<?php echo $this->_tpl_vars['class_name'][$this->_tpl_vars['base']]['k_class_id']; ?>
'  style="height:250px;" title="<?php echo $this->_tpl_vars['class_name'][$this->_tpl_vars['base']]['k_class_name']; ?>
">

<table style='padding:10px;'>  
    <thead> 
		<?php $_from = $this->_tpl_vars['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['content']):
?>
		 <tr> 
		 <?php if ($this->_tpl_vars['content']): ?>
		 <?php if ($this->_tpl_vars['content']['k_art_id']): ?>
			<td style="border:none;line-height:15px;width:60%;">
				<span style="color:#808080;">【<a href='###' onclick="art_more(<?php echo $this->_tpl_vars['content']['k_class_id']; ?>
)" title='<?php echo $this->_tpl_vars['content']['k_class_name']; ?>
'><?php echo $this->_tpl_vars['content']['k_class_name']; ?>
</a>】</span>
				<a href="###" onclick="art_detail(<?php echo $this->_tpl_vars['content']['k_art_id']; ?>
,'<?php echo $this->_tpl_vars['content']['k_art_title']; ?>
',<?php echo $this->_tpl_vars['content']['k_class_id']; ?>
)" title='查看'><?php echo $this->_tpl_vars['content']['k_art_title']; ?>
</a>
			</td>
			<td style="border:none;line-height:15px;">
				<span  style="color:#808080;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['content']['k_art_create_date']; ?>
</span>
			</td>
			<?php endif; ?>
			<?php endif; ?>
			</tr>
		<?php endforeach; endif; unset($_from); ?>
    </thead>  
</table>  
</div>
 <div id='more<?php echo $this->_tpl_vars['class_name'][$this->_tpl_vars['base']]['k_class_id']; ?>
'>
 	<a href="###"  style='width:50px;'  onclick="art_more(<?php echo $this->_tpl_vars['class_name'][$this->_tpl_vars['base']]['k_class_id']; ?>
)"> 更多>> </a>
 	</div>
</td>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_foreach['k_content']['total'] == 1): ?>
<td></td>
<?php endif; ?>
</tr>
</table>