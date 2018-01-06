<?php

function getMillisecond1() { 
    list($s1, $s2) = explode(' ', microtime()); 
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
}

$total_t1 = getMillisecond1();

include('config.php');
include('phpQuery-onefile.php');


//获取完整链接
function getFullUrl(){
    # 解决通用问题
    $requestUri = '';
    if (isset($_SERVER['REQUEST_URI'])) {
        $requestUri = $_SERVER['REQUEST_URI'];
    } else {
        if (isset($_SERVER['argv'])) {
            $requestUri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
        } else if(isset($_SERVER['QUERY_STRING'])) {
            $requestUri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
    }
	//    echo $requestUri.'<br />';
    $scheme = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    //$protocol = strstr(strtolower($_SERVER["SERVER_PROTOCOL"]), "/",true) . $scheme;
	$protocol = $GLOBALS['protocol'] . $scheme;
       //端口还是蛮重要的，毕竟需要兼容特殊的场景
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    # 获取的完整url
    $_fullUrl = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $requestUri;
    return $_fullUrl;
}

function getHtmlCurl($url){
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
	$str=$orders;
	return $str;
}

function GetFile($url,$way=1,$coding){
    if($way==1){
        $str=file_get_contents($url);
    }else if($way==2){
		$cookie_file = dirname(__FILE__).'/cookie.txt';
        @$ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_NOBODY,false); 
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_MAXREDIRS,20);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt ($curl, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.4.4; zh-CN; SM-A7000 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.6.6.951 Mobile Safari/537.36");
        $orders=@curl_exec($ch);
        @curl_close($ch);
        $str=$orders;
    }
	/*
    if($coding=="1"){
        $str=iconv("UTF-8", "GBK", $str);
    }elseif ($coding=="2"){
        $str=iconv("GBK", "UTF-8", $str);
    }
	*/
    return $str;
}

function getParams($url) {
   $refer_url = parse_url($url); 
   $params = $refer_url['query']; 
   $arr = array(); 
   if(!empty($params)) 
   { 
       $paramsArr = explode('&',$params);
       foreach($paramsArr as $k=>$v) 
       { 
          $a = explode('=',$v); 
          $arr[$a[0]] = $a[1]; 
       } 
   } 
   return $arr; 
}

/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function send_post($url, $post_data) {
 

  $postdata = http_build_query($post_data);
  $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-type:application/x-www-form-urlencoded',
      'content' => $postdata,
      'timeout' => 15 * 60 // 超时时间（单位:s）
    )
  );
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
 /*
	$cookie_file = dirname(__FILE__).'\cookie.txt';
	$ch = curl_init();
	// 返回结果存放在变量中，不输出 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); 
	curl_setopt($ch, CURLOPT_POST, true);
	$headers_login = array(
		"User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.91 Safari/537.36",
		"Content-type: application/json;charset='utf-8'", 
	); 
	$fields_string = ""; 
	foreach($post_data as $key => $value){ 
		$fields_string .= $key . "=" . $value . "&"; 
	} 
	$fields_string = rtrim($fields_string , "&"); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_login); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

	curl_setopt ($curl, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieSuccess);//用来存放登录成功的cookie

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result= curl_exec($ch);
	curl_close($ch);
	*/
 
  return $result;
}

function curlPost($url,$post_data,$contentType = "Content-Type: application/json"){
	//curl验证成功
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		$contentType
	));

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		print curl_error($ch);
	}
	curl_close($ch);
	return $result;
}

function getJson($url){
	$handle = fopen($url,"rb");
	$content = "";
	while (!feof($handle)) {
		$content .= fread($handle, 20000);
	}
	fclose($handle);
	return $content;
}




$url = getFullUrl();
$urlArr = explode("selector.php",$url);
$url = $site."/index.php".$urlArr[1];
//$url = str_replace($orderId."/","",$url);
//$url = str_replace("selector.php","index.php",$url);



