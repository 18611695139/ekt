<?php /* Smarty version 2.6.19, created on 2015-08-07 09:20:56
         compiled from service.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'service.htm', 2, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<input type="hidden" id="service_cle_id"  name="service_cle_id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
" />
<div class="form-div" id='_search_panel'></div>
<div id="service_list"></div>
<div id="datagrid_service"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	$('#_search_panel').panel({
		href:'index.php?c=service&m=base_search',
		onLoad:function(width, height){
			$('#service_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});

	//设置列表
	$('#service_list').datagrid({
		title:"客服服务信息",
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'serv_accept_time',
		sortOrder:'desc',//降序排列
		idField:'serv_id',
		url:"index.php?c=service&m=get_service_list",
		queryParams:{"all_type":"2"},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:"cle_id",field:"cle_id",hidden:true},
		{title:'操作',field:'opter_txt',width:60,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='javascript:;' onclick=edit_service('"+rowData.serv_id+"') title='受理'>【受理】</a>";
		}}
		]],
		columns:[[
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['serv_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields']; ?>
' , align:'CENTER',width:"<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile' || $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone'): ?>
			if(value)
			{
				var real_phone = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				value = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='javascript:;' onclick = sys_dial_num('"+value+"'); title='呼叫'  >"+value+"<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick=sys_send_sms("+real_phone+"); title='短信' ><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /></a><?php endif; ?>";
			}
			<?php elseif ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_name'): ?>
			return "<a href='###' onclick=client_exist("+rowData.cle_id+")  title='客户详情'>"+value+"<img src='image/file.png' /></a>";
			<?php elseif ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'serv_content'): ?>
			return "<a href='javascript:;' onclick=edit_service('"+rowData.serv_id+"') title='受理'>"+value+"</a>";
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:"serv_id",field:"serv_id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#service_list').datagrid('clearSelections');
			$('#service_list').datagrid('clearChecked');
		},
		toolbar:[
		<?php if ($this->_tpl_vars['power_service_add']): ?>
		{
			text:'新建服务',
			iconCls:'icon-add',
			handler:function(){
				window.parent.addTab('新建服务',"index.php?c=service&m=add_service","menu_icon");
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_service_delete']): ?>
		{
			iconCls:'icon-del',
			text:'删除客服信息',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','<br>请选择要删除的数据','info');
					return;
				}
				$.messager.confirm('提示', '<br>删除'+selected_num+'条数据', function(r){
					if(r)
					{
						//删除客服服务数据
						delete_service(ids);
					}
					else
					{
						return false;
					}
				});
			}
		} ,'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_service_export']): ?>
		{
			iconCls:'icon-up',
			text:'导出CSV',
			handler:function(){
            export_service('csv');
			}
		},'-',
        {
            iconCls:'icon-up',
            text:'导出excel',
            handler:function(){
                export_service('excel');
            }
        },'-',
		<?php endif; ?>
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				$('#datagrid_service').window({
					title: '显示列表设置',
					href:"index.php?c=datagrid_confirm&display_type=7",
					iconCls: "icon-edit",
					top:150,
					width:350,
					height:380,
					closed: false,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false
				});
			}
		},'-'
		]
	});
	var pager = $('#service_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

//得到选中项的 客服ID
var selected_num  = 0;
function getSelections()
{
	selected_num  = 0;
	var ids = [];

	var rows = $('#service_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++)
	{
		ids.push(rows[i].serv_id);
		selected_num++;
	}

	return ids.join(',');
}

/**
*  删除  客服服务
*/
function delete_service(serv_id)
{
	if( !serv_id )
	{
		$.messager.alert("提示","<br>缺少必要参数！","info");
		return false;
	}

	$.messager.confirm('提示', '<br>是否删除该信息？', function(r){
		if(r){
			$.ajax({
				type:'POST',
				url: "index.php?c=service&m=delete_service",
				data: {"serv_id":serv_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#service_list').datagrid('load');
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

/**
*  编辑 客户服务
*/
function edit_service(serv_id)
{
	if( !serv_id )
	{
		$.messager.alert("提示","<br>缺少必要参数！","info");
		return false;
	}

	window.parent.addTab('客服受理',"index.php?c=service&m=edit_service&serv_id="+serv_id,"menu_icon");
}

//列表 - 业务受理
function link_client_accept(cle_id)
{
	window.parent.addTab('业务受理','index.php?c=client&m=accept&cle_id='+cle_id,'menu_icon');
}

//基本搜索
function base_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=service&m=base_search');
}

//高级搜索
function advanced_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=service&m=advance_search');
}

/*客户详情*/
function client_exist(cle_id)
{
	$.ajax({
		type:"POST",
		url:'index.php?c=client&m=client_exist',
		data:{"cle_id":cle_id},
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0'){
				window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&cle_id='+cle_id,'menu_icon');
			}
			else
			{
				$.messager.alert('错误',responce['message'],'error');
			}
		}
	});
}

/*导出*/
function export_service(type)
{
    //列表参数
    var datagrid_param = $('#service_list').datagrid('options');
    var queryParams   = datagrid_param.queryParams;

    //排序
    queryParams.sortName  = datagrid_param.sortName;
    queryParams.sortOrder = datagrid_param.sortOrder;
    queryParams.total     = $('#service_list').datagrid('getData').total;

    var sql_condition = json2url(queryParams);
    if( sql_condition )
    {
        sql_condition = "&"+sql_condition;
    }
    window.location.href = 'index.php?c=service&m=service_output'+sql_condition;

    if(type == 'excel' && queryParams.total>10000)
    {
        $.messager.confirm('数据量大，建议用【导出CSV】导出数据', '<br>确定继续导出excel吗？', function(r){
            if(r){
                window.location.href = 'index.php?c=service&m=service_output&export_type='+type+sql_condition;
            }
            else
            {
                return false;
            }
        });
    } else {
        window.location.href = 'index.php?c=service&m=service_output&export_type='+type+sql_condition;
    }
}
</script>