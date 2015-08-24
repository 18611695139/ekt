<?php
/**
 * EasyTone php读写csv的函数
 * ============================================================================
 * 版权所有 2008-2009 北京华夏成讯科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinawintel.com；
 * ============================================================================
 * $Id    : cls_csv.php 
 * $Author: ZT
 * $time  : Tue Nov 17 13:57:40 CST 2009
*/

class CI_Csv
{
	var $charset   = 'utf-8';

	//构造函数
	function __construct($charset='utf-8')
	{
		$this->charset = $charset;
	}

	/**
	 * 导出csv程序
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $array
	 * @return unknown
	 */
	function creatcsv($filename,$array)
	{
		@ini_set('display_errors',        0);
		$filename = str_ireplace(".csv","",$filename);
		$filename = est_iconv($this->charset, 'GBK', $filename);
		header( "Pragma: public" );
		header( "Expires: 0" ); // set expiration time
		header( "Cache-Component: must-revalidate, post-check=0, pre-check=0" );
		header(	"Content-type: application/vnd.ms-excel; charset=utf-8");
		Header(	"Content-Disposition: attachment; filename=$filename.csv");
		header( 'Content-Transfer-Encoding: binary' );

		if(is_array($array))
		{
			if(is_array(@$array[0]))
			{
				foreach ($array as $tmp)
				{
					echo est_iconv($this->charset, 'GBK', join(',',$tmp));
					echo "\r\n";
				}
			}
			else
			{
				echo est_iconv($this->charset, 'GBK', join(',',$array));
			}
		}
		else
		{
			$this->ErrorMsg("param is not array");
			return false;
		}
	}

	//导出到excel文件
	function creatxls($filename,$array)
	{
		@ini_set('display_errors',        0);
		$filename = str_ireplace(".csv","",$filename);
		$filename = est_iconv($this->charset, 'GBK', $filename);
		header( "Pragma: public" );
		header( "Expires: 0" ); // set expiration time
		header( "Cache-Component: must-revalidate, post-check=0, pre-check=0" );
		header("Content-type: application/vnd.ms-excel; charset=GBK");
		Header("Content-Disposition: attachment; filename=$filename.xls");
		header( 'Content-Transfer-Encoding: binary' );

		echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=gb2312' /></head>";
		if(is_array($array))
		{
			if(is_array(@$array[0]))
			{
				echo "<table>";
				foreach ($array as $tmp)
				{
					echo "<tr>";
					foreach ($tmp as $item)
					{
						$item = est_iconv($this->charset, 'GBK', $item);
						echo  "<td style='vnd.ms-excel.numberformat:@ '>".$item."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			}
		}
		else
		{
			$this->ErrorMsg("param is not array");
			return false;
		}
		echo "</html>";
	}

	//导出到excel文件的压缩包
	function creatzipxls($filename,$array)
	{
		@ini_set('display_errors',        0);

		$filename = eregi_replace(".xls$","",$filename);
		$filename = est_iconv($this->charset, 'GBK', $filename);

		$this->load->library('Phpzip');
		$str =  "<table>";
		foreach ($array as $tmp)
		{
			$str .=  "<tr>";
			foreach ($tmp as $key => $item)
			{
				$str .=    "<td style='vnd.ms-excel.numberformat:@'>".$item."</td>";
			}
			$str .=   "</tr>";
		}
		$str .=   "</table>";
		$str = est_iconv($this->charset, 'GBK', $str);
		$this->phpzip->add_file($str, "$filename.xls");

		header( "Pragma: public" );
		header( "Expires: 0" ); // set expiration time
		header( "Cache-Component: must-revalidate, post-check=0, pre-check=0" );
		header("Content-type: application/vnd.ms-excel; charset=GBK");
		Header("Content-Disposition: attachment; filename=$filename.zip");
		header( 'Content-Transfer-Encoding: binary' );
		die($this->phpzip->file());
	}

	function _fgetcsv(&$handle, $length = null, $d = ',', $e = '"') {
		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof=false;
		while ($eof != true) {
			$_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
			if ($itemcnt % 2 == 0)
			$eof = true;
		}
		$_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
		$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
			$_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1' , $_csv_data[$_csv_i]);
			$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
		}
		return empty ($_line) ? false : $_csv_data;
	}

	/**
	 * 读取csv文件
	 *
	 */
	function readcsv($filename){
		$array = array();
		$handle = fopen($filename,"r");
		$i = 0;
		while ($data = $this->_fgetcsv($handle, 1024, ",")) {
			foreach ($data as $key=>$val)
			{
				$data[$key] = trim(est_iconv('GBK',$this->charset,$val));
			}
			if($data == array(0=>''))
			{
				continue;
			}
			$array[] = $data;
			$i++;
		}

		fclose($handle);
		return $array;
	}

	function ErrorMsg($message = '')
	{
		print_r($message);
		exit;
	}
}
?>