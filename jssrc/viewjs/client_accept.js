//version 1.2
var original_data = {};//原始数据
$(document).ready(function(){
	//隐藏号码
	if(power_phone_view==0)
	{
		if(power_update_c_phone==1)
		{
			$('#cle_phone').val(hidden_part_number($('#cle_phone').val()));
			if(client_phone2.length!=0)
			{
				$('#cle_phone2').val(hidden_part_number($('#cle_phone2').val()));
			}
			if(client_phone3.length!=0)
			{
				$('#cle_phone3').val(hidden_part_number($('#cle_phone3').val()));
			}
		}
		else
		{
			$('#label_cle_phone').html(hidden_part_number($('#label_cle_phone').html()));
			if(client_phone2.length!=0)
			{
				$('#label_cle_phone2').html(hidden_part_number($('#label_cle_phone2').html()));
			}
			if(client_phone3.length!=0)
			{
				$('#label_cle_phone3').html(hidden_part_number($('#label_cle_phone3').html()));
			}
		}
	}

	//自动保存并取下一条(自动跳转)
	if(get_cookie("est_save_next") == 1)
	{
		$("#save_next").attr("checked",true)
	}
	if(power_cle_stat==1)
	{
		//自动放弃放弃空号错号数据 自动放弃
		if(get_cookie("est_release_invalid_data") == 1)
		{
			$("#release_invalid_data").attr("checked",true)
		}
		//自动保存数据(号码状态选为[未呼通]、[空号错号]时自动保存)
		if(get_cookie("est_save_data") == 1)
		{
			$("#save_data").attr("checked",true)
		}

		//号码状态点击效果
		$('input[type="radio"][name="cle_stat"]').click(function(){
			$(this).siblings('span').css('font-weight','');
			$(this).next().css('font-weight','bold');
		});
	}

	//得到通话ID
	get_callid();

	//  每一页的第一条时，更改为“上一页”
	if(parseInt(row_index) <= 1)
	{
		$('#last_client').html('<span class="glyphicon glyphicon-fast-backward"></span> 上一页');
	}

	// 每一页的最后一条时，更改为“下一页”
	if( parseInt(row_index) >= parseInt(row_limit))
	{
		$('#next_client').html('<span class="glyphicon glyphicon-fast-forward"></span> 下一页');
	}

	//自动呼叫
	if(system_autocall==1)
	{
		_autocall(system_autocall_number);
	}

	//初始化客户阶段
	var init_stage = $('#cle_stage').val();
	change_stage_step(init_stage);

	//记录原始数据
	original_data = get_current_value();

	if(power_use_contact!=1)
	{
		/*显示联系人电话*/
		change_contact_mobile();
	}
});


/**
* 自动呼叫客户，当客户电话为空时，呼叫联系人。
* @param phone_num 需要外呼的号码 默认为传过来的号码，一般是客户电话
**/
function _autocall(phone_num)
{
	var cle_id = $("#cle_id").val();
	if(get_cookie("last_cle_id") != cle_id)
	{
		set_cookie("last_cle_id",cle_id);
	}
	else
	{
		_show_msg("<b>您刚和这个客户联系了。</b>",'error');
		return;
	}
	if(power_use_contact!=1)
	{
		//如果客户电话为空，则呼叫联系人电话
		if( phone_num == "")
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=contact&m=get_contact_phone",
				data:{"cle_id":cle_id},
				dataType:"json",
				success: function(responce){
					if(responce['error']=='0')
					{
						phone_num = responce["content"];
						if(phone_num == "")
						{
							_show_msg("提示：这个客户没有联系号码！")
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
						_show_msg('获取联系人电话失败','error');
					}
				}
			});
		}
	}
	sys_dial_num(phone_num);
}

/**
* 放弃当前客户数据 - 只能放弃属于自己的数据(数据所属人为当前坐席)
* @param pre_or_next （可选参数） 放弃以后取数据 默认next
*
**/
function release_client()
{
	if(!power_client_release)
	{
		_show_msg('提示：您没有放弃这条数据的权限！');
		return false;
	}
	$.messager.confirm("数据放弃确认","<br>您确认要放弃这个客户吗？",function (r){
		if(r)
		{
			_release_client_data('next');
		}
		else
		{
			return false;
		}
	});
}

