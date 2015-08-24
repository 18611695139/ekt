/**
* EasyTone 公共函数和初始化函数库
* ============================================================================
* 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
* 网站地址: http://www.chinawintel.com；
* ============================================================================
* $Id    : common.js
* $Author: ZT
* $time  : Wed Oct 14 14:59:41 CST 2009
* @version 1.1
*/

function maxwin()
{
	//判断浏览器是否支持window.screen判断浏览器是否支持screen
	if (window.screen)
	{
		var myw = screen.availWidth; //定义一个myw，接受到当前全屏的宽
		var myh = screen.availHeight;  //定义一个myw，接受到当前全屏的高

		window.moveTo(0, 0);     //把window放在左上脚
		window.resizeTo(myw, myh);   //把当前窗体的长宽跳转为myw和myh
	}
}

/**
* 自定义 getheight 函数，用于取得当前浏览器的高度
*
**/
function get_height()
{
	//根据浏览器的不同，取得页面高度
	if (!document.all)
	{
		height = window.innerHeight;
	}else
	{
		height = document.body.clientHeight;
	}

	return height;
}


/**
* 自定义 getheight 函数，用于取得当前浏览器的宽度
**/
function get_width()
{
	//根据浏览器的不同，取得页面宽度
	if (!document.all)
	{
		width = window.innerWidth;
	}else
	{
		width = document.body.clientWidth;
	}
	return width;
}


/**
* 写cookies函数
**/
function set_cookie(name,value)
{
	var Days = arguments[2];
	if(Days>0)
	{
		var exp  = new Date();  //new Date("December 31, 9998");
		exp.setTime(exp.getTime() + Days*24*60*60*1000);
		document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
	}
	else
	{
		document.cookie = name + "="+ escape (value);
	}
}

/**
* 取cookies函数
**/
function get_cookie(name)
{
	var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
	if(arr != null) return unescape(arr[2]);
	return null;
}
/**
* 删除cookie
**/
function del_cookie(name)
{
	var Days = -1; //此 cookie 将被保存 30 天
	var exp  = new Date();  //new Date("December 31, 9998");
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ '0' + ";expires=" + exp.toGMTString();
}
/**
* 写列表显示行数cookie
**/
function set_list_rows_cookie(row)
{
	if(row >= 10 && row !== get_cookie('list_rows'))
	{
		set_cookie('list_rows',row);
	}
}
/**
* 获取列表显示行数cookie
**/
function get_list_rows_cookie()
{
	var row = get_cookie('list_rows');
	if(row == null || row < 10)
	{
		set_list_rows_cookie(10);
		row = 10;
	}
	return row;
}
/**
* 自定义 header 函数，用于过滤可能出现的安全隐患
**/
function est_header(string)
{
	if(string != string.replace(/location:/i,''))
	{
		var url = string.replace(/location:/i,'');
		self.location = url;
	}
}


/*文件下载*/
function download(src)
{
	window.open("./includes/cls_download.php?filename="+src);
}

//数据分类处设置颜色
function set_color(id)
{
	$('#searchForm>a').css('color','#335b64');
	$('#'+id).css('color','red');
}
function list_search(obj)
{
	console.log($(obj).attr('name'))
	$('a[name='+$(obj).attr('name')+']').css('color','#335b64');
	$(obj).css('color','red');
}

//取得当前时间
function now_time(){
	var now= new Date();
	var year=now.getYear();
	var month=now.getMonth()+1;
	var day=now.getDate();
	var hour=now.getHours();
	var minute=now.getMinutes();
	var second=now.getSeconds();
	var nowdate=year+"-"+month+"-"+day+" "+hour+":"+minute+":"+second;
	return nowdate;
}

/**
* 时间转换函数 - 将时间戳转化为具体的时分秒
* value 初始时间戳
*/
function timeFormate(value)
{
	if(value){
		var Day=Math.floor(value/86400);
		if(Day) Day=Day+'天';
		else Day='';
		var Hours=Math.floor(value%86400/3600);
		if(Hours) Hours=Hours+':';
		else Hours='';
		var Minutes=Math.floor(value%86400/60);
		if(Minutes) Minutes=Math.floor(Minutes%60)+':';
		else Minutes='';
		var Seconds = Math.floor(value%86400%60);
		var last_time=Hours+Minutes+Seconds;
		return last_time;
	}else{
		return '';
	}
}

