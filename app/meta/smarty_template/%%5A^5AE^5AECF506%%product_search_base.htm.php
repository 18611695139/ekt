<?php /* Smarty version 2.6.19, created on 2015-08-11 12:58:59
         compiled from product_search_base.htm */ ?>
 <form action="javascript:quick_search()" method="POST" name="searchForm"  id="searchForm">
    <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
	   产品名称 <input type="text" style='width:130px;' id="product_name_search" name="product_name_search" />&nbsp;&nbsp;
	   产品编号 <input type="text" style='width:130px;' id="product_num_search" name="product_num_search" />&nbsp;&nbsp;
	 <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
	  <IMG style="VERTICAL-ALIGN: middle" border=0 alt="分类" src="image/icon_listsearch.png">
	   <FONT color=#cc0066  >快速查找</FONT>
	   <A id='visit0' href="javascript:product_state_search ='';set_color('visit0'); quick_search();" style="color:red;">全部产品</A><FONT color=#99cc00 face=Wingdings>w</FONT>
	   <A id='visit1' href="javascript:product_state_search ='1';set_color('visit1'); quick_search();">上架</A><FONT color=#99cc00 face=Wingdings>w</FONT>
		<A id='visit2'   href="javascript:product_state_search='2';set_color('visit2'); quick_search();">下架</A>&nbsp;&nbsp;
	     <img alt='高级搜索' src='image/switch.png'>
    	<span href='###' onclick='advanced_search()' title='高级搜索' style='color:red;cursor:pointer;'>高级搜索</span>
 </form>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});
});

// 快速查找
var product_state_search = 1;
function quick_search(){
	var product_name = $('#product_name_search').val();
	var product_num = $('#product_num_search').val();

	var queryParams = $('#product_list').datagrid('options').queryParams;
	queryParams.product_name   = product_name;
	queryParams.product_num   = product_num;
	queryParams.product_state = product_state_search;
	$('#product_list').datagrid('load');
}
</script>