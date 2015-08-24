<?php /* Smarty version 2.6.19, created on 2015-08-07 10:18:02
         compiled from knowledge_hot.htm */ ?>
<div>
<ul>
<?php $_from = $this->_tpl_vars['art_hot']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['hot']):
?>
<li><a href="###" onclick="art_detail(<?php echo $this->_tpl_vars['hot']['k_art_id']; ?>
,'<?php echo $this->_tpl_vars['hot']['k_art_title']; ?>
',<?php echo $this->_tpl_vars['hot']['k_class_id']; ?>
)"><?php echo $this->_tpl_vars['hot']['k_art_title']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</ul>
</div>