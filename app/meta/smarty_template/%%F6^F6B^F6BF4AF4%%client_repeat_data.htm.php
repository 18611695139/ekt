<?php /* Smarty version 2.6.19, created on 2015-08-15 16:34:44
         compiled from client_repeat_data.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_repeat_data.htm', 13, false),)), $this); ?>
<div id="client_repeat_data_list"></div>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	//设置列表
	$('#client_repeat_data_list').datagrid({
		nowrap: true,
		striped: true,
		rownumbers:true,
		fitColumns:true,
		remoteSort:false,
		idField:'cle_id',
		url:"index.php?c=client&m=list_repeat_client_query",
		queryParams:{"name":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","phone":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['phone'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
", "cle_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
"},
		columns:[[
		{title:"客户名称" ,field:"cle_name",width:120,align:'CENTER'},
		{title:"客户电话" ,field:"cle_phone",width:100,align:'CENTER',formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return show_real;
			}
			else
			return '';
		}},
		<?php if ($this->_tpl_vars['client_base']['cle_phone2']): ?>
		{title:"办公电话" ,field:"cle_phone2",width:100,align:'CENTER',formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return show_real;
			}
			else
			return '';
		}},
		<?php endif; ?>
		<?php if ($this->_tpl_vars['client_base']['cle_phone3']): ?>
		{title:"其他电话" ,field:"cle_phone3",width:100,align:'CENTER',formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return show_real;
			}
			else
			return '';
		}},
		<?php endif; ?>
		{title:"客户阶段" ,field:"cle_stage",width:100,align:'CENTER'},
		<?php if ($this->_tpl_vars['power_use_contact'] != 1): ?>
		{title:"联系人名称" ,field:"con_name",width:100,align:'CENTER'},
		{title:"联系人电话" ,field:"con_mobile",width:100,align:'CENTER',formatter:function(value,rowData,rowIndex){
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return show_real;
			}
			else
			return '';
		}},
		<?php endif; ?>
		{title:"最近一次联系时间" ,field:"cle_last_connecttime",width:100,align:'CENTER',formatter:function(value,rowData,rowIndex){
			if( value != '0000-00-00' )
			{
				return value;
			}
		}},
		{title:"所属部门" ,field:"dept_name",width:80,align:'CENTER'},
		{title:"数据所属人" ,field:"user_name",width:80,align:'CENTER'}	
		]]
	});
});
</script>