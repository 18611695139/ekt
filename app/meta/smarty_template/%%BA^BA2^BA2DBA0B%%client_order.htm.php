<?php /* Smarty version 2.6.19, created on 2015-08-11 12:59:13
         compiled from client_order.htm */ ?>
<div id='order_list'></div>
<div id="order_detail_window"></div>
<div id="order_product_detail_window"></div>

<script type="text/javascript" src="./jssrc/jquery.preview.js"></script>
<script type="text/javascript" src="./jssrc/jquery.easyui.datagrid-detailview.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	$('#order_list').datagrid({
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageList:[10],
		sortName:'order_id',
		sortOrder:'desc',//降序排列
		idField:'order_id',
		url:'index.php?c=order&m=get_order_query&write_cookie=2',
		queryParams:{'cle_id':'<?php echo $this->_tpl_vars['cle_id']; ?>
'},
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
		]],
		columns:[[
		<?php if ($this->_tpl_vars['system_order_product_amount'] == 1): ?>
		{title:'订单产品',field:'product_thum_pic',width:60,align:"CENTER",formatter:function(value,rowData,rowIndex){
			if(value!='')
			return "<a href='javascript:;' onclick=show_order_product_detail("+rowData.product_id+")  class='show-order-tooltip' title='产品名称："+rowData.product_name+"，产品单价："+rowData.product_price+"'><img src='"+rowData.product_thum_pic+"' /></a>";
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
		<?php if ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_num' || $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_state' || $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_price' || $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_accept_time' || $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'user_id'): ?>
		{title: "<?php echo $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields']; ?>
' ,align:"CENTER",width:"150",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['_display_field'][$this->_sections['key']['index']]['fields'] == 'order_num'): ?>
			return value+"<a href='###' title='查看订单详情' onclick=window.parent.addTab('订单受理','index.php?c=order&m=order_accept&system_pagination=0&order_id="+rowData.order_id+"','menu_icon'); ><img src='image/file.png' /> </a>";
			<?php else: ?>
			return value
			<?php endif; ?>
		}},
		<?php endif; ?>
		<?php endfor; endif; ?>
		{title:'order_id',field:'order_id',hidden:true}
		]]
		<?php if ($this->_tpl_vars['power_insert_order']): ?>
		,toolbar:[{
			iconCls:'icon-add',
			text:'添加订单',
			handler:function(){
				window.parent.addTab('添加订单',"index.php?c=order&m=add_order&cle_id=<?php echo $this->_tpl_vars['cle_id']; ?>
","menu_icon");
			}
		}
		]
		<?php endif; ?>
		,onLoadSuccess: function(){
			$('#order_list').datagrid('clearSelections');
			//图片检测到鼠标焦点，预览图片
			if($('a.preview').length){
				var img = preloadIm();
				imagePreview(img);
			}
			
			$('.show-order-tooltip').tooltip({
				trackMouse:true,
				position:'right'
			});
		}
	});

	var pager = $('#order_list').datagrid('getPager');
	$(pager).pagination({showPageList:false});
});
/*展示订单详情*/
function order_view(order_id)
{
	<?php if ($this->_tpl_vars['power_view_order']): ?>
	$('#order_detail_window').window({
		title: '订单详情',
		iconCls:"icon-save",
		href:"index.php?c=order&m=view_order&order_id="+order_id,
		width:582,
		height:400,
		top:50,
		shadow:true,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
	<?php else: ?>
	$.messager.alert('提示','您没有查看订单的权限！','info');
	<?php endif; ?>
}

function show_order_product_detail(product_id)
{
	<?php if ($this->_tpl_vars['power_view_product']): ?>
	$('#order_product_detail_window').window({
		title: '产品详情',
		iconCls:"icon-ok",
		href:"index.php?c=product&m=view_product&product_id="+product_id,
		width:580,
		top:50,
		shadow:true,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
	<?php else: ?>
	$.messager.alert('提示','您没有查看产品的权限！','info');
	<?php endif; ?>
}


</script>