$(document).ready(function(){
	var url = 'index.php?c=user&m=get_user_box';
	//部门
	$('#dept_id_search').combotree({
		url:'index.php?c=department&m=get_department_tree',
		onClick:function(node){
			$(this).tree('expand', node.target);
			new_url = url+'&dept_id_search='+$('#dept_id_search').combotree('getValue');
			if(node.attributes!=1)
			user_combobox(new_url);//所属人
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#dept_id_search').combotree('options').url = "index.php?c=department&m=get_department_tree";

			}
		}
	});

	//所属人
	user_combobox(url);

});

/*清空部门*/
function clear_dept()
{
	$('#dept_id_search').combotree('clear');
	user_combobox('index.php?c=user&m=get_user_box');
}

/*受理人  处理人*/
function user_combobox(url)
{
	//受理人
	$('#user_id_search').combobox({
		url:url,
		valueField:'user_id',
		textField:'user_name_num',
		mode:'remote'
	});
	//处理人
	$('#deal_user_id_search').combobox({
		url:url,
		valueField:'user_id',
		textField:'user_name_num',
		mode:'remote'
	});
}

//检索
function quick_search()
{
	var search_data = {};
	search_data.service_type      = $("input:radio[name='serv_type_search']:checked").val();//服务类型
	search_data.service_state     = $("input:radio[name='serv_state_search']:checked").val();//状态
	search_data.cle_name          = $("#cle_name_search").val();//客户姓名
	search_data.cle_phone         = $("#cle_phone_search").val();//客户电话
	if(power_use_contact!=1)
	{
		search_data.con_name          = $("#con_name_search").val();//联系人姓名
		search_data.con_mobile        = $("#con_mobile_search").val();//联系人电话
	}
	search_data.accept_time_search_start = $("#accept_time_search_start").val();//受理时间开始
	search_data.accept_time_search_end   = $("#accept_time_search_end").val();//受理时间结束
	search_data.user_id_search           = $("#user_id_search").combobox("getValue");//受理人
	search_data.deal_time_search_start   = $("#deal_time_search_start").val();//处理时间开始
	search_data.deal_time_search_end     = $("#deal_time_search_end").val();////处理时间结束
	search_data.deal_user_id_search      = $("#deal_user_id_search").combobox("getValue");//处理人
	search_data.serv_content       = $("#serv_content_search").val();//内容
	search_data.serv_remark        = $("#serv_remark_search").val();//备注
	/*处理部门*/
	if(role_type ==1)
	{
		search_data.dept_id = $("#dept_id_search").combotree('getValue');
	}
	//自定义 field_id 字段 make逻辑条件 field_content 搜索内容
	var field_data = {};
	for(var x=1; x<=6;x++)
	{
		if(  $("#check_"+x+"").attr('checked') )
		{
			var content = $('#f_c_'+x+'').val();
			var make = $('#make'+x+'').val();
			var field_confirm = $('#field_confirm'+x+'').val();
			var type = '';//类型
			if(content == 'jl_three')
			{
				field_data[x+'_1'] = {'field_id':field_confirm,'make':make,'field_content':$('#f_c_'+x+'_1').val(),'type':'jl','jb':'1'};
				field_data[x+'_2'] = {'field_id':field_confirm,'make':make,'field_content':$('#f_c_'+x+'_2').val(),'type':'jl','jb':'2'};
				field_data[x+'_3'] = {'field_id':field_confirm,'make':make,'field_content':$('#f_c_'+x+'_3').val(),'type':'jl','jb':'3'};
			}
			else if(content == 'jl_two')
			{
				field_data[x+'_1'] = {'field_id':field_confirm,'make':make,'field_content':$('#f_c_'+x+'_1').val(),'type':'jl','jb':'1'};
				field_data[x+'_2'] = {'field_id':field_confirm,'make':make,'field_content':$('#f_c_'+x+'_2').val(),'type':'jl','jb':'2'};
			}
			else
			{
				field_data[x] = {'field_id':field_confirm,'make':make,'field_content':content,'type':type};
			}
		}
	}
	search_data.field_confirm_values = json2str(field_data);

	$('#service_list').datagrid('options').queryParams = {};
	var queryParams = $('#service_list').datagrid('options').queryParams;
	$.each(search_data,function(field,value){
		if(value != undefined && value.length != 0 )
		{
			queryParams[field] = value;
		}
	});
	$('#service_list').datagrid('load');
}

