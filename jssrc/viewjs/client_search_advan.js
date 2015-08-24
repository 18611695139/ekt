$(document).ready(function(){
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
	//所属部门或人 （部门与人的树）
	if(control_flag!='my_client' && control_flag!='public' && role_type!=2)
	{
		$('#dept_user_search').combotree({
			url:'index.php?c=user&m=get_dept_user_tree',
			onClick:function(node){
				$(this).tree('expand', node.target);
				if(no_user_search==1)
				{
					var dept_user = $("#dept_user_search").combotree('getValue');
					dept_user = dept_user.split('user');
					if(dept_user[0]=='')
					{
						$("#no_user_data").attr("checked",false);
						$("#no_user_data").attr("disabled",true);
						$('#no_color').css('color','#cccccc');//无所属人颜色
					}
					else
					{
						$("#no_user_data").attr("disabled",false);
						$('#no_color').css('color','#000000');
					}
				}
			},
			onBeforeLoad : function(row,param){
				if (!row)
				{ // load top level rows
					param.id = 0;   // set id=0, indicate to load new page rows
				}
			}
		});
		$('#dept_user_search').combotree('setValue',dept_session_id);
	}

	//所属部门 （部门树）
	if(control_flag=='public' && (role_type!=2 || power_public_all))
	{
		$('#dept_id_search').combotree({
			url:'index.php?c=department&m=get_department_tree&gl_all_dept=true',
			onClick:function(node){
				$(this).tree('expand', node.target);
			},
			onBeforeLoad : function(node, param){
				if (node){
					return false;
				} else {
					$('#dept_id_search').combotree('options').url = "index.php?c=department&m=get_department_tree&gl_all_dept=true";
				}
			}
		});
		if(power_public_all)
		$('#dept_id_search').combotree('setValue',1);
		else
		$('#dept_id_search').combotree('setValue',dept_session_id);
	}
});

//点击清空
function search_cle_name()
{
	var _name = $('#cle_name_search').val();
	if(_name == '支持拼音首字母')
	{
		$('#cle_name_search').css('color','#000000');
		$('#cle_name_search').val('');
	}
}
//判断是否为空
function if_null()
{
	var name_value = $('#cle_name_search').val();
	if(name_value.length == 0)
	{
		$('#cle_name_search').css('color','#cccccc');
		$('#cle_name_search').val('支持拼音首字母');
	}
}

function quick_search()
{
	var search_data = {};

	/*客户名称*/
	if($('#cle_name_search').val() != '支持拼音首字母')
	{
		search_data.cle_name = $('#cle_name_search').val();
	}
	/*电话*/
	search_data.cle_phone = $("#cle_phone_search").val();
	search_data.cle_creat_time_start = $("#cle_creat_time_search_start").val();//创建时间
	search_data.cle_creat_time_end = $("#cle_creat_time_search_end").val();//创建时间
	search_data.cle_update_time_start = $("#cle_update_time_search_start").val();//更新时间
	search_data.cle_update_time_end = $("#cle_update_time_search_end").val();//更新时间
	search_data.cle_last_connecttime_start = $("#cle_last_connecttime_search_start").val(); //最近联系时间
	search_data.cle_last_connecttime_end = $("#cle_last_connecttime_search_end").val(); //最近联系时间
	search_data.con_rec_next_time_start = $('#con_rec_next_time_start').val();//下次联系时间
	search_data.con_rec_next_time_end = $('#con_rec_next_time_end').val();//下次联系时间
	search_data.impt_id = $('#impt_id_search').val();//导入批次号
	search_data.cle_dial_number = $('#cle_dial_number_search').val();//通话次数
    search_data.dployment_num = $('#dployment_num_search').val();//调配次数
	//联系人
	if(control_flag!='data_deal' && power_use_contact!=1)
	{
		search_data.con_name = $("#con_name_search").val();//联系人名
		search_data.con_mobile = $("#con_mobile_search").val();//联系人电话
	}
	//可配置的基本字段 cle_info_source cle_address cle_remark cle_province_id cle_city_id cle_stat cle_stage
	$("[client_base='true']:input").each(function(){
		if($(this).attr('id')=='cle_province_id' || $(this).attr('id')=='cle_city_id')
		{
			if($(this).val()!=0)
			{
				search_data[$(this).attr('id')] = $(this).val();
			}
		}
		else
		{
			search_data[$(this).attr('id')] = $(this).val();
		}
	});

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

	if((control_flag!='my_client')&&(control_flag!='public')&&(role_type!=2))
	{
		var dept_user = $("#dept_user_search").combotree('getValue');
		dept_user = dept_user.split('user');
		if(dept_user[0]=='')
		{
			search_data.user_id = dept_user[1];/*所属人*/
		}
		else
		{
			search_data.dept_id = dept_user[0];/*部门*/
			//是否只搜无所属人的数据
			if(no_user_search==1 && $('#no_user_data').attr('checked') == 'checked')
			{
				search_data.user_id = 0;/*所属人*/
			}
		}
		search_data.dployment_num = $('#dployment_num_search').val();//调配次数
	}
	else
	{
		search_data.user_id = user_session_id;
	}

	//公共
	if(control_flag=='public')
	{
		search_data.cle_public_type = $("#cle_public_type").val();//公共数据类型
		if((role_type!=2 || power_public_all))
		{
			search_data.dept_id = $("#dept_id_search").combotree('getValue');
		}
	}

	$('#client_list').datagrid('options').queryParams = {};
	var queryParams = $('#client_list').datagrid('options').queryParams;
	$.each(search_data,function(field,value){
		if(value != undefined && value.length != 0 )
		{
			queryParams[field] = value;
		}
	});

	//queryParams.repeat_condition = 0;

	$('#client_list').datagrid('load');
}

//显示自定义信息
function show_field_detail(a)
{
	var parent_id = $('#field_confirm'+a+'').val();
	if(parent_id.length === 0)
	{
		$('#check_'+a+'').removeAttr("checked");
		$('#field_content'+a+'').html("<input type='text' id='f_c_"+a+"' name='f_c_"+a+"' value='' />");
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
								$('#field_content'+a+'').html(' \
									<input type="hidden" id="f_c_'+a+'" name="f_c_'+a+'" value="jl_three" /> \
									<select id="f_c_'+a+'_1" name="f_c_'+a+'_1" style="width:71px;" onchange=change_comfirm_jl("f_c_'+a+'_1","f_c_'+a+'_2",2,'+parent_id+',"f_c_'+a+'_3")> \
										<option value="">--请选择--</option>'+option+' \
									</select> \
									<select id="f_c_'+a+'_2" name="f_c_'+a+'_2" style="width:71px;" onchange=change_comfirm_jl("f_c_'+a+'_2","f_c_'+a+'_3",3,'+parent_id+')> \
										<option value="">--请选择--</option> \
									</select> \
									<select id="f_c_'+a+'_3" name="f_c_'+a+'_3" style="width:71px;"> \
										<option value="">--请选择--</option> \
									</select>');
							}
							else
							{
								$('#field_content'+a+'').html(' \
									<input type="hidden" id="f_c_'+a+'" name="f_c_'+a+'" value="jl_two" /> \
									<select id="f_c_'+a+'_1" name="f_c_'+a+'_1" style="width:106px;" onchange=change_comfirm_jl("f_c_'+a+'_1","f_c_'+a+'_2",2,'+parent_id+')> \
										<option value="">--请选择--</option>'+option+' \
									</select> \
									<select id="f_c_'+a+'_2" name="f_c_'+a+'_2" style="width:106px;"> \
										<option value="">--请选择--</option> \
									</select>');
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