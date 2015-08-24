<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:27
         compiled from order_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'order_add.htm', 107, false),array('modifier', 'cat', 'order_add.htm', 128, false),array('modifier', 'default', 'order_add.htm', 264, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!--订单基本信息-->
<div id='order_tools'>

<div class="main-div" title='订单基本信息'>
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" align="center" border="0" style="width:98%">
 <tbody>
	 <tr>
         <td class="narrow-label"><label for="order_num">订单编号：</label></td>		           
         <td ><input type='text' id='order_num' name='order_num' value='<?php echo $this->_tpl_vars['order_num']; ?>
' readonly /></td>
      </tr>
      <tr >
       <td class="narrow-label"><label for="order_remark">订单状态：</label></td>	
	   <td  colspan="3" align="right" style="padding:5px 35px 5px 0px">
	 	<input id="order_state" name="order_state" value="" type="hidden"/>
        <div id="mainNav">
 	       <?php $_from = $this->_tpl_vars['order_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['orderState'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['orderState']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['_state']):
        $this->_foreach['orderState']['iteration']++;
?>
            <span id="order_step_<?php echo $this->_tpl_vars['skey']; ?>
" onclick="click_order_step('<?php echo $this->_tpl_vars['skey']; ?>
','<?php echo $this->_foreach['orderState']['total']; ?>
')" title="<?php echo $this->_tpl_vars['_state']['name']; ?>
" cvalue="span_order_step" <?php if (($this->_foreach['orderState']['iteration'] == $this->_foreach['orderState']['total'])): ?>class="mainNavNoBg"<?php endif; ?>  ><a title="<?php echo $this->_tpl_vars['_state']['name']; ?>
"><?php echo $this->_tpl_vars['_state']['name']; ?>
</a></span>
           <?php endforeach; endif; unset($_from); ?>
 	    </div>
	   </td>
	  </tr>
      <tr>
      <?php if ($this->_tpl_vars['order_base']['order_delivery_mode']): ?>
        <td class="narrow-label"><label for="order_delivery_mode">配送方式：</label></td>	           
        <td>
         <select name="order_delivery_mode" id="order_delivery_mode" order_base_field='true'> 
          <option value="" SELECTED >&nbsp;</option>
          <?php $_from = $this->_tpl_vars['delivery_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info_delivery']):
?>
 	 	  <option value="<?php echo $this->_tpl_vars['info_delivery']['name']; ?>
" ><?php echo $this->_tpl_vars['info_delivery']['name']; ?>
</option>
 	 	 <?php endforeach; endif; unset($_from); ?>
 		 </select>
 		</td>
 		<?php endif; ?>
 		<?php if ($this->_tpl_vars['order_base']['order_delivery_number']): ?>
 		<td class="narrow-label"><label for="delivery_number">配送单号：</label></td>
 		<td>
 			<span title="只能填数字（可带小数点）" class="easyui-tooltip" data-options="position:'right'">
 				<input type='text' id='order_delivery_number' name='order_delivery_number' value="" order_base_field='true'/>
 			</span>
 		</td>
      </tr>
      <tr>
      <?php endif; ?>
         <td class="narrow-label"><label for="order_price">订单总价：</label></td>		           
         <td ><input type='text' id='order_price' name='order_price' value=''/></td>  
      </tr>
      <tr>
         <td class="narrow-label"><label for="cle_name">客户名称：</label></td>		           
         <td >
          <div class="input-append">
               <input type="text" id="cle_name"  name="cle_name"  value="<?php echo $this->_tpl_vars['client_info']['cle_name']; ?>
" readonly />
               <button type="button" class="btn" onclick="_add_client()" title="查找客户">
                    <span class="glyphicon glyphicon-zoom-in"></span>
               </button>
               <button type="button" class="btn" onclick="_client_detail()" title="客户详情">
                    <span class="glyphicon glyphicon-briefcase"></span>
               </button>
           </div>
           <input type="hidden"  id='cle_id' name='cle_id' value='<?php echo $this->_tpl_vars['client_info']['cle_id']; ?>
'  />
         </td>
         <td class="narrow-label"><label for="cle_phone">客户电话：</label></td>		           
         <td >
          	<div class="input-append">
               <input type="text" id="cle_phone"  name="cle_phone"  value="<?php echo $this->_tpl_vars['client_info']['cle_phone']; ?>
" />
               <button type="button" class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['client_info']['cle_phone']; ?>
','cle_phone')" title="呼叫号码">
                    <span class="glyphicon glyphicon-phone"></span>
               </button>
               <?php if ($this->_tpl_vars['power_sendsms']): ?>
               <button type="button" class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['client_info']['cle_phone']; ?>
','cle_phone')" title="发送短信">
                    <span class="glyphicon glyphicon-envelope"></span>
               </button>
               <?php endif; ?>
             </div>
             <input type='hidden' id='real_cle_phone' name='real_cle_phone' value='<?php echo $this->_tpl_vars['client_info']['cle_phone']; ?>
' />
         </td>  
       </tr>
     <?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
     <tr>
       	<td class="narrow-label"><label for="con_name">联系人姓名：</label></td>		           
        <td><input id="con_name" name="con_name" /></td>
        <td class="narrow-label"><label for="con_mobile">联系人电话：</label></td>		           
        <td > 
         	<div class="input-append">
               <input type="text" id="con_mobile"  name="con_mobile"  value="<?php echo $this->_tpl_vars['client_info']['con_mobile']; ?>
" />
               <button type="button" class="btn" onclick="sys_dial_num('<?php echo $this->_tpl_vars['client_info']['con_mobile']; ?>
','con_mobile')" title="呼叫号码">
                    <span class="glyphicon glyphicon-phone"></span>
               </button>
               <?php if ($this->_tpl_vars['power_sendsms']): ?>
               <button type="button" class="btn" onclick="sys_send_sms('<?php echo $this->_tpl_vars['client_info']['con_mobile']; ?>
','con_mobile')" title="发送短信">
                    <span class="glyphicon glyphicon-envelope"></span>
               </button>
               <?php endif; ?>
             </div>
              <input type="hidden" id="real_con_mobile"  name="real_con_mobile"  value="<?php echo $this->_tpl_vars['client_info']['con_mobile']; ?>
" />
         </td> 
      </tr>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['order_base']['cle_address']): ?> 
      <tr>
         <td class="narrow-label"><label for="cle_address">客户地址：</label></td>		           
         <td colspan='3'><input type='text' id='cle_address' name='cle_address' class='span6' value='<?php echo $this->_tpl_vars['client_info']['cle_address']; ?>
' order_base_field='true' /></td>
      </tr>
      <?php endif; ?>
      
       <!--自定义字段  -----begin-------->
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'order_count'), $this);?>
<!--  计算  -->
    <?php $_from = $this->_tpl_vars['order_confirm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['confirm_info']):
        $this->_foreach['list_Name']['iteration']++;
?>
    
    <?php if ($this->_tpl_vars['confirm_info']['data_type'] == 3): ?>
    <?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'order_count'), $this);?>
