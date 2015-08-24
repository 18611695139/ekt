var original_data = {};//原始数据
$(document).ready(function() {
	//得到通话ID
	get_callid();
	
	//  每一页的第一条时，更改为“上一页”
	if(parseInt(row_index) <= 1)
	{
		$('#last_order').html('<span class="glyphicon glyphicon-fast-backward"></span> 上一页');
	}
	// 每一页的最后一条时，更改为“下一页”
	if(parseInt(row_index) >= parseInt(row_limit))
	{
		$('#next_order').html('<span class="glyphicon glyphicon-fast-forward"></span> 下一页');
	}
	
	//联系人
	if(power_use_contact!=1)
	{
		var con_url = '';
		if(global_cle_id!=0)
		{
			con_url = "index.php?c=contact&m=get_contact_list";
		}

		$('#con_name').combogrid({
			panelWidth:153,
			showHeader:false,
			sortName:'con_if_main',
			sortOrder:'desc',
			idField:'con_id',
			textField:'con_name',
			url:con_url,
			queryParams:{"cle_id":global_cle_id},
			columns:[[
			{field:'con_name',width:150,align:"left",sortable:true}
			]],
			onClickRow:function(row_index,rowData)
			{
				var sec_con_mobile = rowData.con_mobile
				$("#real_con_mobile").val(sec_con_mobile);
				//不显示全部号码
				if(power_phone_view==0 &&sec_con_mobile)
				{
					sec_con_mobile =  hidden_part_number(sec_con_mobile);
				}
				$("#con_mobile").val(sec_con_mobile);
			},
			keyHandler: {
				up: function(){},
				down: function(){},
				enter: function(){},
				query: function(q){
					//不显示panel
					$(this).combo('hidePanel');
				}
			},
			onLoadSuccess:function()
			{
				if( flag_con_info > 0 )
				{
					$('#con_name').combogrid("setText",global_con_name);
					flag_con_info = 0;
				}
			}
		});
		if(power_phone_view==0)
		{
			$('#con_mobile').val(hidden_part_number(global_con_mobile));
		}
	}

	//记录原始数据
	original_data = get_current_value();
	if(power_use_contact!=1)
	original_data.con_name = global_con_name;

	$('#order_price').val(global_order_price);
	if(power_phone_view==0)
	{
		$('#cle_phone').val(hidden_part_number(global_cle_phone));
	}

	//自动保存并取下一条
	if(get_cookie("est_save_order_next") == 1)
	{
		$("#save_next").attr("checked",true)
	}

	//自动呼叫
	if(system_autocall)
	{
		_autocall(system_autocall_number);
	}

	//初始化订单状态
	var order_state = $('#order_state').val();
	change_order_state_step(order_state);
});


/**
* 自动呼叫客户，当客户电话为空时，呼叫联系人。
* @param phone_num 需要外呼的号码 默认为传过来的号码，一般是客户电话
**/
function _autocall(phone_num)
{
	if(!global_cle_id)
	{
		_show_msg("这个订单没有客户！");
		return;
	}

	if(get_cookie("last_order_id") != global_order_id)
	{
		set_cookie("last_order_id",global_order_id);
	}
	else
	{
		_show_msg("<img src='./themes/default/icons/no.png' /> <b>您刚和这个订单的客户联系了。</b>");
		return;
	}

	//如果客户电话为空，则呼叫联系人电话
	if(power_use_contact!=1)
	{
		if( phone_num == "")
		{
			if(global_con_mobile)
			{
				phone_num = global_con_mobile;
			}
			else
			{
				$.ajax({
					type:'POST',
					url: "index.php?c=contact&m=get_contact_phone",
					data:{"cle_id":global_cle_id},
					dataType:"json",
					success: function(responce){
						if(responce['error']=='0')
						{
							phone_num = responce["content"];
							if(phone_num == "")
							{
								$.messager.alert("提示","这个订单客户没有联系号码！","info")
								return;
							}
							else
							{
								sys_dial_num(phone_num);
							}
						}
						else
						{
							$.messager.alert('错误','获取联系人电话失败','error');
						}
					}
				});
			}
		}
	}
	sys_dial_num(phone_num);
}


