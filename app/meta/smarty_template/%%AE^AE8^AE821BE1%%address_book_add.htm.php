<?php /* Smarty version 2.6.19, created on 2015-08-17 10:27:42
         compiled from address_book_add.htm */ ?>
<input type="hidden"  id="tx_id"  name="tx_id" value="<?php echo $this->_tpl_vars['result']['tx_id']; ?>
"/>
<div  class="main-div" >
  <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:100%;padding-right:50px;" align="center">
     <tbody >
     <tr>
        <td class="micro-label" ><label>姓名:</label></td> <td><input type="text"  id="tx_name" name="tx_name"  value="<?php echo $this->_tpl_vars['result']['tx_name']; ?>
" size="21"/></td>
     </tr>
     <tr>    
     
        <td class="micro-label" ><label>手机:</label></td> <td>     <div class="border-div" > <div class="in_input"><input type="text" id="tx_phone1"  name="tx_phone1"  value="<?php echo $this->_tpl_vars['result']['tx_phone1']; ?>
" class="easyui-validatebox"   validType="phone_number"  /></div>  <div class="call_phone" onclick="sys_dial_num('','tx_phone1');" title="呼叫"></div>  <?php if ($this->_tpl_vars['power_sendsms']): ?><div class="send_msg" title="发短信" onclick="sys_send_sms('','tx_phone1');"  ></div><?php endif; ?>  </div>   </td>
        <td class="micro-label" ><label>备用手机:</label></td> <td>   <div class="border-div" > <div class="in_input"><input type="text" id="tx_phone2"  name="tx_phone2"  value="<?php echo $this->_tpl_vars['result']['tx_phone2']; ?>
" class="easyui-validatebox"  validType="phone_number"  /></div>  <div class="call_phone" onclick="sys_dial_num('','tx_phone2');" title="呼叫"></div>  <?php if ($this->_tpl_vars['power_sendsms']): ?><div class="send_msg" title="发短息" onclick="sys_send_sms('','tx_phone2');"  ></div><?php endif; ?>  </div>   </td>
     </tr>
     <tr>
        <td class="micro-label"><label for="remark">备注：</td ><td colspan="11"><textarea id="tx_remark"  name="tx_remark" style="width: 99%" rows="3" cols="20"><?php echo $this->_tpl_vars['result']['tx_remark']; ?>
</textarea></td>	
     </tr>
     </tbody>
  </table>
</div>

<div style="text-align:right;padding:5px;" > 
     <?php if ($this->_tpl_vars['type'] == 1): ?>  
      <a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="save_book_data(1)" >保存</a>   <!-- 公司数据  -->
      &nbsp; &nbsp; &nbsp; &nbsp;
      <?php endif; ?>
      
      <?php if ($this->_tpl_vars['type'] == 2): ?>
      <a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="save_book_data(2)" >保存</a>   <!-- 坐席个人数据  -->
      <?php endif; ?>
      <?php if ($this->_tpl_vars['type'] == 3): ?>
      <a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="save_book_data(3)" >保存</a>   <!-- 保存数据  -->
	  <?php endif; ?>
      </div> 
      
<script src="./jssrc/viewjs/address_book_add.js" type="text/javascript"></script>