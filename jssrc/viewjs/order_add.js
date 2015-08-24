var order_product_amount;/*产品订单数量*/
$(document).ready(function() {
	/*客户号码显示*/
	if(!power_phone_view)
	{
		$('#cle_phone').val(hidden_part_number($('#cle_phone').val()));//客户电话
		if(power_use_contact!=1)
		{
			$('#con_mobile').val(hidden_part_number($('#con_mobile').val()));//联系人电话
		}
	}
	order_confirm_dictionary();
	//产品列表
	$('#order_product_list').datagrid({
		fitColumns:true,
		toolbar:[{
			text:'选择产品',
			iconCls:'icon-add',
			handler:function(){
				$('#order_product_list').datagrid('acceptChanges');
				$('#window_class').css('display','block');
				$('#window_class').window({
					title: '订单产品',
					href:"index.php?c=order&m=select_products",
					width:600,
					top:50,
					shadow:true,
					closed: false,
					collapsible:false,
					minimizable:false,
					maximizable:false,
					cache:false
				});
			}
		},'-',{
			text:'移除产品',
			iconCls:'icon-remove',
			handler:function(){
				var row = $('#order_product_list').datagrid('getSelected');
				if (row){
					var index = $('#order_product_list').datagrid('getRowIndex', row);
					$('#order_product_list').datagrid('deleteRow', index);
					confirm_products_info();
					order_product_amount = order_product_amount-1;
					if_system_aomenu_1(order_product_amount);
				}
			}
		}],
		onBeforeLoad:function(){
			$(this).datagrid('rejectChanges');
		},
		onLoadSuccess:function(data){
			//图片检测到鼠标焦点，预览图片
			if($('a.preview').length){
				var img = preloadIm();
				imagePreview(img);
			}

			//确认订单产品
			if( data.total > 0 )
			{
				confirm_products_info();
			}
			order_product_amount = data.total;
			//系统只允许添加一个产品时
			if_system_aomenu_1(order_product_amount);
		}
	});
	//联系人
	if(power_use_contact!=1)
	{
		var url = '';
		if(global_cle_id)
		{
			url = "index.php?c=contact&m=get_contact_list";
		}
		$('#con_name').combogrid({
			panelWidth:153,
			showHeader:false,
			sortName:'con_if_main',
			sortOrder:'desc',
			idField:'con_id',
			textField:'con_name',
			url:url,
			queryParams:{"cle_id":global_cle_id},
			columns:[[
			{field:'con_name',width:150,align:"left",sortable:true}
			]],
			onClickRow:function(rowIndex,rowData)
			{
				var sec_con_mobile = rowData.con_mobile
				$("#real_con_mobile").val(sec_con_mobile);
				//不显示全部号码
				if(!power_phone_view)
				{
					if( sec_con_mobile )
					{
						sec_con_mobile =  hidden_part_number(sec_con_mobile);
					}
				}
				$("#con_mobile").val(sec_con_mobile);
			},
			keyHandler: {
				up: function(){},
				down: function(){},
				enter: function(){},
				query: function(q){
					//不显示panel
					$(this).combo('hidePanel');
				}
			}
			,
			onLoadSuccess:function()
			{
				if( flag_con_info > 0 )
				{
					$('#con_name').combogrid("setText",global_con_name);
					flag_con_info = 0;
				}
			}
		});
	}
});

//系统只允许添加一个产品时
function if_system_aomenu_1(amount)
{
	if(system_order_product_amount==1)
	{
		if(amount>1)
		{
			_show_msg("当前系统只允许添加一个订单产品",'error');
			$('#save_btn').linkbutton({'disabled':true});
		}
		else
		{
			_show_msg('');
			$('#save_btn').linkbutton({'disabled':false});
		}
	}
}

//自定义字段设置
function edit_field_confirm()
{
	$('#set_field_confirm').window({
		title: '订单自定义字段设置',
		href:"index.php?c=field_confirm&m=field_setting&field_type=3",
		iconCls: "icon-seting",
		top:150,
		width:700,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		shadow:false,
		modal:true
	});
}

