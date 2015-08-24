<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:31
         compiled from order_statistics.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'>
 <form action="javascript:quick_search()" name="searchForm" id="searchForm">
		<img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
		部门/坐席 <input type="text" id="dept_user_search" name="dept_user_search" value='' style='width:150px;' />&nbsp;&nbsp;
         日期  <span class="combo datebox" style="width: 165px;"><input type="text" class="combo-text validatebox-text" id='deal_date_search_start' name='deal_date_search_start' value="<?php echo $this->_tpl_vars['today_start']; ?>
" style="width: 141px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_date_search_start',dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></span></span></span> ~  <span class="combo datebox" style="width: 165px;"><input type="text" class="combo-text validatebox-text" id='deal_date_search_end' name='deal_date_search_end' value="<?php echo $this->_tpl_vars['today_end']; ?>
"  style="width: 141px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_date_search_end',dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></span></span></span>
         订单状态 <select id='order_state_search' name='order_state_search'>
         <option value=''>--请选择--</option>
        <?php $_from = $this->_tpl_vars['order_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['o_state']):
?>
			<option value='<?php echo $this->_tpl_vars['o_state']['name']; ?>
'><?php echo $this->_tpl_vars['o_state']['name']; ?>
</option>
 		<?php endforeach; endif; unset($_from); ?>
         </select>
    
          <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
          <span style="color:red;">注：默认显示当天的有下单的坐席数据</span>
    </form>
</div>

<div id='statistics_list'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var state_time = '<?php echo $this->_tpl_vars['today_date']; ?>
';
var end_time = '<?php echo $this->_tpl_vars['today_date']; ?>
';
$(document).ready(function() {
	//部门
	$('#dept_user_search').combotree({
		url:'index.php?c=user&m=get_dept_user_tree',
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(row,param){
			if (!row)
			{ // load top level rows
				param.id = 0;   // set id=0, indicate to load new page rows
			}
		}
	});
	$('#dept_user_search').combotree('setValue','<?php echo $this->_tpl_vars['dept_session_id']; ?>
');


	<?php unset($this->_sections['state']);
$this->_sections['state']['name'] = 'state';
$this->_sections['state']['loop'] = is_array($_loop=$this->_tpl_vars['order_state']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['state']['show'] = true;
$this->_sections['state']['max'] = $this->_sections['state']['loop'];
$this->_sections['state']['step'] = 1;
$this->_sections['state']['start'] = $this->_sections['state']['step'] > 0 ? 0 : $this->_sections['state']['loop']-1;
if ($this->_sections['state']['show']) {
    $this->_sections['state']['total'] = $this->_sections['state']['loop'];
    if ($this->_sections['state']['total'] == 0)
        $this->_sections['state']['show'] = false;
} else
    $this->_sections['state']['total'] = 0;
if ($this->_sections['state']['show']):

            for ($this->_sections['state']['index'] = $this->_sections['state']['start'], $this->_sections['state']['iteration'] = 1;
                 $this->_sections['state']['iteration'] <= $this->_sections['state']['total'];
                 $this->_sections['state']['index'] += $this->_sections['state']['step'], $this->_sections['state']['iteration']++):
$this->_sections['state']['rownum'] = $this->_sections['state']['iteration'];
$this->_sections['state']['index_prev'] = $this->_sections['state']['index'] - $this->_sections['state']['step'];
$this->_sections['state']['index_next'] = $this->_sections['state']['index'] + $this->_sections['state']['step'];
$this->_sections['state']['first']      = ($this->_sections['state']['iteration'] == 1);
$this->_sections['state']['last']       = ($this->_sections['state']['iteration'] == $this->_sections['state']['total']);
?><?php endfor; endif; ?>
	$('#statistics_list').datagrid({
		title:'订单统计列表',
		//		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		rownumbers:true,
		checkOnSelect:false,
		remoteSort:false,
		showFooter: true,
		pagination:true,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		fitColumns:true,
		url:'index.php?c=order&m=get_order_statistics_info',
		queryParams:{'start_date':'<?php echo $this->_tpl_vars['today_start']; ?>
','end_date':'<?php echo $this->_tpl_vars['today_end']; ?>
'},
		frozenColumns:[[
		{title:'坐席',field:'user_name',align:"CENTER",width:120},
		{title:'所属部门',field:'dept_name',align:"CENTER",width:120},
		{title:'订单数',field:'order_total',align:"CENTER",width:120},
		{title:'订单总额(元)',field:'price',align:"CENTER",width:120},
		{title:'订单产品数',field:'product_number',align:"CENTER",width:120}
		]],
		columns:[[
		{title: "订单状态",align:"CENTER",colspan:<?php echo $this->_sections['state']['total']; ?>
}
		],[
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['order_state']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['order_state'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['order_state'][$this->_sections['key']['index']]['state_id']; ?>
' ,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			return value;
		}},
		<?php endfor; endif; ?>
		{field:'user_id',hidden:true}
		]]
	});
	var pager = $('#statistics_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/*搜索*/
function quick_search()
{
	var search_data = {};
	search_data.start_date = $("#deal_date_search_start").val();
	search_data.end_date = $("#deal_date_search_end").val();
	search_data.order_state = $("#order_state_search").val();
	var dept_user = $("#dept_user_search").combotree('getValue');
	dept_user = dept_user.split('user');
	if(dept_user[0]=='')
	{
		search_data.user_id = dept_user[1];/*所属人*/
	}
	else
	{
		search_data.dept_id = dept_user[0];/*部门*/
	}

	$('#statistics_list').datagrid('options').queryParams = {};
	var queryParams = $('#statistics_list').datagrid('options').queryParams;
	$.each(search_data,function(field,value){
		if(typeof(value) != undefined && value.length != 0 )
		{
			queryParams[field] = value;
		}
	});

	$('#statistics_list').datagrid('load');
}

</script>