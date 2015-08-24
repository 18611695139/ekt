<?php

class Phone_location_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * 处理电话号码 -  座机号保持不变，手机号去掉前面的0和86
	 *
	 * @param string $tel   电话号码
	 * @return string
	 */
	public function remove_prefix_zero($tel)
	{
		//过滤电话号码，只留下 数字、-和/
		$tel = preg_replace('/[^\d|^\-|^\/]/','',$tel);

		if(strlen($tel) < 7)
		{
			return $tel;
		}

		//先去除86
		if(strlen($tel) > 12 && substr($tel,0, 2) == "86")
		{
			$tel = substr($tel,2);
		}
		if(strlen($tel) == 12 && substr($tel,0, 2) == "01")
		{
			$tel = substr($tel,1,11);//先截取11个字符，防止是多个手机号码
		}
		elseif (strlen($tel) == 13 && substr($tel,0, 3) == "001")
		{
			$tel = substr($tel,2,11);//先截取11个字符，防止是多个手机号码
		}
		else
		{
			$tel = substr($tel,0,13);//截取13个字符，防止是多个手机号码
		}

		return $tel;
	}
}
