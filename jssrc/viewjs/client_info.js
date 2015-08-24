//检测客户姓名是否重复
var _repeat_window_state = 'close';
$(document).ready(function(){
	if(!power_phone_view && global_cle_phone)
	{
		$('#cle_phone').val(hidden_part_number($('#cle_phone').val()));
	}

    $('#tab_contact').tabs();
    $('#tab_client').tabs();

	//保存之后
	var cookie_end_type = get_cookie("est_end_type");
	if( !cookie_end_type )
	{
		cookie_end_type = 0;
	}
	$("input[type=radio][name='end_type'][value='"+cookie_end_type+"']").attr("checked",'checked');
	//初始化跳转选项
	record_end_type(cookie_end_type);
});

//自定义字段设置
function edit_field_confirm(field_type)
{
	var title = "";
	var top = 150;
	if(field_type == 0)
	{
		title = " - 客户字段自定义";
	}
	else if(field_type == 1)
	{
		title = " - 联系人字段自定义";
		top = 350;
	}

	$('#set_field_confirm').window({
		title: '自定义字段设置'+title,
		href:"index.php?c=field_confirm&m=field_setting&field_type="+field_type,
		iconCls: "icon-seting",
		top:top,
		width:600,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		shadow:false,
		modal:true
	});
}

//数字字典
function edit_dictionary()
{
	$('#set_dictionary').window({
		title: '数字字典',
		href:"index.php?c=dictionary&type=client",
		iconCls: "icon-seting",
		top:150,
		width:600,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		shadow:false,
		modal:true,
		onClose:function()
		{
			window.location.reload();
		}
	});
}

/*点击选择阶段*/
function click_stage_step(stage_index,total)
{
	//去除所有class[ done  lastDone  current   mainNavNoBg   ]
	$("span[cvalue='span_step'],mainNav").removeClass();
	//初始化最后一项设置
	$("#stage_step_"+eval(total-1)+"").addClass("mainNavNoBg");

	//把选中项设置为 current
	$("#cle_stage").val($("#stage_step_"+stage_index+"")[0].title);
	if( stage_index == (total-1) )
	{
		//最后一项
		$("#stage_step_"+stage_index+"").addClass("mainNavNoBg current");
	}
	else
	{
		$("#stage_step_"+stage_index+"").addClass("current");
	}

	//客户阶段改变 状态变
	//change_cle_stat();

	//当前选中项向后设置
	var temp_index = stage_index;
	while( temp_index >= 0 )
	{
		temp_index --;
		if( temp_index == (stage_index-1) )
		{
			$("#stage_step_"+temp_index+"").addClass("lastDone");
		}
		else
		{
			$("#stage_step_"+temp_index+"").addClass("done");
		}
	}
}

//客户阶段改变 状态变
function change_cle_stat()
{
	var cle_stage = $('#cle_stage').val();
	if(cle_stage.length == 0)
	{
		$("input[type=radio][name='cle_stat'][value='未拨打']").attr("checked",'checked')
	}
	else if(cle_stage.length != 0)
	{
		$("input[type=radio][name='cle_stat'][value='呼通']").attr("checked",'checked')
	}
}

//号码验证
function number_verification(phone_id,msg_id)
{
	var phone = $('#'+phone_id).val();
	// ^[0-9]*[1-9][0-9]*$  正整数  _phone_update_msg
	r = /^[0-9]*[1-9][0-9]*$/;
	if(phone.length!=0)
	{
		if(r.test(phone))
		{
			var msg = '';
			if(phone.length>5)
			{
				check_repeat_data(phone_id);//判断重复
			}
		}
		else
		{
			var msg = "<img src='./image/important.gif' border='0' height='16' width='16' align='absmiddle' />&nbsp;<b>错误输入，号码必须是由数字组成的</b>";
		}

		$('#'+msg_id).html(msg);

	}

}

//保存之后 -
function record_end_type(type)
{
	set_cookie("est_end_type",type);

	$("#label_type_0").css('color','#335b64');
	$("#label_type_1").css('color','#335b64');
	$("#label_type_2").css('color','#335b64');
	$("#label_type_"+type+"").css("color","red");
}


