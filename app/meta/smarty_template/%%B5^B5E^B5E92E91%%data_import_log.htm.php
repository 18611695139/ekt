<?php /* Smarty version 2.6.19, created on 2015-07-21 11:09:46
         compiled from data_import_log.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="import_log_list"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	//设置列表
	$('#import_log_list').datagrid({
		title:'<?php if ($this->_tpl_vars['impt_type'] == 1): ?>客户导入日志<?php elseif ($this->_tpl_vars['impt_type'] == 2): ?>产品导入日志<?php endif; ?>',
		nowrap: true,
		striped: true,
		pagination:true,
		pageSize:get_list_rows_cookie(),
		rownumbers:true,
		checkOnSelect:false,
		sortName:'impt_time',
		sortOrder:'desc',//降序排列
		idField:'impt_id',
		url:'index.php?c=data_import&m=import_log_query<?php if ($this->_tpl_vars['impt_type']): ?>&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
<?php endif; ?>',
		queryParams:{<?php if ($this->_tpl_vars['import_id']): ?>"impt_id":"<?php echo $this->_tpl_vars['import_id']; ?>
"<?php endif; ?>},
		frozenColumns:[[
		{title:'批次号',field:'impt_id',width:80,sortable:true,align:"center"},
		{title:'导入总数',field:'impt_total',width:150,sortable:true,align:"center"},
		{title:'状态',field:'impt_state',width:150,sortable:true,align:"center",formatter:function(value,rowData,rowIndex){
			if(value == 1)
			{
				return "成功";
			}
			else
			{
				return "<span style='color:red;'>失败</span>";
			}
		}}
		]],
		columns:[[
		{title:'成功数',field:'impt_success',width:150,sortable:true,align:"center",formatter:function(value,rowData,rowIndex){
			if( value > 0 )
			{
				return value+"&nbsp;&nbsp;<a href='###' onclick=delete_success_data('"+rowData.impt_id+"','"+rowData.impt_type+"'); title='删除本次导入数据'>【删除】</a>";
			}
			else
			{
				return value;
			}
		}},
		{title:'失败数',field:'impt_fail',width:150,sortable:true,align:"center",formatter:function(value,rowData,rowIndex){
			if( value > 0 || rowData.impt_state == 0)
			{
				return value+"&nbsp;&nbsp;<a href='###' onclick=delete_failure_data('"+rowData.impt_id+"','"+rowData.impt_type+"'); title='删除本次导入失败数据'>【删除】</a>&nbsp;&nbsp;<?php if ($this->_tpl_vars['impt_type'] != 2): ?><a href='###' onclick=export_failure_data('"+rowData.impt_id+"'); title='导出本次导入失败数据'>【导出】</a><?php endif; ?>";
			}
			else
			{
				return value;
			}
		}},
		{title:'导入时间',field:'impt_time',width:150,sortable:true,align:"center"},
		{title:'操作人',field:'user_name',width:150,sortable:true,align:"center"}
		]],
		onLoadSuccess: function(){
			$('#import_log_list').datagrid('clearSelections');
		}
	});
	var pager = $('#import_log_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

function delete_failure_data(impt_id,impt_type)
{
	if( !impt_id || !impt_type)
	{
		$.messager.alert("提示","<br>缺少参数，执行失败！","info");
		return false;
	}

	$.messager.confirm("删除数据","<br>删除导入失败的数据！",function(r){
		if(r)
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=data_import&m=delete_failure_data",
				data: {"impt_id":impt_id,"impt_type":impt_type},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#import_log_list').datagrid('load');
					}
					else
					{
						$.messager.alert('错误',"<br>"+responce["message"],'error');
					}
				}
			});
		}
	});
}

//删除导入的数据 - 从客户表中删除导入成功的数据
function delete_success_data(impt_id,impt_type)
{
	if( !impt_id )
	{
		$.messager.alert("提示","<br>缺少参数，执行失败！","info");
		return false;
	}

	$.messager.confirm("删除数据","<br>删除导入成功的数据<br>是否继续？",function(r){
		if(r)
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=data_import&m=delete_success_data",
				data: {"impt_id":impt_id,"impt_type":impt_type},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#import_log_list').datagrid('load');
					}
					else
					{
						$.messager.alert('错误',"<br>"+responce["message"],'error');
					}
				}
			});
		}
	});
}

//导出该批次中，导入失败的数据
function export_failure_data(impt_id)
{
	if( !impt_id )
	{
		$.messager.alert("提示","<br>缺少参数，执行失败！","info");
		return false;
	}

	location.href = "index.php?c=data_import&m=export_failure_data&impt_id="+impt_id;
}
</script>