/**
* 放弃客户数据
* @param pre_or_next 放弃后跳转上一条或者下一条 pre 上一条 next 下一条
* @param auto_call 跳转页面时自动呼叫
**/
function _release_client_data(pre_or_next)
{
	var auto_call = false;
	if(arguments[1])
	{
		auto_call = arguments[1];
	}
	if(!power_client_release)
	{
		_pre_or_next_client(pre_or_next,auto_call);
		return false;
	}

	var cle_id = $('#cle_id').val();
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=release_client_by_id",
		data: {"cle_id":cle_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				if(system_pagination)
				{
					if(pre_or_next == 'next')
					{
						_show_msg('提示：已放弃当前数据,系统将自动取下一条客户数据！')
					}
					else if(pre_or_next == 'pre')
					{
						_show_msg('提示：已放弃当前数据,系统将自动取上一条客户数据！')
					}
					else
					{
						_show_msg('提示：已放弃当前数据！');
						return;
					}
					setTimeout(function(){
						_pre_or_next_client(pre_or_next,auto_call);
					},1500);
				}
				else
				{
					_show_msg('提示：已放弃当前数据！')
				}
			}
			else
			{
				_show_msg(responce["message"],'error')
			}
		}
	});
}

//记录自动保存的选项
function record_memrot_cookie()
{
	//自动跳转
	if( $("#save_next").attr("checked") == "checked")
	{
		set_cookie("est_save_next",1,10);
	}
	else
	{
		del_cookie("est_save_next");
	}

	//放弃无效数据
	if( $("#release_invalid_data").attr("checked") == "checked")
	{
		set_cookie("est_release_invalid_data",1,10);
	}
	else
	{
		del_cookie("est_release_invalid_data");
	}

	//无效 未呼通 空号错号 自动保存
	if($('#save_data').attr('checked') == 'checked')
	{
		set_cookie('est_save_data',1,10);
	}
	else
	{
		del_cookie('est_save_data');
	}
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

/**
* 关键字搜索
*/
function quick_search()
{
	var search_name  = $("#search_cle_name").val();
	var search_phone = $("#search_cle_phone").val();
	if(search_name.length != 0)
	{
		est_header("Location:index.php?c=client&m=search_client&name="+search_name);
	}
	if(search_phone.length >= 4)
	{
		est_header('Location:index.php?c=client&m=search_client&phone='+search_phone);
	}
}

/**
*保存客户数据（保存客户数据前会判断客户数据状态
*@param do_next (可选参数)  pre 取前一条数据 next 取下一条数据
*@return bool
**/
function save_client()
{
	if(!power_client_update)//没有保存权限
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

	var  _cle_stat =  $("input:radio[name='cle_stat']:checked").val(); //状态
	if(  _cle_stat == '未拨打' && rec_callid != 0 )
	{
		$.messager.confirm('提示', '该客户已联系，号码状态仍为 [未拨打] <br>是否继续保存？', function(r){
			if(r)
			{
				_save_client_data(pre_or_next);
			}
		});
	}
	else
	{
		_save_client_data(pre_or_next);
	}
}

//保存数据
function _save_client_data(pre_or_next)
{
	var auto_call = false;
	//如果下一步为空，则看保存数据后是否默认取下一条并且呼叫
	if(pre_or_next == '' && ($("#save_next").attr("checked") == "checked"))
	{
		pre_or_next = 'next';
		if(config_call_type!=1)
		{
			auto_call = true;
		}
	}

	$('#save_client').attr('disabled',true);

	//得到当前数据
	var current_data = get_current_value();
	//数据比较，得到修改过的数据
	var changed_data = data_comparison(original_data,current_data);
	changed_data.cle_id         = $("#cle_id").val();
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=update_client",
		data:changed_data,
		dataType:"json",
		success: function(responce){
			$('#save_client').attr('disabled',false);
			if(responce['error']=='0')
			{
				_show_msg("保存成功",'yes');
				if(power_cle_stat==1)//若启用号码状态
				{
					//保存时，放弃“空号错号”的数据
					var new_cle_stat = $("input:radio[name='cle_stat']:checked").val();
					if(($("#release_invalid_data").attr("checked") == "checked") && (new_cle_stat == "空号错号"))
					{
						_release_client_data(pre_or_next,auto_call);
						return true;
					}
				}

				switch(pre_or_next)
				{
					case 'pre': setTimeout(function(){_pre_or_next_client('pre')},1000);break;
					case 'next': setTimeout(function(){_pre_or_next_client('next',auto_call)},1000);break;
					default :
					original_data = get_current_value();//客户数据重新赋值
					break;
				}
			}
			else
			{
				$.messager.alert('保存客户失败',"<br>"+responce['message'],'error');
			}
		}
	});
}