/**
* 保存订单基本信息（保存订单数据前，验证订单数据）
*
*/
var _data = {};
function save_order_info()
{
	if(!power_update_order)//没有保存权限
	{
		return false;
	}
	//自定义必选字段判断
	var if_continue = true;
	var must_msg = '';
	$("[if_require='true']:input").each(function(){
		if($(this).attr('_date')=='date_box' || $(this).attr('_date')=='textarea_box')
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

	var pre_or_next = '';
	//默认参数 do_next(  上一条/下一条，如果数据有改变，先保存，然后下一条  )
	if(arguments[0])
	{
		pre_or_next = arguments[0];
	}

	var auto_call = false;
	//如果下一步为空，则看保存数据后是否默认取下一条并且呼叫
	if(pre_or_next == '' && ($("#save_next").attr("checked") == "checked"))
	{
		pre_or_next = 'next';
		auto_call = true;
	}

	if(user_session_id != global_user_id && role_type == 2)
	{
		//$.messager.alert('错误','您不能修改不属于您的订单！','error');
		_show_msg("当前订单所属人非您，修改失败",'error');
		$('#save_btn').linkbutton({'disabled':true});
		return;
	}
	else
	{
		//得到当前数据
		var current_data = get_current_value();
		if(current_data['order_price'].length!=0)
		{
			if(isNaN(current_data['order_price']))
			{
				_show_msg('<b>订单总价</b>只能填数字(整数/小数)','error');
				return;
			}
		}
		$('#save_btn').linkbutton({'disabled':true});
		//数据比较，得到修改过的数据

		var changed_data = data_comparison(original_data,current_data);
		changed_data.order_id = global_order_id;
		changed_data.user_id = global_user_id;
		$.ajax({
			type:'POST',
			url: "index.php?c=order&m=update_order",
			data:changed_data,
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					$('#save_btn').linkbutton({'disabled':false});
					_show_msg('保存订单成功','yes');

					switch(pre_or_next)
					{
						case 'pre': setTimeout(function(){_pre_or_next_order('pre')},1000);break;
						case 'next': setTimeout(function(){_pre_or_next_order('next',auto_call)},1000);break;
						default :
						original_data = get_current_value();//客户数据重新赋值
						break;
					}
				}
				else
				{
					$.messager.alert('错误',responce['message'],'error');
				}
			}
		});
	}
}