//数字字典
function edit_dictionary()
{
	$('#set_dictionary').window({
		title: '数据字典',
		href:"index.php?c=dictionary&type=order",
		iconCls: "icon-seting",
		top:150,
		width:500,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		closable:false,
		shadow:false,
		modal:true
	});
}

//确认订单产品
function confirm_products_info()
{
	$('#order_product_list').datagrid('acceptChanges');
	var rows = $('#order_product_list').datagrid('getRows');
	if(rows.length == 0)
	{
		$('#product_info').val("");
	}
	else
	{
		var total = 0;
		var result = {};
		$.each(rows,function(i,selected){
			var product_number = $('#product_number'+selected.product_id).val();
			var product_discount = $('#product_discount'+selected.product_id).val();
			result[i] = {'op_id':selected.op_id,'product_id':selected.product_id,'product_num':selected.product_num,'product_name':selected.product_name,'product_number':product_number,'product_discount':product_discount,'product_thum_pic':selected.product_thum_value,'product_class':selected.product_class,'product_price':selected.product_price};
			total = parseFloat((parseFloat(total) + parseFloat((selected.product_price)*(product_number)*((product_discount)*0.01)))).toFixed(2);
		});
		$('#order_price').val(total);//订单总价
		$('#product_total').html(total);//产品累加总额
		$('#product_info').val(json2str(result));
	}
}