/**
*返回所有的客户的信息
*@return obj
**/
function get_current_value()
{
	var _data = {};
	_data.cle_name          = $("#cle_name").val(); //客户名称
	//客户电话
	if(power_update_c_phone)
	{
		_data.cle_phone         = $("#cle_phone").val();
		if(!power_phone_view && exist_star(_data.cle_phone))
		{
			_data.cle_phone = client_phone;
		}
	}
	//可配置的基本字段 cle_info_source cle_address cle_remark cle_province_id cle_city_id cle_stat cle_stage
	$("[base_field='true']:input").each(function(){
		if($(this).attr('name')=='cle_stat')
		{
			_data.cle_stat = $("input:radio[name='cle_stat']:checked").val(); //号码状态
		}
		else
		{
			_data[$(this).attr('id')] = $(this).val();
			if(($(this).attr('id') == 'cle_phone2' || $(this).attr('id') == 'cle_phone3')&&exist_star($(this).val()))
			{
				if($(this).attr('id') == 'cle_phone2')
				_data[$(this).attr('id')] = client_phone2;
				else
				_data[$(this).attr('id')] = client_phone3;
			}
		}
	});
	if(_data.cle_province_id!=0)
	{
		_data.cle_province_name = $("#cle_province_id").find("option:selected").text();//省
	}
	else
	{
		_data.cle_province_name = '';
	}
	if(_data.cle_city_id!=0)
	{
		_data.cle_city_name = $("#cle_city_id").find("option:selected").text();//市
	}
	else
	{
		_data.cle_city_name = '';
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

//跳到上一个客户
function jump_pre_client()
{
	_check_data_change('pre');
}

/**
*跳到下一个客户
**/
function jump_next_client()
{
	_check_data_change('next');
}

/**
* 检查数据是否改变
* @param pre_or_next 下一步操作
**/
function _check_data_change(pre_or_next)
{
	if(!power_client_update)
	{
		_pre_or_next_client(pre_or_next);
		return false;
	}

	//数据比较，得到修改过的数据
	var current_data = get_current_value();
	var changed_data = data_comparison(original_data,current_data);
	if(!empty_obj(changed_data))
	{
		$.messager.defaults.ok = "保存";
		$.messager.defaults.cancel = "取消";
		$.messager.confirm("数据已改变","<br>是否保存对数据的修改？",function (r){
			if(r)
			{
				save_client(pre_or_next);
			}
			else
			{
				_pre_or_next_client(pre_or_next);
			}
		});
	}
	else
	{
		_pre_or_next_client(pre_or_next);
	}
	return true;
}

/**
* 上一条或者下一条数据
* @param pre_or_next  pre上一条 next下一条
* @param auto_call （可选参数）是否自动呼叫 ture 下一条数据自动外呼 false不自动外呼 默认false
**/
function _pre_or_next_client(pre_or_next)
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
		_trun_page('pre');
	}
	//当前页最后一条
	else if( pre_or_next == 'next' && parseInt(row_index) >= parseInt(row_limit) )
	{
		_trun_page('next',auto_call);
	}
	else
	{
		_jump_actual_client(pre_or_next,auto_call);
	}
}

