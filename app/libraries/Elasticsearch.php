<?php
/**
 * This file is part of ekt_ci.
 * Author: louxin
 * Date: 14-3-7
 * Time: 下午1:39
 * File: Elasticsearch.php
 */

use Elasticsearch\Client;

class Elasticsearch
{
    /**
     * @var null
     */
    private $CI = null;

    /**
     * @var null
     */
    private $client = null;

    public function __construct()
    {
        log_message('debug', "Elasticsearch Class Initialized");
        $this->CI = &get_instance();
        $this->CI->config->load('myconfig');

        $this->client = new Client($this->CI->config->item('elasticsearch'));
    }

    /**
     * 添加索引
     *
     * <pre>
     * $data = array(
     *      'index' => 'name'
     *      'type' => 'indextype',
     *      'body' => array(
     *          'name' => '张三'
     *          'phone' => '18911112222'
     *          'client_id' => 1
     *      )
     * );
     * </pre>
     *
     * @param array $data 索引数据
     * @return array
     */
    public function addIndexes($data = array())
    {
        return $this->client->index($data);
    }

    /**
     * 查询索引
     *
     * @param array $params
     * @return array 查询到的数据
     */
    public function search($params)
    {
        $data = $this->client->search($params);
        $hits = isset($data['hits']) ? $data['hits'] : array();

        return $hits;
    }
}