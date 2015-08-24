<?php /* Smarty version 2.6.19, created on 2015-08-15 17:17:40
         compiled from whitelist_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'whitelist_info.htm', 9, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="container-fluid">
    <div class="well well-small">
        <form class="form-horizontal" method="post" action="index.php?c=white&m=set_whitelist">
            <div class="control-group">
                <label class="control-label" for="trunk_num">所属中继号码</label>
                <div class="controls">
                    <select id="trunk_num" name="trunk_num">
                        <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['phones']), $this);?>

                    </select>
                    <span class="help-block">
                        中继号码不允许为空
                    </span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="phone_type1">添加方式</label>
                <div class="controls">
                    <label class="radio inline">
                        <input id="phone_type1" class="radio" name="phone_type" value="1" checked type="radio"> 单个号码
                    </label>
                    <label class="radio inline">
                        <input id="phone_type2" class="radio" name="phone_type" value="2" type="radio"> 批量添加
                    </label>
                </div>
            </div>
            <div id="phone-container">
                <div class="control-group">
                    <label class="control-label" for="phone">电话号码</label>
                    <div class="controls">
                        <input type="text" id="phone" name="phone[]" placeholder="电话号码">
                        <button type="button" class="btn btn-info" id="add_phone">
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-save"></span> 保存
                    </button>
                    <button type="button" class="btn" id="cancel">
                        <span class="glyphicon glyphicon-arrow-left"></span> 返回
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript">
    $(document).ready(function(){
        $('input[name=phone_type]').on('click', function() {
            var type = $(this).val();
            var $container = $('#phone-container');
            var old_html = $container.html();
            if (type == 1) {
                $container.html('<div class="control-group">\
                        <label class="control-label" for="phone">电话号码</label>\
                            <div class="controls">\
                                    <input type="text" id="phone" name="phone[]" placeholder="电话号码">\
                                    <button type="button" class="btn btn-info" id="add_phone">\
                                    <span class="glyphicon glyphicon-plus"></span>\
                            </button>\
                            </div>\
                        </div>'
                );
            } else {
                $container.html('<div class="control-group">\
                        <label class="control-label" for="phone">电话号码</label>\
                            <div class="controls">\
                                    <textarea id="phone" name="phone[]" rows="6" cols="80"></textarea>\
                            </button>\
                            </div>\
                        </div>');
            }
        });
        $('#add_phone').on('click', function(){
            $('<div class="control-group"> \
                <label class="control-label" for="phone">电话号码</label> \
                <div class="controls"> \
                        <input type="text" id="phone" name="phone[]" placeholder="电话号码"> \
                        <button type="button" class="btn btn-danger" id="remove_phone"> \
                        <span class="glyphicon glyphicon-minus"></span> \
                </button> \
                </div> \
            </div>').appendTo($('#phone-container')).find('#remove_phone').on('click', function(){
                $(this).parent().parent().remove();
            });
        });
        $('#cancel').on('click', function(){
            location.href = 'index.php?c=white';
        });
    });
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>