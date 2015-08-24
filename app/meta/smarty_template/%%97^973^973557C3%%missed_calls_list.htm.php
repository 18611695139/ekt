<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:06
         compiled from missed_calls_list.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'missed_calls_list.htm', 20, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'>
 <form action="javascript:quick_search()" name="searchForm" id="searchForm">
      <table><tbody><tr><td>
        <IMG style="VERTICAL-ALIGN: middle" border=0 alt="分类" src="image/icon_listsearch.png">
		<FONT color=#cc0066 >快速查找</FONT>&nbsp;&nbsp;
		<?php if ($this->_tpl_vars['role_type'] != 2 && $this->_tpl_vars['power_wjld_department']): ?>
		<A id=all1   href="javascript:all_type = '1';set_color_all('all1'); quick_search();" <?php if ($this->_tpl_vars['power_wjld_department']): ?>style="color:red;"<?php endif; ?>  >全部数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
		<A id=all2   href="javascript:all_type = '2';set_color_all('all2'); quick_search();" >我的数据</A>&nbsp;&nbsp;<img src='./image/menu_arrow.gif'>&nbsp;
		<?php endif; ?>
		<A id=deal1   href="javascript:deal_type = '1';set_color_all('deal1'); quick_search();" <?php if (! $this->_tpl_vars['power_wjld_department']): ?>style="color:red;"<?php endif; ?> >未处理</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
		<A id=deal2   href="javascript:deal_type = '2';set_color_all('deal2'); quick_search();" >已处理</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
		<?php if ($this->_tpl_vars['role_type'] != 2 && $this->_tpl_vars['power_wjld_department']): ?>
		<A id=locate2   href="javascript:locate_type = '2';set_color_all('locate2'); quick_search();" style="color:red;" >未分配</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
		<A id=locate1   href="javascript:locate_type = '1';set_color_all('locate1'); quick_search();" >已分配</A>&nbsp;<FONT color=#99cc00 face=Wingdings></FONT>
		<?php endif; ?>
		</td></tr>
		<tr><td>
	   <img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
      日 期：<span class="combo datebox" style="width: 95px;"><input type="text" class="combo-text validatebox-text" id='deal_date_search_start' name='deal_date_search_start' value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['start_day'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
" style="width: 75px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_date_search_start' })" ></span></span></span> ~  <span class="combo datebox" style="width: 95px;"><input type="text" class="combo-text validatebox-text" id='deal_date_search_end' name='deal_date_search_end' value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['end_day'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"  style="width: 75px;" ><span><span class="combo-arrow combo-arrow-hover" onclick="WdatePicker({el: 'deal_date_search_end' })" ></span></span></span>
      &nbsp;来电号<input type="text" id="search_caller" name="search_caller" value=""/>
       <a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)"  onclick="$('#searchForm').submit();" >搜索</a></td></tr>
    <tr><td>
        <div style="display: block;margin: 5px"></div>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "player_voice.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </td></tr></tbody></table>
    </form>
</div>

