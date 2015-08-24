<?php
/**
 * This file is part of manage_aika.
 * Author: louxin
 * Date: 13-7-24
 * Time: 下午1:15
 * File: ApiTest.php
 */

class ControllerTest extends CIUnit_TestCase {

    public function setUp()
    {
        $this->CI =& get_instance();
        //$this->CI->session->set
        $this->CI = set_controller('contact');
    }

    /**
     * ApiTest::testIndex()
     *
     * 测试Add_phone添加分机的功能
     */
    public function testIndex()
    {
        $_GET['cle_id'] = 1;
        $this->CI->index();
    }

}
