<?php
header('content-type:text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*');
define('BASEDIR', dirname(__FILE__));
include_once(BASEDIR . "/lib/DB.class.php");
include_once(BASEDIR . "/lib/function.php");

try {

    #获取流量包
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
    $is_repeat = $_DB->getValue("select id from sms_order where (phone=$mobile or ip='".$ip."') and status=1");
    if ($is_repeat) {
        echo json_encode(['ret' => 2, 'msg' => '暂不支持重复获取']);
        exit;
    }

    $appkey = 'cc2007bd4fc245e29c90497f425fda8f';
    $timestamp = time();
    $username = 'linshuo';
    $url = 'http://liuliang.llqwt.com/api/getmealtag';

    #获取流量包
    $data = [
        'username'  => $username,
        'mobile'    => $mobile,
        'timestamp' => $timestamp,
        'digest'    => strtolower(MD5($username . $mobile . $appkey . $timestamp))
    ];

    $result = json_decode(http_post_json($url, http_build_query($data)));

    if ($result->errorcode != 0) {
        echo json_encode(['ret' => 1, 'msg' => '获取流量包失败', 'data' => $result]);
        exit;
    }

    #筛选最小流量包
    foreach ($result->mealdata as $k => $v) {
        if ($k == 0) {
            $min = $v;
        }
        if ($v->meal < $min->meal) {
            $min = $v;
        }
    }

    #充值流量包
    $url = 'http://liuliang.llqwt.com/api/submit';
    $timestamp = time();
    $data = [
        'username'  => $username,
        'mobile'    => $mobile,
        'mealtag'   => $min->mealtag,
        'timestamp' => $timestamp,
        'notifyurl' => 'http://sg546e.natappfree.cc/api/smsNotify.php',
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
        'meal'        => $min->meal,
        'create_time' => date('Y-m-d h:i:s'),
        'ip'          => $ip
    ]);
    echo json_encode(['ret' => 0, 'msg' => '充值成功', 'order' => $result->orderno, 'provider' => $min->provider, 'meal' => $min->meal]);

} catch (Exception $e) {
    echo "Failed: " . $e->getMessage();
}