<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:35
         compiled from dictionary_content.htm */ ?>
<?php if ($this->_tpl_vars['p_id'] == 2): ?>
<div style="padding:5px 0px 5px 20px;color:red;">
注：客户阶段的修改会改变与之相关的统计。
</div>
<?php else: ?>
<div style="padding:10px;"></div>
<?php endif; ?>
<table id="_dictionary_table_<?php echo $this->_tpl_vars['p_id']; ?>
" cellspacing="0" cellpadding="0" align="center" style="width:98%;border-collapse: collapse; border: 0px solid gray;">
 <tbody>
  <!--客户阶段-->
  <?php if ($this->_tpl_vars['p_id'] == 2): ?>
  <tr>
  	<td>&nbsp;</td>
  	<td><input name='stage_title' value='客户阶段内容' style='border:0;font-weight:900;text-align:center;' readonly />&nbsp;&nbsp;&nbsp;&nbsp;<span style="width:60px;font-weight:bold;">阶段等级</span></td>
  </tr>
  <?php endif; ?>
  <?php if ($this->_tpl_vars['dinfo']): ?>
  	<?php $_from = $this->_tpl_vars['dinfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info']):
?>
  	<tr style="padding: 2px;">
  		<th style="text-align:right;width:15%;padding-right:10px;"><?php echo $this->_tpl_vars['dkey']+1; ?>
</th>
  			<td align="left">
  				<input type="text" size="40" name='_dictionary_option' value="<?php echo $this->_tpl_vars['info']['name']; ?>
" <?php if ($this->_tpl_vars['p_id'] == 6): ?><?php if ($this->_tpl_vars['info']['name'] == '未拨打' || $this->_tpl_vars['info']['name'] == '呼通' || $this->_tpl_vars['info']['name'] == '未呼通' || $this->_tpl_vars['info']['name'] == '空号错号'): ?>disabled<?php endif; ?><?php endif; ?> />
  				<!--客户阶段 等级-->
  				<?php if ($this->_tpl_vars['p_id'] == 2): ?>
  				<select name='client_name' class="span2">
  					<?php $_from = $this->_tpl_vars['client_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cykey'] => $this->_tpl_vars['cle_type']):
?>
   					<option value="<?php echo $this->_tpl_vars['cykey']; ?>
" <?php if ($this->_tpl_vars['all_cle_types'][$this->_tpl_vars['info']['name']] == $this->_tpl_vars['cykey']): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['cle_type']; ?>
</option>
  					<?php endforeach; endif; unset($_from); ?>
  				</select>
  				<?php endif; ?>
  			</td>
  	</tr>
  	<?php endforeach; endif; unset($_from); ?>
  <?php else: ?>
  	<?php $this->assign('loop', '5'); ?>
	<?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['loop']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr style="padding: 2px;">
		<th style="text-align:right;width:15%;padding-right:10px;"><?php echo $this->_sections['loop']['index']+1; ?>
</th>
		<td align="left">
			<input type="text" name='_dictionary_option' value="" />
			<!--客户阶段 等级-->
    		<?php if ($this->_tpl_vars['p_id'] == 2): ?>
    		<select name='client_name' class="span2" >
    			<?php $_from = $this->_tpl_vars['client_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cykey'] => $this->_tpl_vars['cle_type']):
?>
    			<option value="<?php echo $this->_tpl_vars['cykey']; ?>
" <?php if ($this->_tpl_vars['all_cle_types'][$this->_tpl_vars['info']['name']] == $this->_tpl_vars['cykey']): ?>selected<?php endif; ?> ><?php echo $this->_tpl_vars['cle_type']; ?>
</option>
    			<?php endforeach; endif; unset($_from); ?>
    		</select>
    		<?php endif; ?>
		</td>
	</tr>
	<?php endfor; endif; ?>
  <?php endif; ?>
 </tbody>
</table>
<div align="center" style="padding-top:20px;padding-bottom:10px;">
<button class="btn btn-primary" type="button" onclick="_dictionary_add_option(<?php echo $this->_tpl_vars['p_id']; ?>
)">
    <span class="glyphicon glyphicon-plus"></span> 添加
</button>
<button class="btn btn-primary" type="button" onclick="_dictionary_save(<?php echo $this->_tpl_vars['p_id']; ?>
,'<?php if ($this->_tpl_vars['if_refesh']): ?><?php echo $this->_tpl_vars['if_refesh']; ?>
<?php else: ?>0<?php endif; ?>')">
    <span class="glyphicon glyphicon-saved"></span> 保存
</button>
<button class="btn btn-primary" type="button" onclick="_dictionary_tab_reload()">
    <span class="glyphicon glyphicon-share-alt"></span> 重置
</button>
<span id='_dictionary_save_tip_<?php echo $this->_tpl_vars['p_id']; ?>
' style='color:red;'></span>
</div>