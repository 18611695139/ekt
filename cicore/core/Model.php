<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	public $db_read;
	public $db_write;
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		$db_main = $this->session->userdata('db_main');
		$db_name = $this->session->userdata('db_name');
		if(!empty($db_main) && !empty($db_name))
		{
			$db_slave = $this->session->userdata('db_slave');
			$db_name = $this->session->userdata('db_name');
			$db_user = $this->session->userdata('db_user');
			$db_pwd = $this->session->userdata('db_pwd');
			$dsn_main = 'dbdriver://'.$db_user.':'.$db_pwd.'@'.$db_main.'/'.$db_name.'?char_set=utf8&dbcollat=utf8_general_ci&cache_on=false&cachedir=./app/cache&dbdriver=mysqli&pconnect=false&autoinit=false&stricton=false&db_debug=true&swap_pre=';
			$this->db_write = $this->load->database($dsn_main,TRUE);
			$dsn_slave = 'dbdriver://'.$db_user.':'.$db_pwd.'@'.$db_slave.'/'.$db_name.'?char_set=utf8&dbcollat=utf8_general_ci&cache_on=false&cachedir=./app/cache&dbdriver=mysqli&pconnect=false&autoinit=false&stricton=false&db_debug=true&swap_pre=';
			$this->db_read = $this->load->database($dsn_slave,TRUE);
			$this->load->driver('cache', array('adapter'=>'memcached','prefix'=>$this->session->userdata('vcc_id').'_'));
		}
		
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */