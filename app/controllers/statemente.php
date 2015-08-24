<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  业务信息报表
 *
 */
class Statemente extends CI_Controller
{
	/**
     * 指定特定的统计字段
     */
	const FIELD_ID = 122;

	function __construct()
	{
		parent::__construct();
	}

	/**
     *业务信息报表页面
     */
	public function index()
	{
		admin_priv();

		$field_id = self::FIELD_ID;
		$this->load->model("statemente_model");
		$checkbox_data = $this->statemente_model->get_checkbox_options($field_id);
		$this->smarty->assign('checkbox_data',$checkbox_data);
		$accept_start =  date("Y-m-d",time());
		$accept_end =  date("Y-m-d",time());
		$this->smarty->assign('accept_start',$accept_start);
		$this->smarty->assign('accept_end',$accept_end);
		$this->smarty->display("statemente_list.htm");
	}

	/**
     * 按受理时间条件查询
     */
	public function search()
	{
		admin_priv();
		$condation = $this->input->post();
		$field_id = self::FIELD_ID;
		$sql_condition = '';
		if($condation["accept_start"])
		{
			$acceptstart= date("Y-m-d 00:00:00",strtotime($condation["accept_start"]));
			$accept_start = $acceptstart? strtotime($acceptstart) : '';
			$sql_condition = $sql_condition." AND serv_accept_time >='" . $accept_start . "'";
		}
		if($condation["accept_end"])
		{
			$acceptend= date("Y-m-d 23:59:59",strtotime($condation["accept_end"]));
			$accept_end =  strtotime($acceptend) ;
			$sql_condition = $sql_condition." AND serv_accept_time <='" . $accept_end . "'";
		}
		if(isset($sql_condition))
		{
			$searched=1;
		}
		$this->load->model("statemente_model");
		$searck_data = $this->statemente_model->get_checkbox_options($field_id,$sql_condition,$searched);

		$this->smarty->assign('accept_start',$condation["accept_start"]);
		$this->smarty->assign('accept_end',$condation["accept_end"]);
		$this->smarty->assign('checkbox_data',$searck_data);
		$this->smarty->display("statemente_list.htm");
	}

	/**
     * 导出数据
     */
	public function export()
	{
		admin_priv();
		@ini_set('display_errors',        0);
		$data = $this->input->post('export');
		$data = $this->convertEncoding('GBK', 'UTF-8', $data);
		$filename = date("Y_m_d",time())."业务报表.xls";
		$filename = $this->convertEncoding('GBK', 'UTF-8', $filename);
		header('Expires:0');
		header('Pragma:public');
		header("Cache-Component: must-revalidate, post-check=0, pre-check=0" );
		header("Content-type: application/vnd.ms-excel; charset=GBK");
		header("Content-Disposition:attachment;filename=".$filename);
		header( 'Content-Transfer-Encoding: binary' );
		echo($data);
	}

	/**
     * @param string $to_charset
     * @param string $from_charset
     * @param string $string
     * @return string
     */
	public function convertEncoding($to_charset, $from_charset, $string)
	{
		if (function_exists('mb_convert_encoding')) {
			$string = mb_convert_encoding($string, $to_charset, $from_charset);
		} else {
			$string = iconv($from_charset, $to_charset."//TRANSLIT", $string);
		}

		return $string;
	}

}