<!-- 从新计算 -->
      	<tr>
	 		<td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：</label></td >
	 		<td colspan="11">
	 			<textarea id="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" confirm_field='true'  name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" class="span12" rows="3" cols="20" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" _date="textarea_box" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> ><?php echo $this->_tpl_vars['confirm_info']['default']; ?>
</textarea>
	 			<?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
	 		</td>
		</tr>
		
	<?php elseif ($this->_tpl_vars['confirm_info']['data_type'] == 4): ?>
	<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'order_count'), $this);?>
<!-- 从新计算 -->
	 	<tr>
	 		<td class="micro-label"><label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
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
<?php echo smarty_function_counter(array('start' => 0,'print' => false,'assign' => 'order_count'), $this);?>
<!-- 从新计算 -->
<tr>
    <td class="micro-label">
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
       <?php if ($this->_tpl_vars['order_count']%2 == 0): ?>
        <tr>
       <?php endif; ?>
       <td class="narrow-label"  >  <label for="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
"  ><?php echo $this->_tpl_vars['confirm_info']['name']; ?>
：<label></td>
       <td > 
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
" ><?php echo $this->_tpl_vars['detail']; ?>
</option>
           <?php endforeach; endif; unset($_from); ?>
           <option value='' selected>&nbsp;</option>
        </select>
        <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
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
" confirm_field='true' name="<?php echo $this->_tpl_vars['confirm_info']['fields']; ?>
" value="<?php echo $this->_tpl_vars['confirm_info']['default']; ?>
" <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?>class="easyui-validatebox" required="true" missingMessage="<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
不能为空" if_require='true' _chinese_name='<?php echo $this->_tpl_vars['confirm_info']['name']; ?>
'<?php endif; ?> />
         <?php if ($this->_tpl_vars['confirm_info']['if_require'] == 2): ?><span style='color:red;font-weight:bold;'>*</span><?php endif; ?>
         <?php endif; ?>
        </td>   
       <?php if ($this->_tpl_vars['order_count']%2 != 0): ?>
          </tr>
       <?php endif; ?>
       <?php echo smarty_function_counter(array('print' => false,'assign' => 'order_count'), $this);?>
