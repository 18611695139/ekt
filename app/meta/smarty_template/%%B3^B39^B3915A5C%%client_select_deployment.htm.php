<?php /* Smarty version 2.6.19, created on 2015-08-07 10:35:08
         compiled from client_select_deployment.htm */ ?>
<div region="center" border="false" style="padding-left:40px;padding-top:10px;padding-bottom:10px;background:#fff;border:1px solid #ccc;" class='form-inline'>
<table>
		<tr><td>
		<select id='deployment_way' name='deployment_way' onchange='select_user_dept()' style="width:120px;">
			<?php if ($this->_tpl_vars['deployment_dept']): ?><option>指定部门</option><?php endif; ?>
			<option>指定人员</option>
		</select></td><td>
		<?php if ($this->_tpl_vars['deployment_dept']): ?><span id='res_dept'><input id="sys_dept" name="sys_dept" type="text" value=""  /></span><?php endif; ?>
		<span id='res_user'><input type='text' id='sys_name' name='sys_name' value='' title='支持模糊搜索'/></span>
		<input id='user_can_batch' name='user_can_batch' type='hidden' value='无限制' />
		</td></tr>
</table>
</div>
<div region="south" border="false" style="text-align:right;height:15px;line-height:15px;margin-top:10px" id='resource_button'>
	<span style='color:red;' id='user_batch_msg'></span>
	<button class="btn btn-primary" onclick="deployment_by_id();" id="submit_member" title="调配数据">
         <span class="glyphicon glyphicon-saved"></span> 确 定
    </button>
    <img id="loading" src="./image/loading.gif" style="display:none;">
</div>

<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	<?php if ($this->_tpl_vars['deployment_dept']): ?>
	$('#res_dept').css("display",'block');
	$('#res_user').css("display",'none');
	<?php else: ?>
	$('#res_dept').css("display",'none');
	$('#res_user').css("display",'block');
	<?php endif; ?>
	//部门
	$('#sys_dept').combotree({
		url:'index.php?c=department&m=get_department_tree',
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#sys_dept').combotree('options').url = "index.php?c=department&m=get_department_tree";
			}
		}
	});
	//坐席
	$('#sys_name').combobox({
		url:'index.php?c=user&m=get_user_box',
		valueField:'user_id',
		textField:'user_name_num',
		mode:'remote',
		hasDownArrow:false,
		onSelect:function()
		{
			get_this_user_batch();
		}
	});
});

function select_user_dept()
{
	if($('#deployment_way').val() == '指定部门')
	{
		$('#user_batch_msg').html("");
		$('#submit_member').attr('disabled',false);
		$('#res_dept').css("display",'block');
		$('#res_user').css("display",'none');
	}
	else
	{
		$('#res_dept').css("display",'none');
		$('#res_user').css("display",'block');
	}
}

//数据调配
function deployment_by_id()
{
	/*部门*/
	$('#submit_member').attr('disabled',true);
	$("#loading").show();
	var dept_id = 0;
	<?php if ($this->_tpl_vars['deployment_dept']): ?>
	dept_id = $("#sys_dept").combotree('getValue');
	<?php endif; ?>
	/*人员*/
	var user_id = $("#sys_name").combobox("getValue");
	if(dept_id.length==0 && user_id.length==0)
	{
		$("#loading").hide();
		$('#user_batch_msg').html("<img src='./themes/default/icons/no.png' /> <b>操作失败</b>");
		setTimeout(function (){
			$('#user_batch_msg').html("");
			$('#submit_member').attr('disabled',false);
		},800);
		return;
	}

	var cle_id = getSelections();
	if(cle_id == '')
	{
		$("#loading").hide();
		$('#user_batch_msg').html("<img src='./themes/default/icons/no.png' /> <b>请选择需要分配的数据</b>");
		setTimeout(function (){
			$('#user_batch_msg').html("");
			$('#submit_member').attr('disabled',false);
		},1000);
		return;
	}

	if(user_id.length!=0)
	{
		var user_can_batch = $('#user_can_batch').val();
		if(user_can_batch != '无限制')
		{
			var cle_ids = new Array()
			cle_ids = cle_id.split(',');
			if(cle_ids.length > user_can_batch)
			{
				$("#loading").hide();
				$('#user_batch_msg').html("<img src='./themes/default/icons/no.png' /> <b>该员工现有客户加上将分配数量超过客户限制数量</b>");
				setTimeout(function (){
					$('#user_batch_msg').html("");
					$('#submit_member').attr('disabled',false);
				},2000);
				return;
			}
		}
	}
	$.ajax({
		type:'POST',
		url: "index.php?c=client_resource&m=deployment_by_id",
		data: {'user_id':user_id,'cle_id':cle_id,'dept_id':dept_id},
		dataType: "json",
		success: function(responce){
			$("#loading").hide();
			if(responce['error']=='0')
			{
				$('#user_batch_msg').html("<img src='./themes/default/icons/ok.png' />&nbsp;分配成功");

				setTimeout(function ()	{
					$('#resource_deployment').window('close');
					$('#client_list').datagrid('clearSelections');
					$("#client_list").datagrid("reload");
					$('#submit_member').attr('disabled',false);
				},500);

			}
			else
			{
				$('#user_batch_msg').html("<img src='./themes/default/icons/no.png' /> <b>操作失败</b>");
				$('#submit_member').attr('disabled',false);
			}
		}
	});
}

/*获取该员工现有数据及可分配数据数*/
function get_this_user_batch()
{
	$('#user_batch_msg').html("");
	$('#submit_member').attr('disabled',false);
	/*人员*/
	var user_id = $("#sys_name").combobox("getValue");
	$.ajax({
		type:'POST',
		url: "index.php?c=client_resource&m=get_this_user_batch",
		data: {'user_id':user_id},
		dataType: "json",
		success: function(responce){
			if(responce['error']=='0')
			{
				$('#submit_member').attr('disabled',false);
				if(responce['content']!=true)
				{
					$('#user_batch_msg').html("提示：当前还可给该员工分配 <b>"+responce['content']['batch_amount']+"</b>条数据");
					$('#user_can_batch').val(responce['content']['batch_amount']);
					if(responce['content']['batch_amount'] ==0)
					{
						$('#submit_member').attr('disabled',true);
					}
				}
				else
				{
					$('#user_can_batch').val('无限制');
				}
			}
			else
			{
				$('#submit_member').attr('disabled',true);
			}
		}
	});
}
</script>