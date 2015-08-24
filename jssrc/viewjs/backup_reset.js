$(document).ready(function(){
	//设置列表
	$('#backup_list').datagrid({
		title:"备份文件",
		nowrap: true,
		height:get_list_height_fit_window('_search_panel'),
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'backup_datetime',
		sortOrder:'desc',//降序排列
		idField:'backup_id',
		url:"index.php?c=backup_reset&m=backup_list_query",
		frozenColumns:[[
		]],
		columns:[[
		{title: "操作" ,field: 'opter_txt' ,align:"CENTER",width:"150",sortable:true,formatter:function(value,rowData,rowIndex){
			var opter_txt = '';
			if(power_reset!=0)
			{
				opter_txt += "<a href='###' onclick=reset_database('"+rowData.backup_id+"','"+rowData.backup_datetime+"')>【还原】</a>";
			}
			opter_txt += "<a href='###' onclick=backup_delete('"+rowData.backup_id+"')>【删除】</a>";
			return opter_txt;
		}},
		{title: "备份时间" ,field: 'backup_datetime' ,align:"CENTER",width:"150",sortable:true},
		{title: "备份人" ,field: 'backup_create_user_name' ,align:"CENTER",width:"130",sortable:true},
		{title: "备份文件名" ,field: 'backup_file_name' ,align:"CENTER",width:"180",sortable:true},
		{title:"backup_id" ,field:"backup_id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#backup_list').datagrid('clearSelections');
		}
	});
	
	var pager = $('#backup_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/*数据备份*/
function client_data_backup()
{
	var backup_client = $('#backup_client').val();
	if(backup_client.length!=0)
	{
		$.ajax({
			type:'POST',
			url: "index.php?c=backup_reset&m=data_backup",
			data: {"backup_name":backup_client},
			dataType: "json",
			success: function(responce){
				if(responce["error"] == 0)
				{
					$.messager.alert("提示","<br>备份成功","info");
					$('#backup_list').datagrid('load');
				}
				else
				{
					$.messager.alert('错误','备份失败','error');
				}
			}
		});
	}
	else
	{
		$.messager.alert('错误','备份文件名不能为空','info');
	}
}

/*列表 - 还原*/
function reset_database(backup_id,backup_datetime)
{
	$.messager.confirm('提示', '还原后'+backup_datetime+'之后的所有录入的数据都没有了，确定要还原？', function(r){
		if(r)
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=backup_reset&m=reset_database",
				data: {"backup_id":backup_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$.messager.alert("提示","<br>还原成功","info");
					}
					else
					{
						$.messager.alert('错误','还原失败','error');

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

/*删除*/
function backup_delete(backup_id)
{
	$.messager.confirm('提示', '您确认要删除该条备份？', function(r){
		if(r)
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=backup_reset&m=backup_delete",
				data: {"backup_id":backup_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$.messager.alert("提示","<br>删除备份成功","info");
						$('#backup_list').datagrid('load');
					}
					else
					{
						$.messager.alert('错误','删除备份失败','error');

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