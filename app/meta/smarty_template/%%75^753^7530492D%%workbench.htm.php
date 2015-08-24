<?php /* Smarty version 2.6.19, created on 2015-07-14 14:30:06
         compiled from workbench.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'workbench.htm', 220, false),array('modifier', 'default', 'workbench.htm', 297, false),)), $this); ?>
<!--   workbench.htm   $YHX 20125-05-08 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pageheader.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <style>
        .table-no-margin{
            margin-bottom: 0;
        }
    </style>
<script src="jssrc/jquery.portal.js" type="text/javascript" ></script>
<link href='themes/default/portal.css' rel='stylesheet' type='text/css' />
<div border="false" id="workbench_protal">

    <div style='width:50%;float:left;' id="box_left_div"><!-- box left  begin-->
        <!-- 统计 start  -->
        <div id="div_callrecord" data-options="iconCls:'icon-workbench',tools:'#div_call'" title="<?php echo $this->_tpl_vars['stat_title']; ?>
" style="float:left;width:96%;border:1px solid #BFBFBF;">
            <div id="div_call">
                <a href="#" class="icon-more" style="width:51px;" title="坐席工作量统计" onclick="javascript:window.parent.addTab('坐席工作量统计','index.php?c=statistics&m=statistics_tree','menu_icon_tjfxzhsjtjfx');"></a>
            </div>
            <?php if ($this->_tpl_vars['role_type'] != 2): ?>
            <!-- 部门的统计-->
            <div>
                <table class="table table-bordered table-striped table-condensed table-no-margin">
                    <tr>
                        <th>坐席</th>
                        <th>通话数</th>
                        <th>呼通数</th>
                        <!--<th>转化量</th>
                        <th>新增客户</th>
                        <th>回访客户</th>-->
                    </tr>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['statistics']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td align="center"><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['user_name']; ?>
[<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['user_num']; ?>
]</td>
                        <td align="center"><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['conn_num']; ?>
</td>
                        <td align="center"><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['conn_success']; ?>
</td>
                        <!--转化量-->
<!--                        <td align="center"><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['transformation']; ?>
</td>-->
                        <!-- 新增客户 -->
<!--                        <td align="center"><?php if ($this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['new_increment'] > 0): ?> <a href='#' onclick="get_manage_list('<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['user_id']; ?>
','<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['deal_date']; ?>
~<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['deal_date']; ?>
',2,'')" style='text-decoration: underline;'><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['new_increment']; ?>
</a> <?php else: ?> <?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['new_increment']; ?>
 <?php endif; ?></td>-->
                        <!-- 回访客户 -->
<!--                        <td align="center"><?php if ($this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['cle_visit'] > 0): ?> <a href='#' onclick="get_manage_list('<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['user_id']; ?>
','<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['deal_date']; ?>
~<?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['deal_date']; ?>
',3,'')" style='text-decoration: underline;'><?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['cle_visit']; ?>
</a> <?php else: ?> <?php echo $this->_tpl_vars['statistics'][$this->_sections['loop']['index']]['cle_visit']; ?>
 <?php endif; ?> </td>-->
                    </tr>
                    <?php endfor; endif; ?>
                </table>
            </div>
            <?php else: ?>
            <!--坐席自己的统计-->
            <div style="background:#FFFFFF;padding-left:10px;overflow: auto;" id="clum_show">
                <div style="height:260px;" id="chart_chum"> </div>
            </div>
            <?php endif; ?>
        </div><!-- 统计 end  -->

        <!--    公告   start     -->
        <div id="div_announcement" data-options="iconCls:'icon-workbench',tools:'#div_annou'" title="公告" style="float:left;width:96%;border:1px solid #BFBFBF;">
            <div id="div_annou">
                <?php if ($this->_tpl_vars['power_announcement_change']): ?>
                <a href="#" class="icon-new" style="width:51px;" title="添加新公告"
                   onclick="window.parent.addTab('添加公告','index.php?c=announcement&m=add_announcement','menu_icon');"></a>
                <?php endif; ?>
                <?php if ($this->_tpl_vars['power_announcement_more']): ?>
                <a href="#" class="icon-more" style="width:51px;" title="更多公告"
                   onclick="window.parent.addTab('公告管理','index.php?c=announcement','menu_icon_announcement');"></a>
                <?php endif; ?>
            </div>
            <div>
                <table class="table table-condensed table-hover table-no-margin">
                <?php if ($this->_tpl_vars['anns_info']): ?>
                <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['anns_info']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td>
                            <a href='javascript:void(0);' onclick="anns_view('<?php echo $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['anns_id']; ?>
')">
                                <?php echo $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['anns_title']; ?>

                            </a>
                        </td>
                        <td>
                            <?php echo $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['creat_time']; ?>

                        </td>
                        <td>
                            <?php if ($this->_tpl_vars['user_session_id'] == $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['create_user_id']): ?>
                            <?php if ($this->_tpl_vars['power_announcement_change']): ?>
                            <a href='javascript:void(0);' onclick="anns_remove('<?php echo $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['anns_id']; ?>
')" title='删除公告'>
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>
                            <a href='javascript:;' onclick="_update('<?php echo $this->_tpl_vars['anns_info'][$this->_sections['loop']['index']]['anns_id']; ?>
')" title='编辑公告'>
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endfor; endif; ?>
                <?php else: ?>
                 <tr>
                      <td><div class="alert alert-warning" style="margin-bottom: 0">没有公告</div></td>
                 </tr>
                <?php endif; ?>
                </table>
            </div>
        </div><!--  -----公告 -end--------->

    </div><!--box left end  -->

    <!----------------------------------------------------------------------------------------->

    <div style='width:50%;float:right;' id="box_right_div"><!--     box  right    begin -->
        <!--  今天要回访客户 start  -->
        <div id="div_visitclient"  title="今天要回访客户" data-options="iconCls:'icon-workbench',tools:'#div_visit'" style="float:left;width:96%;border:1px solid #BFBFBF;">
            <div id="div_visit">
                <a href="#" class="icon-more" style="width:51px;" title="更多回访客户" onclick="javascript:window.parent.addTab('今天要回访客户','index.php?c=client&visit_type=2','menu_icon');"></a>
            </div>
            <div>
                <table class="table table-condensed table-hover table-no-margin">
                    <tr>
                        <th>客户名称</th>
                       <!-- <th>客户阶段</th>-->
                        <th>最近一次联系时间</th>
                    </tr>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['revisit_client_info']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <!--客户名称-->
                        <td>
                            <?php echo $this->_tpl_vars['revisit_client_info'][$this->_sections['loop']['index']]['cle_name']; ?>

                            <a href='#' onclick="window.parent.addTab('业务受理','index.php?c=client&m=accept&type=client&cle_id=<?php echo $this->_tpl_vars['revisit_client_info'][$this->_sections['loop']['index']]['cle_id']; ?>
','menu_icon');" title='业务受理'>
                                <img src='./image/file.png'>
                            </a>
                        </td>
                        <!-- 客户阶段 -->
                       <!-- <td><?php echo $this->_tpl_vars['revisit_client_info'][$this->_sections['loop']['index']]['cle_stage']; ?>
</td>-->
                        <!-- 最近一次联系时间 -->
                        <td><?php echo $this->_tpl_vars['revisit_client_info'][$this->_sections['loop']['index']]['cle_last_connecttime']; ?>
</td>
                    </tr>
                    <?php endfor; endif; ?>
                </table>
            </div>
        </div><!-- 今天要回访客户 end  -->

        <!--     我的提醒    start    -->
        <div id="div_myremind" iconCls="icon-workbench" title="我的提醒" style="float:left;width:96%;border:1px solid #BFBFBF;"  class="main-div">
            <div>
                <table class="table table-condensed table-hover table-no-margin">
                    <tr>
                        <th>今日提醒</th>
                    </tr>
                    <?php if ($this->_tpl_vars['action'][2]): ?>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['action'][2]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td>
                            <a href='javascript:void(0);' onclick="parent._show_remind('<?php echo $this->_tpl_vars['action'][2][$this->_sections['loop']['index']]['rmd_id']; ?>
');">〖<?php echo $this->_tpl_vars['action'][2][$this->_sections['loop']['index']]['rmd_param_char']; ?>
〗<?php echo $this->_tpl_vars['action'][2][$this->_sections['loop']['index']]['rmd_remark']; ?>
</a>
                            <span><?php echo $this->_tpl_vars['action'][2][$this->_sections['loop']['index']]['rmd_time']; ?>
</span>
                            <?php if ($this->_sections['loop']['rownum'] == 5): ?>
                            <a href="#" onclick="window.parent.addTab('我的提醒','index.php?c=remind&m=list_remind','menu_icon_wdtu')"> 更多>> </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endfor; endif; ?>
                    <?php else: ?>
                    <tr>
                        <td><div class="alert alert-warning" style="margin-bottom: 0">今日没有未处理的提醒</div></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <table class="table table-condensed table-hover table-no-margin">
                    <tr>
                        <th>7日内的提醒</th>
                    </tr>
                    <?php if ($this->_tpl_vars['action'][3]): ?>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['action'][3]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td>
                            <a href='javascript:void(0);' onclick="parent._show_remind('<?php echo $this->_tpl_vars['action'][3][$this->_sections['loop']['index']]['rmd_id']; ?>
');">
                                〖<?php echo $this->_tpl_vars['action'][3][$this->_sections['loop']['index']]['rmd_param_char']; ?>
〗<?php echo $this->_tpl_vars['action'][3][$this->_sections['loop']['index']]['rmd_remark']; ?>

                            </a>
                            <span><?php echo $this->_tpl_vars['action'][3][$this->_sections['loop']['index']]['rmd_time']; ?>
</span>
                            <?php if ($this->_sections['loop']['rownum'] == 5): ?>
                            <a href="#" onclick="window.parent.addTab('我的提醒','index.php?c=remind&m=list_remind','menu_icon_wdtu')"> 更多>> </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endfor; endif; ?>
                    <?php else: ?>
                    <tr>
                        <td><div class="alert alert-warning" style="margin-bottom: 0">没有7日内未处理的提醒</div></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <table class="table table-condensed table-hover table-no-margin">
                    <tr>
                        <th>超期提醒</th>
                    </tr>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['action'][1]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td>
                            <a href='javascript:void(0);' onclick="parent._show_remind('<?php echo $this->_tpl_vars['action'][1][$this->_sections['loop']['index']]['rmd_id']; ?>
');">〖<?php echo $this->_tpl_vars['action'][1][$this->_sections['loop']['index']]['rmd_param_char']; ?>
〗<?php echo $this->_tpl_vars['action'][1][$this->_sections['loop']['index']]['rmd_remark']; ?>
</a>
                            <span style="color:#808080;padding-left:3px;text-align:right;"> <?php echo $this->_tpl_vars['action'][1][$this->_sections['loop']['index']]['rmd_time']; ?>
</span>
                            <?php if ($this->_sections['loop']['rownum'] == 3): ?>
                            <a href="#" class="underline" style='font-weight:bold;' onclick="window.parent.addTab('我的提醒','index.php?c=remind&m=list_remind','menu_icon_wdtu')"> 更多>> </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endfor; endif; ?>
                </table>
            </div>
        </div><!-- 我的提醒 end-->

        <!--  未读消息 start  -->
        <div id="div_readmeg" data-options="iconCls:'icon-workbench',tools:'#div_message'" title="未读消息" style="float:left;width:96%;border:1px solid #BFBFBF;">
            <div id="div_message">
                <a href="#" class="icon-more" style="width:51px;" title="更多消息" onclick="window.parent.addTab('我的消息','index.php?c=message','menu_icon_wdxx');"></a>
            </div>
            <div>
                <table class="table table-condensed table-no-margin">
                    <?php if ($this->_tpl_vars['message']): ?>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['message']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <td>
                            @<?php echo $this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_send_user_name']; ?>
：
                            <a href='#' onclick="message_view('<?php echo $this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_id']; ?>
','<?php echo $this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_send_user_id']; ?>
','<?php echo $this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_send_user_name']; ?>
')" title='查看'>
                                <?php echo ((is_array($_tmp=$this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_content'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 20) : smarty_modifier_truncate($_tmp, 20)); ?>

                            </a>
                            <?php echo $this->_tpl_vars['message'][$this->_sections['loop']['index']]['msg_insert_time']; ?>

                        </td>
                    </tr>
                    <?php endfor; endif; ?>
                    <?php else: ?>
                    <tr>
                        <td><div class="alert alert-warning" style="margin-bottom: 0">没有未读消息</div></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div><!-- 未读消息 end  -->

        <!--  我的未接来电 start  -->
        <div id="div_mymisscalls" data-options="iconCls:'icon-workbench',tools:'#div_misscall'" title="我的未接来电" style="float:left;width:96%;border:1px solid #BFBFBF;">
            <div id="div_misscall">
                <a href="#" class="icon-more" style="width:51px;" title="更多未接来电" onclick="window.parent.addTab('未接来电','index.php?c=missed_calls','menu_icon_wjldgl');"></a>
            </div>
            <div>
                <table class="table table-bordered table-striped table-condensed table-no-margin">
                    <tr>
                        <th>未接来电号</th>
                        <th>来电时间</th>
                        <th>原因</th>
                    </tr>
                    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['my_misscall_info']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                    <tr>
                        <!--未接来电号-->
                        <td>
                            <span id='miss_caller_number<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['id']; ?>
'>
                                <?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['caller']; ?>

                            </span>
                            <a href='#' onclick="accept('<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['id']; ?>
','<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['caller']; ?>
','<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['cle_id']; ?>
')"  title='处理未接来电'>〖处理〗</a>
                        </td>
                        <!-- 来电时间 -->
                        <td><?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['date']; ?>
</td>
                        <!-- 原因 -->
                        <td><?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['miss_reason']; ?>
</td>
                    </tr>
                    <?php endfor; endif; ?>
                </table>
            </div>
        </div><!-- 我的未接来电 end  -->

    </div><!-- box right end  -->
    <!--------------------------------------------------------------------------------------->
</div>

<!-- 详情窗口 -->
<div id="window" title="详细信息" style="width:600px;height:300px;padding:10px;display:none">
	<div style="padding-top:5px;padding-bottom:5px;background:#fff;">
		<label>详细信息：</label>
		<div id="SR_content" style="border:1px solid #ccc;width:100%;height:200px;font-family:verdana;padding-top:5px"></div>
	</div>
</div>
<div id='message_panel'></div>
<div id="view_message"></div><!--显示信息 -->

<script src="./jssrc/fusionCharts.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function() {
	//电话显示
	<?php if (! $this->_tpl_vars['power_phone_view']): ?>
	<?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['loop'] = is_array($_loop=$this->_tpl_vars['my_misscall_info']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	$('#miss_caller_number<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['id']; ?>
').text(hidden_part_number('<?php echo $this->_tpl_vars['my_misscall_info'][$this->_sections['loop']['index']]['caller']; ?>
'));
	<?php endfor; endif; ?>
	<?php endif; ?>

	//初始化div位置
	<?php if ($this->_tpl_vars['user_workbench_layout']): ?>
	var org_layout = decodeURIComponent('<?php echo $this->_tpl_vars['user_workbench_layout']; ?>
');
	_user_workbench_layout(org_layout);
	<?php endif; ?>

	//初始化portal
	var layout_record = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['user_workbench_layout'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
";
	_workbench_protal(layout_record);

	//统计(管理员)
	var role_type = <?php echo $this->_tpl_vars['role_type']; ?>
;
	var statistics = <?php if ($this->_tpl_vars['statistics']): ?><?php echo $this->_tpl_vars['statistics']; ?>
<?php else: ?>0<?php endif; ?>;
	var statistics = "";
	<?php if ($this->_tpl_vars['statistics']): ?>
	statistics = <?php echo $this->_tpl_vars['statistics']; ?>
;
	<?php endif; ?>
	//统计(坐席)
	<?php if ($this->_tpl_vars['role_type'] == 2 && $this->_tpl_vars['statistics'] != 0): ?>
	setTimeout(function(){
		success_chumn();//成功数-柱状图
	},1000);
	<?php endif; ?>
});
//坐席当天的统计-柱状图
function success_chumn()
{
	var graph ="";
	<?php if ($this->_tpl_vars['statistics']): ?>
	graph += "<set name='通话数'   value='<?php echo $this->_tpl_vars['statistics']['conn_num']; ?>
' color='AFD8F8'/>";
	graph += "<set name='呼通数'   value='<?php echo $this->_tpl_vars['statistics']['conn_success']; ?>
' color='F6BD0F'/>";
	/*graph += "<set name='转化量'   value='<?php echo $this->_tpl_vars['statistics']['transformation']; ?>
'  color='AFD8F8'/>";
	graph += "<set name='新增客户' value='<?php echo $this->_tpl_vars['statistics']['new_increment']; ?>
' color='FF8E46'/>";
	graph += "<set name='回访客户' value='<?php echo $this->_tpl_vars['statistics']['cle_visit']; ?>
' color='008E8E'/>";
	graph += "<set name='退化量'   value='<?php echo $this->_tpl_vars['statistics']['recede_num']; ?>
' color='8BBA00'/>";*/
	<?php endif; ?>
	var finishXML="<graph caption='<?php if ($this->_tpl_vars['statistics']): ?><?php echo $this->_tpl_vars['statistics']['deal_date']; ?>
<?php endif; ?> 数据统计情况 ' xAxisName='' yAxisName='number' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='1'  animation='0'  numdivlines='5'  divLineDecimalPrecision='0'  rotateNames='0' numVdivlines='0' yaxisminvalue='0' yaxismaxvalue='' lineThickness='1' showLegend='1' baseFontSize='12' formatNumber='1' numberSuffix=''>"+
	graph+
	"</graph> ";
	var w = $('.main-div').width()-10;
	var chart1 = new FusionCharts("./charts/FCF_Column3D.swf","ChartId",w,"260");
	chart1.setDataXML(finishXML);
	chart1.render("chart_chum");
}
</script>
<script src="./jssrc/viewjs/workbench.js" type="text/javascript"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pagefooter.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>