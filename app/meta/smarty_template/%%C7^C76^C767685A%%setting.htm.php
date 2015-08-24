<?php /* Smarty version 2.6.19, created on 2015-07-21 10:31:21
         compiled from setting.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class='main-div' id='setting_body'>
<fieldset style='border:0px;width:100%;'>
	<div id='setting_left' style='width:38%;float:left;padding-top:30px;'>
	<!--管理员设置-->
	<?php if ($this->_tpl_vars['this_setting'] == 1): ?>
	<span style='font-weight:bold;'>系统基础设置</span>
		<ul style='line-height:28px;'>
			<li id='setting1'>设置部门信息</li>
			<li id='setting2'>角色配置与权限分配</li>
			<li id='setting3'>设置客户(联系人)自定义字段、数字字典</li>
			<?php if ($this->_tpl_vars['power_service']): ?>
			<li id='setting4'>设置客服自定义字段、数字字典</li>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['power_product']): ?>
			<li id='setting5'>设置产品自定义字段</li>
			<li id='setting6'>设置产品分类</li>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['power_order']): ?>
			<li id='setting7'>设置订单自定义字段、数字字典</li>
			<?php endif; ?>
			<li id='setting8'>设置系统参数</li>
		</ul>
	<?php endif; ?>
	
	<!--管理员帮助-->
	<?php if ($this->_tpl_vars['this_setting'] == 201): ?>
	<span style='font-weight:bold;'>系统基础流程</span>
		<ul style='line-height:28px;'>
			<li id='setting201'>添加员工</li>
			<li id='setting202'>导入客户数据</li>
			<li id='setting203'>分配客户数据</li>
			<li id='setting204'>统计功能</li>
			<li id='setting205'>系统监控功能</li>
		</ul>
	<?php endif; ?>
	
	<!--坐席帮助-->
	<?php if ($this->_tpl_vars['this_setting'] == 101): ?>
	<span style='font-weight:bold;'>环境配置</span>
		<ul style='line-height:28px;'>
			<li id='setting101'>配置浏览器(IE)</li>
			<li id='setting102'>设置声卡（音量设置、测试录音）</li>
		</ul>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['this_setting'] == 101 || $this->_tpl_vars['this_setting'] == 103): ?>
	<span style='font-weight:bold;'>初步了解系统</span>
		<ul style='line-height:28px;'>
			<li id='setting103'>通信按钮</li>
			<li id='setting104'>个人设置</li>
		</ul>
	<span style='font-weight:bold;'>日常工作流程</span>
		<ul style='line-height:28px;'>
			<li id='setting105'>客户管理</li>
			<li id='setting106'>业务受理</li>
		</ul>
		<?php endif; ?>
	</div>
<div id='setting_right' style='float:left;width:59%;padding:10px;'>
	<div id='setting_content'></div>
</div>
</fieldset> 

<div style='padding:10px;'>
<table><tbody><tr><td align='right' width='45%'>
<span id='set_back'><a class="easyui-linkbutton"  href="javascript:void(0)"  id="setting_back_btn"  onclick="back_part()">< 上一步</a></span>&nbsp;&nbsp;
</td><td>
<span id='set_next'><a class="easyui-linkbutton"  href="javascript:void(0)"  id="setting_next_btn"  onclick="next_part()" >下一步 ></a></span>
</td><td>
<span id='last_message'></span>
<span id='admin_message' style='display:none;'>
<span style='color:red;'>已完成</span> 需进一步了解系统请使用
<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('帮助向导','index.php?c=setting&m=admin_setting','menu_icon_setting');" >帮助向导</a> 
</span>
</td></tr></tbody></table>
</div>

</div>
<div id='set_field_confirm'></div><!--  自定义字段设置   -->
<div id='set_dictionary'></div><!--  数据字典   -->
<div id='user_panel'></div><!--  添加员工   -->

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="JavaScript" type="text/javascript">
var this_setting = '';/*当前在哪一步*/
$(document).ready(function(){

	$('#setting_back_btn').linkbutton({'disabled':true});
	$('#setting'+<?php echo $this->_tpl_vars['this_setting']; ?>
).css({fontWeight:"bold",color:"#C36615"});

	<?php if ($this->_tpl_vars['this_setting'] == 1): ?>
	$('#setting_content').panel({
		href:'index.php?c=setting&m=get_setting_department'
	});
	<?php else: ?>
	$('#setting_content').panel({
		href:'index.php?c=setting&m=get_setting_content&this_setting=<?php echo $this->_tpl_vars['this_setting']; ?>
'
	});
	<?php endif; ?>

	this_setting=<?php echo $this->_tpl_vars['this_setting']; ?>
;
});

