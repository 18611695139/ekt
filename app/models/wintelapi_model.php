<?php
class Wintelapi_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}


	/**
	 * 调用wintelapi中接口，返回数据（加上认证信息）
	 *
	 * @param string $url
	 * @param string|array $param 参数(参数需是一维数组或空)
	 * @param string $metod 传输方式：POST或GET
	 * @return array|string
	 */
	public function wintelapi_send($url,$param,$metod,$connect_timeout=-1)
	{
		admin_priv();

		$this->config->load('myconfig');
		$cfg = $this->config->config;
		$secret = isset($cfg["wintelapi_secret"])?$cfg["wintelapi_secret"]:''; //密码
		if(isset($cfg["api_wintelapi"]))
		$url = $cfg["api_wintelapi"] . $url;

		$username = $this->session->userdata('vcc_code'); //企业代码
		$nonce = get_rand_str(20);//随机字符串
		$Created = date("Y-m-d H:i:s",(time()-date("Z")));//格林威治时间的时间戳
		$PasswordDigest = base64_encode(sha1(base64_decode($nonce).$Created.$secret, true));//通过算法得出
		$wsse = 'UsernameToken Username="'.$username.'", PasswordDigest="'.$PasswordDigest.'", Nonce="'.$nonce.'", Created="'.$Created.'"';//http头添加的信息
		$head_par = array("X-WSSE"=>$wsse);

		$this->load->library('transport');
		$this->transport->transport(-1, $connect_timeout, -1, true);
		$result = $this->transport->request($url,$param,$metod,$head_par);
		if($result && isset($result['body']))
		{
			return json_decode($result['body'], true);
		}
		else
		{
			return array('code'=>'','message'=>'调用接口失败，原因为【'.$result.'】');
		}
	}

}