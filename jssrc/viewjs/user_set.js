//验证
$.extend($.fn.validatebox.defaults.rules, {
	minLength: {
		validator: function(value, param){
			return value.length >= param[0];
		},
		message: '密码长度需大于3位'
	},
	password_cp:{
		validator: function (value, param) {
			if(value != $('#password').val())
			return false;
			else
			return true;
		},
		message: '两次数据密码不同'
	}
});

function submit_theform()
{
	if($('#user_name').val().length==0)
	{
		$.messager.alert('失败','姓名不能为空','error');
	}
	else
	{
		var old_password=$('#old_password').val();//旧密码
		var newpassword=$('#password').val();//新密码
		var newpassword_cp=$('#password_cp').val();//密码确认
		var user_sms_phone=$('#user_sms_phone').val();//坐席短信提醒号码
		var user_login_state=$("input:radio[name='user_login_state']:checked").val();
		var user_if_tip = $("input:radio[name='user_if_tip']:checked").val();
		var user_display_dialpanel	= $("input:radio[name='user_display_dialpanel']:checked").val();//是否显示外呼窗口
		var user_outcall_popup	= $("input:radio[name='user_outcall_popup']:checked").val();//是否外呼弹屏
		var ofen_phone	= $("input:radio[name='ofen_phone']:checked").val();//是否常接通
		var free_phone = $("input:radio[name='free_phone']:checked").val();//是否自动外呼
		var user_phone_type = $("input:radio[name='user_phone_type']:checked").val();//话机类型
		var user_to_selfphone = $("input:radio[name='user_to_selfphone']:checked").val();//转手机功能
		if(ofen_phone == 1)
		{
			if(free_phone == 1)
			{
				var user_ol_model	=  2;//常接通模式
			}
			else
			{
				var user_ol_model	=  1;//常接通模式
			}
		}
		else
		{
			var user_ol_model	=  0;//常接通模式
		}
		var user_name = $('#user_name').val();

		if(old_password||newpassword||newpassword_cp)
		{
			if( !$('#password').validatebox('isValid') )
			{
				return false;
			}
			if( !$('#password_cp').validatebox('isValid') )
			{
				return false;
			}
		}
		$("#loading").show();
		$.ajax({
			type:"POST",
			url:'index.php?c=user&m=self_update_user',
			data:{'old_password':old_password,'password':newpassword,'user_sms_phone':user_sms_phone,'user_ol_model':user_ol_model,'user_display_dialpanel':user_display_dialpanel,'user_name':user_name,'dept_id':dept_id,'user_login_state':user_login_state,"user_if_tip":user_if_tip,'user_outcall_popup':user_outcall_popup,'user_phone_type':user_phone_type,'user_to_selfphone':user_to_selfphone},
			dataType:'json',
			success:function (responce){
				$("#loading").hide();
				if(responce['error']=='0'){
					$.messager.alert('成功','<br>修改信息成功','info');
				}
				else
				{
					if(responce['message'] == 2)
					{
						$.messager.alert('失败','原始密码错误','error');
					}
					else
					{
						$.messager.alert('失败',responce['message'],'error');
					}
				}
			}
		});
	}
}


/*高级设置*/
function advan_setting()
{
	var button_text = $("#advan_set_user_but").linkbutton("options").text;
	if(button_text=='高级设置')
	{
		$("#user_advan_set_info").css("display",'');
		$("#advan_set_user_but").linkbutton({text:"关闭高级设置"});
	}
	else
	{
		$("#user_advan_set_info").css("display",'none');
		$("#advan_set_user_but").linkbutton({text:"高级设置"});
	}
}