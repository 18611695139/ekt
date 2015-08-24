var selected_role_id = 0;
$(document).ready(function (){
	$("#tab tr")
	.css('cursor','pointer')
	.mouseover(function(){
		if($(this).attr('class')!='click_mous'){
			$(this).addClass('on_mous');
		}
	})
	.mouseout(function(){
		if($(this).attr('class')!='click_mous'){
			$(this).removeClass();
			$(this).addClass('out_mous');
		}
	})
	.click(function(){
		$(this).removeClass();
		$(this).addClass('click_mous');
		$(this).siblings().removeClass();
		select_role($(this).attr('role_id'));
	});

	$("#all_auth").click(function(){
		$("#maindiv input:not(.button)").attr('checked',true);
	});
});

$("#new_role").click(function(){
	$("#role_name").val('');
	$("#role_type").val(1);
	$("#role_div").css('display','');
	$("#do_type").val('insert');
});

$('#edit_role').click(function(){
	if(selected_role_id == 0)
	{
		$.messager.alert('提示','<br>请选择需要编辑的角色','error');
		return;
	}
	$("#role_div").css('display','');
	$("#do_type").val('update');
});


/*	插入角色 */
$("#save_role").click(function(){
	var role_name = $('#role_name').val();
	var role_type = $('#role_type').val();
	var role_type_name = $("#role_type").find('option:selected').text() ;
	var role_id	= selected_role_id;
	var do_type	= $('#do_type').val();
	if(do_type == 'insert')
	{
		var model = 'insert_role';
	}
	else
	{
		var model = 'update_role';
	}
	if(role_name != ''){
		$('#save_role').attr('disabled',true);
		$.ajax({
			url:      'index.php?c=role&m='+model,
			data:     {role_name:role_name,role_type:role_type,role_id:role_id},
			type:     "post",
			dataType: "json",
			success: function(responce){
				if(responce['error'] === 0)
				{
					$("#role_div").css('display','none');
					if(do_type == 'insert')
					{
						selected_role_id = role_id = responce['content'];
						var addtr = $("<tr id='tr_"+role_id+"' role_id="+role_id+"><td style='text-align:center;height:20' valign='bottom' >"+role_name+"</td><td style='text-align:center' valign='bottom' >"+role_type_name+"</td></tr>");
						addtr.css('cursor','pointer')
						.mouseover(function(){
							if($(this).attr('class')!='click_mous'){
								$(this).addClass('on_mous');
							}
						})
						.mouseout(function(){
							if($(this).attr('class')!='click_mous'){
								$(this).removeClass();
								$(this).addClass('out_mous');
							}
						})
						.click(function(){
							$(this).removeClass();
							$(this).addClass('click_mous');
							$(this).siblings().removeClass();
							select_role($(this).attr('role_id'));
						});
						$('#tab').append(addtr);
						select_role(role_id);
					}
					else
					{
						$('#tr_'+role_id).children('td:eq(0)').text(role_name);
						$('#tr_'+role_id).children('td:eq(1)').text(role_type_name);
					}
				}
				else
				{
					$.messager.alert('错误','<br>保存角色失败');
				}
				$('#save_role').attr('disabled',false);
			}
		});
	}
	$("#role_div").css('display','none');
});

/*	删除角色	*/
$("#delete_role").click(function(){
	if(selected_role_id)
	{
		if(selected_role_id==1)
		{
			$.messager.alert('失败','<br>首级角色不能删除，以免影响系统正常使用','error');
		}
		else
		{
			$.messager.confirm("删除确认","<br>确认要删除这个角色吗",function (r){
				if(r){
					$.ajax({
						url:      "index.php?c=role&m=delete_role",
						data:     {role_id: selected_role_id},
						type:     "post",
						dataType: "json",
						success: function(responce){
							if(responce["error"] === 0)
							{
								$("#tr_"+selected_role_id).remove();
								$("#maindiv input").attr('checked','');
								$("#role_div").css('display','none');
							}
							else
							{
								$.messager.alert('失败','<br>删除失败','error');
							}
						}
					});
				}
				$("#deletediv").css('display','none');
			});
		}
	}
	else
	{
		$.messager.alert('错误','<br>删除失败','error');
	}
});

function select_role(role_id){
	selected_role_id = role_id;
	$("#maindiv input").attr('checked',false);
	$.ajax({
		url:      "index.php?c=role&m=get_role_info",
		data:     {role_id: role_id},
		type:     "post",
		dataType: "json",
		success: function(responce){
			if(responce["error"] === 0)
			{
				var role_arr = responce['role_action_list'].split(',');
				if(role_arr == "all")
				{
					$("#maindiv input:not(.button)").attr('checked',true);
				}
				else
				{
					for(var i=0; i<role_arr.length-1;i++)
					{
						$("#"+role_arr[i]).attr('checked',true);
					}
				}
				/*  初始化checkbox */
				if($("#breakin").attr("checked") || $("#intercept").attr("checked"))
				{
					$("#chanspy").attr("checked",true);
					$("#chanspy").attr("disabled",true);
				}
				else
				{
					$("#chanspy").attr("disabled",false);
				}

				$("#role_name").val(responce['role_name']);
				$("#role_type").val(responce['role_type']);
			}
			else
			{
				$.messager.alert('错误','<br>角色设置失败','error');
			}
		}
	});
}

function set_role_action(){
	if(selected_role_id === 0)
	{
		$.messager.alert("提示","<br>请选择要编辑权限的角色","info");
	}
	else
	{
		var role_action_list = '';
		$("#maindiv input:checked").each(function(){
			if($(this).val() != 'on')
			{
				role_action_list += $(this).val()+',';
			}
		});

		$.ajax({
			url:      "index.php?c=role&m=set_role_action_list",
			data:     {"role_action_list": role_action_list,"role_id":selected_role_id},
			type:     "post",
			dataType: "json",
			success: function(responce){
				if(responce["error"] == 0)
				{
					$.messager.alert('成功', '<br>角色设置成功','info');
				}
				else
				{
					$.messager.alert('失败','<br>保存角色失败','error');
				}
			}
		});
	}
}

function toggle_auth_class(id){
	if(id=="wincall")
	{
		if($("#wincall").attr("checked"))
		{
			$("#chanspy").attr("checked",true);
			$("#chanspy").attr("disabled",true);
		}
		else
		{
			$("#chanspy").attr("disabled",false);
		}
	}
	if($("#"+id).attr('checked')){
		$("#td_"+id+" input").attr('checked',true);
	}else{
		$("#td_"+id+" input").attr('checked',false);
	}
}

/*	控制监听checkbox */
function toggle_auth(key){
	if(key=="breakin" || key=="intercept")
	{
		if($("#breakin").attr("checked") || $("#intercept").attr("checked"))
		{
			$("#chanspy").attr("checked",true);
			$("#chanspy").attr("disabled",true);
		}
		else
		{
			$("#chanspy").attr("disabled",false);
		}
	}
}