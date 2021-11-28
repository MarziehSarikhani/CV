<?php
ob_start();
require_once ("includes/includes.php");
$requestURI = $_SERVER['REQUEST_URI'];
$requestURI = urldecode($requestURI);
//$requestURI = preg_replace('/(?:/[\w-]+|\G)[\w-]*\K//+/','/',$requestURI);
$requestURI = preg_replace('/[\/+]{2,}/','/',$requestURI);
$requestURI = preg_replace('/[\/]$/','',$requestURI);
if (substr($requestURI, 0, 1) == '/')
    $requestURI = substr_replace($requestURI,"",0,1);
$keywords = "";
$title = "";
$showItemFlag = false;
$shoAllFlag = false;
$notFoundPageFlag = false;
$temp = 'نمونه-کار';
$lastSlash = strrpos($requestURI,"/");
if($lastSlash !== false)
    $uri = substr($requestURI,$lastSlash + 1);
else  $uri = $requestURI;

$pattern = "/^{$temp}(?:\/[\w\-]+)?$/iu";
preg_match($pattern,$requestURI,$match);
if($match){
    if($uri == 'نمونه-کار'){
        $shoAllFlag = true;
        $title = "نمونه کار طراحی وب سایت و برنامه نویسی";
        $keywords = " نمونه کار طراحی سایت , نمونه کار برنامه نویسی";

    }else if($product = Product::get_product_by_url($uri)){
        $keywords = $product->keywords;
        $showItemFlag = true;
        $title = $product->title;
    }else{
        $title = "وب سایت مرضیه ساریخانی";
        $notFoundPageFlag = true;
    }
    
}else{
    $title = "وب سایت مرضیه ساریخانی";
    $notFoundPageFlag = true;
     }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>   
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="keywords" content="<?=$keywords ?>" />
    <meta name="title" content="<?=$title ?>" />
<!--    <meta name="description" content="" />-->
    <meta name="author" content="مرضیه ساریخانی" />
    <meta name="generator" content="مرضیه ساریخانی" />    
    <title><?=$title ?></title>
    <link href="http://sarikhani.id.ir/contents/reset.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/contents/myGrideSystem.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/contents/panel.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/contents/footer.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/images/favicon.ico" rel="shortcut icon">
    <?php
    if($showItemFlag)
          echo '<link href="http://sarikhani.id.ir/contents/showItem.css" rel="stylesheet" />';
    else if($shoAllFlag)
          echo '<link href="http://sarikhani.id.ir/contents/products.css" rel="stylesheet" />';
    else if($notFoundPageFlag)
        echo '<link href="http://sarikhani.id.ir/contents/pageNotFound.css" rel="stylesheet" />';
    ?>
</head>
<body>
<?php

if($showItemFlag)
   include ("includes/showItem.php");
else if($shoAllFlag)
    include ("includes/showAll.php");
else if($notFoundPageFlag){
      header("HTTP/1.1 404 Not Found");
      include("errors/NotFound.php");
}
    

$footer = new Footer();
$footer->render();
if($showItemFlag)
      echo '<script src="http://sarikhani.id.ir/contents/panelDynamic.js"></script>';
else if($shoAllFlag)
    echo '<script src="http://sarikhani.id.ir/contents/panel.js"></script>';
?>
<script src="http://sarikhani.id.ir/contents/footer.js"></script>
</body>
</html>
