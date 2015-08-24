// version 1.4

var menu_selected	= null;
var error_timeout	= null;
var last_call	= {callnum:'',callid:''};
var default_caller	= ''; //默认主叫
var default_que	= ''; //默认队列
var busy_btn_checked = false; //忙碌状态按钮点击过
var is_callin = false; //是否队列来电标记(true是 false不是)
if(user_login_state == 2)
{
	busy_btn_checked = true;
}

//定义常量
var BUTTON_ON = 1;
var BUTTON_OFF = 2;
var MOUSE_ON = 3;
var OLMODEL_NONE = 0;//无长接通
var OLMODEL_AUTO = 1;//长接通
var OLMODEL_AUTOANSWER = 2//长接通并自动接听
var QUESTATE_LOGON = 1;//队列已登录/空闲

var _pc;
$.parser.onComplete = function(){
	if(_pc) clearTimeout(_pc);
	_pc = setTimeout(function(){
		$("#index_loading").fadeOut("normal",function(){
			$(this).remove();
			if(user_phone != '')
			{
				btn_state_change('login',BUTTON_ON);
				$('#login').click();
			}
			//帮助向导/设置向导 - 弹屏
			if(admin_login_system)
			{
				setting_window(1);
				$.ajax({
					url:'index.php?c=system_config&m=system_had_login',
					dataType:'json',
					cache:false
				});
			}
			else
			{
				if(get_cookie("est_login_system") == 1)	/*坐席*/
				{
					del_cookie("est_login_system");
					setting_window(2);
				}

			}
		});
	}, 1000);
}

$(document).ready(function (){
	//技能组(队列)信息
	wincall = $.wincall({
		phonetype:user_phone_type,
		tel_server:user_tel_server,
		tel_server_port:tel_server_port,
		sip_prefix:sip_prefix,
		dealnumber_before_call:true,
		connect_type:system_connect_type
	});
	initPhone();

	$("#toolbar input[type='button']").mouseover(function (){
		if($(this).attr('disabled') == 'disabled')
		{
			return;
		}
		btn_state_change($(this).attr('id'),MOUSE_ON);
	}).mouseout(function (){
		if($(this).attr('disabled') == 'disabled')
		{
			return;
		}
		btn_state_change($(this).attr('id'),BUTTON_ON);
	});

	//菜单效果
	$('.menu_item').focus(function (){
		this.blur();
	}).click(function (){
		if(menu_selected)
		{
			menu_selected.removeClass('selected');
		}
		$(this).addClass('selected');
		menu_selected = $(this);
		var tabTitle = $.trim($(this).text());
		tabTitle = tabTitle.replace('\u00a0', '');
		var url = $(this).attr('url');
		var icon = $('span',this).attr('class');
		addTab(tabTitle,url,icon);
	});

	//刷新
	$('#refresh').click(function (){
		var selected = $('#tabs').tabs('getSelected');
		var content  = selected.panel('options').content;
		$('#tabs').tabs('update',{
			tab:selected,
			options:{content:content}
		});
	});

	//发消息
	$('#btn_message').click(function (){
		message_panel_window();
	});
	//知识库
	$('#zsk').click(function (){
		addTab('知识库','index.php?c=knowledge','menu_icon');
	});
	//添加提醒
	$("#add_remind").click(function(){
		set_remind_window();
	});

	//tab切换变形问题 /*点击工作桌面刷新*/
	$('#tabs').tabs({
		onSelect: function(title){
			$('#tabs').tabs('resize');
			if(title == '工作桌面')
			{
				var tab = $('#tabs').tabs('getSelected');
				$('#tabs').tabs('update', {
					tab: tab,
					options:{
						content:createFrame('index.php?c=workbench')
					}
				});
			}
		}
	});

	window.onhashchange = function(){
		_monit_hash();
	}

	/*window.onbeforeunload = function(){
	return "确认离开页面?";
	}*/

	//监听消息
	monitor_notice();
});

/**
*监控hash事件，用于跨域打电话。
*hash 命令格式 do:param  例如：call:10086 命令为呼叫10086
*/
function _monit_hash()
{
	var order_str = _get_hash();
	if(!order_str)
	{
		return false;
	}
	var order_arr = order_str.split(':');
	if(order_arr.length != 2)
	{
		return false;
	}
	var order = order_arr[0];
	var param = order_arr[1];
	switch(order)
	{
		case 'call': _dial(param);break;
		default:break;
	}
	_clear_hash();
}

/**
* 清理hash
*/
function _clear_hash()
{
	location.hash = '';
}

/**
* 得到浏览器地址改变的hash
*/
function _get_hash()
{
	var hash = '';
	if (location.hash.charAt(0) == '#') {
		hash= location.hash.substr(1, location.hash.length - 1);
	}
	return hash;
}

// 控制来电的时候标题改变
var initial_title        = "";   //系统初始标题
var time_interval_handel = "";  //来电时，动态改变页面标题 HANDEL
var change_title_number  = 0;  //来电，动态改变标题的次数

/* 客户来电 - 窗口获取焦点、改变tittle - 显示在最前面*/
function _start_change_title()
{
	window.setTimeout(function(){  this.blur(); this.focus();},0);//页面获取焦点前置

	initial_title = document.title;//系统初始标题
	time_interval_handel = setInterval(function(){
		document.title = change_title_number % 2==0 ? "【　　　】- "+initial_title : "【新的来电】- "+initial_title;
		change_title_number ++;
	},500);
}

/*停止标题改变 - 还原标题*/
function _stop_change_title()
{
	if( !time_interval_handel )
	{
		return true;
	}

	clearInterval(time_interval_handel);//停止周期
	document.title = initial_title;//还原标题
}

