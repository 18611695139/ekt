<?php /* Smarty version 2.6.19, created on 2015-08-17 11:48:33
         compiled from monitor_queue_agent.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="user_list"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
    var timeFly       = "";//定时器
$(document).ready(function() {
    $('#user_list').datagrid({
		height:get_list_height_fit_window('_search_panel')-50,
		nowrap: true,
		striped: true,
		rownumbers:true,
		fitColumns:true,
		remoteSort:false,
		sortName:'user_last_login',
		sortOrder:'desc',//降序排列
		idField:'user_id',
		loadMsg:'',
		url:'index.php?c=monitor&m=queue_agent_list&que_id=<?php echo $this->_tpl_vars['que_id']; ?>
',
		frozenColumns:[[
		{field:"user_id",hidden:true},
		{title:'姓名',field:'user_name',width:100,align:'center',sortable:true},
		{title:'工号',field:'user_num',width:100,align:'center',sortable:true},
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

            //计时器->更新数据
            if( timeFly != "" )
            {
                window.clearInterval(timeFly);//清空计时器，停止调用函数
            }
            timeFly = window.setInterval(function(){$('#user_list').datagrid('reload');},5000);
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
</script>