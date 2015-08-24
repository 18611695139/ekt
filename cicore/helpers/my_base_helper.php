<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
	$str = trim($str);
	$strlength = strlen($str);

	if ($length == 0 || $length >= $strlength)
	{
		return $str;
	}
	elseif ($length < 0)
	{
		$length = $strlength + $length;
		if ($length < 0)
		{
			$length = $strlength;
		}
	}

	if (function_exists('mb_substr'))
	{
		$newstr = mb_substr($str, 0, $length, EC_CHARSET);
	}
	elseif (function_exists('iconv_substr'))
	{
		$newstr = iconv_substr($str, 0, $length, EC_CHARSET);
	}
	else
	{
		$newstr = substr($str, 0, $length);
	}

	if ($append && $str != $newstr)
	{
		$newstr .= '...';
	}

	return $newstr;
}

/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip()
{
	static $realip = NULL;

	if ($realip !== NULL)
	{
		return $realip;
	}

	if (isset($_SERVER))
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

			/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
			foreach ($arr AS $ip)
			{
				$ip = trim($ip);

				if ($ip != 'unknown')
				{
					$realip = $ip;

					break;
				}
			}
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			if (isset($_SERVER['REMOTE_ADDR']))
			{
				$realip = $_SERVER['REMOTE_ADDR'];
			}
			else
			{
				$realip = '0.0.0.0';
			}
		}
	}
	else
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
		{
			$realip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_CLIENT_IP'))
		{
			$realip = getenv('HTTP_CLIENT_IP');
		}
		else
		{
			$realip = getenv('REMOTE_ADDR');
		}
	}

	preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

	return $realip;
}

/**
 * 计算字符串的长度（汉字按照两个字符计算）
 *
 * @param   string      $str        字符串
 *
 * @return  int
 */
function str_len($str)
{
	$length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

	if ($length)
	{
		return strlen($str) - $length + intval($length / 3) * 2;
	}
	else
	{
		return strlen($str);
	}
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
	if (empty($value))
	{
		return $value;
	}
	else
	{
		if(is_object($value))
		{
			return addslashes_deep_obj($value);
		}
		else
		{
			return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
		}
	}
}

/**
 * 将对象成员变量或者数组的特殊字符进行转义
 *
 * @access   public
 * @param    mix        $obj      对象或者数组
 * @author   Xuan Yan
 *
 * @return   mix                  对象或者数组
 */
function addslashes_deep_obj($obj)
{
	if (is_object($obj) == true)
	{
		foreach ($obj AS $key => $val)
		{
			$obj->$key = addslashes_deep($val);
		}
	}
	else
	{
		$obj = addslashes_deep($obj);
	}

	return $obj;
}

/**
 * 文件或目录权限检查函数
 *
 * @access          public
 * @param           string  $file_path   文件路径
 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
 *
 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
 *                          返回值在二进制计数法中，四位由高到低分别代表
 *                          可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
 */
function file_mode_info($file_path)
{
	/* 如果不存在，则不可读、不可写、不可改 */
	if (!file_exists($file_path))
	{
		return false;
	}

	$mark = 0;

	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
	{
		/* 测试文件 */
		$test_file = $file_path . '/cf_test.txt';

		/* 如果是目录 */
		if (is_dir($file_path))
		{
			/* 检查目录是否可读 */
			$dir = @opendir($file_path);
			if ($dir === false)
			{
				return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
			}
			if (@readdir($dir) !== false)
			{
				$mark ^= 1; //目录可读 001，目录不可读 000
			}
			@closedir($dir);

			/* 检查目录是否可写 */
			$fp = @fopen($test_file, 'wb');
			if ($fp === false)
			{
				return $mark; //如果目录中的文件创建失败，返回不可写。
			}
			if (@fwrite($fp, 'directory access testing.') !== false)
			{
				$mark ^= 2; //目录可写可读011，目录可写不可读 010
			}
			@fclose($fp);

			@unlink($test_file);

			/* 检查目录是否可修改 */
			$fp = @fopen($test_file, 'ab+');
			if ($fp === false)
			{
				return $mark;
			}
			if (@fwrite($fp, "modify test.\r\n") !== false)
			{
				$mark ^= 4;
			}
			@fclose($fp);

			/* 检查目录下是否有执行rename()函数的权限 */
			if (@rename($test_file, $test_file) !== false)
			{
				$mark ^= 8;
			}
			@unlink($test_file);
		}
		/* 如果是文件 */
		elseif (is_file($file_path))
		{
			/* 以读方式打开 */
			$fp = @fopen($file_path, 'rb');
			if ($fp)
			{
				$mark ^= 1; //可读 001
			}
			@fclose($fp);

			/* 试着修改文件 */
			$fp = @fopen($file_path, 'ab+');
			if ($fp && @fwrite($fp, '') !== false)
			{
				$mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
			}
			@fclose($fp);

			/* 检查目录下是否有执行rename()函数的权限 */
			if (@rename($test_file, $test_file) !== false)
			{
				$mark ^= 8;
			}
		}
	}
	else
	{
		if (@is_readable($file_path))
		{
			$mark ^= 1;
		}

		if (@is_writable($file_path))
		{
			$mark ^= 14;
		}
	}

	return $mark;
}

