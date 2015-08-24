<?php /* Smarty version 2.6.19, created on 2015-07-21 11:10:52
         compiled from callrecord_list.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'callrecord_list.htm', 41, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-div" id="_search_panel">
  <form action="javascript:quick_search()" method="POST" name="searchForm" id="searchForm" class="form-inline">
     <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
     电话<input id="search_key" name="search_key"  type="text" value="" style="width:150px;" />
	<?php if ($this->_tpl_vars['power_callrecord'] || $this->_tpl_vars['role_type'] != 2): ?>
	  坐席<input type="text" id="user_tree" name="user_tree" value="" style="width:150px;" />
	 <?php endif; ?>
	 时间
	 <div class="input-append">
            <input type="text" name="start_date" id="start_date" value="<?php echo $this->_tpl_vars['today_start']; ?>
" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'start_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div> ~
        <div class="input-append">
            <input type="text" name="end_date" id="end_date" value="<?php echo $this->_tpl_vars['today_end']; ?>
" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'end_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div>
	 时长>
	 <input id="min_conn_secs" name="min_conn_secs" type='text' value="" style="width:60px;" />
	 秒
	<button class="btn btn-primary" onclick="$('#searchForm').submit();">
        <span class="glyphicon glyphicon-search"></span> 搜索
    </button>
	<?php if ($this->_tpl_vars['power_callrecord'] || $this->_tpl_vars['role_type'] != 2): ?>
	<img style="VERTICAL-ALIGN: middle" border='0' alt="分类" src="image/icon_listsearch.png"/>
	<a id='sd0' href="javascript:sql_type='1';set_color('sd0'); quick_search();"  >全部数据</a><font color='#99cc00' face='Wingdings'>w</font>
	<a id='sd1' href="javascript:sql_type='2';set_color('sd1'); quick_search();" style="color:red;" >我的数据</a>	
	<?php endif; ?>
	<div style="display: block;margin: 5px"></div>
   <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "player.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </form>
</div>
<div id="call_recod_list"></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var start_date = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['today_start'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var today_end = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['today_end'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
var message_authority = <?php echo $this->_tpl_vars['message_authority']; ?>
;//发短信
var power_phone_view = <?php echo $this->_tpl_vars['power_phone_view']; ?>
;//客户电话（显示）
var power_callrecord = <?php echo $this->_tpl_vars['power_callrecord']; ?>
;//通话记录（全部）
var power_download_record = <?php echo $this->_tpl_vars['power_download_record']; ?>
;//录音下载
var role_type = '<?php echo $this->_tpl_vars['role_type']; ?>
';
</script>
<script src="./jssrc/viewjs/callrecord_list.js?1.4" type="text/javascript"></script>