<?php /* Smarty version 2.6.19, created on 2015-07-21 11:09:49
         compiled from model_list.htm */ ?>
<div align="LEFT">
<b>请选择要导入的数据项</b>
  <a id="add_model" href="javascript:void(0)" class="easyui-linkbutton" title="添加一个新模板" iconCls="icon-add" >添加模板</a>
  <a id="update_model" href="javascript:void(0)" class="easyui-linkbutton" title="修改模板" iconCls="icon-edit">修改模板</a>
</div>
 <!--    模板信息 -->
<div>
 <iframe style="width:100%;height:400px;border:0px;" id="iframe_select_model" src="index.php?c=model&m=select_model&model_id=0&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
"  frameborder="application/msexcel"></iframe>
</div>
<div align="center">
  <a id="next" href="javascript:void(0)" class="easyui-linkbutton" title="选择导入数据字段" iconCls="icon-ok">确  定</a>
</div>
<div id="add_new_model"></div>
<script language="JavaScript" type="text/javascript">
//删除模板信息
function delete_model(model_id)
{
	if(!model_id)
	{
		$.messager.alert('提示', '<br>请选择需要删除的模板！', 'info');
	}
	else
	{
		$.messager.confirm('提示', '<br>您确定删除该模板吗？', function(r){
			if (r)
			{
				$.ajax({
					type:"POST",
					url:'index.php?c=model&m=delete_model',
					data:{"model_id":model_id},
					dataType:'json',
					success:function (responce){
						if(responce['error']=='0'){
							//刷新iframe
							refresh_iframe(0);
						}
						else
						{
							$.messager.alert('错误','操作失败','error');
						}
					}
				});
			}
		});
	}
}

//添加模板
$('#add_model').click(function(){
	$('#add_new_model').window({
		title: '添加模板(选择数据导入项并调整顺序)',
		href:"index.php?c=model&m=add_model&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
",
		iconCls: "icon-add",
		top:50,
		closed: false,
		height: 477,
		width:400,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
});

//修改模板
$('#update_model').click(function(){
	var model_id = GetValue("iframe_select_model","model_id");
	if(model_id>0){
		$('#add_new_model').window({
			title: '修改模板(选择数据导入项并调整顺序)',
			href:"index.php?c=model&m=edit_model&model_id="+model_id+"&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
",
			iconCls: "icon-edit",
			top:50,
			closed: false,
			height: 477,
			width:400,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			cache:false
		});
	}else{
		$.messager.alert('提示', '<br>请选择需要修改的数据模板！', 'info');
	}
});

//下一步
$('#next').click(function(){
	var model_id = GetValue("iframe_select_model","model_id");
	if(!(model_id>0)){
		$.messager.alert('提示', "<br>请选择数据模板！","info");
	}else{
		//刷新iframe
		refresh_choose_model(model_id);
		$("#selected_system_model").val(model_id);
		$('#display_system_model').window('close');
	}
});


//刷新iframe
function refresh_iframe(model_id)
{
	$('#iframe_select_model').attr("src","index.php?c=model&m=select_model&model_id="+model_id+"&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
");
}

//父窗口，得到子窗口的值
//ObjectID是窗口标识，ContentID是元素ID
function GetValue(ObjectID,ContentID)
{

	var IsIE = (navigator.appName == 'Microsoft Internet Explorer')
	if(IsIE)
	{//如果是IE
		return document.frames(ObjectID).document.getElementById(ContentID).value;
	}
	else
	{//如果是FF
		return document.getElementById(ObjectID).contentDocument.getElementById(ContentID).value;
	}
}
</script>