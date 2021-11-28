<?php
ini_set("session.cookie_httponly",1);
ini_set("session.cookie_lifetime",3600);
session_start();
$chars = array();
$var1 = rand(10,30);
$var2 = rand(1,10);
$_SESSION["chaptcha"] = $var1 + $var2;
$chars[0] = $var1;
$chars[1] = "+";
$chars[2] = $var2;
$chars[3] = "=";

$imagebg = imagecreatefromjpeg("./bg_chaptcha.jpg");

$color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
imagefttext($imagebg,rand(20,26), rand(-30,30),5,rand(20,45),$color,"../fonts/font_CHAPTCHA1.TTF",$chars[0]);
imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
for($i = 0 ; $i < 50 ;$i++)
    imagesetpixel($imagebg,rand(0,99),rand(0,49),$color);

$color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
imagefttext($imagebg,rand(20,26),0,35,rand(20,45),$color,"../fonts/font_CHAPTCHA1.TTF",$chars[1]);
imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
for($i = 0 ; $i < 50 ;$i++)
    imagesetpixel($imagebg,rand(0,99),rand(0,49),$color);

$color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
imagefttext($imagebg,rand(20,26),rand(-30,30),55,rand(20,45),$color,"../fonts/font_CHAPTCHA1.TTF",$chars[2]);
imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
for($i = 0 ; $i < 50 ;$i++)
    imagesetpixel($imagebg,rand(0,99),rand(0,49),$color);

$color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
imagefttext($imagebg,rand(20,26),0,80,rand(20,45),$color,"../fonts/font_CHAPTCHA1.TTF",$chars[3]);
imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
for($i = 0 ; $i < 50 ;$i++)
    imagesetpixel($imagebg,rand(0,99),rand(0,49),$color);

for($i = 0 ; $i < 4 ; $i++){
    $color = imagecolorallocate($imagebg,rand(0,100),rand(0,100),rand(0,200));
    imageline($imagebg,rand(0,99),rand(0,49),rand(0,99),rand(0,49),$color);
}


header("Content-Type:image/png");
imagepng($imagebg);
imagedestroy($imagebg);