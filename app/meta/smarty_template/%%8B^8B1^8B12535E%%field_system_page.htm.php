<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:12
         compiled from field_system_page.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div  id='action_field' style='padding-top:30px;width:100%;padding-left:20px;'>
	<div  title="客户基本字段" id="1"></div>
	<div  title="客户自定义字段" id="2"></div>
	<?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
	<div  title="联系人基本字段" id="3"></div>
	<div  title="联系人自定义字段" id="4"></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['power_product']): ?> 
	<div  title="产品基本字段" id="5"></div>
	<div  title="产品自定义字段" id="6"></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['power_order']): ?>
	<div  title="订单基本字段" id="7"></div>
	<div  title="订单自定义字段" id="8"></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['power_service']): ?>
	<div  title="客服基本字段" id="9"></div>
	<div  title="客服自定义字段" id="10"></div>
	<?php endif; ?>
</div>
<script type="text/javascript" language="javascript">
var tmp_select_dept = 0;
var _width = get_width();
if(_width>850)
{
	_width = 850;
}
var _height = get_height();
if(_height<600)
{
    _height = 600;
}
$(function(){
	$('#action_field').tabs({
		width:_width,
        height:_height,
		border:false,
		plain:true,
		tabPosition:'left',
        onSelect: function(title){
            $('#action_field').tabs('resize');
            var tab = $('#action_field').tabs('getSelected');
            var iframeUrl = refreshUrl(title);
            var id = tab.panel('options').id;
            $('#action_field').tabs('update', {
                tab: tab,
                options:{
                    content:"<iframe name='fieldFrame' id='iframepage"+id+"' frameborder='0' src='"+iframeUrl+"' scrolling='none' style='width:100%;height:100%'></iframe>"
                }
            });
        }
	});
});

function refreshUrl(title)
{
    if (title == '客户基本字段'){
        return "index.php?c=field_confirm&m=get_field_base&field_type=0";
    } else if (title == '客户自定义字段') {
        return "index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type=0";
    } else if (title == '联系人基本字段') {
        return "index.php?c=field_confirm&m=get_field_base&field_type=1";
    } else if (title == '联系人自定义字段') {
        return "index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type=1";
    } else if (title == '产品基本字段') {
        return "index.php?c=field_confirm&m=get_field_base&field_type=2";
    } else if (title == '产品自定义字段') {
        return "index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type=2";
    } else if (title == '订单基本字段') {
        return "index.php?c=field_confirm&m=get_field_base&field_type=3";
    } else if (title == '订单自定义字段') {
        return "index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type=3";
    } else if (title == '客服基本字段') {
        return "index.php?c=field_confirm&m=get_field_base&field_type=4";
    } else if (title == '客服自定义字段') {
        return "index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type=4";
    }
}
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>