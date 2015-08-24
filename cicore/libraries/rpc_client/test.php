<?php
header('content-type: text/html; charset=utf-8');
include ("./phprpc_client.php");   
$client = new PHPRPC_Client();   
$client->setProxy(NULL);   
$client->useService('http://192.168.1.123/wintels2010/interface.php');   
$client->setKeyLength(2);   
$client->setEncryptMode(2);   
$client->setCharset('UTF-8');   
$client->setTimeout(10);   
$aa = $client->check_login('0005','123');  //验证密码 
$bb = $client->get_all_user();  //得到所有的坐席信息
   
?>