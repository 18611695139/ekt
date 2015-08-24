<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:48
         compiled from user_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id='_search_panel'>
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm" >
    <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
	关键字：<input type="text" name="search_key_word" id="search_key_word" size="15" />&nbsp;&nbsp;&nbsp;&nbsp;
	部门：<input  id="department" name="department" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;
    <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
  </form>
</div>
<div id="user_list"></div><!--用户列表-->
<div id="user_panel"></div><!--用户编辑-->
<div id="user_add"></div><!-- 添加用户 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>  
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});

	$('#department').combotree({
		url:'index.php?c=department&m=get_department_tree',
		editable:true,
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#department').combotree('options').url = "index.php?c=department&m=get_department_tree";
			}
		}
	});

	$('#user_list').datagrid({
		title:'员工列表',
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'user_id',
		sortOrder:'DESC',//降序排列
		idField:'user_id',
		singleSelect:true,
		url:'index.php?c=user&m=list_user_query',
		frozenColumns:[[
		{title:'user_id',field:'user_id',hidden:true},
		{title:'操作',field:'oper_txt',width:80,align:'center',formatter:function(value,rowData,rowIndex)
		{
			var oper_txt = "<a href='javascript:;' onclick=edit_user('"+rowData.user_id+"') title='编辑'><img src='./image/icon_edit.gif' border='0' height='16' width='16' align='absmiddle'/></a>&nbsp;&nbsp;";
			if(rowData.user_id!='<?php echo $this->_tpl_vars['user_session_id']; ?>
')
			{
				if('<?php echo $this->_tpl_vars['role_session_id']; ?>
' > 1 && rowData.role_id==1)
				{
					return oper_txt;
				}
				oper_txt += "<a href='javascript:;' onclick=delete_user('"+rowData.user_id+"') title='删除'><img src='./image/icon_drop.gif' border='0' height='16' width='16' align='absmiddle'/></a>";
				
			}
			return oper_txt;

		}},
		{title:'姓名',field:'user_name',width:100,sortable:true,formatter:function(value,rowData,rowIndex)
		{
			return "<span><a href='javascript:;' onclick=edit_user('"+rowData.user_id+"') title='编辑'>"+value+"</a></span>";
		}},
		{title:'工号',field:'user_num',width:100,sortable:true}
		]],
		columns:[[
		{title:'角色',field:'role_name',width:120,sortable:true},
		{title:'部门',field:'dept_name',width:120,sortable:true},
		{title:'话机类型',field:'user_phone_type',width:120,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value == 1)
			return '自动识别';
			else if(value == 2)
			return '软话机';
			else
			return '话机';
		}},
		{title:'最后登陆时间',field:'user_last_login',width:130,sortable:true},
		{title:'最后登陆IP',field:'user_last_ip',width:120,sortable:true}
		]],
		toolbar:[
		{
			iconCls:'icon-add',
			text:'添加员工',
			handler:function(){
				$('#user_panel').window({
					title: '添加员工',
					href:"index.php?c=user&m=add_user",
					iconCls: "icon-add",
					top:5,
					width:550,
					closed: false,
					shadow:false,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false
				});
			}
		},'-',
		{
			iconCls:'icon-up',
			text:'导出CSV',
			handler:function(){
				window.location.href = 'index.php?c=user&m=output_user&export_type=csv';
			}
		},'-',
        {
            iconCls:'icon-up',
            text:'导出excel',
            handler:function(){
                window.location.href = 'index.php?c=user&m=output_user&export_type=excel';
            }
        }
		],
		onLoadSuccess: function(){
			$('#user_list').datagrid('clearSelections');
		}
	});
	var pager = $('#user_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/**
*打开修改用户信息窗口
*/
function edit_user(user_id)
{
	$('#user_panel').window({
		title: "编辑员工信息<span style='color:red;'>（提示：修改完该员工信息后,让该员工退出重登系统即可生效）</span>",
		href:"index.php?c=user&m=edit_user&user_id="+user_id,
		iconCls: "icon-edit",
		top:5,
		width:550,
		shadow:false,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

/**
* 关键字搜索
*/
function quick_search()
{
	var key_word = $("#search_key_word").val();
	var department	= $('#department').combotree('getValue');

	var queryParams = $('#user_list').datagrid('options').queryParams;
	queryParams.key_word = key_word;
	queryParams.dept_id = department;
	$('#user_list').datagrid('load');
}

/**
* 删除员工
*/
function delete_user(user_id)
{
	$.messager.confirm('提示', "是否删除该员工信息？<br><font style='color:red;'>删除员工信息将释放该员工所属数据</font>", function(r){
		if(r){
			$.ajax({
				type:'POST',
				url: "index.php?c=user&m=delete_user",
				data: {"user_id":user_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						$('#user_list').datagrid('load');
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

</script>