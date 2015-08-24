<?php /* Smarty version 2.6.19, created on 2015-07-21 09:35:56
         compiled from client_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_info.htm', 17, false),array('modifier', 'cat', 'client_info.htm', 124, false),array('function', 'counter', 'client_info.htm', 103, false),array('function', 'html_options', 'client_info.htm', 204, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="tab_client">
    <div class="main-div" title="客户基本信息<?php if ($this->_tpl_vars['cle_phone']): ?>【没有搜索到相关信息 - 添加客户】<?php endif; ?>">
        <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:100%;">
            <tbody>
            <tr>
                <td class="micro-label" ><label for="cle_name">客户名称：</label>
                </td>
                <td colspan="3" >
                    <input type="text" require="true"  name="cle_name" id="cle_name"  value="" class="span8" onkeyup="check_repeat_data();" />
                </td>
            </tr>
            <tr>
                <td class="micro-label"><label for="cle_phone">客户电话：</label></td>
                <td>
                    <div class="input-append">
                        <input type="text" id="cle_phone"  name="cle_phone"  value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
" onkeyup="number_verification('cle_phone','_phone_update_msg')" />
                        <button class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['cle_phone']; ?>
','cle_phone')" title="呼叫号码">
                            <span class="glyphicon glyphicon-phone"></span>
                        </button>
                        <button class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['cle_phone']; ?>
','cle_phone')" title="发送短信">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </button>
                    </div>
                    <span id='_phone_update_msg' style='color:red;'></span>
                </td>
                <?php if ($this->_tpl_vars['client_base']['cle_info_source']): ?>
                <td class="micro-label"><label for="cle_info_source">信息来源：</label></td>
                <td>
                    <select name="cle_info_source" id="cle_info_source" base_field='true'>
                        <?php $_from = $this->_tpl_vars['cle_info_source']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['info_source']):
?>
                        <option value="<?php echo $this->_tpl_vars['info_source']['name']; ?>
" ><?php echo $this->_tpl_vars['info_source']['name']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                </td>
                </tr>
             <tr>
                <?php endif; ?>
                
	   <?php if ($this->_tpl_vars['client_base']['cle_phone2']): ?>
                 <td class="micro-label">
                     <label for="cle_phone2">办公电话：</label>
                 </td>
                 <td>
                     <div class="input-append">
                         <input type="text" id="cle_phone2"  name="cle_phone2"  value="" onkeyup="number_verification('cle_phone2','_phone_update_msg2')"  base_field='true' />
                         <button class="btn" onclick="sys_dial_num('','cle_phone2')" title="呼叫号码">
                             <span class="glyphicon glyphicon-phone"></span>
                         </button>
                         <button class="btn" onclick="sys_send_sms('','cle_phone2')" title="发送短信">
                             <span class="glyphicon glyphicon-envelope"></span>
                         </button>
                     </div>
                     <span id='_phone_update_msg2' style='color:red;'></span>
                </td>
         	<?php if (! $this->_tpl_vars['client_base']['cle_info_source']): ?>
			</tr><tr>
			<?php endif; ?>
		<?php endif; ?>
		
	   	<?php if ($this->_tpl_vars['client_base']['cle_phone3']): ?>
                <td class="micro-label">
                    <label for="cle_phone3">其他电话：</label>
                </td>
                <td>
                    <div class="input-append">
                        <input type="text" id="cle_phone3"  name="cle_phone3"  value="" onkeyup="number_verification('cle_phone3','_phone_update_msg3')" base_field='true' />
                        <button class="btn" onclick="sys_dial_num('','cle_phone3')" title="呼叫号码">
                            <span class="glyphicon glyphicon-phone"></span>
                        </button>
                        <button class="btn" onclick="sys_send_sms('','cle_phone3')" title="发送短信">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </button>
                    </div>
                    <span id='_phone_update_msg3' style='color:red;'></span>
                </td>
		<?php endif; ?>	
	 </tr>
       <?php if ($this->_tpl_vars['client_base']['cle_stat']): ?>
       <tr>
         <td class="micro-label"><label for="cle_stat">号码状态：<label></td>
         <td colspan="3">
          <?php $_from = $this->_tpl_vars['client_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['state']):
?>
          <input type="radio"  name="cle_stat" value="<?php echo $this->_tpl_vars['state']['name']; ?>
" base_field='true' <?php if ($this->_tpl_vars['state']['name'] == '未拨打'): ?> CHECKED <?php endif; ?> /> <span><?php echo $this->_tpl_vars['state']['name']; ?>
</span>
          <?php endforeach; endif; unset($_from); ?>
       	 </td>
      </tr>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['client_base']['cle_stage']): ?>
      <tr>
           <td class="micro-label"><label for="cle_stage">客户阶段：<label></td>
                <td colspan="3">
                   <input id="cle_stage" name="cle_stage" value="" type="hidden" base_field='true' />
                   <div id="mainNav">
                       <?php $_from = $this->_tpl_vars['cle_stage']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['listName'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['listName']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stage']):
        $this->_foreach['listName']['iteration']++;
?>
                       <span id="stage_step_<?php echo $this->_tpl_vars['skey']; ?>
" onclick="click_stage_step('<?php echo $this->_tpl_vars['skey']; ?>
','<?php echo $this->_foreach['listName']['total']; ?>
')" title="<?php echo $this->_tpl_vars['stage']['name']; ?>
" cvalue="span_step" <?php if (($this->_foreach['listName']['iteration'] == $this->_foreach['listName']['total'])): ?>class="mainNavNoBg"<?php endif; ?>  ><a title="<?php echo $this->_tpl_vars['stage']['name']; ?>
"><?php echo $this->_tpl_vars['stage']['name']; ?>
</a></span>
                       <?php endforeach; endif; unset($_from); ?>
                   </div>
              </td>
        </tr>
        <?php endif; ?>
    <!--配置字段  -----begin-------->
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'client_confirm_count'), $this);?>
<!--  计算  -->
    <?php $_from = $this->_tpl_vars['client_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
    
    <?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'client_confirm_count'), $this);?>
<!-- 从新计算 -->
    <tr>
	 	<td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td >
	 	<td colspan="11">
	 		<textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true'  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" class="span12" rows="3" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date="textarea_box" class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> ><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
</textarea>
	 		<?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	 	</td>
	  </tr>
	  
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'client_confirm_count'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="span2" style="text-align: right"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td>
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
" ><?php echo $this->_tpl_vars['option']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
	     </select>
	    </span>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2"></span>
	    <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3"></span>
	    <?php endif; ?>
	    <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
    </td>
</tr>
	
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 7): ?><!--级联多选框-->
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'client_confirm_count'), $this);?>
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
" confirm_field='true' /> <?php echo $this->_tpl_vars['check_option']['name']; ?>
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
     <?php if ($this->_tpl_vars['client_confirm_count']%2 == 0): ?>
     <tr>
     <?php endif; ?>
        <td class="micro-label"  >  <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  ><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td>
        <td>
            <?php $this->assign('parent_id', $this->_tpl_vars['confirm_info']['id']); ?>
            <?php if ($this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']] || $this->_tpl_vars['confirm_info']['data_type'] == 2): ?>
            	<select id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true' <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> >
               		<?php $_from = $this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
               		<option value="<?php echo $this->_tpl_vars['detail']; ?>
"><?php echo $this->_tpl_vars['detail']; ?>
</option>
               		<?php endforeach; endif; unset($_from); ?>
               		<option value="" selected></option>
             	</select> <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
            <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 5): ?><!-- 日期框 -->
            	<div class="input-append">
        			<input type="text" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php if ($this->_tpl_vars['confirm_info']['default'] == '系统时间'): ?><?php if ($this->_tpl_vars['confirm_info']['datefmt'] == 'yyyy-MM-dd'): ?><?php echo $this->_tpl_vars['now_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['now_time']; ?>
<?php endif; ?><?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" confirm_field='true'  <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date='date_box' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?> readonly>
        			<button type="button" role="date" class="btn" onclick="WdatePicker({el: '<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
',dateFmt:'<?php echo $this->_tpl_vars['confirm_info']['datefmt']; ?>
'})">
           				<span class="glyphicon glyphicon-calendar"></span>
        			</button>
    			</div><?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
            <?php else: ?>
               <input type="text" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true' value="<?php echo $this->_tpl_vars['confirm_info']['default']; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true"  missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> />
               <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
              <?php endif; ?>
            </td>
            <?php if ($this->_tpl_vars['client_confirm_count']%2 != 0): ?>
            </tr>
            <?php endif; ?>
           	 <?php echo smarty_function_counter(array('print' => false,'assign' => 'client_confirm_count'), $this);?>
<!--   加1   -->
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
             <?php if ($this->_tpl_vars['client_base']['cle_province_name'] || $this->_tpl_vars['client_base']['cle_address']): ?>
            <tr>
                <td class="micro-label"><label for="">详细地址：</td>
                <td colspan="5">
                <?php if ($this->_tpl_vars['client_base']['cle_province_name']): ?>
                <select id="cle_province_id" name='cle_province_id' style="width:110px;" base_field='true' onchange="change_regions_type('cle_province_id','cle_city_id','2');"><option value="0" >--选择省--</option>
					<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['regions_info']), $this);?>

				</select>
				<select id="cle_city_id" name="cle_city_id" style="width:110px;" base_field='true'>
				<option value="0">--选择市--</option>
				</select>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['client_base']['cle_address']): ?>
                <input style="width: 50%;" type="text" name="cle_address" id="cle_address" base_field='true' value="">
                <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
             <?php if ($this->_tpl_vars['client_base']['cle_remark']): ?>
            <tr>
                <td class="micro-label"><label for="cle_remark">客户备注：</td >
                <td colspan="11"><textarea id="cle_remark"  name="cle_remark"  base_field='true' rows="3" cols="20" class="span12" ></textarea></td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
<div id='tab_contact'>
    <div class="main-div" title="联系人基本信息">
        <table borderColor="#ffffff" cellSpacing="0" cellPadding="0"  style="width:100%;" align="center"  id="contact_list_table" >
            <tbody>
            <tr>
               <td class="micro-label" style=""><label for="con_name">联系人姓名：<label></td>
               <td><input type="text"  name="con_name" id="con_name"  value=""></td>
               <td class="micro-label"><label for="con_mobile">联系人电话：<label></td>
               <td>
               		<div class="input-append">
                        <input type="text" id="con_mobile"  name="con_mobile"  value="" onkeyup="number_verification('con_mobile','_contact_phone_msg')" />
                        <button class="btn" onclick="sys_dial_num('','con_mobile')" title="呼叫号码">
                            <span class="glyphicon glyphicon-phone"></span>
                        </button>
                        <button class="btn" onclick="sys_send_sms('','con_mobile')" title="发送短信">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </button>
                    </div>
                    <span id='_contact_phone_msg' style='color:red;'></span>
               </td>
            </tr>
            <?php if ($this->_tpl_vars['contact_base']['con_mail']): ?>
            <tr>
                <td class="micro-label"><label for="con_mail">邮箱：</label></td>
                <td ><input type="text" id="con_mail"  name="con_mail"  value="" class="easyui-validatebox"  validType="email" base_field='true'  /></td>
            </tr>
            <?php endif; ?>
  		<!--配置字段  -----begin-------->
  		<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact_confirm_count'), $this);?>
<!-- 计算 -->
  		<?php $_from = $this->_tpl_vars['contact_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
  
  		<?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
  		<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact_confirm_count'), $this);?>
<!-- 从新计算 -->
      		<tr>
	 			<td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td >
	 			<td colspan="11">
	 				<textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true'  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" class="span12" rows="3" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date="textarea_box" class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> ><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
</textarea>
	 				<?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	 			</td>
			</tr>
	
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact_confirm_count'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="span2" style="text-align: right"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td>
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
" ><?php echo $this->_tpl_vars['option']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
	     </select>
	    </span>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2"></span>
	    <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>
	     <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3"></span>
	    <?php endif; ?>
	    <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
    </td>
</tr>
	
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 7): ?><!--级联多选框-->
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact_confirm_count'), $this);?>
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
" confirm_field='true' /> <?php echo $this->_tpl_vars['check_option']['name']; ?>
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
            <?php if ($this->_tpl_vars['contact_confirm_count']%2 == 0): ?>
            <tr>
            <?php endif; ?>
                <td class="micro-label"  >  <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  ><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：<label></td><td >
                <?php $this->assign('parent_id', $this->_tpl_vars['confirm_info']['id']); ?>
                <?php if ($this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']] || $this->_tpl_vars['confirm_info']['data_type'] == 2): ?>
                <select id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true' <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?>>
                    <?php $_from = $this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
                    <option value="<?php echo $this->_tpl_vars['detail']; ?>
"><?php echo $this->_tpl_vars['detail']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    <option value="" selected></option>
                </select> <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
                <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 5): ?><!-- 日期框 -->
                <div class="input-append">
        			<input type="text" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php if ($this->_tpl_vars['confirm_info']['default'] == '系统时间'): ?><?php if ($this->_tpl_vars['confirm_info']['datefmt'] == 'yyyy-MM-dd'): ?><?php echo $this->_tpl_vars['now_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['now_time']; ?>
<?php endif; ?><?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" confirm_field='true'  <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date='date_box' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?> readonly>
        			<button type="button" role="date" class="btn" onclick="WdatePicker({el: '<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
',dateFmt:'<?php echo $this->_tpl_vars['confirm_info']['datefmt']; ?>
'})">
           				<span class="glyphicon glyphicon-calendar"></span>
        			</button>
    			</div><?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
                <?php else: ?>
                <input type="text" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true' value="<?php echo $this->_tpl_vars['confirm_info']['default']; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?> />
                <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
                <?php endif; ?>
            </td>
            <?php if ($this->_tpl_vars['contact_confirm_count']%2 != 0): ?>
            </tr>
            <?php endif; ?>
            <?php echo smarty_function_counter(array('print' => false,'assign' => 'contact_confirm_count'), $this);?>
<!--   加1   -->
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
            <!--配置字段  ------end--------->
            <?php if ($this->_tpl_vars['contact_base']['con_remark']): ?>
            <tr>
                <td class="micro-label"><label for="con_remark">联系人备注：</td>
                <td colspan="5"><textarea id="con_remark"  name="con_remark" base_field='true' rows="4" cols="20" class="span12"></textarea></td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div style="text-align:right; padding: 5px;" class="form-div form-inline">
<span style='color:red;' id='_save_msg'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    保存之后：
    <input type="radio" name="end_type" id="end_type_0" value="0" onclick="record_end_type(0)" />
        <label for="end_type_0"  id="label_type_0">添加联系记录</label>
    <input type="radio" name="end_type" id="end_type_1" value="1" onclick="record_end_type(1)" />
        <label for="end_type_1"  id="label_type_1">继续添加客户</label>
    <input type="radio" name="end_type" id="end_type_2" value="2" onclick="record_end_type(2)" />
        <label for="end_type_2"  id="label_type_2">返回客户列表</label>
    <button type="button" class="btn btn-primary" id="save_client" onclick="save_client_info()">
        <span class="glyphicon glyphicon-saved"></span> 保存数据
    </button>
</div>

<div id="display_repeat_data"></div>
<div id='set_field_confirm'></div><!--  自定义字段设置   -->
<div id='set_dictionary'></div><!--  数据字典   -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script src="./jssrc/easyui-validtype.js" type="text/javascript"></script>
<script src="./jssrc/viewjs/client_info.js?1.3" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var global_cle_phone = '<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
';
var power_phone_view = <?php echo $this->_tpl_vars['power_phone_view']; ?>
;
var phone_ifrepeat = <?php echo $this->_tpl_vars['phone_ifrepeat']; ?>
;//是否允许系统电话重复（参数设置可设置）
var power_fieldsconfirm = <?php echo $this->_tpl_vars['power_fieldsconfirm']; ?>
;
var power_dictionary = <?php echo $this->_tpl_vars['power_dictionary']; ?>
;
var power_use_contact = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['power_use_contact'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
";
var jl_field_type = eval("(" + '<?php echo $this->_tpl_vars['jl_field_type']; ?>
' + ")");

</script>