/*初始化电话按钮*/
function initPhone()
{
	//签入签出
	$('#login').click(function(){
		if($('#login').val() == '签入')
		{
			$(this).attr('disabled', true);
			var ctiIp		= user_cti_server;
			var vccCode		= vcc_code;
			var agentNum	= user_num;
			var password 	= user_password;
			var agentPhone	= user_phone;
			var queueState	= user_login_state;
			var olMode		= user_ol_model;
			wincall.fn_login(ctiIp,vccCode,agentNum,password,agentPhone,queueState,olMode);
		}
		else if($('#login').val() == '签出')
		{
			$(this).attr('disabled', true);
			wincall.fn_logout();
		}
	});

	//空闲
	$('#unbusy').click(function (){
		busy_btn_checked = false;
		wincall.fn_unbusy();
	});
	// 忙碌
	$('#busy').click(function (){
		busy_btn_checked = true;
		wincall.fn_busy();
	});
	//根据置忙原因的
	$.each(busy, function(index, id) {
		$('#busy'+id).click(function (){
			busy_btn_checked = true;
			wincall.fn_busy(id);
		});
	});

	//保持 恢复
	$('#hold').click(function (){
		if($('#hold').val() == '保持')
		{
			wincall.fn_hold();
		}
		else if($('#hold').val() == '恢复')
		{
			wincall.fn_unhold();
		}
	});

	//内呼
	$('#callinner').click(function (){
		$('#dail_panel').window({
			title:'内呼',
			href:'index.php?c=phone_control&m=callinner_phone_control',
			width:450,
			top:100,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			shadow:false,
			cache:false
		});
	});

	//外呼
	$('#callouter').click(function (){
		_dial_panel('','',user_channel);
	});

	//挂断
	$('#hangup').click(function (){
		wincall.fn_hangup();
	});

	//咨询坐席
	$('#consultinner').click(function (){
		$('#dail_panel').window({
			title:'咨询坐席',
			href:'index.php?c=phone_control&m=consultinner_phone_control',
			width:450,
			top:100,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			shadow:false,
			cache:false
		});
	});

	//咨询外线
	$('#consultouter').click(function (){
		$('#dail_panel').window({
			title:'咨询外线',
			href:'index.php?c=phone_control&m=consultouter_phone_control',
			width:320,
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			shadow:false,
			cache:false
		});
	});

	//咨询接回
	$('#consultback').click(function (){
		wincall.fn_consultback();
	});

	//转接
	$('#transfer').click(function (){
		wincall.fn_transfer();
	});

	//三方
	$('#threeway').click(function (){
		wincall.fn_3way();
	});
	//三方接回
	$('#threewayback').click(function (){
		wincall.fn_3wayback();
	});

	//拦截
	$('#intercept').click(function (){
		wincall.fn_intercept();
	});
	//强插
	$('#breakin').click(function (){
		wincall.fn_breakin();
	});
	//满意度
	$('#evaluation').click(function (){
		wincall.fn_evaluate();
	});
    //转技能组
    $('#transque').click(function(){
       // var _ques =  wincall.fn_get_que();
        transque_page();
     });
}

