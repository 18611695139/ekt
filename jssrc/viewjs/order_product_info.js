$(document).ready(function() {
	//产品列表
	if(power_update_order)
	{
		$('#order_product_list').datagrid({
			toolbar:[{
				text:'选择产品',
				iconCls:'icon-add',
				handler:function(){
					$('#order_product_list').datagrid('acceptChanges');
					$('#window_class').css('display','block');
					$('#window_class').window({
						title: '订单产品',
						href:"index.php?c=order&m=select_products",
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
			},'-',{
				text:'移除产品',
				iconCls:'icon-remove',
				handler:function(){
					var row = $('#order_product_list').datagrid('getSelected');
					if (row){
						var index = $('#order_product_list').datagrid('getRowIndex', row);
						$('#order_product_list').datagrid('deleteRow', index);
						confirm_products_info();
						$('#save_product_btn').attr('disabled',false);
						order_product_amount = order_product_amount-1;
						if_system_aomenu_1(order_product_amount);
					}
				}
			}]
			,onBeforeLoad:function(){
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
					var products_info = $("#product_info").val();
					if(products_info != original_goods_info)
					$('#save_product_btn').attr('disabled',false);
				}
				order_product_amount = data.total;
				//系统只允许添加一个产品时
				if_system_aomenu_1(order_product_amount);
			}
		});
	}
	else
	{
		$('#order_product_list').datagrid({
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
					var products_info = $("#product_info").val();
					if(products_info != original_goods_info)
					$('#save_product_btn').attr('disabled',false);
				}

				order_product_amount = data.total;
				//系统只允许添加一个产品时
				if_system_aomenu_1(order_product_amount);
			}
		});
	}
	/*加载订单原产品*/
	global_order_product_info();
	$('#save_product_btn').attr('disabled',true);
});

/*保存产品信息*/
function save_order_product_info()
{
	$('#save_product_btn').attr('disabled',true);
	//产品信息
	var goods_info = $("#product_info").val();
	if(goods_info.length!=0)
	{
		if(goods_info != original_goods_info)
		{
			var order_total_price = $("#order_total_price").val();
			var order_id = global_order_id;
			if(order_id=='')
			{
				$.messager.alert('错误','该订单已不存在','error');
				return;
			}
			$.ajax({
				type:'POST',
				url: "index.php?c=order&m=set_order_product",
				data:{'goods_info':goods_info,'order_id':order_id,'order_total_price':order_total_price},
				dataType:"json",
				success: function(responce){
					if(responce['error']=='0')
					{
						$('#order_price').val(order_total_price);
						$("#save_product_message").html("<img src='./themes/default/icons/ok.png' />&nbsp;保存订单产品成功");
						setTimeout(function(){$("#save_product_message").html("");},1000);
					}
					else
					{
						$.messager.alert('错误',responce['message'],'error');
					}
				}
			});
		}
	}
	else
	{
		$.messager.alert('错误','当前没有要添加的订单产品','error');
	}
}

/*改变数量或百分比*/
function change_num_discount()
{
	if(power_update_order)
	{
		confirm_products_info();
		$('#save_product_btn').attr('disabled',false);
	}
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

		$('#order_total_price').val(total);
		$('#product_total').html(total);
		$('#product_info').val(json2str(result));
	}
}

//系统只允许添加一个产品时
function if_system_aomenu_1(amount)
{
	if(system_order_product_amount==1)
	{
		if(amount>1)
		{
			$('#save_product_message').html('注：系统只允许添加一个订单产品，不能保存');
			$('#save_product_btn').attr('disabled',true);
		}
		else
		{
			$('#save_product_message').html('');
			$('#save_product_btn').attr('disabled',false);
		}
	}
}

/*查看产品详情*/
function show_order_product_detail(product_id)
{
	if(power_view_product == 1)
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
	$.messager.alert('提示','您没有查看产品的权限！','info');
}