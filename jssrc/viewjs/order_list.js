var editIndex = undefined;
function endEditing(){
	if (editIndex == undefined){return true}
	if ($('#order_list').datagrid('validateRow', editIndex))
	{
		var ed = $('#order_list').datagrid('getEditor', {index:editIndex,field:'order_state'});
		if(ed)
		{
			var order_state = $(ed.target).combobox('getValue');
			$('#order_list').datagrid('getRows')[editIndex]['order_state'] = order_state;
		}
		$('#order_list').datagrid('endEdit', editIndex);
		editIndex = undefined;
		return true;
	}
	else
	return false;
}

//列表直接修改【订单状态】、【配送编号】
function change_order_state_or_delivery_number(rowIndex,o_state,o_state_original,o_delivery_number,o_delivery_number_original,o_id,o_num)
{

	var message = '';
	var result = {};
	if(o_state != o_state_original && o_delivery_number == o_delivery_number_original)
	{
		message = '【订单状态】';
		result = {'order_id':o_id,'order_state':o_state};
	}
	if(o_state == o_state_original && o_delivery_number != o_delivery_number_original)
	{
		message = '【配送单号】';
		result = {'order_id':o_id,'order_delivery_number':o_delivery_number};
	}
	if(o_state != o_state_original && o_delivery_number != o_delivery_number_original)
	{
		message = '【订单状态】和【配送单号】';
		result = {'order_id':o_id,'order_delivery_number':o_delivery_number,'order_state':o_state};
	}
	if(o_state != o_state_original || o_delivery_number != o_delivery_number_original)
	{
		var change_data = json2str(result);
		$.ajax({
			type:'POST',
			url: "index.php?c=order&m=update_order_by_list",
			data:{'change_data':change_data},
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					$('#order_list').datagrid('updateRow',{
						index:rowIndex,
						row: {
							order_state_original:o_state,
							order_delivery_number:o_delivery_number
						}
					});
					$.messager.show({
						title:'提示',
						msg:"订单"+o_num+"的"+message+"已成功修改"
					});
				}
				else
				{
					$.messager.alert('错误',responce['message'],'error');
				}
			}
		});
	}
}

