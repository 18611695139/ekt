<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:11
         compiled from address_book.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div">
 
    <table><tbody><tr>
     <td>关键字：</td><td><input type="text" name="search_key" id="search_key" size="25" class="easyui-searchbox" searcher="quick_search" prompt="支持拼音、汉字查询" /></td>
     <td>
      <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm">
     <IMG style="VERTICAL-ALIGN: middle" border=0 alt='分类' src="image/icon_listsearch.png">
	<FONT color=#cc0066>快速查询：</FONT>
	<A id=sd0   href="javascript:sql_type='11';set_color('sd0'); quick_search();" style="color:red;" >所有通讯录</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	<A id=sd1   href="javascript:sql_type='2';set_color('sd1'); quick_search();" >我的通讯录</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>	
	<A id=sd2   href="javascript:sql_type='1';set_color('sd2'); quick_search();">公司通讯录</A>&nbsp;
	<button type="button" class="btn btn-primary" onclick="add_own_address_book()">
        <span class="glyphicon glyphicon-plus"></span> 添加通讯录
    </button>
	 <?php if ($this->_tpl_vars['power_company_address_book']): ?>
  	<button type="button" class="btn btn-primary" onclick="add_company_address_book()">
        <span class="glyphicon glyphicon-plus"></span> 添加公司通讯录
    </button>
 	 <?php endif; ?>
 	 </form>
 	 </td></tr></tbody></table>
  
</div>
<table id="address_book_table"></table> <!-- 通讯录列表  -->
<div id="add_book"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/viewjs/address_book.js?1.1" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var user_id = "<?php echo $this->_tpl_vars['user_id']; ?>
";
var power_company_address_book = <?php echo $this->_tpl_vars['power_company_address_book']; ?>
;
var power_sendsms = <?php echo $this->_tpl_vars['power_sendsms']; ?>
;
</script>