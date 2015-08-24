<?php /* Smarty version 2.6.19, created on 2015-08-15 17:08:27
         compiled from product_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'product_info.htm', 21, false),array('modifier', 'cat', 'product_info.htm', 42, false),array('modifier', 'default', 'product_info.htm', 245, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='product_tools'>
<div class='main-div' style="background-color:white" title="产品编辑">
<form id='product_form' name='product_form' method="post" action="" enctype="multipart/form-data">
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" align="center" border="0" style="width:98%">
 <tbody>
	 <tr>
         <td class="narrow-label"><label for="product_name">产品名称：</label></td>		           
         <td colspan="3"><input type='text' size="60" id='product_name' name='product_name' value='<?php echo $this->_tpl_vars['product_info']['product_name']; ?>
' class="easyui-validatebox" missingMessage="此项不能为空" required="true" />
         <span style='color:red;font-weight:bold;'>*</span></td>         
      </tr>
     
        <tr>
       	<td class="narrow-label"><label for="product_num">产品编号：</label></td>		           
         <td ><input type='text' id='product_num' name='product_num' value='<?php if ($this->_tpl_vars['product_info']['product_num']): ?><?php echo $this->_tpl_vars['product_info']['product_num']; ?>
<?php elseif ($this->_tpl_vars['product_num']): ?><?php echo $this->_tpl_vars['product_num']; ?>
<?php endif; ?>'/> </td> 
         <td class="narrow-label"><label for="product_price">产品价格：</label></td>		           
         <td ><input type='text' id='product_price' name='product_price' value='<?php echo $this->_tpl_vars['product_info']['product_price']; ?>
' style='width:80px;' />元</td>
      </tr>
      
      <!--自定义字段  -----begin-------->
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'product_count'), $this);?>
<!--  计算  -->
    <?php $_from = $this->_tpl_vars['product_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
      <?php $this->assign('parent_id', $this->_tpl_vars['confirm_info']['id']); ?>
      <?php $this->assign('real_field', $this->_tpl_vars['confirm_info']['fields']); ?>
      
      <?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
      <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'product_count'), $this);?>
<!-- 从新计算 -->
      <tr>
	 	<td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td ><td colspan="11"><textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" style="width: 80%" rows="3" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> ><?php echo $this->_tpl_vars['product_info'][$this->_tpl_vars['real_field']]; ?>
</textarea>
	 	<?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	 	</td>
	  </tr>
	  
	  <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
	  <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'product_count'), $this);?>
<!-- 从新计算 -->
	 <tr>
	 	<td class="narrow-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td >
	 	<td colspan="11">
	 	<span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1">
	 	 <select name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1' confirm_field='true' onchange="get_comfirm_jl_options(1,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
_1'<?php endif; ?> >
		 <option value="" >--请选择--</option>
		  <?php $this->assign('field_type', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_1') : smarty_modifier_cat($_tmp, '_1'))); ?>
		  <?php $_from = $this->_tpl_vars['jl_options'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
          <option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
	     </select>
	    </span>
	    <?php $this->assign('jl_f_t', $this->_tpl_vars['confirm_info']['jl_field_type']); ?>
	    <?php $this->assign('field_type2', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_2') : smarty_modifier_cat($_tmp, '_2'))); ?>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2">
	    	<?php if ($this->_tpl_vars['product_info'][$this->_tpl_vars['field_type']]): ?>
	    		<?php $this->assign('p_id', $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type']]); ?>
	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2' value='<?php echo $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type2']]; ?>
' confirm_field='true'/>
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2" confirm_field='true' <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>onchange="get_comfirm_jl_options(2,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)"<?php endif; ?> >
        				<option value="">--请选择--</option>
       	 				<?php $_from = $this->_tpl_vars['jl_options'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
        				<option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type2']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
        				<?php endforeach; endif; unset($_from); ?>
        			</select>
	    		<?php endif; ?>
	    	 <?php endif; ?>
	     	 </span>
	     <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>      
	      <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3">
	    	<?php if ($this->_tpl_vars['product_info'][$this->_tpl_vars['field_type2']]): ?>
	    	<?php $this->assign('p_id2', $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type2']]); ?>
	    	<?php $this->assign('field_type3', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_3') : smarty_modifier_cat($_tmp, '_3'))); ?>
	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3' value='<?php echo $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type3']]; ?>
' confirm_field='true'/>
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3" confirm_field='true'>
        				<option value="">--请选择--</option>
       	 				<?php $_from = $this->_tpl_vars['jl_options'][$this->_tpl_vars['field_type3']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
        				<option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type3']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
        				<?php endforeach; endif; unset($_from); ?>
        			</select>
	    		<?php endif; ?>
	     	<?php endif; ?>
	     	</span>
	     <?php endif; ?>
	     <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	 	</td>
	 </tr>
	
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 7): ?><!--级联多选框-->
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'product_count'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="narrow-label">
        <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label>
    </td>
 	<td  colspan="11">
		<table>
			<?php $this->assign('field_type', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['id'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_1') : smarty_modifier_cat($_tmp, '_1'))); ?>
			<?php $this->assign('field_type2', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['id'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_2') : smarty_modifier_cat($_tmp, '_2'))); ?>
			<?php if ($this->_tpl_vars['product_info']): ?>
				<?php $this->assign('field_value', $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type']]); ?>
				<?php $this->assign('field_value2', $this->_tpl_vars['product_info'][$this->_tpl_vars['field_type2']]); ?>
			<?php endif; ?>
			<?php $_from = $this->_tpl_vars['checkbox_options'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id1'] => $this->_tpl_vars['check_option1']):
?>
     		<tr>
                <th style="padding-bottom:5px;"><?php echo $this->_tpl_vars['check_option1']['name']; ?>
</th>
     		</tr>
     		<tr>
     			<td style="padding-bottom:5px;">
      				<?php $_from = $this->_tpl_vars['checkbox_options'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id2'] => $this->_tpl_vars['check_option']):
?>
      					<?php if ($this->_tpl_vars['check_option']['p_id'] == $this->_tpl_vars['id1']): ?>
         					<input type="checkbox" checkbox_name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" checkbox_pid="<?php echo $this->_tpl_vars['id1']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2[]" value="<?php echo $this->_tpl_vars['id2']; ?>
" confirm_field='true' <?php if ($this->_tpl_vars['field_value2'][$this->_tpl_vars['id2']] == $this->_tpl_vars['id2']): ?>checked<?php endif; ?> /> <?php echo $this->_tpl_vars['check_option']['name']; ?>
&nbsp;
       					<?php endif; ?>
      				<?php endforeach; endif; unset($_from); ?>
      			</td>
      		</tr>
    		<?php endforeach; endif; unset($_from); ?>
		</table>
 	</td>
 </tr>
	
      <?php else: ?>
       <?php if ($this->_tpl_vars['product_count']%2 == 0): ?>
        <tr>
       <?php endif; ?>
       <td class="narrow-label"  >  <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  ><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：<label></td><td > 
        <?php if ($this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']] || $this->_tpl_vars['confirm_info']['data_type'] == 2): ?>
        <select id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> >
           <?php $_from = $this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
           <option value="<?php echo $this->_tpl_vars['detail']; ?>
" <?php if ($this->_tpl_vars['detail'] == $this->_tpl_vars['product_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['detail']; ?>
</option>
           <?php endforeach; endif; unset($_from); ?>
           <option value="" <?php if (! $this->_tpl_vars['product_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> >&nbsp;</option>
        </select>
        <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
        <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 5): ?><!-- 日期框 -->
         <div class="input-append">
             <input type="text" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php if ($this->_tpl_vars['product_info']): ?><?php echo $this->_tpl_vars['product_info'][$this->_tpl_vars['confirm_info']['fields']]; ?>
<?php elseif ($this->_tpl_vars['confirm_info']['default'] == '系统时间'): ?><?php if ($this->_tpl_vars['confirm_info']['datefmt'] == 'yyyy-MM-dd'): ?><?php echo $this->_tpl_vars['now_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['now_time']; ?>
<?php endif; ?><?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" confirm_field='true'  <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date='date_box' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?> readonly>
        	<button type="button" role="date" class="btn" onclick="WdatePicker({el: '<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
',dateFmt:'<?php echo $this->_tpl_vars['confirm_info']['datefmt']; ?>
'})">
           		<span class="glyphicon glyphicon-calendar"></span>
        	</button>
    	</div>
           <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
        <?php else: ?>
         <input type="text" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php echo $this->_tpl_vars['product_info'][$this->_tpl_vars['real_field']]; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> />
          <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
         <?php endif; ?>
         
        </td>   
       <?php if ($this->_tpl_vars['product_count']%2 != 0): ?>
          </tr>
       <?php endif; ?>
       <?php echo smarty_function_counter(array('print' => false,'assign' => 'product_count'), $this);?>
<!--   加1   -->
     <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <!--自定义字段  ------end--------->
     <tr>
      	  <td class="narrow-label"><label for="product_state">产品状态：</label></td>		           
         <td >
         	<input type='radio' name='product_state' value='1' <?php if ($this->_tpl_vars['product_info']['product_state'] == 1 || ! $this->_tpl_vars['product_info']): ?>checked<?php endif; ?> />上架&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='product_state' value='2' <?php if ($this->_tpl_vars['product_info']['product_state'] == 2): ?>checked<?php endif; ?> />下架
         </td>       
         <td class="narrow-label"><label for="product_class_id">产品分类：</label></td>		           
         <td ><input id='product_class_id' name='product_class_id' value='<?php echo $this->_tpl_vars['product_info']['product_class_id']; ?>
'  class="easyui-validatebox" missingMessage="此项不能为空" required="true"/>
         <span style='color:red;font-weight:bold;'>*</span></td> 
      </tr>
     <tr>
         <td class="narrow-label"><label>产品图片：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_thum_pic']): ?>
          <span id='show_product_pic'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_pic']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_thum_pic']; ?>
' /></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_pic_only_name']; ?>
','product_pic','product_thum_pic')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_pic' name='product_pic' value=''/>
         </td>
     </tr>
     <tr>
      	<td class="narrow-label"><label>其他图片1：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic1']): ?>
          <span id='show_product_other_pic1'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic1']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic1']; ?>
' /></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name1']; ?>
','product_other_pic1','product_other_thum_pic1')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic1' name='product_other_pic1' value=''/>
         </td>
         <td class="narrow-label"><label>其他图片2：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic2']): ?>
         <span id='show_product_other_pic2'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic2']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic2']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name2']; ?>
','product_other_pic2','product_other_thum_pic2')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic2' name='product_other_pic2' value=''/>
         </td>
     </tr>
      <tr>
      	<td class="narrow-label"><label>其他图片3：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic3']): ?>
         <span id='show_product_other_pic3'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic3']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic3']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name3']; ?>
','product_other_pic3','product_other_thum_pic3')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic3' name='product_other_pic3' value=''/>
         </td>
         <td class="narrow-label"><label>其他图片4：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic4']): ?>
         <span id='show_product_other_pic4'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic4']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic4']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name4']; ?>
','product_other_pic4','product_other_thum_pic4')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic4' name='product_other_pic4' value=''/>
         </td>
     </tr>
      <tr>
      	<td class="narrow-label"><label>其他图片5：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic5']): ?>
         <span id='show_product_other_pic5'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic5']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic5']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name5']; ?>
','product_other_pic5','product_other_thum_pic6')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic5' name='product_other_pic5' value=''/>
         </td>
         <td class="narrow-label"><label>其他图片6：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic6']): ?>
         <span id='show_product_other_pic6'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic6']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic6']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name6']; ?>
','product_other_pic6','product_other_thum_pic6')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic6' name='product_other_pic6' value=''/>
         </td>
     </tr>
      <tr>
      	<td class="narrow-label"><label>其他图片7：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic7']): ?>
         <span id='show_product_other_pic7'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic7']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic7']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name7']; ?>
','product_other_pic7','product_other_thum_pic7')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic7' name='product_other_pic7' value=''/>
         </td>
         <td class="narrow-label"><label>其他图片8：</label></td>		           
         <td >
         <?php if ($this->_tpl_vars['product_info']['product_other_thum_pic8']): ?>
         <span id='show_product_other_pic8'>
         <a href='###' title='点击查看大图' path='<?php echo $this->_tpl_vars['product_info']['product_other_pic8']; ?>
' class='preview' onclick="window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id=<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
','menu_icon');"><img  src='<?php echo $this->_tpl_vars['product_info']['product_other_thum_pic8']; ?>
'/></a>&nbsp;&nbsp;<a href='javascript:;' onclick="delete_product_pic(<?php echo $this->_tpl_vars['product_info']['product_id']; ?>
,'<?php echo $this->_tpl_vars['product_info']['product_other_pic_only_name8']; ?>
','product_other_pic8','product_other_thum_pic8')" title='删除' class='easyui-tooltip'><img src='./image/no.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>
         <?php endif; ?>
           &nbsp;&nbsp;<input type='file' id='product_other_pic8' name='product_other_pic8' value=''/>
         </td>
     </tr>
  
      <input type="hidden" id="product_id" name="product_id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['product_info']['product_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
"/>
 </tbody> 
</table>
</form>
<div style='padding-left:100px;margin-top:20px;'>
	<div>操作说明：</div>
	<div style='padding-left:30px;'>1.产品分类、名称务必填选 ；</div>
	<div style='padding-left:30px;'>2.只能上传文件格式为 jpg | png | gif 的图片</div>
</div>
</div>
</div>
<div class='form-div' style='text-align:right;'>
	<span style='color:red;' id='_product_msg'></span>
	<a href='###' id="save_btn" class="easyui-linkbutton" iconCls="icon-save" onclick="save_product()">保存数据</a>&nbsp;&nbsp;
</div>
<div id="set_product_field_confirm"></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="./jssrc/jquery.preview.js"></script>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var global_product_class_id     = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['product_info']['product_class_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var power_fieldsconfirm_product = "<?php echo $this->_tpl_vars['power_fieldsconfirm_product']; ?>
";
var product_info_product_id     = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['product_info']['product_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
";
var jl_field_type = eval("(" + '<?php echo $this->_tpl_vars['jl_field_type']; ?>
' + ")");
</script>
<script src="./jssrc/viewjs/product_info.js?1.1" type="text/javascript"></script>