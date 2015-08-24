<?php /* Smarty version 2.6.19, created on 2015-08-13 16:06:28
         compiled from client_history.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'></div>
<div id="client_list"></div>
<div id="datagrid_client"></div>

<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	$('#_search_panel').panel({
		href:'index.php?c=client_history&m=base_search_history',
		onLoad:function(width, height){
			$('#client_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});

	//设置列表
	$('#client_list').datagrid({
		title:"客户历史信息列表",
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		autoRowHeight:false,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'cle_id',
		sortOrder:'desc',//降序排列
		idField:'cle_id',
		url:"index.php?c=client_history&m=get_client_history_list",
		queryParams:{},
		frozenColumns:[[
		{title:'操作',field:'opter_txt',width:50,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='###' onclick=client_view('"+rowData.cle_id+"'); title='查看'>【查看】</a>";
		}}
		]],
		columns:[[
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['cle_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['key']['show'] = true;
$this->_sections['key']['max'] = $this->_sections['key']['loop'];
$this->_sections['key']['step'] = 1;
$this->_sections['key']['start'] = $this->_sections['key']['step'] > 0 ? 0 : $this->_sections['key']['loop']-1;
if ($this->_sections['key']['show']) {
    $this->_sections['key']['total'] = $this->_sections['key']['loop'];
    if ($this->_sections['key']['total'] == 0)
        $this->_sections['key']['show'] = false;
} else
    $this->_sections['key']['total'] = 0;
if ($this->_sections['key']['show']):

            for ($this->_sections['key']['index'] = $this->_sections['key']['start'], $this->_sections['key']['iteration'] = 1;
                 $this->_sections['key']['iteration'] <= $this->_sections['key']['total'];
                 $this->_sections['key']['index'] += $this->_sections['key']['step'], $this->_sections['key']['iteration']++):
$this->_sections['key']['rownum'] = $this->_sections['key']['iteration'];
$this->_sections['key']['index_prev'] = $this->_sections['key']['index'] - $this->_sections['key']['step'];
$this->_sections['key']['index_next'] = $this->_sections['key']['index'] + $this->_sections['key']['step'];
$this->_sections['key']['first']      = ($this->_sections['key']['iteration'] == 1);
$this->_sections['key']['last']       = ($this->_sections['key']['iteration'] == $this->_sections['key']['total']);
?>
		{title: "<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields']; ?>
' ,align:"CENTER",width:"<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone2' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone3'): ?>
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return show_real+"&nbsp;&nbsp;<a href='javascript:;' onclick='sys_dial_num(\""+value+"\")' title='呼叫'><img src='./image/phone.png' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms(\""+value+"\")' title='发短信'><img src='./image/message.png'  /></a><?php endif; ?>";
			}
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:"cle_id" ,field:"cle_id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#client_list').datagrid('clearSelections');
			$('#client_list').datagrid('clearChecked');
		},
		toolbar:[
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				datagrid_client_window();
			}
		},'-'
		]
	});
	var pager = $('#client_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

//显示列表设置
function datagrid_client_window()
{
	$('#datagrid_client').window({
		title: '显示列表设置',
		href:"index.php?c=datagrid_confirm&display_type=0",
		iconCls: "icon-edit",
		top:10,
		width:360,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

//基本搜索
function base_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client_history&m=base_search_history');
}

//高级搜索
function advanced_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=advance_search&flag=history');
}

function client_view(cle_id)
{
	window.parent.addTab('查看历史客户','index.php?c=client_history&m=get_client_base_page&only_page=1&cle_id='+cle_id,'menu_icon');
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>