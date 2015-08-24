<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:54
         compiled from black_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="order_list"></div>
<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $('#order_list').datagrid({
            title:'黑名单列表',
            height:get_list_height_fit_window('_search_panel'),
            nowrap: true,
            striped: true,
            collapsible:false,
            rownumbers:true,
            sortName:'id',
            sortOrder:'desc',//降序排列
            idField:'id',
            url:'index.php?c=black&m=get_list',
            frozenColumns:[[
                {field:'ck',checkbox:true},
                {title:'id',field:'id',width:160,sortable:true,align:'center'},
                {title:'中继号码',field:'trunk_num',width:160,sortable:true,align:'center'}

            ]],
            columns:[[
                {title:'号码',field:'phone_num',width:160,sortable:true,align:'center'}
            ]],
            toolbar:[{
                iconCls:'icon-add',
                text:'添加',
                handler:function()
                {
                    location.href = "index.php?c=black&m=black_add";
                }
            },{
                iconCls:'icon-del',
                text:'删除',
                handler:function()
                {
                    var ids = getSelections();
                    if (!ids) {
                        $.messager.alert('提示框', '请选择要删除的数据');
                    } else {
                        $.messager.confirm('确认框', '是否要删除选中的数据？', function(r){
                            if (r) {
                                $.ajax({
                                    url: 'index.php?c=black&m=black_delete',
                                    type: 'post',
                                    dataType: 'json',
                                    data: {ids: ids},
                                    success: function(response) {
                                        if (!response['error']) {
                                            $('#order_list').datagrid('reload');
                                        } else {
                                            $.messager.show(response['message']);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            }
            ]
        });
    });

    /**
     *列表得到选中的id
     */
    function getSelections()
    {
        var ids = [];
        var rows = $('#order_list').datagrid('getSelections');
        for(var i=0;i<rows.length;i++){
            ids.push(rows[i].id);
        }
        return ids.join(',');
    }
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>