//JS 替换指定位置的字符，返回新字符串
function substr_replace(str, replace, start, length)
{
	if (start < 0) { // start position in str        start = start + str.length;
	}
	length = length !== undefined ? length : str.length;
	if (length < 0) {
		length = length + str.length - start;    }
		return str.slice(0, start) + replace.substr(0, length) + replace.slice(length) + str.slice(start + length);
}

//就是字符串替换函数
function replace(str,a,b)
{
	var i;
	var s2 = str;

	while(s2.indexOf(a)>0)
	{
		i = s2.indexOf(a);
		s2 = s2.substring(0, i) + b
		+ s2.substring(i + 2, s2.length);
	}
	return s2;
}
/**
* 输出对象内容
**/
function alert_obj(obj)
{
	var result ="";
	$.each(obj, function(property,value) {
		//针对对象
		result+=("属性:" + property + " 值:" + value + "\r\n");
	});
	alert(result);
}

/**
*判断对象是否为空
*/
function empty_obj(obj)
{
	var result = true;
	$.each(obj, function(property,value) {
		//针对对象
		if(  value )
		{
			result = false;
			return result;
		}
	});

	return result;
}

/**
* json 语句 转成 url
*/
function json2url(json){
	var tmps = [];
	var urlstr = "";
	$.each(json, function(property,value) {
		tmps.push(property + '=' + value);
	});
	return tmps.join('&');
}
/**
* json语句转成字符串
*/
function json2str(o)
{
	var arr = [];
	var fmt = function(s) {
		if(typeof s == 'function')
		return;
		else if (typeof s == 'object' && s != null)
		return json2str(s);
		else
		return /^(string|number|undefined)$/.test(typeof s) ? '"' + s + '"' : s;
	}
	for (var i in o)
	{
		if(i == 'source')
		{
			arr.push('"' + i + '":' + o[i].index);
		}
		else if(i == 'destination')
		{
			arr.push('"' + i + '":' + o[i].index);
		}
		else if(typeof o[i] != 'function')
		{
			arr.push('"' + i + '":' + fmt(o[i]));
		}
	}
	return '{' + arr.join(',') + '}';
}

/**
*  下载录音
*/
function fn_download(callid,ag_id){
    if (ag_id) {
        var url = 'index.php?c=callrecords&m=get_record_url&callid='+callid+'&ag_id='+ag_id;
    } else {
        var url = 'index.php?c=callrecords&m=get_record_url&callid='+callid;
    }

    est_header("location:"+url);
}

/**
* 得到列表高度以适应窗口
*
**/
function get_list_height_fit_window()
{
	var _height = 0;
	var search_panel_id = arguments[0];
	if(!dom_if_exist(search_panel_id))
	{
		search_panel_id = '';
	}
	if(search_panel_id == '')
	{
		_height = $(window).height() -50;
	}
	else
	{
		_height = $(window).height()-$('#'+search_panel_id).height() -50;
	}
	if(_height < 350)
	{
		_height = 350;
	}
	return _height;
}

/**
*  发短信
*phone_num   号码
*from_file   字段： 如果存在from_file 直接从from_file 中去取得号码，如果from_file 取得的号码有*号 那么用phone_num
*/
function sys_send_sms(phone_num)
{
	if(arguments[1])
	{
		var from_file = arguments[1];
		var tmp_phone_num = $('#'+from_file).val();
		if(tmp_phone_num &&  !exist_star(tmp_phone_num) )
		{
			phone_num = tmp_phone_num;
		}
	}

	if(!dom_if_exist('sys_send_sms_panel'))
	{
		$("<div id='sys_send_sms_panel'></div>").appendTo("body");
	}

	$('#sys_send_sms_panel').window({
		href:"index.php?c=sms&m=send_sms&receiver_phone="+phone_num,
		top:80,
		width:620,
		title:"发短信",
		collapsible:false,
		minimizable:false,
		maximizable:false,
		resizable:false,
		modal:true,
		cache:false
	});
}

