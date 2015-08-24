<?php /* Smarty version 2.6.19, created on 2015-07-21 10:31:21
         compiled from setting_content.htm */ ?>
<!--管理员向导-->
<!--角色配置与权限分配-->
<?php if ($this->_tpl_vars['setting'] == 2): ?>
<div id='setting_content2' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>角色配置与权限分配</div>
<div style='padding-top:10px;'>角色配置与权限分配 可规定好每个登进系统的员工拥有哪些权限！<br/>温馨提示：修改完角色或分配权限，退出重登陆即可生效</div>
<div style='padding:30px 0px;'>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('角色管理','index.php?c=role&m=edit_role','menu_icon_role');">立即进行角色配置</a>
</div>
<div>您也可以通过以下路径进行设置：</div>
<div style='padding:10px 45px;'><b>系统管理</b> => <b>角色管理</b></div>
</div>
<?php endif; ?>

<!--设置客户(联系人)自定义字段、数字字典-->
<?php if ($this->_tpl_vars['setting'] == 3): ?>	
<div id='setting_content3' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置客户（联系人）自定义字段、数字字典</div>
	<div style='padding-top:10px;'>客户（联系人）自定义字段指包括字段名称都由自己设置的字段； 客户数字字典可设置客户阶段、信息来源 两个字段的内容！<br/>温馨提示：首次使用系统时最好先设置好这些参数</div>
	<div style='padding:20px 0px;'>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_dictionary('client')">立即设置客户数字字典</a><br/><br/>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_field_confirm(0,'客户')">立即设置客户自定义字段</a><br/><br/>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_field_confirm(1,'联系人')">立即设置联系人自定义字段</a>
</div>
<div>您也可以通过以下路径进行设置：</div>
<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户管理</b> => <b>添加客户</b> => <b>（页面右方）</b></div>	
</div>
<?php endif; ?>

<!--设置客服自定义字段、数字字典-->
<?php if ($this->_tpl_vars['setting'] == 4): ?>
<div id='setting_content4' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置客服自定义字段、数字字典</div>
		<div style='padding-top:10px;'>客服自定义字段指包括字段名称都由自己设置的字段； 客服数字字典可设置服务类型 的内容！<br/>温馨提示：首次使用系统时最好先设置好这些参数</div>
		<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_dictionary('service')">立即设置客服数字字典</a><br/><br/>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_field_confirm(4,'客服')">立即设置客服自定义字段</a>
		</div>
		<div>您也可以通过以下路径进行设置：</div>
		<div style='padding:10px 45px;'><b>客服管理</b> =>  <b>添加服务</b> => <b>（页面右方）</b></div>
	</div>
<?php endif; ?>

<!--设置产品自定义字段、数字字典-->
<?php if ($this->_tpl_vars['setting'] == 5): ?>
<div id='setting_content5' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置产品自定义字段</div>
		<div style='padding-top:10px;'>产品自定义字段指包括字段名称都由自己设置的字段！<br/>温馨提示：首次使用系统时最好先设置好这些参数</div>
		<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_field_confirm(2,'产品')">立即设置产品自定义字段</a>
		</div>
		<div>您也可以通过以下路径进行设置：</div>
		<div style='padding:10px 45px;'><b>产品管理</b> => <b>产品管理</b> => <b>添加产品</b> => <b>（页面右方）</b></div>	
</div>
<?php endif; ?>

<!--设置订单自定义字段、数字字典-->
<?php if ($this->_tpl_vars['setting'] == 7): ?>
<div id='setting_content7' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置订单自定义字段、数字字典</div>
	<div style='padding-top:10px;'>订单自定义字段指包括字段名称都由自己设置的字段； 订单数字字典可设置订单状态 的内容！<br/>温馨提示：首次使用系统时最好先设置好这些参数</div>
	<div style='padding:20px 0px;'>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_dictionary('order')">立即设置订单数字字典</a><br/><br/>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="edit_field_confirm(3,'订单')">立即设置订单自定义字段</a>
	</div>
	<div>您也可以通过以下路径进行设置：</div>
	<div style='padding:10px 45px;'><b>订单管理</b> => <b>添加订单</b> => <b>（页面右方）</b></div>	
</div>
<?php endif; ?>

<!--参数设置-->
<?php if ($this->_tpl_vars['setting'] == 8): ?>
<div id='setting_content8'  style='background:#ffffff;padding-left:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置系统参数</div>
	<div style='padding-top:10px;'>系统参数包括短信后缀、是否允许号码重复。</div>
	<div style='padding:20px 0px;'>
	<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('参数设置','index.php?c=system_config','menu_icon_sysconfig');">立即设置系统参数</a>
	</div>
	<div>您也可以通过以下路径进行设置：</div>
	<div style='padding:10px 45px;'><b>系统管理</b> => <b>参数设置</b>
</div>
<?php endif; ?>

