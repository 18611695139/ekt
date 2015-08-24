//得到选中项的 客户ID
var selected_name = "";
var selected_num  = 0;
function getSelections()
{
	selected_name = "";
	selected_num  = 0;
	var ids = [];
	var rows = $('#client_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++)
	{
		if(i<5){
			selected_name +="【"+ rows[i].cle_name+"】 ";
		}
		ids.push(rows[i].cle_id);
		selected_num++;
	}
	selected_name += "......";
	return ids.join(',');
}

/*搜索内容*/
function search_panel_info()
{
	$('#_search_panel').panel({
		href:'index.php?c=client&m=base_search',
		onLoad:function(width, height){
			$('#client_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});
}

//判断是否能删除客户
function remove_client()
{
	var ids = getSelections();
	if(ids == '')
	{
		$.messager.alert('错误','<br>请选择要删除的客户数据','error');
		return;
	}
	var _relative_deal = '';
	if(delete_client_relative==1)
	{
		_relative_deal = "，其他模块与被删客户相关的数据将保留";
	}
	else if(delete_client_relative==2)
	{
		_relative_deal = "，<span style='color:red;'>其他模块与被删客户相关的数据也一同删除</span>";
	}
	$.messager.confirm('提示', '删除'+selected_num+'条数据'+_relative_deal, function(r){
		if(r)
		{
			//删除客户数据
			delete_client(ids);
		}
		else
		{
			return false;
		}
	});
}

//删除客户数据
function delete_client(cle_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=delete_client",
		data: {"cle_id":cle_id},
		dataType: "json",
		success: function(responce){

			if(responce["error"] == 0)
			{
				$.messager.alert('提示',"<br>"+responce['content'],'info');
				$('#client_list').datagrid('load');
			}
			else
			{
				$.messager.alert('错误','删除失败','error');

			}

		}
	});
}

//判断是否能释放
function if_can_release()
{
	var ids = getSelections();
	if(ids == '')
	{
		$.messager.alert('提示(只能放弃属于自己的数据)','<br>请选择要放弃的客户数据','info');
		return;
	}
	$.messager.confirm('提示(只能放弃属于自己的数据)', '<br>放弃选中数据？', function(r){
		if(r){
			//放弃客户数据 by cle_id
			release_client_by_id(ids);
		}
		else
		{
			return false;
		}
	});
}

//放弃客户数据 - 只能放弃属于自己的数据(数据所属人为当前坐席)
// cle_id:客户ID
function release_client_by_id(cle_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=release_client_by_id",
		data: {"cle_id":cle_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$.messager.alert('提示(只能放弃属于自己的数据)',"<br>已放弃"+responce['content']+"条数据",'info');
				$('#client_list').datagrid('load');
			}
			else
			{
				$.messager.alert('错误','操作失败，提示：只能放弃自己的数据','error');
			}
		}
	});
}

/*客户管理列表 - 业务受理*/
//cookie中记录当前客户列表中的 客户ID ； type: dial号码跳转  link姓名跳转
function link_client_accept(cle_id,type)
{
	var list_option       = $('#client_list').datagrid('options');
	//列表参数
	var client_list_param = [];
	client_list_param[0]  = list_option.pageNumber;
	client_list_param[1]  = list_option.pageSize;
	client_list_param[2]  = list_option.sortName;
	client_list_param[3]  = list_option.sortOrder
	set_cookie("client_list_param",client_list_param.join(","));

	var row_data     = $('#client_list').datagrid("getRows");
	var table_cle_id = [];
	$.each(row_data,function(key,value){
		table_cle_id[key] = value.cle_id;
	});
	//记录当前列表显示数据的 客户ID
	set_cookie("last_next_cleID",table_cle_id.join(","));

	//当前列表的检索条件
	var queryParams   = list_option.queryParams;
	var param_str = json2str(queryParams);
	set_cookie('accept_condition',param_str);

	if( type == 'dial' )
	{
		// 外呼电话电话号码
		var phone = arguments[2];
		sys_dial_num(phone);
	}
	// 跳转 - 业务受理
	window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=1&cle_id='+cle_id,'menu_icon');
}

//基本搜索
function base_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=base_search');
}

//高级搜索
function advanced_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=advance_search');
}

//显示列表设置
function datagrid_client_window()
{
	$('#datagrid_client').window({
		title: '显示列表设置',
		href:"index.php?c=datagrid_confirm&display_type=0",
		iconCls: "icon-edit",
		top:10,
		width:360,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

//导出
function output_client(type)
{
	//列表参数
	var datagrid_param = $('#client_list').datagrid('options');
	var queryParams    = datagrid_param.queryParams;
	//排序
	queryParams.sortName  = datagrid_param.sortName;
	queryParams.sortOrder = datagrid_param.sortOrder;
	queryParams.total     = $('#client_list').datagrid('getData').total;
	var sql_condition  = json2url(queryParams);
	if( sql_condition )
	{
		sql_condition = "&"+sql_condition;
	}
    if(type == 'excel' && queryParams.total>10000)
    {
        $.messager.confirm('数据量大，建议用【导出CSV】导出数据', '<br>确定继续导出excel吗？', function(r){
            if(r){
                window.location.href = 'index.php?c=client&m=data_output&export_type='+type+sql_condition;
            }
            else
            {
                return false;
            }
        });
    } else {
        window.location.href = 'index.php?c=client&m=data_output&export_type='+type+sql_condition;
    }

}