<?php /* Smarty version 2.6.19, created on 2015-08-07 15:07:48
         compiled from service_search_advan.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'service_search_advan.htm', 59, false),array('modifier', 'default', 'service_search_advan.htm', 114, false),)), $this); ?>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
  <table>
  	<tbody>
     <tr>
     <td  >
       <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" /> 服 务 类 型</td>
     <td align='left'  colspan="3">
		     <?php $_from = $this->_tpl_vars['service_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
		 	   <input type="radio"  name="serv_type_search" value="<?php echo $this->_tpl_vars['sitem']['name']; ?>
"  /><?php echo $this->_tpl_vars['sitem']['name']; ?>

		 	 <?php endforeach; endif; unset($_from); ?></td>
	 </tr>
     <tr>
 	  <td style='padding-left:30px;' >服 务 状 态</td><td align='left'  colspan="3">
 	        <?php $_from = $this->_tpl_vars['service_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
 	         <input type="radio"  name="serv_state_search" value="<?php echo $this->_tpl_vars['skey']; ?>
"   /><?php echo $this->_tpl_vars['skey']; ?>

 	         <?php endforeach; endif; unset($_from); ?></td>
     </tr>
     <tr>
     <td style='padding-left:30px;' >客 户 姓 名</td><td><input id="cle_name_search" name="cle_name_search" value=""/></td>
     <td  >客 户 电 话</td><td><input id="cle_phone_search" name="cle_phone_search" value=""/></td>
     </tr>
     <?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
     <tr>
     <td style='padding-left:30px;' >联系人姓 名</td><td><input id="con_name_search" name="con_name_search" value=""/></td>
     <td  >联系人电 话</td><td><input id="con_mobile_search" name="con_mobile_search" value=""/></td>
     </tr>
     <?php endif; ?>
     <tr>
      <td style='padding-left:30px;'> 转 交 人 </td><td align='left' ><span class='easyui-tooltip' data-options="position:'right'" title='支持模糊搜索'><input type="text" id="deal_user_id_search" name="deal_user_id_search" value='' style='width:152px;' /></span></td>
      <td> 受 理 人 </td><td align='left'><span class='easyui-tooltip' data-options="position:'right'" title='支持模糊搜索'><input type="text" id="user_id_search" name="user_id_search" value='' style='width:152px;' /></span></td>
      <?php if ($this->_tpl_vars['role_type'] != 2): ?>
     <td> 处 理 部 门 </td><td align='left' colspan='2'><input id="dept_id_search" name="dept_id_search" value="" style='width:120px;' /><a href='###' class="easyui-linkbutton" data-options="plain:true"  onclick="clear_dept()"> 清空 </a></td>
     <?php endif; ?>
     </tr>
     <tr>
     <td  style='padding-left:30px;'>受 理 时 间</td><td align='left' colspan='3'><span class="combo datebox" style="width: 115px;"><input type="text" class="combo-text validatebox-text" id='accept_time_search_start' name='accept_time_search_start'  style="width: 95px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'accept_time_search_start' ,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></span></span></span> ~  <span class="combo datebox" style="width: 115px;"><input type="text" class="combo-text validatebox-text" id='accept_time_search_end' name='accept_time_search_end'  style="width: 95px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'accept_time_search_end' ,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></span></span></span></td>
     </tr>
     
      <tr>
     <td  style='padding-left:30px;'>处 理 时 间</td><td align='left' colspan='3'><span class="combo datebox" style="width: 115px;"><input type="text" class="combo-text validatebox-text" id='deal_time_search_start' name='deal_time_search_start'  style="width: 95px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_time_search_start' ,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></span></span></span> ~  <span class="combo datebox" style="width: 115px;"><input type="text" class="combo-text validatebox-text" id='deal_time_search_end' name='deal_time_search_end'  style="width: 95px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_time_search_end',dateFmt:'yyyy-MM-dd HH:mm:ss' })" ></span></span></span></td>
     </tr>
      <tr>
     <td style='padding-left:30px;' align="right">内 容&nbsp;&nbsp;</td><td colspan='3' align='left'><input type='text' id='serv_content_search' name='serv_content_search' value='' size='40' /></td>
     </tr>
     <tr>
     <td style='padding-left:30px;' align="right">备 注&nbsp;&nbsp;</td><td colspan='3' align='left'><input type='text' id='serv_remark_search' name='serv_remark_search' value='' size='40' /></td>
     </tr>
     </tbody>
     </table>
     <table>
     </tbody>
     <tr>
     	<td style='padding-left:28px;font-weight:bold;'>自定义字段(选择搜索)：</td>
     </tr>
     <tr>
     	<td style='padding-left:36px;'>
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
     	<td style='padding-left:36px;'>
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
     </tr>
     <tr>
     	<td style='padding-left:36px;'>
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
     	<span id='field_content6'><input type="text" id="f_c_6" name="f_c_6" value=""/></span>
  	 </td>
  	</tr>
    </tbody>
   
    </table>
    <span style='padding-left:30px;'>&nbsp;</span>
    <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
     <?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'data_deal'): ?>
     <img alt='基本搜索' src='image/switch.png'>
     <a href='###' onclick='base_search()' title='基本搜索' style='color:red;'>基本搜索</a>
     <?php endif; ?>
	    </form>
<script language="JavaScript" type="text/javascript">
var role_type = <?php echo $this->_tpl_vars['role_type']; ?>
;
var power_use_contact = <?php echo $this->_tpl_vars['power_use_contact']; ?>
;//是否使用联系人模块
</script>
<script src="./jssrc/viewjs/service_search_advan.js" type="text/javascript"></script>