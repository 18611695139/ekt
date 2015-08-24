<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:50
         compiled from system_config_info.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="main-div">
<table borderColor="#ffffff" cellSpacing="0" cellPadding="0"  style="width:100%;border-width:0px">
<tbody >
    <tr>
	<td class="narrow-label" ><label for="sms_signature">短信后缀：</label></td>
	<td style="width: 30%"><input type='text' id='sms_signature' name='sms_signature' value='<?php echo $this->_tpl_vars['config_info']['sms_signature']; ?>
' /><span style="color:red;">提示：短信末尾自动添加后缀</span></td>
	</tr>
    <tr>
        <td class="narrow-label" ><label for="client_amount">客户限制数量：</label></td>
        <td style="width: 30%"><input type='text' id='client_amount' class="easyui-validatebox" data-options="required:true,validType:['number','minLength[1]']" name='client_amount' value="<?php echo $this->_tpl_vars['config_info']['client_amount']; ?>
" /><span style="color:red;">提示：必须数字 （0代表无限制）</span></td>
    </tr>
    <tr>
	<td class="narrow-label" ><label  title="通道连接方式">通道连接方式：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="connect_type_1" title="flash">
		<input type="radio" name="connect_type"  id="connect_type_1" value="swf"  <?php if ($this->_tpl_vars['config_info']['connect_type'] == 'swf'): ?>CHECKED<?php endif; ?>  /> flash
	</label>
	<label class="checkbox inline" for="connect_type_2" title="消息队列">
		<input type="radio" name="connect_type" id="connect_type_2" value="amq" <?php if ($this->_tpl_vars['config_info']['connect_type'] == 'amq'): ?>CHECKED<?php endif; ?> /> 消息队列（网络条件不好的时候用）
	</label>
	</td>
	</tr>
	<tr>
	<td class="narrow-label" ><label title="是否使用联系人模块">是否使用联系人模块：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="use_contact1" title="使用联系人模块">
		<input type="radio" name="use_contact"  id="use_contact1" value="0"  <?php if ($this->_tpl_vars['config_info']['use_contact'] == 0): ?>CHECKED<?php endif; ?>  /> 是
	</label>
	<label class="checkbox inline" for="use_contact2" title="不使用联系人模块">
		<input type="radio" name="use_contact" id="use_contact2" value="1" <?php if ($this->_tpl_vars['config_info']['use_contact'] == 1): ?>CHECKED<?php endif; ?> /> 否
	</label>
	</td>
	</tr>
	<tr>
	<td class="narrow-label" ><label title="设置坐席能否处理不属于自己的数据来电">是否可处理非本人数据的来电：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="deal_other_1" title="客户来电，可以处理不属于自己的数据">
		<input type="radio" name="deal_other_client"  id="deal_other_1" value="1"  <?php if ($this->_tpl_vars['config_info']['deal_other_client'] == 1): ?>CHECKED<?php endif; ?>  /> 是
	</label>
	<label class="checkbox inline" for="deal_other_2" title="客户来电，只能处理属于自己的数据">
		<input type="radio" name="deal_other_client" id="deal_other_2" value="2" <?php if ($this->_tpl_vars['config_info']['deal_other_client'] == 2): ?>CHECKED<?php endif; ?> /> 否
	</label>
	</td>
	</tr>
	<tr>
	<td class="narrow-label" ><label>是否允许客户号码重复：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="ifrepeat_1">
		<input type="radio" name="phone_ifrepeat" id="ifrepeat_1" value="0"  <?php if (! $this->_tpl_vars['config_info']['phone_ifrepeat']): ?>CHECKED<?php endif; ?>  /> 允许重复
	</label>
	<label class="checkbox inline" for="ifrepeat_2">
		<input type="radio" name="phone_ifrepeat" id="ifrepeat_2" value="1" <?php if ($this->_tpl_vars['config_info']['phone_ifrepeat'] == 1): ?>CHECKED<?php endif; ?> /> 不允许重复
	</label>
	</td>
	</tr>
	<?php if ($this->_tpl_vars['power_order']): ?>
	<tr>
	<td class="narrow-label" ><label>下订单时允许产品数量：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="order_product_1">
		<input type="radio" name="order_product_amount" id="order_product_1" value="1"  <?php if ($this->_tpl_vars['config_info']['order_product_amount'] == 1): ?>CHECKED<?php endif; ?>  /> 最多一个产品
	</label>
	<label class="checkbox inline" for="order_product_2">
		<input type="radio" name="order_product_amount" id="order_product_2" value="2" <?php if ($this->_tpl_vars['config_info']['order_product_amount'] == 2): ?>CHECKED<?php endif; ?> /> 可多个产品
	</label>
	</td>
	</tr>
	<?php endif; ?>
	<tr>
	<td class="narrow-label" ><label title="员工部门改变，处理所属数据">员工部门改变时，相应数据处理：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" for="dept_dealData_1" title="数据所属部门改变为新部门">
		<input type="radio" name="change_dept_dealData"  id="dept_dealData_1" value="1"  <?php if ($this->_tpl_vars['config_info']['change_dept_dealData'] == 1): ?>CHECKED<?php endif; ?>  /> 数据所属部门改变为新部门
	</label>
	<label class="checkbox inline" for="dept_dealData_2" title="收回所属数据">
		<input type="radio" name="change_dept_dealData" id="dept_dealData_2" value="2" <?php if ($this->_tpl_vars['config_info']['change_dept_dealData'] == 2): ?>CHECKED<?php endif; ?> /> 收回所属数据
	</label>
	</td>
	</tr>
	<?php if ($this->_tpl_vars['power_order'] || $this->_tpl_vars['power_service']): ?>
	<tr>
	<td class="narrow-label" ><label title="删除客户时，相应数据处理">删除客户时，相应数据处理：</label></td>
	<td style="width: 30%">
	<label class="checkbox inline" >
		<input type="radio" name="delete_client_relative" value="1"  <?php if ($this->_tpl_vars['config_info']['delete_client_relative'] == 1): ?>CHECKED<?php endif; ?>/> 不作处理
	</label>
	<label class="checkbox inline" >
		<input type="radio" name="delete_client_relative" value="2" <?php if ($this->_tpl_vars['config_info']['delete_client_relative'] == 2): ?>CHECKED<?php endif; ?>/> 一同删除
	</label>
	</td>
	</tr>
	<?php endif; ?>
    <tr>
        <td class="narrow-label" ><label  title="系统呼叫类型">系统呼叫类型：</label></td>
        <td style="width: 30%">
            <label class="checkbox inline">
                <input type="radio" name="call_type" value="0"  <?php if ($this->_tpl_vars['config_info']['call_type'] == 0): ?>CHECKED<?php endif; ?> /> 呼入呼出
            </label>
            <label class="checkbox inline">
                <input type="radio" name="call_type" value="1" <?php if ($this->_tpl_vars['config_info']['call_type'] == 1): ?>CHECKED<?php endif; ?> /> 呼入
            </label>
            <label class="checkbox inline">
                <input type="radio" name="call_type" value="2" <?php if ($this->_tpl_vars['config_info']['call_type'] == 2): ?>CHECKED<?php endif; ?> /> 呼出
            </label>
        </td>
    </tr>

    <tr>
        <td class="narrow-label" ><label  title="系统自动回收客户功能">系统自动回收客户功能：</label></td>
        <td style="width: 30%">
            <label class="checkbox inline" onclick='show_auto_tab()'>
                <input type="radio" name="auto_back_client" value="0" <?php if ($this->_tpl_vars['config_info']['auto_back_client'] == 0): ?>CHECKED<?php endif; ?> /> 不启用
            </label>
            <label class="checkbox inline" onclick='show_auto_tab()'>
                <input type="radio" name="auto_back_client" value="1" <?php if ($this->_tpl_vars['config_info']['auto_back_client'] == 1): ?>CHECKED<?php endif; ?> /> 启用方案一
            </label>
            <label class="checkbox inline" onclick='show_auto_tab()'>
                <input type="radio" name="auto_back_client" value="2" <?php if ($this->_tpl_vars['config_info']['auto_back_client'] == 2): ?>CHECKED<?php endif; ?> /> 启用方案二
            </label>
            <div id="Options1" style="display:none;">
                方案一：自动回收<input type='text' id='no_contact_day11' name='no_contact_day11' value="<?php echo $this->_tpl_vars['config_info']['no_contact_day1']; ?>
