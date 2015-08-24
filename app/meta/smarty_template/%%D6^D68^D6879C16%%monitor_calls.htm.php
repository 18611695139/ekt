<?php /* Smarty version 2.6.19, created on 2015-08-07 08:55:21
         compiled from monitor_calls.htm */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="user_list"></div>

<script language="JavaScript" type="text/javascript">
    var timeFly       = "";//定时器
    $(document).ready(function() {
        $('#user_list').datagrid({
            height:get_list_height_fit_window('_search_panel'),
            nowrap: true,
            striped: true,
            rownumbers:true,
            fitColumns:true,
            loadMsg:"",
            sortName:'que_id',
            sortOrder:'desc',//降序排列
            idField:'que_id',
            url:'index.php?c=monitor&m=calls_list&que_id=<?php echo $this->_tpl_vars['que_id']; ?>
',
            frozenColumns:[[
                {field:'que_id',hidden:true},
                {title:'主叫号码',field:'queuer_num',width:120,align:'center'},
                {title:'技能组',field:'que_name',width:100,align:'center'},
                {title:'客户姓名',field:'cle_name',width:100,align:'center'},
                <?php if ($this->_tpl_vars['cle_stage'] == 1): ?>
                {title:'客户阶段',field:'cle_stage',width:100,align:'center'},
                <?php endif; ?>
                {title:'所属坐席',field:'user_name',width:100,align:'center'}
            ]],
            columns:[[
                {title:'进入队列时间',field:'in_time',width:100,align:'center'},
                {title:'排队状态',field:'queuer_sta',width:100,align:'center'},
                {title:'排队时长',field:'in_secs',width:100,align:'center'}
            ]],
            onLoadSuccess: function (responce){
                //update_call_monitor_data();
                //计时器->更新数据
                if( timeFly != "" )
                {
                    window.clearInterval(timeFly);//清空计时器，停止调用函数
                }
                timeFly = window.setInterval(function(){$('#user_list').datagrid('reload');},5000);
            }
        });
    });

    var all_call_data = "";//列表中的初始技能组数据
    var list_call_ids = '';
    function update_call_monitor_data()
    {
        all_call_data = $("#user_list").datagrid("getRows");
        list_call_ids  = [];
        $.each(all_call_data, function(property,value) {
            list_call_ids.push(value.que_id);
        });
        list_call_ids = list_call_ids.join(",");

        //计时器->更新数据
        if( timeFly != "" )
        {
            window.clearInterval(timeFly);//清空计时器，停止调用函数
        }
        timeFly = window.setInterval("get_call_updateRow()",5000);
    }

    /**
     *  把返回的数据更新至列表中
     */
    function get_call_updateRow()
    {
        //获取监控数据
        $.ajax({
            type:'POST',
            url: 'index.php?c=monitor&m=get_calls_monitor_data&que_id=<?php echo $this->_tpl_vars['que_id']; ?>
',
            data: {"list_call_ids":list_call_ids},
            dataType: "json",
            success: function(responce){
                if(responce["error"] == 0)
                {
                    //更新列表数据
                    $.each(all_call_data, function(property,value)
                    {
                        var tmp_que_d    = value.que_id;

                        if(responce['remove_data'][tmp_que_d])
                        {
                            $('#user_list').datagrid('deleteRow',property);
                        }
                        else
                        {
                            var update_value  = "";
                            if(typeof(responce['update_data'][tmp_que_d])=="object"&&!empty_obj(responce['update_data'][tmp_que_d]))
                            {
                                update_value = responce['update_data'][tmp_que_d];
                            }
                            else
                            {
                                update_value = value;
                            }

                            $('#user_list').datagrid('updateRow',{
                                index: property,
                                row: update_value
                            });
                        }
                    });

                    if(responce['add_data']!='')
                    {
                        for(i=0;i<responce['add_data'].length;i++)
                        {
                            $('#user_list').datagrid('appendRow',responce['add_data'][i]);
                        }
                    }
                }
            }
        });
    }

</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>