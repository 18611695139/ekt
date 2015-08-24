<?php /* Smarty version 2.6.19, created on 2015-08-06 16:07:36
         compiled from client_contact_record.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'client_contact_record.htm', 5, false),)), $this); ?>
<div id="contact_record" class="main-div"></div>
<div id="listen_record_panel"></div>
<div id="contact_record_content" class="easyui-window" title="联系内容-详情" style="width:350px;padding:10px;" data-options="closed:true,collapsible:false,minimizable:false,maximizable:false,cache:false,modal:false,closable:true,draggable:false"></div>
<script language="JavaScript" type="text/javascript">
var global_cle_id = <?php echo ((is_array($_tmp=@$this->_tpl_vars['cle_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var global_order_id =<?php echo ((is_array($_tmp=@$this->_tpl_vars['order_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
;
var power_download_record = <?php echo $this->_tpl_vars['power_download_record']; ?>
;//录音下载
</script>
<script src="./jssrc/viewjs/client_contact_record.js?1.2" type="text/javascript"></script>