/*得到文本框中当前的值*/
function get_current_value()
{
	_data = {};
	_data.order_state = $('#order_state').val();
	_data.cle_id = $("#cle_id").val();
	_data.cle_name = $("#cle_name").val();
	_data.cle_phone = $("#real_cle_phone").val();
	_data.order_price = $('#order_price').val();

	//可设置的基本字段 客户地址cle_address、配送方式order_delivery_mode、配送编号order_delivery_number、备注order_remark
	$("[order_base_field='true']:input").each(function(){
		_data[$(this).attr('id')] = $(this).val();
	});

	//联系人
	if(power_use_contact!=1)
	{
		if(_data.cle_id != 0)
		{
			_data.con_name = 	$('#con_name').combogrid("getText");
			_data.con_mobile = number_verification(_data.cle_id,_data.con_name);
		}
		else
		{
			_data.con_name = 	'';
			_data.con_mobile = '';
		}
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
	return _data;
}

/*号码验证*/
function number_verification(cle_id,con_name)
{
	var con_mobile = $('#con_mobile').val();
	var real_con_moblie = $("#real_con_moblie").val();
	// ^[0-9]*[1-9][0-9]*$  正整数  _phone_update_msg
	r = /^[0-9]*[1-9][0-9]*$/;
	if(con_mobile.length!=0)
	{
		if(r.test(con_mobile))
		{
			if(global_con_mobile == con_mobile)
			return global_con_mobile;
			else
			{
				$.ajax({
					type:'POST',
					url: "index.php?c=contact&m=insert_contact",
					data:{'con_name':con_name,'con_mobile':con_mobile,'cle_id':cle_id},
					dataType:"json"
				});
				return con_mobile;
			}
		}
		else
		{
			return real_con_moblie;
		}
	}
	else
	return '';
}

/*选择客户*/
function _add_client()
{
	var phone = $("#cle_phone").val();
	if( exist_star(phone)  )
	{
		phone = "";
	}
	$('#client_window').window({
		title: '选择客户(我的客户)',
		href:"index.php?c=client&m=select_clients&phone="+phone,
		width:600,
		top:50,
		shadow:true,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

/*跳到上一条*/
function jump_pre_order()
{
	_check_data_change('pre');
}

/**
*跳到下一条
**/
function jump_next_order()
{
	_check_data_change('next');
}

/**
* 检查数据是否改变
* @param pre_or_next 下一步操作
**/
function _check_data_change(pre_or_next)
{
	if(!power_update_order)
	{
		_pre_or_next_order(pre_or_next);
		return;
	}

	//数据比较，得到修改过的数据
	var current_data = get_current_value();

	if(current_data['order_price'].length!=0)
	{
		if(isNaN(current_data['order_price']))
		{
			_show_msg('<b>订单总价</b>只能填数字(整数/小数)','error');
			return;
		}
	}
	var changed_data = data_comparison(original_data,current_data);
	if(!empty_obj(changed_data))
	{
		$.messager.defaults.ok = "保存";
		$.messager.defaults.cancel = "取消";
		$.messager.confirm("提示","<br>是否保存对数据的修改？",function (r){
			if(r)
			{
				//保存订单
				save_order_info(pre_or_next);
			}
			else
			{
				_pre_or_next_order(pre_or_next);
			}
		});
	}
	else
	{
		//取上一条/下一条
		_pre_or_next_order(pre_or_next);
	}
	return;
}

/**
* 上一条或者下一条数据
* @param pre_or_next  pre上一条 next下一条
* @param auto_call
**/
function _pre_or_next_order(pre_or_next)
{
	if(!system_pagination)
	{
		return false;
	}
	if(pre_or_next != 'pre' && pre_or_next != 'next')
	{
		return false;
	}

	var auto_call = false;
	if(arguments[1])
	{
		auto_call = arguments[1];
	}

	//当前页第一条
	if(pre_or_next == 'pre' && parseInt(row_index) <= 1  )
	{
		_trun_page_order('pre');
	}
	//当前页最后一条
	else if( pre_or_next == 'next' && parseInt(row_index) >= parseInt(row_limit) )
	{
		_trun_page_order('next',auto_call);
	}
	else
	{
		_jump_actual_order(pre_or_next,auto_call);
	}
}

//翻页  pre_or_next: pre上一页   next下一页
function _trun_page_order(pre_or_next,auto_call)
{
	if(pre_or_next == 'pre')
	{
		var order_list_param = get_cookie('order_list_param');
		var order_list_param_arr = order_list_param.split(",");
		var page = order_list_param_arr[0];
		if(page <= 1)
		{
			_show_msg("提示：当前已经是第一页！")
			return false;
		}
	}
	$.ajax({
		type:'POST',
		url: "index.php?c=order&m=trun_page_order",
		data:{"pre_or_next":pre_or_next},
		dataType:"json",
		success: function(responce){
			if(responce['error']==0)
			{
				if(pre_or_next == 'next')
				{
					var old_last_next_orderID = get_cookie('last_next_orderID');
					if(old_last_next_orderID == responce["last_next_orderID"])
					{
						_show_msg("提示：当前已经是最后一页！");
						return false;
					}
					else
					{
						set_cookie("order_list_param",responce["order_list_param"]);
					}
				}
				//记录当前列表显示数据的 订单ID
				set_cookie("last_next_orderID",responce["last_next_orderID"]);
				_jump_actual_order(pre_or_next,auto_call,true);
			}
			else
			{
				$.messager.alert('错误',responce["message"],'error');
			}
		}
	});
}

/**
* 实际跳转页面
* @param pre_or_next: pre上一条 next下一条
* @param turn_page： 可选参数 是否为翻页 true是 false不是 默认false
**/
function _jump_actual_order(pre_or_next,auto_call)
{
	//翻页
	var turn_page = false;
	if(arguments[2])
	{
		turn_page = arguments[2];
	}

	var last_next_orderID = get_cookie("last_next_orderID");
	last_next_orderID = last_next_orderID.split(",");

	if( !last_next_orderID || !last_next_orderID.length )
	{
		_show_msg("提示：数据不存在！")
		return false;
	}

	var order_id = 0;
	if(pre_or_next == 'pre')
	{
		if(turn_page)
		{
			//翻页之后，需要上一页的 最后一条
			var stand = parseInt(last_next_orderID.length)-1;
			order_id  = last_next_orderID[stand];
		}
		else
		{
			order_id  = last_next_orderID[parseInt(row_index)-2];
		}
	}
	else if ( pre_or_next == 'next')
	{
		if(turn_page)
		{
			//翻页之后，需要下一页的第一条
			order_id = last_next_orderID[0];
		}
		else
		{
			order_id = last_next_orderID[parseInt(row_index)];
		}
	}

	if(order_id == 0 || order_id == 'undefined')
	{
		_show_msg("提示：数据不存在！")
		return false;
	}

	//外呼
	if(auto_call)
	{
		auto_call = 1;
	}
	else
	{
		auto_call = 0;
	}
	//订单信息	 - 订单受理
	window.parent.addTab('订单受理','index.php?c=order&m=order_accept&system_pagination=1&system_autocall='+auto_call+'&order_id='+order_id,'menu_icon');
}

//取得通话id为了取得录音
var rec_callid = 0;
function get_callid()
{
	//判断当前是否是通话状态，如果是将客户和该通话绑定。
	if(parent.wincall&&parent.wincall.fn_getParam('CallId'))
	{
		rec_callid = parent.wincall.fn_getParam('CallId');
	}
	else
	{
		setTimeout('get_callid()',5000);
	}
}

/*客户详情*/
function _client_detail()
{
	var cle_id = $("#cle_id").val();
	if( cle_id.length!=0 && cle_id>0 )
	{
		window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&cle_id='+cle_id,'menu_icon');
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
	$("#_order_msg").html(msg);
	setTimeout(function(){
		$("#_order_msg").html("");
	},3000);
}

//记录自动保存的选项
function record_memrot_cookie()
{
	//自动跳转
	if( $("#save_next").attr("checked") == "checked")
	{
		set_cookie("est_save_order_next",1,10);
	}
	else
	{
		del_cookie("est_save_order_next");
	}
}


/*点击选择订单状态*/
var order_state_arr = []; //订单状态数组
//返回order_state的在数组中的序号
function _get_order_state_index(order_state)
{
	if(order_state_arr.length == 0)
	{
		$('#mainNav span').each(function(){
			order_state_arr.push($(this).attr('title'));
		});
	}
	var order_state_index = -1;
	$.each(order_state_arr, function(index,value) {
		if(value == order_state)
		{
			order_state_index = index;
		}
	});
	return order_state_index;
}

//改变订单状态
function change_order_state_step(order_state)
{
	if(order_state == '')
	return;

	var total_step = $('#mainNav span').length;
	var last_step_index = total_step - 1;
	var order_state_index = _get_order_state_index(order_state);
	if(order_state_index == -1)
	return;

	var old_order_state = $('#order_state').val();
	var old_order_state_index = _get_order_state_index(old_order_state);

	if(old_order_state_index > order_state_index)
	{
		_show_msg('提示：订单当前状态为 '+old_order_state+'，当前操作退阶！')
	}

	//去除所有class[ done  lastDone  current   mainNavNoBg   ]
	$('#mainNav span').removeClass();
	//初始化最后一项设置
	$('#mainNav span:last').addClass("mainNavNoBg");

	//把选中项设置为 current
	$("#order_state").val(order_state);
	if( order_state_index == last_step_index )//最后一项
	{
		$('#mainNav span:eq('+order_state_index+')').addClass("mainNavNoBg current");
	}
	else
	{
		$('#mainNav span:eq('+order_state_index+')').addClass("current");
	}

	//当前选中项向前设置
	$('#mainNav span:lt('+order_state_index+')').addClass("done");
	if(order_state_index >=1)
	{
		$('#mainNav span:eq('+(order_state_index - 1)+')').attr('class','lastDone')
	}
}