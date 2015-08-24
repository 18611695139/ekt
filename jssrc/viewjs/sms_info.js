//验证
$.extend($.fn.validatebox.defaults.rules, {
	phone_num: {
		validator: function (value, param) {
			return /^[\d\*]{11}(,[\d\*]{11})*$/.test(value);
		},
		message: '号码格式不正确：号码为0-9的数字(长度为11位)，以英文逗号分隔(首位不能输入逗号)'
	},
	phone_single: {
		validator: function (value, param) {
			return /^[\d\*]{11}$/.test(value);
		},
		message: '号码格式不正确：号码为0-9的数字(长度为11位)'
	}
});

$(document).ready(function (){
	calculation();//计算短信内容字数
	//短信模板
	$('#sms_model').combobox({
		url:'index.php?c=sms&m=get_smsmodel',
		valueField:'mod_id',
		textField:'theme',
		mode:'remote',
		onSelect:function(record){
			$("#_sms_content").val(record["content"]);
		}
	});

	//重置
	$('#_sms_form').get(0).reset();
	radio_click(1);

	if(!power_phone_view)
	$('#_single_phone').val(hidden_part_number(receiver_phone));
	else
	$('#_single_phone').val(receiver_phone);
});

//计算模板字数
function calculation()
{
	var length = $("#_sms_content").val().length;
	if( length <= MAX_smsLength)
	{
		$("#_cal").val(length);//已输入字符数
		$("#_left_num").val(MAX_smsLength-length);//剩余可输入字符数

		if(length<= EACH_smsLength) //小于70字
		{
			$("#tiaoshu").val(Math.ceil( length/EACH_smsLength )+"/"+MAX_tiaoshu );//短信条数
		}
		else
		{
			$("#tiaoshu").val(Math.ceil( (length-4)/EACH_smsLength )+"/"+MAX_tiaoshu );//短信条数
		}
	}
	else
	{
		var str = $("#_sms_content").val().substr(0,MAX_smsLength);
		$("#_sms_content").val(str);
		$.messager.alert('提示',"<br>达到短信模板最大输入上限",'info');
	}
}

//选择radio事件
function radio_click(num)
{
	if(num == 2 )
	{//文件导入手机号码
		$("#_file_address").attr('disabled',false);//文件导入手机号码
		$("#_group_phone").attr('disabled',true);//逗号分隔号码
	}
	else if(num == 3 )
	{//逗号分隔号码
		$("#_file_address").attr('disabled',true);//文件导入手机号码
		$("#_group_phone").attr('disabled',false);//逗号分隔号码
	}
}

//添加短信收件人
function mod_send_action()
{
	$('#massmsg_add_contact').window({
		href:"sms_massmsaage.php?act=add_contact",
		width:700,
		height:458,
		title:"添加收件人",
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		cache:false
	});
}

//发送信息
function _send_sms()
{
	var content = $("#_sms_content").val();//短信内容
	if(content=="" )
	{
		$.messager.alert('提示','<br>短信内容不能为空！','info');
		return  false;
	}

	if(group_sms)
	{
		var radio_type_kind =  $("input:radio[name='type_kind']:checked").val();//添加号码的方式   2文件上传号码   3逗号分隔号码
		if(radio_type_kind ==2)//文件导入接收人
		{
			var file_address=$('#_file_address').val();
			if(file_address.length==0)
			{
				$.messager.alert('错误','没有文件上传或短信的内容为空！','error');
				return false;
			}
			$('#_btn_submit').attr('disabled',true);
			//上传并发送
			ajaxFileUpload(content);
		}
		else if(radio_type_kind ==3)//手动添加接收人，以逗号分隔
		{
			var _receiver = $("#_group_phone").val();
			if(!_receiver )
			{
				$.messager.alert('提示','<br>请添加短信接收人<br>多个号码之间用逗号分隔！','info');
				return  false;
			}
			var expression = /^\d{11}(,\d{11})*$/;
			if( expression.test(_receiver) ==false )
			{
				$.messager.alert("提示","号码格式不正确：号码为0-9的数字(长度为11位)，以英文逗号分隔(首位不能输入逗号)","info");
				return false;
			}
			$('#_btn_submit').attr('disabled',true);

			//ajax发送
			var group_phone = $('#_group_phone').val();
			$.ajax({
				type:"POST",
				url:'index.php?c=sms&m=send_sms_by_comma',
				data:{'_group_phone':group_phone,'_sms_content':content},
				dataType:'json',
				success:function (responce)
				{
					if(responce['error']=='0')
					{
						$('#upload_msg').html("<img src='./themes/default/icons/ok.png' />&nbsp;发送成功");
						setTimeout(function(){
							$("#upload_msg").html("");
						},3000);
						//页面清空
						$('#_sms_form').get(0).reset();
					}
					else
					{
						$('#upload_msg').html("<img src='./themes/default/icons/no.png' />&nbsp;发送失败");
						setTimeout(function(){
							$("#upload_msg").html("");
						},3000);
					}
					$('#_btn_submit').attr('disabled',false);
				}
			});
		}
	}
	else
	{
		//接收号码
		var _single_phone = $("#_single_phone").val();
		if(!power_phone_view)
		{
			if(_single_phone &&  exist_star(_single_phone) )
			{
				_single_phone = receiver_phone;
			}
		}

		if(_single_phone == "")
		{
			$.messager.alert('提示','接收号码为空<br>请添加短信接收人','info');
			return  false;
		}
		if( !$("#_single_phone").validatebox('isValid') )
		{
			return false;
		}
		var _sms_content = $('#_sms_content').val();

		$('#_btn_submit').attr('disabled',true);
		$.ajax({
			type:"POST",
			url:'index.php?c=sms&m=send_sms_single',
			data:{'_single_phone':_single_phone,'_sms_content':_sms_content},
			dataType:'json',
			success:function (responce){
			}
		});
		//刷新短信列表
		if(  document.getElementById("sms_list") )
		{
			$('#sms_list').datagrid('reload');
		}
		$('#sys_send_sms_panel').window('close');
		$('#_btn_submit').attr('disabled',false);
	}
}

/*异步文件上传*/
function ajaxFileUpload(content)
{
	var fileValue =  $('#_file_address').val();
	if(fileValue.length == 0)
	return false;

	$("#loading").show();

	$.ajaxFileUpload({
		url:'index.php?c=sms&m=send_sms_by_file',
		secureuri:false,
		fileElementId:'_file_address',
		dataType: 'json',
		data:{sms_content:content},
		success: function (data, status)
		{
			$("#loading").hide();
			if(typeof(data.error) != 'undefined')
			{
				if(data.error != '')
				{
					$.messager.alert('执行错误',data.error,'error');
				}else
				{
					$('#upload_msg').html("<img src='./themes/default/icons/ok.png' />&nbsp;发送完成");
					setTimeout(function(){
						$("#upload_msg").html("");
					},3000);
					//页面清空
					$('#_sms_form').get(0).reset();
					$('#_btn_submit').attr('disabled',false);
				}
			}
		},
		error: function (data, status, e)
		{
			$.messager.alert('执行错误',e,'error');
		}
	});
	return false;
}