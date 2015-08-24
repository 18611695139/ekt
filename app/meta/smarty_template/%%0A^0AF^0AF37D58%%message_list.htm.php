<?php /* Smarty version 2.6.19, created on 2015-07-21 11:10:50
         compiled from message_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<div id="personal_address" title="收件箱" icon="icon_shoujian" style="display:block;">
		<div class="form-div" id='_search_panel'>
		<table><tbody>
		<tr><td>关键字：</td><td><input type="text" name="keyword1" id="keyword1" size="25" class="easyui-searchbox" searcher="quick_search" prompt="请输入搜索内容" /></td>
		<td>
		<form action="javascript:quick_search()" name="searchForm" id="searchForm">
		<IMG style="VERTICAL-ALIGN: middle" border=0 alt=分类 src="image/icon_listsearch.png">
	    <FONT color=#cc0066>快速查找 </FONT>
	    <A id=sd0  href="javascript:sql_type='1';set_color('sd0');quick_search();" >全部数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	    <A id=sd1  href="javascript:sql_type='2';set_color('sd1');quick_search();" style="color:red;">未读</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	    <A id=sd2  href="javascript:sql_type='3';set_color('sd2');quick_search();">已读</A>&nbsp;&nbsp;&nbsp;
	    </form>
	    </td><td>
	    <IMG alt='转到发件箱' src='image/switch.png'>
    	 <A href='javascript:quick_send_box()' title='转到发件箱' style='color:red;'>转到发件箱</A>
    	 	
    	 </td>
    	 </tr>
	
		</tbody></table>
		</div>
		<div id="list_table1"></div>
	</div>	
	
	
	<div id='message_panel'></div>
	<div id="window" title="详细信息" style="width:600px;height:300px;padding:10px;display:none">
		<div style="padding-top:5px;padding-bottom:5px;background:#fff;">

					<label>详细信息：</label>
					<div id="SR_content" style="border:1px solid #ccc;width:100%;height:200px;font-family:verdana;padding-top:5px"></div>
			
		</div>
		
	</div>

<style>
.icon_shoujian{
	background:url('./image/shoujian.png') no-repeat;
}
.icon_fajian{
	background:url('./image/fajian.png') no-repeat;
}
</style>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
var power_sendxx = '<?php echo $this->_tpl_vars['power_sendxx']; ?>
';
</script>
<script src="./jssrc/viewjs/message_list.js" type="text/javascript"></script>