//用红字显示
function showimport(responce)
{
	var content = '';
	var show = true;
	var dialog  = false;
	switch(responce)
	{
		case '002' : show = false;content = '系统置忙';break;
		case '003' : show = false;content = '系统置闲';break;
		case '004' : show = false;content = '系统占用';break;
		case '005' : show = false;content = '事后处理';break;
		case '007' : content = '常接通电话离线';break;
		case '011' : show = false;content = '上线空闲';break;
		case '012' : show = false;content = '上线示忙';break;
		case '024' : show = false;content = '强制签出';break;
		case '096' : content = '系统重签入';break;
		case '097' :  content = '连接失败';break;
		case '098' : content = 'flash未加载';break;
		case '099' : show = false;content = '断开socket';break;
		case '0000' : show = false;dialog = true;content = '通话完成';break;
		case '0400' : show = false;content = '外呼中';break;
		case '0500' : show = false;dialog = true;content = '被呼叫来电';break;
		case '0501' : show = false;dialog = true;content = '队列分配来电';break;
		case '0502' : show = false;content = '外呼来电';break;
		case '0503' : show = false;content = '监听来电';break;
		case '0504' : show = false;content = '被咨询';break;
		case '0505' : show = false;dialog = true;content = '自动外呼来电';break;
		case '0507' : show = false;content = '常接通模式来电';break;
		case '0508' : show = false;content = '外线转坐席';break;
		case '0600' : show = false;content = '通话中';break;
		case '0800' : show = false;content = '常接通通话中';break;
		case '0900' : dialog = true;content = '常接通模式电话断开';break;
		case '1000' : dialog = true;show = false;content = '己方接通';break;
		case '2100' : content = '咨询外呼中';break;
		case '2200' : content = '咨询接通';break;
		case '2300' : content = '咨询接回';break;
		case '2400' : content = '咨询未呼通';break;
		case '2500' : content = '三方接通';break;
		case '2600' : content = '三方接回';break;
		case '3100' : content = '监听成功';break;
		case '3200' : content = '强插成功';break;
		case '3300' : content = '转接成功';break;
		case '3400' : content = '拦截成功';break;
		case '3500' : content = '通话已转接';break;//被转接的坐席收到此信息
		case '4000' : content = '常接通模式未接通';break;
		case '5500' : content = '对方振铃';break;
		case '20001' : content = '软电话注册成功';break;
		case '20002' : content = '软电话重新注册';break;
		case '20003' : content = '软电话注册失败';break;
		case '10010' : content = '连接成功';break;
		case '10011' : content = '与服务器断开连接';break;
		case '10012' : content = '无法连接到服务器';break;
		case '10013' : content = '安全错误';break;
		case '10014' : show = false;content = ' 发送数据错误，服务器还没准备好';break;
		case '10015' : content = '网络异常';break;
		case '10016' : content = '网络异常，中间件断开';break;
		case '001000' : content = '初始化成功';break;
		case '001001' : content = '坐席已被初始化';break;
		case '001002' : content = '本socket已被初始化';break;
		case '001003' : content = '分机号不存在';break;
		case '001004' : content = '分机已有坐席使用';break;
		case '001005' : content = '队列不存在';break;
		case '001006' : content = '已达登录上限';break;
		case '001007' : content = '分机类型错误';break;
		case '001020' : content = '企业错误(停用或过期等)';break;
		case '001801' : content = '连接数据库错误';break;
		case '001804' : content = '帐号错误';break;
		case '001805' : content = '找到多个坐席';break;
		case '001806' : content = '密码错误';break;
		case '001807' : content = '分机查询错误';break;
		case '001808' : content = '分机不存在';break;
		case '001809' : content = '找到多个分机';break;
		case '001810' : content = '查询参数[vcccode]为空';break;
		case '001811' : content = '查询参数[agnum]为空';break;
		case '001812' : content = '查询参数[agpass]为空';break;
		case '001813' : content = '查询参数[agphone]为空';break;
		case '001999' : content = '其它初始化错误';break;
		case '002000' : content = '签出成功';break;//反初始化成功
		case '002001' : content = '坐席尚未初始化';break;
		case '002999' : content = '其它反初始化错误';break;
		case '003000' : content = '签入成功';break;
		case '003001' : content = '坐席尚未初始化';break;
		case '003999' : content = '其它签入错误';break;
		case '004000' : show = false;content = '签出成功';break;
		case '004001' : content = '坐席尚未初始化';break;
		case '004002' : content = '坐席尚未登录';break;
		case '004999' : content = '其它签出错误';break;
		case '005000' : show = false;content = '置忙成功';break;
		case '005001' : content = '坐席尚未初始化';break;
		case '005002' : content = '坐席尚未签入';break;
		case '005003' : content = '已处于置忙';break;
		case '005004' : content = '不允许置忙';break;
		case '005999' : content = '其它置忙错误';break;
		case '006000' : show = false;content = '置闲成功';break;
		case '006001' : content = '坐席尚未初始化';break;
		case '006002' : content = '坐席尚未登录';break;
		case '006003' : content = '已处于置闲';break;
		case '006999' : content = '其它置闲错误';break;
		case '007000' : content = '未登录';break;
		case '007010' : content = '尚未初始化';break;
		case '009000' : show = false;content = '进入自动外呼成功';break;
		case '009001' : content = '坐席尚未初始化';break;
		case '009002' : content = '坐席尚未登录';break;
		case '009003' : content = '项目错误(此项目非自动外呼项目或没有这个项目)';break;
		case '009004' : content = '已进入自动外呼';break;
		case '009999' : content = '其它进入自动外呼错误';break;
		case '010000' : show = false;content = '退出自动外呼成功';break;
		case '010001' : content = '坐席尚未初始化';break;
		case '010002' : content = '坐席尚未登录';break;
		case '010003' : content = '项目错误(此项目非自动外呼项目或没有这个项目)';break;
		case '010004' : content = '已退出自动外呼';break;
		case '010999' : content = '其它退出自动外呼错误';break;
		case '021000' : show = false;content = '呼叫坐席成功';break;
		case '021001' : content = '主叫坐席错误';break;
		case '021002' : content = '被叫坐席错误';break;
		case '021003' : content = '队列错误';break;
		case '021004' : content = '队列不允许呼叫';break;
		case '021005' : content = '主叫状态不对';break;
		case '021006' : content = '被叫状态不空闲';break;
		case '021007' : content = '被叫坐席不属于本企业';break;
		case '021008' : content = '主叫号码不对';break;
		case '021011' : content = '没有外呼权限';break;
		case '021020' : content = '企业错误(停用或过期等)';break;
		case '021030' : content = '常接通模式不能呼叫坐席';break;
		case '021451' : content = '消息格式错误';break;
		case '021501' : content = '企业未启用';break;
		case '021502' : content = '企业已过期或未开通';break;
		case '021503' : content = '余额不足';break;
		case '021504' : content = '外呼号码为黑名单';break;
		case '021999' : content = '其它呼叫坐席错误';break;
		case '022000' : show = false;content = '呼叫外线成功';break;
		case '022001' : content = '呼叫坐席错误';break;
		case '022002' : content = '呼叫错误';break;
		case '022003' : content = '队列错误';break;
		case '022004' : content = '队列不允许呼叫';break;
		case '022005' : content = '主叫状态不对';break;
		case '022007' : content = '企业不匹配';break;
		case '022008' : content = '主叫号码错误';break;
		case '022009' : content = '常接通模式未接通';break;
		case '022010' : content = '通道错误';break;
		case '022011' : content = '没有外呼权限(坐席没有外呼的权限)';break;
		case '022020' : content = '企业错误(停用或过期等)';break;
		case '022451' : content = '消息格式错误';break;
		case '022501' : content = '企业未启用';break;
		case '022502' : content = '企业已过期或未开通';break;
		case '022503' : content = '余额不足';break;
        case '022504' : content = '外呼号码为黑名单';break;
		case '022999' : content = '其它呼叫外线错误';break;
		case '023000' : show = false;content = '挂机成功';break;
		case '023001' : content = '坐席错误';break;
		case '023002' : content = '不处于通话中';break;
		case '023999' : content = '其它挂机错误';break;
		case '024000' : show = false;content = '咨询成功';break;
		case '024001' : content = '主叫坐席错误';break;
		case '024002' : content = '咨询坐席错误';break;
		case '024004' : content = '咨询坐席不空闲';break;
		case '024005' : content = '不在通话中';break;
		case '024006' : content = '通话类型不对';break;
		case '024007' : content = '通话异常';break;
		case '024009' : content = '已处于咨询或三方';break;
		case '024451' : content = '消息格式错误';break;
		case '024501' : content = '企业未启用';break;
		case '024502' : content = '企业已过期或未开通';break;
		case '024503' : content = '余额不足';break;
        case '024504' : content = '咨询号码为黑名单';break;
		case '024999' : content = '其它咨询坐席错误';break;
		case '025000' : show = false;content = '咨询成功';break;
		case '025001' : content = '主叫坐席错误';break;
		case '025002' : content = '咨询坐席错误';break;
		case '025004' : content = '咨询坐席不空闲';break;
		case '025005' : content = '不在通话中';break;
		case '025006' : content = '通话类型不对';break;
		case '025007' : content = '通话异常';break;
		case '025009' : content = '于咨询或三方';break;
		case '025020' : content = '企业错误(停用或过期等)';break;
		case '025451' : content = '消息格式错误';break;
		case '025501' : content = '企业未启用';break;
		case '025502' : content = '企业已过期或未开通';break;
		case '025503' : content = '余额不足';break;
		case '025504' : content = '咨询号码为黑名单';break;
		case '025999' : content = '其它咨询外线错误';break;
		case '026000' : show = false;content = '咨询接回成功';break;
		case '026001' : content = '坐席错误';break;
		case '026002' : content = '没有通话';break;
		case '026003' : content = '通话异常';break;
		case '026004' : content = '不在咨询三方中';break;
		case '026005' : content = '咨询状态不对';break;
		case '026999' : content = '其它咨询接回错误';break;
		case '027000' : show = false;content = '三方成功';break;
		case '027001' : content = '坐席错误';break;
		case '027002' : content = '没有通话';break;
		case '027003' : content = '通话异常';break;
		case '027004' : content = '不在咨询中';break;
		case '027005' : content = '咨询状态异常';break;
		case '027999' : content = '其它三方错误';break;
		case '028000' : show = false;content = '三方接回成功';break;
		case '028001' : content = '坐席错误';break;
		case '028002' : content = '没有通话';break;
		case '028003' : content = '通话异常';break;
		case '028004' : content = '不在咨询中';break;
		case '028005' : content = '咨询状态异常';break;
		case '028999' : content = '其它三方接回错误';break;
		case '029000' : show = false;content = '转接成功';break;
		case '029001' : content = '坐席错误';break;
		case '029002' : content = '没有通话';break;
		case '029003' : content = '通话异常';break;
		case '029004' : content = '不在咨询中';break;
		case '029005' : content = '询状态异常';break;
		case '029006' : content = '不允许转接外线';break;
		case '029999' : content = '其它转接错误';break;
		case '030000' : show = false;content = '监听成功';break;
		case '030001' : content = '监听坐席错误';break;
		case '030002' : content = '被监听坐席错误';break;
		case '030004' : content = '监听坐席状态不对';break;
		case '030005' : content = '不在通话中';break;
		case '030006' : content = '通话类型不对';break;
		case '030007' : content = '通话异常';break;
		case '030009' : content = '已被监听或强插';break;
		case '030020' : content = '企业错误(停用或过期等)';break;
		case '030030' : content = '常接通模式不能监听';break;
		case '030999' : content = '其它监听错误';break;
		case '031000' : show = false;content = '拦截成功';break;
		case '031001' : content = '坐席错误';break;
		case '031002' : content = '不在监听中';break;
		case '031999' : content = '其它拦截错误';break;
		case '032000' : show = false;content = '强插成功';break;
		case '032001' : content = '坐席错误';break;
		case '032003' : content = '不在监听或强插中';break;
		case '032999' : content = '其它强插错误';break;
		case '033000' : show = false;content = '保持成功';break;
		case '033001' : content = '坐席错误';break;
		case '033002' : content = '坐席不在通话中';break;
		case '033003' : content = '尚未接通';break;
		case '033004' : content = '已保持';break;
		case '033005' : content = '没有权限';break;
		case '033999' : content = '其他保持错误';break;
		case '034000' : show = false;content = '恢复成功';break;
		case '034999' : content = '其它恢复错误';break;
		case '041000' : content = '满意度成功';break;
		case '041001' : content = '坐席错误';break;
		case '041002' : content = '坐席不在通话中';break;
		case '041003' : content = '坐席不在通话中';break;
		case '041004' : content = '通话异常';break;
		case '041005' : content = '通话类型错误';break;
		case '041006' : content = '通话状态不对';break;
		case '041007' : content = '通道错误';break;
		case '041999' : content = '其它满意度错误';break;
		case '042000' : show = false;content = '握手信号';break;
        case '043000' : content = '转IVR成功';break;
        case '043001' : content = '坐席错误';break;
        case '043002' : content = '坐席不在通话中';break;
        case '043003' : content = '坐席不在通话中';break;
        case '043004' : content = '通话异常';break;
        case '043005' : content = '通话类型错误';break;
        case '043006' : content = '通话状态不对';break;
        case '043007' : content = '通道错误';break;
        case '043999' : content = '其它转IVR错误';break;
        case '044000' : content = '转技能组成功';break;
        case '044001' : content = '坐席错误';break;
        case '044002' : content = '坐席不在通话中';break;
        case '044003' : content = '坐席不在通话中';break;
        case '044004' : content = '通话异常';break;
        case '044005' : content = '通话类型错误';break;
        case '044006' : content = '通话状态不对';break;
        case '044007' : content = '通道错误';break;
        case '044008' : content = '技能组错误';break;
        case '044999' : content = '其它转技能组错误';break;
		case '051000' : show = false;content = '常接通模式接通';break;
		case '051001' : content = '坐席错误';break;
		case '051002' : content = '不处于通话中';break;
		case '051003' : content = '非振铃状态';break;
		case '051020' : content = '企业错误(停用或过期等)';break;
		case '051030' : content = '常接通模式不允许';break;
		case '051999' : content = '其它应答错误';break;
		default: content='事件'+responce;break;
	}

	if(user_if_tip == 0 || user_if_tip == 1)
	{
		if( content != '' && dialog )
		{
			$.messager.show({
				title:'系统消息',
				msg:content,
				showType:'show'
			});
		}
	}

	if(content!='' && show)
	{
		clearTimeout(error_timeout);
		$('#phone_error').text(content);
		$('#phone_error').css('display','');
		error_timeout = setTimeout(function ()	{
			$('#phone_error').css('display','none');
		},3000);
	}
}

