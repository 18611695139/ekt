<?php /* Smarty version 2.6.19, created on 2015-08-07 10:48:52
         compiled from statistics_call_repeat.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'>
    <form action="javascript:quick_search()" name="searchForm" id="searchForm" class="form-inline">
        <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH"/>
        日期
        <div class="input-append">
            <input type="text" name="start_time" id="start_time" value="<?php echo $this->_tpl_vars['today_start']; ?>
" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'start_time',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div>
        ~
        <div class="input-append">
            <input type="text" name="end_time" id="end_time" value="<?php echo $this->_tpl_vars['today_end']; ?>
" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'end_time',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div>
        &nbsp;&nbsp;
        通话类型
        <select name="cdr_call_type" id="cdr_call_type" style="width:100px;">
        	<option value="">--请选择--</option>
        	<?php $_from = $this->_tpl_vars['cdr_call_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['type']):
?>
        	<option value="<?php echo $this->_tpl_vars['key']; ?>
"  <?php if ($this->_tpl_vars['key'] == 2): ?> SELECTED <?php endif; ?>   ><?php echo $this->_tpl_vars['type']; ?>
</option>
        	<?php endforeach; endif; unset($_from); ?>
    	</select>
        <button class="btn btn-primary" onclick="$('#searchForm').submit();">
            <span class="glyphicon glyphicon-search"></span> 搜索
        </button>
        <button type="button" class="btn btn-info" onclick="export_csv()">
            <span class="glyphicon glyphicon-export"></span> 导出CSV
        </button>
        <span style="color:red;">注：默认显示当天数据</span>
    </form>
</div>
<div id='repetition_list'></div>
<div id='show_sta_detail'></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function () {
	$('#repetition_list').datagrid({
		title: '通话重复号码统计列表',
		nowrap: true,
		striped: true,
		collapsible: true,
		pagination: true,
		rownumbers: true,
		fitColumns:true,
		sortName: 'cus_phone',
		sortOrder: 'desc',//降序排列
		idField: 'id',
		url: 'index.php?c=statistics_call_repeat&m=get_call_repeat_info',
		queryParams:{call_type:'2',start_time:'<?php echo $this->_tpl_vars['today_start']; ?>
',end_time:'<?php echo $this->_tpl_vars['today_end']; ?>
'},
		frozenColumns: [[
		]],
		columns: [[
		{title: '号码',field:'cus_phone',width:120,sortable: true,align:'center'},
		{title: '通话类型',field:'call_type',width:160,align:'center'},
		{title: '拨打次数', field: 'num', width: 100, align: 'center'},
		{title: '接通数', field: 'through', width: 100, align: 'center'},
		{title: '比例',field: 'ratio',width: 100, align:'center'}
		]]
	});
});


/**
*   快速查找
*/

function quick_search() {
	var start_time = $('#start_time').val();
	var end_time = $('#end_time').val();
	var cdr_call_type = $('#cdr_call_type').val();
	var queryParams = $('#repetition_list').datagrid('options').queryParams;
	queryParams.call_type = cdr_call_type;
	queryParams.start_time = start_time;
	queryParams.end_time = end_time;
	$('#repetition_list').datagrid('reload');
}

/**
* 导出
*/
function export_csv()
{
	//列表参数
	var datagrid_param = $('#repetition_list').datagrid('options');
	var queryParams    = datagrid_param.queryParams;
	//排序
	queryParams.sortName  = datagrid_param.sortName;
	queryParams.sortOrder = datagrid_param.sortOrder;
	queryParams.total     = $('#repetition_list').datagrid('getData').total;
	var sql_condition  = json2url(queryParams);
	if( sql_condition )
	{
		sql_condition = "&"+sql_condition;
	}
	location.href = 'index.php?c=statistics_call_repeat&m=export_repeat'+sql_condition;
}

</script>