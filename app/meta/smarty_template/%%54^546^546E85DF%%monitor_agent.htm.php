<?php /* Smarty version 2.6.19, created on 2015-08-06 17:25:45
         compiled from monitor_agent.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'monitor_agent.htm', 153, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id='_search_panel'>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm" >
    <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
	关键字：<input type="text" name="search_key_word" id="search_key_word" size="15" />&nbsp;&nbsp;&nbsp;&nbsp;
	部门：<input type='text' id="department" name="department" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;
    <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
  </form>
</div>

<div id="user_list"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
	
	//部门
	$('#department').combotree({
		url:'index.php?c=department&m=get_department_tree',
		editable:true,
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#department').combotree('options').url = "index.php?c=department&m=get_department_tree";
			}
		}
	});
	
	$('#user_list').datagrid({
		height:get_list_height_fit_window('_search_panel')-50,
		nowrap: true,
		striped: true,
		rownumbers:true,
		fitColumns:true,
		pagination:true,
		remoteSort:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'user_last_login',
		sortOrder:'desc',//降序排列
		idField:'user_id',
		loadMsg:'',
		url:'index.php?c=monitor&m=agent_list_query',
		frozenColumns:[[
		{field:"user_id",hidden:true},
		{title:'姓名',field:'user_name',width:100,align:'center',sortable:true},
		{title:'工号',field:'user_num',width:100,align:'center',sortable:true},
		{title:'部门',field:'dept_name',width:120,sortable:true},
		{title:'号码',field:'pho_num',width:100,align:'center',sortable:true}
		]],
		columns:[[
		{title:'状态',field:'status',width:80,align:'center',sortable:true,formatter:function(value,rowData,rowIndex)
		{
			if( value == "通话" )
			{
				return value+"&nbsp;&nbsp;<a href='javascript:;' onclick=monitor_agent_chanspy('"+rowData.user_id+"') title='监听坐席通话'>【监听】</a>";
			}
			else
			{
				return value;
			}
		}},
		{field:"status_secs_int",hidden:true},
		{title:'状态持续时长',field:'status_secs',width:100,align:'center',sortable:true,formatter:function(value,rowData,rowIndex){
			//将时间戳转化为具体的时分秒
			return timeFormate(value);
		}}
		]],
		onLoadSuccess: function (responce){
			//加载坐席监控数据
			update_monitor_data();

			//持续状态计时
			if( time_cumul != "" )
			{
				window.clearInterval(time_cumul);//清空计时器，停止调用函数
			}
			time_cumul = window.setInterval("cumulative_time()",1000);
		}
	});
});

/**
*监听
*/
function monitor_agent_chanspy(user_id)
{
	var logstate = parent.wincall.logState();
	if( !logstate)
	{
		$.messager.alert('错误','坐席未签入','error');
		return;
	}

	//挂断
	//parent.wincall.fn_hangup();
	//监听
	parent.wincall.fn_chanspy(Number(user_id));
}

/**
* 关键字搜索
*/
function quick_search()
{
	var key_word = $("#search_key_word").val();
	var department	= $('#department').combotree('getValue');
	
	var queryParams = $('#user_list').datagrid('options').queryParams;
	queryParams.key_word = key_word;
	queryParams.dept_id = department;
	$('#user_list').datagrid('load');
}

/**
*  状态持续时间累加
*/
var time_cumul = "";
function cumulative_time()
{
	//返回当前页的行
	var rows_data = $('#user_list').datagrid("getRows");
	$.each(rows_data, function(property,value) {
		//针对对象
		if( value.status != "<span style='color:grey'>离线</span>" )
		{
			//状态持续时间int型
			var tmp_time = parseInt(value.status_secs_int);
			tmp_time++;
			value.status_secs_int = tmp_time;
			value.status_secs     = tmp_time;

			$('#user_list').datagrid('updateRow',{
				index: property,
				row: value
			});
		}
	});
}

//加载坐席监控数据
var all_user_data = "";//列表中的初始员工数据
var list_user_id  = "";//列表中的员工ID（多个ID以逗号分隔）
var list_que_id   = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['que_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
";//技能组ID
var timeFly       = "";//定时器
function update_monitor_data()
{
	all_user_data = $("#user_list").datagrid("getRows");
	list_user_id  = [];
	$.each(all_user_data, function(property,value) {
		list_user_id.push(value.user_id);
	});
	list_user_id = list_user_id.join(",");

	//把返回的数据更新至列表中
	get_updateRow();

	//计时器->更新数据
	if( timeFly != "" )
	{
	window.clearInterval(timeFly);//清空计时器，停止调用函数
	}
	timeFly = window.setInterval("get_updateRow()",15000);
}

/**
*  把返回的数据更新至列表中
*/
function get_updateRow()
{
	//获取监控数据
	$.ajax({
		type:'POST',
		url: "index.php?c=monitor&m=get_agent_monitor_data",
		data: {"list_user_id":list_user_id,"que_id":list_que_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				//更新列表数据
				$.each(all_user_data, function(property,value)
				{
					var tmp_user_d    = value.user_id;
					var update_value  = "";
					if( typeof(responce[tmp_user_d]) == "object" && !empty_obj(responce[tmp_user_d]) )
					{
						responce[tmp_user_d].user_name  = value.user_name;
						responce[tmp_user_d].user_num   = value.user_num;
						responce[tmp_user_d].dept_name  = value.dept_name;

						update_value = responce[tmp_user_d];
					}
					else
					{
						value.status = "<span style='color:grey'>离线</span>";
						update_value = value;
					}

					$('#user_list').datagrid('updateRow',{
						index: property,
						row: update_value
					});
				});
			}
			else
			{

			}
		}
	});
}
</script>