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

/*保存用户信息*/
function save_user_add()
{
	if($('#user_name').val().length==0 || $('#user_num').val().length==0)
	{
		$.messager.alert('错误','姓名或工号不能为空','info');
	}
	else
	{
		if( $('#dept_id').combotree('getValue'))
		{
			$('#submit_user_btn').attr('disabled',true);

			var _data = {};
			_data.user_name = $('#user_name').val();//姓名
			_data.user_num = $('#user_num').val();//工号
			_data.role_id = $('#role').val();//角色id
			_data.user_password = $('#user_password').val();//密码
			_data.dept_id = $('#dept_id').combotree('getValue');//部门
			_data.dept_name	= $('#dept_id').combobox('getText');
			_data.user_que = $("#user_que").val();//队列
			_data.user_tel_server = $('#user_tel_server').val();//通讯服务器(SIP网关)
			_data.user_cti_server = $("#user_cti_server").val();//中间件服务器
			_data.user_phone_type = $("input:radio[name='user_phone_type']:checked").val();//话机类型 1自动 2软电话 3话机
			_data.user_login_state = $("input:radio[name='user_login_state']:checked").val();//坐席登录状态 0默认1空闲2忙碌
			_data.user_outcaller_type =  $("input:radio[name='user_outcaller_type']:checked").val();//外呼主叫号码类型 0全部 1指定 
			_data.user_outcaller_num =  $('#user_outcaller_num').val();//指定的外呼主叫
			var ofen_phone = $("input:radio[name='ofen_phone']:checked").val();//是否常接通
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
			
			if($("#user_advan_set_info").css('display') == '')
            {
                _data.user_phone_permissions  = $("input:radio[name='user_phone_permissions']:checked").val();//呼叫权限
            }
			
			$.ajax(
			{
				type:'POST',
				url: "index.php?c=user&m=insert_user",
				data: _data,
				dataType:"json",
				success: function(responce)
				{
					if(responce['error'] === 0)
					{
						$('#user_list').datagrid('load');
						$('#user_panel').window('close');
					}
					else
					{
						$.messager.alert('错误',responce['message'],'info');
					}
				}
			});
		}
		else
		{
			$.messager.alert('错误','请选择部门','info');
		}
    }
}

/*控制外呼主叫号码框是否可填*/
function change_oucaller_type_add(stand)
{
	if( stand=='0' )
	$("#user_outcaller_num").attr('disabled',true);
	else if( stand=='1' )
	$("#user_outcaller_num").attr('disabled',false);
}

function on_num()
{
	$('#num_message').html('*');
	$('#submit_user_btn').linkbutton({'disabled':false});
}

/*判断工号是否存在*/
function check_num()
{
	if($('#user_num').val().length!=0)
	{
		var num = $('#user_num').val();
		$.ajax(
		{
			type:'POST',
			url: "index.php?c=user&m=if_have_user_num",
			data: {user_num:num},
			dataType:"json",
			success: function(responce)
			{
				if(responce['error'] === 0)
				{
					$('#num_message').html('该工号已存在');
					$('#submit_user_btn').linkbutton({'disabled':true});
				}
				else
				{
					$('#num_message').html('*');
					$('#submit_user_btn').linkbutton({'disabled':false});
				}
			}
		});
	}
}