" style="width:50px;" />天没联系客户;<br/>
                数据回收后位置<select id="auto_back_place1" name="auto_back_place1">
                  					<option value="0" <?php if ($this->_tpl_vars['config_info']['auto_back_place'] == 0): ?>selected<?php endif; ?> >公司</option>
                  					<option value="1" <?php if ($this->_tpl_vars['config_info']['auto_back_place'] == 1): ?>selected<?php endif; ?> >数据所属部门<option>
                  				</select>
            </div>
            <div id="Options2" style="display:none;">
                方案二：
                    临界点（数据创建时间至今的天数）：<input type='text' id='client_has_create_day' name='client_has_create_day' value="<?php echo $this->_tpl_vars['config_info']['client_has_create_day']; ?>
"  style="width:50px;" /> 天 ，
                   	临界点内<input type='text' id='no_contact_day1' name='no_contact_day1' value="<?php echo $this->_tpl_vars['config_info']['no_contact_day1']; ?>
" style="width:50px;" /> 天没联系客户自动回收 ，
                  	临界点外<input type='text' id='no_contact_day2' name='no_contact_day2' value="<?php echo $this->_tpl_vars['config_info']['no_contact_day2']; ?>
" style="width:50px;" /> 天没联系客户自动回收;<br/>
                  	数据回收后位置<select id="auto_back_place2" name="auto_back_place2">
                  					<option value="0" <?php if ($this->_tpl_vars['config_info']['auto_back_place'] == 0): ?>selected<?php endif; ?> >公司</option>
                  					<option value="1" <?php if ($this->_tpl_vars['config_info']['auto_back_place'] == 1): ?>selected<?php endif; ?> >数据所属部门<option>
                  				</select>
            </div>
        </td>
    </tr>
    <tr>
        <td class="narrow-label" ><label  title="是否启用历史信息(客户)">是否启用历史信息(客户)：</label></td>
        <td style="width: 30%">
            <label class="checkbox inline" onclick='show_auto_tab()'>
                <input type="radio" name="use_history" value="1"  <?php if ($this->_tpl_vars['config_info']['use_history'] == 1): ?>CHECKED<?php endif; ?> /> 是
            </label>
            <label class="checkbox inline" onclick='show_auto_tab()'>
                <input type="radio" name="use_history" value="0"  <?php if ($this->_tpl_vars['config_info']['use_history'] == 0): ?>CHECKED<?php endif; ?> /> 否
            </label>
            <div id="Options3" style="display:none;">
               临界天数：<input type='text' id='created_day' name='created_day' value="<?php echo $this->_tpl_vars['config_info']['created_day']; ?>
