<?php /* Smarty version 2.6.19, created on 2015-08-07 10:48:33
         compiled from statistics_dept_user_tree.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id='_search_panel' class='form-div'>
 <form action="javascript:quick_search()" name="searchForm" id="searchForm" class="form-inline">
		<img src="image/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
         日期  
        <div class="input-append">
            <input type="text" name="deal_date_search_start" id="deal_date_search_start" value="<?php echo $this->_tpl_vars['today_date']; ?>
" style="width:100px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'deal_date_search_start'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div> ~
        <div class="input-append">
            <input type="text" name="deal_date_search_end" id="deal_date_search_end" value="<?php echo $this->_tpl_vars['today_date']; ?>
" style="width:100px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'deal_date_search_end'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div>
         
          <button class="btn btn-primary" onclick="$('#searchForm').submit();">
        	<span class="glyphicon glyphicon-search"></span> 搜索
    	  </button>
          <span style="color:red;">注：默认显示当天的数据</span>
          &nbsp;&nbsp;&nbsp;&nbsp;
           时长格式&nbsp;&nbsp;
    	  <input type="radio" name="timeFormate" onclick="change_timeFormate(1)" checked /> 时:分:秒 
    	  <input type="radio" name="timeFormate" onclick="change_timeFormate(2)"/> 秒
    </form>
   
    <div style="MARGIN-BOTTOM: 3px;MARGIN-TOP: 5px;MARGIN-LEFT: 5px;">
    说明：<br/>
    坐席呼通量：指坐席接通的数量(包括呼入/呼出).&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
    坐席呼出量：指坐席呼出的数量(包括接通/未接通). &nbsp;&nbsp;&nbsp;坐席呼出接通量：指坐席呼出且接通的数量.</div>
    <div style="MARGIN-LEFT: 5px;">坐席未呼通量：指坐席未接通的数量(包括呼入/呼出).	&nbsp;&nbsp;&nbsp; 坐席呼入量：指呼叫坐席的数量(包括接通/未接通). &nbsp;&nbsp;&nbsp;坐席呼入接通量：指呼叫坐席且接通的数量.</div>
  
</div>

<div id='statistics_list'></div>
<div id='show_sta_detail'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
var state_time = '<?php echo $this->_tpl_vars['today_date']; ?>
';
var end_time = '<?php echo $this->_tpl_vars['today_date']; ?>
';
var timeFormate = 1;
$(document).ready(function() {
	<?php unset($this->_sections['stage']);
$this->_sections['stage']['name'] = 'stage';
$this->_sections['stage']['loop'] = is_array($_loop=$this->_tpl_vars['stages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['stage']['show'] = true;
$this->_sections['stage']['max'] = $this->_sections['stage']['loop'];
$this->_sections['stage']['step'] = 1;
$this->_sections['stage']['start'] = $this->_sections['stage']['step'] > 0 ? 0 : $this->_sections['stage']['loop']-1;
if ($this->_sections['stage']['show']) {
    $this->_sections['stage']['total'] = $this->_sections['stage']['loop'];
    if ($this->_sections['stage']['total'] == 0)
        $this->_sections['stage']['show'] = false;
} else
    $this->_sections['stage']['total'] = 0;
if ($this->_sections['stage']['show']):

            for ($this->_sections['stage']['index'] = $this->_sections['stage']['start'], $this->_sections['stage']['iteration'] = 1;
                 $this->_sections['stage']['iteration'] <= $this->_sections['stage']['total'];
                 $this->_sections['stage']['index'] += $this->_sections['stage']['step'], $this->_sections['stage']['iteration']++):
$this->_sections['stage']['rownum'] = $this->_sections['stage']['iteration'];
$this->_sections['stage']['index_prev'] = $this->_sections['stage']['index'] - $this->_sections['stage']['step'];
$this->_sections['stage']['index_next'] = $this->_sections['stage']['index'] + $this->_sections['stage']['step'];
$this->_sections['stage']['first']      = ($this->_sections['stage']['iteration'] == 1);
$this->_sections['stage']['last']       = ($this->_sections['stage']['iteration'] == $this->_sections['stage']['total']);
?><?php endfor; endif; ?>
	$('#statistics_list').treegrid({
		title:'工作量统计',
		nowrap: true,
		striped: true,
		rownumbers:true,
		checkOnSelect:false,
		url:'index.php?c=statistics&m=get_statistics_tree_info',
		idField:'id',
		treeField:'text',
		frozenColumns:[[
		{title:'部门 | 坐席',field:'text',width:180,formatter:function(value,rowData,rowIndex){
			return "<span style='color:red'>"+value+"</span>";
		}}
		]],
		columns:[[
		{title: "话务统计",align:"CENTER",colspan:10}
		<?php if ($this->_tpl_vars['client_base']['cle_stage']): ?>
		,{title: "转化量",align:"CENTER",colspan:2},
		{title: "退化量" ,field: 'recede_num' ,rowspan:2,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='') return 0;
			return rowData['row'].recede_num;
		}}
		<?php if ($this->_sections['stage']['total'] > 0): ?>
		,{title: "转化分步",align:"CENTER",colspan:<?php echo $this->_sections['stage']['total']; ?>
}
		<?php endif; ?>
		<?php endif; ?>
		],[
		{title: "登录时长" ,field:'login_secs',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.attributes != 'last')
			return '-';
			else
			{
				if(timeFormate==1)
				{
					if(rowData.login_secs_timeFormate!='00:00:00')
					{
						return "<a href='###' onclick='show_detail("+rowData.user_id+")' style='text-decoration: underline;'>"+rowData.login_secs_timeFormate+"</a>";
					}
					else
					{
						return rowData.login_secs_timeFormate;
					}
				}
				else
				{
					if(rowData.login_secs!='0')
					{
						return "<a href='###' onclick='show_detail("+rowData.user_id+")' style='text-decoration: underline;'>"+rowData.login_secs+"</a>";
					}
					else
					{
						return rowData.login_secs;
					}
				}
			}
		}},
		{title: "就绪时长" ,field:'ready_secs',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.attributes != 'last')
			return '-';
			else
			{
				if(timeFormate==1)
				{
					return rowData.ready_secs_timeFormate;
				}
				else
				{
					return rowData.ready_secs;
				}
			}
		}},
		{title: "置忙时长" ,field:'busy_secs',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.attributes != 'last')
			return '-';
			else
			{
				if(timeFormate==1)
				{
					return rowData.busy_secs_timeFormate;
				}
				else
				{
					return rowData.busy_secs;
				}
			}
		}},
		{title: "通话时长" ,field:'conn_secs',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(timeFormate==1)
			{
				if(rowData['row']=='')
				return '00:00:00';
				else
				return rowData['row'].conn_secs_timeFormate;
			}
			else
			{
				if(rowData['row']=='')
				return '0';
				else
				return rowData['row'].conn_secs;
			}
		}},
		{title: "坐席呼通量" ,field:'conn_success',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if( rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return rowData['row'].conn_success;
		}},
		{title: "坐席未呼通量" ,field:'conn_fail',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return (rowData['row'].conn_num - rowData['row'].conn_success);
		}},
		{title: "坐席呼出量" ,field: 'conn_out_num' ,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return rowData['row'].conn_out_num;
		}},
		{title: "坐席呼出接通量" ,field:'conn_success_out',align:"CENTER",width:100,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return rowData['row'].conn_success_out;
		}},
		{title: "坐席呼入量" ,field:'conn_in_num',align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return rowData['row'].conn_in_num;
		}},
		{title: "坐席呼入接通量" ,field:'conn_success_in',align:"CENTER",width:100,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='')
			return 0;
			else
			return rowData['row'].conn_success_in;
		}},
		<?php if ($this->_tpl_vars['client_base']['cle_stage']): ?>
		{title: "新增量" ,field: 'new_increment' ,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='') return 0;
			return rowData['row'].new_increment;
		}},
		{title: "回访量" ,field: 'back' ,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='') return 0;
			return (rowData['row'].transformation - rowData['row'].new_increment);

		}},
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['stages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['key']['show'] = true;
$this->_sections['key']['max'] = $this->_sections['key']['loop'];
$this->_sections['key']['step'] = 1;
$this->_sections['key']['start'] = $this->_sections['key']['step'] > 0 ? 0 : $this->_sections['key']['loop']-1;
if ($this->_sections['key']['show']) {
    $this->_sections['key']['total'] = $this->_sections['key']['loop'];
    if ($this->_sections['key']['total'] == 0)
        $this->_sections['key']['show'] = false;
} else
    $this->_sections['key']['total'] = 0;
if ($this->_sections['key']['show']):

            for ($this->_sections['key']['index'] = $this->_sections['key']['start'], $this->_sections['key']['iteration'] = 1;
                 $this->_sections['key']['iteration'] <= $this->_sections['key']['total'];
                 $this->_sections['key']['index'] += $this->_sections['key']['step'], $this->_sections['key']['iteration']++):
$this->_sections['key']['rownum'] = $this->_sections['key']['iteration'];
$this->_sections['key']['index_prev'] = $this->_sections['key']['index'] - $this->_sections['key']['step'];
$this->_sections['key']['index_next'] = $this->_sections['key']['index'] + $this->_sections['key']['step'];
$this->_sections['key']['first']      = ($this->_sections['key']['iteration'] == 1);
$this->_sections['key']['last']       = ($this->_sections['key']['iteration'] == $this->_sections['key']['total']);
?>
		{title: "<?php echo $this->_tpl_vars['stages'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['stages'][$this->_sections['key']['index']]['stage_id']; ?>
' ,align:"CENTER",width:80,formatter:function(value,rowData,rowIndex){
			if(rowData.id == 0) return '-';
			if(rowData['row']=='') return 0;
			return rowData['row'].<?php echo $this->_tpl_vars['stages'][$this->_sections['key']['index']]['stage_id']; ?>
;
		}},
		<?php endfor; endif; ?>
		<?php endif; ?>
		{title:"id" ,field:"id",hidden:true}
		]],
		toolbar:[
		{
			iconCls:'icon-up',
			text:"导出",
			handler:function(){
				window.location.href = 'index.php?c=statistics&m=output_statistics_data&state_time='+state_time+'&end_time='+end_time+'&timeFormate='+timeFormate;
			}
		},'-'
		],
		onBeforeLoad:function(row,param)
		{
			param.state_time = state_time;
			param.end_time = end_time;
			if (!row)
			{ // load top level rows
				param.id = 0;   // set id=0, indicate to load new page rows
			}
		}
	});
});

/*搜索*/
function quick_search()
{
	state_time = $("#deal_date_search_start").val();
	end_time = $("#deal_date_search_end").val();
	$('#statistics_list').treegrid('reload');
}

/*操作详情*/
function show_detail(user_id)
{
	$('#show_sta_detail').window({
		title: '最近操作详情',
		href:"index.php?c=statistics&m=get_user_sta_detail&user_id="+user_id+"&start_time="+state_time+"&end_time="+end_time,
		top:60,
		width:500,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

/*改变时长显示方式*/
function change_timeFormate(Formate)
{
	timeFormate = Formate;
	$('#statistics_list').treegrid('reload');
}

</script>