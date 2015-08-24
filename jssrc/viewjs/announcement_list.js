/**
*列表得到选中的id
*/
function getSelections()
{
	var ids = [];
	var rows = $('#announcement_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++)
	{
		//坐席只能删除自己创建的数据
		if( user_id == rows[i].create_user_id )
		{
			ids.push(rows[i].anns_id);
		}
	}
	return ids;
}

//修改
function _update(anns_id)
{
	window.location.href="index.php?c=announcement&m=announcement_edit&anns_id="+anns_id;
}

/**
*   快速查找
*/
function quick_search(value,name){

	var title              = $.trim(value);

	var queryParams        = $('#announcement_list').datagrid('options').queryParams;
	queryParams.anns_title = title;
	$('#announcement_list').datagrid('load');
}

/*查看公告*/
function anns_view(anns_id)
{
	window.parent.addTab('查看公告','index.php?c=announcement&m=announcement_view&anns_id='+anns_id,'menu_icon');
}
/*添加*/
function add_anns()
{
	window.location.href="index.php?c=announcement&m=add_announcement";
	return false;
}

/*删除*/
function delete_anns()
{
	var ids = getSelections();
	if(ids == '')
	{
		$.messager.alert('提示','<br>员工只能删除自己创建的公告<br>请选择需要删除的数据！','error');
		return;
	}

	$.messager.defaults.ok = '确认删除';
	$.messager.defaults.cancel = '取消';
	$.messager.confirm('提示', "员工只能删除自己创建的公告<br>将删除<span style='color:red;'>"+ids.length+"</span>条公告<br>是否继续？", function(r){
		if(r){
			ids = ids.join(",");
			$.ajax({
				type:"POST",
				url:'index.php?c=announcement&m=announcement_delete',
				data:{"anns_id":ids},
				dataType:'json',
				success:function (responce){
					if(responce['error']=='0'){
						$('#announcement_list').datagrid('load');
						$('#announcement_list').datagrid('clearSelections');
						$('#announcement_list').datagrid('clearChecked');
					}
					else
					{
						$.messager.alert('错误',responce['message'],'error');
					}
				}
			});
		}
		else
		{
			$('#announcement_list').datagrid('clearSelections');
			$('#announcement_list').datagrid('clearChecked');
			return false;
		}
	});
}