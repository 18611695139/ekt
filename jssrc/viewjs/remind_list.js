$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
	//提醒管理列表
	$('#remind_list').datagrid({
		title:'提醒管理',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		fitColumns:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'rmd_time',
		sortOrder:'ASC',//降序排列
		idField:'rmd_id',
		url:'index.php?c=remind&m=remind_list_query',
		queryParams:{"sql_type":"5"},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'rmd_id',field:'rmd_id',hidden:true},
		{title:'提醒内容',field:'rmd_remark',width:350,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value)
			return "<a href='javascript:;' onclick=view_remind('"+rowData.rmd_id+"') class='underline' title='详情' >"+value+"</a>";
		}}
		]],
		columns:[[
		{title:'提醒类型',field:'rmd_param_char',width:160,sortable:true,formatter:function(value,rowData,rowIndex){
			if( rowData.rmd_type == 1 ||rowData.rmd_type == 2)
			{
				if(rowData.rmd_deal==0)
				return "<a href='###' onclick=send_remind_list('"+rowData.rmd_id+"','"+rowData.rmd_type+"','"+rowData.rmd_param_int+"') title='立即处理'>"+value+"</a>";
				else
				{
					if(rowData.rmd_type == 1)/*客户相关提醒*/
					return "<a href='###' onclick=\"window.parent.addTab('业务受理','index.php?c=client&m=accept&cle_id="+rowData.rmd_param_int+"','menu_icon');\" title='已处理，查看客户详情'>"+value+"</a>";
					else if(rowData.rmd_type == 2)/*订单相关提醒*/
					return "<a href='###' onclick=\"window.parent.addTab('订单受理','index.php?c=order&m=order_accept&order_id="+rowData.rmd_param_int+"','menu_icon');\" title='已处理，查看订单详情'>"+value+"</a>";
				}
			}
			else
			return value;
		}},
		{title:'状态',field:'rmd_deal',width:80,sortable:true,align:"center",formatter:function(value,rowData,rowIndex){
			if(value =='1')  return "已处理";
			if(value =='0')
			return "未处理 "+"<a href='javascript:;' onclick=send_deal('"+rowData.rmd_id+"') title='标记为已处理'><img src='./image/icon_deal.png' align='absmiddle' border='0' height='16' width='16' /></a>";
		}},
		{title:'是否短息提醒',field:'rmd_sendsms',width:80,sortable:true,align:"center",formatter:function(value,rowData,rowIndex){
			if( value == 1 ) return "是";
			else if( value ==  0 ) return "否";
		}},
		{title:'提醒时间',field:'rmd_time',align:"center",width:150,sortable:true}
		]],
		onLoadSuccess: function(){
			$('#remind_list').datagrid('clearSelections');
			$('#remind_list').datagrid('clearChecked');
		},
		toolbar:[
		{
			iconCls:'icon-add',
			text:'添加提醒',
			handler:function(){
				$('#set_remind').window({
					href:"index.php?c=remind&m=new_remind&rmd_param_char="+encodeURIComponent("我的提醒"),
					width:520,
					title:"加入提醒",
					collapsible:false,
					minimizable:false,
					maximizable:false,
					resizable:false,
					cache:false
				});
			}
		},'-',
		{
			text:'批量标记为已处理',
			iconCls:'icon-edit',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','请选中要处理的数据！','error');
					return;
				}
				$.messager.confirm('提示', '您确定要处理选中数据么？', function(r){
					if(r){
						$.ajax({
							type:"POST",
							url:'index.php?c=remind&m=mak_remind_deal',
							data:{"rmd_id":ids},
							dataType:'json',
							success:function (responce){
								if(responce['error']=='0'){
									$('#remind_list').datagrid('load');
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
			text:'批量删除',
			iconCls:'icon-del',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','请选中要删除的数据！','error');
					return;
				}
				remove_remind(ids);
			}
		}]
	});
	
	var pager = $('#remind_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

//查看并处理提醒窗口
function view_remind(rmd_id)
{
	$('#view_remind').window({
		title: '我的提醒',
		href:"index.php?c=remind&m=view_remind_data&rmd_id="+rmd_id,
		iconCls: "icon-search",
		top:100,
		closed: false,
		width:550,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}
//删除提醒
function remove_remind(rmd_ids){

	$.messager.confirm('提示', '<br>您确定要删除选中数据？', function(r){
		if(r){
			$.ajax({
				type:'POST',
				url: "index.php?c=remind&m=delete_remind",
				data: {"rmd_ids":rmd_ids},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#remind_list').datagrid('load');
					}
					else
					{
						$.messager.alert('错误',responce["message"],'error');
					}
				}
			});
		}else{
			return false;
		}
	});
}
//处理提醒（标记为已处理）
function send_deal(rmd_id){
	$.ajax({
		type:'POST',
		url: "index.php?c=remind&m=mak_remind_deal",
		data: {"rmd_id":rmd_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$('#remind_list').datagrid('load');
			}
			else
			{
				$.messager.alert('错误',responce["message"],'error');
			}
		}
	});
}
// 快速查找
var sql_type = "5";//数据分类  默认未处理
function quick_search(){
	var start_time = $('#start_time').val();
	var end_time = $('#end_time').val();
	if( (start_time > end_time)&& start_time &&end_time )
	{
		$.messager.alert("提示","<br>起始时间不能大于结束时间！","info");
		return ;
	}
	var queryParams = $('#remind_list').datagrid('options').queryParams;
	queryParams.rmd_time_start   = start_time;
	queryParams.rmd_time_end     = end_time;
	queryParams.sql_type = sql_type;
	$('#remind_list').datagrid('load');
}
/**
*列表得到选中的id
*/
function getSelections()
{
	var ids = [];
	var rows = $('#remind_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].rmd_id);
	}
	return ids.join(',');
}

//立即处理
function send_remind_list(rmd_id,rmd_type,cle_or_order_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=remind&m=mak_remind_deal",
		data: {"rmd_id":rmd_id,"action_type":"dealRigntNow"},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				if(rmd_type == 1)/*客户相关提醒*/
				window.parent.addTab('业务受理','index.php?c=client&m=accept&cle_id='+cle_or_order_id,'menu_icon');
				else if(rmd_type == 2)/*订单相关提醒*/
				window.parent.addTab('订单受理','index.php?c=order&m=order_accept&order_id='+cle_or_order_id,'menu_icon');
			}
			else
			{
				$.messager.alert("警告",responce["message"],"warning");
			}
		}
	});
}