<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:34
         compiled from service_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'service_info.htm', 33, false),array('modifier', 'cat', 'service_info.htm', 115, false),array('function', 'counter', 'service_info.htm', 92, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['form_act_service'] != 'insert_service'): ?>
<div id='service_tools'>
	<div class="main-div" title='客服服务信息' style="padding-bottom: 0">
	<?php endif; ?>
	<table class="table table-condensed" style="margin-bottom: 0">
	    <tbody>
	      <tr> 
	         <td class="span2" style="text-align: right"><label for="serv_type">服务类型：<label></td>
	         <td colspan="5" >
	         <?php $_from = $this->_tpl_vars['service_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
 	         <input type="radio"  name="serv_type" value="<?php echo $this->_tpl_vars['sitem']['name']; ?>
"  <?php if ($this->_tpl_vars['service_info']['serv_type'] == $this->_tpl_vars['sitem']['name']): ?> CHECKED <?php endif; ?> /><?php echo $this->_tpl_vars['sitem']['name']; ?>

 	         <?php endforeach; endif; unset($_from); ?></td>
	      </tr>
	      <tr> 
	         <td class="span2" style="text-align: right"><label for="serv_state">状态：<label></td>
	         <td colspan="5" >
	         <?php $_from = $this->_tpl_vars['service_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
?>
 	         <input type="radio"  name="serv_state" value="<?php echo $this->_tpl_vars['skey']; ?>
"   onclick="click_service_state();"   <?php if ($this->_tpl_vars['service_info']['serv_state'] == $this->_tpl_vars['skey']): ?>CHECKED<?php endif; ?> /><?php echo $this->_tpl_vars['skey']; ?>

 	         <?php endforeach; endif; unset($_from); ?></td>
	      </tr>

          <?php if ($this->_tpl_vars['form_act_service'] == 'insert_service' && $this->_tpl_vars['service_info']['cle_id']): ?>
          <input type="hidden" id="cle_name_service"  name="cle_name_service"  value="<?php echo $this->_tpl_vars['service_info']['cle_name']; ?>
" readonly/>
          <input type="hidden" id="cle_phone_service"  name="cle_phone_service"  value="<?php echo $this->_tpl_vars['service_info']['cle_phone']; ?>
" />
          <input type="hidden"  id='cle_id_service' name='cle_id_service' value='<?php echo $this->_tpl_vars['service_info']['cle_id']; ?>
'  />
          <?php else: ?>
	      <tr>
         <td class="span2" style="text-align: right"><label for="cle_name_service">客户名称：</label></td>		           
         <td >
          <div class="input-append">
              <input type="text" id="cle_name_service"  name="cle_name_service"  value="<?php echo $this->_tpl_vars['service_info']['cle_name']; ?>
" readonly/>
              <input type="hidden"  id='cle_id_service' name='cle_id_service' value='<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
'  />
               <button type="button" class="btn" onclick="_add_client()" title="查找客户">
                    <span class="glyphicon glyphicon-zoom-in"></span>
               </button>
               <button type="button" class="btn" onclick="_client_detail()" title="客户详情">
                    <span class="glyphicon glyphicon-briefcase"></span>
               </button>
          </div>
         </td>
         <td class="span2" style="text-align: right"><label for="cle_phone_service">客户电话：</label></td>		           
         <td >
         	<div class="input-append">
               <input type="text" id="cle_phone_service"  name="cle_phone_service"  value="<?php echo $this->_tpl_vars['service_info']['cle_phone']; ?>
" />
               <button type="button" class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['service_info']['cle_phone']; ?>
','cle_phone')" title="呼叫号码">
                    <span class="glyphicon glyphicon-phone"></span>
               </button>
               <?php if ($this->_tpl_vars['power_sendsms']): ?>
               <button type="button" class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['service_info']['cle_phone']; ?>
','cle_phone')" title="发送短信">
                    <span class="glyphicon glyphicon-envelope"></span>
               </button>
               <?php endif; ?>
             </div>
             <input type='hidden' id='real_cle_phone_service' name='real_cle_phone_service' value='<?php echo $this->_tpl_vars['service_info']['cle_phone']; ?>
' />
         </td>
         </tr>
         <?php endif; ?>

     <?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>    
      <tr>
       <td class="span2" style="text-align: right"><label for="con_name_service">联系人姓名：</label></td>		           
         <td >
         <input id="con_name_service" name="con_name_service" />
         </td>
         <td class="span2" style="text-align: right"><label for="con_mobile_service">联系人电话：</label></td>		           
         <td>
         <div class="input-append">
              <input type="text" id="con_mobile_service"  name="con_mobile_service"  value="<?php echo $this->_tpl_vars['service_info']['con_mobile']; ?>
" />
              <button type="button" class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['service_info']['con_mobile']; ?>
','con_mobile')" title="呼叫号码">
                  <span class="glyphicon glyphicon-phone"></span>
              </button>
              <?php if ($this->_tpl_vars['power_sendsms']): ?>
              <button type="button" class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['service_info']['con_mobile']; ?>
','con_mobile')" title="发送短信">
                   <span class="glyphicon glyphicon-envelope"></span>
              </button>
              <?php endif; ?>
         </div>
         <input type="hidden" id="real_con_mobile_service"  name="real_con_mobile_service"  value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['con_mobile'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
" /> 
         </td>
      </tr>
      <?php endif; ?>      
      <tr>
         <td class="span2" style="text-align: right"><label for="deal_user_id">转交人：</label></td>		           
         <td >
         <input id="deal_user_id" name="deal_user_id"  value="<?php echo $this->_tpl_vars['service_info']['user_id']; ?>
"/>
         <span style='color:red;font-weight:bold;'>*</span>
         </td>
      </tr>
	      
<!--自定义字段  -----begin-------->
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'service_count'), $this);?>
<!--  计算  -->
	<?php $_from = $this->_tpl_vars['service_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
	 <?php $this->assign('parent_id', $this->_tpl_vars['confirm_info']['id']); ?>
	 <?php $this->assign('real_field', $this->_tpl_vars['confirm_info']['fields']); ?>
	 
	 <?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
	 <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'service_count'), $this);?>
