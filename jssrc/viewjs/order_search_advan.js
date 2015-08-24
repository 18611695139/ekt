$(document).ready(function(){
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});

	//部门
	$('#dept_user_search').combotree({
		url:'index.php?c=user&m=get_dept_user_tree',
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(row,param){
			if (!row)
			{ // load top level rows
				param.id = 0;   // set id=0, indicate to load new page rows
			}
		}
	});
	$('#dept_user_search').combotree('setValue',dept_session_id);

	/*订单产品*/
	$('#product_search').combobox({
		url:'index.php?c=product&m=get_product_box',
		valueField:'product_id',
		textField:'product',
		mode:'remote',
		hasDownArrow:false
	});

});

/*检索条件*/
function quick_search()
{
	var search_data = {};

	search_data.order_num = $('#order_num_search').val();//订单编号
	search_data.order_accept_time_start = $('#order_accept_time_start').val();
	search_data.order_accept_time_end = $('#order_accept_time_end').val();
	search_data.cle_name = $('#cle_name_search').val();/*客户名称*/
	search_data.cle_phone = $("#cle_phone_search").val();/*客户电话*/
	search_data.order_state = $("#order_state_search").val();/*订单状态*/
	search_data.product_id = $("#product_search").combobox("getValue");//订单产品id
	//可配置的基本字段 order_delivery_mode order_delivery_number cle_address order_remark
	$("[order_base='true']:input").each(function(){
		search_data[$(this).attr('id')] = $(this).val();
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

	if(role_type != 2)
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
		}
	}
	else
	{
		search_data.user_id = user_session_id;
	}

	$('#order_list').datagrid('options').queryParams = {};
	var queryParams = $('#order_list').datagrid('options').queryParams;
	$.each(search_data,function(field,value){
		if(value != undefined && value.length != 0 )
		{
			queryParams[field] = value;
		}
	});
	$('#order_list').datagrid('load');
}

//显示自定义信息
function show_field_detail(a)
{
	var parent_id = $('#field_confirm'+a+'').val();
	if(parent_id.length == 0)
	{
		$('#check_'+a+'').removeAttr("checked");
		$('#field_content'+a+'').html("<input type='text' id='f_c_"+a+"' name='f_c_"+a+"' value='' style='width:120px;'/>");
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