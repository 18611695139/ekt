<?php /* Smarty version 2.6.19, created on 2015-08-07 09:28:31
         compiled from datagrid_confirm_info.htm */ ?>
<div class="main-div">
<table cellspacing="0" cellpadding="0" style="width:98%" align="center" border="0" >
<tbody>
 <tr>
    <!--TODO:操作详细信息-->
    <td align="center">
      <form id="theform" name="theform">
      <?php if ($this->_tpl_vars['display_type'] >= 0): ?>
	     <div>
		     <div style="float:left;margin-left:5%;width:60%">
			    <?php if ($this->_tpl_vars['display_info']): ?>
			   <div style="overflow-y: auto; width:80%;" id="overflow">
			     <table  id="t1" cellspacing="0" cellpadding="0" style="width:90%;align:center;border-collapse: collapse;">
			        <tbody>
			        <?php $_from = $this->_tpl_vars['display_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['info']):
?>
			        <tr id="id<?php echo $this->_tpl_vars['key']; ?>
" onclick="select('id<?php echo $this->_tpl_vars['key']; ?>
')">
				          <td align="center" >
				          <input type="checkbox"  name="if_display[]" id="if_display<?php echo $this->_tpl_vars['key']+1; ?>
"  onclick="click_checkbox(<?php echo $this->_tpl_vars['key']+1; ?>
)"  <?php if ($this->_tpl_vars['info']['if_display'] == 1): ?>checked<?php endif; ?>/> 
				          <input type="hidden"  name="if_display_value[]" id="if_display_value<?php echo $this->_tpl_vars['key']+1; ?>
" cvalue="if_display_value_check" value="<?php echo $this->_tpl_vars['info']['if_display']; ?>
"/>
				          </td>
				          <td align="left" >
				          <?php echo $this->_tpl_vars['info']['name']; ?>
<input type="hidden"  name="id[]" id="id<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo $this->_tpl_vars['info']['id']; ?>
"/>
				          </td>
				          <input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
">
			         </tr>
			        <?php endforeach; endif; unset($_from); ?>
			        </tbody>
			      </table> 
		       </div>   
		       <?php endif; ?>
	    </div>
	<!--    操作按钮  -->
     <div style="float:left;margin-left:1px;margin-top:5%;">
         <input id="display_type" name="display_type" value="<?php echo $this->_tpl_vars['display_type']; ?>
" type="hidden"/>
         <div style="margin-top:10px">
         	<button type="button" class="btn btn-primary" id="cancel" title="重置">
        		<span class="glyphicon glyphicon-remove"></span> 重置
    		</button>
         </div>
	     <div style="margin-top:10px">
	     	<button type="button" class="btn btn-primary" onclick="UpSequence()" title="上移">
        		<span class="glyphicon glyphicon-arrow-up"></span> 上移
    		</button>
	     </div>
	     <div style="margin-top:10px">
	     	<button type="button" class="btn btn-primary" onclick="DownSequence()" title="下移">
        		<span class="glyphicon glyphicon-arrow-down"></span> 下移
    		</button>
	     </div>
	     <div style="margin-top:10px">
	     	<button type="button" class="btn btn-primary" id="save" title="保存">
        		<span class="glyphicon glyphicon-saved"></span> 保存
    		</button>
	     </div>
     </div>
     <!--    操作按钮  -->
    </div>
  <?php else: ?>
    <div style="vertical-align: top;width: 60%;">
        <strong>说明</strong><br />
        请设置列表显示数据，这将影响列表的显示
    </div>
  <?php endif; ?>
      </form>
    </td>
    <!--TODO:操作详细信息结束-->
 </tr>
 </tbody>
</table>
</div>

<script language="javascirpt" type="text/javascript">
var checkID;
var checkID_before;
$(document).ready(function(){
	$("#overflow").css("height",300);
});

//选中td
function select(td_id){
	checkID = td_id;
	$("#"+checkID_before).css("background-color","#ffffff");
	$("#"+checkID).css("background-color","#E4EDFE");
	checkID_before = td_id;
}

//checkbox 的 单击事件
function click_checkbox(number)
{
	if( $("#if_display"+number+"").attr("checked") == "checked" )
	{
		$("#if_display_value"+number+"").val(1);
	}
	else
	{
		$("#if_display_value"+number+"").val(0);
	}
}

//保存按钮
$('#save').click(function(){
	var display_type = $('#display_type').val();
	
	var ids = document.getElementsByName('id[]');
	var if_display = document.getElementsByName('if_display_value[]');
	var id ='';
	var if_display_value = '';
	for(j=0;j<ids.length;j++)
	{
		id += ids[j].value +'###';
		if_display_value += if_display[j].value +'###';	
	}
	
	$.ajax({
			type:'POST',
			url: "index.php?c=datagrid_confirm&m=save_datagrid_info",
			data:{display_type:display_type,id:id,if_display_value:if_display_value},
			dataType:"json",
			success: function(responce){
				if(responce["error"] == 0)
				{
					window.location.reload();
				}
				else
				{
					$.messager.alert('错误',responce["message"],'error');
				}
			}
		});
});
//重置按钮
$('#cancel').click(function(){
	document.getElementById('theform').reset();
});

//上移
function UpSequence() {
	//把他的上一个往下排把他排上去如果是第一个就不让他往上排
	if ($("#" + checkID).prev().html() != null) {

		var checkedTR = $("#" + checkID).prev();
		checkedTR.insertAfter($("#" + checkID));
	}else{
		$.messager.alert("提示", "<br>该字段已经是第一个，不能向上移动","info");
		return;
	}
}
//下移
function DownSequence() {
	//把他的下一个往上排把他排下去如果是最后一个就不让他往下排
	if ($("#" + checkID).next().html() != null) {
		var checkedTR = $("#" + checkID).next();
		checkedTR.insertBefore($("#" + checkID));
	}else{
		$.messager.alert("提示", "<br>该字段已经是最后一个，不能向下移动","info");
		return;
	}
}

</script>