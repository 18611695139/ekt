<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:37
         compiled from client_search_advan.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_search_advan.htm', 7, false),array('function', 'html_options', 'client_search_advan.htm', 166, false),)), $this); ?>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
  <table>
  	<tbody>
  	<tr>
  		<td>客 户 名 称 </td>
  		<td align="left" colspan="2"><input type="text" style='color:#cccccc;width:151px;' id="cle_name_search" name="cle_name_search" value="支持拼音首字母" onclick='search_cle_name()' onblur='if_null()' /></td>
      	<?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'data_deal' && ! $this->_tpl_vars['power_use_contact']): ?>
     	<td> 联系人名称 </td>
     	<td align='left'><input type='text' id='con_name_search' name='con_name_search' value='' style='width:151px;' /></td>
     	<?php endif; ?>
     	<?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'my_client'): ?>
     	<?php if (( ((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) == 'public' ) && ( $this->_tpl_vars['role_type'] != 2 || $this->_tpl_vars['power_public_all'] )): ?>
     	<td> 所 属 部 门 </td>
     	<td align='left' colspan='2'>
     		<input type="text" id="dept_id_search" name="dept_id_search" value='' style='width:151px;' />
     	</td>
     	<?php elseif (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'public' && $this->_tpl_vars['role_type'] != 2): ?>
     	<td> 所属部门或人 </td>
     	<td align='left' colspan='2'>
     		<input type="text" id="dept_user_search" name="dept_user_search" value='' style='width:151px;' />&nbsp;&nbsp;<?php if ($this->_tpl_vars['no_user_search']): ?>
     		<span id='no_color'><input name='no_user_data' id='no_user_data' type='checkbox'/> 无所属人 </span><?php endif; ?>
     	</td>
     	<?php endif; ?>
     	<?php endif; ?>
    </tr>
    <tr>
     	<td> 客 户 电 话 </td>
     	<td align="left" colspan="2"><input type="text" id="cle_phone_search" name="cle_phone_search" value="" style='width:151px;' /></td>
      	<?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'data_deal' && ! $this->_tpl_vars['power_use_contact']): ?>
      	<td> 联系人电话 </td>
      	<td align='left'><input type='text' id='con_mobile_search' name='con_mobile_search' value='' style='width:151px;' /></td>
      	<?php endif; ?>
     	<?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) == 'public'): ?>
      	<td> 公共数据类型 </td>
      	<td align='left'>
      		<select name='cle_public_type' id='cle_public_type' style='width:151px;'>
      			<option value=''>--请选择--</option>
      			<option value='3'><?php echo $this->_tpl_vars['client_public_type']['3']; ?>
</option>
      			<option value='1'><?php echo $this->_tpl_vars['client_public_type']['1']; ?>
</option>
      			<option value='2'><?php echo $this->_tpl_vars['client_public_type']['2']; ?>
</option>
      		</select>
      	</td>
      	<?php endif; ?>
    </tr>
    <tr>
      	<td> 最近联系时间 </td>
      	<td align='left' colspan='2'>
      		<div class="input-append">
         		<input type="text" name="cle_last_connecttime_search_start" id="cle_last_connecttime_search_start" class="input-small" readonly>
         		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_last_connecttime_search_start'})">
         			<span class="glyphicon glyphicon-calendar"></span>
         		</button>
      		</div> ~ 
      		<div class="input-append">
        		<input type="text" name="cle_last_connecttime_search_end" id="cle_last_connecttime_search_end" class="input-small" readonly>
        		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_last_connecttime_search_end'})">
         			<span class="glyphicon glyphicon-calendar"></span>
        		</button>
      		</div>
     	</td>
     	<td> 导入批次号 </td>
     	<td align='left'><input type='text' id='impt_id_search' name='impt_id_search' value='' style='width:151px;' /></td>
     	<?php if ($this->_tpl_vars['client_base']['cle_stage']): ?>
     	<td> 客 户 阶 段 </td>
     	<td align='left' >
     		<select name='cle_stage' id='cle_stage' client_base='true' style='width:151px;'>
     			<option value=''>--请选择--</option>
                <?php $_from = $this->_tpl_vars['_stage']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['stage']):
