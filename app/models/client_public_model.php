<?php
class Client_public_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}


	/**
	 * 占用一条无所属人的数据
	 *
	 * @param int $cle_id
	 * @return bool
	 * @author zgx
	 */
	public function take_up_client($cle_id=0)
	{
		if(empty($cle_id))
		{
			return FALSE;
		}
		$dept_id = $this->session->userdata('dept_id');
		$user_id = $this->session->userdata('user_id');
		$reslut = $this->db_write->query("UPDATE est_client SET dept_id=".$dept_id.",user_id=".$user_id.",cle_public_type='0',cle_update_time=CURDATE() , cle_update_user_id='$user_id' WHERE user_id=0 AND cle_id=".$cle_id."");
		if($reslut)
		{
			$role_action = $this->session->userdata('role_action');
			$action = explode(',',$role_action);
			if(in_array('ddgl',$action))
			{
				//订单所属人改变
				$this->load->model('order_model');
				$this->order_model->update_order_user_id_when_client_resource($cle_id,$user_id,$dept_id);
			}
			//写入日志
			$this->load->model("log_model");
			$this->log_model->write_client_log('坐席占用数据',$cle_id);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}