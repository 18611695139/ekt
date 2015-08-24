<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:23
         compiled from order_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
var power_view_product = <?php echo $this->_tpl_vars['power_view_product']; ?>
;
$(document).ready(function() {
	$("#output_choose_window").window("close");/*导出选择窗口*/
	$("#change_more_window").window("close");/*批量修改*/

	$('#_search_panel').panel({
		href:'index.php?c=order&m=order_base_search',
		onLoad:function(width, height){
			$('#order_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});
	$('#order_list').datagrid({
		title:'订单列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
        showFooter: true,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'order_id',
		sortOrder:'desc',//降序排列
		idField:'order_id',
		url:'index.php?c=order&m=get_order_query',
		queryParams:{'all_type':'2'},
		<?php if ($this->_tpl_vars['system_order_product_amount'] == 2): ?>
		view: detailview,
		detailFormatter:function(index,row){
			return '<div id="ddv-' + index + '" style="padding:5px 0"></div>';
		},
		onExpandRow: function(index,row){
			$('#ddv-'+index).panel({
				border:false,
				cache:false,
				href:'index.php?c=order&m=get_list_product&order_id='+row.order_id+'&order_num='+row.order_num,
				onLoad:function(){
					$('#order_list').datagrid('fixDetailRowHeight',index);
				}
			});
			$('#order_list').datagrid('fixDetailRowHeight',index);
		},
		<?php endif; ?>
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'操作',field:'oper_txt',width:50,align:"CENTER",formatter:function(value,rowData,rowIndex){
			if(rowData.order_num!='总金额')
			return "<span><a href='javascript:;' onclick=link_order_accept("+rowData.order_id+",'link','') title='订单受理'>【受理】</a></span>";
			else
			return '';
		}},
		]],
		columns:[[
		<?php if ($this->_tpl_vars['system_order_product_amount'] == 1): ?>
		{title:'订单产品',field:'product_thum_pic',width:60,align:"CENTER",formatter:function(value,rowData,rowIndex){
			if(value!=''&&value!=undefined)
			return "<a href='javascript:;' onclick=show_order_product_detail("+rowData.product_id+") class='show-order-tooltip' title='产品名称："+rowData.product_name+"，产品单价："+rowData.product_price+"'><img src='"+rowData.product_thum_pic+"' /></a>";
			else
			return '';
		}},
		{title:'购买量',field:'product_number',width:40,align:"CENTER"},
		<?php endif; ?>
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields']; ?>
' ,align:"CENTER",width:"<?php echo $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_num'): ?>
			if(value=='总金额')
			return value;
			if(value!=''&&value!=undefined){
				return "<span>"+value+"<a href='javascript:;' onclick=link_order_accept("+rowData.order_id+",'link','') title='订单受理'><img src='image/file.png' /></a></span>";}else{return '';}
				<?php elseif ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_name'): ?>
				if(value!=''&&value!=undefined){
					return "<span class='show-order-tooltip' title='"+value+"'>"+value+"<a href='###' onclick=client_exist("+rowData.cle_id+") class='underline' title='客户详情'><img src='image/file.png' /></a></span>";
				}else{return '';}
				<?php elseif ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone' || $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile'): ?>
				if(value){
					var show_real = value;
					<?php if (! $this->_tpl_vars['power_phone_view']): ?>
					show_real = hidden_part_number(value);
					<?php endif; ?>
					return "<a href='javascript:;' onclick = link_order_accept("+rowData.order_id+",'dail','"+value+"') title='呼叫'  >"+show_real+"&nbsp;&nbsp;<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick=sys_send_sms("+value+"); title='短信' ><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /></a><?php endif; ?>";
				}else{return '';}
				<?php else: ?>
				return value;
				<?php endif; ?>
		}
		<?php if ($this->_tpl_vars['power_update_order']): ?>
		<?php if ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_state'): ?>
		,editor:{
			type:'combobox',
			options:{
				valueField:'value',
				textField:'text',
				required:true,
				data:[
				<?php $_from = $this->_tpl_vars['order_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['info_state']):
?>
				{value:'<?php echo $this->_tpl_vars['info_state']['name']; ?>
',text:'<?php echo $this->_tpl_vars['info_state']['name']; ?>
'},
				<?php endforeach; endif; unset($_from); ?>
				{}]
			}
		}
		<?php endif; ?>
		<?php if ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_delivery_number'): ?>
		,editor:'text'
		<?php endif; ?>
		<?php endif; ?>
		},
		<?php endfor; endif; ?>
		{title:'order_id',field:'order_id',hidden:true}
		]],
		onLoadSuccess: function(data){
			$('#order_list').datagrid('clearSelections');
			$('#order_list').datagrid('clearChecked');

			$('.show-order-tooltip').tooltip({
				trackMouse:true,
				position:'right'
			});
		},
		<?php if ($this->_tpl_vars['power_update_order']): ?>
		onClickCell:function(rowIndex,field,value)
		{
			if(field == 'order_state' || field == 'order_delivery_number')
			{
				if (editIndex != rowIndex)
				{
					if (endEditing())
					{
						$('#order_list').datagrid('selectRow', rowIndex)
						.datagrid('beginEdit', rowIndex);
						editIndex = rowIndex;
					}
					else
					$('#order_list').datagrid('selectRow', editIndex);
				}
			}
			else
			{
				$('#order_list').datagrid('endEdit', editIndex);
				editIndex = undefined;
			}
		},
		onAfterEdit:function(rowIndex,rowData,changes)
		{
			var o_state = rowData.order_state;
			var o_state_original = rowData.order_state_original;
			var o_delivery_number = rowData.order_delivery_number;
			var o_delivery_number_original = rowData.order_delivery_number_original;
			var o_id = rowData.order_id;
			var o_num = rowData.order_num;

			change_order_state_or_delivery_number(rowIndex,o_state,o_state_original,o_delivery_number,o_delivery_number_original,o_id,o_num);
		},
		<?php endif; ?>
		toolbar:[
		<?php if ($this->_tpl_vars['power_insert_order']): ?>
		{
			iconCls:'icon-add',
			text:'添加订单',
			handler:function(){
				window.parent.addTab('添加订单','index.php?c=order&m=add_order','menu_icon');
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_update_order']): ?>
		{
			iconCls:'icon-edit',
			text:'批量修改状态',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','<br>请选择需要修改的订单','info');
					return;
				}
				$('#change_more_window').window('open');
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_delete_order']): ?>
		{
			iconCls:'icon-del',
			text:'批量删除',
			handler:function(){
				delete_order();
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_output_order']): ?>
		{
			iconCls:'icon-up',
			text:'导出csv',
			handler:function(){
				output_order_datch(0,'csv');
			}
		},'-',
        {
           iconCls:'icon-up',
           text:'导出excel',
           handler:function(){
               output_order_datch(0,'excel');
           }
        },'-',
		<?php endif; ?>
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				datagrid_list_display_window();
			}
		},'-'
		]
	});
	var pager = $('#order_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});
</script>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script src="./jssrc/jquery.easyui.datagrid-detailview.js" type="text/javascript"></script>
<script src="./jssrc/viewjs/order_list.js?1.2" type="text/javascript"></script>

<!--搜索-->
<div id='_search_panel' class='form-div'></div>
<div id='order_list'></div><!--列表-->
  
<div id="datagrid_list_display"></div> <!--自定义字段-->
<div id="order_product_detail_window"></div><!--产品详情-->

<!--  1导出选中订单数据  2导出列表搜到的所有订单数据--><!--导出选择-->
<div id="output_choose_window" class="easyui-window" title="选择导出（有选中订单）" style="width:350px;padding:10px;" data-options="closed:true,collapsible:false,minimizable:false,maximizable:false,cache:false,modal:true,closable:true,draggable:false">
	<div align="left">
		<input type="radio" id="choose_output_1" name="choose_out" value="1"  onclick="click_choose();"/><label for="choose_out_1">导出选中订单数据</label></br>
		<input type="radio" id="choose_output_2" name="choose_out" value="2"  onclick="click_choose();"/><label for="choose_out_2">导出列表搜到的所有订单数据</label></br>
	</div>
</div>

<!-- 批量修改-->
<div id="change_more_window" class="easyui-window" title="批量修改" style="width:350px;padding:10px;" data-options="closed:true,collapsible:false,minimizable:false,maximizable:false,cache:false,modal:true,closable:true,draggable:false">
	<div style='text-align:center'>
		选中订单的【订单状态】将修改为 <select name='order_list_state' id='order_list_state' style='width:100px;'><?php $_from = $this->_tpl_vars['order_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['info_state']):
?><option value='<?php echo $this->_tpl_vars['info_state']['name']; ?>
'  ><?php echo $this->_tpl_vars['info_state']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select>
		<div style='padding-top:10px;text-align:center;'>
			<a class='easyui-linkbutton' href='javascript:void(0)' onclick='chose_change_more_window()'>确定</a>
		</div>
	</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>