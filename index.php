<?php
include_once ("includes/includes.php");
ini_set("session.cookie_httponly",1);
ini_set("session.cookie_lifetime",3600);
session_start();
counterVisitorSite();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="keywords" content="طراحی سایت , مرضیه ساریخانی , طراحی وب سایت , ساخت سایت,ساخت وب سایت,طراحی سایت وردپرس"  />
    <meta name="title" content="مرضیه ساریخانی" />
    <meta name="author" content="مرضیه ساریخانی" />
    <meta name="generator" content="مرضیه ساریخانی" />
    <title>وب سایت مرضیه ساریخانی</title>

    <link href="http://sarikhani.id.ir/contents/reset.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/contents/homepage.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/contents/footer.css" rel="stylesheet" />
    <link href="http://sarikhani.id.ir/images/favicon.ico" rel="shortcut icon">
</head>
<body class="hiddenScrollbarInEdge hiddenScrollbar">
<div id="fullpage" class="row">
    <section id="mainPage" class="section active" data-anchor="page1">
        <div  class="wrapper row">
            <h1>نمونه کار</h1>
            <div id="sliderProduct">
                <?php
                
                if($paths = Product::get_slideShow()){
                    ?>
                    <ul class="row" style="left:-400px;">
                        <?php
                        $pattern = '/^http(?:s)?:\/\/.+/';
                        foreach ($paths as $path ){
                             preg_match($pattern,$path->link,$match);
                             if(is_file($path->slideShow_path)) {
                                 ?>
                                 <li>
                                     <article class="group">
                                         <a <?php  if($match) echo "target=\"_blank\" href=\"{$path->link}\""; else  echo "href=\"http://sarikhani.id.ir/نمونه-کار/{$path->link}\""; ?>>
                                             <img src="http://sarikhani.id.ir/<?= $path->slideShow_path ?>" alt="<?=$path->title ?>"/>
                                             <span class="imagelabel">نمونه کار</span>
                                             <h2><?= $path->title ?></h2>
                                         </a>
                                     </article>
                                 </li>
                                 <?php
                             }
                        }
                        ?>
                </ul>
                <?php
                }
                ?>

            </div>
            <div id="nextprev" class="control  noSelect">
                <span id="nextProduct">&lt;</span>
                <span id="prevProduct">&gt;</span>
            </div>
            <div id="showProducts">
                <a href="http://sarikhani.id.ir/نمونه-کار" >مشاهده سایر نمونه کارها</a>
            </div>
        </div>
    </section>
    <?php
    $footer = new Footer();
    $footer->render();
    ?>
</div>
<div id="sideNav" >
    <span  id="goTotopPage" data-anchor="up" class="bullet activeBullet"></span>
    <span  id="goTobottomPage" data-anchor="down"  class="bullet"></span>
</div>
<script src="http://sarikhani.id.ir/contents/homepage.js"></script>
<script src="http://sarikhani.id.ir/contents/slider.js" ></script>
<script src="http://sarikhani.id.ir/contents/footer.js"></script>
</body>
</html>
