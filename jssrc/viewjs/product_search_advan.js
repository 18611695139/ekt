$(document).ready(function(){
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});

	$('#product_class_search').combotree({
		url:'index.php?c=product&m=get_product_class_tree',
		editable:true,
		lines:true,
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#product_class_search').combotree('options').url = "index.php?c=product&m=get_product_class_tree";

			}
		}
	});
	$('#product_class_search').combotree('setValue',1);
});

function quick_search()
{
	var product_name = $('#product_name_search').val();
	var product_class_id = $('#product_class_search').combotree('getValue');
	var product_num = $('#product_num_search').val();
	var product_state = $('#product_state_search').val();
	var price1 = $('#price1').val();
	var price2 = $('#price2').val();

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
	var field_confirm_values = json2str(field_data);

	var queryParams = $('#product_list').datagrid('options').queryParams;
	queryParams.product_name   = product_name;
	queryParams.product_num   = product_num;
	queryParams.product_class_id     = product_class_id;
	queryParams.product_state = product_state;
	queryParams.price1 = price1;
	queryParams.price2 = price2;
	/*自定义字段*/
	queryParams.field_confirm_values = field_confirm_values;
	$('#product_list').datagrid('load');
}

//显示自定义信息
function show_field_detail(a)
{
	var parent_id = $('#field_confirm'+a+'').val();
	if(parent_id.length == 0)
	{
		$('#check_'+a+'').removeAttr("checked");
		$('#field_content'+a+'').html("<input type='text' id='f_c_"+a+"' name='f_c_"+a+"' value='' style='width:210px;'/>");
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