/*上一步*/
function back_part()
{
	$('#setting'+this_setting).css({fontWeight:"normal",color:"#000000"});
	this_setting = parseInt(this_setting)-1;
	if(this_setting==7)
	{
		<?php if (! $this->_tpl_vars['power_order']): ?>
		this_setting = 6;
		<?php endif; ?>
	}
	if(this_setting==6)
	{
		<?php if (! $this->_tpl_vars['power_product']): ?>
		this_setting = 4;
		<?php endif; ?>
	}
	if(this_setting==4)
	{
		<?php if (! $this->_tpl_vars['power_service']): ?>
		this_setting = 3;
		<?php endif; ?>
	}
	get_setting_content(this_setting);
}

/*下一步*/
function next_part()
{
	$('#setting'+this_setting).css({fontWeight:"normal",color:"#000000"});
	this_setting = parseInt(this_setting)+1;
	if(this_setting==4)
	{
		<?php if (! $this->_tpl_vars['power_service']): ?>
		this_setting = 5;
		<?php endif; ?>
	}
	if(this_setting==5)
	{
		<?php if (! $this->_tpl_vars['power_product']): ?>
		this_setting = 7;
		<?php endif; ?>
	}
	if(this_setting==7)
	{
		<?php if (! $this->_tpl_vars['power_order']): ?>
		this_setting = 8;
		<?php endif; ?>
	}
	get_setting_content(this_setting);
}


function get_setting_content(setting)
{
	$('#setting'+setting).css({fontWeight:"bold",color:"#C36615"});
	$('#setting_back_btn').linkbutton({'disabled':false});
	$('#setting_next_btn').linkbutton({'disabled':false});
	$('#last_message').html("");
	$('#admin_message').css({'display':'none'});
	$('#set_next').css({'display':'block'});
	if(setting == 1)
	{
		$('#setting_back_btn').linkbutton({'disabled':true});
		$('#setting_content').panel('open').panel('refresh','index.php?c=setting&m=get_setting_department');
	}
	else if(setting == 6)
	{
		$('#setting_content').panel('open').panel('refresh','index.php?c=setting&m=get_setting_product_class');
	}
	else
	{
		$('#setting_content').panel('open').panel('refresh','index.php?c=setting&m=get_setting_content&this_setting='+setting);
		/*下一步*/
		if(setting == 205 ||setting == 106)
		{
			$('#set_next').css({'display':'none'});
			$('#last_message').html("<span style='color:red;'>向导完成</span>");
		}
		if(setting == 8)
		{
			$('#set_next').css({'display':'none'});
			$('#admin_message').css({'display':'block'});
		}
		/*上一步*/
		if(setting==201)
		$('#setting_back_btn').linkbutton({'disabled':true});
		<?php if ($this->_tpl_vars['user_phone_type'] == 'softphone'): ?>
		if(setting == 101)
		$('#setting_back_btn').linkbutton({'disabled':true});
		<?php endif; ?>
		<?php if ($this->_tpl_vars['user_phone_type'] == 'telephone'): ?>
		if(setting == 103)
		$('#setting_back_btn').linkbutton({'disabled':true});
		<?php endif; ?>
	}
}

//自定义字段设置
function edit_field_confirm(field_type,title)
{
	$('#set_field_confirm').window({
		title:title+'自定义字段设置',
		href:'index.php?c=field_confirm&m=field_setting&if_refesh=1&field_type='+field_type,
		iconCls: "icon-seting",
		top:150,
		width:700,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		modal:true
	});
}

//数字字典
function edit_dictionary(str)
{
	$('#set_dictionary').window({
		title:'数字字典',
		href:'index.php?c=dictionary&if_refesh=1&type='+str,
		iconCls: "icon-seting",
		top:150,
		width:500,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false,
		modal:true
	});
}

function add_user()
{
	$('#user_panel').window({
		title: '添加员工',
		href:"index.php?c=user&m=add_user",
		iconCls: "icon-add",
		top:100,
		width:550,
		closed: false,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		cache:false
	});
}

</script>