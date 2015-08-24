$(document).ready(function() {
	if( power_fieldsconfirm_product > 0 )
	{
		$('#product_tools').tabs({
			tools:[
			{
				text:'产品自定义字段',
				iconCls:'icon-seting',
				handler:function(){
					$('#set_product_field_confirm').window({
						title: '产品自定义字段设置',
						href:"index.php?c=field_confirm&m=field_setting&field_type=2",
						iconCls: "icon-seting",
						top:150,
						width:700,
						closed: false,
						collapsible:false,
						minimizable:false,
						maximizable:false,
						cache:false
					});
				}
			}
			]
		});
	}

	$('#product_class_id').combotree({
		url:'index.php?c=product&m=get_product_class_tree',
		lines:true,
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#product_class_id').combotree('options').url = "index.php?c=product&m=get_product_class_tree";

			}
		}
	});
	$('#product_class_id').combotree('setValue', global_product_class_id);

	if($('a.preview').length){
		var img = preloadIm();
		imagePreview(img);
	}
});

/*添加或编辑产品*/
function save_product()
{
	if( !$('#product_name').validatebox('isValid')  )
	{
		_show_msg("产品名称不能为空",'error');
		return false;
	}
	//自定义必选字段判断
	var if_continue = true;
	var must_msg = '';
	$("[if_require='true']:input").each(function(){
		if($(this).attr('_date')=='date_box')
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

	var product_class_id = $('#product_class_id').combotree('getValue');
	if(product_class_id!='')
	{
		var product_id = product_info_product_id;
		if(product_id == 0)
		{
			$("#product_form").attr('action',"index.php?c=product&m=insert_product"); //添加新产品
		}
		else
		{
			$("#product_form").attr('action',"index.php?c=product&m=update_product");//编辑产品
		}
		$('#save_btn').attr('disabled',true);
		$("#product_form").submit();
	}
	else
	{
		_show_msg("产品分类不能为空",'error');
		return false;
	}
}

/*删除图片*/
function delete_product_pic(product_id,product_only_name,pic,thumb_pic)
{
	$.ajax({
		type:"POST",
		url:'index.php?c=product&m=delete_product_pic',
		data:{"product_id":product_id,'product_only_name':product_only_name,'pic':pic,'thumb_pic':thumb_pic},
		dataType:'json',
		success:function (responce){
			if(responce['error']=='0')
			{
				$('#show_'+responce['content']).css("display","none");
				_show_msg("图片删除成功",'yes');
			}
			else
			{
				_show_msg(responce['message'],'error');
			}
		}
	});
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
	$("#_product_msg").html(msg);
	setTimeout(function(){
		$("#_product_msg").html("");
	},3000);
}