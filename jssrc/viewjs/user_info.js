var original_data = {};//原始数据
$(document).ready(function(){
	//验证
	$.extend($.fn.validatebox.defaults.rules, {
		minLength: {
			validator: function(value, param){
				return value.length >= param[0];
			},
			message: '密码长度需大于3位'
		}
	});

	$('#dept_id').combotree(
	{
		url:'index.php?c=department&m=get_department_tree',
		onClick:function(node)
		{
			$('#dept_id').combotree('tree').tree('expand',node.target);//改变目录状态
		},
		onBeforeLoad : function(node, param)
		{
			if (node)
			{
				return false;
			}
			else
			{
				$('#dept_id').combotree('options').url = 'index.php?c=department&m=get_department_tree';
			}
		}
	});
	if(role_session_id!=1 && _role_id==1)
	{
		$('#submit_user_btn').linkbutton({'disabled':true});
		$("#_user_msg").html('提示：权限不够，不能编辑该员工信息');
	}

	//记录原始数据
	original_data = get_modify_user_value();
	original_data.role_id = _role_id;
	original_data.dept_id = _dept_id;
	original_data.dept_name = _dept_name;

});

//关闭修改用户信息窗口
function cancel_user()
{
	$('#user_panel').window('close');
}

/*外呼主叫号码填写权限*/
function change_oucaller_type(stand)
{
	if( stand=='0' )
	$("#user_outcaller_num").attr('disabled',true);
	else if( stand=='1' )
	$("#user_outcaller_num").attr('disabled',false);
}

//保存用户信息
function save_user()
{
	if($('#user_name').val()== '')
	{
		_show_msg('员工姓名不能为空','error');
	}
	else
	{
		if( $('#dept_id').combobox('getValue'))
		{
			$('#submit_user_btn').attr('disabled',true);
			var newpassword=$('#password').val();//新密码
			if(newpassword)
			{
				if( !$('#password').validatebox('isValid') )
				{
					return false;
				}
			}
			$('#submit_user_btn').linkbutton({'disabled':true});
			//数据比较，获取修改过的数据
			var modify_data = get_modify_user_value();
			var change_data = data_comparison(original_data,modify_data);
			change_data.user_id	=  user_session_id;//用户id
			change_data.password	= $('#password').val();//新密码
			$.ajax(
			{
				type:'POST',
				url: "index.php?c=user&m=update_user",
				data: change_data,
				dataType:"json",
				success: function(responce)
				{
					if(responce['error'] === 0)
					{
						$('#user_list').datagrid('load');
						_show_msg('保存成功&nbsp;','yes');
						$('#submit_user_btn').linkbutton({'disabled':true});
						cancel_user();
					}
					else
					{
						_show_msg(responce['message'],'error');
					}
				}
			});
		}
		else
		{
			_show_msg('请选择部门','error');
			//$.messager.alert('错误','请选择部门','info');
		}
	}
}
/*数据比较，得到修改过的数据*/
function get_modify_user_value()
{
	var _data = {};
	_data.user_name = $('#user_name').val();
	_data.role_id	=  $('#role').val();//用户角色
	_data.dept_id	=  $('#dept_id').combobox('getValue');//部门id
	_data.dept_name	=  $('#dept_id').combobox('getText');//部门
	_data.user_remark	=  $('#user_remark').val();//备注
	_data.user_login_state	=  $("input:radio[name='user_login_state']:checked").val();//登陆初始状态
	_data.user_tel_server	=  $('#user_tel_server').val();//通讯服务器(SIP网关)
	_data.user_cti_server   =  $("#user_cti_server").val();//中间件服务器
	_data.user_phone_type	= $("input:radio[name='user_phone_type']:checked").val();//话机类型
	_data.user_outcaller_type	=  $("input:radio[name='user_outcaller_type']:checked").val();//外呼主叫类型
	_data.user_outcaller_num	=  $('#user_outcaller_num').val();//指定的外呼主叫
	_data.user_channel	=  $('#user_channel').val();//外呼通道
	var ofen_phone	= $("input:radio[name='ofen_phone']:checked").val();//是否常接通
	var free_phone = $("input:radio[name='free_phone']:checked").val();//是否自动外呼
	if(ofen_phone == 1)
	{
		if(free_phone == 1)
		{
			_data.user_ol_model	=  2;//常接通模式
		}
		else
		{
			_data.user_ol_model	=  1;//常接通模式
		}
	}
	else
	{
		_data.user_ol_model	=  0;//常接通模式
	}

	if($("#user_advan_set_info").css('display') == ''){
		_data.user_phone_permissions  = $("input:radio[name='user_phone_permissions']:checked").val();//呼叫权限
	}

	return _data;
}

/*高级设置*/
function advan_setting()
{
	var button_text = $("#advan_set_user__but").linkbutton("options").text;
	if(button_text=='高级设置')
	{
		$("#user_advan_set_info").css("display",'');
		$("#advan_set_user__but").linkbutton({text:"关闭高级设置"});
	}
	else
	{
		$("#user_advan_set_info").css("display",'none');
		$("#advan_set_user__but").linkbutton({text:"高级设置"});
	}
}

//显示消息
function _show_msg(msg)
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
	$("#_user_msg").html(msg);
	setTimeout(function(){
		$("#_user_msg").html("");
	},2000);
}