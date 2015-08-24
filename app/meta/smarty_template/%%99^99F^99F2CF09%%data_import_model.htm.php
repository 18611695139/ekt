<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:34
         compiled from data_import_model.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div style="overflow: auto;height:80px;" class="list-div">
     <table  style="width:<?php echo $this->_tpl_vars['table_width']; ?>
px;"   >
      <thead>
         <tr>
          <?php $_from = $this->_tpl_vars['table_head']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tkey'] => $this->_tpl_vars['head']):
?>
            <th style="width:30px;"><?php echo $this->_tpl_vars['head']; ?>
</th>
          <?php endforeach; endif; unset($_from); ?>
         </tr>
     </thead>
       <tbody >
        
         <tr id="model_tr">  
          <?php $_from = $this->_tpl_vars['model_detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
             <td align="center"><?php echo $this->_tpl_vars['detail']['name']; ?>
</td>   
          <?php endforeach; endif; unset($_from); ?>    
         </tr>
       </tbody>
    </table>
    <input type="hidden" id="OK_model_id" name="OK_model_id"  value="<?php echo $this->_tpl_vars['model_id']; ?>
" />
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>