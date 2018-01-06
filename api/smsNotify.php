<?php
header('content-type:text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*');
define('BASEDIR', dirname(__FILE__));
include_once(BASEDIR . "/lib/DB.class.php");
include_once(BASEDIR . "/lib/function.php");
$file = BASEDIR . "/log/smslog.txt";
date_default_timezone_set('PRC');
var_dump(date('Y:m:d h:i:s', time()));
$data = file_get_contents('php://input');

file_put_contents($file, json_encode($data) . date('Y:m:d h:i:s', time()) . PHP_EOL);
if (empty($data)) {
    exit;
}

$data = json_decode(urldecode($data));

if ($data->errorcode != 0) {
    exit;
}
$row = [
    'status'      => 1,
    'price'       => $data->price,
    'update_time' => date('Y-m-d h:i:s')
];

$dbset = [
    'dsn'      => 'mysql:host=bdm302895516.my3w.com;dbname=bdm302895516_db',
    'name'     => 'bdm302895516',
    'password' => 'zhangcong1com',
];

$_DB = new DB($dbset);
$_DB->update('sms_order', $row, 'order_no=' . $data->orderno);

echo "0";