//显示自定义信息
function show_field_detail(a)
{
	var parent_id = $('#field_confirm'+a+'').val();
	if(parent_id.length == 0)
	{
		$('#check_'+a+'').removeAttr("checked");
		$('#field_content'+a+'').html("<input type='text' id='f_c_"+a+"' name='f_c_"+a+"' value=''/>");
	}
	else
	{
		$('#check_'+a+'').attr("checked","true");
		$.ajax({
			type:"POST",
			url:'index.php?c=field_confirm&m=get_field_detail',
			data:{parent_id:parent_id},
			dataType:'json',
			success:function (responce){
				if(responce['error']=='0')
				{
					if(responce['content'] == '')
					{
						$('#field_content'+a+'').html("<input type='text' id='f_c_"+a+"' name='f_c_"+a+"' value=''/>");
					}
					else
					{
						var option = '';
						if(responce['content']['data_type']==2)//下拉
						{
							for(i=0;i<responce['content']['info'].length;i++)
							{
								option += "<option value='"+responce['content']['info'][i]+"'>"+responce['content']['info'][i]+"</option>";
							}
							$('#field_content'+a+'').html("<select id='f_c_"+a+"' name='f_c_"+a+"'><option value='' selected>--请选择--</option>"+option+"</select>");
						}
						else if(responce['content']['data_type']==4||responce['content']['data_type']==7)//级联jl_type
						{
							for(i=0;i<responce['content']['info'].length;i++)
							{
								option += "<option value='"+responce['content']['info'][i]+"'>"+responce['content']['info'][i]+"</option>";
							}
							$.each(responce['content']['info'],function(id,value){
								option += "<option value='"+id+"'>"+value+"</option>";  //添加一项option
							});
							if(responce['content']['jl_series']==3)
							{
								$('#field_content'+a+'').html("<input type='hidden' id='f_c_"+a+"' name='f_c_"+a+"' value='jl_three' />\
									<select id='f_c_"+a+"_1' name='f_c_"+a+"_1' style='width:71px;' onchange=change_comfirm_jl('f_c_"+a+"_1','f_c_"+a+"_2',2,"+parent_id+",'f_c_"+a+"_3')>\
										<option value=''>--请选择--</option>"+option+"\
									</select>\
									<select id='f_c_"+a+"_2' name='f_c_"+a+"_2' style='width:71px;' onchange=change_comfirm_jl('f_c_"+a+"_2','f_c_"+a+"_3',3,"+parent_id+")>\
										<option value=''>--请选择--</option>\
									</select>\
									<select id='f_c_"+a+"_3' name='f_c_"+a+"_3' style='width:71px;'><option value=''>--请选择--</option></select>");
							}
							else
							{
								$('#field_content'+a+'').html("<input type='hidden' id='f_c_"+a+"' name='f_c_"+a+"' value='jl_two' />\
									<select id='f_c_"+a+"_1' name='f_c_"+a+"_1' style='width:106px;' onchange=change_comfirm_jl('f_c_"+a+"_1','f_c_"+a+"_2',2,"+parent_id+")>\
										<option value=''>--请选择--</option>"+option+"\
									</select>\
									<select id='f_c_"+a+"_2' name='f_c_"+a+"_2' style='width:106px;'><option value=''>--请选择--</option></select>");
							}
						}
					}

				}
				else
				{
					$.messager.alert('错误',responce['message'],'error');
				}
			}
		});
	}
}