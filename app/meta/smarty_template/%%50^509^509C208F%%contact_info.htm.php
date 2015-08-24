<?php /* Smarty version 2.6.19, created on 2015-08-07 10:42:39
         compiled from contact_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'contact_info.htm', 33, false),array('modifier', 'cat', 'contact_info.htm', 60, false),array('modifier', 'default', 'contact_info.htm', 243, false),)), $this); ?>
<div class="main-div" style="padding-top:20px;">
<input type="hidden" id="add_cle_id" name="add_cle_id" value="<?php echo $this->_tpl_vars['cle_id']; ?>
"/>
<input type="hidden" id="add_con_id" name="add_con_id" value="<?php echo $this->_tpl_vars['con_id']; ?>
"/>
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:95%;" align="center"   >
   <tbody>
      <tr>
        <td class="micro-label"><label for="con_name">联系人姓名：<label></td>
        <td><input type="text"  name="con_name" id="con_name"  value="<?php echo $this->_tpl_vars['contact_info']['con_name']; ?>
" class="span2"></td>
        <td class="micro-label"><label for="con_mobile">联系人电话：<label></td>
        <td>
        	<div class="input-append">
              <input type="text" id="con_mobile"  name="con_mobile"  onkeyup='number_verification()' value="<?php echo $this->_tpl_vars['contact_info']['con_mobile']; ?>
" class="span2" />
              <button type="button" class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['contact_info']['con_mobile']; ?>
','con_mobile')" title="呼叫号码">
                  <span class="glyphicon glyphicon-phone"></span>
              </button>
              <?php if ($this->_tpl_vars['power_sendsms']): ?>
              <button type="button" class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['contact_info']['con_mobile']; ?>
','con_mobile')" title="发送短信">
                   <span class="glyphicon glyphicon-envelope"></span>
              </button>
              <?php endif; ?>
         	</div>
         	<span id='_phone_con_msg' style='color:red;'></span>
        </td>   
    </tr>
    <?php if ($this->_tpl_vars['contact_base']['con_mail']): ?>
    <tr>
      <td class="micro-label"><label for="con_mail">邮箱：</label></td>
      <td><input type="text" id="con_mail"  name="con_mail"  value="<?php echo $this->_tpl_vars['contact_info']['con_mail']; ?>
" class="span2" /></td>
    </tr>
    <?php endif; ?>
    
     <!--自定义字段  -----begin-------->
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact'), $this);?>
<!--  计算  -->
    <?php $_from = $this->_tpl_vars['contact_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
    <?php $this->assign('parent_id', $this->_tpl_vars['confirm_info']['id']); ?>
    <?php $this->assign('real_field', $this->_tpl_vars['confirm_info']['fields']); ?> 
    
    <?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact'), $this);?>
<!-- 从新计算 -->
      <tr>
       <td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
">
           <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：
       </td>
       <td colspan="5"><textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" style="width:90%" contact_confirm='true' rows="4" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" contact_if_require='true'<?php endif; ?> ><?php echo $this->_tpl_vars['contact_info'][$this->_tpl_vars['real_field']]; ?>
</textarea>
       </td>
	  </tr>
	  
<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="micro-label">
        <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
">
            <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：
        </label>
    </td>
    <td colspan="11">
    	<span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1">
	 	 <select name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_1' style="width:110px;" contact_confirm='true' onchange="get_comfirm_jl_options_contact(1,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
_1'<?php endif; ?> >
		 <option value="" >--请选择--</option>
		  <?php $this->assign('field_type', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_1') : smarty_modifier_cat($_tmp, '_1'))); ?>
		  <?php $_from = $this->_tpl_vars['jl_options_contact'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
          <option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
	     </select>
	    </span>
	    <?php $this->assign('jl_f_t', $this->_tpl_vars['confirm_info']['jl_field_type']); ?>
	    <?php $this->assign('field_type2', ((is_array($_tmp=$this->_tpl_vars['confirm_info']['fields'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_2') : smarty_modifier_cat($_tmp, '_2'))); ?>
	    <span id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2">
	    <?php $this->assign('p_id', $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type']]); ?>
	    	<?php if ($this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type']]): ?>
	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2' value='<?php echo $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type2']]; ?>
' contact_confirm='true' style="width:110px;" />
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2" style="width:110px;" contact_confirm='true' <?php if ($this->_tpl_vars['confirm_info']['jl_series'] == 3): ?>onchange="get_comfirm_jl_options_contact(2,'<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
','<?php echo $this->_tpl_vars['confirm_info']['jl_series']; ?>
',<?php echo $this->_tpl_vars['confirm_info']['id']; ?>
)"<?php endif; ?> >
        				<option value="">--请选择--</option>
       	 				<?php $_from = $this->_tpl_vars['jl_options_contact'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
        				<option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type2']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
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
	    	<?php if ($this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type2']]): ?>
	    	<?php $this->assign('p_id2', $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type2']]); ?>

	    		<?php if ($this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] && $this->_tpl_vars['jl_f_t'][$this->_tpl_vars['p_id2']] == 1): ?>
	    			<input type='text' name='<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3' value='<?php echo $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type3']]; ?>
' contact_confirm='true' style="width:110px;" />
	    		<?php else: ?>
	    			<select name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_3" contact_confirm='true' style="width:110px;">
                        <option value="">--请选择--</option>
                        <?php $_from = $this->_tpl_vars['jl_options_contact'][$this->_tpl_vars['field_type3']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jlkey'] => $this->_tpl_vars['option']):
?>
                        <option value="<?php echo $this->_tpl_vars['jlkey']; ?>
" <?php if ($this->_tpl_vars['jlkey'] == $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type3']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['option']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
	    		<?php endif; ?>
	     	<?php endif; ?>
	     <?php endif; ?>
    </td>
</tr>


<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 7): ?><!--级联多选框-->
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'contact'), $this);?>
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
			<?php if ($this->_tpl_vars['contact_info']): ?>
				<?php $this->assign('field_value', $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type']]); ?>
				<?php $this->assign('field_value2', $this->_tpl_vars['contact_info'][$this->_tpl_vars['field_type2']]); ?>
			<?php endif; ?>
			<?php $_from = $this->_tpl_vars['checkbox_options_contact'][$this->_tpl_vars['field_type']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id1'] => $this->_tpl_vars['check_option1']):
?>
     		<tr>
                <th style="padding-bottom:5px;"><?php echo $this->_tpl_vars['check_option1']['name']; ?>
</th>
     		</tr>
     		<tr>
     			<td style="padding-bottom:5px;">
      				<?php $_from = $this->_tpl_vars['checkbox_options_contact'][$this->_tpl_vars['field_type2']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id2'] => $this->_tpl_vars['check_option']):
?>
      					<?php if ($this->_tpl_vars['check_option']['p_id'] == $this->_tpl_vars['id1']): ?>
         					<input type="checkbox" checkbox_name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" checkbox_pid="<?php echo $this->_tpl_vars['id1']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
_2[]" value="<?php echo $this->_tpl_vars['id2']; ?>
" contact_confirm='true' <?php if ($this->_tpl_vars['field_value2'][$this->_tpl_vars['id2']] == $this->_tpl_vars['id2']): ?>checked<?php endif; ?> /> <?php echo $this->_tpl_vars['check_option']['name']; ?>
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
       <?php if ($this->_tpl_vars['contact']%2 == 0): ?>
       <tr>
       <?php endif; ?>
       <td class="micro-label"  >
           <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  >
               <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：
           </label>
       </td>
       <td>
         <?php if ($this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']] || $this->_tpl_vars['confirm_info']['data_type'] == 2): ?>
          <select id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" style="width:140px;" contact_confirm='true' <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>contact_if_require='true'<?php endif; ?> 
           <?php $_from = $this->_tpl_vars['field_detail'][$this->_tpl_vars['parent_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['detail']):
?>
           <option value="<?php echo $this->_tpl_vars['detail']; ?>
" <?php if ($this->_tpl_vars['detail'] == $this->_tpl_vars['contact_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['detail']; ?>
</option>
           <?php endforeach; endif; unset($_from); ?>
           <option value="" <?php if (! $this->_tpl_vars['contact_info'][$this->_tpl_vars['real_field']]): ?>selected<?php endif; ?> >&nbsp;</option>
          </select>

        <?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 5): ?><!-- 日期框 -->
         <div class="input-append">
        	<input type="text" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php if ($this->_tpl_vars['form_act'] == 'update_contact'): ?><?php echo $this->_tpl_vars['contact_info'][$this->_tpl_vars['confirm_info']['fields']]; ?>
<?php elseif ($this->_tpl_vars['confirm_info']['default'] == '系统时间'): ?><?php if ($this->_tpl_vars['confirm_info']['datefmt'] == 'yyyy-MM-dd'): ?><?php echo $this->_tpl_vars['now_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['now_time']; ?>
<?php endif; ?><?php else: ?><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
<?php endif; ?>" contact_confirm='true' class="span2"  <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>_date='date_box' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
' if_require='true'<?php endif; ?> readonly>
        	<button type="button" role="date" class="btn" onclick="WdatePicker({el: '<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
',dateFmt:'<?php echo $this->_tpl_vars['confirm_info']['datefmt']; ?>
'})">
           		<span class="glyphicon glyphicon-calendar"></span>
        	</button>
    	</div>
        <?php else: ?>
           <input type="text" id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" contact_confirm='true' value="<?php echo $this->_tpl_vars['contact_info'][$this->_tpl_vars['real_field']]; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" contact_if_require='true'<?php endif; ?> />
        <?php endif; ?>
          </td> 
       <?php if ($this->_tpl_vars['contact']%2 != 0): ?>
        </tr>
       <?php endif; ?>
       <?php echo smarty_function_counter(array('print' => false,'assign' => 'contact'), $this);?>
<!--   加1   -->
       <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <!--自定义字段  ------end--------->
   <?php if ($this->_tpl_vars['contact_base']['con_remark']): ?>
   <tr>
     <td class="micro-label"><label for="con_remark">备注：</td>
     <td colspan="5"><textarea id="con_remark"  name="con_remark" style="width:100%" rows="4" cols="20"><?php echo $this->_tpl_vars['contact_info']['con_remark']; ?>
</textarea></td>
   </tr>
   <?php endif; ?>
   </tbody>
</table>
</div>
<div style="text-align:right;padding-right:20px;padding-bottom:10px">
<span style='color:red' id='_contact_msg'></span>
<a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="save_contact()" id="save_contact">保存</a>
<!--<a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)" onclick="contact_cancel()">取消</a>-->
</div>		
<script language="JavaScript" type="text/javascript">
var jl_field_type_contact = eval("(" + '<?php echo $this->_tpl_vars['jl_field_type_contact']; ?>
' + ")");
var contact_original_data = {};//原始数据
$(document).ready(function(){
	<?php if (! $this->_tpl_vars['power_phone_view']): ?>
	$('#con_mobile').val(hidden_part_number($('#con_mobile').val()));
	<?php endif; ?>

	//记录原始数据
    contact_original_data = get_current_value_contact();
});
function contact_cancel()
{
	$('#new_contact_panel').window('close');
}

//保存联系人信息
function save_contact()
{
	//$('#save_contact').linkbutton({'disabled':true});
	//自定义必选字段判断
	var contact_if_continue = true;
	$("[contact_if_require='true']:input").each(function(){
		if($(this).val()==0 || $(this).val()=='')
		{
			contact_if_continue = false;
		}
	});
	if(contact_if_continue==false)
	{
		$('#_contact_msg').html('提示：保存失败，必选项不能为空！');
		setTimeout(function(){
		$("#_contact_msg").html("");
		},2000);
		return false;
	}

	//得到当前数据
	var current_data = get_current_value_contact();
	if( empty_obj(current_data) )
	{
		$.messager.alert('提示',"<br>联系人信息为空，请填写数据！",'info');
		return;
	}

	//数据比较，得到修改过的数据
    <?php if ($this->_tpl_vars['form_act'] == 'insert_contact'): ?>
    var changed_data = current_data;
    <?php else: ?>
	var changed_data = data_comparison(contact_original_data,current_data);
    <?php endif; ?>
	changed_data.con_id = $("#add_con_id").val();
	changed_data.cle_id = $("#add_cle_id").val();
	$.ajax({
		type:'POST',
		url: "index.php?c=contact&m=<?php echo ((is_array($_tmp=@$this->_tpl_vars['form_act'])) ? $this->_run_mod_handler('default', true, $_tmp, 'insert_contact') : smarty_modifier_default($_tmp, 'insert_contact')); ?>
",
		data: changed_data,
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$('#contact_list').datagrid('reload');
				$('#new_contact_panel').window('close');
			}
			else
			{
				$.messager.alert('错误',"<br>"+responce["message"],'error');
			}
			$('#save_contact').linkbutton({'disabled':false});
		}
	});
}

//得到文本框中当前的值
function get_current_value_contact()
{
	var contact_data = {};

	//联系人基本信息
	contact_data.con_name          = $("#con_name").val();//联系人姓名
	contact_data.con_mobile        = $("#con_mobile").val();//联系人电话
	contact_data.con_mail          = $("#con_mail").val();//邮箱
	contact_data.con_remark        = $("#con_remark").val();//备注
	<?php if (! $this->_tpl_vars['power_phone_view']): ?>
	if(contact_data.con_mobile && exist_star(contact_data.con_mobile) )
	{
		contact_data.con_mobile = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['contact_info']['con_mobile'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
	}
	<?php endif; ?>
	//自定义字段
	$("[contact_confirm='true']:input").each(function(){
		if($(this).attr('type')=='checkbox')
		{
			var field1 = $(this).attr('checkbox_name')+'_1';
            var field2 = $(this).attr('checkbox_name')+'_2';
            var pid = {};
            contact_data[field1] = "";
            contact_data[field2] = "";
			 $("input:checkbox[name='"+$(this).attr('name')+"']").each(function(i){ 
                if($(this).attr("checked")){
                    contact_data[field2] += $(this).val()+",";
                    pid[$(this).attr('checkbox_pid')] = $(this).attr('checkbox_pid');
                }
            });
            $.each(pid, function(index, value) {
            	 contact_data[field1] += value+",";
            });
		}
		else
		{
			contact_data[$(this).attr('name')] = $(this).val();
		}
	});
	return contact_data;
}

//号码验证
function number_verification()
{
	var con_mobile = $('#con_mobile').val();
	// ^[0-9]*[1-9][0-9]*$  正整数  _phone_update_msg
	r = /^[0-9]*[1-9][0-9]*$/;
	if(con_mobile.length!=0)
	{
		if(r.test(con_mobile))
		{
			var msg = '';
		}
		else
		{
			var msg = "<img src='./image/important.gif' border='0' height='16' width='16' align='absmiddle' />&nbsp;<b>号码必须由数字组成</b>";
		}

		$('#_phone_con_msg').html(msg);

	}
}

//级联 - 获取联系人下一级
function get_comfirm_jl_options_contact(deep,field_name,series,field_id)
{
	var jl_field_contact = jl_field_type_contact[field_id];
	var p_id = $('select[name="'+field_name+'_'+deep+'"]').val();
	var next_field = field_name+'_'+(parseInt(deep)+1);
	$('#'+next_field).html('');
	if(series==3 && deep==1)
	{
		$('#'+field_name+'_'+(parseInt(deep)+2)).html('');
	}
	
	if((series==2)||(series==3 && deep==2))
	{
		if(jl_field_contact[p_id]!=undefined && jl_field_contact[p_id]==1)
		{
			$('#'+next_field).html("<input type='text' name='"+next_field+"' style='width:110px;' value='' contact_confirm='true'/>");
		}
		else
		{
			if( p_id > 0 )
			{
                get_jl_options_contact(p_id,field_name,(parseInt(deep)+1),field_id,series);
			}
		}
	}
	else
	{
        get_jl_options_contact(p_id,field_name,(parseInt(deep)+1),field_id,series);
	}
}

    /*获取级联选项信息*/
    function get_jl_options_contact(p_id,field_name,deep,field_id,series)
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
                            $('#'+next_field).html("<select name='"+next_field+"' style='width:110px;' contact_confirm='true' onchange=\"get_comfirm_jl_options_contact(2,'"+field_name+"',"+series+",'"+field_id+"')\" >\
		  				<option value=''>--请选择--</option>"+str+"\
	     				</select>");
                        }
                        else
                        {
                            $('#'+next_field).html("<select name='"+next_field+"' style='width:110px;' contact_confirm='true' >\
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