/**
     * 获得服务器上的 GD 版本
     *
     * @access      public
     * @return      int         可能的值为0，1，2
     */
function gd_version()
{
	static $version = -1;

	if ($version >= 0)
	{
		return $version;
	}

	if (!extension_loaded('gd'))
	{
		$version = 0;
	}
	else
	{
		// 尝试使用gd_info函数
		if (PHP_VERSION >= '4.3')
		{
			if (function_exists('gd_info'))
			{
				$ver_info = gd_info();
				preg_match('/\d/', $ver_info['GD Version'], $match);
				$version = $match[0];
			}
			else
			{
				if (function_exists('imagecreatetruecolor'))
				{
					$version = 2;
				}
				elseif (function_exists('imagecreate'))
				{
					$version = 1;
				}
			}
		}
		else
		{
			if (preg_match('/phpinfo/', ini_get('disable_functions')))
			{
				/* 如果phpinfo被禁用，无法确定gd版本 */
				$version = 1;
			}
			else
			{
				// 使用phpinfo函数
				ob_start();
				phpinfo(8);
				$info = ob_get_contents();
				ob_end_clean();
				$info = stristr($info, 'gd version');
				preg_match('/\d/', $info, $match);
				$version = $match[0];
			}
		}
	}

	return $version;
}

/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder)
{
	$reval = false;

	if (!file_exists($folder))
	{
		/* 如果目录不存在则尝试创建该目录 */
		@umask(0);

		/* 将目录路径拆分成数组 */
		preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp[0][0] == '/') ? '/' : '';

		/* 遍历包含路径信息的数组 */
		foreach ($atmp[1] AS $val)
		{
			if ('' != $val)
			{
				$base .= $val;

				if ('..' == $val || '.' == $val)
				{
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';

					continue;
				}
			}
			else
			{
				continue;
			}

			$base .= '/';

			if (!file_exists($base))
			{
				/* 尝试创建目录，如果创建失败则继续循环 */
				if (@mkdir(rtrim($base, '/'), 0777))
				{
					@chmod($base, 0777);
					$reval = true;
				}
			}
		}
	}
	else
	{
		/* 路径已经存在。返回该路径是不是一个目录 */
		$reval = is_dir($folder);
	}

	clearstatcache();

	return $reval;
}

/**
 * 获得系统是否启用了 gzip
 *
 * @access  public
 *
 * @return  boolean
 */
function gzip_enabled()
{
	static $enabled_gzip = NULL;

	if ($enabled_gzip === NULL)
	{
		$enabled_gzip = function_exists('ob_gzhandler');
	}

	return $enabled_gzip;
}


/**
 *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function make_semiangle($str)
{
	$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
	'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
	'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
	'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
	'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
	'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
	'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
	'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
	'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
	'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
	'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
	'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
	'ｙ' => 'y', 'ｚ' => 'z',
	'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
	'】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
	'‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
	'》' => '>',
	'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
	'：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
	'；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
	'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
	'　' => ' ');

	return strtr($str, $arr);
}

/**
 * 检查文件类型
 *
 * @access      public
 * @param       string      filename            文件名
 * @param       string      realname            真实文件名
 * @param       string      limit_ext_types     允许的文件类型
 * @return      string
 */
