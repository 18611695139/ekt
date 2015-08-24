<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:41
         compiled from product_class_info.htm */ ?>
<?php if (! $this->_tpl_vars['setting_product_class']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<div class="main-div product_height" style='background:#ffffff;'>
<table><tbody><tr>
<td style='width:15%'></td>
<td style='width:20%'>
<div style="height:220px;">
<ul id="p_c_tree" class="easyui-tree"></ul>
</div>
</td>
<td style='width:40%'>
<div  id='action_product' style="height:220px;">
<div id="add_department" title="添加分类" >
<table border="0">
<tbody>
<tr>
<td align="right" ><label for="before_product">上级分类：</label></td>
<td align="left">
<input type="text" name="before_product" id="before_product" disabled />
</td>
</tr>
<tr>
<td  align="right" ><label for="add_p_c_name">分类名称：</label></td>
<td align="left">
<input type="text" name="add_p_c_name" id="add_p_c_name"/>
</td>
</tr>
<tr>
<td align="right">&nbsp;</td><td align="left">
<a class="easyui-linkbutton" iconCls="icon-add" href="javascript:void(0)"  onclick="add_p_c()" id="add_p_c_btn" >添加</a>
<a href="#"  class="easyui-linkbutton" iconCls="icon-cancel"  onclick="cansal_action()" id="add_btn">取消</a>
</td>
</tr>
</tbody>
</table>
</div>

<div id="edit_department"  title="修改分类">
<table border="0">
<tbody>
<tr>
<td  align="right" ><label for="this_name">当前分类：</label></td><td align="left">
<input type="text" name="this_name" id="this_name" disabled />
</td>
</tr>
<tr>
<td  align="right" ><label for="edit_p_c_name">修改名称：</label></td><td align="left">
<input type="text" name="edit_p_c_name" id="edit_p_c_name"/>
</td></tr>
<tr><td align="right">&nbsp;</td><td align="left">
<a class="easyui-linkbutton" iconCls="icon-edit" href="javascript:void(0)"  onclick="edit_p_c()" >修改</a>
<a href="###"  class="easyui-linkbutton" iconCls="icon-cancel"  onclick="cansal_action()" >取消</a>
</td>
</tr>
</tbody>
</table>
</div>

<div id="delete_department" title="删除分类">
<table border="0">
<tbody>
<tr>
<td  align="right" ><label for="del_this_name">当前分类：</label></td><td align="left">
<input type="text" name="del_this_name" id="del_this_name" disabled />
</td></tr>
<tr><td align="right">&nbsp;</td>
<td align="left">
<a class="easyui-linkbutton" iconCls="icon-del" href="javascript:void(0)"  onclick="del_p_c()" >删除</a>
</td></tr>
</tbody></table>
</div>

</div>
</td></tr>
</tbody></table>
</div>
<?php if ($this->_tpl_vars['setting_product_class']): ?>
<div style='padding:1px 10px;'>提示：您也可以通过以下路径进行设置：</div>
<div style='padding:5px 60px 10px 60px;'><b>产品管理</b> => <b>产品分类</b></div>
<?php else: ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<script type="text/javascript" language="javascript">
$(function(){
	<?php if (! $this->_tpl_vars['setting_product_class']): ?>
	$('.product_height').css({height:""+get_list_height_fit_window()+""});//高度
	<?php endif; ?>
	$('#action_product').tabs({
		width:300,
		border:false,
		plain:true
	});
	
	$('#p_c_tree').tree({
		url : "index.php?c=product&m=get_product_class_tree",
		lines:true,
		onClick : function(p_c){
			$('#p_c_tree').tree('expand',p_c.target);//改变目录状态
			var select_name = ($('#p_c_tree').tree('getSelected')).text;
			/*添加 - 分类名称*/
			$('#add_p_c_name').val('');
			/*添加 -  上级分类*/
			$('#before_product').val(select_name);
			/*编辑 - 当前分类*/
			$('#this_name').val(select_name);
			/*编辑 - 修改分类*/
			$('#edit_p_c_name').val('');
			/*删除 - 当前分类*/
			$("#del_this_name").val(select_name);
		},
		onLoadSuccess:function(){
			//默认选中根节点
			var node = $('#p_c_tree').tree('find', 1);
			$('#p_c_tree').tree('select', node.target);
			var select_name = ($('#p_c_tree').tree('getSelected')).text;
			/*添加 - 分类名称*/
			$('#add_p_c_name').val('');
			/*添加 -  上级分类*/
			$('#before_product').val(select_name);
			/*编辑 - 当前分类*/
			$('#this_name').val(select_name);
			/*编辑 - 修改分类*/
			$('#edit_p_c_name').val('');
			/*删除 - 当前分类*/
			$("#del_this_name").val(select_name);
		}
	});
});

/**
*取消
*/
function cansal_action()
{
	/*添加 - 分类名称*/
	$('#add_p_c_name').val('');
	/*编辑 - 修改分类*/
	$('#edit_p_c_name').val('');
}

/**
*添加一个下级分类
*/
function add_p_c()
{
	var p_c_p = $('#p_c_tree').tree('getSelected');
	if(!p_c_p)
	{
		$.messager.alert('Notice','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a>请指定当前分类','warning');
		return;
	}
	var p_c_p_id = ($('#p_c_tree').tree('getSelected')).id;
	var p_c_p_deep = ($('#p_c_tree').tree('getSelected')).attributes['deep'];
	p_c_p_deep = parseInt(p_c_p_deep);
	var p_c_pid = ($('#p_c_tree').tree('getSelected')).attributes['pid'];

	var p_c_name = $('#add_p_c_name').val();
	if(p_c_name == "")
	{
		$.messager.alert('Notice','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a>请指定下级分类名称!','warning');
		return;
	}
	$('#add_p_c_btn').attr('disabled',true);

	$.ajax({
		url : "index.php?c=product&m=insert_product_class",
		data : {p_c_p_id:p_c_p_id,p_c_name:p_c_name,p_c_pid:p_c_pid},
		type : 'post',
		dataType : 'json',
		success : function(responce){
			if(responce['error'] === 0)
			{
				var p_c_id = responce['content'];
				var p_c_attr = {};
				p_c_attr['deep'] = parseInt(p_c_p_deep)+1;
				p_c_attr['pid'] = p_c_p_id;
				$('#p_c_tree').tree('append',{
					parent : p_c_p ? p_c_p.target : null,
					data : [{id:p_c_id,text:p_c_name,attributes:p_c_attr}]
				});
				/*添加 - 分类名称*/
				$('#add_p_c_name').val('');
				$('#p_c_tree').tree('expand',p_c_p.target);
			}
			else
			{
				$.messager.alert('Notice','增加分类失败','warning');
			}
			$('#add_p_c_btn').attr('disabled',false);
		}
	});
}

/**
*编辑分类
**/
function edit_p_c(){
	var p_c	= $('#p_c_tree').tree('getSelected');
	var p_c_id	= $('#p_c_tree').tree('getSelected').id;
	var p_c_name	= $('#edit_p_c_name').val();

	if(!p_c || p_c_name == "")
	{
		$.messager.alert('Notice','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a>请指定当前分类和新名称!','warning');
		return false;
	}
	$.ajax({
		url : 'index.php?c=product&m=update_product_class',
		data : {p_c_id:p_c_id, p_c_name: p_c_name},
		dataType : 'json',
		type : 'post',
		success : function(responce){
			if(responce['error'] === 0)
			{
				p_c.text = p_c_name;
				$('#p_c_tree').tree('update',p_c);

				var select_name = ($('#p_c_tree').tree('getSelected')).text;
				/*添加 - 分类名称*/
				$('#add_p_c_name').val('');
				/*添加 -  上级分类*/
				$('#before_product').val(select_name);
				/*编辑 - 当前分类*/
				$('#this_name').val(select_name);
				/*编辑 - 修改分类*/
				$('#edit_p_c_name').val('');
				/*删除 - 当前分类*/
				$("#del_this_name").val(select_name);
			}
		}
	});
}

/**
*删除节点
**/
function del_p_c()
{
	var p_c = $('#p_c_tree').tree('getSelected');
	if(!p_c)
	{
		$.messager.alert('提示','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a>请指定需要删除的分类','warning');
		return false;
	}
	$.messager.confirm('提示','<br><a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a> 该分类下所有子类及所属产品会一同删除，您确定删除该分类吗？',function(r){
		if(r)
		{
			var p_c_id = p_c.id;
			var p_c_deep = p_c.attributes['deep'];
			var p_c_pid = p_c.attributes['pid'];
			if(p_c_pid!=0)
			{
				$.ajax({
					url : 'index.php?c=product&m=delete_product_class',
					data : {'p_c_id':p_c_id,'p_c_deep':p_c_deep,'p_c_pid':p_c_pid},
					dataType : 'json',
					type : 'post',
					success : function(responce){
						if(responce['error'] === 0)
						{
							$('#p_c_tree').tree('remove',p_c.target);

							/*添加 -  上级分类*/
							$('#before_product').val('');
							/*编辑 - 当前分类*/
							$('#this_name').val('');
							/*删除 - 当前分类*/
							$("#del_this_name").val('');
						}
						else
						{
							$.messager.alert('Notice',responce['message'],'warning');
						}
					}
				});
			}
			else
			{
				$.messager.alert('错误','不能删除顶级类','error');
			}

		}
	});
}
</script>