//翻页  pre_or_next: pre上一页   next下一页
function _trun_page(pre_or_next,auto_call)
{
	if(pre_or_next == 'pre')
	{
		var client_list_param = get_cookie('client_list_param');
		var client_list_param_arr = client_list_param.split(",");
		var page = client_list_param_arr[0];
		if(page <= 1)
		{
			_show_msg("提示：当前已经是第一页啦！");
			return false;
		}
	}
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=trun_page",
		data:{"pre_or_next":pre_or_next},
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				if(pre_or_next == 'next')
				{
					var old_last_next_cleID = get_cookie('last_next_cleID');
					if(old_last_next_cleID == responce["last_next_cleID"])
					{
						_show_msg("提示：当前已经是最后一页啦！");
						return false;
					}
					else
					{
						set_cookie("client_list_param",responce["client_list_param"]);
					}
				}
				//记录当前列表显示数据的 客户ID
				set_cookie("last_next_cleID",responce["last_next_cleID"]);
				_jump_actual_client(pre_or_next,auto_call,true);
			}
			else
			{
				_show_msg(responce["message"],'error');
				//$.messager.alert('错误',responce["message"],'error');
			}
			return true;
		}
	});
	return true;
}

/**
* 实际跳转页面
* @param pre_or_next: pre上一条 next下一条
* @param auto_call: true自动外呼，false否
* @param turn_page： 可选参数 是否为翻页 true是 false不是 默认false
**/
function _jump_actual_client(pre_or_next,auto_call)
{
	//翻页
	var turn_page = false;
	if(arguments[2])
	{
		turn_page = arguments[2];
	}
	var last_next_cleID = get_cookie("last_next_cleID") || '';
	last_next_cleID = last_next_cleID.split(",");

	if( !last_next_cleID || !last_next_cleID.length )
	{
		$.messager.alert("提示","<br>数据不存在！");
		return false;
	}

	var cle_id = 0;
	if(pre_or_next == 'pre')
	{
		if(turn_page)
		{
			//翻页之后，需要上一页的 最后一条
			var stand = parseInt(last_next_cleID.length)-1;
			cle_id  = last_next_cleID[stand];
		}
		else
		{
			cle_id  = last_next_cleID[parseInt(row_index)-2];
		}
	}
	else if ( pre_or_next == 'next')
	{
		if(turn_page)
		{
			//翻页之后，需要下一页的第一条
			cle_id = last_next_cleID[0];
		}
		else
		{
			cle_id = last_next_cleID[parseInt(row_index)];
		}
	}

	if(cle_id == 0 || cle_id == 'undefined')
	{
		$.messager.alert("提示","<br>数据不存在！");
		return false;
	}

	if(auto_call)
	{
		auto_call = 1;
	}
	else
	{
		auto_call = 0;
	}
	window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=1&system_autocall='+auto_call+'&cle_id='+cle_id,'menu_icon');
}

//检测客户姓名是否重复
var _repeat_window_state = 'close';
function check_repeat_data(phone_id)
{
	if(_repeat_window_state == 'open')
	{
		return;
	}
	//客户姓名  名称小于三位时取消检测
	var cle_name = $("#cle_name").val();
	if(cle_name.length <= 3 || cle_name == original_data.cle_name )//
	{
		cle_name = "";
	}

	//客户电话 号码中带*号 取消检测
	var cle_phone = $("#"+phone_id).val();
	if(exist_star(cle_phone) || cle_phone == original_data.cle_phone )
	{
		cle_phone = '';
	}
	var cle_id = $('#cle_id').val();
	if( cle_name || cle_phone )
	{
		$.ajax({
			type:'POST',
			url: "index.php?c=client&m=check_repeat_data",
			data:{"name":cle_name,"phone":cle_phone,"cle_id":cle_id},
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					$('#display_repeat_data').window({
						href:"index.php?c=client&m=display_repeat_data&name="+cle_name+"&phone="+cle_phone+"&cle_id="+cle_id,
						top:50,
						width:800,
						title:"重复性检查 - 数据库里已存在相似或重复的记录(多于10条时，只显示前10条)",
						collapsible:false,
						minimizable:false,
						maximizable:false,
						resizable:false,
						cache:false,
						onOpen:function(){
							_repeat_window_state = 'open';
						},
						onClose:function(){
							_repeat_window_state = 'close';
						}
					});
					if(!phone_allow_repeat)/* 不允许重复 */
					{
						$('#save_client').attr('disabled',true);
					}
				}
			}
		});
	}
}

