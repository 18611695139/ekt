<?php
if(!class_exists('Stomp'))
{
	require_once('Stomp.php');
}

$queue  = '/queue/wintel.toag';

$callback = trim($_GET['callback']);
$linkid   = intval($_GET['linkid']);
$timeout  = isset($_GET['timeout']) ? intval($_GET['timeout']) : 20;
$ctiip	  = trim($_GET['ctiip']);

require_once('../app/config/activemq.php');
/* connection */
try
{
	$stomp = new Stomp("tcp://".$config['activemq']['hostname'].":".$config['activemq']['port']);
	$stomp->setReadTimeout($timeout);
	if($linkid == 0)//订阅消息(若linkid为0则先获取linkid)
	{
		$stomp->subscribe($queue, array('selector'=>'linkid=0'));
	}
	//否则则订阅该linkid的消息
	else
	{
		$stomp->subscribe($queue, array('selector'=>'linkid='.$linkid));
	}
	if($frame = $stomp->readFrame())
	{
		if ($frame)
		{
			$stomp->ack($frame);
			$linkstate = $frame->headers['linkstate'];
			echo $callback.'({"linkstate":'.$linkstate.',"body":'.$frame->body.'})';
		}
	}
	else
	{
		echo $callback.'('.json_encode(array()).')';
	}
	unset($stomp);
}
catch(StompException $e)
{
	file_put_contents('../app/logs/consumer'.date("Y_m_d").'.log', '['.date('Y-m-d H:i:s').']'.' Connection failed: ' . $e->getMessage()."\r\n", FILE_APPEND);
	die('Connection failed: ' . $e->getMessage());
}