if(strpos($url,"action=config")>0){
	$arr = array();
	$arr["hot"] = $hotTag;
	$arr["qq"] = $qq;
	$arr["version"] = $version;
	$arr["downurl"] = $downurl;
	$arr["currentShenheVersion"] = $currentShenheVersion;
	header("Content-Type: text/html; charset=utf-8");
	echo json_encode($arr);
	exit();
}else if(strpos($url,"action=js")>0){
	echo "document.title";
	exit;
}else if(strpos($url,"action=description")>0){
	$descArr = explode("=",$url);
	$descUrl = 'http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?&data={"item_num_id":"'.$descArr[2].'"}';
	//echo htmlentities(getJson($descUrl),ENT_QUOTES,"UTF-8");
	//echo $descUrl;
	$descJson = json_decode(getJson($descUrl),true);
	$len = count($descJson["data"]["images"]);
	$descHtml = "";
	for($i=0;$i<$len;$i++){
		$myhtml = "<img onload='imgReady();' src='".$descJson["data"]["images"][$i]."'/>";
		//$myhtml = str_replace("<img","<img onload='imgReady();'",$myhtml);
		$descHtml = $descHtml.$myhtml;
	}
	$myd = array();
	$myd["data"] = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><head><meta name='viewport' content='width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no'/></head><body>".$descHtml."<script language='javascript'>var nodes=document.getElementsByTagName('img');var nodesLen=nodes.length;var num=0;function imgReady(){num++;if(num==nodesLen){window.location.href='ios::'+Math.max(document.documentElement.scrollHeight,document.documentElement.clientHeight)}}</script></body></html>";
	$myd["data"] = $descJson["data"]["images"];
	header("Content-Type: text/html; charset=utf-8");
	echo json_encode($myd);
	exit();
}



$data = array();
$items = array();

/*
淘抢购：http://wx.chaovip.com/app/index.php?i=2&c=entry&do=plus_tqg&m=bsht_tbk&shopid= 
聚划算：
http://wx.chaovip.com/app/index.php?i=2&c=entry&do=plus_jhs&m=bsht_tbk&shopid= 
品牌券：
http://wx.chaovip.com/app/index.php?i=2&c=entry&do=plus_ppq&m=bsht_tbk&shopid= 
必买清单：
http://wx.chaovip.com/app/index.php?i=2&c=entry&do=today&m=bsht_tbk&shopid= 
*/