<div id='missed_calls_list'></div>
<div id='missed_calls_window'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var role_type = <?php echo $this->_tpl_vars['role_type']; ?>
;
var power_wjld_department = <?php echo ((is_array($_tmp=@$this->_tpl_vars['power_wjld_department'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
$(document).ready(function() {
	//text绑定回车事件
	$("input[type='text']").keydown(function(event){
		if(event.keyCode == 13){
			$('#searchForm').submit();
		}
	});

	//设置列表
	$('#missed_calls_list').datagrid({
		title:"未接来电列表",
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'start_time',
		sortOrder:'desc',//降序排列
		idField:'id',
		url:"index.php?c=missed_calls&m=missed_calls_query",
		queryParams:{<?php if ($this->_tpl_vars['role_type'] != 2 && $this->_tpl_vars['power_wjld_department']): ?>"all_type":"1","locate_type":"2",<?php else: ?>"all_type":"2",<?php endif; ?>"deal_date_search_start":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['start_day'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","deal_date_search_end":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['end_day'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"<?php if (! $this->_tpl_vars['power_wjld_department']): ?>,"deal_type":"1"<?php endif; ?>},
		frozenColumns:[[
		<?php if ($this->_tpl_vars['power_wjld_department']): ?>
		{field:'ck',checkbox:true},
		<?php endif; ?>
		{title: '录音', field: 'call_id', width:60,align:'center',formatter:function(value,rowData,rowIndex){
			if( value > 0 && ( rowData.reason == 102 || rowData.reason == 104) )
			{
				var str = '<a href="###" onclick=fn_listen_voice('+value+'); title="收听该录音" ><img src="./image/play_icon.gif" border="0" height="16" width="16" align="absmiddle"/></a>';
				<?php if ($this->_tpl_vars['power_download_record']): ?>
				str += '<a href="###" onclick=fn_download_voice('+value+'); title="下载该录音" ><img src="./image/disk.png"  border="0" height="16" width="16" align="absmiddle"/></a>';
				<?php endif; ?>
				return str;
			}
			else
			return '';
		}},
		{title:'操作',field:'oper_txt',width:80,align:"CENTER",formatter:function(value,rowData,rowIndex){
			if(rowData.state==0)
			return "<a href='###' onclick=accept('"+rowData.id+"','"+rowData.caller+"','"+rowData.cle_id+"',0)  title='处理'>【处理】</a>";
			else
			return '【已处理】';
		}}
		]],
		columns:[[
		{title: '客户名称' ,field: 'cle_name' ,width:120,sortable:true},
		{title: '未接来电号' ,field: 'caller' ,width:130,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var show_phone = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_phone = hidden_part_number(value);
				<?php endif; ?>
				return "<span><a href='javascript:;' onclick=accept('"+rowData.id+"','"+rowData.caller+"','"+rowData.cle_id+"',1)  title='呼叫'>"+show_phone+"&nbsp;&nbsp;<img src='./image/phone.png' border='0' height='16' width='16' align='absmiddle' /></a>&nbsp;&nbsp;<?php if ($this->_tpl_vars['power_sendsms']): ?><a href='javascript:;' onclick=sys_send_sms("+value+"); title='短信'><img src='./image/message.png' border='0' height='16' width='16' align='absmiddle' /> </a><?php endif; ?></span>";
			}
			else
			return value;
		}},
		{title: '状态' ,field: 'state' ,width:90,sortable:true,formatter:function(value,rowData,rowIndex){
			if(value==0)
			return "<span style='color:red;'>未处理</span>";
			else
			return '已处理';
		}},
		{title: '来电时间' ,field: 'date' ,width:130,sortable:true},
		{title: '接入号' ,field: 'server_num' ,width:130,sortable:true},
		{title: '部门' ,field: 'dept_name' ,width:120,sortable:true},
		{title: '坐席' ,field: 'user_name' ,width:100,sortable:true},
		{title: '原因' ,field: 'miss_reason' ,width:200,sortable:true},
		{title:"id" ,field:"id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#missed_calls_list').datagrid('clearSelections');
			$('#missed_calls_list').datagrid('clearChecked');
		}
		<?php if ($this->_tpl_vars['power_wjld_department']): ?>
		,
		toolbar:[
		{
			iconCls:'icon-next',
			text:'选中分配',
			handler:function(){
				var ids = getSelections();
				if(ids == '')
				{
					$.messager.alert('提示','<br>请选择需要分配的未接来电！','error');
					return;
				}
				else
				{
					_missed_calls_window(ids);

				}
			}
		},'-',
		{
			iconCls:'icon-text',
			text:'批量分配',
			handler:function(){
				_miss_calls_batch();
			}
		}]
		<?php endif; ?>
	});
	var pager = $('#missed_calls_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});

/*下载留言*/
function fn_download_voice(callid){
    var url = 'index.php?c=missed_calls&m=get_voice_url&callid='+callid;
    est_header("location:"+url);
}



</script>  
<script src="./jssrc/viewjs/missed_calls_list.js" type="text/javascript"></script>