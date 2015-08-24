<?php /* Smarty version 2.6.19, created on 2015-08-07 10:18:40
         compiled from knowledge_art_edit.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'knowledge_art_edit.htm', 5, false),)), $this); ?>
<div style="width:100%;">
	<div class="easyui-panel" title="<?php if ($this->_tpl_vars['article_info']['k_art_id']): ?>编辑文章<?php else: ?>添加文章<?php endif; ?>" >
		<div style="padding:10px;">
			<label style="font-weight:bold;">标题：</label>
			<input type="text" id='_art_title' name='_art_title' value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['article_info']['k_art_title'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
" style="width:250px;"/>
			<label style="padding-left:15px;font-weight:bold;">栏目：</label>
			<input id="_art_class" name='_art_class' class="easyui-combotree" url="index.php?c=knowledge&m=get_class_tree" value="<?php echo $this->_tpl_vars['article_info']['k_class_id']; ?>
" required="true">
			<label style="padding-left:15px;font-weight:bold;">热点：</label>
			<input type="radio" name="_art_hot" value="1" <?php if ($this->_tpl_vars['article_info']['k_art_hot'] == 1): ?>checked<?php endif; ?> />是
			<input type="radio" name="_art_hot" value="0" <?php if ($this->_tpl_vars['article_info']['k_art_hot'] != 1): ?>checked<?php endif; ?> />否
		</div>
		<div style="padding:10px;">
			<textarea id="_art_content" name="_art_content"  ><?php echo ((is_array($_tmp=@$this->_tpl_vars['article_info']['k_art_content'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
</textarea>
		</div>
		<div style="padding:6px 0px 10px 10px;">
			<button type="button" id="_btn_art" onclick="_save_art()">保存文章</button>
			<span id='_success_message' style='color:red;'></span>
		</div>
	</div>
</div>

<script language="JavaScript" type='text/javascript'>
$(document).ready(function(){
	CKEDITOR.replace( '_art_content' ,{
		filebrowserBrowseUrl: './jssrc/ckeditor/ckfinder/ckfinder.html', //浏览文件url
		filebrowserUploadUrl: './jssrc/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', //上传文件url
		filebrowserImageBrowseUrl: './jssrc/ckeditor/ckfinder/ckfinder.html?Type=Images',//浏览图片url
		//filebrowserImageUploadUrl: './jssrc/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images', //上传图片url
        filebrowserImageUploadUrl: 'index.php?c=knowledge&m=upload', //上传图片url
		filebrowserFlashBrowseUrl: './jssrc/ckeditor/ckfinder/ckfinder.html?Type=Flash', //浏览flash的url
		filebrowserFlashUploadUrl: './jssrc/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash', //上传flash的url
		extraPlugins: 'forms,colordialog,justify,font,panelbutton,colorbutton',
		height: ($(document).height()-350),
		startupFocus: true,
		blockless:true
	});
});

function _save_art()
{
	var data = {};
	<?php if ($this->_tpl_vars['article_info']): ?>
	data.art_id = <?php echo $this->_tpl_vars['article_info']['k_art_id']; ?>
;
	<?php else: ?>
	data.art_id = '';
	<?php endif; ?>
	data.art_title = $("#_art_title").val();
	data.art_content =  CKEDITOR.instances._art_content.getData();
	data.art_class = $('#_art_class').combotree('getValue');
	data.art_hot = $("input:radio[name='_art_hot']:checked").val();

	if(data.art_content =="")
	{
		$.messager.alert('提示','<br>文章内容不能为空！','info');
		return  false;
	}

	if(data.art_id == '')
	{
		var url = "index.php?c=knowledge&m=insert_article";
	}
	else
	{
		var url = "index.php?c=knowledge&m=update_article";
	}

	$('#_btn_art').attr('disabled',true);
	$.ajax({
		type:'POST',
		url: url,
		data:data,
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				$('#_success_message').html('操作成功，自动跳到文章详情页面');
				get_menu_content(data.art_class,data.art_title);
				$('#knowledge_panel').panel('open').panel('refresh','index.php?c=knowledge&m=show_article_detail&k_art_id='+responce['content']);
				$('#hot_div').panel('open').panel('refresh','index.php?c=knowledge&m=get_hot_art');
			}
			else
			{
				$.messager.alert('错误',responce['message'],'error');
			}
		}
	});
}
</script>