<!--   加1   -->
     <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <!--自定义字段  ------end--------->
    <?php if ($this->_tpl_vars['order_base']['order_remark']): ?>
      <tr>
         <td class="narrow-label"><label for="order_remark">备注：</label></td>		           
         <td colspan="11"><textarea id="order_remark"  name="order_remark"  order_base_field='true' class="span12" rows="3" cols="20"></textarea></td>
      </tr>
     <?php endif; ?>
  </tbody>
 </table>
</div>
</div>

<!--订单产品信息-->
<div class='easyui-panel'  style="background-color:white" >
<table id="order_product_list" title='订单产品列表' width="100%" style="height:auto" rownumbers="true" iconCls="icon-search" singleSelect="true" idField="op_id" url="">
	<thead>
		<tr>
		    <th field="op_id"  hidden="true" ></th> 
		    <th field="product_id"  hidden="true" >产品ID</th> 
		    <th field="product_num" width="100" align="center" >产品编号</th> 
            <th field="product_name_view" width="100" align="center">产品名称</th>
            <th field="product_thum_pic" width="100" align="center" >产品示意图</th>
            <th field="product_thum_value" hidden="true" ></th>
			<th field="product_class" width="100" align="center">产品分类</th>			
			<th field="product_price" width="100" align="center">单价(元)</th>
			<th field="product_discount" width="60" align="center">折扣(%)</th>
			<th field="product_number" width="60" align="center">数量</th>
		</tr>
	</thead>
</table>
<input type='hidden' id='product_info' name='product_info' value='' />
<div style='text-align:right;padding:10px;'>
	<span style='font-weight:bold;'>累加总额：</span><span id='product_total' style='width:50px;'></span>元&nbsp;&nbsp;
</div>
</div>

<div id='window_class'></div>
<div id='client_window'></div>
<div id='order_product_detail_window'></div>
<div id='set_field_confirm'></div><!--  自定义字段设置   -->
<div id='set_dictionary'></div><!--  数据字典   -->



<div class='form-div' style='text-align:right;padding:5px;'>
	<span style='color:red' id='_order_msg'></span>&nbsp;&nbsp;
	 <button type="button" class="btn btn-primary" id="save_btn" onclick="save_order_info()">
        <span class="glyphicon glyphicon-saved"></span> 添加订单
    </button>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript" src="./jssrc/jquery.preview.js"></script>
<script language="JavaScript" type="text/javascript">
var jl_field_type = eval("(" + '<?php echo $this->_tpl_vars['jl_field_type']; ?>
' + ")");
var flag_con_info = 0;
<?php if ($this->_tpl_vars['client_info']['con_name']): ?>
flag_con_info = 1;
<?php endif; ?>
var power_phone_view = <?php echo $this->_tpl_vars['power_phone_view']; ?>
;
var global_con_name = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['client_info']['con_name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var global_cle_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['client_info']['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var system_order_product_amount = <?php echo ((is_array($_tmp=@$this->_tpl_vars['system_order_product_amount'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var power_view_product = <?php echo $this->_tpl_vars['power_view_product']; ?>
;
var power_use_contact = <?php echo $this->_tpl_vars['power_use_contact']; ?>
;

function order_confirm_dictionary()
{
	$('#order_tools').tabs({
		tools:[
		<?php if ($this->_tpl_vars['power_fieldsconfirm_order']): ?>
		{
			iconCls:'icon-seting',
			text:'订单自定义字段',
			handler:function(){
				edit_field_confirm();
			}
		},
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_dictionary_order']): ?>
		{
			iconCls:'icon-seting',
			text:'数据字典',
			handler:function(){
				edit_dictionary();
			}
		}
		<?php endif; ?>
		]
	});
}
</script>
<script src="./jssrc/viewjs/order_add.js?1.1" type="text/javascript"></script>