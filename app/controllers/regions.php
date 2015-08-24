<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regions extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 得到指定省得所有市信息
	 *
	 */
	public function get_regions_type()
	{
		$parent_id = $this->input->post("parent_id");
		$region_type = $this->input->post('region_type');

		//省市信息
		$this->load->model('regions_model');
		$regions_info = $this->regions_model->get_regions($region_type,$parent_id);

		make_json_result($regions_info);
	}
}