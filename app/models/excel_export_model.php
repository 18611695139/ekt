<?php
class Excel_export_model extends CI_Model {

    /** @var array $phoneFields 电话号码的字段 */
    private $phoneFields = array(
        'cle_phone','con_mobile','cle_phone2','cle_phone3'
    );

    /** @var array $cells 表格的抬头 */
    private $cells = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC',
        'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ',
        'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE',
        'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS',
        'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG',
        'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU',
        'CV', 'CW', 'CX', 'CY', 'CZ'
    );

    private $phpexcel;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        $this->phpexcel = new PHPExcel();
    }

    /**
     * {@inheritdoc}
     */
    public function export($data = array(),$fields =array(),$fileName = '')
    {
        if (!is_array($fields)) {
            return false;
        }

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        $cacheSettings = array();
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        /*$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
        $cacheSettings = array( 'memcacheServer'  => '192.168.1.25',
            'memcachePort'    => 11211,
            'cacheTime'       => 600
        );
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);*/

        $this->phpexcel->getProperties()->setCreator("中通天鸿（北京）通信科技有限公司")
            ->setLastModifiedBy("中通天鸿（北京）通信科技有限公司")
        ;
        $this->phpexcel->setActiveSheetIndex(0);

        //如果存在表头则设置
        $cellIndex = 0;
        foreach ($fields as $field => $value) {
            $title = $value;
            $cell = $this->cells[$cellIndex] . '1';
            $this->phpexcel->getActiveSheet()->setCellValue($cell, $title);
            if (in_array($field, $this->phoneFields)) {
                $this->phpexcel->getActiveSheet()->getColumnDimension($this->cells[$cellIndex])->setAutoSize(true);
            }
            $cellIndex++;
        }

        //导出数据
        foreach ($data as $index => $row) {
            $cellIndex = 0;
            foreach ($fields as $field => $value) {
                $cellNum = $this->cells[$cellIndex++];
                $cell = $cellNum . ($index+2);
                $pValue = isset($row[$field]) ? strval($row[$field]) : '';

                $this->phpexcel->getActiveSheet()->setCellValue($cell, $pValue);
                //处理字段属性
                if (in_array($field, $this->phoneFields)) {
                    $this->phpexcel->getActiveSheet()->setCellValueExplicit($cell,$pValue,PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->phpexcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode("@");

                }
                //$options = array();
                //$this->phpexcel->getActiveSheet()->getStyle($cell)->applyFromArray($options);

            }
            unset($data[$index]);
        }

        $fileName = empty($fileName) ? 'Data'.date('YmdHis').'.xls' : $fileName . '.xls';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8');
        header("Content-Disposition: attachment;filename=$fileName");
        header('Cache-Control: max-age=0');

        $objWriter = new PHPExcel_Writer_Excel5($this->phpexcel);
        //$objWriter = IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save("php://output");
        exit;
    }
}
