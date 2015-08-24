$(document).ready(function() {

	//设置 收件箱 列表
	$('#list_table1').datagrid({
		title:'收件箱-收到的消息',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		fitColumns:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'msg_show_time',
		sortOrder:'desc',//降序排列
		idField:'msg_id',
		url:'index.php?c=message&m=get_message_query',
		queryParams:{"data_type":"2","msg_type":"-1"},   //data_type：2默认显示未读消息    msg_type:-1收件箱
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'msg_id',field:'msg_id',hidden:true,align:'center'}
		]],
		columns:[[
		{title:'信息内容',field:'msg_content',width:300,sortable:true,align:'left',formatter:function(value,rowData,rowIndex){
			if(value)
			return "<a href='###' onclick=_view("+rowData.msg_id+",'-1',"+rowData.msg_send_user_id+",'"+rowData.msg_send_user_name+"') class='underline' title='查看信息' >"+value+"</a>";
		}},
		{title:'发信人',field:'msg_send_user_name',width:120,sortable:true,align:'center',formatter:function(value,rowData,rowIndex){
			if(value){
				return value;
			}else{
				return "系统";
			}
		}},
		{title:'发送时间',field:'msg_insert_time',width:150,sortable:true,align:'center'},
		{title:'状态',field:'msg_if_readed',width:100,sortable:true,align:'center',formatter:function(value,rowData,rowIndex){
			if( value == '0') return "<span style='color:red;'>"+'未读'+"</span>";
			if( value == '1') return "<span style='color:blue;'>"+'已读'+"</span>";
		}}
		]],
		onLoadSuccess: function(){
			$('#list_table1').datagrid('clearSelections');
			$('#list_table1').datagrid('clearChecked');
		},
		toolbar:[{
			text:'删除消息',
			iconCls:'icon-del',
			handler:function(){
				var ids = shoujian_getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','<br>请选中要删除的数据！','error');
					return;
				}
				$.messager.confirm('提示', '<br>您确定要删除这些数据么？', function(r){
					if(r){
						$.ajax({
							type:"POST",
							url:'index.php?c=message&m=delete_message',
							data:{"msg_id":ids},
							dataType:'json',
							success:function (responce){
								if(responce['error']=='0'){
									$('#list_table1').datagrid('load');
									$('#list_table1').datagrid('clearSelections');
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
		},'-',
		{
			text:'标记为已读',
			iconCls:'icon-ok',
			handler:function(){
				batch_read();
			}
		}
		]
	});
	//收件箱  end-----------------

	var pager = $('#list_table1').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/**
*收件箱 列表得到选中的id
*/
function shoujian_getSelections()
{
	var ids = [];
	var rows = $('#list_table1').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].msg_id);
	}
	return ids.join(',');
}

function shoujian_make_getSelections()
{
	var no_read_ids = [];
	var rows = $('#list_table1').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		if(rows[i].msg_if_readed==0)
		{
			no_read_ids.push(rows[i].msg_id);
		}
	}
	return no_read_ids.join(',');
}

/**
*收件箱 关键字搜索
**/
var sql_type = "2";//数据分类
function quick_search(value,name)
{

	var queryParams = $('#list_table1').datagrid('options').queryParams;
	queryParams.search_key = value;
	queryParams.data_type  = sql_type;
	$('#list_table1').datagrid('load');
}

/**
*  收件箱 查看
*/
function _view(msg_id,kind,send_user_id,send_user_name){
	$('#list_table1').datagrid('clearSelections');//去除所有选中标志
	$.ajax({
		type: 'post',
		url: 'index.php?c=message&m=message_view',
		data: {'kind':kind,'msg_id':msg_id},   //  kind=-2:发件箱      kind=-1：收件箱
		dataType: 'json',
		success: function(responce)
		{
			var record_content = responce['content'];
			if( kind == '-1' )  //更改收件箱消息 状态
			{
				if(power_sendxx==1)
				{
					record_content = record_content+"&nbsp;&nbsp;<span class='underline' onclick='msg_replay("+send_user_id+")'>【回复】</span>";
				}
				else
				{
					record_content = record_content+"&nbsp;&nbsp;";
				}
				$('#window').css('display','block');
				$('#window').window({
					title: '详细信息',
					top:100,
					closed: false,
					collapsible:false,
					minimizable:false,
					onBeforeOpen: function(){
						$('#SR_content').html(record_content);
					}
				});

				var row = $("#list_table1").datagrid('getSelected');//获得当前选中项的内容
				row.msg_if_readed = '1';  //已读

				var index = $("#list_table1").datagrid('getRowIndex',row);

				$("#list_table1").datagrid('refreshRow',index);
			}
		}
	});
}

//消息回复
function msg_replay(reply_user_id)
{

	$('#message_panel').window({
		href:'index.php?c=message&m=message_panel&selected_id='+reply_user_id,
		width:490,
		height:390,
		title:'发消息',
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		shadow:false,
		cache:false
	});
}

function quick_send_box()//显示发件箱
{
	est_header("Location:index.php?c=message&m=send_box");
}

//批量标记为已读
function batch_read(){
	var no_read_ids = shoujian_make_getSelections();
	if(no_read_ids == '')
	{
		$.messager.alert('提示','<br>请选中要标记的未读数据！','error');
		return;
	}
	$.messager.confirm('提示', '<br>您确定要标记这些信息为已读吗？', function(r){
		if(r){
			$.ajax({
				type:"POST",
				url:'index.php?c=message&m=update_read',
				data:{"msg_id":no_read_ids},
				dataType:'json',
				success:function (responce){
					if(responce['error']=='0'){
						$('#list_table1').datagrid('load');
						$('#list_table1').datagrid('clearSelections');
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