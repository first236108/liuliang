<?php

include('config.php');

$url = "http://m.chaovip.com/index.php";

echo getHtmlCache($url)["htmlstr"];