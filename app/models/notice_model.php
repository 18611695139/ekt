<?php
/**
 * notice 类。用于控制桌面上弹出提出
 *
 */

class Notice_model extends CI_Model {

	private $queue  = '/queue/msg';//队列名称
	private $host = '';//队列ip
	private $port = '';//队列端口

	function __construct()
	{
		parent::__construct();
		require_once(APPPATH.'config/activemq.php');
		$this -> host = $config['activemq']['hostname'];
		$this -> port = $config['activemq']['port'];
	}

    /**
     * 写消息 将消息写入文件中
     *
     * @param string $type 类型 message remind announce system
     * @param int $receiver_user_id 收notice用户id
     * @param string $sender 发送人
     * @param string $content 内容
     * @param int $show_time 显示给坐席的时间
     * @param int $reply_user_id 回复user_id
     * @param string $mark 标识（用于删除）
     */
	public function write_notice($type='message',$receiver_user_id=0,$sender='',$content='',$show_time=0,$reply_user_id=0,$mark='')
	{
		if(empty($receiver_user_id) || empty($content))
		{
			return false;
		}
		if(strlen($content) > 1000)
		{
			return false;
		}

		if(empty($show_time))
		{
			$show_time= time();
		}
		$vcc_id = $this->session->userdata('vcc_id');
		$notice = new stdClass();
		$notice->reply_user_id=$reply_user_id;
		$notice->content=$content;
		$notice->type=$type;
		$notice->mark=$mark;
		$notice->sender=$sender;


		$header = array('vcc_id'=>$vcc_id,'show_time'=>$show_time,'receiver'=>$receiver_user_id,'mark'=>$mark);
		if($notice->type == 'remind')
		{
			$header['persistent'] = 'true';
		}
		$this->load->library('json');
		$notice_str = $this->json->encode($notice);

		try
		{
			if(!class_exists('Stomp'))
			{
				require_once(FCPATH.'mqclient/Stomp.php');
			}
			$stomp = new Stomp("tcp://".$this -> host.":".$this->port);
			$stomp->send($this->queue, $notice_str, $header);
			unset($stomp);
		}
		catch(StompException $e)
		{
			$this->error_log('Connection failed: ' . $e->getMessage());
			die('Connection failed: ' . $e->getMessage());
		}

        return true;
	}

	/**
	 * 读消息|监听消息
	 * @return array
	 */
	public function monitor_notice()
	{
		$user_id = $this->session->userdata("user_id");
		$vcc_id = $this->session->userdata('vcc_id');

		$notice = '';
		try
		{
			if(!class_exists('Stomp'))
			{
				require_once(FCPATH.'mqclient/Stomp.php');
			}
			$stomp = new Stomp("tcp://".$this -> host.":".$this->port);
			$stomp->setReadTimeout(30);
			$now= time()+60;
			$stomp->subscribe($this->queue,array('selector'=>"vcc_id=$vcc_id AND receiver=$user_id AND show_time<=$now"));
			$notice_amq = $stomp->readFrame();

			if (!empty($notice_amq))
			{
				$stomp->ack($notice_amq);
				$this->load->library('json');
				$notice = $this->json->decode($notice_amq->body);
			}
			$stomp->unsubscribe($this->queue,array('selector'=>"vcc_id=$vcc_id AND receiver=$user_id AND show_time<=$now"));
			unset($stomp);
		}
		catch(StompException $e)
		{
			$this->error_log('Connection failed: ' . $e->getMessage());
			die('Connection failed: ' . $e->getMessage());
		}

		if(!empty($notice))
		{
			$notice->content = str_replace("\n",'<br>',$notice->content);
			switch ($notice->type)
			{
				case 'message':
					$this->load->model("message_model");
					$this->message_model->set_message_readed($notice->mark);
					$revice = '';
					if(!empty($notice->reply_user_id))
					$notice->sender = $notice->sender.$revice;
					break;
				default:
					break;
			}
			return object_to_array($notice);
		}
		else
		{
			return array();
		}
	}

    /**
     * 删除消息，通过删除标记mark
     *
     * @param int $user_id
     * @param string $type 类型
     * @param string $mark 删除标记
     * @return bool
     */
	public function remove_notice($user_id=0,$type='message',$mark='')
	{
		if(empty($user_id) || empty($mark))
		{
			return false;
		}
		$vcc_id = $this->session->userdata('vcc_id');

		try
		{
			if(!class_exists('Stomp'))
			{
				require_once(FCPATH.'mqclient/Stomp.php');
			}
			$stomp = new Stomp("tcp://".$this -> host.":".$this->port);
			$stomp->setReadTimeout(2);
			$stomp->subscribe($this->queue,array('selector'=>"vcc_id=$vcc_id AND receiver=$user_id AND mark=$mark"));
			$notice_amq = $stomp->readFrame();

			if (!empty($notice_amq))
			{
				$stomp->ack($notice_amq);
			}
			$stomp->unsubscribe($this->queue,array('selector'=>"vcc_id=$vcc_id AND receiver=$user_id AND mark=$mark"));
			unset($stomp);
		}
		catch(StompException $e)
		{
			$this->error_log('Connection failed: ' . $e->getMessage());
			die('Connection failed: ' . $e->getMessage());
		}

        return true;
	}

	private function error_log($message = '')
	{
		$logfilename = APPPATH.'logs/error_activemq_'. date('Y_m_d') . '.log';
		$str = 'date: '.date('Y_m_d H:i:s')  . "\n"
		.'error: '.$message . "\n\n";
		file_put_contents($logfilename, $str,FILE_APPEND);
	}
}