?>
 	 			<option value="<?php echo $this->_tpl_vars['stage']; ?>
" ><?php echo $this->_tpl_vars['stage']; ?>
</option>
 	 			<?php endforeach; endif; unset($_from); ?>
     		</select>
     	</td>
     	<?php endif; ?>
     </tr>
     <tr>
     	<td> 下次联系时间 </td>
     	<td align='left' colspan='2'>
     		<div class="input-append">
         		<input type="text" name="con_rec_next_time_start" id="con_rec_next_time_start" class="input-small" readonly>
         		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'con_rec_next_time_start'})">
         			<span class="glyphicon glyphicon-calendar"></span>
         		</button>
      		</div> ~ 
      		<div class="input-append">
        		<input type="text" name="con_rec_next_time_end" id="con_rec_next_time_end" class="input-small" readonly>
        		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'con_rec_next_time_end'})">
         			<span class="glyphicon glyphicon-calendar"></span>
        		</button>
      		</div>
     	</td>
     	<td > 通 话 次 数 </td>
     	<td align='left'><input type="text" id='cle_dial_number_search' name='cle_dial_number_search' value='' style='width:151px;'></td>
     	<?php if ($this->_tpl_vars['client_base']['cle_stat']): ?>
     	<td > 号 码 状 态 </td>
     	<td align='left' colspan='2'>
     		<select name='cle_stat' id='cle_stat' client_base='true' style='width:151px;'>><option value=''>--请选择--</option>
     			<?php $_from = $this->_tpl_vars['client_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stat']):
?>
 	 			<option value="<?php echo $this->_tpl_vars['stat']; ?>
" ><?php echo $this->_tpl_vars['stat']; ?>
</option>
 	 			<?php endforeach; endif; unset($_from); ?>
     		</select>
     	</td>
     	<?php endif; ?>
     </tr>
     <tr>
     	<td> 创 建 时 间 </td>
     	<td align='left' colspan='2'>
      		<div class="input-append">
         		<input type="text" name="cle_creat_time_search_start" id="cle_creat_time_search_start" class="input-small" readonly>
         		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_creat_time_search_start'})">
         			<span class="glyphicon glyphicon-calendar"></span>
         		</button>
      		</div> ~ 
      		<div class="input-append">
        		<input type="text" name="cle_creat_time_search_end" id="cle_creat_time_search_end" class="input-small" readonly>
        		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_creat_time_search_end'})">
         			<span class="glyphicon glyphicon-calendar"></span>
        		</button>
      		</div>
     	</td>
     	<?php if ($this->_tpl_vars['client_base']['cle_phone2']): ?>
     	<td> 办 公 电 话 </td>
     	<td align='left'><input type='text' id='cle_phone2' name='cle_phone2' value='' client_base='true' style='width:151px;'/></td>
     	<?php endif; ?>
     	<?php if ($this->_tpl_vars['client_base']['cle_info_source']): ?>
	 	<td>信 息 来 源</td>
	 	<td><select name="cle_info_source" id="cle_info_source" client_base='true' style='width:151px;'>
 	 		<option value="" SELECTED >--请选择--</option>
 			<?php $_from = $this->_tpl_vars['cle_info_source']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['info_source']):
?>
 	 		<option value="<?php echo $this->_tpl_vars['info_source']['name']; ?>