if(strpos($url,"tomy=outside")>0){  //站外

	$arr = array();
	$arr["limit"] = getParams($url)["page"]."";  //这个是页码的参数
	$arr["typename"] = "moren";
	if(strpos($url,"typename=ishot")>0){
		$arr["typename"] = "ishot";
	}else if(strpos($url,"typename=ishit")>0){
		$arr["typename"] = "ishit";
	}else if(strpos($url,"typename=isfee")>0){
		$arr["typename"] = "isfee";
	}
	$arr["minfee"] = "";
	$arr["maxfee"] = "";
	$arr["diyminifee"] = "999999";
	$arr["fqcid"] = "";
	
	
	if(strpos($url,"index=1")>0){
		$url1 = "http://wx.chaovip.com/app/index.php?i=2&c=entry&op=list&shopid=1000&do=plus_tqg_ajax&m=bsht_tbk";
	}else if(strpos($url,"index=2")>0){
		$url1 = "http://wx.chaovip.com/app/index.php?i=2&c=entry&op=list&shopid=1000&do=plus_jhs_ajax&m=bsht_tbk";
	}else if(strpos($url,"index=3")>0){
		$url1 = "http://wx.chaovip.com/app/index.php?i=2&c=entry&op=list&shopid=1000&do=plus_ppq_ajax&m=bsht_tbk";
	}else if(strpos($url,"index=4")>0){
		$url1 = "http://wx.chaovip.com/app/index.php?i=2&c=entry&op=list&shopid=1000&do=today_ajax&m=bsht_tbk";
	}
	
	//echo send_post($url,$arr);
	//exit;
	
	$json = json_decode(send_post($url1,$arr),true);
	
	if($json["status"] == "200"){
		$jarr = $json["content"];
		$len = count($jarr);
		for($i=0;$i<$len;$i++){
			$jobj = $jarr[$i];
			$goods = array();
			$goods["picturl"] = $jobj["pic"];
			$goods["title"] = $jobj["title"];
			$goods["true_price"] = trim($jobj["itemfee2"]);
			$goods["quantity"] = $jobj["itemmsell"];
			
			$goods["quan_val"] = $jobj["jiage2"]."";
			$goods["type"] = array();
			if(strpos($jobj["icon"],"tmall.png")>0){
				array_push($goods["type"],"天猫");
			}else if(strpos($jobj["icon"],"taobao.png")>0){
				array_push($goods["type"],"淘宝");
			}
			
			$jumpUrl = urldecode($jobj["url"]);
			$htmlStr = getHtmlCurl1($jumpUrl);
			//$htmlarr = getHtmlCache($jumpUrl);
			//$htmlStr = $htmlarr["htmlstr"];
			$doc = phpQuery::newDocumentHTML($htmlStr);
			$goods["link"] = urldecode($doc->find("#readurl")->html());
			
			array_push($items,$goods);
		}
	}
	
	//header("Content-Type: text/html; charset=utf-8");
	//echo json_encode($items);
	//exit;

}else{
	
	
	
	//phpQuery::newDocumentFile($url);
	//$htmlarr = getHtmlCache($url);
	//$htmlStr = $htmlarr["htmlstr"];
	//$data["from"] = $htmlarr["from"];
	//phpQuery::newDocumentHTML($htmlStr);
	
	if(strpos($url,"r=p/wap")>0){  //疯抢排行，没有下一页，不能够上拉加载
		
		//$htmlStr = GetFile($url,2,1);
		//$htmlarr = getHtmlCache($url);
		//$htmlStr = $htmlarr["htmlstr"];
		$doc = phpQuery::newDocumentHTML(getHtmlCurl1($url));
		$uls = $doc->find("div[class='goods-list']")->find("div[class='goods-item']");
		foreach($uls as $li2){
			$goods = array();
			$li = pq($li2);
			$goods["picturl"] = $li->find("a[class='img']")->find("img:eq(0)")->attr('src');
			if(strpos($goods["picturl"],"//")==0){
				$goods["picturl"] = "https:".$goods["picturl"];
			}
			$goods["title"] = trim($li->find("a[class='title']")->find("div[class='text']")->text());   //->find("div[class='goods-padding']")
			$goods["true_price"] = $li->find("div[class='price-wrapper']")->find("span[class='price']")->text();
			$goods["true_price"] = trim(str_replace("￥","",$goods["true_price"]));
			$goods["quantity"] = $li->find("div[class='price-wrapper']")->find("div[class='sold-wrapper']")->find("span[class='sold-num']")->text();
			$goods["link"] = $li->find("a[class='img']")->attr('href');
			if(!strpos($goods["link"],$site)){
				$goods["link"] = $site."/".$orderId.$goods["link"];
				$goods["link"] = str_replace("index.php","selector.php",$goods["link"]);
			}
			$goods["quan_val"] = $li->find("a[class='img']")->find("span:eq(0)")->find("b")->text();
			
			$goods["type"] = array();
			$myis = $li->find("div[class='goods-type']")->find("i");
			if(count($myis)>0){
				foreach($myis as $thei){
					$myi = pq($thei);
					array_push($goods["type"],$myi->attr('title'));
				}
			}
			
			array_push($items,$goods);
		}
		
	}else if(strpos($url,"r=ddq")>0){  //咚咚抢，没有下一页，不能上拉加载
		
		//$str = GetFile($url,1,1);
		//$htmlarr = getHtmlCache($url);
		//$str = $htmlarr["htmlstr"];
		$str = getHtmlCurl1($url);
		$arr = explode("dataDef",$str);
		$array = explode("goodsUrl",$arr[1]);
		$str = trim($array[0]);
		$str = trim(substr($str,1,strlen($str)));
		$str = trim(substr($str,0,strlen($str)-1));
		$menu = json_decode($str,true);
		$keysArr = array();
		foreach ($menu as $key => $value) {
			array_push($keysArr,$key);
		}
		sort($keysArr);
		$keyStr = implode(",",$keysArr);
		$menu["keys"] = $keyStr;
		$jstr = json_encode($menu);
		//saveCacheByUrl($url,$jstr);
		header("Content-Type: text/html; charset=utf-8");
		echo $jstr;
		exit;
		
	}else if(strpos($url,"r=index/r")>0){  //小编力荐

		//$htmlStr = GetFile($url,2,1);
		$htmlStr = getHtmlCurl1($url);
		//$htmlStr = $htmlarr["htmlstr"];
		//$htmlStr = getHtmlCurl($url);
		$jsStr = "<script language='javascript'>$('body').after('<div id=\"ajaxContent\"></div>');var bigObj={'data':new Array()};function newGoodsObj(){var goodsObj={'picturl':'','title':'','true_price':'','quantity':'','link':'','quan_val':'','type':new Array()};return goodsObj}function getJson(nodes){bigObj.data.length=0;var len=nodes.size();for(var i=0;i<len;i++){var node=nodes.eq(i);var goodsObj=newGoodsObj();goodsObj.picturl=node.find('a[class=\"img\"]').find('img:eq(0)').prop('src');if(goodsObj.picturl.indexOf('rolling.gif')>0){goodsObj.picturl=node.find('a[class=\"img\"]').find('img:eq(0)').attr('data-original')}if(goodsObj.picturl.indexOf('//')==0){goodsObj.picturl='https:'+goodsObj.picturl}goodsObj.title=node.find('a[class=\"title\"]').find('div[class=\"text\"]').text();goodsObj.true_price=node.find('div[class=\"price-wrapper\"]').find('span[class=\"price\"]').text();goodsObj.trsue_price=goodsObj.true_price.replace('￥','');goodsObj.quantity=node.find('div[class=\"price-wrapper\"]').find('div[class=\"sold-wrapper\"]').find('span[class=\"sold-num\"]').text();goodsObj.link=node.find('a[class=\"img\"]').prop('href');goodsObj.quan_val=node.find('a[class=\"img\"]').find('span:eq(0)').find('b').text();var myis=node.find('div[class=\"goods-type\"]').find('i');var jlen=myis.size();if(jlen>0){for(var j=0;j<jlen;j++){var signal=myis.eq(j);goodsObj.type.push(signal.attr('title'))}}bigObj.data.push(goodsObj)}return JSON.stringify(bigObj);}function getTunjianData(p){var tPaht='/index.php';$.ajax(tPaht,{data:{r:'index/ajaxR',page:p,cac_id:'cXVlcnlUaGVuRmV0Y2g7NDs0MTAyMDAzMzM1OnQ5b2JXWEt2UmRPc1NrNVhodXpKTGc7NDEwMTk4NTg4Njo2Q3pRX2RsaVJrZWxVS05xdm5DSklROzQxMDIyNzgzODc6ei1ZeGNQbFNRa1NmT1JCM0N5eUR2Zzs0MTAyMjYxNDMzOmFURGVqSXdSUWJDTTRQQ2xXZTBrMnc7MDs='},dataType:'json',type:'post',error:function(xhr,type,errorThrown){getTunjianData(p)},success:function(result,status,xhr){if(result.status==0){if(result.data.pageStatus===false){window.webkit.messageHandlers.returnJson.postMessage('{\"bb\":\"a\",\"data\":[]}');return}$('#ajaxContent').html(result.data.content);window.webkit.messageHandlers.returnJson.postMessage(getJson($('#ajaxContent').find('.goods-item')))}else{window.webkit.messageHandlers.returnJson.postMessage('{\"bc\":\"zz\",\"data\":[]}')}}})}window.webkit.messageHandlers.returnJson.postMessage(getJson($('.goods-list').find('.goods-item')));</script>";
		echo str_replace("</body>",$jsStr."</body>",$htmlStr);
		exit;
		
	}else if(strpos($url,"r=p")>0  && strpos($url,"r=p/d")==false){   //人气
		phpQuery::newDocumentHTML(getHtmlCurl1($url));
		//phpQuery::newDocumentFile($url);
		$uls = pq("div[class='goods-list main-container']")->find("ul[class='clearfix']")->find("li");
		foreach($uls as $li2){
			$goods = array();
			$li = pq($li2);
			$goods["picturl"] = $li->find("img:eq(0)")->attr('src');
			if(strpos($goods["picturl"],"//")==0){
				$goods["picturl"] = "https:".$goods["picturl"];
			}
			$goods["title"] = trim($li->find("a[class='title clearfix']")->find("span")->text());
			$goods["true_price"] = $li->find("span[class='price']:eq(1)")->text();
			$goods["true_price"] = trim(str_replace("￥","",$goods["true_price"]));
			$goods["quantity"] = $li->find("span[class='sold-count']")->text();
			$goods["link"] = $li->find("a[class='img']")->attr('href');
			if(!strpos($goods["link"],$site)){
				$goods["link"] = $site."/".$orderId.$goods["link"];
				$goods["link"] = str_replace("index.php","selector.php",$goods["link"]);
			}
			$goods["quan_val"] = $li->find("span[class='price']:eq(0)")->text();
			array_push($items,$goods);
		}
		
	}else
	{  //其它 
	
		
		phpQuery::newDocumentHTML(getHtmlCurl1($url));
		$uls = pq("div[class='goods-list main-container']")->find("ul[class='clearfix']")->find("li");
		foreach($uls as $li2){
			$goods = array();
			$li = pq($li2);
			$goods["picturl"] = $li->find("img:eq(0)")->attr('src');
			if(strpos($goods["picturl"],"//")==0){
				$goods["picturl"] = "https:".$goods["picturl"];
			}
			$goods["title"] = trim($li->find("div[class='title']")->find("a")->text());   //->find("div[class='goods-padding']")
			$goods["true_price"] = $li->find("span[class='price theme-color-8']")->find("b")->text();
			$goods["true_price"] = str_replace("￥","",$goods["true_price"]);
			if(trim($goods["true_price"]=="")){
				$goods["true_price"] = $li->find("span[class='price']")->find("b")->text();
				$goods["true_price"] = str_replace("￥","",$goods["true_price"]);
			}
			$goods["true_price"] = trim($goods["true_price"]);
			$goods["quantity"] = $li->find("span[class='goods-num']")->find("b")->text();
			$goods["link"] = $li->find("a[class='img']")->attr('href');
			if(!strpos($goods["link"],$site)){
				$goods["link"] = $site."/".$orderId.$goods["link"];
				$goods["link"] = str_replace("index.php","selector.php",$goods["link"]);
			}
			$goods["quan_val"] = trim($li->find("span[class='coupon theme-bg-color-9 theme-color-1 theme-border-color-1']")->find("b")->text());
			if($goods["quan_val"]==""){
				$goods["quan_val"] = trim($li->find("span[class='coupon']")->find("b")->text());
			}
			
			$goods["type"] = array();
			$myis = $li->find("div[class='goods-type']")->find("i");
			foreach($myis as $thei){
				$myi = pq($thei);
				array_push($goods["type"],$myi->attr('title'));
			}
			
			array_push($items,$goods);
		}
		
		if($url == $site."/index.php"){  //解析广告
			$adImgArr = array();
			$adLinkArr = array();
			$ads = pq("div[class='swiper-wrapper']")->find("div");
			foreach($ads as $li2){
				$li = pq($li2);
				$adLink = $li->find("a")->attr('href');
				$adLink = str_replace("index",$orderId."/selector",$adLink);
				$adImg = $li->find("img")->attr('src');
				if(!strpos($adImg,$site)){
					$adLink = $site.$adLink;
				}
				array_push($adImgArr,$adImg);
				array_push($adLinkArr,$adLink);
			}
			$data["adImgs"] = $adImgArr;
			$data["adLinks"] = $adLinkArr;
			
			//更新总数
			$data["total"] = pq("span[class='tatal']")->text();
			
			//******************************
			//解析 优惠快报
			//$jsonStr = GetFile($url."?r=index/recomd&type=2",2,1);
			/*
			$jsonStr = getHtmlCurl1($url."?r=index/recomd&type=2");
			//$htmlarr = getHtmlCache($url."?r=index/recomd&type=2");
			//$jsonStr = $htmlarr["htmlstr"];
			$jarr = json_decode($jsonStr, true);
			if(count($jarr["data"][0]["cms_wap_yhkb"]["data"])>0){
				$data["yhkb_title"] = $jarr["data"][0]["cms_wap_yhkb"]["data"][0]["flash_content"];
				$data["yhkb_href"] = $site."/index.php".$jarr["data"][0]["cms_wap_yhkb"]["data"][0]["href"];
			}else{
				$data["yhkb_title"] = "";
				$data["yhkb_href"] = "";
			}
			*/
			
			
			//解析热卖商品
			/*
			phpQuery::newDocumentFile($site."/index.php?r=p&u=".$uParam);
			//$htmlarr = getHtmlCache($site."/index.php?r=p&u=".$uParam);
			//$hstr = $htmlarr["htmlstr"];
			//phpQuery::newDocumentHTML($hstr);
			$uls = pq("div[class='goods-list main-container']")->find("ul[class='clearfix']")->find("li");
			$remaiArr = array();
			foreach($uls as $li2){
				if(count($remaiArr)>=20){
					break;
				}
				$goods = array();
				$li = pq($li2);
				$goods["picturl"] = $li->find("img:eq(0)")->attr('src');
				if(strpos($goods["picturl"],"//")==0){
					$goods["picturl"] = "https:".$goods["picturl"];
				}
				$goods["title"] = trim($li->find("a[class='title clearfix']")->find("span")->text());
				$goods["true_price"] = $li->find("span[class='price']:eq(1)")->text();
				$goods["true_price"] = trim(str_replace("￥","",$goods["true_price"]));
				$goods["quantity"] = $li->find("span[class='sold-count']")->text();
				$goods["link"] = $li->find("a[class='img']")->attr('href');
				if(!strpos($goods["link"],$site)){
					$goods["link"] = $site."/".$orderId.$goods["link"];
					$goods["link"] = str_replace("index.php","selector.php",$goods["link"]);
				}
				$goods["quan_val"] = $li->find("span[class='price']:eq(0)")->text();
				array_push($remaiArr,$goods);
			}
			$data["remai"] = $remaiArr;
			*/
		
		}
		
		if(strpos($url,"r=l/d")>0 && strpos($url,"id=")>0){  //普通商品详情页
			$data["detailSTitle"] = trim(pq("div[class='desc']")->text());
			//$data["quanLink"] = pq("div[class='ehy-normal clearfix']")->find("a:eq(0)")->attr('href');
			$data["quanLink"] = pq("a[class='theme-bg-color-8']")->attr('href');
			$data["quantity"] = trim(pq("div[class='text-wrap']")->find("span:eq(1)")->find("i:eq(0)")->text());
			$data["true_price"] = trim(pq("span[class='now-price']")->find("i:eq(0)")->text());
			$data["quan_val"] = trim(pq("div[class='buy-coupon theme-color-8']")->find("span:eq(1)")->find("b:eq(0)")->text());
			$data["picturl"] = trim(pq("div[class='detail-row clearfix']")->find("a:eq(0)")->find("img:eq(0)")->attr('src'));
			if($data["picturl"]==""){
				$data["picturl"] = trim(pq("#jp_container_1")->attr('data-post'));
			}
			$data["item_num_id"] = trim(pq("div[class='detail-row clearfix']")->find("a:eq(0)")->attr('biz-itemid'));
			$data["title"] = trim(pq("div[class='detail-col']")->find("a:eq(0)")->find("span[class='title']")->text());
			$data["type"] = array();
			$myis = pq("div[class='goods-tit-type']")->find("div[class='goods-type']")->find("i");
			foreach($myis as $thei){
				$myi = pq($thei);
				array_push($data["type"],$myi->attr('title'));
			}
			if($data["detailSTitle"]=="" && $data["quanLink"]=="" && $data["quantity"]=="" && $data["true_price"]=="" && $data["quan_val"]=="" && $data["picturl"]=="" && $data["title"]==""){
				$data["isexist"] = "0";
			}else{
				$data["isexist"] = "1";
			}
			
			if($data["item_num_id"]==""){
				$url1 = str_replace("r=l/d","r=p/d",$url);
				//$htmlarr = getHtmlCache($url);
				//$hstr = $htmlarr["htmlstr"];
				$hstr = getHtmlCurl1($url1);
				phpQuery::newDocumentHTML($hstr);
				$data["item_num_id"] = trim(pq("div[class='pic-detail-btn']")->attr('data-goodsid'));
			}
		}else if((strpos($url,"r=p/d")>0 && strpos($url,"id=")>0)){  //小编推荐的商品详情
			$data["detailSTitle"] = trim(pq("div[class='text theme-color-4']")->text());
			//$data["quanLink"] = pq("div[class='ehy-normal clearfix']")->find("a:eq(0)")->attr('href');
			$data["quanLink"] = pq("a[class='tb_app']:eq(0)")->attr('href');
			$data["quantity"] = trim(pq("div[class='goods-num fr']")->find("b:eq(0)")->text());
			$data["true_price"] = trim(pq("div[class='goods-price fl']")->find("b:eq(0)")->text());
			$data["true_price"] = trim(str_replace("￥","",$data["true_price"]));
			$data["quan_val"] = trim(pq("div[class='goods-quan shoufa-quan fl']")->find("b:eq(0)")->text());
			$data["picturl"] = trim(pq("a[class='tb_app']")->find("img:eq(0)")->attr('src'));
			$data["item_num_id"] = trim(pq("div[class='pic-detail-btn']")->attr('data-goodsid'));
			$data["title"] = trim(pq("div[class='title-wrapper clearfix']")->text());
			$data["type"] = array();
			$myis = pq("div[class='goods-tag']")->find("span");
			foreach($myis as $thei){
				$myi = pq($thei);
				array_push($data["type"],$myi->text());
			}
			if($data["detailSTitle"]=="" && $data["quanLink"]=="" && $data["quantity"]=="" && $data["true_price"]=="" && $data["quan_val"]=="" && $data["picturl"]=="" && $data["title"]==""){
				$data["isexist"] = "0";
			}else{
				$data["isexist"] = "1";
			}
			$items = array();
			$uls = pq("div[class='ads-list']")->find("div[class='goods-item']");
			//解析下面列表的商品
			foreach($uls as $li2){
				$goods = array();
				$li = pq($li2);
				$goods["picturl"] = $li->find("img:eq(0)")->attr('src');
				if(strpos($goods["picturl"],"//")==0){
					$goods["picturl"] = "https:".$goods["picturl"];
				}
				$goods["title"] = trim($li->find("a:eq(1)")->text());   //->find("div[class='goods-padding']")
				$goods["true_price"] = $li->find("div[class='price-wrapper']")->find("span[class='price']")->text();
				$goods["true_price"] = trim(str_replace("￥","",$goods["true_price"]));
				$goods["quantity"] = $li->find("div[class='price-wrapper']")->find("div[class='sold-wrapper']")->find("span[class='sold-num']")->text();
				$goods["link"] = $li->find("a:eq(0)")->attr('href');
				if(!strpos($goods["link"],$site)){
					$goods["link"] = $site."/".$orderId.$goods["link"];
					$goods["link"] = str_replace("index.php","selector.php",$goods["link"]);
				}
				$goods["quan_val"] = $li->find("a:eq(0)")->find("span:eq(0)")->find("b")->text();
				
				$goods["type"] = array();
				$myis = $li->find("div[class='goods-type']")->find("i");
				foreach($myis as $thei){
					$myi = pq($thei);
					array_push($goods["type"],$myi->attr('title'));
				}
				
				array_push($items,$goods);
			}
		}
		if(isset($data["picturl"])){
			//$arrs = explode("jpg",$data["picturl"]);
			//$data["picturl"] = $arrs[0]."jpg_800x800.jpg";
			//$data["picturl"] = str_replace("310x310","800x800",$data["picturl"]);
			$data["picturl"] = str_replace("310x310","800x800",$data["picturl"]);
			$data["picturl"] = str_replace("400x400","800x800",$data["picturl"]);
			if(strpos($data["picturl"],"http")!=0 && strpos($data["picturl"],"https")!=0){
				$data["picturl"] = "https".$data["picturl"];
			}
		}
		if(isset($data["detailSTitle"])){
			$data["detailSTitle"] = str_replace("推荐理由","小编介绍",$data["detailSTitle"]);
		}
		
	}
}


$data["data"] = $items;
$data["excute_time"] = getMillisecond1() - $total_t1;

$jstr = json_encode($data);

header("Content-Type: text/html; charset=utf-8");
echo $jstr;