//程序消息返回
function callProcess(responce){
	if(responce=='' || !responce)
	return;
	showimport(responce);
	switch(responce)
	{
		case '002' : //系統置忙
		case '012' : //上线置忙
		case '005000' : //置忙成功
		callQueueState('忙碌');
		btn_state_change('busy',BUTTON_OFF);
		$.each(busy, function(index, id) {
			btn_state_change('busy'+id,BUTTON_OFF);
		});
		btn_state_change('unbusy',BUTTON_ON);
		btn_state_change('callinner',BUTTON_ON);
		btn_state_change('hangup',BUTTON_OFF);
		btn_state_change('autocall',BUTTON_ON);
		break;
		case '003' :  //系統置閑
		case '011' :  //上线空闲
		case '006000' :  //置闲成功
		callQueueState('空闲');
		btn_state_change('busy',BUTTON_ON);
		$.each(busy, function(index, id) {
			btn_state_change('busy'+id,BUTTON_ON);
		});
		btn_state_change('unbusy',BUTTON_OFF);
		btn_state_change('callinner',BUTTON_ON);
		btn_state_change('hangup',BUTTON_OFF);
		btn_state_change('autocall',BUTTON_ON);
		break;
		case '004' :  //系统占用
		callQueueState('系统占用');
		btn_state_change('hangup',BUTTON_ON);
		btn_state_change('busy',BUTTON_OFF);
		$.each(busy, function(index, id) {
			btn_state_change('busy'+id,BUTTON_OFF);
		});
		btn_state_change('unbusy',BUTTON_OFF);
		btn_state_change('callinner',BUTTON_OFF);
		break;
		case '005' :  //事后处理
		_stop_music();
		_stop_clock();
		_clear_number_loc();
		callQueueState('事后处理');
		if(busy_btn_checked) //忙碌状态时候处理直接忙碌
		{
			wincall.fn_busy();
		}
		else
		{
			btn_state_change('busy',BUTTON_ON);
			$.each(busy, function(index, id) {
				btn_state_change('busy'+id,BUTTON_ON);
			});
			btn_state_change('unbusy',BUTTON_ON);
		}
		break;
		case '007' :  //电话离线状态
		callQueueState('电话离线');
		$("#toolbar input[type='button']").each(function (i,item){
			btn_state_change(item.id,BUTTON_OFF);
		});
		btn_state_change('login',BUTTON_ON);
		break;
		case '024':
		$.messager.alert('警告','您已被管理员强制签出！','error');
		break;
        case '025':
        $.messager.alert('警告','您已被管理员强制置闲！','error');
        break;
        case '026':
        $.messager.alert('警告','您已被管理员强制置忙！','error');
        break;
		case '096' : //系统重签入
		$("#toolbar input[type='button']").each(function (i,item){
			btn_state_change(item.id,BUTTON_OFF);
		});
		break;
		case '098'://flash未加载
		btn_state_change('login',BUTTON_ON);
		break;
		case '099' :  //断开连接
		case '004000' :  //签出成功
		_stop_music();
		_stop_clock();
		_stop_change_title();
		_clear_number_loc();
		$('#autocall_stat').css('display','none');
		callSeatState('未登录');
		callQueueState('未登录');
		$('#login').val('签入');
		$('#hold').val('保持');
		$("#toolbar input[type='button']").each(function (i,item){
			btn_state_change(item.id,BUTTON_OFF);
		});
		btn_state_change('login',BUTTON_ON);
		break;
		case '0000' : //通话完成
		_stop_music();
		_stop_change_title();
		_stop_clock();
		_clear_number_loc();
		callSeatState('通话完成');
		$('#hold').val('保持');
		_close_ring_panel();
		btn_state_change('consultinner',BUTTON_OFF);
		btn_state_change('consultouter',BUTTON_OFF);
		btn_state_change('hangup',BUTTON_OFF);
		btn_state_change('evaluation',BUTTON_OFF);
		btn_state_change('transfer',BUTTON_OFF);
		btn_state_change('hold',BUTTON_OFF);
		btn_state_change('consultback',BUTTON_OFF);
		btn_state_change('threeway',BUTTON_OFF);
		btn_state_change('threewayback',BUTTON_OFF);
		btn_state_change('transque',BUTTON_OFF);
        if(busy_btn_checked) //忙碌状态时候处理直接忙碌
        {
            wincall.fn_busy();
        }
        else
        {
            btn_state_change('busy',BUTTON_ON);
            $.each(busy, function(index, id) {
                btn_state_change('busy'+id,BUTTON_ON);
            });
            btn_state_change('unbusy',BUTTON_ON);
        }
		break;
		case '0400' : //呼叫中
		callSeatState('呼叫中');
		btn_state_change('hangup',BUTTON_ON);
		var _called = wincall.fn_getParam('Called');
		last_call = {callnum:_called,callid:wincall.fn_getParam('CallId')};
		_set_number_loc(_called)
		//外呼弹屏： 1是  2否
		if(user_outcall_popup == 1)
		{
			var title_phone = _called;
			if(power_phone_view != 1)
			{
				title_phone     = hidden_part_number(title_phone);
			}
			if(pop_address != '')
			{
				var _pop_address = eval('"'+pop_address+'"');
				addTab('业务受理'+title_phone,_pop_address,'menu_icon');
			}
			else
			{
				addTab('业务受理'+title_phone+'','index.php?c=client&m=search_client&phone='+_called,'menu_icon');
			}
		}
		break;
		case '0500' : // 来电  被叫 (呼叫坐席)
		callSeatState('来电');
		btn_state_change('hangup',BUTTON_ON);
		last_call = {callnum:wincall.fn_getParam('Caller'),callid:wincall.fn_getParam('CallId')};
		if(user_ol_model != OLMODEL_AUTOANSWER)
		{
			_start_music();
		}
		var _OAgId = wincall.fn_getParam('OAgId');//来电坐席ag_i
		_user_callin(_OAgId,'');
		break;
		case '0501':  //队列来电  队列分配
		_start_music();
		_start_change_title();
		btn_state_change('hangup',BUTTON_ON);
		callSeatState('队列来电');
        is_callin = true; //是否队列来电标记(true是 false不是)
		last_call = {callnum:wincall.fn_getParam('Caller'),callid:wincall.fn_getParam('CallId')};
		var _caller = wincall.fn_getParam('Caller');
		var _queid = wincall.fn_getParam('QueId');//队列
		var _quename = que_list[_queid];
		var _servnum = wincall.fn_getParam('ServNum');//服务号码
        var _title_str = '';
        if (_quename != undefined) {
             _title_str = '【'+_quename+'】';  //   队列：_quename
        }

		//来电弹屏
		var title_phone = _caller;
		if(power_phone_view != 1)
		{
			title_phone = hidden_part_number(title_phone);
		}
		if(pop_address != '')
		{
			var _called = wincall.fn_getParam('Called');
			var _custom = wincall.fn_getParam('Custom');
			var _pop_address = eval('"'+pop_address+'"');
			addTab(title_phone+'来电',_pop_address,'menu_icon');
		}
		else
		{
			addTab(title_phone+'来电','index.php?c=client&m=search_client&callin=1&phone='+_caller,'menu_icon');
		}
		_set_number_loc(_caller);
		$('#callin_number').html(title_phone);
		_ring_panel_dialog('新的来电'+_title_str);
		break;
		case '0502': //外呼来电  外呼时呼叫自己
		btn_state_change('hangup',BUTTON_ON);
		callSeatState('外呼来电');
		if(user_phone_type == 'softphone')
		{
			wincall.fn_answer();
		}
		break;
		case '0503':  // 监听来电 监听时呼叫自己
		btn_state_change('hangup',BUTTON_ON);
		callSeatState('监听来电');
		if(user_phone_type == 'softphone')
		{
			wincall.fn_answer();
		}
		break;
		case '0504':  //咨询来电  被咨询
		_start_music();
		btn_state_change('hangup',BUTTON_ON);
		callSeatState('咨询来电');
		var _OAgId = wincall.fn_getParam('OAgId');//来电坐席ag_id
		var coustom_number = wincall.fn_getParam('Others');//客户电话
		_user_callin(_OAgId,coustom_number);
		break;
		case '0505'://自动外呼来电
		_start_change_title();
		var _caller = wincall.fn_getParam('Caller');
		_set_number_loc(_caller);
		last_call = {callnum:_caller,callid:wincall.fn_getParam('CallId')};
		var acPro = wincall.fn_getParam('AcPro');//项目ID
		var acTask = wincall.fn_getParam('AcTask');//任务ID;
		if(user_ol_model != OLMODEL_AUTOANSWER)
		{
			_start_music();
			setTimeout(function(){
				wincall.fn_answer();
			},1000);
			break;
		}
		case '0507'://常接通模式来电
		setTimeout(function(){
			wincall.fn_answer(true);
		},2000);
		break;
		case '0508'://外线直接转坐席
		_start_music();
		_start_change_title();
		btn_state_change('hangup',BUTTON_ON);
		callSeatState('客户来电');
		last_call = {callnum:wincall.fn_getParam('Caller'),callid:wincall.fn_getParam('CallId')};
		var _caller = wincall.fn_getParam('Caller');
		//来电弹屏
		var title_phone = _caller;
		if(power_phone_view != 1)
		{
			title_phone = hidden_part_number(title_phone);
		}
		if(pop_address != '')
		{
			var _called = wincall.fn_getParam('Called');
			var _custom = wincall.fn_getParam('Custom');
			var _servnum = wincall.fn_getParam('ServNum');
			var _pop_address = eval('"'+pop_address+'"');
			addTab(title_phone+'来电',_pop_address,'menu_icon');
		}
		else
		{
			addTab(title_phone+'来电','index.php?c=client&m=search_client&callin=1&phone='+_caller,'menu_icon');
		}
		_set_number_loc(_caller);
		$('#callin_number').html(title_phone);
		_ring_panel_dialog('新的来电');
		break;
		case '0600':  //通话中
		case '0800'://常接通模设计接通
		_stop_music();
		_stop_change_title();
		callSeatState('通话中');
		btn_state_change('hangup',BUTTON_ON);
		btn_state_change('evaluation',BUTTON_ON);
		btn_state_change('consultinner',BUTTON_ON);
		btn_state_change('consultouter',BUTTON_ON);
		btn_state_change('hold',BUTTON_ON);
        if(is_callin==true)
        {
            btn_state_change('transque',BUTTON_ON);
            is_callin = false;
        }
		_start_clock();
		_close_ring_panel();
		break;
		case '024000' : //咨询成功
		case '025000' :
		btn_state_change('consultback',BUTTON_ON);
		btn_state_change('consultinner',BUTTON_OFF);
		btn_state_change('consultouter',BUTTON_OFF);
		break;
		case '2200':   //咨询接通
		btn_state_change('threeway',BUTTON_ON);
		btn_state_change('transfer',BUTTON_ON);
		btn_state_change('consultinner',BUTTON_OFF);
		btn_state_change('consultouter',BUTTON_OFF);
		break;
		case '2300':
		case '026000':
		case '2400':  //咨询未呼通
		btn_state_change('consultback',BUTTON_OFF);
		btn_state_change('transfer',BUTTON_OFF);
		btn_state_change('threeway',BUTTON_OFF);
		btn_state_change('consultinner',BUTTON_ON);
		btn_state_change('consultouter',BUTTON_ON);
		break;
		case '2500': //三方中
		case '027000': //三方成功
		btn_state_change('threewayback',BUTTON_ON);
		btn_state_change('consultback',BUTTON_OFF);
		btn_state_change('transfer',BUTTON_OFF);
		btn_state_change('threeway',BUTTON_OFF);
		break;
		case '2600': //三方接回
		case '028000': //三方接回成功
		btn_state_change('threewayback',BUTTON_OFF);
		btn_state_change('consultinner',BUTTON_ON);
		btn_state_change('consultouter',BUTTON_ON);
		break;
		case '003000' :  //签入成功
		callSeatState('签入');
		_set_default_caller_que();
		if(user_ol_model == OLMODEL_NONE)
		{
			if(user_login_state == QUESTATE_LOGON)
			{
				callQueueState('空闲');
				btn_state_change('busy',BUTTON_ON);
				$.each(busy, function(index, id) {
					btn_state_change('busy'+id,BUTTON_ON);
				});
			}
			else
			{
				callQueueState('忙碌');
				btn_state_change('unbusy',BUTTON_ON);
			}
		}
		else
		{
			callQueueState('常接通离线');
		}
		$('#login').val('签出');
		$('#autocall').val('进入AC');
		btn_state_change('autocall',BUTTON_ON);
		btn_state_change('login',BUTTON_ON);
		btn_state_change('callinner',BUTTON_ON);
		btn_state_change('callouter',BUTTON_ON);
		break;
		case '009000'://进入自动外呼成功
		case '009004':
		$('#autocall').val('退出AC');
		$('#autocall_stat').css('display','block');
		btn_state_change('autocall',BUTTON_ON);
		break;
		case '010000'://退出自动外呼成功
		case '010004':
		$('#autocall').val('进入AC');
		$('#autocall_stat').css('display','none');
		btn_state_change('autocall',BUTTON_ON);
		break;
		case '033000' : //保持成功
		$('#hold').val('恢复');
		btn_state_change('hold',BUTTON_ON);
		break;
		case '034000': //恢复成功
		$('#hold').val('保持');
		btn_state_change('hold',BUTTON_ON);
		break;
		case '3100':
		$('#monitor_agent_chanspy').window({
			href:"index.php?c=monitor&m=monitor_agent_chanspy",
			width:150,
			title:"监听",
			collapsible:false,
			minimizable:false,
			maximizable:false,
			resizable:false,
			cache:false,
			onClose:function(){
				//挂断
				wincall.fn_hangup();
			}
		});
		break;
	}
}

