<?php /* Smarty version 2.6.19, created on 2015-08-07 09:20:56
         compiled from service_search_base.htm */ ?>
<form action="javascript:quick_search('flag')" method="POST" name="searchForm" id="searchForm">
 <div>
     &nbsp;&nbsp;<IMG style="VERTICAL-ALIGN: middle" border=0 alt="分类" src="image/icon_listsearch.png">
	<FONT color=#cc0066 >快速查找</FONT>&nbsp;&nbsp;
	<?php if ($this->_tpl_vars['power_service_alldata']): ?>
	<A id=all1   href="javascript:all_type = '1';set_color_all('all1'); quick_search('flag');" title="全部数据" >全部数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	<?php endif; ?>
	<A id=all2   href="javascript:all_type = '2';set_color_all('all2'); quick_search('flag');" style="color:red;" title="我受理的数据和转交给我的数据"  >我的数据</A>&nbsp;&nbsp;<img src='./image/menu_arrow.gif'>&nbsp;
	<A id=all3   href="javascript:all_type = '3';set_color_all('all3'); quick_search('flag');" title="我受理的数据" >我受理的数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	<A id=all4   href="javascript:all_type = '4';set_color_all('all4'); quick_search('flag');" title="转交给我的数据" >转交给我的数据</A>&nbsp<FONT color=#99cc00 face=Wingdings>|</FONT>
	
	<?php $_from = $this->_tpl_vars['service_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
        $this->_foreach['list_Name']['iteration']++;
?>
	<A id="service_type_<?php echo $this->_tpl_vars['sitem']['name']; ?>
"   href="javascript:service_type='<?php echo $this->_tpl_vars['sitem']['name']; ?>
';set_color_type('service_type_<?php echo $this->_tpl_vars['sitem']['name']; ?>
'); quick_search();"><?php echo $this->_tpl_vars['sitem']['name']; ?>
</A>
	<?php if (($this->_foreach['list_Name']['iteration']-1) == $this->_tpl_vars['service_type_count']): ?>&nbsp;<FONT color=#99cc00 face=Wingdings>|</FONT><?php else: ?><FONT color=#99cc00 face=Wingdings>w</FONT><?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	
	<?php $_from = $this->_tpl_vars['service_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list_Name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_Name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['sitem']):
        $this->_foreach['list_Name']['iteration']++;
?>
	<A id="service_state_<?php echo $this->_tpl_vars['skey']; ?>
"   href="javascript:service_state='<?php echo $this->_tpl_vars['sitem']; ?>
';set_color_state('service_state_<?php echo $this->_tpl_vars['skey']; ?>
'); quick_search();" ><?php echo $this->_tpl_vars['sitem']; ?>
</A><FONT color=#99cc00 face=Wingdings>w</FONT>
 	<?php endforeach; endif; unset($_from); ?>
</div>
<div>
  <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
  关键字 <input id="search_serv_content" name="search_serv_content" value="" />
   &nbsp;&nbsp;
   <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a>
     <img alt='高级搜索' src='image/switch.png'>
     <span href='###' onclick='advanced_search()' title='高级搜索' style='color:red;cursor:pointer;'>高级搜索</span>
</div>
</form>

<script language="JavaScript" type="text/javascript">
/*1:全部数据/2:我的数据/3:我受理的数据/4:我处理的数据*/
function set_color_all(id)
{
	$('#searchForm a').css('color','#335b64');
	$('#'+id).css('color','red');

	/*服务类型*/
	service_type  = "";
	last_type_id  = "";
	/*服务状态*/
	service_state = "";
	lasr_state_id = "";
}

//服务类型
var last_type_id = "";
function set_color_type(id)
{
	if(last_type_id)
	{
		$("#"+last_type_id+"").css('color','#335b64');
	}

	$('#'+id).css('color','red');
	last_type_id = id;

	$("#search_serv_content").val("");
}

//服务状态
var lasr_state_id = "";
function set_color_state(id)
{
	if( lasr_state_id )
	{
		$("#"+lasr_state_id+"").css('color','#335b64');
	}

	$('#'+id).css('color','red');
	lasr_state_id = id;

	$("#search_serv_content").val("");
}

//检索
/*1:全部数据/2:我的数据/3:我受理的数据/4:我处理的数据*/
var all_type = 2;
/*服务状态*/
var service_state = "";
/*服务类型*/
var service_type = "";
function quick_search()
{
	var flag = 0; //  0:快速查找  1:“搜索”按钮
	if( arguments[0])
	{
		flag = 1;
	}

	/*主题*/
	var search_serv_content = "";
	if( flag == 1 )//1:“搜索”按钮
	{
		search_serv_content = $("#search_serv_content").val();

		/*服务类型*/
		service_type  = "";
		$("#"+last_type_id+"").css('color','#335b64');
		last_type_id  = "";
		/*服务状态*/
		service_state = "";
		$("#"+lasr_state_id+"").css('color','#335b64');
		lasr_state_id = "";
	}
	$('#service_list').datagrid('options').queryParams = {};
	var queryParams = $('#service_list').datagrid('options').queryParams;
	queryParams.all_type          = all_type;
	queryParams.service_type      = service_type;
	queryParams.service_state     = service_state;
	queryParams.search_serv_content = search_serv_content;
	$('#service_list').datagrid('load');
}
</script>