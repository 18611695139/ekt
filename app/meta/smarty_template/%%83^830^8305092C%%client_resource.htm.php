<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:34
         compiled from client_resource.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'></div>
<div id="client_list"></div>
<div id="datagrid_resource"></div>
<div id="resource_deployment"></div>

<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//搜索
	$('#_search_panel').panel({
		href:'index.php?c=client_resource&m=base_search',
		onLoad:function()
		{
			$('#client_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});
	//设置列表
	$('#client_list').datagrid({
		title:"客户列表(资源调配)<span style='color:red;'>&nbsp;批量分配 只能分配本部门（除子部门）无所属人的数据，其他数据想再分配，请先收回</span>",
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		collapsible:false,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'cle_creat_time',
		sortOrder:'desc',//降序排列
		idField:'cle_id',
		url:"index.php?c=client_resource&m=get_client_resource",
		queryParams:{"all_type":"3"},
		frozenColumns:[[
		{field:'ck',checkbox:true}
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
			<?php if ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_name'): ?>
			return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"');>"+value+"</a>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile'): ?>
			if(value)
			{
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				value = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"'); class='underline'>"+value+"</a>";
			}
			else
			return value;
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_address'): ?>
			return "<span class='show-res-tooltip'>"+value+"</span>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_remark'): ?>
			return "<span class='show-res-tooltip'>"+value+"</span>";
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
			$('.show-res-tooltip').tooltip({
				trackMouse:true,
				position:'right',
				onShow: function(){
					var content = '<div style="width:180px;">'+$(this).text().replace(/\n/g, "<br>")+'</div>';
					$(this).tooltip('update',content);
				}
			});
		},
		toolbar:[
		{
			iconCls:'icon-next',
			text:'选中数据调配',
			handler:function(){
				var cle_ids = getSelections();
				if(cle_ids == '')
				{
					$.messager.alert('提示','<br>请选择需要分配的数据！','error');
					return;
				}
				else
				{
					deployment_client();
				}
			}
		},"-",
		<?php if ($this->_tpl_vars['power_batch_deploy']): ?>
		{
			iconCls:'icon-text',
			text:'批量分配',
			handler:function(){
				var queryParams = $('#client_list').datagrid('options').queryParams;
				if( queryParams  )
				{
					queryParams     = json2url(queryParams);
					queryParams     = "&"+queryParams;
				}
				var total       = $('#client_list').datagrid('getData').total;
				window.parent.addTab('批量分配','index.php?c=client_resource&m=deployment_batch&total='+total+queryParams,'menu_icon');
			}
		},"-",
		<?php endif; ?>
		{
			iconCls:'icon-refresh',
			text:'选中数据收回',
			handler:function(){
				var cle_ids = getSelections();
				if(cle_ids == '')
				{
					$.messager.alert('提示','<br>请选择需要收回的数据！','error');
					return;
				}
				else
				{
					$.messager.confirm('提示', '<br>您确定要收回该客户？', function(r){
						if(r){
							$.ajax({
								type:'POST',
								url: "index.php?c=client_resource&m=take_back_client",
								data: {"cle_id":cle_ids},
								dataType: "json",
								success: function(responce){
									if(responce["error"] == 0)
									{
										$('#client_list').datagrid('load');
									}
									else
									{
										$.messager.alert('错误',responce["message"],'error');
									}
								}
							});
						}
					});
				}
			}
		},"-",
		{
			iconCls:'icon-refresh',
			text:'批量收回',
			handler:function(){
				var total       = $('#client_list').datagrid('getData').total;
				$.messager.confirm('提示', '<br>您确定要收回当前列表满足搜索条件的 '+total+'条数据？', function(r){
					if(r){
						var queryParams = $('#client_list').datagrid('options').queryParams;
						$.ajax({
								type:'POST',
								url: "index.php?c=client_resource&m=take_more_client_back",
								data:queryParams,
								dataType: "json",
								success: function(responce){
									if(responce["error"] == 0)
									{
										$('#client_list').datagrid('load');
									}
									else
									{
										$.messager.alert('错误',responce["message"],'error');
									}
								}
							});
					}
				});
			}
		},"-",
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				$('#datagrid_resource').window({
					title: '显示列表设置',
					href:"index.php?c=datagrid_confirm&display_type=3",
					iconCls: "icon-edit",
					top:150,
					width:450,
					closed: false,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false
				});
			}
		}
		]
	});
	var pager = $('#client_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

function getSelections(){
	var ids  = [];
	var rows = $('#client_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].cle_id);
	}
	return ids.join(',');
}

//业务受理
function link_client_accept(cle_id)
{
	window.parent.addTab('业务受理','index.php?c=client&m=accept&cle_id='+cle_id,'menu_icon');
}


//选中数据分配
function deployment_client()
{
	$('#resource_deployment').window({
		title:"客户分配",
		href:"index.php?c=client_resource&m=deployment_client",
		width:420,
		height:130,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		cache:false
	});
}

//基本搜索
function base_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client_resource&m=base_search');
}

//高级搜索
function advanced_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=advance_search&flag=resource');
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>