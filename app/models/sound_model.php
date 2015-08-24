<?php

class Sound_model extends CI_Model {
	private $PASSED = 1;//审核通过
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 返回所有可用的语音
	 * 
	 * @return array
	 * <code>
	 * array(
	 * 		[0] => array(
	 * 				[sound_id] => 语音id
	 * 				[sound_name] => 语音名称
	 * 			)
	 * )
	 * </code>
	 *
	 */
	public function get_usable_sounds()
	{
		$this->db_read->select('sound_id,sound_name');
		$sound_query = $this->db_read->get_where('est_sound',array('sound_state'=>$this->PASSED));
		return $sound_query->result_array();
	}
	
	/**
	 * 返回某一语音信息
	 * 
	 * @param int $sound_id 语音id
	 * @return array
	 */
	public function get_sounds_info($sound_id=0)
	{
		$sound_info = array();
		if($sound_id == 0)
		{
			return false;
		}
		$query = $this->db_read->get_where('est_sound',array('sound_id'=>$sound_id));
		$sound_info = $query->row_array();
		return $sound_info;
	}
}