//保存，保存客户信息
function save_client_info()
{
    //自定义必选字段判断
	var if_continue = true;
	var must_msg = '';
	$("[if_require='true']:input").each(function(){
		if($(this).attr('_date')=='date_box'|| $(this).attr('_date')=='textarea_box')
		{
			if($(this).val()=='')
			{
				if_continue = false;
				must_msg += '['+$(this).attr('_chinese_name')+"]";
			}
		}
		else if(!$(this).validatebox('isValid'))
		{
			must_msg += '['+$(this).attr('_chinese_name')+"]"; 
			if_continue = false;
		}
	});
	if(	if_continue == false)
	{
		if(must_msg.length!=0)
		{
			_show_msg(must_msg+" 不能为空",'error');
		}
		return false;
	}

	$('#save_client').attr('disabled',true);
	var _data = {};
	//客户基本信息
	_data.cle_name          = $("#cle_name").val(); //客户名称
	_data.cle_phone         = $("#cle_phone").val(); //客户电话
	//可配置的基本字段 cle_info_source cle_address cle_remark cle_province_id cle_city_id con_mail con_remark cle_stat cle_stage
	$("[base_field='true']:input").each(function(){
		if($(this).attr('name')=='cle_stat')
		{
			_data.cle_stat = $("input:radio[name='cle_stat']:checked").val(); //号码状态
		}
		else
		{
			_data[$(this).attr('id')] = $(this).val();
		}
	});
	if( empty_obj(_data) )
	{
		_show_msg("客户基本信息为空，请填写数据！",'error');
		return;
	}
	if(_data.cle_province_id!=0)
	{
		_data.cle_province_name        = $("#cle_province_id").find("option:selected").text();//省
	}
	else
	{
		_data.cle_province_name = '';
	}
	_data.cle_city_id        = $("#cle_city_id").val();//市id
	if(_data.cle_city_id!=0)
	{
		_data.cle_city_name        = $("#cle_city_id").find("option:selected").text();//市
	}
	else
	{
		_data.cle_city_name = '';
	}
	//联系人基本信息
	if(power_use_contact!=1)
	{
		_data.con_name          = $("#con_name").val();//联系人姓名
		_data.con_mobile        = $("#con_mobile").val();//联系人电话
	}
	//自定义字段
	$("[confirm_field='true']:input").each(function(){
		if($(this).attr('type')=='checkbox')
		{
			var field1 = $(this).attr('checkbox_name')+'_1';
            var field2 = $(this).attr('checkbox_name')+'_2';
            var pid = {};
            _data[field1] = "";
            _data[field2] = "";
			 $("input:checkbox[name='"+$(this).attr('name')+"']").each(function(i){ 
                if($(this).attr("checked")){
                    _data[field2] += $(this).val()+",";
                    pid[$(this).attr('checkbox_pid')] = $(this).attr('checkbox_pid');
                }
            });
            $.each(pid, function(index, value) {
            	 _data[field1] += value+",";
            });
		}
		else
		{
			_data[$(this).attr('name')] = $(this).val();
		}
	});
	
	if(power_phone_view!=1)
	{
		if(_data.cle_phone && exist_star(_data.cle_phone) )
		{
			_data.cle_phone = global_cle_phone;
		}
	}
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=insert_client",
		data:_data,
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				//当前Tab的title
				var tabTitle = window.parent.get_current_tabTitle();

				var end_type = $("input:radio[name='end_type']:checked").val()
				if(end_type == 0 )
				{
					//业务受理
					window.parent.addTab('业务受理',"index.php?c=client&m=accept&cle_id="+responce['content'],"menu_icon");
					window.parent.closeTab(tabTitle);
				}
				else if(end_type ==1)
				{
					//继续添加客户
					window.parent.addTab('添加客户',"index.php?c=client&m=new_client","menu_icon");
					window.parent.closeTab(tabTitle);
				}
				else if(end_type==2)
				{
					//返回客户列表
					window.parent.addTab("客户管理","index.php?c=client","menu_icon_client");
					window.parent.closeTab(tabTitle);
				}
			}
			else
			{
				_show_msg("<b>"+responce['message']+"</b>",'error');
			}
			$('#save_client').attr('disabled',false);
		}
	});
}


//检测客户姓名是否重复
function check_repeat_data(phone_id)
{
	if(_repeat_window_state == 'open')
	{
		return;
	}
	//客户姓名
	var cle_name = $("#cle_name").val();
	if(  cle_name.length < 2 && cle_name)
	{
		cle_name = "";
	}

	//客户电话
	var cle_phone = $("#"+phone_id).val();
	if(power_phone_view==0)
	{
		if(cle_phone && exist_star(cle_phone) )
		{
			cle_phone = global_cle_phone;
		}
	}
	if( global_cle_phone == cle_phone && cle_phone)
	{
		cle_phone = "";
	}

	if( cle_name || cle_phone )
	{
		$('#save_client').attr('disabled',true);
		$.ajax({
			type:'POST',
			url: "index.php?c=client&m=check_repeat_data",
			data:{"name":cle_name,"phone":cle_phone},
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					if( responce["content"]  )
					{
						$('#display_repeat_data').window({
							href:"index.php?c=client&m=display_repeat_data&name="+cle_name+"&phone="+cle_phone,
							top:50,
							width:800,
							title:"重复性检查 - 数据库里已存在相似或重复的记录(多于10条时，只显示前10条)",
							collapsible:false,
							minimizable:false,
							maximizable:false,
							resizable:false,
							onOpen:function(){
								_repeat_window_state = 'open';
							},
							onClose:function(){
								_repeat_window_state = 'close';
							}
						});

						if(phone_ifrepeat==0)
						{
							/*  0不过滤号码重复，允许重复 */
							$('#save_client').attr('disabled',false);
						}
						else
						{
							//过滤重复号码
							if(responce["phone_repeat"] == 0)
							{
								//电话号码不重复
								$('#save_client').attr('disabled',false);
							}
						}
					}
					else
					{
						//电话号码不重复
						$('#save_client').attr('disabled',false);
					}
				}
				else
				{
					$('#save_client').attr('disabled',false);
				}
			}
		});
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
	$("#_save_msg").html(msg);
	setTimeout(function(){
		$("#_save_msg").html("");
	},4000);
}
