<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:10
         compiled from announcement_list.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'announcement_list.htm', 15, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id="_search_panel" name='_search_panel'>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
  <table>
  <tbody>
	 <tr><td>标题：</td><td><input type="text" name="search_key" id="search_key" size="25" class="easyui-searchbox" searcher="quick_search" prompt="输入公告标题" onclick="if( $(this).val() == '输入公告标题' ) $(this).val('');" /></td></tr>
	 </tbody></table>
  </form>
</div>
<div id="add_ans"></div>
<div id="announcement_list"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/viewjs/announcement_list.js?1.1" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var user_id = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
";
$(document).ready(function() {
	//设置列表
	$('#announcement_list').datagrid({
		title:'公告列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		fitColumns:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'anns_id',
		sortOrder:'desc',//降序排列
		idField:'anns_id',
		url:'index.php?c=announcement&m=announcement_list',
		frozenColumns:[[
		{title:'anns_id',field:'anns_id',hidden:true},
		<?php if ($this->_tpl_vars['power_announcement_change']): ?>
		{field:'ck',checkbox:true},
		{title:'操作',field:'oper_txt',width:50,formatter:function(value,rowData,rowIndex){
			var tmp_element = rowData.create_user_id;
			if( user_id == tmp_element )
			return "&nbsp;&nbsp;<a href='javascript:;' onclick=_update('"+rowData.anns_id+"') title='编辑公告'><img src='./image/icon_edit.gif' border='0' height='16' width='16' align='absmiddle'/></a>";
			else
			return "";
		}},
		<?php endif; ?>
		{title:'标题',field:'anns_title',width:350,sortable:true,align:"left",formatter:function(value,rowData,rowIndex){
			return "<a href='javascript:;' onclick=anns_view("+rowData.anns_id+") class='underline' title='查看公告' >&nbsp;&nbsp;&nbsp;"+value+"</a>";
		}}
		]],
		columns:[[
		{title:'公告范围',field:'dept_name',width:100,sortable:true,align:"center"},
		{title:'发布人',field:'create_user_name',width:100,sortable:true,align:"center"},
		{title:'发布时间',field:'creat_time',width:150,sortable:true,align:"center"}
		]],
		onLoadSuccess: function(){
			$('#announcement_list').datagrid('clearSelections');
			$('#announcement_list').datagrid('clearChecked');
		}
		<?php if ($this->_tpl_vars['power_announcement_change']): ?>
		,toolbar:[
		{
			iconCls:'icon-add',
			text:'添加公告',
			handler:function()
			{
				add_anns();
			}
		},'-',
		{
			text:'删除公告',
			iconCls:'icon-del',
			handler:function()
			{
				delete_anns();
			}
		}]
		<?php endif; ?>
	});
	var pager = $('#announcement_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});
</script>