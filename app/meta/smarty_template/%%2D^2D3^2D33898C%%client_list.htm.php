<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:32
         compiled from client_list.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_list.htm', 34, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['statistics_type'] == ''): ?>
<div id='_search_panel' class='form-div'></div>
<?php endif; ?>
<div id="client_list"></div>
<div id="datagrid_client"></div>
<div id="export_client"></div>
<div id="client_data_deal"></div>

<script src="./jssrc/datepicker/WdatePicker.js" type="text/javascript"></script>
<script src="./jssrc/viewjs/client_list.js?1.2" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	<?php if ($this->_tpl_vars['statistics_type'] == ''): ?>
	search_panel_info();
	<?php endif; ?>

	//设置列表
	$('#client_list').datagrid({
		title:"客户列表",
		height:get_list_height_fit_window('_search_panel'),
		nowrap: true,
		striped: true,
		pagination:true,
		autoRowHeight:false,
		rownumbers:true,
		checkOnSelect:false,
		pageSize:get_list_rows_cookie(),
		pageList:[50,30,10],
		sortName:'cle_id',
		sortOrder:'desc',//降序排列
		idField:'cle_id',
		url:"index.php?c=client&m=list_client_query",
		queryParams:{<?php if ($this->_tpl_vars['statistics_type'] == ''): ?>"visit_type":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['visit_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"<?php else: ?>"user_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","cle_stage_change_time_start":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_stage_change_time_start'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","cle_stage_change_time_end":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_stage_change_time_end'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","statistics_type":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['statistics_type'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","cle_stage":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_stage'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
","dept_id":"<?php echo ((is_array($_tmp=@$this->_tpl_vars['dept_id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"<?php endif; ?>},
		frozenColumns:[[
		{field:'ck',checkbox:true},
		{title:'操作',field:'opter_txt',width:50,align:"CENTER",formatter:function(value,rowData,rowIndex){
			return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"','link'); title='业务受理'>【受理】</a>";
		}}
		]],
		columns:[[
		<?php unset($this->_sections['key']);
$this->_sections['key']['name'] = 'key';
$this->_sections['key']['loop'] = is_array($_loop=$this->_tpl_vars['cle_display_field']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		{title: "<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['name']; ?>
" ,field: '<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields']; ?>
' ,align:"CENTER",width:"<?php echo $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['field_list_width']; ?>
",sortable:true,formatter:function(value,rowData,rowIndex){
			<?php if ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_name'): ?>
			return "<span><a href='###' onclick=link_client_accept('"+rowData.cle_id+"','link'); class='underline' title='业务受理'>"+value+"</a></span>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'con_mobile' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone2' || $this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_phone3'): ?>
			if(value)
			{
				var show_real = value;
				<?php if (! $this->_tpl_vars['power_phone_view']): ?>
				show_real = hidden_part_number(value);
				<?php endif; ?>
				return "<a href='###' onclick=link_client_accept('"+rowData.cle_id+"','dial','"+value+"'); class='underline' title='业务受理'>"+show_real+"</a>&nbsp;&nbsp;<a href='javascript:;' onclick=link_client_accept('"+rowData.cle_id+"','dial','"+value+"'); title='呼叫'><img src='./image/phone.png' /></a><?php if ($this->_tpl_vars['power_sendsms']): ?>&nbsp;&nbsp;<a href='javascript:;' onclick='sys_send_sms(\""+value+"\")' title='发短信'><img src='./image/message.png'  /></a><?php endif; ?>";
			}
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_address'): ?>
			return "<span class='show-tooltip'>"+value+"</span>";
			<?php elseif ($this->_tpl_vars['cle_display_field'][$this->_sections['key']['index']]['fields'] == 'cle_remark'): ?>
			return "<span class='show-tooltip'>"+value+"</span>";
			<?php else: ?>
			return value;
			<?php endif; ?>
		}},
		<?php endfor; endif; ?>
		{title:"cle_id" ,field:"cle_id",hidden:true}
		]],
		onLoadSuccess: function(){
			$('#client_list').datagrid('clearSelections');
			$('#client_list').datagrid('clearChecked');
			$('.show-tooltip').tooltip({
				trackMouse:true,
				position:'right',
				onShow: function(){
					var content = '<div style="width:180px;">'+$(this).text().replace(/\n/g, "<br>")+'</div>';
					$(this).tooltip('update',content);
				}
			});
		},
		toolbar:[
		<?php if ($this->_tpl_vars['power_client_add']): ?>
		{
			iconCls:'icon-add',
			text:'添加客户',
			handler:function(){
				window.parent.addTab('添加客户',"index.php?c=client&m=new_client","menu_icon");
			}
		},'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_delete']): ?>
		{
			iconCls:'icon-del',
			text:'删除客户',
			handler:function(){
				remove_client();
			}
		} ,'-',
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_release']): ?>
		{
			iconCls:'icon-release',
			text:"<span title = '只能放弃属于自己的数据' >选中放弃</span>",
			handler:function(){
				if_can_release();
			}
		},"-",
		<?php endif; ?>
		<?php if ($this->_tpl_vars['power_client_export']): ?>
		{
			iconCls:'icon-up',
			text:'导出CSV',
			handler:function(){
				output_client('csv');
			}
		},'-',
        {
           iconCls:'icon-up',
           text:'导出excel',
           handler:function(){
               output_client('excel');
           }
        },'-',
		<?php endif; ?>
		{
			iconCls:'icon-seting',
			text:'列表设置',
			handler:function(){
				datagrid_client_window();
			}
		},'-'
		]
	});
	var pager = $('#client_list').datagrid('getPager');
	$(pager).pagination({onChangePageSize:function(rows){
		set_list_rows_cookie(rows);
	}});
});
var delete_client_relative = <?php echo ((is_array($_tmp=@$this->_tpl_vars['delete_client_relative'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
;
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>