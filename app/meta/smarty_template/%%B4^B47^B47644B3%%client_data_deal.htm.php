<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:36
         compiled from client_data_deal.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class='form-div'>
<div class="easyui-accordion" id='data_accordion'>
	<div title='第一步 搜索要处理的数据' style="overflow:auto;padding:10px;"  href='index.php?c=client&m=advance_search&flag=data_deal'></div>
	<div title="第二步 处理数据" href="index.php?c=client_data_deal&m=deal_result"></div>
</div>
</div>

<div id="client_list" ></div>
<input type='hidden' id='list_total' name='list_total' value='' />
<div id="datagrid_client"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){

	//设置列表
	$('#client_list').datagrid({
		title:"客户列表",
		nowrap: true,
		striped: true,
		autoRowHeight:false,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'cle_id',
		sortOrder:'desc',//降序排列
		idField:'cle_id',
		url:"index.php?c=client_data_deal&m=list_client_query",
		queryParams:{},
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
			return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"'); class='underline'>"+value+"</a>";
			power_phone_view
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone'): ?>
			if(value)
			{
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				value = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"'); class='underline'>"+value+"</a>";
			}
			else
			return value;
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:"cle_id" ,field:"cle_id",hidden:true}
		]],
		onLoadSuccess: function(data){
			if(data.total!=0)
			{
				$('#data_accordion').accordion('select',"第二步 处理数据");
				$('#list_total').val(data.total);
			}
			$('#client_list').datagrid('clearSelections');
			$('#client_list').datagrid('clearChecked');
		},
		onDblClickRow:function (rowIndex, rowData)  //双击  -  业务受理
		{
			link_client_accept(rowData.cle_id);
		},
		toolbar:[
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				$('#datagrid_client').window({
					title: '显示列表设置',
					href:"index.php?c=datagrid_confirm&display_type=4",
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
		},'-']
	});
	
	var pager = $('#client_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

//点击清空
function search_cle_name()
{
	var _name = $('#cle_name_search').val();
	if(_name == '支持拼音首字母')
	{
		$('#cle_name_search').css('color','#000000');
		$('#cle_name_search').val('');
	}
}
//判断是否为空
function if_null()
{
	var name_value = $('#cle_name_search').val();
	if(name_value.length == 0)
	{
		$('#cle_name_search').css('color','#cccccc');
		$('#cle_name_search').val('支持拼音首字母');
	}
}

function link_client_accept(cle_id)
{
	window.parent.addTab('业务受理','index.php?c=client&m=accept&cle_id='+cle_id,'menu_icon');
}

</script>