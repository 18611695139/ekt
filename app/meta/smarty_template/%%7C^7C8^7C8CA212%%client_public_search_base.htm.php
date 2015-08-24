<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:35
         compiled from client_public_search_base.htm */ ?>
 <form action="javascript:quick_search()" method="POST" name="searchForm"  id="searchForm">
 	<div class="btn-group" data-toggle="buttons-radio" id="Formsearch">
        <button class="btn btn-link" id='td0' onclick="all_type = '0';set_colors('td0'); quick_search();">全部客户</button>
        <button class="btn btn-link" id='td1' onclick="all_type = '3';set_colors('td1'); quick_search();" data-toggle="tooltip" title='新导入且无所属人的客户'>新客户</button>
        <button class="btn btn-link" id='td2' onclick="all_type = '1';set_colors('td2'); quick_search();"  data-toggle="tooltip" title="坐席放弃的客户">放弃的客户</button>
        <button class="btn btn-link" id='td3' onclick="all_type = '2';set_colors('td3'); quick_search();"  data-toggle="tooltip" title="管理员回收的数据">回收的客户</button>
   </div>
   <div style="display: block;margin: 5px" class="form-inline">
	 	客户姓名<input id="cle_name_search" name="cle_name_search"  type="text" value=""  />&nbsp;&nbsp;
    	<button class="btn btn-primary" onclick="$('#searchForm').submit();">
        	<span class="glyphicon glyphicon-search"></span> 搜索
    	</button>
    	<button type="button" class="btn" onclick="advanced_search()">
        	<span class="glyphicon glyphicon-zoom-in"></span> 高级搜索
    	</button>
    </div>
</form>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
	$('#td1').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
    $('#td2').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
    $('#td3').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
});
/*检索条件*/
all_type = '0';
function quick_search()
{
	var cle_name = $('#cle_name_search').val();
	$('#client_list').datagrid('options').queryParams = {};
	var queryParams = $('#client_list').datagrid('options').queryParams;
	queryParams.cle_name   = cle_name;
	queryParams.all_type   = all_type;

	$('#client_list').datagrid('load');
}
</script>