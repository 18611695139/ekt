<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:37
         compiled from contact_list.htm */ ?>
<input type="hidden" id="contact_cle_id" name="contact_cle_id" value="<?php echo $this->_tpl_vars['contact_cle_id']; ?>
"/>
<div id="contact_list"></div>
<div id="datagrid_contact"></div>
<div id="new_contact_panel"></div>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//设置列表
	$('#contact_list').datagrid({
		title:"",
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageList:[10],
		sortName:'con_if_main',
		sortOrder:'desc',//降序排列
		idField:'con_id',
		url:"index.php?c=contact&m=get_contact_list",
		queryParams:{"cle_id":"<?php echo $this->_tpl_vars['contact_cle_id']; ?>
"},
		frozenColumns:[[
		{title:"cle_id",field:"cle_id",hidden:true},
		{title:'操作',field:'opter_txt',width:50,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<span><a href='javascript:;' onclick=update_contact('"+rowData.con_id+"') title='编辑'><img src='./image/icon_edit.gif' border='0' height='16' width='16' /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick=delete_contact('"+rowData.con_id+"') title='删除'><img src='./image/icon_drop.gif' border='0' height='16' width='16'></a></span>";
		}},
		{title:"主联系人",field:'con_if_main',align:"CENTER",width:55,formatter:function(value,rowData,rowIndex){
			if(value == 1)
			{
				return  "<a href='###;' onclick=set_master_contact(0,'"+rowData.con_id+"');><img src='./image/p.gif' border='0'  align='absmiddle' title='主联系人'/></a>"
			}
			else
			{
				return  "<a href='###;' onclick=set_master_contact(1,'"+rowData.con_id+"');><img src='./image/record.gif' border='0' width='9' height='9'  align='absmiddle' title='联系人'/></a>"
			}
		}}
		]],
		columns:[[
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['con_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['con_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['con_display_field'][$this->_sections['key']['index']]['fields']; ?>
' , align:'CENTER', width:"<?php echo $this->_tpl_vars['con_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
", sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['con_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile'): ?>
			if(value)
			{
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				value = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='javascript:;' onclick = 'sys_dial_num(\""+rowData.con_mobile+"\")'; title='呼叫'  >"+value+"<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms(\""+rowData.con_mobile+"\")'; title='短信' ><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /></a><?php endif; ?>";
			}
			<?php elseif ($this->_tpl_vars['con_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mail'): ?>
			if(value)
			{
				return "<a href='mailTo:"+value+"' class='underline' style='color:blue;'>"+value+"</a>";
			}
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:"con_id" ,field:"con_id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#contact_list').datagrid('clearSelections');
		},
		toolbar:[
		{
			iconCls:'icon-add',
			text:'添加联系人',
			handler:function(){
				$('#new_contact_panel').window({
					href:"index.php?c=contact&m=new_contact&cle_id=<?php echo $this->_tpl_vars['contact_cle_id']; ?>
",
					width:700,
					top:150,
					title:"添加联系人",
					collapsible:false,
					minimizable:false,
					maximizable:false,
					resizable:false,
					cache:false,
					modal:true
				});
			}
		},'-',
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				$('#datagrid_contact').window({
					title: '显示列表设置',
					href:"index.php?c=datagrid_confirm&display_type=1",
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
		}
		]
	});
	
	var pager = $('#contact_list').datagrid('getPager');
	$(pager).pagination({showPageList:false});
});

//设置联系人类型
function set_master_contact(con_if_main,con_id)
{
	var str = "普通联系人";
	if(con_if_main == 1)
	{
		str = "主联系人";
	}

	var cle_id = $("#contact_cle_id").val();
	if(!con_id || !cle_id)
	{
		$.messager.alert("提示","<br>缺少必要参数，设置失败！","info");
		return false;
	}

	$.ajax({
		type:'POST',
		url: "index.php?c=contact&m=set_master_contact",
		data: {"con_if_main":con_if_main,"con_id":con_id,"cle_id":cle_id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				$('#contact_list').datagrid('reload');
			}
			else
			{
				$.messager.alert('错误',"<br>"+responce["message"],'error');
			}
		}
	});

}

// 删除联系人
function delete_contact(con_id)
{
	$.messager.confirm('提示',"<br>删除该联系人？", function(r){
		if(r)
		{
			$.ajax({
				type:'POST',
				url: "index.php?c=contact&m=delete_contact",
				data: {"con_id":con_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#contact_list').datagrid('reload');
					}
					else
					{
						$.messager.alert('错误',"<br>"+responce["message"],'error');
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

//编辑联系人
function update_contact(con_id)
{
	if(!con_id)
	{
		$.messager.alert('提示',"<br>缺少联系人参数",'info');
		return false;
	}

	$('#new_contact_panel').window({
		href:"index.php?c=contact&m=edit_contact&con_id="+con_id,
		width:700,
		top:150,
		title:"编辑联系人",
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		cache:false,
		modal:true
	});
}



</script>