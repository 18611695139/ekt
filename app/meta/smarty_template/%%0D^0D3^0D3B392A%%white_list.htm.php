<?php /* Smarty version 2.6.19, created on 2015-07-21 11:12:55
         compiled from white_list.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="order_list"></div>
<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $('#order_list').datagrid({
            title:'白名单列表',
            height:get_list_height_fit_window('_search_panel'),
            nowrap: true,
            striped: true,
            collapsible:false,
            rownumbers:true,
            sortName:'id',
            sortOrder:'desc',//降序排列
            idField:'id',
            url:'index.php?c=white&m=get_list',
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
                text:'新建',
                handler:function()
                {
                    location.href = "index.php?c=white&m=white_add";
                }
            },{
                text:'删除',
                iconCls:'icon-del',
                handler:function(){
                    var ids = getSelections();
                    if(ids == '')
                    {
                        $.messager.alert('提示','请选中要删除的数据！','error');
                        return;
                    }
                    $.messager.confirm('提示', '您确定要删除这些数据么？', function(r){
                        if(r){
                            $.ajax({
                                type:"POST",
                                url:'index.php?c=white&m=white_del',
                                data:{ids: ids},
                                dataType:'json',
                                success:function (responce){
                                    if(responce['error']=='0'){
                                        $('#order_list').datagrid('reload');
                                    }
                                    else
                                    {
                                        $.messager.alert('提示',responce['message'],'info');
                                    }
                                }
                            });
                        }
                    });
                }
            }]
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