<!--添加员工-->
<?php if ($this->_tpl_vars['setting'] == 201): ?>
	<div id='setting_content201'  style='background:#ffffff;padding-left:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>添加员工</div>
		<div style='padding-top:10px;'>提示：您可以通过以下路径进行设置（包括添加、查看、修改、删除员工）：</div>
		<div style='padding:10px 45px;'><b>系统管理</b> => <b>员工管理</b></div>
		<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="add_user()">立即添加员工</a>&nbsp;&nbsp;
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('员工管理','index.php?c=user&m=list_user','menu_icon_user');">立即员工管理页面</a>
	</div>
	</div>
<?php endif; ?>

<!--导入客户数据-->
<?php if ($this->_tpl_vars['setting'] == 202): ?>
<div id='setting_content202' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>导入客户数据</div>
	<div style='padding-top:10px;'>导入客户数据有两种方式，分别是添加一条客户数据 和 批量添加客户数据！</div>
	<div>一、添加一条客户数据</div>
	<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('添加客户','index.php?c=client&m=new_client','menu_icon');">立即添加一条客户数据</a>
	</div>
	<div>您也可以通过以下路径添加一条客户数据：</div>
	<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户管理</b> => <b>添加客户</b></div>	
	
	<div style='padding-top:20px;'>二、批量添加客户数据</div>
	<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('客户导入','index.php?c=data_import&impt_type=1','menu_icon_input');">立即批量添加客户数据</a>
	</div>
	<div>您也可以通过以下路径批量添加客户数据：</div>
	<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户导入</b></div>	
</div>
<?php endif; ?>

<!--分配客户数据-->
<?php if ($this->_tpl_vars['setting'] == 203): ?>
<div id='setting_content203' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>分配客户数据</div>
<div style='padding-top:10px;'>把新添加的数据分配出去：</div>
<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('客户调配','index.php?c=client_resource','menu_icon_zytp');">立即分配客户数据</a>
</div>
<div>您也可以通过以下路径进行分配：</div>
<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户调配</b></div>
</div>
<?php endif; ?>

<!--导入产品数据-->
<?php if ($this->_tpl_vars['setting'] == 204): ?>
<div id='setting_content204' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>统计功能</div>
<div style='padding:10px;'>统计模块 分为坐席工作量统计和部门工作量统计。</div>
<div style='padding:0px 0px 10px 30px;'><b>坐席工作量统计</b>：显示每个坐席的通话时长、呼通数、每阶段客户数等（默认显示当天）。</div>
<div style='padding:0px 0px 10px 30px;'><b>部门工作量统计</b>：显示每个部门的通话时长、呼通数、每阶段客户数等（默认显示当天）。</div>
	<div>您也可以通过以下路径查看统计内容：</div>
	<div style='padding:0px 0px 10px 45px;'><b>统计分析</b> => <b>坐席工作量统计</b></div>	
	<div style='padding:0px 0px 10px 45px;'><b>统计分析</b> => <b>部门工作量统计</b></div>	
</div>
<?php endif; ?>

<!--添加订单-->
<?php if ($this->_tpl_vars['setting'] == 205): ?>
<div id='setting_content205' style='background:#ffffff;padding-left:10px;'>
<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>系统监控功能</div>
<div style='padding-bottom:10px;'>系统监控 分为技能组监控、坐席监控、排队监控。</div>
<div style='padding:0px 0px 10px 30px;'><b>技能组监控</b>：技能组，用于电话转接时连接到哪个组中执行沟通任务;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;技能组监控，实时监控技能组的工作情况。</div>
<div style='padding:0px 0px 10px 30px;'><b>坐席监控</b>：实时监控坐席的状态、登录时间、通话情况。</div>
<div style='padding:0px 0px 10px 30px;'><b>排队监控</b>：实时监控队列情况，可查看排队时长等。</div>
<div>您也可以通过以下路径查看：</div>
<div style='padding:0px 0px 10px 45px;'><b>系统监控</b> => <b>技能组监控</b></div>	
<div style='padding:0px 0px 10px 45px;'><b>系统监控</b> => <b>坐席监控</b></div>	
<div style='padding:0px 0px 10px 45px;'><b>系统监控</b> => <b>排队监控</b></div>	
</div>
<?php endif; ?>


<!--坐席向导-->
<!-- 配置IE-->
<?php if ($this->_tpl_vars['setting'] == 101): ?>
<div id='setting_content101' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>配置浏览器(IE)</div>
	<div style='font-weight:bold;'>根据以下路径及图示设置IE的 可信任站点 及 安全级别：</div>
	<div style='padding:10px 0px 5px 30px;'>浏览器菜单栏  工具 -> Internet选项 -> 安全</div>
	<img src='./image/setting/hu1.jpg' border='0' height='400' width='400' align='absmiddle' />
</div>
<?php endif; ?>

<!-- 设置声卡-->
<?php if ($this->_tpl_vars['setting'] == 102): ?>
<div id='setting_content102' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>设置声卡</div>
	<div >声音设置可以分为两个部分：音量调节，测试录音。</div>
	<div style='padding:10px 0px 10px 10px;font-weight:bold;'>一、音量调节</div>
	<img src='./image/setting/hu2.jpg' border='0' height='400' width='400' align='absmiddle' />
	<div style='padding:10px 0px 10px 10px;font-weight:bold;'>二、测试录音（测试所录的录音是否能有声音）</div>
	<img src='./image/setting/hu3.jpg' border='0' height='200' width='400' align='absmiddle' />