<!-- 从新计算 -->
	 <tr>
	     <td class="span2" style="text-align: right"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td>		           
	     <td colspan="5">
	      <textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" service_confirm_field='true' class="span12" rows="3" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" _date="textarea_box" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" service_if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> ><?php if ($this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]): ?><?php echo $this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?></textarea>
	      <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	     </td>
	 </tr>
	 
	 <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
	 <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'service_count'), $this);?>
<!-- 从新计算 -->
	 <tr>
	 	<td class="span2" style="text-align: right"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td>
	 	<td colspan="11">
	 	<span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1">
	 	 <select name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1'  service_confirm_field='true' onchange="get_comfirm_jl_options_service(1,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" service_if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
_1'<?php endif; ?> >
		 <option value="" >--请选择--</option>
		  <?php $this->assign('field_type', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_1') : smarty_modifier_cat($_tmp, '_1'))); ?>
		  <?php $_from = $this->_tpl_vars['jl_options_service'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
          <option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
	     </select>
	    </span>
	    <?php $this->assign('field_type2', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_2') : smarty_modifier_cat($_tmp, '_2'))); ?>
	    <?php $this->assign('jl_f_t', $this->_tpl_vars['confirm_info']['jl_field_type']); ?>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2">
	    	<?php if ($this->_tpl_vars['service_info'][$this->_tpl_vars['field_type']]): ?>
	    	 	<?php $this->assign('p_id', $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type']]); ?>
	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2' value='<?php echo $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type2']]; ?>
' service_confirm_field='true'/>
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2" service_confirm_field='true' <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>onchange="get_comfirm_jl_options_service(2,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)"<?php endif; ?> >
        				<option value="">--请选择--</option>
       	 				<?php $_from = $this->_tpl_vars['jl_options_service'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
        				<option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type2']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
        				<?php endforeach; endif; unset($_from); ?>
        			</select>
	    		<?php endif; ?>
	    	 <?php endif; ?>
	     	 </span>
	     <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>      
	     <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3">
	     <?php $this->assign('field_type3', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_3') : smarty_modifier_cat($_tmp, '_3'))); ?>
	    	<?php if ($this->_tpl_vars['service_info'][$this->_tpl_vars['field_type2']]): ?>
	    	<?php $this->assign('p_id2', $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type2']]); ?>
	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3' value='<?php echo $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type3']]; ?>
' service_confirm_field='true'/>
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3" style="width:130px;" service_confirm_field='true'>
        				<option value="">--请选择--</option>
       	 				<?php $_from = $this->_tpl_vars['jl_options_service'][$this->_tpl_vars['field_type3']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
        				<option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type3']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
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
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'service_count'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="span2" style="text-align: right">
        <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label>
    </td>
 	<td  colspan="11">
		<table>
			<?php $this->assign('field_type', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['id'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_1') : smarty_modifier_cat($_tmp, '_1'))); ?>
			<?php $this->assign('field_type2', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['id'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_2') : smarty_modifier_cat($_tmp, '_2'))); ?>
			<?php if ($this->_tpl_vars['service_info']): ?>
				<?php $this->assign('field_value', $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type']]); ?>
				<?php $this->assign('field_value2', $this->_tpl_vars['service_info'][$this->_tpl_vars['field_type2']]); ?>
			<?php endif; ?>
			<?php $_from = $this->_tpl_vars['checkbox_options_service'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id1'] => $this->_tpl_vars['check_option1']):
?>
     		<tr>
                <th style="padding-bottom:5px;"><?php echo $this->_tpl_vars['check_option1']['name']; ?>
</th>
     		</tr>
     		<tr>
     			<td style="padding-bottom:5px;">
      				<?php $_from = $this->_tpl_vars['checkbox_options_service'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id2'] => $this->_tpl_vars['check_option']):
?>
      					<?php if ($this->_tpl_vars['check_option']['p_id'] == $this->_tpl_vars['id1']): ?>
         					<input type="checkbox" checkbox_name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" checkbox_pid="<?php echo $this->_tpl_vars['id1']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2[]" value="<?php echo $this->_tpl_vars['id2']; ?>
" service_confirm_field='true' <?php if ($this->_tpl_vars['field_value2'][$this->_tpl_vars['id2']] == $this->_tpl_vars['id2']): ?>checked<?php endif; ?> /> <?php echo $this->_tpl_vars['check_option']['name']; ?>
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
	 <?php if ($this->_tpl_vars['service_count']%2 == 0): ?>
	 <tr>
	 <?php endif; ?>
	    <td class="span2" style="text-align: right" >  <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  ><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：<label></td>
	    <td >
	      <?php if ($this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']] || $this->_tpl_vars['confirm_info']['data_type'] == 2): ?>
	      <select id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" service_confirm_field='true' <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" service_if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> >
	         <?php $_from = $this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
	         <option value="<?php echo $this->_tpl_vars['detail']; ?>
" <?php if ($this->_tpl_vars['detail'] == $this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['detail']; ?>
</option>
	         <?php endforeach; endif; unset($_from); ?>
	         <option value="" <?php if (! $this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> >&nbsp;</option>
	       </select><?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	     <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 5): ?><!-- 日期框 -->
          <div class="input-append">
        	<input type="text" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php if ($this->_tpl_vars['form_act_service'] == 'update_service'): ?><?php echo $this->_tpl_vars['service_info'][$this->_tpl_vars['confirm_info']['fields']]; ?>
<?php elseif ($this->_tpl_vars['confirm_info']['default'] == '系统时间'): ?><?php if ($this->_tpl_vars['confirm_info']['datefmt'] == 'yyyy-MM-dd'): ?><?php echo $this->_tpl_vars['now_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['now_time']; ?>
<?php endif; ?><?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" service_confirm_field='true'  <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date='date_box' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' service_if_require='true'<?php endif; ?> readonly>
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
" service_confirm_field='true' value="<?php if ($this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]): ?><?php echo $this->_tpl_vars['service_info'][$this->_tpl_vars['real_field']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" service_if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> />
	       <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	   <?php endif; ?>
	   </td>  
	<?php if ($this->_tpl_vars['service_count']%2 != 0): ?>
    </tr>
    <?php endif; ?>
    <?php echo smarty_function_counter(array('print' => false,'assign' => 'service_count'), $this);?>
<!--   加1   -->
 <?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<!--自定义字段  ------end--------->
<?php if ($this->_tpl_vars['service_base']['serv_content']): ?>
	<tr>
	    <td class="span2" style="text-align: right"><label for="serv_content">服务内容：</label></td>		           
	    <td colspan="5"><textarea id="serv_content"  name="serv_content" class="span12" rows="3" base_field='true' cols="20"><?php echo $this->_tpl_vars['service_info']['serv_content']; ?>
</textarea></td>	      
	</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['service_base']['serv_remark']): ?>
	<tr>
	    <td class="span2" style="text-align: right"><label for="serv_remark">备注：</label></td>		           
	    <td colspan="5"><textarea id="serv_remark"  name="serv_remark" class="span12" rows="3" base_field='true' cols="20"><?php echo $this->_tpl_vars['service_info']['serv_remark']; ?>
</textarea></td>	      
	</tr>
<?php endif; ?>
	</tbody>
  </table>
<?php if ($this->_tpl_vars['form_act_service'] != 'insert_service' || ! $this->_tpl_vars['service_info']): ?>
	</div>
</div>
<?php endif; ?>

<div style="text-align:right; padding: 5px;" class="form-div form-inline">
<input type="hidden" id="serv_id"  name="serv_id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['serv_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
"/>
<span id='_service_msg' style='color:red;'></span>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($this->_tpl_vars['form_act_service'] == 'insert_service' || ( $this->_tpl_vars['form_act_service'] == 'update_service' && $this->_tpl_vars['power_service_edit'] )): ?>
<?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
<input type="checkbox" id="save_new_contact" name="save_new_contact" onclick="record_memrot_cookie()" /><label title="保存新联系人信息" class='easyui-tooltip'>自动保存新联系人</label>
<?php endif; ?>
<button type="button" class="btn btn-primary" id="save_service" onclick="save_service_info()">
     <span class="glyphicon glyphicon-saved"></span> 保存信息
</button>
<?php endif; ?>
</div>

<div id="set_field_confirm"></div>
<div id="set_dictionary"></div>
<div id="client_window"></div>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var original_data_service = {};//原始数据
var flag_con_info = 0;
<?php if ($this->_tpl_vars['service_info']['con_name']): ?>
flag_con_info = 1;
<?php endif; ?>
var power_phone_view = <?php echo ((is_array($_tmp=@$this->_tpl_vars['power_phone_view'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var power_service_edit = <?php echo ((is_array($_tmp=@$this->_tpl_vars['power_service_edit'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;

var form_act_service = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['form_act_service'])) ? $this->_run_mod_handler('default', true, $_tmp, 'insert_service') : smarty_modifier_default($_tmp, 'insert_service')); ?>
";

var global_cle_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var global_con_name = '<?php echo $this->_tpl_vars['service_info']['con_name']; ?>
';
var global_user_id = '<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
';
var global_cle_phone = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['cle_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var global_con_mobile = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_info']['con_mobile'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var power_use_contact = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['power_use_contact'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
";
var jl_field_type_service = eval("(" + '<?php echo $this->_tpl_vars['jl_field_type_service']; ?>
' + ")");

$(document).ready(function(){
	//客户电话的‘*’号隐藏号码处理
	if(power_phone_view == 0)
	{
		//客户电话
		$('#cle_phone_service').val(hidden_part_number($('#cle_phone_service').val()));
		if(power_use_contact != 1)
		{
			//联系人电话
			$('#con_mobile_service').val(hidden_part_number($('#con_mobile_service').val()));
		}
	}

	<?php if ($this->_tpl_vars['power_service_confirm'] || $this->_tpl_vars['power_service_dictionary']): ?>
	$('#service_tools').tabs({
		tools:[
		<?php if ($this->_tpl_vars['power_service_confirm']): ?>
		{
			iconCls:'icon-seting',
			text:'服务自定义字段',
			handler:function(){
				$('#set_field_confirm').window({
					title: '客服服务自定义字段设置',
					href:"index.php?c=field_confirm&m=field_setting&field_type=4",
					iconCls: "icon-seting",
					top:150,
					width:700,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false,
					shadow:false,
					modal:true
				});
			}
		},
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_service_dictionary']): ?>
		{
			iconCls:'icon-seting',
			text:'数据字典',
			handler:function(){
				$('#set_dictionary').window({
					title: '数据字典',
					href:"index.php?c=dictionary&type=service",
					iconCls: "icon-seting",
					top:150,
					width:500,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false,
					closable:false,
					shadow:false,
					modal:true
				});
			}
		}
		<?php endif; ?>
		]
	});
	<?php endif; ?>

	if(power_use_contact != 1)
	{
		var url = '';
		if(global_cle_id)
		{
			url = "index.php?c=contact&m=get_contact_list";
		}
		//联系人
		$('#con_name_service').combogrid({
			panelWidth:230,
			sortName:'con_if_main',
			sortOrder:'desc',
			idField:'con_id',
			textField:'con_name',
			url:url,
			queryParams:{"cle_id":global_cle_id},
			columns:[[
			{field:'con_name',title:'姓名',width:100,align:"center",sortable:true},
			{field:'con_mobile',title:'电话',width:120,align:"center",sortable:true,formatter:function(value,rowData,rowIndex){
				//客户电话的‘*’号隐藏号码处理
				if(power_phone_view)
				return value;
				else
				{
					if( value )
					{
						return hidden_part_number(value);
					}
				}
			}}
			]],
			onClickRow:function(rowIndex,rowData)
			{
				var sec_con_mobile = rowData.con_mobile
				$("#real_con_mobile_service").val(sec_con_mobile);
				//不显示全部号码
				if(!power_phone_view)
				{
					if( sec_con_mobile )
					{
						sec_con_mobile =  hidden_part_number(sec_con_mobile);
					}
				}
				$("#con_mobile_service").val(sec_con_mobile);
			},
			keyHandler: {
				up: function(){},
				down: function(){},
				enter: function(){},
				query: function(q){
					//不显示panel
					$(this).combo('hidePanel');
				}
			}
			,
			onLoadSuccess:function()
			{
				if( flag_con_info > 0 )
				{
					$('#con_name_service').combogrid("setText",global_con_name);
					flag_con_info = 0;
				}
			}
		});

		//保存新联系人
		if(form_act_service == 'insert_service' || (form_act_service == 'update_service' && power_service_edit))
		{
			if(get_cookie("est_service_contact") == 1)
			{
				$("#save_new_contact").attr("checked","true")
			}
		}
	}

	//转交人
	$('#deal_user_id').combobox({
		url:'index.php?c=user&m=get_user_box',
		valueField:'user_id',
		textField:'user_name_num',
		mode:'remote'
	});
	
	/*    状态选择
	* 仅是“未处理”和“处理中”才可以选择转交人
	*/
	click_service_state();

	//记录原始数据
	original_data_service = get_current_value_service();

});


//添加对应客户
function _add_client()
{
	var phone = $("#cle_phone_service").val();
	if(  exist_star(phone)  )
	{
		phone = "";
	}

	$('#client_window').window({
		title: '选择客户(我的客户)',
		href:"index.php?c=client&m=select_clients&phone="+phone+"&type=service",
		width:582,
		top:50,
		shadow:true,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false
	});
}

/*    状态选择
* 仅是“未处理”和“处理中”才可以选择转交人
*/
function click_service_state()
{
	var serv_state = $("input:radio[name='serv_state']:checked").val();
	if( serv_state == "未处理" || serv_state == "处理中" )
	{
		$("#deal_user_id").combobox('enable');
	}
	else
	{
		$("#deal_user_id").combobox('setValue', '<?php echo $this->_tpl_vars['user_session_id']; ?>
');
		$("#deal_user_id").combobox('disable');
	}
}

/*
*  保存客服信息
*/
function save_service_info()
{
	var cle_id = $("#cle_id_service").val();
	if(cle_id==0 )
	{
		_show_msg_service("服务对应客户不能为空，请选择客户信息！",'error');
		//查找对应客户
		_add_client();
		return false;
	}
	if($("#deal_user_id").combobox("getValue").length==0)
	{
		_show_msg_service('转交人不能为空','error');
		return false;
	}

	//自定义必选字段判断
	var if_continue = true;
	var must_msg = '';
	$("[service_if_require='true']:input").each(function(){
		if($(this).attr('_date')=='date_box' || $(this).attr('_date')=='textarea_box')
		{
			if($(this).val()=='')
			{
				if_continue = false;
				must_msg += '['+$(this).attr('_chinese_name')+"]";
			}
		}
		else if(!$(this).validatebox('isValid'))
		{
			must_msg += '['+$(this).attr('_chinese_name')+"]";
			if_continue = false;
		}
	});
	if(	if_continue == false)
	{
		if(must_msg.length!=0)
		{
			_show_msg_service(must_msg+" 不能为空",'error');
		}
		return false;
	}

	// 客服服务 数据
	var _service_data = {};

	//得到文本框中当前的值
	var current_data = get_current_value_service();

	if(form_act_service == 'update_service')
	{
		//编辑已存在的 客户服务 信息
		//数据比较，得到修改过的数据
		_service_data = data_comparison(original_data_service,current_data);
		_service_data.serv_id = $("#serv_id").val();
		if( !_service_data.serv_id )
		{
			$.messager.alert("提示","<br>缺少参数！","info");
			return false;
		}

		if(current_data.cle_id)
		{
			_service_data.current_cle_id     = current_data.cle_id;
		}
		if(power_use_contact!=1 && ( _service_data.con_name != 'undefined' || _service_data.con_mobile != 'undefined' ))
		{
			_service_data.current_con_name   = current_data.con_name;
			_service_data.current_con_mobile = current_data.con_mobile;
		}
	}
	else if(form_act_service == 'insert_service')
	{
		//添加新 客服服务 信息
		_service_data = current_data;
	}

	//自动保存联系人  1是  2否
	_service_data.save_new_contact = 0;
	if(power_use_contact!=1)
	{

		if( $("#save_new_contact").attr("checked") == "checked" )
		{
			_service_data.save_new_contact = 1;
		}
	}

	$('#save_service').attr('disabled',true);
	$.ajax({
		type:'POST',
		url: "index.php?c=service&m="+form_act_service,
		data:_service_data,
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				_show_msg_service("保存成功",'yes');
				if(responce['content']==1)
				{
					// 客服服务  列表
					setTimeout(function(){
					window.parent.addTab('客服管理',"index.php?c=service","menu_icon_service");
					},1000);
				}
			}
			else
			{
				$.messager.alert('错误',"<br>"+responce['message'],'error');
			}

			$('#save_service').attr('disabled',false);
		}
	});

}


//得到文本框中当前的值
function get_current_value_service()
{
	var _data = {};
	_data.serv_type  = $("input:radio[name='serv_type']:checked").val();//服务类型
	_data.serv_state = $("input:radio[name='serv_state']:checked").val();//状态

	_data.cle_id     = $("#cle_id_service").val();
	_data.cle_name   = $("#cle_name_service").val();//客户名称
	_data.cle_phone  = $("#cle_phone_service").val();//客户电话
	if(!power_phone_view)
	{
		if(_data.cle_phone && exist_star(_data.cle_phone) )
		{
			var real_cle_phone = $("#real_cle_phone_service").val();
			if( real_cle_phone )
			{
				_data.cle_phone = real_cle_phone;
			}
			else if( global_cle_phone != "" )
			{
				_data.cle_phone = global_cle_phone;
			}
			else
			{
				_data.cle_phone = "";
			}
		}
	}
	if(power_use_contact!=1)
	{
		_data.con_name   = $("#con_name_service").combobox("getText");//联系人姓名
		_data.con_mobile = $("#con_mobile_service").val();//联系人电话   real_con_mobile
		if(!power_phone_view)
		{
			if( _data.con_mobile &&  exist_star(_data.con_mobile) )
			{
				var real_con_mobile = $("#real_con_mobile_service").val();
				if( real_con_mobile )
				{
					_data.con_mobile = real_con_mobile;
				}
				else if( global_con_mobile != "" )
				{
					_data.con_mobile = global_con_mobile;
				}
				else
				{
					_data.con_mobile = "";
				}
			}
		}
	}

	_data.user_id = $("#deal_user_id").combobox("getValue");// 转交人/处理人

	//自定义字段
	$("[service_confirm_field='true']:input").each(function(){
		if($(this).attr('type')=='checkbox')
		{
			var field1 = $(this).attr('checkbox_name')+'_1';
			var field2 = $(this).attr('checkbox_name')+'_2';
			var pid = {};
			_data[field1] = "";
			_data[field2] = "";
			$("input:checkbox[name='"+$(this).attr('name')+"']").each(function(i){
				if($(this).attr("checked")){
					_data[field2] += $(this).val()+",";
					pid[$(this).attr('checkbox_pid')] = $(this).attr('checkbox_pid');
				}
			});
			$.each(pid, function(index, value) {
				_data[field1] += value+",";
			});
		}
		else
		{
			_data[$(this).attr('name')] = $(this).val();
		}
	});

	//可配置的基本字段 serv_content serv_remark
	$("[base_field='true']:input").each(function(){
		_data[$(this).attr('id')] = $(this).val();
	});
	return _data;
}


//记录checkbox的选项
function record_memrot_cookie()
{
	//保存新联系人
	if( $("#save_new_contact").attr("checked") == "checked")
	{
		set_cookie("est_service_contact",1,2);
	}
	else
	{
		del_cookie("est_service_contact");
	}
}

/*客户详情*/
function _client_detail()
{
	var cle_id = $("#cle_id_service").val();
	if( cle_id.length!=0 && cle_id>0 )
	{
		window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&cle_id='+cle_id,'menu_icon');
	}
}

//显示消息
function _show_msg_service(msg)
{
	var type='';
	if(arguments[1])
	{
		type = arguments[1];
	}
	if(type=='yes')
	{
		msg = "<img src='./themes/default/icons/ok.png' />&nbsp;"+msg;
	}
	else if(type=='error')
	{
		msg = "<img src='./themes/default/icons/no.png' />&nbsp;"+msg;
	}
	$("#_service_msg").html(msg);
	setTimeout(function(){
		$("#_service_msg").html("");
	},3000);
}


//级联 - 获取联系人下一级
function get_comfirm_jl_options_service(deep,field_name,series,field_id)
{
	var jl_field_service = jl_field_type_service[field_id];
	var p_id = $('select[name="'+field_name+'_'+deep+'"]').val();
	var next_field = field_name+'_'+(parseInt(deep)+1);
	$('#'+next_field).html('');
	if(series==3 && deep==1)
	{
		$('#'+field_name+'_'+(parseInt(deep)+2)).html('');
	}
	
	if((series==2)||(series==3 && deep==2))
	{
		if(jl_field_service[p_id]!=undefined && jl_field_service[p_id]==1)
		{
			$('#'+next_field).html("<input type='text' name='"+next_field+"' value='' service_confirm_field='true'/>");
		}
		else
		{
			if( p_id > 0 )
			{
                get_jl_options_service(p_id,field_name,(parseInt(deep)+1),field_id,series);
			}
		}
	}
	else
	{
        get_jl_options_service(p_id,field_name,(parseInt(deep)+1),field_id,series);
	}
}

/*获取级联选项信息*/
function get_jl_options_service(p_id,field_name,deep,field_id,series)
{
   var next_field = field_name+'_'+deep;
   $.ajax({
             type:'POST',
             url: "index.php?c=field_confirm&m=get_jl_options",
             data:{"parent_id":p_id,"type":deep,"field_id":field_id},
             dataType:"json",
             success: function(responce){
                 if(responce['error']=='0')
                 {
                    var str = '';
                    $.each(responce['content'],function(id,value){
                          str +=  '<option value="'+id+'">'+value+'</option>';
                    });
                    if(str.length!=0)
                    {
                        if(series==3&&deep==2)
                        {
                            $('#'+next_field).html("<select name='"+next_field+"' service_confirm_field='true' onchange=\"get_comfirm_jl_options_service(2,'"+field_name+"',"+series+",'"+field_id+"')\" >\
		  				    <option value=''>--请选择--</option>"+str+"\
	     				    </select>");
                        }
                        else
                        {
                            $('#'+next_field).html("<select name='"+next_field+"' service_confirm_field='true' >\
		  				    <option value=''>--请选择--</option>"+str+"\
	     				    </select>");
                        }
                    }
                 }
                 else
                 {
                     $.messager.alert('错误',"<br>".responce['message'],'error');
                 }
             }
         });
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>