<?php
if(!function_exists('curl_init')){
  die('ERROR: CURL not available!');
}elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
  die('400 Bad request.');
}

error_reporting(0);
ini_set('max_execution_time', 7200);
set_time_limit(7200);

$headers[] = 'Connection: keep-alive';
$headers[] = 'Accept: ' . $_SERVER['HTTP_ACCEPT'];
$headers[] = 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'];

$serverAddr = 'http://mini5.opera-mini.net/';
if(isset($_SERVER['HTTP_X_PREFER_SERVER']) and $_SERVER['HTTP_X_PREFER_SERVER']){
  $serverAddr = 'http://' . $_SERVER['HTTP_X_PREFER_SERVER'] . '/';
}

function readBody(&$curlInterface, &$content){
  $ret = strlen($content);
  echo $content;
  if(connection_status() != 0){
    curl_close($curlInterface);
    exit;
  }
  flush();
  return $ret;
}

header('Content-Type: application/octet-stream');
header('Cache-Control: private, no-cache');
header('Connection: keep-alive');

$curlInterface = curl_init();
curl_setopt($curlInterface,CURLOPT_POST, TRUE);
curl_setopt($curlInterface,CURLOPT_HEADER, FALSE);
curl_setopt($curlInterface,CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curlInterface,CURLOPT_URL, $serverAddr);
curl_setopt($curlInterface,CURLOPT_POSTFIELDS, file_get_contents('php://input'));
curl_setopt($curlInterface,CURLOPT_HTTPHEADER, $headers);
curl_setopt($curlInterface,CURLOPT_WRITEFUNCTION, 'readBody');
curl_exec($curlInterface);
curl_close($curlInterface);

exit;