<?php /* Smarty version 2.6.19, created on 2015-08-07 10:18:07
         compiled from knowledge_more.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'knowledge_more.htm', 21, false),)), $this); ?>
<div style="width:100%">
<div id='more_article_list'></div>
</div>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	$('#more_article_list').datagrid({
		title:'<?php echo $this->_tpl_vars['class_name_one']; ?>
',
		height:get_list_height_fit_window(''),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		fitColumns:true,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'k_art_click_rate',
		sortOrder:'DESC',//降序排列
		idField:'k_art_id',
		url:'index.php?c=knowledge&m=get_more_art_query',
		queryParams:{"k_class_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['_class_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","search_key":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['search_key'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","k_art_title_advan":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['k_art_title_advan'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","k_class_id_advan":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['k_class_id_advan'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","k_art_hot_advan":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['k_art_hot_advan'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","k_content_advan":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['k_content_advan'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"},
		frozenColumns:[[
		<?php if ($this->_tpl_vars['power_zsk_delete']): ?>
		{field:'ck',checkbox:true},
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_zsk_update']): ?>
		{title:'操作',field:'oper_txt',width:30,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='#' onclick='edit_article("+rowData.k_art_id+")'><img src='image/icon_edit.gif' title='编辑' /></a>";
		}},
		<?php endif; ?>
		{title:'文章标题',field:'k_art_title',width:220,sortable:true,formatter:function(value,rowData,rowIndex){
			return "<a href='###' onclick=\"art_detail("+rowData.k_art_id+",\'"+rowData.k_art_title+"\',"+rowData.k_class_id+")\">"+value+"</a>";
		}},
		{title:'k_art_id',field:'k_art_id',hidden:true}
		]],
		columns:[[
		{title:'点击率',field:'k_art_click_rate',width:40,align:"CENTER",sortable:true},
		{title:'栏目分类',field:'k_class_name',width:80,align:"CENTER"},
		{title:'热点',field:'k_art_hot',width:40,sortable:true,align:"CENTER",formatter:function(value,rowData,rowIndex){
			if(value == 1) return '是'; else return '否';
		}},
		{title:'最后修改人',field:'k_art_update_user',width:60,sortable:true},
		{title:'最后修改时间',field:'k_art_update_date',width:115,sortable:true}
		]],
		onLoadSuccess: function(){
			$('#more_article_list').datagrid('clearSelections');
			$('#more_article_list').datagrid('clearChecked');
		}
		<?php if ($this->_tpl_vars['power_zsk_delete']): ?>
		,toolbar:[{
			text:'批量删除',
			iconCls:'icon-del',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','<br>请选择要删除的文章','info');
					return;
				}
				$.messager.confirm('提示','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a> 您确定删除吗？',function(r){
					if(r)
					{
						$.ajax({
							url : 'index.php?c=knowledge&m=remove_article',
							data : {k_art_ids:ids},
							dataType : 'json',
							type : 'post',
							success : function(responce){
								if(responce['error'] === 0)
								{
									$('#more_article_list').datagrid('load');
									$('#hot_div').panel('open').panel('refresh');
									$('#class_div').panel('open').panel('refresh');
									
								}
								else
								{
									$.messager.alert('Notice','删除失败','warning');
								}
							}
						});
					}
				});

			}
		}]
		<?php endif; ?>
	});
	var pager = $('#more_article_list').datagrid('getPager');
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
	var rows = $('#more_article_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++){
		ids.push(rows[i].k_art_id);
	}
	return ids.join(',');
}

/*获取修改文章页面*/
function edit_article(art_id)
{
	$('#knowledge_panel').panel('open').panel('refresh','index.php?c=knowledge&m=edit_article&k_art_id='+art_id);
}
</script>