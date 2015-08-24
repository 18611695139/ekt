function _dictionary_tab_reload(if_refesh)
{
	if(type == 'client' || type == 'order')
	{
		var tab = $('#tab_dictionary').tabs('getSelected');  // get selected panel
		tab.panel('refresh');
	}
	else
	{
		_close_dictionary_window(if_refesh);
	}
}

function _dictionary_save(dic_id,if_refesh)
{
	var table_name = '_dictionary_table_'+dic_id;
	var opt = [];
	$('#'+table_name+' input[name="_dictionary_option"]').each(function(){
		opt.push($(this).val());
	});

	$.ajax({
		type:'POST',
		url: "index.php?c=dictionary&m=save_dictionary_detail",
		data:{'option':JSON.stringify(opt),'dict':dic_id},
		dataType:"json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				//客户类型
				if(dic_id==2)
				{
					var cle_type = [];
					$('#'+table_name+' select[name="client_name"]').each(function(){
						cle_type.push($(this).val());
					});
					$.ajax({
						type:'POST',
						url: "index.php?c=client_type&m=save_cle_type",
						data:{'cle_stage':JSON.stringify(opt),'cle_type':JSON.stringify(cle_type)},
						dataType:"json",
						success: function(responce){
							if(responce["error"] == 0)
							{

							}
						}
					});
				}

				$("#_dictionary_save_tip_"+dic_id).html("<img src='./themes/default/icons/ok.png' />&nbsp;保存成功");
				setTimeout(function(){
					$("#_dictionary_save_tip_"+dic_id).html("");
					_dictionary_tab_reload(if_refesh);
				},1500);
			}
			else
			{
				$.messager.alert('错误',responce["message"],'error');
			}
		}
	});
}

function _dictionary_add_option(dic_id)
{
	//得到当前input的量
	var table_name = '_dictionary_table_'+dic_id;
	var opt_length = $('#'+table_name+' input[name="_dictionary_option"]').size()
	var new_opt_poz = opt_length + 1;
	if(dic_id!=2)
	{
		var new_tr = $(' \
			<tr style="padding:2px;"> \
				<th align="center" style="background-color:#F4FAFB;text-align:right;width:15%;padding-right:10px;">'+new_opt_poz+'</th> \
				<td align="left"><input type="text" name="_dictionary_option" value="" /></td> \
			</tr>');
	}
	else
	{
		var new_tr = $(' \
			<tr style="padding:2px;"> \
				<th align="center" style="background-color:#F4FAFB;text-align:right;width:15%;padding-right:10px;">'+new_opt_poz+'</th> \
				<td align="left"><input type="text" name="_dictionary_option" value="" /> \
					<select id="client_type" name="client_name" class="span2">'+client_type_option_str+'</select> \
				</td> \
			</tr>');
	}
	new_tr.appendTo('#'+table_name);
}

function _close_dictionary_window(if_refesh)
{
	if(if_refesh==0)
	{
		window.location.reload();
	}
	$('#set_dictionary').window('close');
}