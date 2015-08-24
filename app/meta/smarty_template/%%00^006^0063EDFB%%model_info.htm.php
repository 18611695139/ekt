<?php /* Smarty version 2.6.19, created on 2015-07-21 11:10:07
         compiled from model_info.htm */ ?>
<div class="form-div">     
模板名称<input id="panel_model_name" name="panel_model_name"  type="text"  size="40"  value="<?php echo $this->_tpl_vars['model_name']; ?>
" class="easyui-validatebox" required="true"  missingMessage="名称不能为空"/>
</div>
<table style="margin-top:-13">
<tbody>
<tr>
<td width="70%">
  <div style="overflow-y: auto; height: 370px; width:100%;border:1px solid gray">
	<table style="width:100%" align="center" cellpadding="1" border="0" style="background-color: #F4FAFB;border-collapse: collapse;" id="_model_table">
	<tbody>	
      <?php $_from = $this->_tpl_vars['model_field']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['info']):
?>
         <tr style="padding: 2px;"  id="_id<?php echo $this->_tpl_vars['key']; ?>
" onclick="_model_select_tr('_id<?php echo $this->_tpl_vars['key']; ?>
')" >
		      <td align="center" ><input type="checkbox" field_id='<?php echo $this->_tpl_vars['info']['id']; ?>
'   <?php if ($this->_tpl_vars['info']['if_used']): ?>checked<?php endif; ?> /></td>
		      <td align="left" ><?php echo $this->_tpl_vars['info']['name']; ?>
</td>
         </tr>
      <?php endforeach; endif; unset($_from); ?>
	</tbody>
	</table> 
	<input  type="hidden"  id="panel_model_id"  name="panel_model_id" value="<?php echo $this->_tpl_vars['model_id']; ?>
"  />
   <input  type="hidden" id="panel_model_type" name="panel_model_type" value="<?php echo $this->_tpl_vars['model_type']; ?>
"/>
</div>
</td>
<td>
<div style="margin-top:10px;margin-left:15px"><div><a onClick=" _model_up_tr()" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-up" icon title="上移" >上移</a></div>
<div style="margin-top:10px"><a onClick="_model_down_tr()" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-down" title="下移" >下移</a></div>
<div style="margin-top:10px"><a id="_save_model" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" title="保存" >保存</a></div>
</td>
<tr>
</tbody>
</table>
<script language="JavaScript" type="text/javascript">
var _model_selected_tr;

//选中tr
function _model_select_tr(tr_id){
	if(_model_selected_tr)
	{
		$("#"+_model_selected_tr).css("background-color","#ffffff");
	}
	_model_selected_tr = tr_id;
	$("#"+_model_selected_tr).css("background-color","#E4EDFE");
}

//上移
function _model_up_tr() {
	//把他的上一个往下排把他排上去如果是第一个就不让他往上排
	if ($("#" + _model_selected_tr).prev().html() != null)
	{
		var checkedTR = $("#" + _model_selected_tr).prev();
		checkedTR.insertAfter($("#" + _model_selected_tr));
	}
}
//下移
function _model_down_tr() {
	//把他的下一个往上排把他排下去如果是最后一个就不让他往下排
	if ($("#" + _model_selected_tr).next().html() != null)
	{
		var checkedTR = $("#" + _model_selected_tr).next();
		checkedTR.insertBefore($("#" + _model_selected_tr));
	}
}

//保存按钮
$('#_save_model').click(function()
{
	if(!$('#panel_model_name').validatebox('isValid'))
	return false;

	var _data = {};
	_data.field_id = [];

	var i=0;
	$("#_model_table input[type=checkbox]").each(function(){
		var if_select = $(this).attr('checked');
		if(if_select)
		{
			_data.field_id[i] = $(this).attr('field_id');
			i++;
		}
	});

	_data.model_id   = $("#panel_model_id").val();
	_data.model_name = $("#panel_model_name").val();
	_data.model_type = $("#panel_model_type").val();

	$('#_save_model').linkbutton({'disabled':true});
	$.ajax({
		type:'POST',
		url: "<?php echo $this->_tpl_vars['model_action']; ?>
",
		data:_data,
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				//刷新iframe
				refresh_iframe(0);
				$('#add_new_model').window('close');
			}
			else{
				$.messager.alert('错误','执行错误','error');
			}
			$('#_save_model').linkbutton({'disabled':false});
		}
	});
});
</script>