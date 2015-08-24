<?php
/**
 * This file is part of ekt_ci.
 * Author: louxin
 * Date: 14-3-7
 * Time: 下午3:47
 * File: test.php
 */

class Test extends CI_Controller
{
    public function index()
    {
        $this->load->library('elasticsearch');
        $data = array(
            'index' => 'ekt',
            'type' => 'client',
            'body' => array(
                'name' => '测试客户1',
                'phone' => '18522233344',
                'phone2' => '',
                'phone3' => '',
                'pinyin' => 'cskh1',
                'client_id' => 5
            )
        );
        echo '<pre>';
        var_dump($this->elasticsearch->addIndexes($data));
        echo '</pre>';
    }

    public function search()
    {
        $this->load->library('elasticsearch');
        $params = array(
            'index' => 'ekt',
            'type' => 'client',
            'body' => array(
                'query' => array(
                    'match' => array(
                        'phone' => array(
                            'query' => '18511111111',
                            'type' => 'phrase'
                        )
                    )
                )
            )
        );

        header('Content-type: text/html;charset=utf8');
        echo '<pre>';
        print_r($this->elasticsearch->search($params));
        echo '</pre>';
    }

    public function search2()
    {
        $this->load->library('elasticsearch');
        $params = array(
            'index' => 'ekt',
            'type' => 'client',
            'body' => array(
                'query' => array(
                    'multi_match' => array(
                            'query' => '1852223334',
                            'type' => 'phrase',
                            'fields'=>array('phone','name'),
                            'operator'=>'and'
                    )
                )
            )
        );

        header('Content-type: text/html;charset=utf8');
        echo '<pre>';
        print_r($this->elasticsearch->search($params));
        echo '</pre>';
    }

    public function delete($id)
    {
        $this->load->library('elasticsearch');
        $params = array(
            'index' => 'ekt',
            'type' => 'client',
            'id' =>$id
        );
        echo '<pre>';
        print_r($this->elasticsearch->delete($params));
        echo '</pre>';
    }

    public function rpctest()
    {
        $ch = curl_init('http://127.0.0.1:82/ekt/index.php?c=phone_control&m=dealnumber');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('number' => '18601307681'));
        curl_exec($ch);
    }
}