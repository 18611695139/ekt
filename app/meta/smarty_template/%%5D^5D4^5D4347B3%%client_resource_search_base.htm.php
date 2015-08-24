<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:35
         compiled from client_resource_search_base.htm */ ?>
<form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
	<div class="btn-group" data-toggle="buttons-radio" >
        <button class="btn btn-link" id='td0' onclick="all_type = '1'; quick_search();">全部客户</button>
        <button class="btn btn-link" data-toggle="tooltip" title='新添加还没调配过的客户数据' id='td2' onclick="all_type = '3'; quick_search();">新客户</button>
        <button class="btn btn-link active" data-toggle="tooltip" title="坐席放弃或者管理员回收的客户数据" id='td3' onclick="all_type = '4'; quick_search();">待再分配客户</button>
   </div>
    <div style="display: block;margin: 5px" class="form-inline">
    <label for="search_key"></label>
    <input type="text" data-toggle="tooltip" title="支持客户/联系人名称 或 电话(4位以上)搜索,注最多检索10条" class="form-control input-sm"
     id="search_key" name="search_key" onclick='search_cle_name()' onblur='if_null()' value="客户姓名支持拼音首字母" />
    <button class="btn btn-primary" onclick="$('#searchForm').submit();">
        <span class="glyphicon glyphicon-search"></span> 搜索
    </button>
    <button class="btn" onclick="advanced_search()">
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
	$('#search_key').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
    $('#td2').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
    $('#td3').on('focus', function(){
        $(this).tooltip({placement: 'bottom'}).tooltip('show');
    });
});

//检索
var all_type ="3";

function quick_search()
{
	var _data        = {};
	/*客户名称*/
	if($('#search_key').val() == '客户姓名支持拼音首字母')
	{
		_data.search_key = '';
	}
	else
	{
		_data.search_key = $('#search_key').val();
	}

	_data.all_type   = all_type;

	$('#client_list').datagrid('options').queryParams = {};
	var queryParams = $('#client_list').datagrid('options').queryParams;

	queryParams.all_type  = _data.all_type;
	queryParams.search_key  = _data.search_key;

	$('#client_list').datagrid('reload');
}

//点击清空
function search_cle_name()
{
	var _name = $('#search_key').val();
	if(_name == '客户姓名支持拼音首字母')
	{
		$('#search_key').css('color','#000000');
		$('#search_key').val('');
	}
}
//判断是否为空
function if_null()
{
	var name_value = $('#search_key').val();
	if(name_value.length == 0)
	{
		$('#search_key').css('color','#cccccc');
		$('#search_key').val('客户姓名支持拼音首字母');
	}
}

/*function set_colors(id)
{
    $('#Formsearch>button').css('color','#335b64');
    $('#'+id).css('color','red');
}*/
</script>