" ><?php echo $this->_tpl_vars['info_source']['name']; ?>
</option>
 	 		<?php endforeach; endif; unset($_from); ?></select>
 	 	</td>
 	 	<?php endif; ?>
     </tr>
     <tr>
     	<td> 更 新 时 间 </td>
     	<td align='left' colspan='2'>
      		<div class="input-append">
         		<input type="text" name="cle_update_time_search_start" id="cle_update_time_search_start" class="input-small" readonly>
         		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_update_time_search_start'})">
         			<span class="glyphicon glyphicon-calendar"></span>
         		</button>
      		</div> ~ 
      		<div class="input-append">
        		<input type="text" name="cle_update_time_search_end" id="cle_update_time_search_end" class="input-small" readonly>
        		<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'cle_update_time_search_end'})">
         			<span class="glyphicon glyphicon-calendar"></span>
        		</button>
      		</div>
     	</td>
     	<?php if ($this->_tpl_vars['client_base']['cle_phone3']): ?>
     	<td> 其 他 电 话 </td>
     	<td align='left'><input type="text" id="cle_phone3" name="cle_phone3" value="" client_base='true' style='width:151px;'/></td>
     	<?php endif; ?>
     	<?php if ($this->_tpl_vars['role_type'] != 2): ?>
     	<td> 调 配 次 数 </td>
     	<td align='left'><input type="text" id="dployment_num_search" name="dployment_num_search" value="" style='width:151px;'/></td>
     	<?php endif; ?>
     </tr>
     <?php if ($this->_tpl_vars['client_base']['cle_province_name'] || $this->_tpl_vars['client_base']['cle_address']): ?>
     <tr>
        <td> 详 细 地 址 </td>
        <td colspan='6' align='left'>
        	<?php if ($this->_tpl_vars['client_base']['cle_province_name']): ?>
     		<select id="cle_province_id" name='cle_province_id' style="width:90px;" client_base='true' onchange="change_regions_type('cle_province_id','cle_city_id','2');">
     			<option value="0" >--选择省--</option>
				<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['regions_info']), $this);?>

			</select>
			<select id="cle_city_id" name="cle_city_id" style="width:90px;" client_base='true'>
			<option value="0">--选择市--</option>
			</select>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['client_base']['cle_address']): ?>
       		<input type='text' id='cle_address' name='cle_address' value='' class="span5" client_base='true'/>
       		<?php endif; ?>
       	</td>
     </tr>
     <?php endif; ?>
     <?php if ($this->_tpl_vars['client_base']['cle_remark']): ?>
     <tr>
     	 <td> 备 注 </td>
     	 <td colspan='6' align='left'><input type='text' id='cle_remark' name='cle_remark' value='' class="span8" client_base='true' /></td>
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
	     	<select name='field_confirm2' id='field_confirm2' onchange='show_field_detail(2)' style='width:100px;'>
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
	     	<select name='field_confirm3' id='field_confirm3' onchange='show_field_detail(3)' style='width:100px;'>
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
	     	<span id='field_content6'><input type="text" id="f_c_6" name="f_c_6" value=""/></span>
	  	</td>
  </tr>
 </tbody>
</table>
<span style='padding-left:30px;'>&nbsp;</span>
<button class="btn btn-primary" onclick="$('#searchForm').submit();">
    <span class="glyphicon glyphicon-search"></span> 搜索
</button>
<?php if (((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')) != 'data_deal'): ?>
<button type="button" class="btn" onclick="base_search()">
    <span class="glyphicon glyphicon-zoom-in"></span> 基本搜索
</button>
<?php endif; ?>
</form>
<script language="JavaScript" type="text/javascript">
var role_type = '<?php echo $this->_tpl_vars['role_type']; ?>
';
var user_session_id = '<?php echo $this->_tpl_vars['user_session_id']; ?>
';
var dept_session_id = '<?php echo $this->_tpl_vars['dept_session_id']; ?>
';
var no_user_search = '<?php echo ((is_array($_tmp=@$this->_tpl_vars['no_user_search'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
'; //是否有无所属人检索
var control_flag = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['control_flag'])) ? $this->_run_mod_handler('default', true, $_tmp, 'manage') : smarty_modifier_default($_tmp, 'manage')); ?>
";
var list_type = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['list_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var power_use_contact = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['power_use_contact'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
";
var power_public_all = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['power_public_all'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
";
</script>
<script src="./jssrc/viewjs/client_search_advan.js?1.3" type="text/javascript"></script>