/**
*
* 内线来电，第二个参数显示客户电话
*/
function _user_callin(user_id,coustom_number) //坐席来电，得到坐席名称
{
	$('#caller_loc').text('');
	$('#calling_loc').text('');

	$.ajax({
		url: 'index.php?c=user&m=get_user_name',
		data: {'user_id':user_id},
		dataType: 'json',
		success: function(responce){
			var ag_name = responce['content'];
			if(coustom_number == '')
			{
				$('#callin_number').html(ag_name);
			}
			else
			{
				$('#callin_number').html(coustom_number);
				//来电弹屏
				var title_phone = coustom_number;
				if(power_phone_view != 1)
				{
					title_phone = hidden_part_number(title_phone);
				}
				if(pop_address != '')
				{
					var _pop_address = eval('"'+pop_address+'"');
					addTab(title_phone+'来电',_pop_address,'menu_icon');
				}
				else
				{
					addTab(title_phone+'来电','index.php?c=client&m=search_client&callin=1&phone='+coustom_number,'menu_icon');
				}
			}

			_ring_panel_dialog(ag_name+'来电咨询');
		}
	});
}

function _start_music(){
	if(user_phone_type == 'softphone')
	{
		var asound = getFlashObject("asound");
		asound.SetVariable("f",'./media/start.mp3');
		asound.GotoFrame(1);
	}
	else
	{
		return true;
	}
}

