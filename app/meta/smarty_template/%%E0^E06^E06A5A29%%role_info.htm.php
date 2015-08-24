<?php /* Smarty version 2.6.19, created on 2015-07-14 23:33:45
         compiled from role_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'role_info.htm', 39, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="p" class="easyui-panel" style="width:auto;height:auto;padding:10px;background:#fafafa;">
<div>
<div id='maindiv2' class="main-div" style="float:left;width:30%;padding:0 0 0 0;">
<!--	角色列表	-->
<div id='list' style="overflow:auto;">
	<table id='tab'>
	<th style="text-align:center;border:1 solid #BBDDE5;background-color:#F4FAFB;font-size:16;">角色名称</th>
	<th style="text-align:center;border:1 solid #BBDDE5;background-color:#F4FAFB;font-size:16;">数据权限</th>
	<?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loop']['show'] = true;
$this->_sections['loop']['max'] = $this->_sections['loop']['loop'];
$this->_sections['loop']['step'] = 1;
$this->_sections['loop']['start'] = $this->_sections['loop']['step'] > 0 ? 0 : $this->_sections['loop']['loop']-1;
if ($this->_sections['loop']['show']) {
    $this->_sections['loop']['total'] = $this->_sections['loop']['loop'];
    if ($this->_sections['loop']['total'] == 0)
        $this->_sections['loop']['show'] = false;
} else
    $this->_sections['loop']['total'] = 0;
if ($this->_sections['loop']['show']):

            for ($this->_sections['loop']['index'] = $this->_sections['loop']['start'], $this->_sections['loop']['iteration'] = 1;
                 $this->_sections['loop']['iteration'] <= $this->_sections['loop']['total'];
                 $this->_sections['loop']['index'] += $this->_sections['loop']['step'], $this->_sections['loop']['iteration']++):
$this->_sections['loop']['rownum'] = $this->_sections['loop']['iteration'];
$this->_sections['loop']['index_prev'] = $this->_sections['loop']['index'] - $this->_sections['loop']['step'];
$this->_sections['loop']['index_next'] = $this->_sections['loop']['index'] + $this->_sections['loop']['step'];
$this->_sections['loop']['first']      = ($this->_sections['loop']['iteration'] == 1);
$this->_sections['loop']['last']       = ($this->_sections['loop']['iteration'] == $this->_sections['loop']['total']);
?>
	<tr id="tr_<?php echo $this->_tpl_vars['roles'][$this->_sections['loop']['index']]['role_id']; ?>
" role_id='<?php echo $this->_tpl_vars['roles'][$this->_sections['loop']['index']]['role_id']; ?>
' >
		<td  style="text-align:center;height:20;width:70%" valign="bottom"><?php echo $this->_tpl_vars['roles'][$this->_sections['loop']['index']]['role_name']; ?>
</td>
		<td  style="text-align:center;height:20" valign="bottom"><?php echo $this->_tpl_vars['roles'][$this->_sections['loop']['index']]['role_type_name']; ?>
</td>
	</tr>
	<?php endfor; endif; ?>
	</table>
</div>
<!--	添加角色开始  -->
	<div>
		<center>
			<button class="btn btn-primary" id="new_role" type="button"> 
				<span class="glyphicon glyphicon-plus"></span> 新建
			</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<button class="btn btn-primary" id="edit_role" type="button"> 
				<span class="glyphicon glyphicon-edit"></span> 编辑
			</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<button class="btn btn-primary" id="delete_role" type="button"> 
				<span class="glyphicon glyphicon-remove"></span> 删除
			</button>
		</center>
	</div>
	
	<div id="role_div" style="display:none">
		<table>
			<tr>
				<td align="center">
					<form class="form-inline">
					角色名称：<input type="text" id="role_name" class="input-small" />
					数据权限：<select name="role_type" id="role_type" class="input-small" ><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['role_types']), $this);?>
</select>
					<input type="hidden" id="do_type" value="insert" />
					<button class="btn btn-primary" id="save_role" type="button"> 
						<span class="glyphicon glyphicon-saved"></span> 保存
					</button>
					</form>
				</td>
			</tr>
		</table>
	</div>
<!--	添加角色结束  -->

<div id='_remark' style='color:red;'>
<pre>
  <b>注：</b>
	<b>部门</b>	限制于本部门和下属部门的数据
	<b>个人</b>	限制于自己的数据
	
	<b>特别注意：权限修改一律退出重登系统即可生效</b>
</pre>
</div>

</div>
<div id='maindiv' class="main-div" style="float:left;width:65%;padding-left:15px;margin-left:8px">
<form action="javascript:set_role_action()" method="POST" name="searchForm" id="searchForm">
<table>
<tr>
<td align="left">
<?php $_from = $this->_tpl_vars['authority']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['class_name'] => $this->_tpl_vars['auth_class']):
?>
<tr>
    <th style="border:1 solid #BBDDE5;background-color:#F0F8FF;font-size:13;text-align:left" id="th_<?php echo $this->_tpl_vars['class_name']; ?>
">
        <label class="radio inline" for="<?php echo $this->_tpl_vars['class_name']; ?>
">
            <input type="checkbox" id="<?php echo $this->_tpl_vars['class_name']; ?>
" onclick="toggle_auth_class('<?php echo $this->_tpl_vars['class_name']; ?>
')">&nbsp;&nbsp;<b><?php echo $this->_tpl_vars['auth_class']['name']; ?>
</b>
        </label>
    </th>
</tr>
<tr>
	<td style="padding:0 5 8 5;" id="td_<?php echo $this->_tpl_vars['class_name']; ?>
">&nbsp;&nbsp;
	    <?php $_from = $this->_tpl_vars['auth_class']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['auth_name'] => $this->_tpl_vars['auth']):
?>
	    <label class="radio inline" for="<?php echo $this->_tpl_vars['auth_name']; ?>
">
            <input type="checkbox" name="authority[]" id="<?php echo $this->_tpl_vars['auth_name']; ?>
" value="<?php echo $this->_tpl_vars['auth_name']; ?>
" onclick="toggle_auth('<?php echo $this->_tpl_vars['auth_name']; ?>
')"> <?php echo $this->_tpl_vars['auth'][0]; ?>

        </label>
	    <?php endforeach; endif; unset($_from); ?>
	</td>
</tr>
<?php endforeach; endif; unset($_from); ?>
<tr>
<td align="center" colspan="3">
<button class="btn btn-primary" id="all_auth" type="button"> 
	<span class="glyphicon glyphicon-check"></span> 全选 
</button>&nbsp;&nbsp;&nbsp;&nbsp;
<button class="btn btn-primary" id="save_action" onclick="$('#searchForm').submit();"> 
	<span class="glyphicon glyphicon-saved"></span> 保存 
</button>

</td>
</tr>
</table>
</form>
</div>
</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<style>
.on_mous{
	background-color:#eeeeee;
}
.out_mous{
	background-color:'';
}
.click_mous{
	background-color:#cccccc;
}
</style>
<script src="./jssrc/viewjs/role_info.js" type="text/javascript"></script>