function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
	if ($realname)
	{
		$extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
	}
	else
	{
		$extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
	}

	if ($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false)
	{
		return '';
	}

	$str = $format = '';

	$file = @fopen($filename, 'rb');
	if ($file)
	{
		$str = @fread($file, 0x400); // 读取前 1024 个字节
		@fclose($file);
	}
	else
	{
		if (stristr($filename, ROOT_PATH) === false)
		{
			if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
			$extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
			$extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
			$extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert')
			{
				$format = $extname;
			}
		}
		else
		{
			return '';
		}
	}

	if ($format == '' && strlen($str) >= 2 )
	{
		if (substr($str, 0, 4) == 'MThd' && $extname != 'txt')
		{
			$format = 'mid';
		}
		elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')
		{
			$format = 'wav';
		}
		elseif (substr($str ,0, 3) == "\xFF\xD8\xFF")
		{
			$format = 'jpg';
		}
		elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt')
		{
			$format = 'gif';
		}
		elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A")
		{
			$format = 'png';
		}
		elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt')
		{
			$format = 'bmp';
		}
		elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt')
		{
			$format = 'swf';
		}
		elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
		{   // D0CF11E == DOCFILE == Microsoft Office Document
			if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc')
			{
				$format = 'doc';
			}
			elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls')
			{
				$format = 'xls';
			} elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt')
			{
				$format = 'ppt';
			}
		} elseif (substr($str ,0, 4) == "PK\x03\x04")
		{
			$format = 'zip';
		} elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt')
		{
			$format = 'rar';
		} elseif (substr($str ,0, 4) == "\x25PDF")
		{
			$format = 'pdf';
		} elseif (substr($str ,0, 3) == "\x30\x82\x0A")
		{
			$format = 'cert';
		} elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt')
		{
			$format = 'chm';
		} elseif (substr($str ,0, 4) == "\x2ERMF")
		{
			$format = 'rm';
		} elseif ($extname == 'sql')
		{
			$format = 'sql';
		} elseif ($extname == 'txt')
		{
			$format = 'txt';
		}
	}

	if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false)
	{
		$format = '';
	}

	return $format;
}

/**
 * 获取服务器的ip
 *
 * @access      public
 *
 * @return string
 **/
function real_server_ip()
{
	static $serverip = NULL;

	if ($serverip !== NULL)
	{
		return $serverip;
	}

	if (isset($_SERVER))
	{
		if (isset($_SERVER['SERVER_ADDR']))
		{
			$serverip = $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$serverip = '0.0.0.0';
		}
	}
	else
	{
		$serverip = getenv('SERVER_ADDR');
	}

	return $serverip;
}

/**
 * 自定义 header 函数，用于过滤可能出现的安全隐患
 *
 * @param   string  string  内容
 *
 * @return  void
 **/
function est_header($string, $replace = true, $http_response_code = 0)
{
	$string = str_replace(array("\r", "\n"), array('', ''), $string);

	if (preg_match('/^\s*location:/is', $string))
	{
		@header($string . "\n", $replace);
		exit();
	}

	@header($string, $replace, $http_response_code);
}


function est_geoip($ip)
{
	static $fp = NULL, $offset = array(), $index = NULL;

	$ip    = gethostbyname($ip);
	$ipdot = explode('.', $ip);
	$ip    = pack('N', ip2long($ip));

	$ipdot[0] = (int)$ipdot[0];
	$ipdot[1] = (int)$ipdot[1];
	//局域网
	if ($ipdot[0] == 10 || $ipdot[0] == 127 || ($ipdot[0] == 192 && $ipdot[1] == 168) || ($ipdot[0] == 172 && ($ipdot[1] >= 16 && $ipdot[1] <= 31)))
	{
		return 'LAN';
	}

	if ($fp === NULL)
	{
		$fp = fopen(ROOT_PATH . 'includes/codetable/ipdata.dat', 'rb');
		if ($fp === false)
		{
			return 'Invalid IP data file';
		}
		$offset = unpack('Nlen', fread($fp, 4));
		if ($offset['len'] < 4)
		{
			return 'Invalid IP data file';
		}
		$index  = fread($fp, $offset['len'] - 4);
	}

	$length = $offset['len'] - 1028;
	$start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
	for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8)
	{
		if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip)
		{
			$index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
			$index_length = unpack('Clen', $index{$start + 7});
			break;
		}
	}

	fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
	$area = fread($fp, $index_length['len']);

	fclose($fp);
	$fp = NULL;

	return $area;
}


