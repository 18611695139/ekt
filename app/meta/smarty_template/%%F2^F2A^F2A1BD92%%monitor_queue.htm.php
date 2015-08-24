<?php /* Smarty version 2.6.19, created on 2015-08-06 17:25:47
         compiled from monitor_queue.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
var swftype = 1;
</script>
<div id="_search_panel" style='padding-bottom:5px'>
<a class="easyui-linkbutton"  href="javascript:void(0)"  iconCls="icon-histogram" onclick="javascript:swftype = 1;setTimeout(function(){updateChart(1);},1);" >柱状图</a>
<a class="easyui-linkbutton"  href="javascript:void(0)"  iconCls="icon-chart" onclick="javascript:swftype = 2;setTimeout(function(){updateChart(2);},1);" >饼状图</a>
</div>
<div id="user_list"></div><!--   技能组列表  -->
<div align="center" id="listDiv"> <!-- 图表  -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="javascript" src="jssrc/fusionCharts.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	$('#user_list').datagrid({
		nowrap: true,
		striped: true,
		rownumbers:true,
		fitColumns:true,
        pagination:true,
        pageSize:get_list_rows_cookie(),
        pageList:[50,30,10],
		sortName:'que_id',
		sortOrder:'asc',//降序排列
		idField:'que_id',
		url:'index.php?c=monitor&m=queue_list&ques='+encodeURIComponent('<?php echo $this->_tpl_vars['ques']; ?>
'),
		frozenColumns:[[
		{title:'技能组名',field:'que_name',width:100,align:'left'}
		]],
		columns:[[
		{field:'que_id',hidden:true},
		{title:'在线',field:'online',width:120,align:'center'},
		{title:'排队',field:'queue',width:100,align:'center',formatter:function(value,rowData,rowIndex){
			return value+"&nbsp;&nbsp;<span><a href='javascript:void(0);' onclick=window.parent.addTab('排队监控','index.php?c=monitor&m=calls&que_id="+rowData.que_id+"','menu_icon'); title='查看'>【查看】</a></span>";
		}},
		{title:'振铃',field:'ring',width:100,align:'center'},
		{title:'通话',field:'call',width:100,align:'center'},
		{title:'事后处理',field:'rest',width:100,align:'center'},
		{title:'就绪',field:'ready',width:100,align:'center'},
		{title:'置忙',field:'busy',width:100,align:'center'},
		{title:'技能组内坐席',field:'oper_txt',width:80,align:'center',formatter:function(value,rowData,rowIndex){
            if (rowData.online > 0) {
                return "<span><a href='javascript:void(0);' onclick=fn_view('"+rowData.que_id+"') title='查看'><img src='./image/icon_view.gif' border='0' height='16' width='16' /></a></span>";
            } else {
                return "<span><a href='javascript:void(0);' onclick=$.messager.alert('提示','该技能组没有在线坐席！','info'); title='查看'><img src='./image/icon_view.gif' border='0' height='16' width='16' /></a></span>";
            }

		}}
		]],
		onLoadSuccess: function(){
			//柱状图
			updateChart(swftype);

			//更新技能组监控信息
			update_que_monitor_data();
		}
	});
});

//更新技能组信息
var all_que_data = "";//列表中的初始技能组数据
var timeFly       = "";//定时器
function update_que_monitor_data()
{
	all_que_data = $("#user_list").datagrid("getRows");

	//计时器->更新数据
	if( timeFly != "" )
	{
		window.clearInterval(timeFly);//清空计时器，停止调用函数
	}
	timeFly = window.setInterval("get_que_updateRow()",15000);
}

/**
*  把返回的数据更新至列表中
*/
function get_que_updateRow()
{
	//获取监控数据
	$.ajax({
		type:'POST',
		url: 'index.php?c=monitor&m=get_queue_monitor_data&ques='+encodeURIComponent('<?php echo $this->_tpl_vars['ques']; ?>
'),
		dataType: "json",
		success: function(responce){
			if(responce["error"] == 0)
			{
				//更新列表数据
				$.each(all_que_data, function(property,value)
				{
					var tmp_que_d    = value.que_id;
					var update_value  = "";
					if( typeof(responce[tmp_que_d]) == "object" && !empty_obj(responce[tmp_que_d]) )
					{
						update_value = responce[tmp_que_d];
					}
					else
					{
						update_value = value;
					}

					$('#user_list').datagrid('updateRow',{
						index: property,
						row: update_value
					});
				});
				updateChart(swftype);
			}
		}
	});
}

/**
* 显示图表
*/
function view_column(dataobj)
{
	if(dataobj.length < 10)
	dataobj.max = 10;
	else
	dataobj.max = dataobj.max + 5;
	var strXML = "";
	strXML="<graph xaxisname='系统统计信息' yAxisName='个数' hovercapbg='DEDEBE' hovercapborder='889E6D' rotateNames='0' yAxisMaxValue='"+dataobj.max+"' numdivlines='9' yAxisMinValue='10' divLineColor='CCCCCC' divLineAlpha='80' decimalPrecision='0' showAlternateHGridColor='1' AlternateHGridAlpha='30' AlternateHGridColor='CCCCCC' caption='系统信息监控' baseFontSize='14' animation='0'>";
	strXML+="<set value='"+dataobj.queue+"' name='排队' color='AFD8F8' />"
	strXML+="<set value='"+dataobj.ring+"' name='振铃' color='F6BD0F' />"
	strXML+="<set value='"+dataobj.call+"' name='通话' color='8BBA00' />"
	strXML+="<set value='"+dataobj.rest+"' name='整理' color='FF8E46' />"
	strXML+="<set value='"+dataobj.ready+"' name='就绪' color='008E8E' />"
	strXML+="<set value='"+dataobj.busy+"' name='置忙' color='D64646' />"
	strXML+="</graph>";
	return strXML;
}
function view_pie(dataobj)
{
	var strXML = "";
	strXML="<graph caption='系统信息监控' baseFontSize='14' showNames='1' decimalPrecision='0' animation='0'>";
	strXML+="<set value='"+dataobj.queue+"' name='排队' color='AFD8F8' />"
	strXML+="<set value='"+dataobj.ring+"' name='振铃' color='F6BD0F' />"
	strXML+="<set value='"+dataobj.call+"' name='通话' color='8BBA00' />"
	strXML+="<set value='"+dataobj.rest+"' name='整理' color='FF8E46' />"
	strXML+="<set value='"+dataobj.ready+"' name='就绪' color='008E8E' />"
	strXML+="<set value='"+dataobj.busy+"' name='置忙' color='D64646' />"
	strXML+="</graph>";
	return strXML;
}

//图标显示： 柱状图，饼图
function updateChart(type)
{
	var dataobj    = {};
	
	$.ajax({
		type:"POST",
		url:'index.php?c=monitor&m=get_monitor_system',
		dataType:'json',
        async:false,
		success:function (responce){
			if(responce['error']=='0'){
                dataobj = responce['content'];
			}
			else
			{
				$.messager.alert('提示',responce['message'],'info');
			}
		}
	});

	var div_w = $("#listDiv").width();
	if(type == 1)
	{
		chartSWF = "./charts/Column3D.swf";
		strXML = view_column(dataobj);
		var chart1 = new FusionCharts(chartSWF,"chart1Id",div_w,400,"0","0");
		chart1.setDataXML(strXML);
		chart1.render('listDiv');
	}
	else
	{
		chartSWF = "./charts/Pie3D.swf";
		strXML = view_pie(dataobj);
		var chart1 = new FusionCharts(chartSWF,"chart1Id",div_w,400,"0","0");
		chart1.setDataXML(strXML);
		chart1.render('listDiv');
	}
}

function fn_view(que_id)
{
	parent.addTab("坐席监控","index.php?c=monitor&m=queue_agent&que_id="+que_id,"menu_icon");
}
</script>