function _stop_music(){
	if(user_phone_type == 'softphone')
	{
		var asound = getFlashObject("asound");
		asound.SetVariable("f",'not-exist.mp3');
		asound.GotoFrame(1);
	}
	else
	return true;
}

//设置向导1 首次2坐席
function setting_window(type)
{
	$('#setting_background').css({position:'absolute',filter:'alpha(Opacity=80)',opacity:'0.5',zIndex:'1000',top:'0px',left:'0px',width:'100%',height:'100%',background:'#000000',textAlign:'center',paddingTop:'20%'});
	$('#setting_panel').window({
		href:'index.php?c=setting&m=setting_bomb_box&type='+type,
		title:'系统向导',
		width:380,
		top:200,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		closable:false,
		draggable:false
	});
}

//加选项卡函数
function addTab(subtitle,url,icon)
{
	if(!$('#tabs').tabs('exists',subtitle))
	{
		$('#tabs').tabs('add',{
			title:subtitle,
			content:createFrame(url),
			closable:true,
			icon:icon,
			cache:false
		});
		/*双击关闭TAB选项卡*/
		$('.tabs-inner').dblclick(function(){
			var subtitle = $(this).children('span').text();
			if(subtitle != '工作桌面')
			$('#tabs').tabs('close',subtitle);
		});
	}
	else
	{
		$('#tabs').tabs('select',subtitle);
		var tab = $('#tabs').tabs('getSelected');
		$('#tabs').tabs('update', {
			tab: tab,
			options:{
				content:createFrame(url)
			}
		});
	}
}

