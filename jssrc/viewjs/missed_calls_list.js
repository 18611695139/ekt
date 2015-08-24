function getSelections(){
	var ids  = [];
	var rows = $('#missed_calls_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].id);
	}
	return ids.join(',');
}

/*1:全部数据/2:我的数据*/
var last_deal_color = "";
var last_locate_color = "";
function set_color_all(id)
{
	//全部数据、我的数据
	if( id == "all1" || id == "all2" )
	{
		//清除所有选中
		$('#all1').css('color','#335b64');
		$('#all2').css('color','#335b64');
		$('#deal1').css('color','#335b64');
		$('#deal2').css('color','#335b64');
		$('#locate1').css('color','#335b64');
		$('#locate2').css('color','#335b64');

		/*1未处理  2已处理*/
		deal_type = "";
		/*1已分配   2未分配*/
		locate_type = "";
	}
	else
	{
		/*1未处理  2已处理*/
		if( id == "deal1" || id == "deal2" )
		{
			if(last_deal_color)
			{
				$('#'+last_deal_color).css('color','#335b64');
			}
			last_deal_color = id;
		}

		/*1已分配   2未分配*/
		if( id == "locate1" || id == "locate2")
		{
			if(last_locate_color)
			{
				$('#'+last_locate_color).css('color','#335b64');
			}
			last_locate_color = id;
		}
	}

	//选中变色
	$('#'+id).css('color','red');
}
if(role_type==1 && power_wjld_department)
{
	/*1全部数据  2我的数据*/
	var all_type = '1';
	/*1已分配   2未分配*/
	var locate_type = "2";
}
else
{
	/*1全部数据  2我的数据*/
	var all_type = '2';
	/*1已分配   2未分配*/
	var locate_type = "";
}

if(power_wjld_department)
{
	/*1未处理  2已处理*/
	var deal_type = "";
}
else
{
	/*1未处理  2已处理*/
	var deal_type = "1";
}
function quick_search()
{
	var deal_date_search_start = $("#deal_date_search_start").val();
	var deal_date_search_end = $("#deal_date_search_end").val();

	//来电号
	var search_caller  = $("#search_caller").val();

	var queryParams = $('#missed_calls_list').datagrid('options').queryParams;
	queryParams.deal_date_search_start  = deal_date_search_start;
	queryParams.deal_date_search_end    = deal_date_search_end;
	queryParams.caller           = search_caller;

	/*1全部数据  2我的数据*/
	queryParams.all_type    = all_type;
	/*1未处理  2已处理*/
	queryParams.deal_type   = deal_type;
	/*1已分配   2未分配*/
	queryParams.locate_type = locate_type;

	$('#missed_calls_list').datagrid('load');
}

//处理
function accept(id,phone_num,cle_id,autocall)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=missed_calls&m=change_missed_calls_state",
		data: {"id":id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				if(cle_id > 0)
				{
					if(autocall == 0)
					window.parent.addTab('业务受理','index.php?c=client&m=accept&type=client&cle_id='+cle_id,'menu_icon');
					else
					window.parent.addTab('业务受理','index.php?c=client&m=accept&type=client&system_autocall_number='+phone_num+'&system_autocall=1&cle_id='+cle_id,'menu_icon');

				}
				else
				{
					if(autocall == 1)
					{
						sys_dial_num(phone_num);
					}
					window.parent.addTab('业务受理','index.php?c=client&m=search_client&phone='+phone_num,'menu_icon');
				}
			}
			else
			{
				$.messager.alert('错误',responce["message"],'error');
			}
		}
	});
}

//选中分配
function _missed_calls_window(ids)
{
	$('#missed_calls_window').window({
		title: '未接来电选中分配',
		href:"index.php?c=missed_calls&m=deal_missed_calls&ids="+ids,
		iconCls: "icon-edit",
		top:150,
		width:450,
		height:120,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

/*批量分配*/
function _miss_calls_batch()
{
	var total       = $('#missed_calls_list').datagrid('getData').total;
	if( total == 0 )
	{
		$.messager.alert('提示','<br>缺少未接来电数据！','error');
		return;
	}

	//列表参数
	var datagrid_param    = $('#missed_calls_list').datagrid('options');
	var queryParams       = datagrid_param.queryParams;

	//排序
	queryParams.sortName  = datagrid_param.sortName;
	queryParams.sortOrder = datagrid_param.sortOrder;

	var sql_condition     = json2url(queryParams);
	if( sql_condition )
	{
		sql_condition     = "&"+sql_condition;
	}

	window.parent.addTab('未接来电 - 批量分配','index.php?c=missed_calls&m=missed_deployment_batch&total='+total+sql_condition,'menu_icon');
}