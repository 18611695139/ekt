<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:39
         compiled from client_other_message.htm */ ?>
<div class="main-div" style="width:99%;" align="center">
<fieldset>
       <textarea  style="width:99%;height:300px;overflow:auto;" id="log_detail" readonly ></textarea>
</fieldset>    
</div>

<table borderColor="#ffffff" cellSpacing="0" cellPadding="0" style="width:100%;" style="padding-top:6px; margin-top:10px">
<tbody >
	 <tr>
	  <td class="micro-label">所属部门：</td><td ><?php echo $this->_tpl_vars['client_info']['dept_name']; ?>
</td>
	 <td class="micro-label">所属人：</td><td ><?php echo $this->_tpl_vars['client_info']['user_name']; ?>
</td>
	 <td class="micro-label">创建时间：</td><td ><?php if ($this->_tpl_vars['client_info']['cle_creat_time'] != "0000-00-00"): ?><?php echo $this->_tpl_vars['client_info']['cle_creat_time']; ?>
<?php endif; ?></td>
	 </tr>
	 <tr>
	 <td class="micro-label">首次联系时间：</td><td><?php if ($this->_tpl_vars['client_info']['cle_first_connecttime'] != "0000-00-00"): ?><?php echo $this->_tpl_vars['client_info']['cle_first_connecttime']; ?>
<?php endif; ?></td>
	 <td class="micro-label">最近一次联系时间：</td><td><?php if ($this->_tpl_vars['client_info']['cle_last_connecttime'] != "0000-00-00"): ?><?php echo $this->_tpl_vars['client_info']['cle_last_connecttime']; ?>
<?php endif; ?></td>
	 <td class="micro-label">下次联系时间：</td><td><?php if ($this->_tpl_vars['client_info']['con_rec_next_time'] != "0000-00-00"): ?><?php echo $this->_tpl_vars['client_info']['con_rec_next_time']; ?>
<?php endif; ?></td>
	 </tr>
	 </tbody>
</table>

<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//日志
	var str = "";
	var get_result_log = <?php echo $this->_tpl_vars['result_log']; ?>
;
	var log_length     = get_result_log.length;
	if( log_length > 0 )
	{
		for(var i = 0; i < log_length ; i ++)
		{
			str += "["+get_result_log[i]["log_time"]+"  "+get_result_log[i]["user_name"]+"  "+get_result_log[i]["user_ip"]+"  ";
			var current = get_result_log[i]["contents"];
			var new_str = current.split("|");

			for(var j =0;j < new_str.length;j++)
			{
				if(j == 0 )
				{
					str += new_str[j]+"]\r\n";
				}
				else
				{
					<?php if (! $this->_tpl_vars['power_phone_view']): ?>
					new_str[j] =  new_str[j].replace(/^客户电话:.+$/,'客户电话:*********');
					new_str[j] =  new_str[j].replace(/^办公电话:.+$/,'办公电话:*********');
					new_str[j] =  new_str[j].replace(/^其他电话:.+$/,'其他电话:*********');
					<?php endif; ?>
					str += new_str[j]+"\r\n";
				}

			}
			str += "\r\n";
		}
	}

	$("#log_detail").val(str);
});
</script>