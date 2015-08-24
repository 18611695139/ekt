$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
	var gl_all_data = 0;
	if(power_callrecord==1)
	{
		gl_all_data = 1;
	}
	$('#user_tree').combobox({
		url:'index.php?c=user&m=get_user_box&gl_all_data='+gl_all_data,
		valueField:'user_id',
		textField:'user_name_num',
		hasDownArrow:false,
		mode:'remote'
	});

	$('#call_recod_list').datagrid({
		title:'通话记录',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'start_time',
		sortOrder:'desc',
		idField:'id',
		url:'index.php?c=callrecords&m=callrecords_query',
		queryParams:{"date_type":"2","start_date":start_date,"end_date":today_end},
		frozenColumns:[[
		{title:'id',field:'id',hidden:true},
		{title: '录音', field: 'callid', width:120,align:'center',formatter:function(value,rowData,rowIndex){
			if( value>0 && rowData.conn_secs != '00:00:00' && rowData.call_type != '(被)咨询')
			{
				var val = '<span><a href="javascript:void(0);" onclick="fn_listen('+value+','+rowData.user_id+')" title="收听该录音" ><img src="./image/play_icon.gif" border="0" height="16" width="16" align="absmiddle"/></a></span>';
				if(power_download_record==1)
				{
					val += '<span><a href="javascript:void(0);" onclick="fn_download('+value+','+rowData.user_id+')" title="下载该录音" ><img src="./image/disk.png"  border="0" height="16" width="16" align="absmiddle"/></a></span>';
				}
				return val;
			}
			return '';
		}},
		{title:'通话类型',field:'call_type',width:100,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value=='呼入')
			{
				return "<img src='./themes/default/icons/redo.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
			else if(value=='呼出')
			{
				return "<img src='./themes/default/icons/undo.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
			else
			{
				return "<img src='./themes/default/icons/next.png' border='0' height='16' width='16' style='cursor: pointer;' align='absmiddle' title='"+value+"' />"+value;
			}
		}},
		{title:'客户名称',field:'cle_name',width:150,sortable:true,formatter:function(value,rowData,rowIndex){
			if( value )
			{
				return "<a href='javascript:;' onclick=client_exist('"+rowData.cle_id+"','','link');  title='受理'>"+value+"</a>";
			}
			else
			return '';
		}},
		{title:'客户电话',field:'cus_phone',width:150,sortable:true,formatter:function(value,rowData,rowIndex){
			if( value )
			{
				var show_phone = value;
				if(power_phone_view==0)
				show_phone = hidden_part_number(value);

				var cus_phone_text = "<a href='javascript:;' onclick=client_exist('"+rowData.cle_id+"','"+value+"','link');  title='受理'>"+show_phone+"</a>&nbsp;&nbsp;<a href='javascript:;' onclick=client_exist('"+rowData.cle_id+"','"+value+"','call');  title='呼叫'><img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a>&nbsp;&nbsp;";
				if(message_authority==1)
				{
					cus_phone_text += "<a href='javascript:;' onclick=sys_send_sms("+value+"); title='短信'><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /> </a>";
				}
				return cus_phone_text;
			}
			else
			return '';
		}}
		]],
		columns:[[
		{title:'开始时间',field:'start_time',width:150,sortable:true},
		{title:'通话时长',field:'conn_secs',width:150,sortable:true},
		{title:'挂断原因',field:'endresult',width:150},
		{title:'所属坐席',field:'user_name',width:100}
		]],
		onLoadSuccess: function(){
			$('#call_recod_list').datagrid('clearSelections');
		}
	});

	var pager = $('#call_recod_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

var sql_type = "2";//数据分类
function quick_search()
{
	var search_key  = $("#search_key").val();//电话
	var start_date  = $("#start_date").val();//开始时间
	var end_date    = $("#end_date").val();//结束时间
	var min_conn_secs = $("#min_conn_secs").val();//通话时长

	$('#call_recod_list').datagrid('options').queryParams = {};
	var queryParams = $('#call_recod_list').datagrid('options').queryParams;
	queryParams.date_type = sql_type;
	queryParams.cus_phone = search_key;//电话
	queryParams.start_date = start_date;//开始时间
	queryParams.end_date   = end_date;//结束时间
	queryParams.conn_secs   = min_conn_secs;//通话时长
	if((power_callrecord==1||role_type!=2) && $('#user_tree').combobox("getValue").length!=0)
	{
		var user_id    = $('#user_tree').combobox("getValue");
		queryParams.user_id     = user_id;
	}

	$('#call_recod_list').datagrid('load');
}

/*业务受理*/
function client_exist(cle_id,phone,type)
{
	if(type=='call')
	{
		sys_dial_num(phone);
	}
	$.ajax({
		type:"POST",
		url:'index.php?c=client&m=client_exist',
		data:{"cle_id":cle_id},
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0'){
				// 电话号码跳转 - 业务受理
				window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&system_autocall=0&cle_id='+cle_id,'menu_icon');
			}
			else
			{
				window.parent.addTab('业务受理','index.php?c=client&m=search_client&phone='+phone,'menu_icon');
			}
		}
	});
}