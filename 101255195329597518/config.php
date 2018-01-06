<?php

include('MysqliDb.php');
//*****修改以下三行，看分号右边的说明
$site = "http://www.lszhejiugou.xyz";             //在左边双引号内填写完整网址，注意最后没有“/”
$orderId = "101255195329597518";              //在左边的双引号内填写订单文件夹名字
$protocol = "http";                       //网站是https开头的在左边的双引号内填写https，是http开头的在边的双引号内填写http
$qq = "291361642";          //在左边的双引号内填写客服QQ号
$version = "6";             //左边双引号内填写整数，安卓端每次更新加1
$downurl= "http://a.app.qq.com/o/simple.jsp?pkgname=com.zjg.lingshuo";      //左边双引号内填写新版本安卓apk的下载地址，没有的留空
$currentShenheVersion = "0";  //现在审核中的苹果版本号，如果审核通过了，请在双引号内填写0.
$uParam = "401818";  //网站的u参数(一串数字)，参考提交的app资料相应的地方

//************************************************************************************************************************


$hotTag = "牛仔裤,童装,女鞋,背包,充电宝,口红,女装秋,面膜,耳机";  //热门搜索标签，用英文逗号隔开
$apiKey = "";      //你的大淘客的api key
$host = "";    //你的数据库的ip地址
$port = "3306";    //数据库端口号
$username = "";  //登录数据库的用户名
$password = "";   //登录数据库的密码
$databaseName = "chaovip_dtk";  //数据库名称
$dbCacheTime = "61";  //数据缓存时间,单位秒，建议大于60




///////////////////////填写以上内容/////////////////////////


//////////////////////以下内容不需要修改////////////////////
$tableName = "chaovip_db_pages";
$dbarr = Array (
		'host' => $host,
		'username' => $username, 
		'password' => $password,
		'db'=> $databaseName,
		'port' => $port,
		'prefix' => '',
		'charset' => 'utf8');

$goodsApi = "http://api.dataoke.com/index.php?r=port/index&appkey=".$apiKey."&v=2&id=";   //单品api
$allGoodsApi = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=".$apiKey."&v=2&page=";  //全站优惠券api

//$indexPage = $domain."/index.php?r=l&page=";



function getMillisecond() { 
    list($s1, $s2) = explode(' ', microtime()); 
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
}

function getHtmlCurl1($url){
	$cookie_file = dirname(__FILE__).'\cookie.txt';
	@$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	//curl_setopt($ch,CURLOPT_HEADER,0);
	//curl_setopt($ch,CURLOPT_NOBODY,false); 
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	//curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	//curl_setopt($ch,CURLOPT_MAXREDIRS,20);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.91 Safari/537.36");
	$orders=@curl_exec($ch);
	@curl_close($ch);
	return $orders;
}

function getHtmlCurl2($url){
	$post_data = array("from" => "server");
	$cookie_file = dirname(__FILE__).'\cookie.txt';
	@$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	//curl_setopt($ch,CURLOPT_HEADER,0);
	//curl_setopt($ch,CURLOPT_NOBODY,false); 
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	//curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	//curl_setopt($ch,CURLOPT_MAXREDIRS,20);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.91 Safari/537.36");
　　curl_setopt($ch, CURLOPT_POST, 1);
　　curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$orders=@curl_exec($ch);
	@curl_close($ch);
	return $orders;
}