//自动保存
function auto_save(state)
{
	if(($('#save_data').attr('checked') == 'checked') && ( state == '未呼通' || state == '空号错号'))
	{
		save_client();
	}
}

/*点击选择阶段*/
var stage_arr = []; //客户阶段数组
//返回stage的在数组中的序号
function _get_stage_index(stage)
{
	if(stage_arr.length == 0)
	{
		$('#mainNav span').each(function(){
			stage_arr.push($(this).attr('title'));
		});
	}
	var stage_index = -1;
	$.each(stage_arr, function(index,value) {
		if(value == stage)
		{
			stage_index = index;
		}
	});
	return stage_index;
}
//改变客户阶段
function change_stage_step(stage)
{
	if(stage == '')
	return;

	var total_step = $('#mainNav span').length;
	var last_step_index = total_step - 1;
	var stage_index = _get_stage_index(stage);

	if(stage_index == -1)
	return;

	var old_cle_stage = local_cle_stage;
	var old_stage_index = _get_stage_index(old_cle_stage);

	if(old_stage_index > stage_index)
	{
		_show_msg('提示：当前操作为退阶！')
	}

	//去除所有class[ done  lastDone  current   mainNavNoBg   ]
	$('#mainNav span').removeClass();
	//初始化最后一项设置
	$('#mainNav span:last').addClass("mainNavNoBg");

	//把选中项设置为 current
	$("#cle_stage").val(stage);
	if( stage_index == last_step_index )//最后一项
	{
		$('#mainNav span:eq('+stage_index+')').addClass("mainNavNoBg current");
	}
	else
	{
		$('#mainNav span:eq('+stage_index+')').addClass("current");
	}

	//当前选中项向前设置
	$('#mainNav span:lt('+stage_index+')').addClass("done");
	if(stage_index >=1)
	{
		$('#mainNav span:eq('+(stage_index - 1)+')').attr('class','lastDone')
	}
}


/*联系人*/
function change_contact_mobile()
{
	var con_mobile = $('#cle_contact_name').val();
	if(!con_mobile)
	{
		return;
	}
	if(!power_phone_view)
	{
		con_mobile = hidden_part_number(con_mobile);
	}
	$('#cle_con_mobile').html(con_mobile);
	if(con_mobile.length!=0)
	$("#_cle_con_sms_dial").css("display",'');
	else
	$("#_cle_con_sms_dial").css("display",'none');

}

/*过往联系记录详情*/
function show_contact_record_detail()
{
	var cle_id = $('#cle_id').val();
	$('#contact_record_detail_window').window({
		title: '过往联系记录-查看',
		href:"index.php?c=contact_record&m=contact_record_detail&cle_id="+cle_id,
		top:100,
		width:560,
		height:450,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

//号码验证
function number_verification(phone_id,msg_id)
{
	var cle_phone = $('#'+phone_id).val();
	// ^[0-9]*[1-9][0-9]*$  正整数  _phone_update_msg
	r = /^[0-9]*[1-9][0-9]*$/;
	if(cle_phone.length!=0)
	{
		if(r.test(cle_phone))
		{
			var msg = '';
		}
		else
		{
			var msg = "<img src='./image/important.gif' border='0' height='16' width='16' align='absmiddle' />&nbsp;<b>错误输入，号码必须是由数字组成的</b>";
		}

		$('#'+msg_id).html(msg);
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
	$("#_accept_msg").html(msg);
	setTimeout(function(){
		$("#_accept_msg").html("");
	},2000);
}