/**
* 打电话
* phone_num 号码
* from_file 字段： 如果存在from_file 直接从from_file 中去取得号码，如果from_file 取得的号码有*号 那么用phone_num
**/
function sys_dial_num(phone_num)
{
	if(arguments[1])
	{
		var from_file = arguments[1];
		var tmp_phone_num = $('#'+from_file).val();
		if(tmp_phone_num )
		{
			//号码中带有*号
			if( exist_star(tmp_phone_num) )
			{
				if( phone_num )
				{
					var diff      = parseInt(tmp_phone_num.length) - parseInt(phone_num.length);
					var start_str = tmp_phone_num.substr(0,1);
					if( diff == 1 && start_str == "0" )
					{
						phone_num = "0"+phone_num;
					}
				}
			}
			else
			{
				phone_num = tmp_phone_num;
			}
		}
	}

	if(phone_num != '')
	{
		parent._dial(phone_num);
	}
}

/**
* 判断字符串中是否存在*号， 存在返回 TRUE; 否则返回 FALSE
*/
function exist_star(str)
{
	var result = false;
	if( str )
	{
		//有*号
		if( str.indexOf('*') > -1 )
		{
			result = true;
		}
	}

	return result;
}

/**
* 隐藏部分号码 用星号代替
*
**/
function hidden_part_number(str)
{
	if(str.length > 7)
	{
		//str = substr_replace(str, "****",7, 4); //隐藏后4位
		str = substr_replace(str, "****",3, 4); //隐藏中间4位
	}
	return str;
}

/**
* 元素是否存在 存在ture 不存在 false
*
**/
function dom_if_exist(dom_id)
{
	if($('#'+dom_id).size() == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**
*数据比较，得到修改过的数据
*data 为对象或者数组
*返回值为对象
**/
function data_comparison(data_old,data_new)
{
	//发生改变的数据
	var changed_data = {};

	$.each(data_new,function(field,value){
		if(value != 'undefined' && data_old[field] != value )
		{
			changed_data[field] = value;
		}
	});

	return changed_data;
}

/**
* 计算年龄周岁
* @birthday 出生日期 2001-02-02
* @return_month_when_baby 如果是婴儿（1周岁以内）返回月份 0.X
* @return int age
**/
function get_age(birthday)
{
	var return_month_when_baby = false;
	if(arguments[1])
	{
		return_month_when_baby = true;
	}

	var age;
	var birthday_arr = birthday.split("-");
	var birthYear = birthday_arr[0];
	var birthMonth = birthday_arr[1];
	var birthDay = birthday_arr[2];

	d = new Date();
	var nowYear = d.getFullYear();
	var nowMonth = d.getMonth() + 1;
	var nowDay = d.getDate();

	if(nowYear == birthYear)//同年
	{
		age = 0;
	}
	else
	{
		var ageDiff = nowYear - birthYear ; //年之差
		if(ageDiff > 0)
		{
			if(nowMonth == birthMonth)
			{
				var dayDiff = nowDay - birthDay;//日之差
				if(dayDiff < 0)
				{
					age = ageDiff - 1;
				}
				else
				{
					age = ageDiff ;
				}
			}
			else
			{
				var monthDiff = nowMonth - birthMonth;//月之差
				if(monthDiff < 0)
				{
					age = ageDiff - 1;
				}
				else
				{
					age = ageDiff ;
				}
			}
		}
		else
		{
			age = -1;//返回-1 表示出生日期输入错误 晚于今天
		}
	}

	if(age == 0 && return_month_when_baby)
	{
		age_month = nowMonth - birthMonth
		if(nowYear - birthYear == 1)
		{
			age_month = age_month + 12;
		}

		if(nowMonth - birthMonth >0 && nowDay - birthDay < 0 )
		{
			age_month = age_month -1;
		}
		age = '0.'+age_month;
	}
	return age;//返回周岁年龄
}

//获取省市信息
function change_regions_type(id,next_field,deep)
{
	var tmp_p_id   = $('#'+id+'').find('option:selected').val();//选中项的ID

	if( tmp_p_id > 0 )
	{
		$.ajax({
			type:'POST',
			url: "index.php?c=regions&m=get_regions_type",
			data:{"parent_id":tmp_p_id,"region_type":deep},
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					$("#"+next_field+"").empty();
					$("#"+next_field+"").append("<option value='0'>--选择市--</option>");  //添加一项option
					$.each(responce['content'],function(id,value){
						$("#"+next_field+"").append("<option value='"+id+"'>"+value+"</option>");  //添加一项option
					});
				}
				else
				{
					$.messager.alert('错误',"<br>".responce['message'],'error');
				}
			}
		});
	}
	else
	{
		$("#"+next_field+"").empty();
		$("#"+next_field+"").append("<option value='0'>--选择市--</option>");  //添加一项option
	}
}

