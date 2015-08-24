$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});

	//设置列表
	$('#sms_list').datagrid({
		title:'短信列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		rownumbers:true,
		sortOrder:'desc',
		sortName:'sms_send_time',
		idField:'sms_id',
		url:'index.php?c=sms&m=list_sms_query',
		queryParams:{"sql_type_st":'3'},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'sms_id',field:'sms_id',hidden:true,align:'center'},
		{title:'发信人',field:'user_name',sortable:true,width:100,align:'center'},
		{title:'接收号码',field:'receiver_phone',sortable:true,width:120,align:'center',formatter:function(value,rowData,rowIndex){
			var show_real = value;
			if(!power_phone_view)
			{
				show_real = hidden_part_number(value);
			}
			return show_real;
		}}
		]],
		columns:[[
		{title:'发送时间',field:'sms_send_time',sortable:true,width:120,align:'center',formatter:function(value,rowData,rowIndex){
			if(value != "0")
			return value;
		}},
		{title:'发送结果',field:'sms_result',sortable:true,width:120,align:'center',formatter:function(value,rowData,rowIndex){
			if(value == '1')
			{
				return '成功';
			}
			else if(value == '0')
			{
				return '已发送<img height="16" style="cursor:pointer" width="16" border="0" align="absmiddle" src="./image/refresh.gif" title="重新发送" onclick="resend_sms('+rowData.sms_id+')">';
			}
			else
			{
				return '失败<img height="16" style="cursor:pointer" width="16" border="0" align="absmiddle" src="./image/refresh.gif" title="重新发送" onclick="resend_sms('+rowData.sms_id+')"';
			}
		}},
		{title:'失败原因',field:'sms_fail_reason',sortable:true,width:200,align:'center'},
		{title:'信息内容',field:'sms_contents',width:300,align:'center',formatter:function(value,rowData,rowIndex){
			if(value)
			return "<a onclick=sms_content("+rowData.sms_id+") title='查看信息'  class='underline'>"+value+"</a>";
		}}
		]],
		onLoadSuccess: function(){
			$('#sms_list').datagrid('clearSelections');
			$('#sms_list').datagrid('clearChecked');
		},
		toolbar:[
		{
			iconCls:'icon-message',
			text:'发短信',
			handler:function()
			{
				sys_send_sms('');
			}
		},'-'],
		onDblClickRow:function (rowIndex, rowData)  //单击 查看
		{
			sms_content(rowData.sms_id);

		}
	});
	var pager = $('#sms_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/**
*重新发送消息
**/
function resend_sms(sms_id)
{
	if(sms_id)
	{
		$.ajax({
			url:'index.php?c=sms&m=resend_sms',
			data:{'sms_id':sms_id},
			type:'POST',
			dataType:'json',
			success:function(responce)
			{
			}
		});

		$.messager.alert('成功','短信发送成功');
		$('#sms_list').datagrid('reload');
	}
}
//查看短信内容
function sms_content(sms_id)
{

	$.ajax({
		type: 'post',
		url: 'index.php?c=sms&m=view_smscontent',
		data: {'sms_id':sms_id},
		dataType: 'json',
		success: function(responce)
		{
			var sms_content = responce['content'];
			if(responce['error'] == 0 )
			{
				$('#window').css('display','block');
				$('#window').window({
					title: '详细信息',
					top:100,
					closed: false,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					onBeforeOpen: function(){
						$('#SR_content').html(sms_content);
						calculation();
					}
				});
			}
			else
			{
				$.messager.alert("错误",responce["message"],"error");
			}
		}
	});
}

var sql_type_st = "3"; //默认：今周
var sql_type_ss = "0"; //  5成功  6失败
var _data = {};
function quick_search(){
	_data = {};
	//收信人
	_data.receiver_phone = $('#receiver_phone').val();
	//发送时间
	_data.send_start_time = $('#send_start_time').val();
	_data.send_end_time = $('#send_end_time').val();

	var queryParams = $('#sms_list').datagrid('options').queryParams;
	//收信人
	queryParams.receiver_phone  = _data.receiver_phone;
	//发送时间
	queryParams.send_start_time = _data.send_start_time;
	queryParams.send_end_time   = _data.send_end_time;

	queryParams.sql_type_st     = sql_type_st;
	queryParams.sql_type_ss     = sql_type_ss;
	$('#sms_list').datagrid('reload');
}


//数据分类处设置颜色
var last_st_cokor = "";
var last_ss_cokor = "";
function set_color_sms(id)
{
	//全部数据
	if( id =='sd0' )
	{
		$('#searchForm>a').css('color','#335b64');
		$('#'+id).css('color','red');
		sql_type_st = "1";
		sql_type_ss = "0";
	}
	else if( id =='st0' ||id =='st1' ||id =='st2')
	{
		if( last_st_cokor )
		{
			$('#'+last_st_cokor).css('color','#335b64');
		}
		$('#'+id).css('color','red');
		$('#sd0').css('color','#335b64'); //全部数据
		last_st_cokor = id;
	}
	//成功、失败
	else if( id =='ss0' ||id =='ss1' )
	{
		if( last_ss_cokor )
		{
			$('#'+last_ss_cokor).css('color','#335b64');
		}
		$('#'+id).css('color','red');
		last_ss_cokor = id;
	}
}

//计算短信条数，字数
function calculation()
{
	var length = $("#SR_content").val().length;

	$("#cal").val(length);//已输入字符数
	if(length <= EACH_smsLength) //小于每条短信规定长度
	{
		$("#SR_tiaoshu").val(Math.ceil( length/EACH_smsLength ));//短信条数
	}
	else
	{
		$("#SR_tiaoshu").val(Math.ceil( length/ ( EACH_smsLength-3) ));//短信条数
	}
}