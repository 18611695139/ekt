<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:35
         compiled from client_service.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_service.htm', 17, false),)), $this); ?>
<div id="service_list"></div>
<div id="datagrid_service"></div>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//设置列表
	$('#service_list').datagrid({
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageList:[10],
		sortName:'serv_accept_time',
		sortOrder:'desc',//降序排列
		idField:'serv_id',
		url:"index.php?c=service&m=get_service_list&write_cookie=2",
		queryParams:{"cle_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['service_cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
"},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:"cle_id",field:"cle_id",hidden:true},
		{title:'操作',field:'opter_txt',width:60,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='javascript:;' onclick=edit_service('"+rowData.serv_id+"') title='受理服务'>【受理】</a>";
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
		<?php if ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] != 'cle_phone' && $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] != 'cle_name'): ?>
		{title: "<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields']; ?>
' , align:'CENTER',width:"<?php echo $this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile'): ?>
			if(value)
			{
				var real_phone = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				value = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='javascript:;' onclick = sys_dial_num('"+value+"'); title='呼叫'  >"+value+"<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick=sys_send_sms("+real_phone+"); title='短信' ><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /></a><?php endif; ?>";
			}
			<?php elseif ($this->_tpl_vars['serv_display_field'][$this->_sections['key']['index']]['fields'] == 'serv_content'): ?>
			return "<a href='javascript:;' onclick=edit_service('"+rowData.serv_id+"') title='受理'>"+value+"</a>";
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endif; ?>
		<?php endfor; endif; ?>
		{title:"serv_id",field:"serv_id",hidden:true}
		
		]],
		onLoadSuccess: function(){
			$('#service_list').datagrid('clearSelections');
			$('#service_list').datagrid('clearChecked');
		},
		toolbar:[
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
		} ,'-'
		<?php endif; ?>
		]
	});
	
	var pager = $('#service_list').datagrid('getPager');
	$(pager).pagination({showPageList:false});
})

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
</script>