//列表设置
function datagrid_list_display_window()
{
	$('#datagrid_list_display').window({
		title: '显示列表设置',
		href:"index.php?c=datagrid_confirm&display_type=6",
		iconCls: "icon-seting",
		top:10,
		width:360,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

/*删除*/
function delete_order()
{
	var order_ids = getSelections();
	if(order_ids == '')
	{
		$.messager.alert('提示','<br>请选择要删除的订单','info');
		return;
	}
	$.messager.confirm('提示', '您确定要删除选中订单？', function(r){
		if(r){
			$.ajax({
				type:"POST",
				url:'index.php?c=order&m=delete_order',
				data:{"order_ids":order_ids},
				dataType:'json',
				success:function (responce){
					if(responce['error']=='0')
					{
						$('#order_list').datagrid('load');
					}
					else
					{
						$.messager.alert('执行错误',responce['message'],'error');
					}
				}
			});
		}
		else
		{
			return false;
		}
	});
}

/**
*列表得到选中的id
*/
function getSelections()
{
	var ids = [];
	var rows = $('#order_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].order_id);
	}
	return ids.join(',');
}

//基本搜索
function base_search_order()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=order&m=order_base_search');
}

//高级搜索
function advanced_search_order()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=order&m=order_advance_search');
}

/*订单受理*/
//order ID; type: dial号码跳转  link姓名跳转 phone 电话
function link_order_accept(order_id,type,phone)
{
	$('#order_list').datagrid('cancelEdit', editIndex);
	$('#order_list').datagrid('getRows')[editIndex]
	write_cookie_order_id();
	if(type=='link')
	{
		window.parent.addTab('订单受理','index.php?c=order&m=order_accept&system_pagination=1&order_id='+order_id,'menu_icon');
	}
	else if(type=='dail')
	{
		window.parent.addTab('订单受理','index.php?c=order&m=order_accept&system_pagination=1&system_autocall=1&system_autocall_number='+phone+'&order_id='+order_id,'menu_icon');
	}
}

//cookie中记录当前订单列表中的 订单ID
function write_cookie_order_id()
{
	var list_option       = $('#order_list').datagrid('options');
	var order_list_param = [];
	order_list_param[0]  = list_option.pageNumber;
	order_list_param[1]  = list_option.pageSize;
	order_list_param[2]  = list_option.sortName;
	order_list_param[3]  = list_option.sortOrder
	order_list_param[4]  = $('#order_list').datagrid('getData').total;
	//列表参数
	set_cookie("order_list_param",order_list_param.join(","));

	var row_data     = $('#order_list').datagrid("getRows");
	var table_order_id = [];
	$.each(row_data,function(key,value){
		table_order_id[key] = value.order_id;

	});
	//记录当前列表显示数据的 订单ID
	set_cookie("last_next_orderID",table_order_id.join(","));


	//当前列表的检索条件
	var queryParams   = list_option.queryParams;
	var param_str = json2str(queryParams);
	set_cookie('accept_order_condition',param_str);
}

/*查看产品详情*/
function show_order_product_detail(product_id)
{
	if(power_view_product == 1)
	{
		$('#order_product_detail_window').window({
			title: '产品详情',
			iconCls:"icon-ok",
			href:"index.php?c=product&m=view_product&product_id="+product_id,
			width:580,
			top:50,
			shadow:true,
			closed: false,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			cache:false
		});
	}
	else
	$.messager.alert('提示','您没有查看产品的权限！','info');
}

/*批量导出*/
function output_order_datch(choose,type)
{
	var ids = getSelections();
	if(ids != '' && choose == 0)
	{
		open_choose_window();
	}
	else if(ids != '' && choose==1)
	{
		//列表参数
		var datagrid_param = $('#order_list').datagrid('options');
		//排序
		var sortName  = datagrid_param.sortName;
		var sortOrder = datagrid_param.sortOrder;
        var total     = $('#order_list').datagrid('getData').total;

        if(type == 'excel' && queryParams.total>10000)
        {
            $.messager.confirm('数据量大，建议用【导出CSV】导出数据', '<br>确定继续导出excel吗？', function(r){
                if(r){
                    window.location.href = 'index.php?c=order&m=output_order&sortName='+sortName+'&sortOrder='+sortOrder+'&order_ids='+ids+'&total='+total+'&export_type='+type;
                }
                else
                {
                    return false;
                }
            });
        } else {
            window.location.href = 'index.php?c=order&m=output_order&sortName='+sortName+'&sortOrder='+sortOrder+'&order_ids='+ids+'&total='+total+'&export_type='+type;
        }
	}
	else
	{
		//列表参数
		var datagrid_param = $('#order_list').datagrid('options');
		var queryParams    = datagrid_param.queryParams;
		//排序
		queryParams.sortName  = datagrid_param.sortName;
		queryParams.sortOrder = datagrid_param.sortOrder;
        queryParams.total     = $('#order_list').datagrid('getData').total;

		var sql_condition  = json2url(queryParams);
		if( sql_condition )
		{
			sql_condition = "&"+sql_condition;
		}

        if(type == 'excel' && queryParams.total>10000)
        {
            $.messager.confirm('数据量大，建议用【导出CSV】导出数据', '<br>确定继续导出excel吗？', function(r){
                if(r){
                    window.location.href = 'index.php?c=order&m=output_order'+sql_condition+'&export_type='+type;
                }
                else
                {
                    return false;
                }
            });
        } else {
            window.location.href = 'index.php?c=order&m=output_order'+sql_condition+'&export_type='+type;
        }
	}
}

//打开选择窗口
function open_choose_window()
{
	$('#output_choose_window').window('open');
}

function click_choose()
{
	var choose_out = $("input:radio[name='choose_out']:checked").val();
	$("#output_choose_window").window("close");
	$("input:radio[name='choose_out']").prop('checked',false);
	output_order_datch(choose_out);
}

/*批量修改*/
function chose_change_more_window()
{
	var ids = getSelections();
	var states = $('#order_list_state').val();

	$("#change_more_window").window("close");/*批量修改*/

	if(states != '' && ids != '')
	{
		$.ajax({
			type:"POST",
			url:'index.php?c=order&m=batch_update_order_state',
			data:{"order_ids":ids,'order_state':states},
			dataType:'json',
			success:function (responce){
				if(responce['error']=='0'){
					$('#order_list').datagrid('load');
				}
				else
				{
					$.messager.alert('执行错误',responce['message'],'error');
				}
			}
		});
	}
}

/*客户详情*/
function client_exist(cle_id)
{
	$.ajax({
		type:"POST",
		url:'index.php?c=client&m=client_exist',
		data:{"cle_id":cle_id},
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0'){
				window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&cle_id='+cle_id,'menu_icon');
			}
			else
			{
				$.messager.alert('错误',responce['message'],'error');
			}
		}
	});
}