<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:34
         compiled from data_import.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<table class="table table-hover table-bordered table-condensed">
  <tr><th>导入日志</th></tr>
  <tr>
      <td>
        <button class="btn btn-primary" onclick="window.parent.addTab('<?php if ($this->_tpl_vars['impt_type'] == 1): ?>客户导入日志<?php elseif ($this->_tpl_vars['impt_type'] == 2): ?>产品导入日志<?php endif; ?>','index.php?c=data_import&m=display_import_log&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
','menu_icon')">
            <span class="glyphicon glyphicon-list"></span> 查看导入日志
        </button>
        查看数据导入日志、回滚数据、导出数据
       </td>
  </tr>
</table>

<table class="table table-hover table-bordered table-condensed">
   <tr><th>1、选择要导入的数据项并导出选中的数据模板</th></tr>
   <tr>
      <td>
       <div>
            <button class="btn btn-primary" id="choose_model" name="choose_model">
                <span class="glyphicon glyphicon-search"></span> 选择模板
            </button>
            <button class="btn btn-primary" onclick="export_selected_model()">
                <span class="glyphicon glyphicon-export"></span> 导出模板
            </button>
            <span style="color:red;">提示：先选择模板，才能导出选中模板</span>
        </div>
        <iframe style="width:100%;height:100px;border:0;" id="choose_model_iframe" src="index.php?c=data_import&m=import_model&model_id=0&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
"  frameborder="application/msexcel"></iframe>
       </td>
   </tr>
 </table>
 
<form id="theForm" action="index.php?c=data_import&m=data_upload" method="POST" name="theForm" enctype="multipart/form-data">
<input type="hidden" id="selected_system_model" name="selected_system_model" value="" /><!--  模板ID  -->
<input type="hidden" id="impt_type" name="impt_type" value="<?php echo $this->_tpl_vars['impt_type']; ?>
" /><!--  模板ID  -->

<table class="table table-hover table-bordered table-condensed">
   <tr><th>2、选择导入数据文件</th></tr>
   <tr>
      <td>
      	<div>请选择本地文件(*.CSV,*.TXT)：
         	<input type="file" name="file_address" id="file_address" size="60" style="margin-left:20px;" />
         	<span class="require-field">*</span>
       	</div>
       </td>
   </tr>
</table>

<table class="table table-hover table-bordered table-condensed">
   <tr><th>3、输入必填项</th></tr>
   <tr>
   
   		<?php if ($this->_tpl_vars['impt_type'] == 1): ?>
   		<td>
   			<div class="form-inline" style="padding-left:20px;">
   			数据所属
            <select name="data_owner" id="data_owner">
                <?php if ($this->_tpl_vars['role_type'] == 1): ?><option value="1">部门数据</option><?php endif; ?>
                <option value="2">自己的数据</option>
            </select>
            </div>
       </td>
       <td>
       		<?php if ($this->_tpl_vars['client_base']['cle_info_source']): ?>
       		<div class="form-inline" style="padding-left:20px;">
       		信息来源
       		<select name="cle_info_source" id="cle_info_source">
               <?php $_from = $this->_tpl_vars['cle_info_source']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['info_source']):
?>
               <option value="<?php echo $this->_tpl_vars['info_source']['name']; ?>
" ><?php echo $this->_tpl_vars['info_source']['name']; ?>
</option>
               <?php endforeach; endif; unset($_from); ?>
            </select>
            </div>
        <?php else: ?>
        	<input type='hidden' id='cle_info_source' name='cle_info_source' value='' />
       	<?php endif; ?>
       </td>
       <?php elseif ($this->_tpl_vars['impt_type'] == 2): ?>       
        <td>
        	<div class="form-inline" style="padding-left:20px;">
        		产品分类 <input id='product_class_id' name='product_class_id' value='1' class="easyui-validatebox" missingMessage="此项不能为空" required="true"/>
        	</div>
        </td>
        <td>
        	<div class="form-inline" style="padding-left:20px;">
        		产品状态
        		<select id='product_state' name='product_state'>
                     <option value='1'>上架</option><option value='2'>下架</option>
                </select>
        	</div>
        </td>
       <?php endif; ?>
   </tr>