" style="width:50px;" /> 天 （数据创建时间、更新时间、上次联系时间都大于临界天数，即该时间内数据没修改过，即可放进历史数据里）

            </div>
        </td>
    </tr>
    <tr>
        <td class="narrow-label" ><label  title="是否启用转技能组功能">是否启用转技能组功能：</label></td>
        <td style="width: 30%">
            <label class="checkbox inline">
                <input type="radio" name="use_transque" value="1"  <?php if ($this->_tpl_vars['config_info']['use_transque'] == 1): ?>CHECKED<?php endif; ?> /> 是
            </label>
            <label class="checkbox inline">
                <input type="radio" name="use_transque" value="0"  <?php if ($this->_tpl_vars['config_info']['use_transque'] != 1): ?>CHECKED<?php endif; ?> /> 否
            </label>
        </td>
    </tr>

	<tr>
	 <td align="center" colSpan="2">
	  <button class="btn btn-primary" id="save_sys_config" onclick="save_config()">
          <span class="glyphicon glyphicon-saved"></span> 保存设置
      </button>
	 </td>
	</tr>
 </tbody>
</table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
var original_data = {};//原始数据
$(document).ready(function(){
	//记录原始数据
	original_data = get_current_value();

    show_auto_tab();
});
/**
 * 保存
 * @returns {boolean}
 */