function get_current_tabTitle()
{
	var selected = $('#tabs').tabs('getSelected');
	var title  = selected.panel('options').title;

	return title;
}

function closeTab(title)
{
	$('#tabs').tabs('close',title);
}

function createFrame(url)
{
	var s = "<iframe name='mainFrame' frameborder='0' scrolling='yes' src='"+url+"' style='width:100%;height:100%;'></iframe>";
	return s;
}

//监听消息
function monitor_notice(){
	$.ajax({
		url:'index.php?c=notice&m=monitor_notice',
		dataType:'json',
		cache:false,
		success: function(responce){
			if(responce && responce['error'] == 0){
				var sender = '';
				switch(responce['type'])
				{
					case 'message':
					if(responce['reply_user_id'])
					sender = responce['sender']+" 【<span class='reply' onclick='msg_replay("+responce['reply_user_id']+")'>回复</span>】";
					break;
					case 'remind':
					sender = "提醒时间到 【<span class='reply' onclick='parent._show_remind("+responce['mark']+")'>处理</span>】";
					break;
					case 'announce':
					sender = "公告消息 【<span class='reply' onclick=addTab('查看公告','index.php?c=announcement&m=announcement_view&anns_id="+responce['mark']+"','menu_icon')>查看</span>】";
					break;
					case 'system':
					sender = '系统消息';
					break;
					default:
					return;
				}

				$.messager.show({
					title:sender,
					msg:responce['content'],
					showType:'show',
					height:150,
					timeout:0
				});
			}
			monitor_notice();
		},
		error:function(XMLHttpRequest, textStatus){
			setTimeout(function(){
				monitor_notice();
			},2000);

		}
	});
}

//消息回复
function msg_replay(reply_user_id)
{

	$('#message_panel').window({
		href:'index.php?c=message&m=message_panel&selected_id='+reply_user_id,
		width:490,
		height:390,
		title:'发消息',
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		shadow:false,
		cache:false
	});
}

//按钮状态修改 1启用，2禁用 3激活
function btn_state_change(name,type)
{
	var img = name;
	$.each(busy, function(index, id) {
		if(name=='busy'+id)
		{
			img = 'busy';
		}
	});
    if(img=='transque')
    {
        img = 'autocall';
    }
	if($('#'+name).val() == '恢复' || $('#'+name).val() == '退出AC')
	{
		img = 'un'+name;
	}
	if(type == BUTTON_ON)
	{
		$('#'+name).attr('disabled',false);
		$('#'+name).css('cursor','hand');
		$('#'+name).css('background','url(./image/btn_'+img+'_e.gif)');
	}
	else if(type == BUTTON_OFF)
	{
		$('#'+name).attr('disabled',true);
		$('#'+name).css('cursor','default');
		$('#'+name).css('background','url(./image/btn_'+img+'_d.gif)');
	}
	else if(type == MOUSE_ON)
	{
		$('#'+name).css('background','url(./image/btn_'+img+'_on.gif)');
	}
}