function _add_client()
{
	var phone = $("#cle_phone").val();
	if( exist_star(phone)  )
	{
		phone = "";
	}
	$('#client_window').window({
		title: '选择客户(我的客户)',
		href:"index.php?c=client&m=select_clients&phone="+phone,
		width:582,
		top:50,
		shadow:true,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

//保存订单信息
//   type  类型：  1未处理   3结单
var _data = {};
function save_order_info(type)
{
	//自定义必选字段判断
	var if_continue = true;
	var must_msg = '';
	$("[if_require='true']:input").each(function(){
		if($(this).attr('_date')=='date_box' || $(this).attr('_date')=='textarea_box')
		{
			if($(this).val()=='')
			{
				if_continue = false;
				must_msg += '['+$(this).attr('_chinese_name')+"]";
			}
		}
		else if(!$(this).validatebox('isValid'))
		{
			must_msg += '['+$(this).attr('_chinese_name')+"]";
			if_continue = false;
		}
	});
	if(	if_continue == false)
	{
		if(must_msg.length!=0)
		{
			_show_msg(must_msg+" 不能为空",'error');
		}
		return false;
	}

	_data = {};
	_data.cle_id = $("#cle_id").val();
	_data.cle_name = $("#cle_name").val();
	_data.cle_phone = $("#real_cle_phone").val();
	_data.order_price = $('#order_price').val();

	//可设置的基本字段 客户地址cle_address、配送方式order_delivery_mode、配送编号order_delivery_number、备注order_remark
	$("[order_base_field='true']:input").each(function(){
		_data[$(this).attr('id')] = $(this).val();
	});

	if(power_use_contact!=1)
	{
		var con_mobile = $("#con_mobile").val();
		var real_con_moblie = $("#real_con_mobile").val();
		if(_data.cle_id != 0)
		{
			_data.con_name = 	$('#con_name').combogrid("getText");
			if(con_mobile.length != 0)
			{
				r = /^[0-9]*[1-9][0-9]*$/;
				if(r.test(con_mobile))
				{
					if(con_mobile != real_con_moblie)
					{
						$.ajax({
							type:'POST',
							url: "index.php?c=contact&m=insert_contact",
							data:{'con_name':_data.con_name,'con_mobile':con_mobile,'cle_id':_data.cle_id},
							dataType:"json"
						});
					}
				}
				else
				{
					con_mobile = real_con_moblie;
				}
			}
			_data.con_mobile = con_mobile;
		}
		else
		{
			if(con_mobile.length!=0)
			{
				_show_msg("提示：请先选择客户，在填写联系人");
				return;
			}
		}
	}
	//自定义字段
	$("[confirm_field='true']:input").each(function(){
		if($(this).attr('type')=='checkbox')
		{
			var field1 = $(this).attr('checkbox_name')+'_1';
            var field2 = $(this).attr('checkbox_name')+'_2';
            var pid = {};
            _data[field1] = "";
            _data[field2] = "";
			 $("input:checkbox[name='"+$(this).attr('name')+"']").each(function(i){ 
                if($(this).attr("checked")){
                    _data[field2] += $(this).val()+",";
                    pid[$(this).attr('checkbox_pid')] = $(this).attr('checkbox_pid');
                }
            });
            $.each(pid, function(index, value) {
            	 _data[field1] += value+",";
            });
		}
		else
		{
			_data[$(this).attr('name')] = $(this).val();
		}
	});

	if( empty_obj(_data) )
	{
		_show_msg("提示：订单基本信息为空，请填写数据！");
		return;
	}
	_data.goods_info = $("#product_info").val(); //产品信息
	_data.order_num = $("#order_num").val();//订单编号
	_data.order_state = $('#order_state').val();//订单状态

	$.ajax({
		type:'POST',
		url: "index.php?c=order&m=insert_order",
		data:_data,
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				_show_msg('添加成功，将跳转到订单管理页面','yes');
				est_header("location:index.php?c=order&m=order_list");
			}
			else
			{
				//$.messager.alert('错误',responce['message'],'error');
				_show_msg(responce['message'],'error');
			}
		}
	});
}


/*查看产品详情*/
function show_order_product_detail(product_id)
{
	if(power_view_product)
	{
		$('#order_product_detail_window').window({
			title: '产品详情',
			iconCls:"icon-ok",
			href:"index.php?c=product&m=view_product&product_id="+product_id,
			width:580,
			top:50,
			shadow:true,
			closed: false,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			cache:false
		});
	}
	else
	{
		//$.messager.alert('提示','您没有查看产品的权限！','info');
		_show_msg('您没有查看产品的权限！','error');
	}
}

/*客户详情*/
function _client_detail()
{
	var cle_id = $("#cle_id").val();
	if( cle_id.length!=0 && cle_id>0 )
	{
		window.parent.addTab('业务受理','index.php?c=client&m=accept&system_pagination=0&cle_id='+cle_id,'menu_icon');
	}
}

/*点击选择订单状态*/
function click_order_step(state_index,total)
{
	//去除所有class[ done  lastDone  current   mainNavNoBg   ]
	$("span[cvalue='span_order_step'],mainNav").removeClass();
	//初始化最后一项设置
	$("#order_step_"+eval(total-1)+"").addClass("mainNavNoBg");

	//把选中项设置为 current
	$("#order_state").val($("#order_step_"+state_index+"")[0].title);
	if( state_index == (total-1) )
	{
		//最后一项
		$("#order_step_"+state_index+"").addClass("mainNavNoBg current");
	}
	else
	{
		$("#order_step_"+state_index+"").addClass("current");
	}

	//当前选中项向后设置
	var temp_index = state_index;
	while( temp_index >= 0 )
	{
		temp_index --;
		if( temp_index == (state_index-1) )
		{
			$("#order_step_"+temp_index+"").addClass("lastDone");
		}
		else
		{
			$("#order_step_"+temp_index+"").addClass("done");
		}
	}
}

//显示消息
function _show_msg(msg)
{
	var type='';
	if(arguments[1])
	{
		type = arguments[1];
	}
	if(type=='yes')
	{
		msg = "<img src='./themes/default/icons/ok.png' />&nbsp;"+msg;
	}
	else if(type=='error')
	{
		msg = "<img src='./themes/default/icons/no.png' />&nbsp;"+msg;
	}
	$("#_order_msg").html(msg);
	setTimeout(function(){
		$("#_order_msg").html("");
	},4000);
}