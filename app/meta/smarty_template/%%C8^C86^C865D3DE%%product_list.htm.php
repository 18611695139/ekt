<?php /* Smarty version 2.6.19, created on 2015-08-11 12:58:59
         compiled from product_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id='_search_panel'></div>
<div id='product_list'></div>
<div id='set_product_field_confirm'></div>
<div id='datagrid_product'></div>
<div id='product_detail_window'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="./jssrc/jquery.preview.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	$('#_search_panel').panel({
		href:'index.php?c=product&m=product_base_search',
		onLoad:function(width, height){
			$('#product_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});
	$('#product_list').datagrid({
		title:'产品列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'product_id',
		sortOrder:'desc',//降序排列
		idField:'product_id',
		url:'index.php?c=product&m=get_product_query',
		frozenColumns:[[
		]],
		columns:[[
		<?php if ($this->_tpl_vars['power_view_delete']): ?>
		{field:'ck',checkbox:true},
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_view_update']): ?>
		{title:'操作',field:'oper_txt',width:40,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<span><a href='javascript:;' onclick=window.parent.addTab('编辑产品','index.php?c=product&m=edit_product&product_id="+rowData.product_id+"','menu_icon'); title='编辑'><img src='./image/icon_edit.gif' border='0' height='16' width='16' align='absmiddle'/></a>";
		}},
		<?php endif; ?>
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['product_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['fields']; ?>
' ,align:"CENTER",width:"<?php echo $this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['fields'] == 'product_name'): ?>
			return value+"<a href='###' title='查看产品详情' onclick='show_product_detail("+rowData.product_id+")'><img src='image/file.png' /></a>";
			<?php elseif ($this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['fields'] == 'product_thum_pic'): ?>
			if(value!=''){return "<span ><a href='###' title='点击查看大图' path='"+rowData.product_pic+"' class='preview' onclick=window.parent.addTab('图片','index.php?c=product&m=show_pic&product_id="+rowData.product_id+"','menu_icon');><img src='"+value+"' border='0' align='absmiddle' /></a></span>";}else{return "<img src='"+rowData.no_picture+"' title='无图' border='0' align='absmiddle'>";}
			<?php elseif ($this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['fields'] == 'product_state'): ?>
			if(value==1){return "上架<?php if ($this->_tpl_vars['power_view_update']): ?><a href='###' onclick=change_product_state("+rowData.product_id+","+rowData.product_state+")><img src='./image/stop.gif' border='0' height='16' width='16' align='absmiddle' title='点击下架' /></a><?php endif; ?>";}else if(value==2){return "下架<?php if ($this->_tpl_vars['power_view_update']): ?><a href='###' onclick=change_product_state("+rowData.product_id+","+rowData.product_state+")><img src='./image/run.gif' border='0' height='16' width='16' align='absmiddle' title='点击上架' /></a><?php endif; ?>";}else{return '';}
			<?php elseif ($this->_tpl_vars['product_display_field'][$this->_sections['key']['index']]['fields'] == 'product_price'): ?>
			if(value!='')
			{
				return value+'&nbsp;元'
			}
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:'product_id',field:'product_id',hidden:true}
		]],
		onLoadSuccess: function(){
			$('#product_list').datagrid('clearSelections');
			$('#product_list').datagrid('clearChecked');
			if($('a.preview').length){
				var img = preloadIm();
				imagePreview(img);
			}
		},
		toolbar:[
		<?php if ($this->_tpl_vars['power_view_insert']): ?>
		{
			text:'添加产品',
			iconCls:'icon-add',
			handler:function(){
				window.parent.addTab('添加产品',"index.php?c=product&m=add_product","menu_icon");
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_view_delete']): ?>
		{
			text:'删除选中产品',
			iconCls:'icon-del',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','请选中要删除的产品！','error');
					return false;
				}
				$.messager.confirm('提示', '您确定要删除选中产品？', function(r){
					if(r){
						$.ajax({
							type:"POST",
							url:'index.php?c=product&m=delete_product',
							data:{"product_ids":ids},
							dataType:'json',
							success:function (responce){
								if(responce['error']=='0'){
									$('#product_list').datagrid('load');
								}
								else
								{
									$.messager.alert('执行错误',responce['message'],'error');
								}
							}
						});
					}
					else
					{
						return false;
					}
				});
			}
		},'-',
		<?php endif; ?>
		{
			text:'列表设置',
			iconCls:'icon-seting',
			handler:function(){
				$('#datagrid_product').window({
					title: '显示列表设置',
					href:"index.php?c=datagrid_confirm&display_type=5",
					iconCls: "icon-seting",
					top:10,
					width:360,
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
	var pager = $('#product_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/**
*列表得到选中的id
*/
function getSelections()
{
	var ids = [];
	var rows = $('#product_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].product_id);
	}
	return ids.join(',');
}

/**
* 改变 产品状态
*/
function change_product_state(product_id,product_state)
{
	$.ajax({
		type:"POST",
		url:'index.php?c=product&m=set_product_state',
		data:{"product_id":product_id,'product_state':product_state},
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0'){
				$('#product_list').datagrid('load');
			}
			else
			{
				$.messager.alert('执行错误',responce['message'],'error');
			}
		}
	});
}

//基本搜索
function base_search()
{
	$('#product_list').datagrid('options').queryParams = {};
	$('#_search_panel').panel('open').panel('refresh','index.php?c=product&m=product_base_search');
}

//高级搜索
function advanced_search()
{
	$('#product_list').datagrid('options').queryParams = {};
	$('#_search_panel').panel('open').panel('refresh','index.php?c=product&m=product_advance_search');
}

/*查看产品详情*/
function show_product_detail(product_id)
{
	<?php if ($this->_tpl_vars['power_view_product']): ?>
	$('#product_detail_window').window({
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