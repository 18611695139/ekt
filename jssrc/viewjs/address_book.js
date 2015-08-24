$(document).ready(function() {
	//设置列表
	$('#address_book_table').datagrid({
		title:'通讯录列表',
		height:get_list_height_fit_window('searchForm'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		fitColumns:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'tx_id',
		sortOrder:'desc',//降序排列
		idField:'tx_id',
		url:'index.php?c=address_book&m=address_book_query',
		queryParams:{'sql_type':'11'},
		columns:[[
		{title:'tx_id',field:'tx_id',hidden:true},
		{title:'操作',field:'opter_txt',width:80,formatter:function(value,rowData,rowIndex){
			if(power_company_address_book!=0)
			{
				return "<span><a href='javascript:;' onclick=book_edit('"+rowData.tx_id+"') title='编辑'><img src='./image/icon_edit.gif' border='0' height='16' width='16' align='absmiddle'/></a>&nbsp;&nbsp<a href='javascript:;' onclick=book_remove('"+rowData.tx_id+"') title='删除'><img src='./image/icon_drop.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>";
			}
			else
			{
				if(rowData.tx_executor==user_id)
				{
					return "<span><a href='javascript:;' onclick=book_edit('"+rowData.tx_id+"') title='编辑'><img src='./image/icon_edit.gif' border='0' height='16' width='16' align='absmiddle'/></a>&nbsp;&nbsp<a href='javascript:;' onclick=book_remove('"+rowData.tx_id+"') title='删除'><img src='./image/icon_drop.gif' border='0' height='16' width='16' align='absmiddle'/></a></span>";
				}
			}

		}},
		{title:'名称',field:'tx_name',width:120,sortable:true},
		{title:'电话1',field:'tx_phone1',width:130,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var tx_phone1_text = "<a href='javascript:;' onclick=sys_dial_num('"+value+"'); title='呼叫'>"+value+"&nbsp;&nbsp;<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a>";
				if(power_sendsms!=0)
				{
					tx_phone1_text += "&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms("+value+")' title='发短信'><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /> </a>";
				}
				return tx_phone1_text;

			}
			else return ''
		}},
		{title:'电话2',field:'tx_phone2',width:130,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var tx_phone2_text = "<a href='javascript:;' onclick=sys_dial_num('"+value+"'); title='呼叫'>"+value+"&nbsp;&nbsp;<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a>";
				if(power_sendsms!=0)
				{
					tx_phone2_text += "&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms("+value+")' title='发短信'><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /> </a>";
				}
				return tx_phone2_text;
			}
			else return ''
		}},
		{title:'备注',field:'tx_remark',width:300,sortable:true},
		{title:'所属',field:'tx_executor',width:100,sortable:true, formatter:function(value,rowData,rowIndex){
			if( value == -1)
			{
				return "<span   style='color:blue;'>公司通讯录</span>"
			}
			else
			{
				return "<span  >我的通讯录</span>"
			}
		}}
		]]
	});
	var pager = $('#address_book_table').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

//添加自己的通信录
function add_own_address_book()
{
	$('#add_book').window({
		href:"index.php?c=address_book&m=add_address_book&type=2",
		width:600,
		top:150,
		title:"添加通讯录",
		iconCls:'icon-text',
		minimizable:false,
		maximizable:false,
		resizable:true,
		cache:false,
		modal:true
	});
}

//添加公司通讯录
function add_company_address_book()
{
	$('#add_book').window({
		href:"index.php?c=address_book&m=add_address_book&type=1",
		width:600,
		top:150,
		title:"添加公司通讯录",
		iconCls:'icon-text',
		minimizable:false,
		maximizable:false,
		resizable:true,
		cache:false,
		modal:true
	});
}

//删除一条通讯录
function book_remove(tx_id)
{
	$.messager.confirm('提示', '删除该条通讯录？', function(r){
		if(r){
			$.ajax({
				type:'POST',
				url: "index.php?c=address_book&m=delete_address_book",
				data: {"tx_id":tx_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#address_book_table').datagrid('load');
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


//编辑
function book_edit(tx_id)
{
	$('#add_book').window({
		href:"index.php?c=address_book&m=edit_address_book&tx_id="+tx_id,
		width:600,
		top:150,
		title:"编辑",
		iconCls:'icon-text',
		minimizable:false,
		maximizable:false,
		resizable:true,
		cache:false,
		modal:true
	});
}


//搜索
var sql_type = "11";//数据分类
function quick_search(value,name)
{
	var queryParams = $('#address_book_table').datagrid('options').queryParams;
	queryParams.search_key = value;
	queryParams.sql_type = sql_type;
	$('#address_book_table').datagrid('reload');

}