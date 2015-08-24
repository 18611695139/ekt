<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统提示信息
 *
 * @access      public
 * @param       string $msg_detail 消息内容
 * @param       int $msg_type 消息类型， 0消息，1错误，2询问
 * @param       array $links 可选的链接
 * @param       bool $auto_redirect 是否需要自动跳转
 */
function sys_msg($msg_detail = '', $msg_type = 0, $links = array(), $auto_redirect = TRUE)
{
    $CI = & get_instance();
    if (count($links) == 0) {
        $links[0]['text'] = '';
        $links[0]['href'] = '';
        $auto_redirect = FALSE;
    }

    $CI->smarty->assign('ur_here', '系统消息');
    $CI->smarty->assign('msg_detail', $msg_detail);
    $CI->smarty->assign('msg_type', $msg_type);
    $CI->smarty->assign('links', $links);
    $CI->smarty->assign('default_url', $links[0]['href']);
    $CI->smarty->assign('auto_redirect', $auto_redirect);

    $CI->smarty->display('message.htm');

    exit;
}

/**
 * log_action()
 *
 * @param int $type 操作类型 1登录 2登出 3添加 4更新 5删除
 * @param string $content 记录操作
 */
function log_action($type, $content)
{
    $CI = & get_instance();
    $user_id = $CI->session->userdata('user_id');
    $user_num = $CI->session->userdata('user_num');
    $user_name = $CI->session->userdata('user_name');

    /* 不记录系统管理员的操作信息 */
    if ($CI->session->userdata('login_type') != 3) {
        $arr = array(
            'user_id' => $user_id,
            'user_num' => "用户:" . $user_num . "[" . $user_name . "]",
            'action' => $type,
            'content' => $content,
            'acttime' => time(),
            'actip' => real_ip()
        );
        //		$CI->db->insert('ec_logs',$arr);
    }
}

/**
 * 将通过表单保存过来的年月日变量合成为"2004-05-10"的格式。
 *
 * 此函数适用于通过smarty函数html_select_date生成的下拉日期。
 *
 * @param  string $prefix 年月日变量的共同的前缀。
 * @return date                日期变量。
 */
function sys_joindate($prefix)
{
    /* 返回年-月-日的日期格式 */
    $year = empty($_POST[$prefix . 'Year']) ? '0' : $_POST[$prefix . 'Year'];
    $month = empty($_POST[$prefix . 'Month']) ? '0' : $_POST[$prefix . 'Month'];
    $day = empty($_POST[$prefix . 'Day']) ? '0' : $_POST[$prefix . 'Day'];

    return $year . '-' . $month . '-' . $day;
}

/**
 * 判断管理员对某一个操作是否有权限。
 *
 * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
 * @param     string $priv_str 操作对应的priv_str
 * @param bool $msg_output
 * @return bool 没有权限直接退出
 */
function admin_priv($priv_str = '', $msg_output = true)
{
    $CI = & get_instance();
    if (!$CI->session->userdata('user_id')) {
        $links[0]['text'] = '登录';
        $links[0]['href'] = 'index.php?c=login&m=signout';
        $links[0]['target'] = '_parent';
        sys_msg("登录超时，请重新登录", 1, $links);
    } else {
        // 判断有没有此权限
        if ($priv_str) {
            $action_list = $CI->session->userdata('role_action_list');
            if (!preg_match('/,*' . $priv_str . ',*/', $action_list) && $action_list != 'all') {
                if ($msg_output) {
                    sys_msg("您没有此权限");
                } else {
                    exit();
                }
            }
        }
        return true;
    }
}

/**
 * 检查管理员权限
 *
 * @access  public
 * @param   string $authz
 * @return  boolean
 */
function check_authz($authz)
{
    $CI = & get_instance();
    $action_list = $CI->session->userdata('role_action_list');
    return (preg_match('/^(.*,)?' . $authz . ',/i', $action_list) || $action_list == 'all');
}

/**
 * 数据权限限制
 *
 * @return string where条件
 */
function data_permission()
{
    //检索条件
    $wheres = "";
    $CI = & get_instance();
    $role_type = $CI->session->userdata('role_type');
    $user_id = $CI->session->userdata("user_id");
    $dept_id = $CI->session->userdata('dept_id');

    switch ($role_type) {
        case DATA_DEPARTMENT: //部门数据权限
            $CI->load->model("department_model");
            //得到所有的子部门的ID(包含自身)
            $dept_children = $CI->department_model->get_department_children_ids($dept_id);
            if (!empty($dept_children)) {
                $dept_children = implode(",", $dept_children);
                $wheres = "dept_id IN ($dept_children)";
            } else {
                $wheres = "dept_id = $dept_id";
            }
            break;
        case DATA_PERSON:
            $wheres = "user_id = '$user_id'";
            break;
        default:
            $wheres = "user_id = '$user_id'";

    }
    return $wheres;
}

