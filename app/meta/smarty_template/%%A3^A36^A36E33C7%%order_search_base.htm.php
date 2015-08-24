<?php /* Smarty version 2.6.19, created on 2015-07-21 11:11:24
         compiled from order_search_base.htm */ ?>
<form action="javascript:quick_search()" method="POST" name="searchForm"  id="searchForm">
<div>
  &nbsp;&nbsp;<IMG style="VERTICAL-ALIGN: middle" border=0 alt="分类" src="image/icon_listsearch.png">
	<FONT color=#cc0066 >快速查找</FONT>
	<?php if ($this->_tpl_vars['role_type'] != 2): ?>
	<A id=all1   href="javascript:all_type = '1';set_color_all('all1'); quick_search();" >全部数据</A>&nbsp;<FONT color=#99cc00 face=Wingdings>w</FONT>
	<?php endif; ?>
	<A id=all2   href="javascript:all_type = '2';set_color_all('all2'); quick_search();" style="color:red;">我的数据</A>
	&nbsp;&nbsp;<img src='./image/menu_arrow.gif'>&nbsp;
	<?php $_from = $this->_tpl_vars['order_state']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['o_state']):
?>
	<A id=state<?php echo $this->_tpl_vars['skey']; ?>
  href="javascript:state_type='<?php echo $this->_tpl_vars['o_state']['name']; ?>
';set_color_state('state<?php echo $this->_tpl_vars['skey']; ?>
'); quick_search();"><?php echo $this->_tpl_vars['o_state']['name']; ?>
</A><FONT color=#99cc00 face=Wingdings>w</FONT>
 	<?php endforeach; endif; unset($_from); ?>
</div>
<div style="display: block;margin: 5px" class="form-inline">
     订单编号 <input type="text" id="order_num_search" name="order_num_search" class="span2" />
	 配送单号 <input type="text" id="order_delivery_number_search" name="order_delivery_number_search" class="span2" />
	 下单时间
	 <div class="input-append">
            <input type="text" name="order_accept_time_start" id="order_accept_time_start" value="" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'order_accept_time_start',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
        </div> ~
        <div class="input-append">
            <input type="text" name="order_accept_time_end" id="order_accept_time_end" value="" style="width:120px;" readonly>
            <button type="button" role="date" class="btn" onclick="WdatePicker({el: 'order_accept_time_end',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                <span class="glyphicon glyphicon-calendar"></span>
            </button>
     </div>
    <button class="btn btn-primary" onclick="$('#searchForm').submit();">
        <span class="glyphicon glyphicon-search"></span> 搜索
    </button>
    <button type="button" class="btn" onclick="advanced_search_order()">
        <span class="glyphicon glyphicon-zoom-in"></span> 高级搜索
    </button>
 </div>
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
/*1:全部数据/2:我的数据*/
function set_color_all(id)
{
	$('#searchForm a').css('color','#335b64');
	$('#'+id).css('color','red');

	/*订单状态*/
	state_type = "";
}

var last_state_color = '';
function set_color_state(id)
{
	if(last_state_color)
	{
		$('#'+last_state_color).css('color','#335b64');
	}
	$('#'+id).css('color','red');
	last_state_color = id;

	$('#order_num_search').val('');
	$('#order_accept_time_start').val('');
	$('#order_accept_time_end').val('');
}
/* 全部数据1 我的数据2*/
var all_type = '2';
/*订单状态*/
var state_type = '';
// 快速查找
function quick_search(){
	var order_num = $('#order_num_search').val();
	var order_delivery_number = $('#order_delivery_number_search').val();
	var order_accept_time_start = $('#order_accept_time_start').val();
	var order_accept_time_end = $('#order_accept_time_end').val();
	if(order_num.length!=0 || order_accept_time_start.length!=0 || order_accept_time_end.length!=0)
	{
		set_color_all('all'+all_type);
	}
	$('#order_list').datagrid('options').queryParams = {};
	var queryParams = $('#order_list').datagrid('options').queryParams;
	queryParams.order_num = order_num;
	queryParams.order_delivery_number = order_delivery_number;
	queryParams.order_accept_time_start = order_accept_time_start;
	queryParams.order_accept_time_end = order_accept_time_end;
	queryParams.all_type = all_type;
	queryParams.order_state = state_type;
	$('#order_list').datagrid('load');
}
</script>