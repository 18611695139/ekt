function save_book_data(type)  //type  类型   1公司数据    2个人数据 3编辑
{
	var _data = {};

	_data.tx_id = $("#tx_id").val();
	_data.tx_name = $("#tx_name").val(); //名称
	_data.tx_phone1 = $("#tx_phone1").val();
	_data.tx_phone2 = $("#tx_phone2").val();
	_data.tx_remark = $("#tx_remark").val();

	_data.tx_type = type;

	var save_url = '';
	if(type != 3)
	save_url = "index.php?c=address_book&m=insert_address_book";
	else
	save_url =  "index.php?c=address_book&m=update_address_book";

	$.ajax({
		type:'POST',
		url:save_url,
		data:_data,
		dataType:"json",
		success: function(responce){

			if(responce['error']=='0')
			{
				$('#address_book_table').datagrid('load');
				$('#add_book').window('close');
			}
			else{
				$.messager.alert('错误',responce['message'],'error');
			}
		}
	});

}

//验证
$.extend($.fn.validatebox.defaults.rules, {
	phone_number: {
		validator: function(value, param){
			return /^[0-9]*$/.test(value);
		},
		message: '手机号码规则'
	}
});