<?php
ini_set("session.cookie_httponly",1);
ini_set("session.cookie_lifetime",3600);
session_start();
$chars = array();
for($i = 0 ; $i <5 ; $i++){
    do{
        $ascii = rand(49,122);
    }while($ascii > 57 and $ascii < 97);
    $chars[$i] = chr($ascii);
}
$code = $chars[0] . $chars[1] . $chars[2] . $chars[3] . $chars[4];
$_SESSION["chaptcha"] = $code;
$imagebg = imagecreatefromjpeg("./bg_chaptcha.jpg");
for($i = 0 ; $i < 5 ; $i++){
    $size = rand(20,26);
    $angle = rand(-30,30);
    $x = $i * 20 + 5;
    $y = rand(20,45);
    $color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
    imagefttext($imagebg,$size,$angle,$x,$y,$color,"../fonts/font_CHAPTCHA.TTF",$chars[$i]);
    imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
    for($j = 0 ; $j < 50 ;$j++)
       imagesetpixel($imagebg,rand(0,99),rand(0,49),$color);
}
header("Content-Type:image/png");
imagepng($imagebg);