</table>

<table class="table table-hover table-bordered table-condensed">
   <tr><th>4、数据过滤</th></tr>
   <tr>
   	<td>
   		<div class="form-inline" style="padding-left:20px;">
   			<?php if ($this->_tpl_vars['impt_type'] == 1): ?>
   			<input type="checkbox"  value="1" name="filter_cle_name" id="filter_cle_name" checked />&nbsp; 过滤客户姓名相同的数据 &nbsp;&nbsp;&nbsp;
   			<input type="checkbox"  value="1" name="filter_cle_phone" id="filter_cle_phone" checked <?php if ($this->_tpl_vars['phone_ifrepeat']): ?>disabled<?php endif; ?>/>&nbsp; 过滤客户电话相同的数据 &nbsp;&nbsp;&nbsp;
   			<?php elseif ($this->_tpl_vars['impt_type'] == 2): ?>
   			<input type="checkbox"  value="1" name="filter_product_name" id="filter_product_name" checked />&nbsp; 过滤产品名称相同的数据 &nbsp;&nbsp;&nbsp;
   			<?php endif; ?>
   			<input type="checkbox"  value="1" name="shuffle" id="shuffle" checked/> 打乱顺序
   		</div>
   	</td>
   </tr>
</table>

<table class="table table-hover table-bordered table-condensed">
   <tr><th>5、确定导入</th></tr>
   <tr>
      <td>
      	 <button type="button" class="btn btn-primary" onclick="start_uoload_data()">
              <span class="glyphicon glyphicon-play"></span> 开始导入
         </button>
       </td>
   </tr>
</table>

</form>

<div id="display_system_model"></div> <!-- 模板 -->

<script language="javascirpt" type="text/javascript">
$(document).ready(function(){
	//选择模板
	$('#choose_model').click(function()
	{
		$('#display_system_model').window({
			href:"index.php?c=model&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
",
			title:"模板管理",
			top:10,
			width:400,
			height:500,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			cache:false
		});
	});

	//产品分类
	<?php if ($this->_tpl_vars['impt_type'] == 2): ?>
	$('#product_class_id').combotree({
		url:'index.php?c=product&m=get_product_class_tree',
		editable:true,
		lines:true,
		onClick:function(node){
			$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#product_class_id').combotree('options').url = "index.php?c=product&m=get_product_class_tree";

			}
		}
	});
	<?php endif; ?>
});

//刷新iframe
function refresh_choose_model(model_id)
{
	$('#choose_model_iframe').attr("src","index.php?c=data_import&m=import_model&model_id="+model_id+"&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
");
}

//导出模板
function export_selected_model()
{
	var model_id = $("#selected_system_model").val();
	if( !(model_id > 0) ){
		$.messager.alert('提示', '<br>请选择需要导出的数据模板！', 'info');

	}else{
		window.location.href = 'index.php?c=model&m=export_model&model_id='+model_id+'&impt_type=<?php echo $this->_tpl_vars['impt_type']; ?>
';
	}
}

//开始上传
function start_uoload_data()
{
	if(!$("#file_address").val())
	{
		$.messager.alert('提示', '<br>请选择导入数据文件！', 'info');
		return false;
	}

	var model_id = $("#selected_system_model").val();
	if( !(model_id > 0) )
	{
		$.messager.defaults.ok = '选择模板';
		$.messager.defaults.cancel = '取消';
		$.messager.confirm("提示","<br>缺少数据模板信息<br>请选择数据模板！",function(r){
			if(r)
			{
				$('#choose_model').click();
			}
			return false;
		});
	}
	else
	{
		//保存
		$("#theForm").submit();
	}
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>