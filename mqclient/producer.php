<?php
if(!class_exists('Stomp'))
{
	require_once('Stomp.php');
}

$queue  = '/queue/wintel.toapp';
$callback = trim($_GET['callback']);
$message  = trim($_GET['content']);
$linkid	  = trim($_GET['linkid']);
$ctiip	  = trim($_GET['ctiip']);

$header = array();
$header['linkip']     = real_ip();
$header['linkid']     = $linkid;

/* connection */
require_once('../app/config/activemq.php');
try
{
	$stomp = new Stomp("tcp://".$config['activemq']['hostname'].":".$config['activemq']['port']);
	$stomp->send($queue, $message, $header);
	unset($stomp);
}
catch(StompException $e)
{
	file_put_contents('../app/logs/consumer'.date("Y_m_d").'.log', '['.date('Y-m-d H:i:s').']'.' Connection failed: ' . $e->getMessage()."\r\n", FILE_APPEND);
	die('Connection failed: ' . $e->getMessage());
}

echo $callback.'({"ret":0})';
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