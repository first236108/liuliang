<?php


function GetFile($url,$way=2){
    if($way==1){
        $str=file_get_contents($url);
    }else if($way==2){
        @$ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_NOBODY,false); 
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_MAXREDIRS,20);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
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

echo GetFile("https://lanxujf.m.tmall.com/?spm=a222m.7628550/B.0.0");









