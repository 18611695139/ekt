<?php /* Smarty version 2.6.19, created on 2015-08-07 10:48:50
         compiled from statistics_missed_calls.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id="_search_panel">
  <form action="javascript:quick_search()" method="POST" name="searchFormsearchForm" id="searchForm">
     <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    日期  <span class="combo datebox" style="width: 98px;"><input type="text" class="combo-text validatebox-text" id='start_date' name='start_date' value="<?php echo $this->_tpl_vars['today_date']; ?>
" style="width: 75px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'start_date' })" ></span></span></span> ~  <span class="combo datebox" style="width: 98px;"><input type="text" class="combo-text validatebox-text" id='end_date' name='end_date' value="<?php echo $this->_tpl_vars['today_date']; ?>
"  style="width: 75px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'end_date' })" ></span></span></span>
   <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
   <span style="color:red;">注：默认显示当天的数据</span>
  </form>
</div>

<div id="missed_calls_statistic"></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/fusionCharts.js" type="text/javascript"></script>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	$('#missed_calls_statistic').datagrid({
		title:'未接来电统计列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		rownumbers:true,
		checkOnSelect:false,
		remoteSort:false,
		pagination:true,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		fitColumns:true,
		url:'index.php?c=statistics&m=get_statistics_missed_calls_query',
		queryParams:{'start_date':'<?php echo $this->_tpl_vars['today_date']; ?>
','end_date':'<?php echo $this->_tpl_vars['today_date']; ?>
'},
		frozenColumns:[[
		]],
		columns:[[
		{title:'来电日期',field:'lost_date',align:"CENTER",width:120,sortable:true},
		{title:'未接来电数',field:'missed_num',align:"CENTER",width:120,sortable:true},
		{title:'已分配数',field:'fen_num',align:"CENTER",width:120,sortable:true},
		{title:'未分配数',field:'weifen_num',align:"CENTER",width:120,formatter:function(value,rowData,rowIndex){
			return rowData.missed_num - rowData.fen_num;
		}},
		{title:'已处理数',field:'deal_num',align:"CENTER",width:120,sortable:true},
		{title:'未处理数',field:'undeal_num',align:"CENTER",width:120,formatter:function(value,rowData,rowIndex){
			return rowData.missed_num - rowData.deal_num;
		}}
		]]
	});
	var pager = $('#missed_calls_statistic').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/**
*   检索
*/
function quick_search()
{
	var start_date = $("#start_date").val();
	var end_date = $("#end_date").val();

	var queryParams = $('#missed_calls_statistic').datagrid('options').queryParams;
	queryParams.start_date = start_date;
	queryParams.end_date = end_date;
	$('#missed_calls_statistic').datagrid('load');
}
</script>
