<?php /* Smarty version 2.6.19, created on 2015-08-07 09:28:06
         compiled from data_import_process.htm */ ?>
<!--  client_import_process.htm   -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="main-div">
    <div style="margin: 50px; padding: 8px; border: 1px solid gray; background: #EAEAEA; ">
        <div style="padding-bottom:15px"><font id="state_txt" color="gray">上传文件成功...</font></div>
        <div id="progress" class="easyui-progressbar" ></div><!--   进度条 -->
        <div id="status">&nbsp;</div>
        <div id="percent"><span id="process">0/0</span></div>
    </div>
</div>

<script language="JavaScript" type="text/javascript">
var total = 0;
var deal = 0;
var state = '';

$(document).ready(function() {
	$.ajax({
		url:   'index.php?c=data_import&m=import_data',
		data:  {'param':'<?php echo $this->_tpl_vars['param_data']; ?>
'},
		type:  'post'
	});
	read_progress();
});

//得到进度数据
function read_progress()
{
	if(state != '导入结束')
	{
		$.ajax({
			url: 'index.php?c=data_import&m=read_process',
			data: {"tmp_file":'<?php echo $this->_tpl_vars['tmp_file']; ?>
'},
			type: 'post',
			async: false,
			success:function(responce){
				var result = responce.split("#");
				state = result[0];
				$('#state_txt').text(state);

				deal = result[1];
				total   = result[2];

				if(deal && total)
				{
					var process = (deal / total)*100;
					process     = Math.ceil(process);
					$('#progress').progressbar('setValue', process);
					$('#process').html(deal+"/"+total);
				}

				if(state == "导入结束")
				{
					var error = result[3];
					var import_id = result[4];
					$('#progress').progressbar('setValue', 100);
					$('#process').html(total+"/"+total);
					$.messager.alert('导入数据完成，查看导入的数据', "成功导入数据共"+deal+"条。<br>异常数据"+error+"条。<br>过滤数据"+(total-deal)+"条。<br><br><a href='./index.php?c=data_import&m=display_import_log&import_id="+import_id+"&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
' style='color:red;'>点击查看导入日志</a>",'notice',function(){
						//导入结束
						import_over();
					});
				}
				else if(state == "导入异常")
				{
					$('#state_txt').text(state+":"+deal);
					// 导入结束
					setTimeout("import_over()", 3000);
				}
				else
				{
					setTimeout("read_progress()", 1000);
				}
			}
		});
	}
}

// 导入结束
function import_over()
{
	location.href = 'index.php?c=data_import&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
';
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>