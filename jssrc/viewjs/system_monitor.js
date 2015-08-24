function restart_http()
{
	$('#http_stat').html('<font color="red">重启中...</font>');
	$.ajax({
		url:'index.php?c=system_monitor&m=restart_http'
	});
	setTimeout(function(){
		window.reload();
	},2000);
}

function restart_mysql()
{
	$('#mysql_stat').html('<font color="red">重启中...</font>');
	$.ajax({
		url:'index.php?c=system_monitor&m=restart_mysql',
		dataType:'json',
		success:function(responce){
			if(responce['error'])
			{
				$('#mysql_stat').html('<font color="red">重启失败</font>');
			}
			else
			{
				$('#mysql_stat').html('正在运行...');
			}
		},
		error:function(error){
			$('#mysql_stat').html('<font color="red">重启失败</font>');
		}
	});
}

function restart_winast()
{
	$('#winast_stat').html('<font color="red">重启中...</font>');
	$.ajax({
		url:'index.php?c=system_monitor&m=restart_winast',
		dataType:'json',
		success:function(responce){
			if(responce['error']){
				$('#winast_stat').html('<font color="red">重启失败</font>');
			}
			else{
				$('#winast_stat').html('正在运行...');
			}
		},
		error:function(error){
			$('#winast_stat').html('<font color="red">重启失败</font>');
		}
	});
}

function restart_synchronous()
{
	$('#synchronous_stat').html('<font color="red">重启中...</font>');
	$.ajax({
		url:'index.php?c=system_monitor&m=restart_synchronous',
		dataType:'json',
		success:function(responce){
			if(responce['error']){
				$('#synchronous_stat').html('<font color="red">重启失败</font>');
			}
			else{
				$('#synchronous_stat').html('正在运行...');
			}
		},
		error:function(error){
			$('#synchronous_stat').html('<font color="red">重启失败</font>');
		}
	});
}

function reboot_http_server(){
	$.messager.confirm('确认','是否要重启服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=reboot_http_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','重启失败','warning');
					}
					else{
						$.messager.alert('成功','开始重启服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','重启失败','warning');
				}
			});
		}
	});
}

function shutdown_http_server(){
	$.messager.confirm('确认','是否要关闭服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=shutdown_http_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','关机失败','warning');
					}
					else{
						$.messager.alert('成功','开始关闭服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','关机失败','warning');
				}
			});
		}
	});
}

function reboot_mysql_server(){
	$.messager.confirm('确认','是否要重启服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=reboot_mysql_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','重启失败','warning');
					}
					else{
						$.messager.alert('成功','开始重启服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','重启失败','warning');
				}
			});
		}
	});
}

function shutdown_mysql_server(){
	$.messager.confirm('确认','是否要关闭服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=shutdown_mysql_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','关机失败','warning');
					}
					else{
						$.messager.alert('成功','开始关闭服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','关机失败','warning');
				}
			});
		}
	});
}

function reboot_phone_server(){
	$.messager.confirm('确认','是否要重启服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=reboot_phone_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','重启失败','warning');
					}
					else{
						$.messager.alert('成功','开始重启服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','重启失败','warning');
				}
			});
		}
	});
}

function shutdown_phone_server(){
	$.messager.confirm('确认','是否要关闭服务器？',function(r){
		if(r){
			$.ajax({
				url:'index.php?c=system_monitor&m=shutdown_phone_server',
				dataType:'json',
				success:function(responce){
					if(responce['error']){
						$.messager.alert('提示','关机失败','warning');
					}
					else{
						$.messager.alert('成功','开始关闭服务器');
					}
				},
				error:function(error){
					$.messager.alert('提示','关机失败','warning');
				}
			});
		}
	});
}