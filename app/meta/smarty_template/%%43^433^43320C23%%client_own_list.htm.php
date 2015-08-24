<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:31
         compiled from client_own_list.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_own_list.htm', 34, false),)), $this); ?>
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
		href:'index.php?c=client&m=base_search&flag=my_client',
		onLoad:function(width, height){
			$('#client_list').datagrid('resize',{
				height:get_list_height_fit_window('_search_panel')
			});
		}
	});

	//设置列表
	$('#client_list').datagrid({
		title:"我的客户",
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
		url:"index.php?c=client&m=list_client_query",
		queryParams:{"user_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'操作',field:'opter_txt',width:50,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"','link'); title='业务受理'>【受理】</a>";
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
			<?php if ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_name'): ?>
			return "<span><a href='###' onclick=link_client_accept('"+rowData.cle_id+"','link'); class='underline' title='业务受理'>"+value+"</a></span>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone2' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone3'): ?>
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"','dial','"+value+"'); class='underline' title='业务受理'>"+show_real+"</a>&nbsp;&nbsp;<a href='javascript:;' onclick=link_client_accept('"+rowData.cle_id+"','dial','"+value+"'); title='呼叫'><img src='./image/phone.png' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms(\""+value+"\")' title='发短信'><img src='./image/message.png'  /></a><?php endif; ?>";
			}
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_address'): ?>
			return "<span class='show-tooltip'>"+value+"</span>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_remark'): ?>
			return "<span class='show-tooltip'>"+value+"</span>";
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
			$('.show-tooltip').tooltip({
				trackMouse:true,
				position:'right',
				onShow: function(){
					var content = '<div style="width:180px;">'+$(this).text().replace(/\n/g, "<br>")+'</div>';
					$(this).tooltip('update',content);
				}
			});
		},
		toolbar:[
		<?php if ($this->_tpl_vars['power_client_add']): ?>
		{
			iconCls:'icon-add',
			text:'添加客户',
			handler:function(){
				window.parent.addTab('添加客户',"index.php?c=client&m=new_client","menu_icon");
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_delete']): ?>
		{
			iconCls:'icon-del',
			text:'删除客户',
			handler:function(){
				remove_client();
			}
		} ,'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_release']): ?>
		{
			iconCls:'icon-release',
			text:"<span title = '只能放弃属于自己的数据' >选中放弃</span>",
			handler:function(){
				if_can_release();
			}
		},"-",
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_export']): ?>
                                    {
                                        iconCls:'icon-up',
                                                text:'导出CSV',
                                            handler:function(){
                                        output_client('csv');
                                    }
                                    },'-',
                                    {
                                        iconCls:'icon-up',
                                        text:'导出excel',
                                        handler:function(){
                                            output_client('excel');
                                        }
                                    },'-',
		<?php endif; ?>
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
var delete_client_relative = <?php echo ((is_array($_tmp=@$this->_tpl_vars['delete_client_relative'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
;





//得到选中项的 客户ID
var selected_name = "";
var selected_num  = 0;
function getSelections()
{
	selected_name = "";
	selected_num  = 0;
	var ids = [];
	var rows = $('#client_list').datagrid('getChecked');
	for(var i=0;i<rows.length;i++)
	{
		if(i<5){
			selected_name +="【"+ rows[i].cle_name+"】 ";
		}
		ids.push(rows[i].cle_id);
		selected_num++;
	}
	selected_name += "......";
	return ids.join(',');
}

//判断是否能删除客户
function remove_client()
{
	var ids = getSelections();
	if(ids == '')
	{
		$.messager.alert('错误','<br>请选择要删除的客户数据','error');
		return;
	}
	var _relative_deal = '';
	if(delete_client_relative==1)
	{
		_relative_deal = "，其他模块与被删客户相关的数据将保留";
	}
	else if(delete_client_relative==2)
	{
		_relative_deal = "，<span style='color:red;'>其他模块与被删客户相关的数据也一同删除</span>";
	}
	$.messager.confirm('提示', '删除'+selected_num+'条数据'+_relative_deal, function(r){
		if(r)
		{
			//删除客户数据
			delete_client(ids);
		}
		else
		{
			return false;
		}
	});
}

//删除客户数据
function delete_client(cle_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=delete_client",
		data: {"cle_id":cle_id},
		dataType: "json",
		success: function(responce){

			if(responce["error"] == 0)
			{
				$.messager.alert('提示',"<br>"+responce['content'],'info');
				$('#client_list').datagrid('load');
			}
			else
			{
				$.messager.alert('错误','删除失败','error');

			}

		}
	});
}

//判断是否能释放
function if_can_release()
{
	var ids = getSelections();
	if(ids == '')
	{
		$.messager.alert('提示(只能放弃属于自己的数据)','<br>请选择要放弃的客户数据','info');
		return;
	}
	$.messager.confirm('提示(只能放弃属于自己的数据)', '<br>放弃选中数据？', function(r){
		if(r){
			//放弃客户数据 by cle_id
			release_client_by_id(ids);
		}
		else
		{
			return false;
		}
	});
}

//放弃客户数据 - 只能放弃属于自己的数据(数据所属人为当前坐席)
// cle_id:客户ID
function release_client_by_id(cle_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=client&m=release_client_by_id",
		data: {"cle_id":cle_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$.messager.alert('提示(只能放弃属于自己的数据)',"<br>已放弃"+responce['content']+"条数据",'info');
				$('#client_list').datagrid('load');
			}
			else
			{
				$.messager.alert('错误','操作失败，提示：只能放弃自己的数据','error');
			}
		}
	});
}

/*客户管理列表 - 业务受理*/
//cookie中记录当前客户列表中的 客户ID ； type: dial号码跳转  link姓名跳转
function link_client_accept(cle_id,type)
{
	var list_option       = $('#client_list').datagrid('options');
	//列表参数
	var client_list_param = [];
	client_list_param[0]  = list_option.pageNumber;
	client_list_param[1]  = list_option.pageSize;
	client_list_param[2]  = list_option.sortName;
	client_list_param[3]  = list_option.sortOrder
	set_cookie("client_list_param",client_list_param.join(","));

	var row_data     = $('#client_list').datagrid("getRows");
	var table_cle_id = [];
	$.each(row_data,function(key,value){
		table_cle_id[key] = value.cle_id;
	});
	//记录当前列表显示数据的 客户ID
	set_cookie("last_next_cleID",table_cle_id.join(","));

	//当前列表的检索条件
	var queryParams   = list_option.queryParams;
	var param_str = json2str(queryParams);
	set_cookie('accept_condition',param_str);

	if( type == 'dial' )
	{
		// 外呼电话电话号码
		var phone = arguments[2];
		sys_dial_num(phone);
	}
	// 跳转 - 业务受理
	window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=1&cle_id='+cle_id,'menu_icon');
}

//基本搜索
function base_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=base_search&flag=my_client');
}

//高级搜索
function advanced_search()
{
	$('#_search_panel').panel('open').panel('refresh','index.php?c=client&m=advance_search&flag=my_client');
}

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

//导出
function output_client(type)
{
	//列表参数
	var datagrid_param = $('#client_list').datagrid('options');
	var queryParams    = datagrid_param.queryParams;
	//排序
	queryParams.sortName  = datagrid_param.sortName;
	queryParams.sortOrder = datagrid_param.sortOrder;
	queryParams.total     = $('#client_list').datagrid('getData').total;
	var sql_condition  = json2url(queryParams);
	if( sql_condition )
	{
		sql_condition = "&"+sql_condition;
	}
    if(type == 'excel' && queryParams.total>10000)
    {
        $.messager.confirm('数据量大，建议用【导出CSV】导出数据', '<br>确定继续导出excel吗？', function(r){
            if(r){
                window.location.href = 'index.php?c=client&m=data_output&export_type='+type+sql_condition;
            }
            else
            {
                return false;
            }
        });
    } else {
        window.location.href = 'index.php?c=client&m=data_output&export_type='+type+sql_condition;
    }
}

</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>