</div>
<?php endif; ?>

<!-- 签入系统-->
<?php if ($this->_tpl_vars['setting'] == 103): ?>
<div id='setting_content103' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>签入系统</div>
	<div >&nbsp;&nbsp;&nbsp;环境配置完后，就可以签入系统了。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/1.png' border='0' align='absmiddle' />&nbsp;<b>签入</b>：点击左上角“签入”按钮，提示签入成功后且处于“空闲”等待电话接入。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/8.png' border='0' align='absmiddle' />&nbsp;<b>满意度</b>：挂断电话后，点击“满意度”，可进行满意度调查。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/9.png' border='0' align='absmiddle' />&nbsp;<b>咨询坐席</b>：若有无法解答的问题，点击咨询坐席，进行询问，期间客户听到的是音乐。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/11.png' border='0' align='absmiddle' />&nbsp;<b>咨询接回</b>：咨询完毕后，点击此按钮，继续与客户通话。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/12.png' border='0' align='absmiddle' />&nbsp;<b>转接</b>：将正在沟通的客户转接给其它的坐席上。</div>
	<div style='padding:10px 0px 0px 30px;'><img src='./image/setting/13.png' border='0' align='absmiddle' />&nbsp;<b>三方</b>：若有无法解决问题需要第三方加入时，点击“三方”（即三人同时在一路电话中沟通）进行咨询。</div>
	<div style='padding:10px 0px 10px 30px;'><img src='./image/setting/14.png' border='0' align='absmiddle' />&nbsp;<b>三方接回</b>：加入第三方沟通完毕后，点击回到与客户沟通中。</div>
	<div style='padding:10px 0px 10px 30px;'>提示：外呼时，可使用拨号盘键入号码</div>
</div>
<?php endif; ?>

<!--个人设置-->
<?php if ($this->_tpl_vars['setting'] == 104): ?>
<div id='setting_content104' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>个人设置</div>
	<div >个人设置 可修改密码、姓名、短信提醒号码等个人信息。(个人设置 在 我的助手 -> 个人设置)</div>
	<div style='padding:20px 0px;'>
		<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('个人设置','index.php?c=user&m=self_set_user','menu_icon_userset');">立即设置个人信息</a>
	</div>
	<div style='padding:0px 10px 10px 10px;'>
	温馨提示：话机类型（软电话、实物话机），若选自动识别时，系统会根据登录系统时输入的分机号(坐席电话)长度来判断，长度小于5为软电话，大于等于5为话机。
	</div>
</div>
<?php endif; ?>

<!-- 客户管理-->
<?php if ($this->_tpl_vars['setting'] == 105): ?>
<div id='setting_content105' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>客户管理</div>
	<div  style='padding:10px;'>客户管理 日常工作首先进入此页面。&nbsp;&nbsp;<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="window.parent.addTab('客户管理','index.php?c=client','menu_icon_client');">立即进入客户管理页面</a></div>
	<div>在客户管理页面的工作：</div>
	<div  style='padding:10px 0px 0px 10px;'>1、先处理 今天要回访 的客户；</div>
	<div  style='padding:10px 0px 0px 10px;'>2、然后处理 过期未回访 的客户；</div>
	<div  style='padding:10px 0px 0px 10px;'>3、有时间在处理 7天内要回访 的客户。</div>
	<div  style='padding:10px;'> 提示:列表设置中可根据个人喜好设定列表显示的列。</div>
	<div>您也可以通过以下路径进入客户管理页面：</div>
	<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户管理</b></div>
</div>
<?php endif; ?>

<!-- 业务受理-->
<?php if ($this->_tpl_vars['setting'] == 106): ?>
<div id='setting_content106' style='background:#ffffff;padding:10px;'>
	<div style='padding:10px;font-weight:bold;font-size:15px;text-align:center;'>业务受理</div>
	<div  style='padding:10px;'>客户基本信息 在这通过客户阶段可以看到该客户进展到哪个阶段；与客户沟通后，记录客户相关信息（客户电话、地址、状态等）。</div>
	<div  style='padding:10px;'><b>联系人</b> 在这可以查看、添加或删除该客户相关的联系人。</div>
	<div  style='padding:10px;'><b>相关文件</b> 在这可以上传、下载或删除该客户相关的文件(如合同、需求等)。</div>
	<div  style='padding:10px;'><b>其他信息</b> 在这可以查看该客户所有操作日志、创建时间、下次联系时间、首次联系时间、所属人、所属部门。</div>
	<div  style='padding:10px;'>业务受理下方可以查看 该客户所有<b>过往联系记录</b>，还可以<b>新建联系记录</b>、创建下次联系时间提醒。</div>
	<div>您也可以通过以下路径进入：</div>
	<div style='padding:10px 45px;'><b>客户管理</b> => <b>客户管理</b> => <b>受理</b>（有数据即可看见）</div>
</div>
<?php endif; ?>