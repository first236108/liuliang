<?php
header('content-type:text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*');
define('BASEDIR', dirname(__FILE__));
include_once(BASEDIR . "/lib/DB.class.php");
include_once(BASEDIR . "/lib/function.php");

try {

    $mobile = $_POST['mobile'];

    if (!preg_match("/^1[3456789][0-9]{9}$/", $mobile)) {
        echo json_encode(['ret' => 1, 'msg' => '手机号码不正确']);
        exit;
    }

    $dbset = [
        'dsn'      => 'mysql:host=bdm302895516.my3w.com;dbname=bdm302895516_db',
        'name'     => 'bdm302895516',
        'password' => 'zhangcong1com',
    ];

    $_DB = new DB($dbset);
    $ip = getTrueIp();
    $is_repeat = $_DB->getValue("select id from sms_order where (phone=$mobile or ip='" . $ip . "') and status=0");
    if ($is_repeat) {
        echo json_encode(['ret' => 2, 'msg' => '暂不支持重复获取']);
        exit;
    }

    $appkey = 'd53a21e613654c238d7de3b0fc5fe7b4';
    $timestamp = time();
    $username = 'linshuohf';
    $url = 'http://liuliang.llqwt.com/api/feesubmit';

    #充值话费
    $fee = 1;
    $data = [
        'username'  => $username,
        'mobile'    => $mobile,
        'fee'       => $fee,
        'timestamp' => $timestamp,
        'digest'    => strtolower(MD5($username . $mobile . $appkey . $timestamp))
    ];

    $result = json_decode(http_post_json($url, http_build_query($data)));
    if ($result->errorcode != 0) {
        echo json_encode(['ret' => 1, 'msg' => '充值失败', 'data' => $result]);
        exit;
    }

    #记录状态
    $insert_flag = $_DB->add('sms_order', [
        'order_no'    => $result->orderno,
        'phone'       => $mobile,
        'fee'         => $fee,
        'create_time' => date('Y-m-d h:i:s'),
        'ip'          => $ip
    ]);

    echo json_encode(['ret' => 0, 'msg' => '充值成功', 'order' => $result->orderno, 'fee' => $fee]);

} catch (Exception $e) {
    echo "Failed: " . $e->getMessage();
}