/**
 * 检查管理员权限，返回JSON格式数剧
 *
 * @access  public
 * @param   string $authz
 * @return  void
 */
function check_authz_json($authz)
{
    if (!check_authz($authz)) {
        make_json_error("您没有此权限");
    }
}

/**
 * 创建一个JSON格式的数据
 *
 * @access  public
 * @param   string $content
 * @param   integer $error
 * @param   string $message
 * @param   array $append
 * @return  void
 */
function make_json_response($content = '', $error = 0, $message = '', $append = array())
{
    $CI = & get_instance();
    $CI->load->library('Json');

    $res = array('error' => $error, 'message' => $message, 'content' => $content);
    if (!empty($append) && is_array($append)) {
        foreach ($append AS $key => $val) {
            $res[$key] = $val;
        }
    }
    $val = $CI->json->encode($res);
    exit($val);
}

/**
 * 创建一个JSON格式的正确信息
 *
 * @access  public
 * @param string $content
 * @param string $message
 * @param array $append
 * @return  void
 */
function make_json_result($content = '', $message = '', $append = array())
{
    make_json_response($content, 0, $message, $append);
}

/**
 * 创建一个JSON格式的错误信息
 *
 * @access  public
 * @param string $msg
 * @param   array $append
 * @return  void
 */
function make_json_error($msg = '', $append = array())
{
    make_json_response('', 1, $msg, $append);
}


/**
 * 返回简单sql结果
 *
 * @param bool $result
 * @exception true make_json_result false make_json_error
 * @return string
 */
function make_simple_response($result = TRUE)
{
    if ($result) {
        make_json_result($result);
    } else {
        make_json_error();
    }
}

/**
 * 把电话号码返回成 3 -4 -4格式
 * @param string $phone 电话号
 */
function phone_formate($phone)
{
    $firt_str = substr($phone, 0, 3);
    $second_str = substr($phone, 3, 4);
    $third_str = substr($phone, 7);
    return $firt_str . " - " . $second_str . " - " . $third_str;
}

/**
 * 得到随机字符串
 * @param $length
 * @return string*
 */
