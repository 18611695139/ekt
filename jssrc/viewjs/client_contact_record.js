$(document).ready(function(){
	$('#contact_record').datagrid({
		nowrap: true,
		striped: true,
		collapsible:false,
		pagination:true,
		rownumbers:true,
		singleSelect:true,
		fitColumns:true,
		checkOnSelect:false,
		pageList:[10],
		sortName:'con_rec_time',
		sortOrder:'desc',
		idField:'con_rec_id',
		url:'index.php?c=contact_record&m=contact_record_query',
		queryParams:{"cle_id":global_cle_id,"order_id":global_order_id},
		frozenColumns:[[
		{title:'con_rec_id',field:'con_rec_id',hidden:true},
		{title: '录音', field: 'callid', width:50,align:'center',sortable:false,formatter:function(value,rowData,rowIndex){
			if( value>0 && rowData.conn_secs != '00:00:00')
			{
				var val = "<img src='./image/play_icon.gif' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' onclick=listen_record("+value+") title='听录音' />&nbsp;";
				if(power_download_record==1)
				{
					val += "<a href='javascript:void(0);' onclick=fn_download("+value+") title='下载该录音' ><img src='./image/disk.png'  border='0' height='16' width='16' align='absmiddle'/></a>";
				}
				return val;
			}
			else
			return '';
		}}
		]],
		columns:[[
		{title:'通话类型',field:'call_type',width:60,align:'center',sortable:true,formatter:function(value,rowData,rowIndex){
			if(value=='呼入')
			{
				return "<img src='./themes/default/icons/redo.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
			else if(value=='呼出' || value=='自动外呼')
			{
				return "<img src='./themes/default/icons/undo.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
			else if(value=='')
			{
				return '';
			}
			else
			{
				return "<img src='./themes/default/icons/next.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
		}},
		{title:'联系内容',field:'con_rec_content',width:220,sortable:true,formatter:function(value,rowData,rowIndex){
			return "<a href='javascript:;' title='点击查看详情' onclick=$('#contact_record_content').text('"+value+"');$('#contact_record_content').window('open');><span class='show-tooltip'>"+value+"</span></a>";
		}},
		{title:'通话时长',field:'conn_secs',width:60,sortable:true},
		{title:'联系时间',field:'con_rec_time',width:120,sortable:true},
		{title:'下次联系时间',field:'con_rec_next_time',width:80,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value != "0000-00-00") return value
		}},
		{title:'处理人',field:'user_name',width:100}
		]],
		onLoadSuccess: function(){
			$('#contact_record').datagrid('clearSelections');
			$('.show-tooltip').tooltip({
				trackMouse:true,
				position:'right',
				onShow: function(){
					var content = '<div style="width:220px;">'+$(this).text().replace(/\n/g, "<br>")+'</div>';
					$(this).tooltip('update',content);
				}
			});
		}
	});

	var pager = $('#contact_record').datagrid('getPager');
	$(pager).pagination({showPageList:false});
});

//听录音，下载录音  type:listen
var _play_panel_init = false;
function listen_record(callid)
{
	if(!callid || callid == 0)
	{
		$.messager.alert("提示","<br>缺少录音参数！");
		return;
	}

	//窗口未打开
	if(!_play_panel_init)
	{
		$('#listen_record_panel').window({
			href:"index.php?c=callrecords&m=record_player&callid="+callid,
			width:625,
			title:"听录音",
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			onOpen:function(){
				_play_panel_init = true;
			},
			onClose:function()
			{
				fn_stop_player();
			}
		});
	}
	else
	{
		$('#listen_record_panel').window('open');
		fn_listen(callid);
	}
}