//级联 - 获取下一级选项信息
function change_comfirm_jl(id,next_field,deep,field_id)
{
	if(arguments[4])
	{
		$("#"+arguments[4]+"").empty();
		$("#"+arguments[4]+"").append("<option value=''>--请选择--</option>");  //添加一项option
	}
	var tmp_p_id   = $('#'+id+'').find('option:selected').val();//选中项的ID

	if( tmp_p_id > 0 )
	{
		$.ajax({
			type:'POST',
			url: "index.php?c=field_confirm&m=get_jl_options",
			data:{"parent_id":tmp_p_id,"type":deep,"field_id":field_id},
			dataType:"json",
			success: function(responce){
				if(responce['error']=='0')
				{
					$("#"+next_field+"").empty();
					$("#"+next_field+"").append("<option value=''>--请选择--</option>");  //添加一项option
					$.each(responce['content'],function(id,value){
						$("#"+next_field+"").append("<option value='"+id+"'>"+value+"</option>");  //添加一项option
					});
				}
				else
				{
					$.messager.alert('错误',"<br>".responce['message'],'error');
				}
			}
		});
	}
	else
	{
		$("#"+next_field+"").empty();
		$("#"+next_field+"").append("<option value=''>--请选择--</option>");  //添加一项option
	}
}

//级联 - 获取下一级
function get_comfirm_jl_options(deep,field_name,series,field_id)
{
	var jl_field = jl_field_type[field_id];
	var p_id = $('select[name="'+field_name+'_'+deep+'"]').val();
	var next_field = field_name+'_'+(parseInt(deep)+1);
	$('#'+next_field).html('');
	if(series==3 && deep==1)
	{
		$('#'+field_name+'_'+(parseInt(deep)+2)).html('');
	}

	if((series==2)||(series==3 && deep==2))
	{
		if(jl_field[p_id]!=undefined && jl_field[p_id]==1)
		{
			$('#'+next_field).html("<input type='text' name='"+next_field+"' value='' confirm_field='true'/>");
		}
		else
		{
			if( p_id > 0 )
			{
				get_jl_options(p_id,field_name,(parseInt(deep)+1),field_id,series);
			}
		}
	}
	else
	{
		get_jl_options(p_id,field_name,(parseInt(deep)+1),field_id,series);
	}
}

/*获取级联选项信息*/
function get_jl_options(p_id,field_name,deep,field_id,series)
{
	var next_field = field_name+'_'+deep;
	$.ajax({
		type:'POST',
		url: "index.php?c=field_confirm&m=get_jl_options",
		data:{"parent_id":p_id,"type":deep,"field_id":field_id},
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				var str = '';
				$.each(responce['content'],function(id,value){
					str +=  '<option value="'+id+'">'+value+'</option>';
				});
				if(str.length!=0)
				{
					if(series==3&&deep==2)
					{
						$('#'+next_field).html("<select name='"+next_field+"' confirm_field='true' onchange=\"get_comfirm_jl_options(2,'"+field_name+"',"+series+",'"+field_id+"')\" >\
		  				<option value=''>--请选择--</option>"+str+"\
	     				</select>");
					}
					else
					{
						$('#'+next_field).html("<select name='"+next_field+"' confirm_field='true' >\
		  				<option value=''>--请选择--</option>"+str+"\
	     				</select>");
					}
				}
			}
			else
			{
				$.messager.alert('错误',"<br>".responce['message'],'error');
			}
		}
	});
}



/*用于异步加载js和css文件时的缓存*/
//$.ajaxSetup({cache: true });