<?php /* Smarty version 2.6.19, created on 2015-08-12 15:03:33
         compiled from order_search_advan.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'order_search_advan.htm', 86, false),)), $this); ?>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
  <table>
  	<tbody>
  	<tr>
  	  <td>订 单 编 号 </td>
  	  <td align='left'><input type="text" id="order_num_search" name="order_num_search" /></td>
  	  <td > 订 单 状 态 </td>
  	  <td align='left'>
       <select name="order_state_search" id="order_state_search"> 
          <?php $_from = $this->_tpl_vars['order_state_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['okey'] => $this->_tpl_vars['info_state_search']):
?>
 	 		<option value="<?php echo $this->_tpl_vars['info_state_search']['name']; ?>
" ><?php echo $this->_tpl_vars['info_state_search']['name']; ?>
</option>
 	 	 <?php endforeach; endif; unset($_from); ?>
 	 	 	<option value="" selected>--请选择--</option>
 		</select>
      </td>
  	  <td  > 订 单 产 品 </td>
  	  <td align='left'>
  	  	<input type="text" style='width:151px;' id="product_search" name="product_search" /> <span style="color:red;">(支持【产品编号】/【产品名称】检索)</span>
  	  </td>
   </tr>
   <tr>
      <td> 客 户 名 称 </td>
      <td align='left'><input type="text"  id="cle_name_search" name="cle_name_search" /></td>
      <?php if ($this->_tpl_vars['order_base']['order_delivery_mode']): ?>
      <td > 配 送 方 式 </td>
      <td align='left'>
       <select name="order_delivery_mode" id="order_delivery_mode" order_base='true'> 
          <?php $_from = $this->_tpl_vars['delivery_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info_delivery']):
?>
 	 		<option value="<?php echo $this->_tpl_vars['info_delivery']['name']; ?>
" ><?php echo $this->_tpl_vars['info_delivery']['name']; ?>
</option>
 	 	 <?php endforeach; endif; unset($_from); ?>
 	 	 	<option value="" selected>--请选择--</option>
 		</select>
      </td>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['role_type'] != 2): ?>
      <td> 所属部门或人 </td>
      <td align='left' colspan='2'><input type="text" id="dept_user_search" name="dept_user_search" value='' style='width:151px;' /></td>
      <?php endif; ?>
     </tr>
    <tr>
     <td> 客 户 电 话 </td>
     <td align='left'><input type="text" id="cle_phone_search" name="cle_phone_search" value="" /></td>
     <?php if ($this->_tpl_vars['order_base']['order_delivery_number']): ?>
     <td > 配 送 单 号 </td>
     <td align='left'><input type="text" id="order_delivery_number" name="order_delivery_number" order_base='true' /></td>
     <?php endif; ?>
     <td> 下 单 时 间 </td>
     <td align='left'>
     	<div class="input-append">
         	<input type="text" name="order_accept_time_start" id="order_accept_time_start" class="input-small" readonly>
         	<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'order_accept_time_start',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
         		<span class="glyphicon glyphicon-calendar"></span>
         	</button>
      	</div> ~ 
      	<div class="input-append">
        	<input type="text" name="order_accept_time_end" id="order_accept_time_end" class="input-small" readonly>
        	<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'order_accept_time_end',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
         		<span class="glyphicon glyphicon-calendar"></span>
        	</button>
      	</div>
     </td>
    </tr>
     <?php if ($this->_tpl_vars['order_base']['cle_address']): ?>
    <tr>
     <td> 客 户 地 址 </td>
     <td colspan='11' align='left'><input type='text' id='cle_address' name='cle_address' value='' class="span8" order_base='true' /></td>
    </tr>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['order_base']['order_remark']): ?>
    <tr>
     <td > 备 注 </td>
     <td colspan='11' align='left'><input type='text' id='order_remark' name='order_remark' value='' class="span10" order_base='true' /></td>
    </tr>
    <?php endif; ?>
 </tbody>
</table>
<table>
 <tbody>
     <tr>
     	<td style='font-weight:bold;'>自定义字段(选择搜索)：</td>
     </tr>
     <tr>
     <td style='padding-left:16px;'>
     	<input name='field_confirm_box[]' id='check_1' type='checkbox'/>
   		<select name='field_confirm1' id='field_confirm1'   onchange='show_field_detail(1)' style='width:100px;'>
   			<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

   		</select>
    	<select id='make1' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
    	<span id='field_content1'><input type="text" id="f_c_1" name="f_c_1" value="" /></span>
     </td>
     <td style='padding-left:30px;'>
     	<input name='field_confirm_box[]' id='check_2' type='checkbox'/>
     	<select name='field_confirm2' id='field_confirm2'   onchange='show_field_detail(2)' style='width:100px;'>
     		<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

     	</select>
     	<select id='make2' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
     	<span id='field_content2'><input type="text" id="f_c_2" name="f_c_2" value="" /></span>
     </td>
       </tr>
     <tr>
     <td style='padding-left:16px;'>
     	<input name='field_confirm_box[]' id='check_3' type='checkbox'/>
     	<select name='field_confirm3' id='field_confirm3'  onchange='show_field_detail(3)' style='width:100px;'>
     		<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

     	</select>
     	<select id='make3' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
     	<span id='field_content3'><input type="text" id="f_c_3" name="f_c_3" value="" /></span>
     </td>
   <td style='padding-left:30px;'>
   		<input name='field_confirm_box[]' id='check_4' type='checkbox'/>
     	<select name='field_confirm4' id='field_confirm4'   onchange='show_field_detail(4)' style='width:100px;'>
     		<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

     	</select>
     	<select id='make4' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
     	<span id='field_content4'><input type="text" id="f_c_4" name="f_c_4" value="" /></span>
     </td>
     </tr><tr>
     <td style='padding-left:16px;'>
     	<input name='field_confirm_box[]' id='check_5' type='checkbox'/>
    	<select name='field_confirm5' id='field_confirm5'   onchange='show_field_detail(5)' style='width:100px;'>
    		<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

    	</select>
     	<select id='make5' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
     	<span id='field_content5'><input type="text" id="f_c_5" name="f_c_5" value="" /></span>
     </td>
     <td style='padding-left:30px;'>
     	<input name='field_confirm_box[]' id='check_6' type='checkbox'/>
     	<select name='field_confirm6' id='field_confirm6'   onchange='show_field_detail(6)' style='width:100px;'>
     		<option value=''>--请选择--</option><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['field_confirm'],'selected' => $this->_tpl_vars['field_confirm_selected']), $this);?>

     	</select> 
     	<select id='make6' name='make' style="width:60px;"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['condition']), $this);?>
</select>
     	<span id='field_content6'><input type="text" id="f_c_6" name="f_c_6" value="" /></span>
  	 </td>
  </tr>
 </tbody>
</table>
<span style='padding-left:30px;'>&nbsp;</span>
<button class="btn btn-primary" onclick="$('#searchForm').submit();">
    <span class="glyphicon glyphicon-search"></span> 搜索
</button>
<button type="button" class="btn" onclick="base_search_order()">
    <span class="glyphicon glyphicon-zoom-in"></span> 基本搜索
</button>
</form>
<script language="JavaScript" type="text/javascript">
var role_type = <?php echo $this->_tpl_vars['role_type']; ?>
;
var user_session_id = <?php echo $this->_tpl_vars['user_session_id']; ?>
;
var dept_session_id = <?php echo $this->_tpl_vars['dept_session_id']; ?>
;
</script>
<script src="./jssrc/viewjs/order_search_advan.js?1.2" type="text/javascript"></script>