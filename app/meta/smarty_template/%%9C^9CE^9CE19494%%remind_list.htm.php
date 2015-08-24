<?php /* Smarty version 2.6.19, created on 2015-07-21 11:10:51
         compiled from remind_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id='_search_panel'>
    <form action="javascript:quick_search()" method="POST" name="searchForm"  id="searchForm" class="form-inline">
	         <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
	         提醒时间：
	         <div class="input-append">
            	<input type="text" name="start_time" id="start_time" value="" style="width:120px;" readonly>
            	<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'start_time',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                	<span class="glyphicon glyphicon-calendar"></span>
            	</button>
        	</div> ~
        	<div class="input-append">
            	<input type="text" name="end_time" id="end_time" value="" style="width:120px;" readonly>
            	<button type="button" role="date" class="btn" onclick="WdatePicker({el: 'end_time',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                	<span class="glyphicon glyphicon-calendar"></span>
            	</button>
        	</div>
	         <button class="btn btn-primary" onclick="$('#searchForm').submit();">
        		<span class="glyphicon glyphicon-search"></span> 搜索
    		</button>
            <IMG style="VERTICAL-ALIGN: middle" border=0 alt='分类' src="image/icon_listsearch.png">
		<FONT color=#cc0066>快速查找 </FONT>
			<A id=sd0  href="javascript:sql_type='1';set_color('sd0');quick_search();">全部数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
			<A id=sd1  href="javascript:sql_type='2';set_color('sd1');quick_search();">今日提醒</A><FONT color=#99cc00 face=Wingdings>w</FONT>
			<A id=sd2  href="javascript:sql_type='3';set_color('sd2');quick_search();">过期未处理提醒</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
			<A id=sd3  href="javascript:sql_type='4';set_color('sd3');quick_search();">7日内提醒</A><FONT color=#99cc00 face=Wingdings>w</FONT>
			<A id=sd4  href="javascript:sql_type='5';set_color('sd4');quick_search();"   style="color:red;">未处理</A><FONT color=#99cc00 face=Wingdings>w</FONT>
			<A id=sd5  href="javascript:sql_type='6';set_color('sd5');quick_search();">已处理</A>
    </form>
</div>
<div id='remind_list'></div>
<div id='set_remind'></div>
<div id='view_remind'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script src="./jssrc/viewjs/remind_list.js?1.1" type="text/javascript"></script>