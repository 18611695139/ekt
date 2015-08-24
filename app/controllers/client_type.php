<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client_type extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	public function save_cle_type()
	{
		$cle_stage_json = $this->input->post('cle_stage');
		$cle_type_json = $this->input->post('cle_type');
		
		$this->load->library('Json');
		$cle_stage = $this->json->decode($cle_stage_json);
		$cle_type = $this->json->decode($cle_type_json);
		$this->load->model('client_type_model');
		$result = $this->client_type_model->save_cle_type_by_stage($cle_stage,$cle_type);
		make_simple_response($result);
	}
}