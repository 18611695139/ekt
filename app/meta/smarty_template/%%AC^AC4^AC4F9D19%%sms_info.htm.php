<?php /* Smarty version 2.6.19, created on 2015-08-07 10:48:13
         compiled from sms_info.htm */ ?>
<?php if ($this->_tpl_vars['group_sms']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<div class="form-div">
<div style="line-height: 15px;padding-left:5px;">使用步骤及注意事项：</div>
<div style="padding-left:5px;">
<ol style="padding-left:25px;line-height:15px;">
<?php if ($this->_tpl_vars['group_sms']): ?> 
<li>上传文本文件(csv或txt)，手机号码之间用逗号或回车换行符隔开</li>
<?php endif; ?>
<li>短信模版使用：在短信模版页面建立模版，发短信时选择所需模板。</li>
</ol>
</div>
</div>
<div class="main-div">
<form name="_sms_form" id="_sms_form" action="" method="post" enctype="multipart/form-data">
   <table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:98%;" align="center" >
     <tbody >
     <?php if ($this->_tpl_vars['group_sms']): ?>       
        <tr >
          <td  width="110" align="right"> <input type="radio"  id="type_kind"  name="type_kind" value="3" onclick="radio_click(3)" checked/> <label>手机号码：</label></td><td><input type="text"  id="_group_phone"  name="_group_phone"  size="40" class="easyui-validatebox"  validType="phone_num" />（多个号码以逗号分隔）</td>
       </tr>
       <tr>
          <td width="110" align="right">
          <input type="radio" align="right" id="type_kind"  name="type_kind" value="2" onclick="radio_click(2)"/> <label>手机号码：</label></td><td><input type="file" name="_file_address" id="_file_address" size="40" disabled  /><img id="loading" src="./image/loading.gif" style="display:none;">
          </td>
       </tr>
       <?php else: ?>       
       <!--    单条发送时，在这儿  -->
         <tr >
          <td  width="110" align="right"> <label>手机号码：</label></td><td><input  id="_single_phone"  name="_single_phone"  value="" class="easyui-validatebox"  validType="phone_single" /></td>
       </tr>
       <?php endif; ?>
        <tr id="tr_msg_model">
          <td  width="110" align="right"> <label>短信模板：</label></td><td><input name="sms_model" id="sms_model" style="width:425;margin-left:20px;" value='--请选择短信模板--' ></td>
       </tr>
       <tr>
          <td width="110" align="right"><label for="_sms_content">短信内容：</label></td><td>
          <textarea id="_sms_content"   name="_sms_content"  rows="10" cols="58" onpropertychange="calculation()" oninput="calculation()" style="width:400px;"></textarea></td>   
       </tr>
       <tr>
          <td width="110"></td><td  align="left" >您一共输入了<input type="text" id="_cal" name="_cal"  value="0" style="border:0;color:red;width:60px;" readonly/>个字,还可以输入<input type="text" id="_left_num" name="_left_num" value="<?php echo $this->_tpl_vars['MAX_smsLength']; ?>
"  style="border:0;color:red;width:60px;" readonly/>个字（<input type="text" id="tiaoshu" name="tiaoshu"  value="0/<?php echo $this->_tpl_vars['MAX_tiaoshu']; ?>
"  style="border:0;color:red;width:60px;" readonly/>条）</td>
        </tr>
        <tr>
           <td width="110"></td>
	       <td align="left" >
	       <div style="margin-left:350px;">
		  <a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)"  id="_btn_submit" onclick="_send_sms();" >发送</a>
		  <span id='upload_msg' style='color:red;'></span>
		  </div>
		 </td>
        </tr>
     </tbody>
   </table>
</form>
</div>
<div id="massmsg_add_contact"></div>
<?php if ($this->_tpl_vars['group_sms']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<script type="text/javascript" src="jssrc/ajaxfileupload.js"></script>
<script src="./jssrc/easyui-validtype.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var power_phone_view = <?php echo $this->_tpl_vars['power_phone_view']; ?>
;
var receiver_phone = '<?php echo $this->_tpl_vars['receiver_phone']; ?>
';

var MAX_smsLength = <?php echo $this->_tpl_vars['MAX_smsLength']; ?>
;
var EACH_smsLength = <?php echo $this->_tpl_vars['EACH_smsLength']; ?>
;
var MAX_tiaoshu = <?php echo $this->_tpl_vars['MAX_tiaoshu']; ?>
;
var group_sms = <?php echo $this->_tpl_vars['group_sms']; ?>
;
</script>
<script src="./jssrc/viewjs/sms_info.js" type="text/javascript"></script>