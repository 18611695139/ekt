<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:38
         compiled from file_list.htm */ ?>
<div>
<div style="padding:5px 30px;background:#f3f3f3;">	
<form name="form" id='file_form'  action="" method="POST" enctype="multipart/form-data">
	<b>文件上传</b> <input id="fileToUpload" type="file" size="30" name="fileToUpload">
	<button class="button" id="buttonUpload" onclick="return ajaxFileUpload();">上传</button>
	<img id="loading" src="./image/loading.gif" style="display:none;">
	<span id='upload_msg' style='color:red;'></span>
</form>    	
</div>
<div id='file_list'></div>
</div>
  
 <script type="text/javascript" src="jssrc/ajaxfileupload.js"></script>
 <script type="text/javascript">
 var cle_id = '<?php echo $this->_tpl_vars['cle_id']; ?>
';
 $(document).ready(function(){
 	$('#file_list').datagrid({
 		nowrap: true,
 		striped: true,
 		pagination:true,
 		rownumbers:true,
 		fitColumns:true,
 		checkOnSelect:false,
 		pageList:[5],
 		sortName:'file_upload_time',
 		sortOrder:'DESC',//降序排列
 		idField:'file_upload_time',
 		queryParams:{"cle_id":cle_id},
 		url:'index.php?c=file&m=list_file_query',
 		frozenColumns:[[
 		{title:'file_id',field:'file_id',hidden:true},
 		{title:'操作',field:'oper_txt',width:110,align:'center',formatter:function(value,rowData,rowIndex){
 			return "<a href='javascript:;' onclick=download_file('"+rowData.file_id+"','"+rowData.file_new_name+"','"+rowData.file_old_name+"') title='下载'>【下载】</a><a href='javascript:;' onclick='delete_file("+rowData.file_id+")' title='删除'>【删除】</a>";
 		}},
 		{title:'文件名',field:'file_old_name',width:350,sortable:true,formatter:function(value,rowData,rowIndex){
 			var file_ext = rowData.file_ext;
 			if(file_ext == 'txt'){
 				return "<img src='./image/txt.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else if(file_ext == 'doc' || file_ext == 'docx'){
 				return "<img src='./image/word.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else if(file_ext == 'xls' || file_ext == 'xlsx' || file_ext == 'csv'){
 				return "<img src='./image/excel.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else if(file_ext == 'ppt' || file_ext == 'pptx'){
 				return "<img src='./image/ppt.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else if(file_ext == 'pdf'){
 				return "<img src='./image/pdf.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else if(file_ext == 'jpg' || file_ext == 'jpeg' || file_ext == 'gif' || file_ext == 'png'){
 				return "<img src='./image/pic.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else  if(file_ext == 'zip' || file_ext == 'rar'){
 				return "<img src='./image/zip.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 			else{
 				return "<img src='./image/unknow.png' border='0' height='16' width='16' align='absmiddle'/>&nbsp;&nbsp;"+value;
 			}
 		}}
 		]],
 		columns:[[
 		{title:'文件大小',field:'file_size',width:80,sortable:true},
 		{title:'上传时间',field:'file_upload_time',width:120,sortable:true},
 		{title:'上传者',field:'user_name',width:80,sortable:true}
 		]]
 	});

 	var pager = $('#file_list').datagrid('getPager');
 	$(pager).pagination({showPageList:false});
 });

/*异步上传*/
 function ajaxFileUpload()
 {
 	var fileValue =  $('#fileToUpload').val();
 	if(fileValue.length == 0)
 	return false;

 	$("#loading").show();

 	$.ajaxFileUpload({
 		url:'index.php?c=file&m=upload_file',
 		secureuri:false,
 		fileElementId:'fileToUpload',
 		dataType: 'json',
 		data:{cle_id:cle_id},
 		success: function (data, status)
 		{
 			$("#loading").hide();
 			$('#file_form').get(0).reset();
 			if(typeof(data.error) != 'undefined')
 			{
 				if(data.error != '')
 				{
 					$.messager.alert('执行错误',data.error,'error');
 				}else
 				{
 					$('#file_list').datagrid('reload');
 					$('#upload_msg').html("<img src='./themes/default/icons/ok.png' />&nbsp;上传成功");
 					setTimeout(function(){
 						$("#upload_msg").html("");
 					},3000);
 				}
 			}
 		},
 		error: function (data, status, e)
 		{
 			$.messager.alert('执行错误',e,'error');
 		}
 	});
 	return false;
 }

 /*删除文件*/
 function delete_file(file_id)
 {
 	$.messager.confirm('提示', '您确定要删除选中文件？', function(r){
 		if(r){
 			$.ajax({
 				type:"POST",
 				url:'index.php?c=file&m=delete_file',
 				data:{"file_ids":file_id},
 				dataType:'json',
 				success:function (responce){
 					if(responce['error']=='0'){
 						$('#file_list').datagrid('load');
 					}
 					else
 					{
 						$.messager.alert('执行错误',responce['message'],'error');
 					}
 				}
 			});
 		}
 		else
 		{
 			return false;
 		}
 	});
 }


 //下载
 function download_file(file_id,file_new_name,file_old_name)
 {
 	est_header("Location:index.php?c=file&m=download_file&file_id="+file_id+"&file_new_name="+file_new_name+"&file_old_name="+file_old_name);
 }
</script>	