//座席状态
function callSeatState(responce)
{
	$('#seat_state').text(responce);
}

//队列状态
function callQueueState(responce)
{
	$('#queue_state').text(responce);
}

//提醒弹出框
function _show_remind(rmd_id)
{
	$('#view_remind').window({
		title: '我的提醒',
		href:'index.php?c=remind&m=view_remind_data&rmd_id='+rmd_id,
		iconCls: 'icon-search',
		top:300,
		closed: false,
		height: 200,
		width:550,
		collapsible:false,
		minimizable:false,
		cache:false
	});
}

function _dial_panel(phone_num,caller,chan)
{
	//外呼弹屏，1是 2否
	$('#dail_panel').window({
		title:'外呼',
		href:'index.php?c=phone_control&m=callouter_phone_control&phone_num='+phone_num+'&outChan='+chan+'&outCaller='+caller,
		width:280,
		top:180,
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		shadow:false,
		cache:false
	});
}

//设置默认的主叫和队列
function _set_default_caller_que()
{
	var _callers =  wincall.fn_get_caller();
	if(_callers.length != 0)
	default_caller= _callers[0];

	var _ques =  wincall.fn_get_que();
	default_que = _ques[0];
}

function _close_ring_panel()
{
	try
	{
		$('#ring_panel').dialog('close');
	}
	catch(e){}
}

function set_caller_cookie()
{
	var cookie_caller = $('#pc_caller').val();
	set_cookie('est_caller',cookie_caller);
}

function getFlashObject(movieName)
{
	if (window.document[movieName])
	{
		return window.document[movieName];
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1)
	{
		if (document.embeds && document.embeds[movieName])
		return document.embeds[movieName];
	}
	else
	{
		return document.getElementById(movieName);
	}
}

/**
*外呼函数
*@phone_num 外呼号码
*@outCaller 外呼主叫号码
*@_chan 外呼通道
**/
function _dial(phone_num)
{
	if(!arguments[1])
	var _caller = '';
	else
	var _caller = arguments[1];

	if(!arguments[2])
	var _chan = user_channel;
	else
	var _chan = arguments[2];

	var logstate = wincall.logState();
	if( !logstate)
	{
		$.messager.alert('错误','坐席未签入','error');
		$('#login').click();
		return;
	}

	if(user_display_dialpanel==1)
	{
		_dial_panel(phone_num,_caller,_chan);
		return;
	}
	else
	{
		if(phone_num == ''){
			return;
		}

		if(_caller == '')
		{
			if(user_outcaller_type == '0')
			{
				_caller= default_caller;
			}
			else
			{
				_caller = user_outcaller_num;
			}

		}
		var _que = default_que;
		wincall.fn_dialouter(phone_num,_caller,_que,_chan);
	}
}


/*来电归属地*/
function _set_number_loc(number)
{
	$('#caller_loc').text('');
	$('#calling_loc').text('');

	$.ajax({
		type:'POST',
		url: 'index.php?c=phone_control&m=phone_area_info',
		data: {'number':number},
		dataType: 'json',
		success: function(responce){
			if(responce['error'] === 0)
			{
				if(wincall.fn_getParam('CallId'))
				{
					if(power_phone_view != 1)
					{
						$('#caller_loc').text(hidden_part_number(number)+' '+responce['city']);
					}
					else
					{
						$('#caller_loc').text(number+' '+responce['city']);
					}
					$('#calling_loc').text(responce['city']);
				}
			}
		}
	});
}

function _clear_number_loc(number)
{
	$('#caller_loc').text('')
}

//发消息
function message_panel_window()
{
	$('#message_panel').window({
		href:'index.php?c=message&m=message_panel',
		width:490,
		height:390,
		title:'发消息',
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false
	});
}
//添加提醒
function set_remind_window()
{
	$('#set_remind').window({
		href:"index.php?c=remind&m=new_remind&rmd_param_char="+encodeURIComponent("我的提醒"),
		width:520,
		title:"加入提醒",
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		cache:false
	});
}
//拨号盘
function keypad_panel_window()
{
	$('#keypad_panel').window({
		href:'index.php?c=phone_control&m=keypad_phone_control',
		width:200,
		height:180,
		top:265,
		title:'拨号盘',
		iconCls:'icon-text',
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:true,
		shadow:false,
		modal:false
	});
}

var callclock = null;
var time_tmp = 0;
var minute;
var second;
//开始通话计时
function _start_clock()
{
	_stop_clock();
	callclock = setInterval(function (){
		time_tmp++;
		second = time_tmp%60;
		minute = Math.floor(time_tmp/60);
		if(minute < 10){minute = '0'+minute;}
		if(second < 10){second = '0'+second;}
		$('#seat_state').text(minute+':'+second);
	},1000);
}
//停止通话计时
function _stop_clock()
{
	if(callclock)
	{
		clearInterval(callclock);
		time_tmp = 0;
	}
}

if(user_phone_type == 'softphone' ||  user_ol_model == OLMODEL_AUTO)
{
	var _ring_panel_dialog = function(title)
	{
		$('#ring_panel').dialog({
			title:title,
			width:300,
			height:150,
			buttons:[
			{
				iconCls:'icon-answer',
				text:'接听',
				handler:function(){
					_close_ring_panel();
					wincall.fn_answer();
				}
			},
			{
				iconCls:'icon-hangup',
				text:'拒接',
				handler:function(){
					_close_ring_panel();
					wincall.fn_hangup();
				}
			}
			]
		});
	}
}
else
{
	var _ring_panel_dialog = function(title)
	{
		$('#ring_panel').dialog({
			title:title,
			width:300,
			height:150,
			buttons:[
			{
				iconCls:'icon-hangup',
				text:'拒接',
				handler:function(){
					_close_ring_panel();
					wincall.fn_hangup();
				}
			}
			]
		});
	}
}

function transque_page()
{
    //转技能组
    $('#transque_panel').window({
        title:'转技能组',
        href:'index.php?c=phone_control&m=get_one_other_que',
        width:280,
        top:180,
        collapsible:false,
        minimizable:false,
        maximizable:false,
        resizable:false,
        shadow:false,
        cache:false
    });
}