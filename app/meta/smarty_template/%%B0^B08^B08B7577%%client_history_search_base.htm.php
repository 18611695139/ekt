<?php /* Smarty version 2.6.19, created on 2015-08-13 16:06:28
         compiled from client_history_search_base.htm */ ?>
 <form action="javascript:quick_search()" method="POST" name="searchForm"  id="searchForm" class="form-inline">
	 <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
	 客户姓名
	 <input id="cle_name_search" name="cle_name_search"  type="text" value=""/>&nbsp;&nbsp;
	 <button class="btn btn-primary" onclick="$('#searchForm').submit();">
        <span class="glyphicon glyphicon-search"></span> 搜索
    </button>
    
    <button class="btn" onclick="advanced_search()">
        <span class="glyphicon glyphicon-zoom-in"></span> 高级搜索
    </button>
</form>
  

<script language="JavaScript" type="text/javascript">
/*检索条件*/
function quick_search()
{
	var cle_name = $('#cle_name_search').val();
	$('#client_list').datagrid('options').queryParams = {};
	var queryParams = $('#client_list').datagrid('options').queryParams;
	queryParams.cle_name   = cle_name;

	$('#client_list').datagrid('load');
}
</script>