function save_config()
{
	if( !$('#client_amount').validatebox('isValid') )
	{
		$("#client_amount").focus();
		return false;
	}
	var current_data = get_current_value();
	var changed_data = data_comparison(original_data,current_data);

	$('#save_sys_config').linkbutton({'disabled':true});
	$.ajax({
		type:"POST",
		url:'index.php?c=system_config&m=update_config',
		data:changed_data,
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0')
			{
				$.messager.alert('成功','<br>修改信息成功,退出重登系统即可生效','info');

				//数据重新赋值
				original_data = get_current_value();
			}
			else
			{
				$.messager.alert('失败',"<br>"+responce['message'],'error');
			}

			$('#save_sys_config').linkbutton({'disabled':false});
		}
	});
}

//获取当前数据
function get_current_value()
{
	var _data = {};
	_data.sms_signature        = $("#sms_signature").val();//短信后缀
	_data.client_amount        = $("#client_amount").val();//客户限制数量
	_data.phone_ifrepeat       = $("input:radio[name='phone_ifrepeat']:checked").val();//电话号码过滤
	_data.order_product_amount = $("input:radio[name='order_product_amount']:checked").val();//订单产品数量
	_data.deal_other_client    = $("input:radio[name='deal_other_client']:checked").val();//处理非本人数据的来电
	_data.change_dept_dealData = $("input:radio[name='change_dept_dealData']:checked").val();//员工部门改变，处理所属数据
	_data.connect_type = $("input:radio[name='connect_type']:checked").val();//通道连接方式
	_data.use_contact = $("input:radio[name='use_contact']:checked").val();//是否使用联系人模块
	_data.delete_client_relative = $("input:radio[name='delete_client_relative']:checked").val();//删除客户时，相应数据处理
	_data.call_type = $("input:radio[name='call_type']:checked").val();//呼叫类型
	_data.auto_back_client = $("input:radio[name='auto_back_client']:checked").val();//系统自动回收客户功能
	_data.use_transque = $("input:radio[name='use_transque']:checked").val();//是否启用技能组功能
    if(_data.auto_back_client==1)
    {
        _data.no_contact_day1 = $('#no_contact_day11').val();
        _data.auto_back_place = $('#auto_back_place1').val();

    }
    else if(_data.auto_back_client==2)
    {
        _data.client_has_create_day = $('#client_has_create_day').val();
        _data.no_contact_day1 = $('#no_contact_day1').val();
        _data.no_contact_day2 = $('#no_contact_day2').val();
        _data.auto_back_place = $('#auto_back_place2').val();
    }
	_data.use_history = $("input:radio[name='use_history']:checked").val();//是否启用历史信息
    if(_data.use_history==1)
    {
        _data.created_day = $('#created_day').val();
    }
	return _data;
}

function show_auto_tab()
{
    $('#Options1').css('display','none');
    $('#Options2').css('display','none');
    $('#Options3').css('display','none');
    var auto_back_client = $("input:radio[name='auto_back_client']:checked").val();
    if(auto_back_client!=0)
    {
        $('#Options'+auto_back_client).css('display','');
    }
    var use_history = $("input:radio[name='use_history']:checked").val();
    if(use_history==1)
    {
        $('#Options3').css('display','');
    }
}
</script>