function get_rand_str($length)
{
    $hash = "";
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 得到列表参数
 * @return array page limit sort order
 */
function get_list_param()
{
    $CI = & get_instance();
    $page = $CI->input->post("page");
    $page = empty($page) ? 1 : $page;
    $limit = $CI->input->post("rows");
    $limit = empty($limit) ? 10 : $limit;
    $sort = $CI->input->post("sort");
    $sort = empty($sort) ? NULL : $sort;
    $order = $CI->input->post("order");
    $order = empty($order) ? NULL : $order;

    return array($page, $limit, $sort, $order);
}

/**
 * 计算列表开始位置
 *
 * @param int $total
 * @param int $page
 * @param int $limit
 * @return int
 */
function get_list_start($total, $page, $limit)
{
    $total_pages = ceil($total / $limit);
    $page = $page > $total_pages ? $total_pages : $page;
    $start = $limit * $page - $limit;
    $start = $start > 0 ? $start : 0;
    return $start;
}


/**
 * 对象转为数组
 *
 * @param object $obj
 * @return array
 */
function object_to_array($obj)
{
    $arr = array();
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($_arr as $key => $val) {
        $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}

/**
 * replace_illegal_string()
 * 处理输入中的特殊字符
 * @param mixed $string
 * @return bool|string
 */
function replace_illegal_string($string)
{
    if (!$string) {
        return;
    }

    //字符串
    if (is_string($string)) {
        $string = str_replace("&nbsp;", " ", $string);
        $string = str_replace(",", "，", $string);
        $string = str_replace("\r\n", " ", $string);
        $string = str_replace("\r", " ", $string);
        $string = str_replace("\n", " ", $string);
        $string = trim(addslashes($string));
    } //一维数组
    elseif (is_array($string)) {
        foreach ($string AS $k => $v) {
            $v = str_replace("&nbsp;", " ", $v);
            $v = str_replace(",", "，", $v);
            $v = str_replace("\r\n", " ", $v);
            $v = str_replace("\r", " ", $v);
            $v = str_replace("\n", " ", $v);
            $string[$k] = trim(addslashes($v));
        }
    }
    return $string;
}


/**
 * numeric_to_str()
 *
 * @param string $array
 * @return string
 */
function numeric_to_str($array)
{
    $array = is_numeric($array) ? '#' . $array : trim($array);
    return $array;
}

/**
 * easy_iconv()
 *
 * @param string $in_encode
 * @param string $out_encode
 * @param string $string
 * @return string|boolean
 */
function easy_iconv($in_encode, $out_encode, $string)
{
    if (!$in_encode || !$out_encode || !$string) {
        return FALSE;
    }

    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($string, $out_encode, $in_encode);
    } else {
        if (strtoupper($out_encode) == 'GBK') {
            $out_encode .= '//IGNORE';
        }
        return iconv($in_encode, $out_encode, $string);
    }
}

/***************************************************************************
 *
 * ------------------------------
 * Date : Nov 7, 2006
 * Copyright : 修改自网络代码,版权归原作者所有
 * Mail :
 * Desc. : 拼音转换
 * History :
 * Date :
 * Author :
 * Modif. :
 * Usage Example :
 * echo Pinyin('这是小超的网站，欢迎访问http://www.163.com'); //默认是gb2312编码
 * echo Pinyin('第二个参数随意设置',2); //第二个参数随意设置即为utf8-编
 *
 * $_String:需要转的汉字
 * $_Code： gb2312(默认) ，utf-8
 * $isInitial : false(默认转换为全拼音)    true(转换为拼音首字母)
 ***************************************************************************/

function pinyin($_String, $isInitial = false, $_Code = 'utf-8')
{
    $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
        "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
        "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
        "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
        "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
        "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
        "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
        "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
        "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
        "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
        "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
        "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
        "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
        "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
        "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
        "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

    $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
        "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
        "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
        "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
        "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
        "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
        "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
        "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
        "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
        "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
        "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
        "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
        "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
        "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
        "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
        "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
        "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
        "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
        "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
        "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
        "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
        "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
        "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
        "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
        "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
        "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
        "|-10270|-10262|-10260|-10256|-10254";
    $_TDataKey = explode('|', $_DataKey);
    $_TDataValue = explode('|', $_DataValue);

    $_Data = (PHP_VERSION >= '5.0') ? array_combine($_TDataKey, $_TDataValue) : _Array_Combine($_TDataKey, $_TDataValue);
    arsort($_Data);
    reset($_Data);

    if ($_Code != 'gb2312') {
        $_String = _U2_Utf8_Gb($_String);
    }
    $_Res = '';
    for ($i = 0; $i < strlen($_String); $i++) {
        $_P = ord(substr($_String, $i, 1));
        if ($_P > 160) {
            $_Q = ord(substr($_String, ++$i, 1));
            $_P = $_P * 256 + $_Q - 65536;
        }
        $_Res .= _Pinyin($_P, $_Data, $isInitial);
    }
    return preg_replace("/[^a-z0-9]*/", '', $_Res);
}

function _Pinyin($_Num, $_Data, $isInitial)
{
    if ($_Num > 0 && $_Num < 160) {
        return chr($_Num);
    } elseif ($_Num < -20319 || $_Num > -10247) {
        return '';
    } else {
        foreach ($_Data as $k => $v) {
            if ($v <= $_Num) break;
        }
        if ($isInitial)
            $k = substr($k, 0, 1);
        //是否只显示首写
        return $k;
    }
}

function _U2_Utf8_Gb($_C)
{
    $_String = '';
    if ($_C < 0x80) {
        $_String .= $_C;
    } elseif ($_C < 0x800) {
        $_String .= chr(0xC0 | $_C >> 6);
        $_String .= chr(0x80 | $_C & 0x3F);
    } elseif ($_C < 0x10000) {
        $_String .= chr(0xE0 | $_C >> 12);
        $_String .= chr(0x80 | $_C >> 6 & 0x3F);
        $_String .= chr(0x80 | $_C & 0x3F);
    } elseif ($_C < 0x200000) {
        $_String .= chr(0xF0 | $_C >> 18);
        $_String .= chr(0x80 | $_C >> 12 & 0x3F);
        $_String .= chr(0x80 | $_C >> 6 & 0x3F);
        $_String .= chr(0x80 | $_C & 0x3F);
    }
    return easy_iconv('UTF-8', 'GB2312', $_String);
}

function _Array_Combine($_Arr1, $_Arr2)
{
    for ($i = 0; $i < count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
    return $_Res;
}