/**
 * 去除字符串右侧可能出现的乱码
 *
 * @param   string      $str        字符串
 *
 * @return  string
 */
function trim_right($str)
{
	$len = strlen($str);
	/* 为空或单个字符直接返回 */
	if ($len == 0 || ord($str{$len-1}) < 127)
	{
		return $str;
	}
	/* 有前导字符的直接把前导字符去掉 */
	if (ord($str{$len-1}) >= 192)
	{
		return substr($str, 0, $len-1);
	}
	/* 有非独立的字符，先把非独立字符去掉，再验证非独立的字符是不是一个完整的字，不是连原来前导字符也截取掉 */
	$r_len = strlen(rtrim($str, "\x80..\xBF"));
	if ($r_len == 0 || ord($str{$r_len-1}) < 127)
	{
		return sub_str($str, 0, $r_len);
	}

	$as_num = ord(~$str{$r_len -1});
	if ($as_num > (1<<(6 + $r_len - $len)))
	{
		return $str;
	}
	else
	{
		return substr($str, 0, $r_len-1);
	}
}

/**
 * 将上传文件转移到指定位置
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '')
{
	if (function_exists("move_uploaded_file"))
	{
		if (move_uploaded_file($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
		else if (copy($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
	}
	elseif (copy($file_name, $target_name))
	{
		@chmod($target_name,0755);
		return true;
	}
	return false;
}

/**
 * 获取文件后缀名,并判断是否合法
 *
 * @param string $file_name
 * @param array $allow_type
 * @return blob
 */
function get_file_suffix($file_name, $allow_type = array())
{
	$file_suffix = strtolower(array_pop(explode('.', $file_name)));
	if (empty($allow_type))
	{
		return $file_suffix;
	}
	else
	{
		if (in_array($file_suffix, $allow_type))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

/**
 * 时间转换函数
 *
 * @param unknown_type $diff
 * @return unknown
 */
function timeFormate($diff) {
	$since = "";

	$hours = intval($diff / 3600);
	$left = $diff%3600;
	$since .= sprintf('%02d',$hours).":";


	$mins = intval($left / 60);
	$sec = $left%60;
	$since .= sprintf('%02d',$mins).":";
	$since .= sprintf('%02d',$sec);

	return $since;
}

/**
 * 编码格式转换
 *
 */
function est_iconv($source_lang, $target_lang, $source_string = '')
{
	if(function_exists('mb_convert_encoding'))
	{
		$c = mb_convert_encoding($source_string,$target_lang,$source_lang);
	}
	else
	{
		$c = @iconv($source_lang, $target_lang, $source_string);
	}

	return $c;
}
/**
 * 得到目录下的全部文件信息
 */
function get_file($dirname){
	$d = opendir($dirname);
	$nodess = array();
	while($file = readdir($d)){
		if($file != "." && $file != "..")
		{
			if(is_file($dirname.$file))
			{
				$fileaddresss = $dirname.$file;
				$nodess[] = array('name'=>$file,'address'=> $fileaddresss,'updatetime'=>date('Y-m-d H:i:s',filemtime($fileaddresss)), 'competence'=>substr(sprintf('%o', fileperms($fileaddresss)), -4),"size" => sprintf("%.1f",filesize($fileaddresss)/1024). 'K');
			}
		}
	}
	closedir($d);

	return $nodess;
}

/**
 * 过去去除重复的callid 和 0 
 *
 */
function filter_callid($callid_str)
{
	$record_tmp  = explode(',',$callid_str);
	$callids  = array_unique($record_tmp); //移除数组中重复的值
	foreach ($callids as $key => $callid)
	{
		if($callid == 0)
		{
			unset($callids[$key]);
		}
	}
	return implode(',',$callids);
}

/**
 * escape解码函数
 *
 * @param string $str
 * @return string
 */
function unescape($str){
	$ret = '';
	$len = strlen($str);
	for ($i = 0; $i < $len; $i++){
		if ($str[$i] == '%' && $str[$i+1] == 'u'){
			$val = hexdec(substr($str, $i+2, 4));
			if ($val < 0x7f) $ret .= chr($val);
			else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
			else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
			$i += 5;
		}
		else if ($str[$i] == '%'){
			$ret .= urldecode(substr($str, $i, 3));
			$i += 2;
		}
		else $ret .= $str[$i];
	}
	return $ret;
}