/*工作量统计*/
function get_manage_list(user_id,date,stype,stage)
{
	window.parent.addTab('统计-工作量','index.php?c=client&user_id='+user_id+'&statistics_date='+date+'&statistics_type='+stype+'&cle_stage='+stage,'menu_icon');
}

//工作桌面div位置 org_layout 格式：左边div的id名1,左边div的id名2#右边div的id名1,右边div的id名2
function _user_workbench_layout(org_layout)
{
	org_layout     = org_layout.split("#");
	for(var i = 0; i < org_layout.length ;i++)
	{
		var temp_layout = "";
		var str = "";
		if( i == 0 )
		{//left
			str = "box_left_div";
		}
		else if( i == 1 )
		{//right
			str = "box_right_div";
		}

		if( str != "")
		{
			temp_layout = (org_layout[i]).split(",");
			for(var j = 0 ;j < temp_layout.length; j++)
			{
				$("#"+str+"").append($("#"+temp_layout[j]+""));
			}
		}
	}
}

//初始化portal
function _workbench_protal(layout_record)
{
	$('#workbench_protal').portal({
		border:false,
		onStateChange:function(){
			/*-- box left  begin-- */
			var _data_left = [];
			var left_div   = $("#box_left_div").children();
			var left_total = left_div.length
			for(var i = 0; i < left_total ;i++)
			{
				_data_left[i] = left_div[i].lastChild.id
			}
			_data_left = _data_left.join(",");
			/*-- box end  begin-- */

			/*-- box right  begin-- */
			var _data_right = [];
			var right_div   = $("#box_right_div").children();
			var right_total = right_div.length
			for(var i = 0; i < right_total ;i++)
			{
				_data_right[i] = right_div[i].lastChild.id
			}
			_data_right = _data_right.join(",");
			/*-- box right  begin-- */

			var layout_div = _data_left+"#"+_data_right;
			if( layout_div )
			{
				//记录cookie
				layout_div = encodeURIComponent(layout_div);

				if( layout_record != layout_div )
				{
					layout_record = layout_div;
					$.ajax({
						type:'POST',
						url: "index.php?c=workbench&m=update_workbench_layout",
						data: {"layout_div":layout_div},
						dataType: "json",
						success: function(responce){
						}
					});
				}
			}
		}
	});
}

/**
*删除
**/
function anns_remove(anns_id){
	$.messager.confirm('提示', '<br>是否删除该公告', function(r){
		if(r){
			$.ajax({
				type:'POST',
				url: "index.php?c=announcement&m=announcement_delete",
				data: {"anns_id":anns_id},
				dataType: "json",
				success: function(responce){
					if(responce["error"] == 0)
					{
						window.location.reload();
					}
					else
					{
						$.messager.alert('错误',responce["message"],'error');
					}
				}
			});
		}else{
			return false;
		}
	});

}
//修改
function _update(anns_id){
	window.parent.addTab('编辑公告','index.php?c=announcement&m=announcement_edit&anns_id='+anns_id,'menu_icon');
}

//查看公告
function anns_view(anns_id)
{
	window.parent.addTab('查看公告','index.php?c=announcement&m=announcement_view&anns_id='+anns_id,'menu_icon');
}



/*查看消息*/
function message_view(msg_id,send_user_id,send_user_name)
{
	$.ajax({
		type: 'post',
		url: 'index.php?c=message&m=message_view',
		data: {'msg_id':msg_id},
		dataType: 'json',
		success: function(responce)
		{
			var record_content = responce['content']+"&nbsp;&nbsp;<span class='underline' onclick='msg_replay("+send_user_id+")'>【回复】</span>";
			$('#window').css('display','block');
			$('#window').window({
				title: '详细信息',
				top:100,
				closed: false,
				collapsible:false,
				minimizable:false,
				onBeforeOpen: function(){
					$('#SR_content').html(record_content);
				}
			});
		}
	});
}

//消息回复
function msg_replay(reply_user_id)
{
	$('#message_panel').window({
		href:'index.php?c=message&m=message_panel&selected_id='+reply_user_id,
		width:490,
		height:390,
		title:'发消息',
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		shadow:false,
		cache:false
	});
}

//处理
function accept(id,phone_num,cle_id)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=missed_calls&m=change_missed_calls_state",
		data: {"id":id},
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				if(cle_id > 0)
				{
					window.parent.addTab('业务受理','index.php?c=client&m=accept&type=client&cle_id='+cle_id,'menu_icon');
				}
				else
				{
					window.parent.addTab('添加客户','index.php?c=client&m=new_client&cle_phone='+phone_num,'menu_icon');
				}
			}
			else
			{
				$.messager.alert('错误',responce["message"],'error');
			}
		}
	});
}